<?php

namespace App\Repository;

use App\Entity\Interpretation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Interpretation>
 *
 * @method Interpretation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Interpretation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Interpretation[]    findAll()
 * @method Interpretation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterpretationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interpretation::class);
    }

    public function save(Interpretation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Interpretation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithUser(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a.pseudo, a.ImageProfil, m.titre, m.description, m.date FROM  App\Entity\Interpretation m
            JOIN m.idUser a
            WHERE m.idMusique = :id"
        )->setParameter('id',$id);

        // returns an array of Product objects
        return $query->getResult();
    }

//    /**
//     * @return Interpretation[] Returns an array of Interpretation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Interpretation
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
