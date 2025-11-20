<?php

namespace App\Libs\Queue;

use App\Libs\Domain\Core\Interface\ServiceLocatorInterface;
use App\Libs\Domain\ProgressionEngine\ProgressionScenarioLocator;
use App\Entity\QueueJob;
use App\Messenger\Message\ProcessQueueJobMessage;
use App\Repository\QueueJobRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class QueueManager implements QueueManagerInterface, ServiceSubscriberInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly QueueJobRepository $queueJobRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            QueueJobEnum::SCENARIO_PORTAL_OPEN->value => ProgressionScenarioLocator::class,
            QueueJobEnum::SCENARIO_QUEST_OPEN->value => ProgressionScenarioLocator::class,
        ];
    }

    public function execute(QueueJob $queueJob): void
    {
        $jobType = $queueJob->getType();

        if (!$this->container->has($jobType)) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Unknown queue job: "%s".', self::class, $jobType)
            );
        }

        /** @var ServiceLocatorInterface<QueueJob> $serviceLocator */
        $serviceLocator = $this->container->get($jobType);

        if (!$serviceLocator instanceof ServiceLocatorInterface) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Service "%s" must implement "%s".', self::class, $jobType, ServiceLocatorInterface::class)
            );
        }

        $serviceLocator->handle($queueJob);
    }

    public function enqueue(QueueJobEnum $jobType, JobPayloadInterface $payload): void
    {
        $job = new QueueJob();
        $job->setType($jobType->value);
        $job->setPayload($payload->toArray());

        $this->queueJobRepository->save($job, true);

        $this->messageBus->dispatch(new ProcessQueueJobMessage($job->getId()));
    }
}
