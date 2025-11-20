<?php

namespace App\Libs\Queue;

interface QueuePayloadInterface
{
    public function toArray(): array;
}
