<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class SkillDiscoverInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le message ne peut pas être vide.')]
        #[Assert\Length(
            min: 5,
            max: 255,
            minMessage: 'Le message doit faire au moins {{ limit }} caractères.',
            maxMessage: 'Le message ne peut pas faire plus de {{ limit }} caractères.'
        )]
        public string $message,
    ) {}
}
