<?php

namespace App\Libs\Conversation\Factory;

use App\Libs\Conversation\Context\ContextInterface;
use App\Libs\Conversation\ConversationTypes;

interface ContextFactoryInterface
{
    public function handle(ConversationTypes $promptType, ...$params): ContextInterface;
}
