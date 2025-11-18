<?php

namespace App\Service\Ai\Payload;

use App\Service\Ai\Payload\Dto\StepGenerateDto;
use App\Service\Ai\Payload\Dto\StepItemDto;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class StepsGeneratePayload implements PayloadInterface
{
    private StepGenerateDto $payload;

    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private ValidatorInterface $validator
    ) {}

    public function initialize(object $result): void
    {
        /** @var StepGenerateDto $mappedStepGenerate */
        $mappedStepGenerate = $this->objectMapper->map($result, StepGenerateDto::class);

        /** @var StepItemDto[] $mappedSteps */
        $mappedSteps = [];

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

    public function getPayload(): StepGenerateDto
    {
        return $this->payload;
    }
}
