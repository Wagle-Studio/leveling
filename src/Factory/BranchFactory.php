<?php

namespace App\Factory;

use App\Entity\Branch;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class BranchFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Branch::class;
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
            // ->afterInstantiate(function(Branch $branch): void {})
        ;
    }
}
