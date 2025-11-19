<?php

namespace App\Libs\Conversation\Prompt;

use App\Libs\Conversation\Context\ContextInterface;

interface PromptInterface
{
    public function initialize(ContextInterface $context): void;
    public function getSystemInstructions(): string;
    public function getUserInstructions(): string;
}
