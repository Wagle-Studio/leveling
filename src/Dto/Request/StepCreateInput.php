<?php

namespace App\Dto\Request;

use App\Entity\Step;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: Step::class)]
final readonly class StepCreateInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le label ne peut pas être vide.')]
        #[Assert\Length(
            min: 5,
            max: 100,
            minMessage: 'Le label doit faire au moins {{ limit }} caractères.',
            maxMessage: 'Le label ne peut pas faire plus de {{ limit }} caractères.'
        )]
        public string $label,

        #[Assert\NotBlank(message: "L'instruction ne peut pas être vide.")]
        #[Assert\Length(
            min: 10,
            max: 255,
            minMessage: "L'instruction doit faire au moins {{ limit }} caractères.",
            maxMessage: "L'instruction ne peut pas faire plus de {{ limit }} caractères."
        )]
        public string $instruction,
    ) {}
}
