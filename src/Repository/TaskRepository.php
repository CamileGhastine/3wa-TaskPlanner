<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return array
     */
    public function findAllTaskWithUserAndCategory(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
            ->addSelect('u')
            ->leftJoin('t.categories', 'c')
            ->addSelect('c')
            ->orderBy('t.expiratedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     * @return Task|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findTaskByIdWithUserCategoryAndTag($id): ?Task
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
            ->addSelect('u')
            ->leftJoin('t.categories', 'c')
            ->addSelect('c')
            ->leftJoin('t.tags', 'tag')
            ->addSelect('tag')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->orderBy('t.expiratedAt', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

public function findAllTaskWithUserAndCategoryByCategory(int $id): array
{
    return $this->createQueryBuilder('t')
        ->leftJoin('t.user', 'u')
        ->addSelect('u')
        ->leftJoin('t.categories', 'c')
        ->addSelect('c')
        ->where('c.id = :id')
        ->setParameter('id', $id)
        ->orderBy('t.expiratedAt', 'ASC')
        ->getQuery()
        ->getResult();
}

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByKeyWOrd($word)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
            ->addSelect('u')
            ->leftJoin('t.categories', 'c')
            ->addSelect('c')
            ->leftJoin('t.tags', 'tag')
            ->addSelect('tag')
            ->orWhere('tag.name LIKE :word' )
            ->orWhere('t.title LIKE :word')
            ->orWhere('c.name LIKE :word')
            ->setParameter('word', "%$word%")
            ->orderBy('t.expiratedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
