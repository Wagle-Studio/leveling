<?php

namespace App\Libs\Queue;

use App\Entity\QueueJob;
use App\Libs\Queue\QueueJobEnum;

interface QueueManagerInterface
{
    public function executeJob(QueueJob $job): void;
    public function enqueueJob(QueueJobEnum $jobType, QueuePayloadInterface $payload): void;
}
