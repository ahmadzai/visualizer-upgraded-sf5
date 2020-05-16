<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 1/23/2018
 * Time: 9:01 PM
 */

namespace App\Repository;

use App\Entity\District;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class DistrictRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, District::class);
    }

    public function selectDistrictByProvince($province) {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT d, p.id, p.provinceName FROM App:District d JOIN d.province p WHERE d.province IN (:province) ORDER BY d.province"
            ) ->setParameter('province', $province)
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function selectDistrictBySourceProvinceAndCampaign($source, $province, $campaign) {
        $entity = "App:".$source;
        return $this->getEntityManager()
            ->createQuery(
                "SELECT distinct d.id as d_id, d.districtName as d_districtName, d.districtRiskStatus as d_districtRiskStatus, 
                     p.id, p.provinceName 
                     FROM $entity s JOIN s.district d JOIN d.province p 
                     WHERE p.id IN (:province) and s.campaign in (:camp) ORDER BY p.provinceName, d.districtName"
            ) ->setParameters(['province'=>$province, 'camp'=>$campaign])
            ->getResult(Query::HYDRATE_SCALAR);
    }


    public function searchDistricts($term) {
        $term = $term ? $term : '';
        return $this->getEntityManager()
            ->getRepository('App:District')
            ->createQueryBuilder('p')
            ->where('p.districtName LIKE :term')
            ->setParameter('term', $term.'%')
            ->getQuery()
            ->getResult();
    }

}
