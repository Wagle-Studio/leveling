<?php

namespace App\Dto\Request;

use App\Entity\Objective;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: Objective::class)]
final readonly class ObjectiveRequestPayload
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

        #[Assert\NotBlank(message: 'La durée ne peut pas être vide.')]
        #[Assert\Range(
            notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }}.',
            min: 1,
            max: 30
        )]
        public int $duration,
    ) {}
}
