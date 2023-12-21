<?php

namespace App\Repository;

use App\Entity\Candys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Candys>
 *
 * @method Candys|null find($id, $lockMode = null, $lockVersion = null)
 * @method Candys|null findOneBy(array $criteria, array $orderBy = null)
 * @method Candys[]    findAll()
 * @method Candys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candys::class);
    }

    public function findCandysPaginated(int $page, string $slug, 
    int $limit = 6): array
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('ct', 'cd')
            ->from('App\Entity\Candys', 'cd')
            ->join('cd.categories', 'ct')
            ->where("ct.slug = '$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        //on verifie qu'on a des données
        if(empty($data)){
            return $result;
        }

        //on calcule le nombre de pages
        $pages = ceil($paginator->count() / $limit);

        //on remplit le tableau
        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }

//    /**
//     * @return Candys[] Returns an array of Candys objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Candys
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
