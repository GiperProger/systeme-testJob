<?php

namespace App\Repository;

use App\Entity\PaymentHash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentHash>
 *
 * @method PaymentHash|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentHash|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentHash[]    findAll()
 * @method PaymentHash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentHashRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentHash::class);
    }

    public function save(PaymentHash $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(?PaymentHash $entity, bool $flush = false): void
    {
        if($entity === null){
            return;
        }

        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByHash($hash): ?PaymentHash
    {
          return $this->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.hash = :format')
            ->setParameter('format', $hash)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
