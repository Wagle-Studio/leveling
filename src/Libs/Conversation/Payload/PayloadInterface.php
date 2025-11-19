<?php

namespace App\Libs\Conversation\Payload;

use App\Libs\Conversation\Payload\Dto\PayloadDtoInterface;

interface PayloadInterface
{
    public function initialize(object $result): void;
    public function getData(): PayloadDtoInterface;
}
