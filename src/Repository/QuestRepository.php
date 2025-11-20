<?php

namespace App\Repository;

use App\Entity\Quest;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Quest>
 */
class QuestRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quest::class);
    }
}
