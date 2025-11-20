<?php

namespace App\Libs\Queue;

interface JobPayloadInterface
{
    public function toArray(): array;
}
