<?php

namespace App\Service\Ai\Prompt;

use App\Service\Ai\Context\ContextInterface;

interface PromptInterface
{
    public function initialize(ContextInterface $context): void;
    public function getSystemInstructions(): string;
    public function getUserInstructions(): string;
}
