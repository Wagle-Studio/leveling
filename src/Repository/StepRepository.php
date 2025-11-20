<?php

namespace App\Repository;

use App\Entity\Step;
use Doctrine\Persistence\ManagerRegistry;

final class StepRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Step::class);
    }
}
