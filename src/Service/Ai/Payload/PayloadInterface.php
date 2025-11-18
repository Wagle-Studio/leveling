<?php

namespace App\Service\Ai\Payload;

use App\Service\Ai\Payload\Dto\PayloadDtoInterface;

interface PayloadInterface
{
    public function initialize(object $result): void;
    public function getPayload(): PayloadDtoInterface;
}
