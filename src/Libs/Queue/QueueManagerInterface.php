<?php

namespace App\Libs\Queue;

use App\Entity\QueueJob;

interface QueueManagerInterface
{
    public function execute(QueueJob $job): void;

    public function enqueue(QueueJobEnum $jobType, JobPayloadInterface $payload): void;
}
