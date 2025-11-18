<?php

namespace App\Dto\Request;

use App\Entity\Domain;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: Domain::class)]
final readonly class DomainRequestPayload
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
    ) {}
}
