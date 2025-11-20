<?php

namespace App\Domain\ProgressionEngine\Scenario\PortalBuild;

use App\Domain\Core\Interface\ScenarioInterface;
use App\Entity\Step;
use App\Libs\Conversation\ConversationManagerInterface;
use App\Repository\ObjectiveRepository;
use App\Repository\StepRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class PortalOpenConvScenario implements ScenarioInterface
{
    public function __construct(
        protected StepRepository $stepRepository,
        protected ObjectMapperInterface $objectMapper,
        protected ObjectiveRepository $objectiveRepository,
        protected ConversationManagerInterface $conversationManager,
    ) {}

    public function run(object $payload): void
    {
    }
}
