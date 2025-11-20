<?php

namespace App\Messenger\Message;

final class ProcessQueueJobMessage
{
    public function __construct(
        public readonly int $jobId
    ) {}
}
