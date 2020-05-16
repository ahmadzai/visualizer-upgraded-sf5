<?php
namespace App\Service;
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 11/12/2016
 * Time: 6:44 PM
 */
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class Settings
{

    // constants for number of campaigns
    const NUM_CAMP_CLUSTERS = 6;
    const NUM_CAMP_CHARTS = 10;
    const NUM_CAMP_LIMIT = 10;

    // ONA API_KEY and URL
    const ONA_API = "";
    const ONA_BASE_URL = "https://api.ona.io/api/v1";

    protected $em;

    public static function tableSetting() {
        return [
            'pageLength'=> 15,
            'lengthMenu' => [
                [15, 25, 50, -1],
                [15, 25, 50, 'All']
            ]
        ];
    }

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }

    /***
     * @param $months array of months
     * @return array
     */
    function orderMonths($months) {
        $order_months = array();
        if(is_array($months) && count($months) > 0) {
            $temp = array();
            foreach($months as $month) {
                $m = date_parse($month);
                $temp[$m['month']] = $month;
            }
            ksort($temp);
            $order_months = $temp;
        }

        return $order_months;
    }

    /**
     * @param $table
     * @return array
     */
    public function campaignMenu($table)
    {

        $data = $this->em->createQuery(
            "SELECT ca.id, ca.campaignMonth, ca.campaignType, ca.campaignYear,
             ca.campaignName, ca.campaignStartDate, ca.campaignEndDate
             FROM App:$table a
             JOIN a.campaign ca GROUP BY ca.id ORDER BY ca.id DESC"
        )
            ->getResult(Query::HYDRATE_SCALAR);

        return $data;

    }

    /**
     * @param $table
     * @param $campaignId
     * @return single campaign
     */
    public function latestCampaign($table, $campaignId = 0) {

        if($campaignId === 0 || $campaignId == 0) {
            $data = $this->em->createQuery(
                "SELECT ca.id, ca.campaignMonth, ca.campaignType, ca.campaignYear, 
                ca.campaignName, ca.campaignStartDate, ca.campaignEndDate
               FROM App:$table a
               JOIN a.campaign ca ORDER BY ca.id DESC"
              ) ->setFirstResult(1)
                ->setMaxResults(1)
                ->getResult(Query::HYDRATE_SCALAR);
            //$campaignId = $data[0]['CampID'];
            return $data;
        }
        else {
          $data = $this->em->createQuery(
              "SELECT ca.id, ca.campaignMonth, ca.campaignType, ca.campaignYear, 
               ca.campaignName, ca.campaignStartDate, ca.campaignEndDate
               FROM App:$table a
               JOIN a.campaign ca WHERE ca.id =:camp"
          )->setParameter('camp', $campaignId)->getResult(Query::HYDRATE_SCALAR);
          return $data;
       }
    }

    /**
     * @param $table
     * @param int $no default 3 campaigns
     * @return array
     */
    public function lastFewCampaigns($table, $no = 3) {
        $campaigns = $this->campaignMenu($table);
        $cam = [];
        $i = 0;
        foreach ($campaigns as $campaign) {
            if($i == $no)
                break;
            $cam[] = $campaign['id'];
            $i++;
        }

        return $cam;
    }

      /**
       * @param $table
       * @return array
       */
      public function noEntryCampaigns($table) {
          $campaigns = $this->campaignMenu($table);
          $cam = [];
          foreach ($campaigns as $campaign) {
            $check = $this->campaignEntryCheck($campaign['id']);
              if(isset($check))
                $cam[] = $campaign['id'];

                $check = [];
          }

          return $cam;
      }

      /**
       * @param $campaignId
       * @return array
       */
      public function campaignEntryCheck($campaignId)
      {

          $data = $this->em->createQuery(
              "SELECT adm FROM App:AdminData adm WHERE adm.campaign =:camp"
            )->setParameter('camp', $campaignId)
             ->setFirstResult(1)
             ->setMaxResults(1)
             ->getResult(Query::HYDRATE_SCALAR);

          return $data;

      }

    /**
     * @param $table
     * @param string $column
     * @return mixed
     */
      public function getLastDate($table, $column = 'monitoringDate') {
          $data = $this->em->createQuery(
              "SELECT max(tbl.$column) as lastDate FROM App:$table tbl"
                )
              ->getResult(Query::HYDRATE_SCALAR);

          return $data[0]['lastDate'];
      }

    /**
     * @param string $table
     * @param string $column
     * @return array
     */
    public function getMonths($table, $column = 'monitoringDate') {
        $data = $this->em->createQuery(
            "SELECT YEAR(tbl.$column) AS monthYear, MONTH(tbl.$column) as monthNo, 
                  MONTHNAME(tbl.$column) as monthName FROM App:$table tbl
                  GROUP BY monthYear, monthNo, monthName ORDER BY monthYear DESC, monthNo DESC "
        )
            ->getResult(Query::HYDRATE_SCALAR);
        return $data;
    }

    /**
     * @param $source
     * @return array
     */
    public function selectProvinceBySource($source) {
        $data = $this->em->createQuery(
            "SELECT Distinct p.id, p.provinceName FROM App:$source source 
                  JOIN source.district d JOIN d.province p"
        )
            ->getResult(Query::HYDRATE_SCALAR);

        return $data;
    }

    /**
     * @param $source
     * @param $province
     * @return array
     */
    public function selectDistrictBySource($source, $province) {
        $data = $this->em->createQuery(
            "SELECT Distinct d.id, d.districtName, p.provinceName, p.id as pid FROM App:$source source 
                  JOIN source.district d JOIN d.province p WHERE p.id IN (:prov)"
        )   ->setParameter("prov", $province)
            ->getResult(Query::HYDRATE_SCALAR);

        return $data;
    }


}
