<?php

namespace App\ValueResolver;

use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\ValueResolver\AbstractValueResolver;

final class SkillValueResolver extends AbstractValueResolver
{
    public function __construct(
        private readonly SkillRepository $skillRepository
    ) {
        parent::__construct(
            entityName: Skill::class,
            entityField: 'id',
            entityRepository: $skillRepository,
            validateFieldFn: [$this, 'validateFieldFn']
        );
    }

    protected function validateFieldFn(mixed $entityIdentifier): bool
    {
        return is_int($entityIdentifier) && $entityIdentifier > 0;
    }
}
