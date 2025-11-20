<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Persistence\ManagerRegistry;

final class SkillRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }
}
