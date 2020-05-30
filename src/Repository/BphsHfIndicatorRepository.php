<?php


namespace App\Repository;


use App\Entity\BphsHfIndicator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BphsHfIndicator|null find($id, $lockMode = null, $lockVersion = null)
 * @method BphsHfIndicator|null findOneBy(array $criteria, array $orderBy = null)
 * @method BphsHfIndicator[]    findAll()
 * @method BphsHfIndicator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BphsHfIndicatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BphsHfIndicator::class);
    }

}
