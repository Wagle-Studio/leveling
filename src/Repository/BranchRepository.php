<?php

namespace App\Repository;

use App\Entity\Branch;
use Doctrine\Persistence\ManagerRegistry;

final class BranchRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Branch::class);
    }
}
