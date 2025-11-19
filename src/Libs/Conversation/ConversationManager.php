<?php

namespace App\Libs\Conversation;

use App\Entity\Objective;
use App\Libs\Ai\Agent\OpenAiAgent;
use App\Libs\Conversation\Factory\ContextFactory;
use App\Libs\Conversation\Factory\PayloadFactory;
use App\Libs\Conversation\Factory\PromptFactory;
use App\Libs\Conversation\Payload\PayloadInterface;

final class ConversationManager implements ConversationManagerInterface
{
    function __construct(
        private ContextFactory $contextFactory,
        private PromptFactory $promptFactory,
        private PayloadFactory $payloadFactory,
        private OpenAiAgent $aiAgent,
    ) {}

    public function buildObjectiveSteps(Objective $objective): PayloadInterface
    {
        $conversationType = ConversationTypes::BuildObjectiveSteps;
        $context = $this->contextFactory->handle($conversationType, $objective);
        $prompt = $this->promptFactory->handle($conversationType, $context);
        $result = $this->aiAgent->send($prompt);
        return $this->payloadFactory->handle($conversationType, $result);
    }
}
