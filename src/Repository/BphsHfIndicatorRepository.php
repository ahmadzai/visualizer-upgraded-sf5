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

    public function getAssignedIndicators($year = null) {
        $year = $year ?? date('Y');

        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT ind.shortName as indicatorName FROM App:BphsHfIndicator hfInd 
                            JOIN hfInd.bphsIndicator as ind WHERE hfInd.targetYear IN (:year) ');

            $query->setParameter('year', $year);
        return $query->getResult();
    }

    public function findIndicators($indicators, $year = null) {
        $year = $year ?? date('Y');

        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT ind.id as id, ind.shortName as indicatorName FROM App:BphsHfIndicator hfInd
                            JOIN hfInd.bphsIndicator as ind WHERE hfInd.targetYear = (:year) AND ind.shortName IN (:indicators)');

        $query->setParameters(['year' => $year, 'indicators' => $indicators]);
        return $query->getResult();
    }

    public function findHealthFacilities($facilities, $year = null) {
        $year = $year ?? date('Y');

        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT hf.id as id, hf.facilityName as name FROM App:BphsHfIndicator hfInd 
                            JOIN hfInd.bphsHealthFacility as hf WHERE hfInd.targetYear = (:year) AND hf.id IN (:codes)');

        $query->setParameters(['year' => $year, 'codes' => $facilities]);
        return $query->getResult();
    }

}
