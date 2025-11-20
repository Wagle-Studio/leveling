<?php

namespace App\Messenger\Handler;

use App\Libs\Queue\QueueJobStatusEnum;
use App\Libs\Queue\QueueManagerInterface;
use App\Messenger\Message\ProcessQueueJobMessage;
use App\Repository\QueueJobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessQueueJobMessageHandler
{
    public function __construct(
        private readonly QueueJobRepository $queueJobRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly QueueManagerInterface $queueManager,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(ProcessQueueJobMessage $message): void
    {
        $job = $this->queueJobRepository->find($message->jobId);

        if (!$job) {
            $this->logger->warning('Job not found', ['job_id' => $message->jobId]);
            return;
        }

        if ($job->getStatus() !== QueueJobStatusEnum::PENDING->value) {
            $this->logger->info('Job already processed or in progress', [
                'job_id' => $job->getId(),
                'status' => $job->getStatus()
            ]);
            return;
        }

        $job->setStatus(QueueJobStatusEnum::RUNNING);
        $this->entityManager->flush();

        try {
            $this->queueManager->execute($job);
            $job->setStatus(QueueJobStatusEnum::COMPLETED);
            $job->setProcessedAt(new \DateTimeImmutable());
            $this->entityManager->flush();
        } catch (\Throwable $error) {
            $this->logger->error('Queue job failed', [
                'job_id' => $job->getId(),
                'job_type' => $job->getType(),
                'exception' => $error->getMessage(),
                'trace' => $error->getTraceAsString(),
            ]);

            $job->incrementAttempts();
            $job->setErrorMessage($error->getMessage());

            if ($job->canRetry()) {
                $job->setStatus(QueueJobStatusEnum::PENDING);
                $this->logger->info('Job will be retried', [
                    'job_id' => $job->getId(),
                    'attempts' => $job->getAttempts(),
                    'max_retries' => $job->getMaxRetries(),
                ]);
            } else {
                $job->setStatus(QueueJobStatusEnum::DEAD_LETTER);
                $this->logger->critical('Job moved to dead letter queue after max retries', [
                    'job_id' => $job->getId(),
                    'attempts' => $job->getAttempts(),
                ]);
            }

            try {
                $this->entityManager->flush();
            } catch (\Throwable $flushError) {
                $this->logger->critical('Failed to flush EntityManager after job failure', [
                    'job_id' => $job->getId(),
                    'exception' => $flushError->getMessage(),
                ]);
            }

            throw $error;
        } finally {
            $this->entityManager->clear();
        }
    }
}
