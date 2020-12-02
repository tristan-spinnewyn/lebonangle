<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function countByStatus($value){
        try {
            return $this->createQueryBuilder('a')
                ->select('COUNT(a.id) as nb_advert')
                ->andWhere('a.state = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getScalarResult();
        }
        catch (NonUniqueResultException|NoResultException $e) {
            return 0;
        }
    }

    public function countAdvertInCategory(Category $category)
    {
        try {
            return $this->createQueryBuilder('advert')
                ->select('COUNT(advert.id) as nbAdvert')
                ->where('advert.category = :category')
                ->setParameter('category',$category)
                ->getQuery()
                ->getScalarResult();
        }catch (NonUniqueResultException|NoResultException $e) {
            return 0;
        }
    }

    public function getPaginateAdmin(int $page){
        return $this
            ->createQueryBuilder('advert')
            ->setFirstResult(30*($page-1))
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }

    public function getPaginateAdminByState(int $page, string $state,string $orderEnt, string $order){
        return $this
            ->createQueryBuilder('advert')
            ->andWhere('advert.state = :val')
            ->setParameter('val', $state)
            ->setFirstResult(30*($page-1))
            ->setMaxResults(30)
            ->orderBy($orderEnt, $order)
            ->getQuery()
            ->getResult();
    }

    public function countPaginateAdmin()
    {
        try{
            return $this
                ->createQueryBuilder('advert')
                ->select('COUNT(advert.id) as nb_advert')
                ->getQuery()
                ->getScalarResult();
        } catch (NonUniqueResultException|NoResultException $e){
            return 0;
        }

    }
}
