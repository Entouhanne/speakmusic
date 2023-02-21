<?php

namespace App\Repository;

use App\Entity\Musique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Musique>
 *
 * @method Musique|null find($id, $lockMode = null, $lockVersion = null)
 * @method Musique|null findOneBy(array $criteria, array $orderBy = null)
 * @method Musique[]    findAll()
 * @method Musique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MusiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Musique::class);
    }

    public function save(Musique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Musique $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithMusique(string $query): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT g.Illustration, COUNT(c.date) as nb_interpretation, m.nom,  m.id, a.nom as auteur, 'musique' FROM  App\Entity\Musique m
            JOIN m.idAuteur a
            JOIN m.idAlbum g
            LEFT JOIN m.interpretations c
            WHERE m.nom LIKE :query
            GROUP BY m.nom, g.Illustration, m.id, a.nom"
        )->setParameter('query', '%'.$query.'%');

        // returns an array of Product objects
        return $query->getResult();
    }


    public function findWithAuteur(string $query): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT t.libelle as type,a.dateFormation, g.nom as genre, c.nom as pays, a.nom, a.Illustration, a.id as id , 'auteur' FROM  App\Entity\Auteur a
            JOIN a.pays c
            JOIN a.idGenre g
            JOIN a.type t
            WHERE a.nom LIKE :query"
        )->setParameter('query', '%'.$query.'%');

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findWithAlbum(string $query): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT a.Illustration, a.id, a.nom , 'album' FROM  App\Entity\Album a
            WHERE a.nom LIKE :query"
        )->setParameter('query', '%'.$query.'%');

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findAllLike(string $query): array
    {
        $notification = $this->findWithMusique($query);
        $append = $this->findWithAuteur($query);
        $album = $this->findWithAlbum($query);
        return array_merge($notification, $append, $album);
    }

//    /**
//     * @return Musique[] Returns an array of Musique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Musique
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
