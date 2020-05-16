<?php


namespace App\Repository;


use App\Entity\Campaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
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

    public function selectCampaignBySource($source) {
        $entity = "App:".$source;
        return $this->getEntityManager()
            ->createQuery(
                "SELECT DISTINCT cmp.id, cmp.campaignName FROM $entity s JOIN s.campaign cmp ORDER BY cmp.id DESC "
            )
            ->getResult();
    }

    public function searchCampaigns($term) {
        $term = $term ? $term : '';
        return $this->getEntityManager()
            ->getRepository('App:Campaign')
            ->createQueryBuilder('p')
            ->where('p.campaignName LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
