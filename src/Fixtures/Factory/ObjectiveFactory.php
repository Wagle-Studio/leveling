<?php

namespace App\Fixtures\Factory;

use App\Entity\Objective;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class ObjectiveFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Objective::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->text(255),
            'duration' => self::faker()->numberBetween(1, 30),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Branch $branch): void {})
        ;
    }
}
