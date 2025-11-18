<?php

namespace App\Service\Ai\Payload\Dto;

use App\Entity\Step;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: Step::class)]
final class StepItemDto implements PayloadDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    public string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $instruction;
}
