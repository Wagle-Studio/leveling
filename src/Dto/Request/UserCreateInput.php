<?php

namespace App\Dto\Request;

use App\Entity\User;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: User::class)]
final readonly class UserCreateInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le Discord ID ne peut pas être vide.')]
        #[Assert\Regex(
            pattern: '/^\d+$/',
            message: 'Le Discord ID doit être un nombre valide.'
        )]
        #[Assert\Length(
            min: 17,
            max: 19,
            minMessage: 'Le Discord ID doit faire au moins {{ limit }} caractères.',
            maxMessage: 'Le Discord ID ne peut pas faire plus de {{ limit }} caractères.'
        )]
        public string $discordId,
    ) {}
}
