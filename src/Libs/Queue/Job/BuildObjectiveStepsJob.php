<?php

namespace App\Libs\Queue\Job;

use App\Entity\QueueJob;
use App\Libs\Queue\Payload\QueuePayloadInterface;
use App\Libs\Queue\QueueJobEnum;
use App\Repository\QueueJobRepository;

final class BuildObjectiveStepsJob implements QueueJobInterface
{
    public function __construct(private QueueJobRepository $queueJobRepository) {}

    public function enqueue(QueuePayloadInterface $payload): void
    {
        $job = new QueueJob();
        $job->setType(QueueJobEnum::buildObjectiveSteps->value);
        $job->setPayload($payload->toArray());

        $this->queueJobRepository->save($job, true);
    }
}
