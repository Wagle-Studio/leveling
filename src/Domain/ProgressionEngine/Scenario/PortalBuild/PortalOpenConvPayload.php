<?php

namespace App\Domain\ProgressionEngine\Scenario\PortalBuild;

use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PortalOpenConvPayload
{
    private PortalOpenConvPayloadDto $payload;

    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private ValidatorInterface $validator
    ) {}

    public function initialize(object $result): void
    {
        $mappedSteps = [];
        $mappedStepGenerate = $this->objectMapper->map($result, PortalOpenConvPayloadDto::class);

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

    public function getData(): PortalOpenConvPayloadDto
    {
        return $this->payload;
    }
}
