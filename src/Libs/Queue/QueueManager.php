<?php

namespace App\Libs\Queue;

use App\Entity\QueueJob;
use App\Libs\Queue\Dispatcher\QueueEnqueueDispatcher;
use App\Libs\Queue\Dispatcher\QueueExecutionDispatcher;
use App\Libs\Queue\Payload\QueuePayloadInterface;
use App\Libs\Queue\QueueJobEnum;

final class QueueManager implements QueueManagerInterface
{
    public function __construct(
        private QueueEnqueueDispatcher $enqueueDispatcher,
        private QueueExecutionDispatcher $executionDispatcher
    ) {}

    public function executeJob(QueueJob $job): void
    {
        $this->executionDispatcher->dispatch($job);
    }

    public function enqueueJob(QueueJobEnum $jobType, QueuePayloadInterface $payload): void
    {
        $this->enqueueDispatcher->dispatch($jobType, $payload);
    }
}
