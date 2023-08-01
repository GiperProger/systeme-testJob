<?php

namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Bundle\MakerBundle\Str;

/**
 * @extends ServiceEntityRepository<Tax>
 *
 * @method Tax|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tax|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tax[]    findAll()
 * @method Tax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

    public function save(Tax $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tax $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllFormats(): array
    {
        $formatsSimple = [];

        $formatList = $this->createQueryBuilder('t')
            ->select('t.format')
            ->getQuery()
            ->getArrayResult();

        foreach($formatList as $format){
            $formatsSimple[] = $format['format'];
        }

        return $formatsSimple;
    }

    public function findByTemplate($template): ?Tax
    {
          return $this->createQueryBuilder('t')
            ->select('t')
            ->andWhere('t.format = :format')
            ->setParameter('format', $template)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
