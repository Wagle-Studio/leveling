<?php

namespace App\Command;

use App\Libs\Queue\QueueManagerInterface;
use App\Libs\Queue\QueueJobStatusEnum;
use App\Repository\QueueJobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\QueueJob;

#[AsCommand(name: 'app:queue:work')]
class QueueWorkerCommand extends Command
{
    private const DEFAULT_JOB_TIMEOUT = 180;

    public function __construct(
        private QueueJobRepository $queueJobRepository,
        private EntityManagerInterface $entityManager,
        private QueueManagerInterface $queueManager,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'timeout',
            't',
            InputOption::VALUE_REQUIRED,
            'Maximum execution time for a single job (in seconds)',
            self::DEFAULT_JOB_TIMEOUT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $timeout = (int) $input->getOption('timeout');

        while (true) {
            if (!$this->entityManager->isOpen()) {
                $this->logger->error('EntityManager is closed. Attempting to restart...');
                $output->writeln('<error>EntityManager is closed. Exiting worker.</error>');
                return Command::FAILURE;
            }

            $job = $this->queueJobRepository->claimNextPending();

            if (!$job) {
                sleep(10);
                continue;
            }

            try {
                $this->executeJobWithTimeout($job, $timeout);
                $job->setStatus(QueueJobStatusEnum::COMPLETED->value);
                $job->setProcessedAt(new \DateTimeImmutable());
                $this->entityManager->flush();
            } catch (\Throwable $error) {
                $this->logger->error('Queue job failed', [
                    'job_id' => $job->getId(),
                    'job_type' => $job->getType(),
                    'exception' => $error->getMessage(),
                    'trace' => $error->getTraceAsString(),
                ]);

                $job->setStatus(QueueJobStatusEnum::FAILED->value);
                $job->setErrorMessage($error->getMessage());

                try {
                    $this->entityManager->flush();
                } catch (\Throwable $flushError) {
                    $this->logger->critical('Failed to flush EntityManager after job failure', [
                        'job_id' => $job->getId(),
                        'exception' => $flushError->getMessage(),
                    ]);

                    return Command::FAILURE;
                }
            }

            $this->entityManager->clear();
        }
    }

    private function executeJobWithTimeout(QueueJob $job, int $timeout): void
    {
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        }

        $startTime = time();

        if (function_exists('pcntl_alarm') && function_exists('pcntl_signal')) {
            pcntl_signal(SIGALRM, function () use ($job) {
                throw new \RuntimeException(
                    sprintf('Job %d exceeded timeout limit', $job->getId())
                );
            });
            pcntl_alarm($timeout);
        }

        try {
            $this->queueManager->executeJob($job);
        } finally {
            if (function_exists('pcntl_alarm')) {
                pcntl_alarm(0);
            }
        }

        $executionTime = time() - $startTime;

        if ($executionTime > $timeout * 0.9) {
            $this->logger->warning('Job execution time close to timeout', [
                'job_id' => $job->getId(),
                'job_type' => $job->getType(),
                'execution_time' => $executionTime,
                'timeout' => $timeout,
            ]);
        }
    }
}
