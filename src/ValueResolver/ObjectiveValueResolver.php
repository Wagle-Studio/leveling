<?php

namespace App\ValueResolver;

use App\Entity\Objective;
use App\Repository\ObjectiveRepository;
use App\ValueResolver\AbstractValueResolver;

final class ObjectiveValueResolver extends AbstractValueResolver
{
    public function __construct(
        private readonly ObjectiveRepository $objectiveRepository
    ) {
        parent::__construct(
            entityName: Objective::class,
            entityField: 'id',
            entityRepository: $objectiveRepository,
            validateFieldFn: [$this, 'validateFieldFn']
        );
    }

    protected function validateFieldFn(mixed $entityIdentifier): bool
    {
        return is_int($entityIdentifier) && $entityIdentifier > 0;
    }
}
