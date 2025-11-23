<?php

namespace App\Libs\Domain\ProgressionEngine\Scenario\PortalOpen;

use App\Libs\Domain\ProgressionEngine\ScenarioInterface;
use App\Dto\Entity\StepDto;
use App\Entity\Step;
use App\Libs\Ai\AiProviderInterface;
use App\Repository\ObjectiveRepository;
use App\Repository\StepRepository;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PortalOpenScenario implements ScenarioInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly StepRepository $stepRepository,
        private readonly ObjectMapperInterface $objectMapper,
        private readonly ObjectiveRepository $objectiveRepository,
        private readonly PortalOpenPromptBuilder $portalOpenPromptBuilder,
        private readonly AiProviderInterface $aiAgent,
    ) {}

    public function run(object $payload): mixed
    {
        /** @var PortalOpenPayload $payload */
        $payload = $this->objectMapper->map($payload, PortalOpenPayload::class);

        $payloadErrors = $this->validator->validate($payload);

        if (count($payloadErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid progression scenario payload: %s', self::class, (string) $payloadErrors)
            );
        }

        $objective = $this->objectiveRepository->find($payload->objective_id);

        if ($objective === null) {
            throw new \RuntimeException(
                sprintf('["%s"] Objective with ID "%s" not found.', self::class, $payload->objective_id)
            );
        }

        $this->portalOpenPromptBuilder->initialize($objective);

        $systemInstructions = $this->portalOpenPromptBuilder->getSystemInstructions();
        $userInstructions = $this->portalOpenPromptBuilder->getUserInstructions();

        $convResult = $this->aiAgent->send($systemInstructions, $userInstructions);

        /** @var PortalOpenResult $resultPayload */
        $resultPayload = $this->objectMapper->map($convResult, PortalOpenResult::class);

        $resultPayloadErrors = $this->validator->validate($resultPayload);

        if (count($resultPayloadErrors) > 0) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $resultPayloadErrors)
            );
        }

        $expectedSteps = $objective->getDuration();
        $actualSteps = count($resultPayload->steps);

        if ($actualSteps !== $expectedSteps) {
            throw new \InvalidArgumentException(
                sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $resultPayloadErrors)
            );
        }

        $steps = [];

        foreach ($resultPayload->steps as $payloadStep) {
            /** @var StepDto $rawStep */
            $rawStep = $this->objectMapper->map($payloadStep, StepDto::class);

            $rawStepErrors = $this->validator->validate($rawStep);

            if (count($rawStepErrors) > 0) {
                throw new \InvalidArgumentException(
                    sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $resultPayloadErrors)
                );
            }

            /** @var Step $step */
            $step = $this->objectMapper->map($rawStep, Step::class);

            $stepErrors = $this->validator->validate($step);

            if (count($stepErrors) > 0) {
                throw new \InvalidArgumentException(
                    sprintf('["%s"] Invalid conversation result: %s', self::class, (string) $resultPayloadErrors)
                );
            }

            $step->setObjective($objective);
            $steps[] = $step;
        }

        $this->stepRepository->saveAll($steps, true);

        return null;
    }
}
