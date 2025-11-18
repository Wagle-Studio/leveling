<?php

namespace App\ValueResolver;

use App\Entity\Step;
use App\Repository\StepRepository;
use App\ValueResolver\AbstractValueResolver;

final class StepValueResolver extends AbstractValueResolver
{
    public function __construct(
        private readonly StepRepository $stepRepository
    ) {
        parent::__construct(
            entityName: Step::class,
            entityField: 'id',
            entityRepository: $stepRepository,
            validateFieldFn: [$this, 'validateFieldFn']
        );
    }

    protected function validateFieldFn(mixed $entityIdentifier): bool
    {
        return is_int($entityIdentifier) && $entityIdentifier > 0;
    }
}
