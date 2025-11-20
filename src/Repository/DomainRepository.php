<?php

namespace App\Repository;

use App\Entity\Domain;
use Doctrine\Persistence\ManagerRegistry;

final class DomainRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
    }
}
