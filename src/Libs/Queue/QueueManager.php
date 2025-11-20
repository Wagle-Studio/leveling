<?php

namespace App\Libs\Queue;

use App\Entity\QueueJob;
use App\Libs\Queue\QueueJobScenarioLocator;
use App\Libs\Queue\QueueJobEnum;
use App\Repository\QueueJobRepository;

final class QueueManager implements QueueManagerInterface
{
    public function __construct(
        private QueueJobRepository $queueJobRepository,
        private QueueJobScenarioLocator $queueJobExecutorLocator,
    ) {}

    public function executeJob(QueueJob $queueJob): void
    {
        $this->queueJobExecutorLocator->handle($queueJob);
    }

    public function enqueueJob(QueueJobEnum $jobType, QueuePayloadInterface $payload): void
    {
        $job = new QueueJob();
        $job->setType($jobType->value);
        $job->setPayload($payload->toArray());

        $this->queueJobRepository->save($job, true);
    }
}
