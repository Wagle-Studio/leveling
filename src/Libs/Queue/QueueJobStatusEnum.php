<?php

namespace App\Libs\Queue;

enum QueueJobStatusEnum: string
{
    case PENDING = 'PENDING';
    case RUNNING = 'RUNNING';
    case COMPLETED = 'COMPLETED';
    case DEAD_LETTER = 'DEAD_LETTER';
}
