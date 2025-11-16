<?php

namespace App\Factory;

use App\Entity\Domain;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class DomainFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Domain::class;
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
            // ->afterInstantiate(function(Domain $domain): void {})
        ;
    }
}
