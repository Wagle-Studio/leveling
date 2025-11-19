<?php

namespace App\Libs\Queue\Payload;

interface QueuePayloadInterface
{
    public function toArray(): array;
}
