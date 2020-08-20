<?php


namespace App\Repository;


use App\Entity\BphsHealthFacility;
use App\Entity\BphsIndicatorReach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class BphsIndicatorReachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BphsIndicatorReach::class);
    }

    /**
     * @return int|mixed|string
     */
    public function findYearMonth($year = null) {
        $condition = "";
        if($year !== null)
            $condition = " WHERE indReach.reportYear in (:years) ";
        $query = $this ->getEntityManager()
            ->createQuery("SELECT DISTINCT 
                           CONCAT(indReach.reportYear, CONCAT('-', indReach.reportMonth)) as yearMonth 
                           FROM App:BphsIndicatorReach indReach ".$condition."
                           ORDER BY 
                           STR_TO_DATE(CONCAT(indReach.reportYear, CONCAT(indReach.reportMonth, '01')), '%Y%b%d') 
                           DESC
                           ");
        if($year !== null)
            $query->setParameter('years', $year);
        return $query->getResult();
    }

    public function findReportedMonths() {
        $currentYear = date('Y');
        $yearMonths = $this->findYearMonth($currentYear);
        if(!is_array($yearMonths) || count($yearMonths) < 1) {
            $prevYear = true;
            while ($prevYear) {
                $currentYear -= 1;
                $yearMonths = $this->findYearMonth($currentYear);
                if(is_array($yearMonths) && count($yearMonths) > 0) {
                    $prevYear = false;
                }
            }
        }

        return $yearMonths;
    }

    /**
     * @param null|array $yearMonth
     * @return int|mixed|string
     */
    public function findProvinces($yearMonth = null) {

        list($params, $condition) = $this->yearMonthCondition($yearMonth);

        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT prov.id as id, prov.provinceName as provinceName 
                           FROM App:BphsIndicatorReach indReach 
                           JOIN indReach.hfCode as hf 
                           JOIN hf.district as dist 
                           JOIN dist.province as prov 
                           '.($condition !== "" ? " WHERE ".$condition : "") );

        $query = $this->setParameters($query, $params);
        return $query->getResult();
    }

    /**
     * @param null|array $yearMonth
     * @param null|array $provinces
     * @return int|mixed|string
     */
    public function findDistricts($yearMonth = null, $provinces = null) {

        list($params, $condition) = $this->yearMonthCondition($yearMonth);
        $condition = $this->createCondition($condition, $provinces);
        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT dist.id as id, dist.districtName as districtName,
                           prov.id as pid, prov.provinceName as provinceName 
                           FROM App:BphsIndicatorReach indReach 
                           JOIN indReach.hfCode as hf 
                           JOIN hf.district as dist 
                           JOIN dist.province as prov
                           '.($condition !== "" ? " WHERE ".$condition : ""));

        $query = $this->setParameters($query, $params, $provinces);
        return $query->getResult();
    }

    /**
     * @param null|array $yearMonth
     * @param null|array $provinces
     * @param null|array $districts
     * @return int|mixed|string
     */
    public function findHealthFacilities($yearMonth = null, $provinces = null, $districts = null) {
        list($params, $condition) = $this->yearMonthCondition($yearMonth);
        $condition = $this->createCondition($condition, $provinces, $districts);
        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT hf.id as id, hf.facilityName as facilityName,
                           dist.id as did, dist.districtName as districtName
                           FROM App:BphsIndicatorReach indReach 
                           JOIN indReach.hfCode as hf 
                           JOIN hf.district as dist
                           '.($condition !== "" ? " WHERE ".$condition : ""));

        $query = $this->setParameters($query, $params, $provinces, $districts);
        return $query->getResult();
    }

    /**
     * @param null|array $yearMonth
     * @param null|array $provinces
     * @param null|array $districts
     * @param null|array $facilities
     * @return int|mixed|string
     */
    public function findMappedIndicators($yearMonth = null, $provinces = null, $districts = null, $facilities = null)
    {
        list($params, $condition) = $this->yearMonthCondition($yearMonth);
        $condition = $this->createCondition($condition, $provinces, $districts, $facilities);
        $query = $this ->getEntityManager()
            ->createQuery('SELECT DISTINCT ind.id as id, ind.name as indicatorName 
                           FROM App:BphsIndicatorReach indReach 
                           JOIN indReach.indicator as ind 
                           JOIN indReach.hfCode as hf
                           JOIN hf.district as dist
                           '.($condition !== "" ? " WHERE ".$condition : ""));

        $query = $this->setParameters($query, $params, $provinces, $districts, $facilities);
        return $query->getResult();
    }

    /**
     * @param $yearMonth
     * @return array
     */
    private function yearMonthCondition($yearMonth): array
    {
        $params = null;
        $condition = "";
        if ($yearMonth !== null) {
            $condition = " indReach.reportYear IN (:rYear) AND indReach.reportMonth IN (:rMonth) ";
            $years = [];
            $months = [];
            $yearMonth = $this->checkArrayDimension($yearMonth);
            foreach ($yearMonth as $value) {
                $tmp = explode("-", $value);
                $years[] = $tmp[0];
                $months[] = $tmp[1];
            }
            $params[] = $years;
            $params[] = $months;
        }
        return array($params, $condition);
    }

    /**
     * @param $condition
     * @param null $provinces
     * @param null $districts
     * @param null $facilities
     * @return string
     */
    private function createCondition($condition, $provinces = null, $districts = null, $facilities = null): string
    {
        if ($provinces !== null) {
            $provCondition = "dist.province IN (:provinces)";
            $condition .= $condition !== "" ? " AND " : "";
            $condition .= $provCondition;
        }
        if ($districts !== null) {
            $distCondition = "hf.district IN (:districts)";
            $condition .= $condition !== "" ? " AND " : "";
            $condition .= $distCondition;
        }
        if ($facilities !== null) {
            $hfCondition = "hf.id IN (:facilities)";
            $condition .= $condition !== "" ? " AND " : "";
            $condition .= $hfCondition;
        }
        return $condition;
    }

    /**
     * @param Query $query
     * @param $params
     * @param $provinces
     * @param $districts
     * @param $facilities
     * @return Query
     */
    private function setParameters(\Doctrine\ORM\Query $query, $params = null, $provinces = null,
                                   $districts = null, $facilities = null): Query
    {
        if ($params !== null) {
            $query->setParameters(['rYear' => $params[0], 'rMonth' => $params[1]]);
        }
        if ($provinces !== null) {
            $query->setParameter('provinces', $provinces);
        }
        if ($districts !== null) {
            $query->setParameter('districts', $districts);
        }
        if ($facilities !== null) {
            $query->setParameter('facilities', $facilities);
        }

        return $query;
    }


    public function checkArrayDimension($inputArray)
    {
        if(!is_array($inputArray))
            return [$inputArray];
        if(is_array($inputArray) &&
            (count($inputArray) !== count($inputArray, COUNT_RECURSIVE))
           ) {
            $newFlatArray = array(); // we consider only first element of the second array
            foreach ($inputArray as $value) {
                foreach ($value as $key=>$item)
                {
                    $newFlatArray[] = $item;
                    break; // we just need the first item
                }
            }

            return $newFlatArray;
        }

        return $inputArray;
    }

    public function getReachByMonths($yearMonth = null, $provinces = null, $districts = null, $facilities = null) {
        list($params, $condition) = $this->yearMonthCondition($yearMonth);

        $condition = $this->createCondition($condition, $provinces, $districts, $facilities);
        list($selection, $join, $groupBy, $orderBy) = $this->createSelectionJoinGroupBy($provinces, $districts, $facilities, true);
        $groupBy .= ", indReach.reportYear, indReach.reportMonth";
        $orderBy .= ", STR_TO_DATE(CONCAT(indReach.reportYear, CONCAT(indReach.reportMonth, '01')), '%Y%b%d') ASC, ind.shortName";
        $query = $this ->getEntityManager()
            ->createQuery("SELECT ".$selection.", 
                           CONCAT(indReach.reportYear, CONCAT('-', indReach.reportMonth)) as yearMonth, 
                           ".$this->queryText().'
                           '.$join.'
                           '.($condition !== "" ? " WHERE ".$condition : "").' '.$groupBy.' '.$orderBy);
        $query = $this->setParameters($query, $params, $provinces, $districts, $facilities);
        return $query->getResult();
    }


    public function getReachByMonthsFacilities($yearMonth = null, $provinces = null, $districts = null, $facilities = null) {
        list($params, $condition) = $this->yearMonthCondition($yearMonth);
        $condition = $this->createCondition($condition, $provinces, $districts, $facilities);
        list($selection, $join, $groupBy) = $this->createSelectionJoinGroupBy($provinces, $districts, $facilities);
        $query = $this ->getEntityManager()
            ->createQuery('SELECT '.$selection.', 
                           '.$this->queryText().'
                           '.$join.'
                           '.($condition !== "" ? " WHERE ".$condition : "").' '.$groupBy);

        $query = $this->setParameters($query, $params, $provinces, $districts, $facilities);
        return $query->getResult();
    }


    private function queryText() {
        return " ind.shortName as indicator, SUM(indReach.reach) as totalReach,
                           ROUND(sum(hfInd.annualTarget)/count(distinct indReach.reportMonth), 0) as target, 
                           ROUND((sum(hfInd.annualTarget)/count(distinct indReach.reportMonth))/12, 0) as monthlyTarget,
                           COUNT(DISTINCT indReach.reportMonth) as noMonths,
                           ROUND(SUM(indReach.reach)/ROUND(sum(hfInd.annualTarget)/count(distinct indReach.reportMonth), 0), 2) as overallProgress, 
                           ROUND(SUM(indReach.reach)/((sum(hfInd.annualTarget)/count(distinct indReach.reportMonth))/12 * count( distinct indReach.reportMonth)), 2) as currentProgress,  
                           count( distinct indReach.reportMonth) as noMonthsReported
                           FROM App:BphsIndicatorReach indReach 
                           JOIN indReach.bphsHfIndicator as hfInd
                           JOIN indReach.indicator as ind
                           JOIN indReach.hfCode as hf ";
    }

    /**
     * @param null $provinces
     * @param null $districts
     * @param null $facilities
     * @return array
     */
    private function createSelectionJoinGroupBy($provinces = null, $districts = null, $facilities = null, $byMonth=false): array
    {
        $selection = "";
        $join = "";
        $orderBy = " ORDER BY prov.provinceName ";
        $groupBy = "GROUP BY prov.id, indReach.indicator";
        //if ($provinces !== null) {
            $selection = " prov.id as id, prov.provinceName as provinceName ";
            if($byMonth === true)
                $selection = " CONCAT(prov.id, ind.id) as id, prov.provinceName as provinceName, ind.shortName as indicatorName ";
            $join = "JOIN hf.district as dist 
                     JOIN dist.province as prov ";
        //}
        if ($districts !== null) {
            $orderBy = " ORDER BY prov.provinceName, dist.id ";
            $selection = " dist.id as id, prov.provinceName as provinceName, dist.districtName as districtName ";
            if($byMonth === true)
                $selection = " CONCAT(dist.id, ind.id) as id, 
                               prov.provinceName as provinceName, dist.districtName as districtName, ind.shortName as indicatorName ";
            $groupBy = "GROUP BY prov.id, dist.id, indReach.indicator";
        }
        if ($facilities !== null) {
            $orderBy = " ORDER BY prov.provinceName, dist.id, hf.id ";
            $selection = " hf.id as id, prov.provinceName as provinceName, 
                           dist.districtName as districtName, hf.facilityName ";
            if($byMonth === true)
                $selection = " CONCAT(hf.id, ind.id) as id, 
                               prov.provinceName as provinceName, dist.districtName as districtName, hf.facilityName, ind.shortName as indicatorName ";
            $groupBy = "GROUP BY prov.id, dist.id, hf.id, indReach.indicator";
        }
        return [$selection, $join, $groupBy, $orderBy];
    }
}
