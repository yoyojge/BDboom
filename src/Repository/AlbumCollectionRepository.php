<?php

namespace App\Repository;

use App\Entity\AlbumCollection;
use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AlbumCollection>
 *
 * @method AlbumCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlbumCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlbumCollection[]    findAll()
 * @method AlbumCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlbumCollection::class);
    }

    public function save(AlbumCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AlbumCollection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    //test function custom pour recuperer details des livres d'une collection ::  ne semble pas fonctionner !!!
    public function findByCollectionId($value): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.id = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getResult()
       ;
   }



//    /**
//     * @return AlbumCollection[] Returns an array of AlbumCollection objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AlbumCollection
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
