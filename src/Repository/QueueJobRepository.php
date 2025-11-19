<?php

namespace App\Repository;

use App\Entity\QueueJob;
use App\Libs\Queue\QueueJobStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QueueJob>
 */
class QueueJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QueueJob::class);
    }

    public function findNextPending(): ?QueueJob
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.status = :status')
            ->setParameter('status', QueueJobStatusEnum::PENDING->value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function claimNextPending(): ?QueueJob
    {
        $connection = $this->getEntityManager()->getConnection();

        try {
            $connection->beginTransaction();

            $sql = 'SELECT id FROM queue_job WHERE status = :status ORDER BY id ASC LIMIT 1 FOR UPDATE SKIP LOCKED';
            $result = $connection->executeQuery($sql, ['status' => QueueJobStatusEnum::PENDING->value]);
            $row = $result->fetchAssociative();

            if (!$row) {
                $connection->commit();
                return null;
            }

            $jobId = $row['id'];

            $connection->executeStatement(
                'UPDATE queue_job SET status = :newStatus WHERE id = :id',
                ['newStatus' => QueueJobStatusEnum::RUNNING->value, 'id' => $jobId]
            );

            $connection->commit();
            $this->getEntityManager()->clear();
            return $this->find($jobId);
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function save(QueueJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QueueJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
