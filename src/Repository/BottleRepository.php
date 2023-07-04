<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Bottle;
use App\Entity\Cellar;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Bottle>
 *
 * @method Bottle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bottle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bottle[]    findAll()
 * @method Bottle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BottleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bottle::class);
    }

    public function save(Bottle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bottle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getCellarBotlles(Cellar $cellar)
    {
        return $this->createQueryBuilder('b')
            ->where(':cellar MEMBER OF b.cellars')
            ->setParameters(['cellar' => $cellar])
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(User $user, string $search)
    {
        return $this->createQueryBuilder('b')
            ->where('b.name LIKE :val')
            ->setParameters(['val' => '%' . $search . '%'])
            ->andWhere("b.author = $user")
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Bottle[] Returns an array of Bottle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bottle
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
