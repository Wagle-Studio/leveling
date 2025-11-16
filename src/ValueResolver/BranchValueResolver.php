<?php

namespace App\ValueResolver;

use App\Entity\Branch;
use App\Repository\BranchRepository;
use App\ValueResolver\AbstractValueResolver;

final class BranchValueResolver extends AbstractValueResolver
{
    public function __construct(
        private readonly BranchRepository $branchRepository
    ) {
        parent::__construct(
            entityName: Branch::class,
            entityField: 'id',
            entityRepository: $branchRepository,
            validateFieldFn: [$this, 'validateFieldFn']
        );
    }

    protected function validateFieldFn(mixed $entityIdentifier): bool
    {
        return is_int($entityIdentifier) && $entityIdentifier > 0;
    }
}
