<?php


namespace App\Repository;


use App\Entity\BphsHealthFacility;
use App\Entity\BphsIndicatorReach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BphsIndicatorReachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BphsIndicatorReach::class);
    }

}
