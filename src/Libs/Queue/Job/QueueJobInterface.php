<?php

namespace App\Libs\Queue\Job;

use App\Libs\Queue\Payload\QueuePayloadInterface;

interface QueueJobInterface
{
    public function enqueue(QueuePayloadInterface $payload): void;
}
