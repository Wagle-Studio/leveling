<?php

namespace App\Libs\Queue\Process;

use App\Entity\Step;
use App\Libs\Conversation\ConversationManagerInterface;
use App\Libs\Conversation\Payload\Dto\BuildObjectiveStepsDto;
use App\Libs\Queue\Payload\BuildObjectiveStepsPayload;
use App\Libs\Queue\Payload\QueuePayloadInterface;
use App\Repository\ObjectiveRepository;
use App\Repository\StepRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class BuildObjectiveStepsProcess implements QueueProcessInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private ObjectiveRepository $objectiveRepository,
        private StepRepository $stepRepository,
        private ConversationManagerInterface $conversationManager,
    ) {}

    public function process(QueuePayloadInterface $payload): void
    {
        if (!$payload instanceof BuildObjectiveStepsPayload) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] needs an instance of "%s". Received: "%s".', self::class, BuildObjectiveStepsPayload::class, get_class($payload))
            );
        }

        $objective = $this->objectiveRepository->find($payload->getObjectiveId());

        if ($objective === null) {
            throw new \RuntimeException(sprintf('["%s"] Objective with ID "%s" not found.', self::class, $payload->getObjectiveId()));
        }

        $payload = $this->conversationManager->buildObjectiveSteps($objective);

        $payloadData = $payload->getData();

        if (!$payloadData instanceof BuildObjectiveStepsDto) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] expects payload of type "%s", "%s" given.', self::class, BuildObjectiveStepsDto::class, get_class($payloadData))
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
