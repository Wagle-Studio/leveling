<?php

namespace App\Service\Ai\Factory;

use App\Service\Ai\Payload\PayloadInterface;
use App\Service\Ai\PromptTypes;

interface PayloadFactoryInterface
{
    public function handle(PromptTypes $promptType, object $result): PayloadInterface;
}
