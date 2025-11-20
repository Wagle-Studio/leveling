<?php

namespace App\Dto\Entity;

use App\Entity\Step;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: Step::class)]
final class StepDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
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
