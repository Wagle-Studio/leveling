<?php

namespace App\Repository;

use App\Entity\QueueJob;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<QueueJob>
 */
final class QueueJobRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueueJob::class);
    }
}
