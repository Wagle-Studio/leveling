<?php

namespace App\Service\Ai\Factory;

use App\Service\Ai\Context\ContextInterface;
use App\Service\Ai\Prompt\PromptInterface;
use App\Service\Ai\PromptTypes;

interface PromptFactoryInterface
{
    public function handle(PromptTypes $promptType, ContextInterface $context): PromptInterface;
}
