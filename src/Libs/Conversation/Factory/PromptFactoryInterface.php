<?php

namespace App\Libs\Conversation\Factory;

use App\Libs\Conversation\Context\ContextInterface;
use App\Libs\Conversation\Prompt\PromptInterface;
use App\Libs\Conversation\ConversationTypes;

interface PromptFactoryInterface
{
    public function handle(ConversationTypes $promptType, ContextInterface $context): PromptInterface;
}
