<?php

namespace App\Repository;

use App\Entity\Objective;
use Doctrine\Persistence\ManagerRegistry;

final class ObjectiveRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objective::class);
    }
}
