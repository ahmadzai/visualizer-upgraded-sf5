<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 1/23/2018
 * Time: 8:22 PM
 */

namespace App\Repository;

use App\Entity\Province;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class ProvinceRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Province::class);
    }

    /***
     * @return array
     */
    public function selectAllDistricts() {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT d FROM App:DistrictData d"
            )
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function selectAllRegions() {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT p.provinceRegion as provinceRegion FROM App:Province p ORDER BY p.provinceRegion"
            )
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function selectRegionsBySourceAndCampaign($source, $campaigns) {
        $entity = "App:".$source;
        return $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT p.provinceRegion as provinceRegion
                      FROM $entity s JOIN s.district d JOIN d.province p
                      WHERE s.campaign in (:camp) ORDER BY provinceRegion")
            ->setParameter('camp', $campaigns)
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function selectProvinceBySourceRegionAndCampaign($source, $region, $campaigns) {
        $entity = "App:".$source;
        return $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT p.id as p_id, p.provinceRegion as p_provinceRegion, 
                      p.provinceName as p_provinceName
                      FROM $entity s JOIN s.district d JOIN d.province p
                      WHERE s.campaign in (:camp) AND p.provinceRegion in (:reg) ORDER BY p.provinceName")
            ->setParameters(['camp' => $campaigns, 'reg' => $region])
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function selectProvinceBySourceAndCampaign($source, $campaigns) {
        $entity = "App:".$source;
        return $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT p.id, p.provinceRegion, 
                      p.provinceName
                      FROM $entity s JOIN s.district d JOIN d.province p
                      WHERE s.campaign in (:camp) ORDER BY p.provinceRegion, p.provinceName")
            ->setParameter('camp' ,$campaigns)
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function selectProvinceByRegion($region) {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT p FROM App:Province p WHERE p.provinceRegion IN (:region) ORDER BY p.provinceRegion"
            ) ->setParameter('region', $region)
            ->getResult(Query::HYDRATE_SCALAR);
    }

    public function searchProvinces($term) {
        $term = $term ? $term : '';
        return $this->getEntityManager()
            ->getRepository('App:Province')
            ->createQueryBuilder('p')
            ->where('p.provinceName LIKE :term')
            ->setParameter('term', $term.'%')
            ->getQuery()
            ->getResult();
    }


}
