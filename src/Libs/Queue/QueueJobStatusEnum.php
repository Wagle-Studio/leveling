<?php

namespace App\Libs\Queue;

enum QueueJobStatusEnum: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
