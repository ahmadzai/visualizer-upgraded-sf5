<?php
/**
 * Created by PhpStorm.
 * User: Awesome
 * Date: 8/10/2018
 * Time: 9:37 AM
 */

namespace App\Repository\CoverageCatchup;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class ChartRepo extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    protected $DQL;
    protected $entity;

    public function setDQL($DQL) {
        $this->DQL = $DQL;
    }

    public function setEntity($entity) {
        $this->entity = $entity;
    }

    /**
     * @param $function the function must be pre-defined
     * @param $parameters the parameters must be defined in the function
     * @param null $secondParam
     * @return mixed
     */
    public function callMe($function, $parameters, $secondParam = null) {
        return call_user_func(array($this, $function), $parameters, $secondParam);
    }

    /**
     * @param $districts
     * @param $campaigns
     * @return array
     */
    public function clustersByDistrictCampaign($districts, $campaigns) {
        return $this->getEntityManager()
            ->createQuery("SELECT DISTINCT cvr.clusterNo, cvr.subDistrict,
                                CASE 
                                WHEN cvr.subDistrict IS NULL 
                                THEN cvr.clusterNo 
                                ELSE CONCAT(cvr.subDistrict, '|', cvr.clusterNo)
                                END as cluster,
                                dist.id, dist.districtName 
                                FROM App:".$this->entity." cvr JOIN cvr.district dist 
                                WHERE (cvr.district IN (:districts) AND cvr.campaign IN (:campaigns))
                                ORDER BY cvr.subDistrict DESC ")
            ->setParameters(['districts'=> $districts, 'campaigns' => $campaigns])
            ->getResult(Query::HYDRATE_SCALAR);
    }


    /**
     * @param $districts
     * @return array
     */
    public function clustersByDistrict($districts) {
        return $this->getEntityManager()
            ->createQuery("SELECT DISTINCT cvr.clusterNo, cvr.subDistrict, dist.id, dist.districtName,
                                CASE 
                                WHEN cvr.subDistrict IS NULL 
                                THEN cvr.clusterNo 
                                ELSE CONCAT(cvr.subDistrict, '|', cvr.clusterNo)
                                END as cluster
                                FROM App:".$this->entity." cvr JOIN cvr.district dist 
                                WHERE (cvr.district IN (:districts))
                                ORDER BY cvr.subDistrict DESC")
            ->setParameters(['districts'=> $districts])
            ->getResult(Query::HYDRATE_SCALAR);
    }

    /**
     * @param $district
     * @param $campaigns
     * @return array
     */
    public function subDistrictByDistrict($district, $campaigns) {
        return $this->getEntityManager()
            ->createQuery("SELECT DISTINCT cvr.subDistrict, dist.id, dist.districtName
                                FROM App:".$this->entity." cvr JOIN cvr.district dist 
                                WHERE (cvr.district IN (:district) AND cvr.campaign IN (:campaigns))
                                ORDER BY cvr.subDistrict DESC ")
            ->setParameters(['district'=> $district, 'campaigns' => $campaigns])
            ->getResult(Query::HYDRATE_SCALAR);
    }


    /*
     * New Methods, Once they are in used the old methods would be deleted
     */

    /**
     * @param $campaignIds
     * @param $params ['by' => 'region', 'value' => [regions], 'district'=>[]]
     * @return array
     */
    public function campaignStatistics($campaignIds, $params = ['by'=>'campaign', 'district'=>null]) {

        $condition = $this->createCondition($params);
        //$condition2 = ($params['extra']) ? " AND $params[extra] " : '';

        $params = $params === null ? [] : $params;
        if(array_key_exists('by', $params)) {
            if ($params['by'] === "region") {
                $condition .= " AND p.provinceRegion IN (:param2) ";
            } elseif ($params['by'] === "province") {
                $condition .= " AND p.id IN (:param2) ";
            }
        }

        $em = $this->getEntityManager();
        $dq = $em->createQuery("SELECT cmp.id as joinkey, cmp.id as CID, cmp.campaignStartDate as CDate,
                  cmp.campaignType as CType, cmp.campaignYear as CYear, cmp.campaignMonth as CMonth,
                  cmp.campaignName as CName,
                  ".$this->DQL."
                  FROM App:".$this->entity." cvr JOIN cvr.campaign cmp 
                  JOIN cvr.district d JOIN d.province p
                  WHERE(cvr.campaign IN (:camps) $condition)
                  GROUP BY cvr.campaign");
        $dq->setParameter('camps', $campaignIds);

        if(strpos($condition, "param1") !== false)
            $dq->setParameter("param1", $params['district']);
        if(strpos($condition, "param2") !== false)
            $dq->setParameter("param2", $params['value']);
        if(strpos($condition, "extraParam"))
            $dq->setParameter('extraParam', $params['extra']);


        return $dq->getResult(Query::HYDRATE_SCALAR);
    }

    /**
     * @param $campaignIds
     * @param $params ['by'=>'campaign', 'district'=>null, 'value'=>[]]
     * @return mixed
     */
    public function aggByCampaign($campaignIds, $params = ['by'=>'campaign', 'district'=>null]) {

        $joinKey = "cmp.id, p.provinceRegion";
        $select = ""; // default select has region
        $groupBy = ", p.provinceRegion";
        // set the condition
        $condition = $this->createCondition($params);

        $params = $params === null ? [] : $params;
        if(array_key_exists('by', $params)) {

            if ($params['by'] === "region") {
                $condition .= " AND p.provinceRegion IN (:param2)";
            } elseif ($params['by'] === "province") {
                $joinKey = "cmp.id, p.id";
                $select = "p.id as PCODE, p.provinceName as Province, ";
                $groupBy = " ,p.id";
                $condition .= " AND p.id IN (:param2)";
            }

            // check if the district was not null
            if(isset($params['district'])) {
                $joinKey = "cmp.id, d.id";
                $groupBy = " ,p.id, cvr.district";
                $select = "p.provinceName as Province, d.districtName as District, d.id as DCODE, ";
            }
        }

        $em = $this->getEntityManager();
        $dq = $em->createQuery(
            "SELECT CONCAT(".$joinKey.") as joinkey, 
                  p.provinceRegion as Region, ".$select."
                  cmp.campaignStartDate as CDate, cmp.id as CID,
                  cmp.campaignType as CType, cmp.campaignYear as CYear, cmp.campaignMonth as CMonth,
                  cmp.campaignName as CName,
                  ".$this->DQL."
                  FROM App:".$this->entity." cvr JOIN cvr.campaign cmp
                  JOIN cvr.district d JOIN d.province p 
                  WHERE(cvr.campaign in (:campaign) ".$condition.")
                  GROUP BY cvr.campaign".$groupBy);
        $dq->setParameter('campaign', $campaignIds);

        if(strpos($condition, "param1") !== false)
            $dq->setParameter("param1", $params['district']);
        if(strpos($condition, "param2") !== false)
            $dq->setParameter("param2", $params['value']);
        if(strpos($condition, "extraParam"))
            $dq->setParameter('extraParam', $params['extra']);

        return $dq->getResult(Query::HYDRATE_SCALAR);
    }

    /**
 * @param $campaignIds
 * @param $params ['by'=>'campaign', 'district'=>null, 'value'=>[]]
 * @return mixed
 */
    public function aggByLocation($campaignIds, $params = ['by'=>'campaign', 'district'=>null]) {

        $joinKey = "p.provinceRegion";
        $select = ""; // default select has region
        $groupBy = "p.provinceRegion";
        $condition = "";

        $params = $params === null ? [] : $params;
        if(array_key_exists('by', $params)) {
            if ($params['by'] === "region") {
                $condition .= " AND p.provinceRegion IN (:param2)";
            } elseif ($params['by'] === "province") {
                $joinKey = "p.id";
                $select = "p.id as PCODE, p.provinceName as Province, ";
                $groupBy = "p.id";
                $condition .= " AND p.id IN (:param2)";
            }

            // check if the district was not null
            if (isset($params['district'])) {
                $joinKey = "d.id";
                $groupBy = "cvr.district";
                $select = "p.provinceName as Province, d.districtName as District, d.id as DCODE, ";
            }
        }

        $em = $this->getEntityManager();
        $dq = $em->createQuery(
            "SELECT ".$joinKey." as joinkey, 
                  p.provinceRegion as Region, ".$select." 
                  ".$this->DQL."
                  FROM App:".$this->entity." cvr
                  JOIN cvr.district d JOIN d.province p 
                  WHERE(cvr.campaign in (:campaign) ".$condition.")
                  GROUP BY ".$groupBy);
        $dq->setParameter('campaign', $campaignIds);

        if(strpos($condition, "param1") !== false)
            $dq->setParameter("param1", $params['district']);
        if(strpos($condition, "param2") !== false)
            $dq->setParameter("param2", $params['value']);
        if(strpos($condition, "extraParam"))
            $dq->setParameter('extraParam', $params['extra']);

        return $dq->getResult(Query::HYDRATE_SCALAR);
    }

    /**
     * @param $campaignIds
     * @param $params ['by'=>'campaign', 'district'=>null, 'value'=>[]]
     * @return mixed
     */
    public function aggBySubDistrict($campaignIds, $params = ['by'=>'campaign', 'district'=>null]) {

        $joinKey = "cmp.id, p.provinceRegion";
        $select = ""; // default select has region
        $groupBy = ", p.provinceRegion";
        // set the condition
        $condition = $this->createCondition($params);

        $params = $params === null ? [] : $params;
        if(array_key_exists('by', $params)) {
            if ($params['by'] === "region") {
                $condition .= " AND p.provinceRegion IN (:param2)";
            } elseif ($params['by'] === "province") {
                $joinKey = "cmp.id, p.id";
                $select = "p.id as PCODE, p.provinceName as Province, ";
                $groupBy = " ,p.id";
                $condition .= " AND p.id IN (:param2)";
            }

            // check if the district was not null
            if (isset($params['district'])) {
                $joinKey = "CASE 
                            WHEN cvr.subDistrict IS NULL 
                            THEN Concat(cmp.id, d.id) 
                            ELSE CONCAT(cmp.id, d.id, cvr.subDistrict)
                         END";
                $groupBy = " ,p.id, cvr.district, cvr.subDistrict";
                $select = "p.provinceName as Province, d.districtName as District, d.id as DCODE, cvr.subDistrict as Subdistrict, ";
            }
        }

        $em = $this->getEntityManager();
        $dq = $em->createQuery(
            "SELECT $joinKey as joinkey, 
                  p.provinceRegion as Region, ".$select."
                  cmp.campaignStartDate as CDate, cmp.id as CID,
                  cmp.campaignType as CType, cmp.campaignYear as CYear, cmp.campaignMonth as CMonth,
                  cmp.campaignName as CName,
                  ".$this->DQL."
                  FROM App:".$this->entity." cvr JOIN cvr.campaign cmp
                  JOIN cvr.district d JOIN d.province p 
                  WHERE(cvr.campaign in (:campaign) ".$condition.")
                  GROUP BY cvr.campaign".$groupBy);
        $dq->setParameter('campaign', $campaignIds);

        if(strpos($condition, "param1") !== false)
            $dq->setParameter("param1", $params['district']);
        if(strpos($condition, "param2") !== false)
            $dq->setParameter("param2", $params['value']);
        if(strpos($condition, "extraParam"))
            $dq->setParameter('extraParam', $params['extra']);

        return $dq->getResult(Query::HYDRATE_SCALAR);
    }

    /**
     * @param $campaigns
     * @param $params ['district', 'param' => ['subdist', 'cluster']]
     * @return mixed
     */
    public function clusterAggByCampaign($campaigns, $params) {
        // considering that district key should always be set in $params
        $condition = " AND cvr.district in (:district) ";
        /*
         The district key is required the others are optional
         $params = ['district' => [], 'param' => ['subdist', 'cluster']]
         */
        if(isset($params['param'])) {
            $param = $params['param'];
            if(isset($param['subdist']))
                $condition .= "AND (cvr.subDistrict IS NULL OR cvr.subDistrict = :subdist) ";
            if(isset($param['cluster']))
                $condition .= "AND cvr.clusterNo IN (:cluster) ";
        }

        $em = $this->getEntityManager();
        $dq = $em->createQuery(
            "SELECT CASE 
                            WHEN cvr.subDistrict IS NULL 
                            THEN Concat(cmp.id, d.id, cvr.clusterNo) 
                            ELSE CONCAT(cmp.id, d.id, cvr.subDistrict, cvr.clusterNo)
                         END as joinkey,
              p.provinceRegion as Region, p.provinceName as Province, d.districtName as District, 
              d.id as DCODE, cmp.campaignStartDate as CDate, cmp.id as CID,
              cmp.campaignType as CType, cmp.campaignYear as CYear, cmp.campaignMonth as CMonth, 
              cmp.campaignName as CName,
              cvr.subDistrict as Subdistrict, cvr.clusterNo as ClusterNo,
              CASE 
                WHEN cvr.subDistrict IS NULL 
                THEN cvr.clusterNo 
                ELSE CONCAT(cvr.subDistrict, '-', cvr.clusterNo)
              END as Cluster, 
              ".$this->DQL."
              FROM App:".$this->entity." cvr JOIN cvr.campaign cmp
              JOIN cvr.district d JOIN d.province p 
              WHERE(cvr.campaign in (:campaign) 
              ".$condition.")
              GROUP BY cvr.campaign, cvr.district, cvr.subDistrict, cvr.clusterNo
              ORDER BY cvr.subDistrict, cvr.clusterNo"
        );
        // setting the campaigns
        $dq -> setParameter('campaign', $campaigns);
        // setting the district
        $dq -> setParameter('district', $params['district']);
        // check if additional key was there
        if(isset($params['param'])) {
            $param = $params['param'];
            // check and set the subdistrict
            if(isset($param['subdist']))
                $dq -> setParameter('subdist', $param['subdist']);
            // check and set the cluster
            if(isset($param['cluster']))
                $dq -> setParameter('cluster', $param['cluster']);
        }

        return $dq->getResult(Query::HYDRATE_SCALAR);
    }

    /**
     * @param $campaigns
     * @param $params ['district', 'param' => ['subdist', 'cluster']]
     * @return mixed
     */
    public function clusterAggByLocation($campaigns, $params) {
        // considering that district key should always be set in $params
        $condition = " AND cvr.district in (:district) ";
        /*
         The district key is required the others are optional
         $params = ['district' => [], 'params' => ['subdist', 'cluster']]
         */
        if(isset($params['param'])) {
            $param = $params['param'];
            if(isset($param['subdist']))
                $condition .= "AND (cvr.subDistrict IS NULL OR cvr.subDistrict = :subdist) ";
            if(isset($param['cluster']))
                $condition .= "AND cvr.clusterNo IN (:cluster)) ";
        }

        $em = $this->getEntityManager();
        $dq = $em->createQuery(
            "SELECT CASE 
                            WHEN cvr.subDistrict IS NULL 
                            THEN Concat(d.id, cvr.clusterNo) 
                            ELSE CONCAT(d.id, cvr.subDistrict, cvr.clusterNo)
                         END as joinkey,
              p.provinceRegion as Region, p.provinceName as Province, d.districtName as District, 
              d.id as DCODE, cvr.subDistrict as Subdistrict, cvr.clusterNo as ClusterNo,
              CASE 
                WHEN cvr.subDistrict IS NULL 
                THEN cvr.clusterNo 
                ELSE CONCAT(cvr.subDistrict, '-', cvr.clusterNo)
              END as Cluster, 
              ".$this->DQL."
              FROM App:".$this->entity." cvr JOIN cvr.campaign cmp
              JOIN cvr.district d JOIN d.province p 
              WHERE(cvr.campaign in (:campaign) 
              ".$condition.")
              GROUP BY cvr.district, cvr.subDistrict, cvr.clusterNo
              ORDER BY cvr.subDistrict, cvr.clusterNo"
        );
        // setting the campaigns
        $dq -> setParameter('campaign', $campaigns);
        // setting the district
        $dq -> setParameter('district', $params['district']);
        // check if additional key was there
        if(isset($params['param'])) {
            $param = $params['param'];
            // check and set the subdistrict
            if(isset($param['subdist']))
                $dq -> setParameter('subdist', $param['subdist']);
            // check and set the cluster
            if(isset($param['cluster']))
                $dq -> setParameter('cluster', $param['cluster']);
        }

        return $dq->getResult(Query::HYDRATE_SCALAR);
    }

    private function createCondition($param) {
        $cond = "";
        $district = isset($param['district']) ? $param['district'] : [];
        if(count($district)>0) {
            $distType = $param['district'];
            if (in_array("None", $distType)) {
                $cond = "AND d.districtRiskStatus IS NULL";
            }
            //Todo: make below filter dynamic
            elseif($this->checkHrVhrFocus($distType)) {
                $cond = "AND d.districtRiskStatus IN (:param1) ";
            } elseif(in_array("All", $distType)) {
                $cond = " ";
            } else
                $cond = "AND d.id IN (:param1)";
        }
        // conditino if you want to filter the whole aggregation for few clusters.
        if(isset($param['extra']))
            $cond .= " AND CONCAT(d.id, COALESCE(cvr.subDistrict, ''), cvr.clusterNo) IN (:extraParam)";
        return $cond;
    }

    /**
     * @param array $paramsArray ['district', 'by', 'value']
     * @param array $campaigns
     * @return mixed
     * Function is to select unique clusters based on the condition
     */
    public function selectClustersByCondition(array $campaigns, array $paramsArray = null) {

        $paramsArray = $paramsArray === null ? [] : $paramsArray;
        //dump(array_key_exists('by', $paramsArray)); die;
        $condition = "";
        if(array_key_exists('by', $paramsArray) === true) {

            if($paramsArray['by'] === "region") {
                $condition = " AND p.provinceRegion IN (:param2) ";
            }
            else if($paramsArray['by'] === "province") {
                $condition = " AND p.id IN (:param2) ";
            }
            else if($paramsArray['by'] === "district") {
                $condition = " AND d.id IN (:param2) ";
            }

            $condition .= $this->checkHrVhrFocus($paramsArray['district']) ? " AND d.districtRiskStatus IN (:param3) " : "";
        }

        $em = $this->getEntityManager();
        $dql = $em ->createQuery(
            "SELECT DISTINCT CONCAT(d.id, COALESCE(s.subDistrict, ''), s.clusterNo) as cluster
                      FROM App:".$this->entity." s JOIN s.district d JOIN d.province p
                      WHERE (s.campaign in (:camp) ".$condition." )");

        if(array_key_exists('by', $paramsArray) && $condition != "") {
            $dql->setParameter('param2', $paramsArray['by']==='district'?$paramsArray['district']:$paramsArray['value']);
            if($this->checkHrVhrFocus($paramsArray['district']))
                $dql->setParameter('param3', $paramsArray['district']);
        }

        $dql->setParameter('camp', $campaigns);

        $result = $dql->getScalarResult();
        // we need a flat array one-dimensional
        return array_map('current', $result);
    }


    private function checkHrVhrFocus($paramsArray) {

        return (
               $paramsArray !== null &&
               (in_array("HR", $paramsArray) ||
                in_array("VHR", $paramsArray) ||
                in_array("Focus", $paramsArray))
               ) ? true : false;
    }


    /**
     * @param $campaigns
     * @return mixed
     */
    public function clusterAggByCampaigns($campaigns) {

        $em = $this->getEntityManager();
        $dq = $em->createQuery(
            "SELECT CASE 
                            WHEN cvr.subDistrict IS NULL 
                            THEN Concat(cmp.id, d.id, cvr.clusterNo) 
                            ELSE CONCAT(cmp.id, d.id, cvr.subDistrict, cvr.clusterNo)
                         END as joinkey,
              p.provinceRegion as Region, p.provinceName as Province, d.districtName as District, 
              d.id as DCODE, cmp.campaignStartDate as CDate, cmp.id as CID,
              cmp.campaignType as CType, cmp.campaignYear as CYear, cmp.campaignMonth as CMonth, 
              cmp.campaignName as CName,
              cvr.subDistrict as Subdistrict, cvr.clusterNo as ClusterNo,
              CASE 
                WHEN cvr.subDistrict IS NULL 
                THEN cvr.clusterNo 
                ELSE CONCAT(cvr.subDistrict, '-', cvr.clusterNo)
              END as Cluster, 
              ".$this->DQL."
              FROM App:".$this->entity." cvr JOIN cvr.campaign cmp
              JOIN cvr.district d JOIN d.province p 
              WHERE(cvr.campaign in (:campaign))
              GROUP BY cvr.campaign, cvr.district, cvr.subDistrict, cvr.clusterNo
              ORDER BY cvr.subDistrict, cvr.clusterNo"
        );
        // setting the campaigns
        $dq -> setParameter('campaign', $campaigns);
        // setting the district

        return $dq->getResult(Query::HYDRATE_SCALAR);
    }




}
