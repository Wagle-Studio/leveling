<?php

namespace App\Service\Ai\Payload\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class StepGenerateDto implements PayloadDtoInterface
{
    /** @var StepItemDto[] */
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[Assert\All([
        new Assert\Type(type: StepItemDto::class)
    ])]
    public array $steps = [];
}
