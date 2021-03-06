<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function findByOrder(): array
    {
        return $this->createQueryBuilder('c')
        ->orderBy('c.creation_date', 'DESC')
        ->getQuery()
        ->getResult()
    ;
    }

    public function countComments($trick)
    {
        try {
            return intval($this->createQueryBuilder('c')
                ->select('count(c)')
                ->where('c.trick = :trick')
                ->setParameter('trick', $trick)
                ->getQuery()
                ->getSingleScalarResult());
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getCommentsByLimit($trick, $first_result, $max_results = 10)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->setFirstResult($first_result)
            ->setMaxResults($max_results)
            ->orderBy('c.creation_date', 'DESC')
            ->getQuery()
            ->getResult();

    }


    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
