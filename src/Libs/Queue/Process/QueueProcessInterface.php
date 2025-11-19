<?php

namespace App\Libs\Queue\Process;

use App\Libs\Queue\Payload\QueuePayloadInterface;

interface QueueProcessInterface
{
    public function process(QueuePayloadInterface $payload): void;
}
