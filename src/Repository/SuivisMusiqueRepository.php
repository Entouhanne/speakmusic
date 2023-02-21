<?php

namespace App\Repository;

use App\Entity\SuivisMusique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuivisMusique>
 *
 * @method SuivisMusique|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuivisMusique|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuivisMusique[]    findAll()
 * @method SuivisMusique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuivisMusiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuivisMusique::class);
    }

    public function save(SuivisMusique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuivisMusique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     public function findWithMusique(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a.nom, a.id, m.id as idSuivis FROM  App\Entity\SuivisMusique m
            JOIN m.idMusique a
            WHERE m.idUser = :id"
        )->setParameter('id',$id);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findForDeleting(int $idUser, int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT m.id FROM  App\Entity\SuivisMusique m
            JOIN m.idUser a
            JOIN m.idMusique c
            WHERE m.idUser = :idUser AND  c.id = :id"
        )->setParameter('idUser',$idUser)
        ->setParameter('id', $id)
        ->setMaxResults(1);

        // returns an array of Product objects
        return $query->getResult()[0];
    }

//    /**
//     * @return SuivisMusique[] Returns an array of SuivisMusique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SuivisMusique
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
