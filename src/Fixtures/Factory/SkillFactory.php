<?php

namespace App\Fixtures\Factory;

use App\Entity\Skill;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class SkillFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Skill::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Skill $skill): void {})
        ;
    }
}
