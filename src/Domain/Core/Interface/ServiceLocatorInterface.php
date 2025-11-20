<?php

namespace App\Domain\Core\Interface;

use App\Entity\QueueJob;
use App\Libs\Conversation\ConversationEnum;

interface ServiceLocatorInterface
{
    public function handleQueueJob(QueueJob $queueJob): void;
    public function handleConversation(ConversationEnum $conversationEnum, object $payload): void;
}
