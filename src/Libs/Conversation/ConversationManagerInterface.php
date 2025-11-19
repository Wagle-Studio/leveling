<?php

namespace App\Libs\Conversation;

use App\Entity\Objective;
use App\Libs\Conversation\Payload\PayloadInterface;

interface ConversationManagerInterface
{
    public function buildObjectiveSteps(Objective $objective): PayloadInterface;
}
