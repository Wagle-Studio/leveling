<?php

namespace App\Domain\ProgressionEngine\Scenario\PortalBuild;

use App\Domain\Core\Interface\ScenarioInterface;
use App\Entity\Step;
use App\Libs\Conversation\ConversationManagerInterface;
use App\Repository\ObjectiveRepository;
use App\Repository\StepRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class PortalOpenScenario implements ScenarioInterface
{
    public function __construct(
        protected StepRepository $stepRepository,
        protected ObjectMapperInterface $objectMapper,
        protected ObjectiveRepository $objectiveRepository,
        protected ConversationManagerInterface $conversationManager,
    ) {}

    public function run(object $payload): void
    {
        if (!$payload instanceof PortalOpenQueuePayloadDto) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] needs an instance of "%s". Received: "%s".', self::class, PortalOpenQueuePayloadDto::class, get_class($payload))
            );
        }

        $objective = $this->objectiveRepository->find($payload->getObjectiveId());

        if ($objective === null) {
            throw new \RuntimeException(sprintf('["%s"] Objective with ID "%s" not found.', self::class, $payload->getObjectiveId()));
        }

        $payload = $this->conversationManager->buildObjectiveSteps($objective);

        $payloadData = $payload->getData();

        if (!$payloadData instanceof PortalOpenConvPayloadDto) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] expects payload of type "%s", "%s" given.', self::class, PortalOpenConvPayloadDto::class, get_class($payloadData))
            );
        }

        $steps = [];

        foreach ($payloadData->getSteps() as $payloadStep) {
            $step = $this->objectMapper->map($payloadStep, Step::class);
            $steps[] = $step->setObjective($objective);
        }

        $this->stepRepository->saveAll($steps, true);
    }
}
