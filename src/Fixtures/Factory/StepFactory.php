<?php

namespace App\Fixtures\Factory;

use App\Entity\Step;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class StepFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Step::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->text(255),
            'instruction' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Domain $domain): void {})
        ;
    }
}
