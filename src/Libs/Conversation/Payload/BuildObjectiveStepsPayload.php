<?php

namespace App\Libs\Conversation\Payload;

use App\Libs\Conversation\Payload\Dto\{BuildObjectiveStepsDto, StepItemDto};
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class BuildObjectiveStepsPayload implements PayloadInterface
{
    private BuildObjectiveStepsDto $payload;

    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private ValidatorInterface $validator
    ) {}

    public function initialize(object $result): void
    {
        $mappedSteps = [];
        $mappedStepGenerate = $this->objectMapper->map($result, BuildObjectiveStepsDto::class);

        foreach ($mappedStepGenerate->steps as $stepData) {
            $mappedSteps[] = $this->objectMapper->map($stepData, StepItemDto::class);
        }

        $mappedStepGenerate->steps = $mappedSteps;

        $errors = $this->validator->validate($mappedStepGenerate);

        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->payload = $mappedStepGenerate;
    }

    public function getData(): BuildObjectiveStepsDto
    {
        return $this->payload;
    }
}
