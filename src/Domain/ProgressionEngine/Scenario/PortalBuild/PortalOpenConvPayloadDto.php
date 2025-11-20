<?php

namespace App\Domain\ProgressionEngine\Scenario\PortalBuild;

use App\Entity\Step;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\ObjectMapper\Attribute\Map;

final class PortalOpenConvPayloadDto
{
    /** @var StepItemDto[] */
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[Assert\All([
        new Assert\Type(type: StepItemDto::class)
    ])]
    public array $steps = [];

    public function getSteps(): array
    {
        return $this->steps;
    }
}

#[Map(target: Step::class)]
final class StepItemDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    public string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $instruction;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getInstruction(): string
    {
        return $this->instruction;
    }
}
