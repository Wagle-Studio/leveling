<?php

namespace App\Libs\Conversation\Factory;

use App\Libs\Conversation\Payload\PayloadInterface;
use App\Libs\Conversation\ConversationTypes;

interface PayloadFactoryInterface
{
    public function handle(ConversationTypes $promptType, object $result): PayloadInterface;
}
