<?php

namespace App\Service\Ai\Factory;

use App\Service\Ai\Context\ContextInterface;
use App\Service\Ai\PromptTypes;

interface ContextFactoryInterface
{
    public function handle(PromptTypes $promptType, ...$params): ContextInterface;
}
