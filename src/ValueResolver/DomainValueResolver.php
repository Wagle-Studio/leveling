<?php

namespace App\ValueResolver;

use App\Entity\Domain;
use App\Repository\DomainRepository;
use App\ValueResolver\AbstractValueResolver;

final class DomainValueResolver extends AbstractValueResolver
{
    public function __construct(
        private readonly DomainRepository $domainRepository
    ) {
        parent::__construct(
            entityName: Domain::class,
            entityField: 'id',
            entityRepository: $domainRepository,
            validateFieldFn: [$this, 'validateFieldFn']
        );
    }

    protected function validateFieldFn(mixed $entityIdentifier): bool
    {
        return is_int($entityIdentifier) && $entityIdentifier > 0;
    }
}
