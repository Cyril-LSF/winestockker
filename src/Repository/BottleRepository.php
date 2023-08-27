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

    public function findByFilter(User $user, array $data, Object $entity = null, bool $admin = false)
    {
        $results = $this->createQueryBuilder('b');
            if (!$admin) {
                $results->where('b.author = :author')
                    ->setParameter('author', $user);
            }
        if ($entity) {
            $param = explode('\\', get_class($entity));
            switch (end($param)) {
                case 'Cellar':
                    $results->andWhere(':entity MEMBER OF b.cellars');
                    break;
                case 'Category':
                    $results->andWhere(':entity MEMBER OF b.categories');
                    break;
                default:
                    break;
            }
            $results->setParameter('entity', $entity);
        }
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'name':
                    $results->andWhere("b.name LIKE :$key")
                        ->setParameter($key, '%' . $value . '%');
                    break;
                case 'price':
                    $results->andWhere("b.price BETWEEN 0 AND :$key")
                        ->setParameter($key, $value);
                    break;
                case 'categories':
                    $results->andWhere(":$key MEMBER OF b.categories")
                        ->setParameter($key, $value);
                    break;
                default:
                    $results->andWhere("b.$key = :$key")
                        ->setParameter($key, $value);
                    break;
            }
        }
        return $results->getQuery()->getResult();
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
