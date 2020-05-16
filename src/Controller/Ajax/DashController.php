<?php
/**
 * Created by PhpStorm.
 * User: Wazir Khan Ahmadzai
 * Date: 8/11/2018
 * Time: 3:00 PM
 */

namespace App\Controller\Ajax;



use App\Service\Charts;
use App\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Service\Triangle;
use Symfony\Component\HttpKernel\KernelInterface;

class DashController extends AbstractController
{
    protected $session;
    protected $chart;
    protected $setting;
    /**
     * @var string
     */
    private $rootDir;

    public function __construct(Charts $chart, Settings $setting, KernelInterface $rootDir)
    {
        $this->session = new Session();
        $this->chart = $chart;
        $this->setting = $setting;

        $this->rootDir = $rootDir->getProjectDir();
    }

    /**
     * @param $entity
     * @param $campaigns
     * @param null $params
     * @return array
     * This method is to fetch all the data for dashboard at once
     */
    protected function fetchCampaignData($entity, $campaigns, $params = null)
    {

        // if only filter by campaigns was selected
        $campData = $params['by'] === 'campaign' || $params === null ?
            $this->session->get($entity):
            $this->chart->chartData($entity, "campaignStatistics", $campaigns, $params);
        if($params === null) {
            // check if session wasn't set before
            $campData = $campData === null ?
                $this->chart->chartData($entity, "campaignStatistics", $campaigns, $params):
                $campData;
            $campData = $this->setState($campData, $entity);
        } else {
            // this will indexify the data without setting in the session
            $campData = $this->setState($campData, $entity, False);
        }
        // extract the indices out the new data
        $campIds = array_keys($campData);
        //dump($campIds);die;

        $tempData = null;    // store filtered data
        // if the new requested campaigns ids were in the indices
        if($this->searchArray($campIds, $campaigns)) {
            //extract those indices' data
            $tempData = $this->extractArray($campData, $campaigns);

        } else {
            // this case will run only if the params was null and if the new data was requested
            $tempData = $this->chart->chartData($entity, "campaignStatistics",
                $campaigns, $params);
        }

        $newData = [];                                // store return data
        // set the return data based on the number of campaigns data requested
        if(count($campaigns) > 1) {
            $lastCampId = $this->lastCamp($entity);
            $newData['trend'] = $tempData;
            $newData['oneCampAgg'] = $this->chart->chartData($entity, "aggByCampaign",
                [$lastCampId], $params);
            $newData['oneCamp'] = $this->extractArray($campData, [$lastCampId]);;
            //$newData['oneCamp'] = $this->chart->chartData($entity, "campaignStatistics", [$lastCampId], $params);
        } else {
            $newData['trend'] = $campData;
            $newData['oneCampAgg'] = $this->chart->chartData($entity, "aggByCampaign",
                $campaigns, $params);
            $newData['oneCamp'] = $tempData;
        }

        return $newData;

    }

    /**
     * @param $entity
     * @param $campaigns
     * @param null $params
     * @return array
     * This function is to fetch data as needed (either for trends or one campaign)
     */
    protected function campaignsData($entity, $campaigns, $params = null)
    {
        //dump($params); die;
        // if only filter by campaigns was selected
        $campData = $params === null ?
            $this->session->get($entity):
            $this->chart->chartData($entity, "campaignStatistics", $campaigns, $params);
        if($params === null) {
            // check if session wasn't set before
            $campData = $campData === null || count($campData) === 0 ?
                $this->chart->chartData($entity, "campaignStatistics", $campaigns, $params):
                $campData;
            $campData = $this->setState($campData, $entity);
        } else {
            // this will indexify the data without setting in the session
            $campData = $this->setState($campData, $entity, False);
        }
        // extract the indices out the new data
        $campIds = array_keys($campData);
        //dump($campIds);die;

        $tempData = null;                               // store filtered data
        // if the new requested campaigns ids were in the indices
        if($this->searchArray($campIds, $campaigns)) {
            //extract those indices' data
            $tempData = $this->extractArray($campData, $campaigns);

        } else {
            // this case will run only if the params was null and if the new data requested
            $tempData = $this->chart->chartData($entity, "campaignStatistics",
                $campaigns, $params);
        }

        $newData = [];                                // store return data
        // set the return data based on the number of campaigns data requested
        if(count($campaigns) > 1) {
            //$lastCampId = $this->lastCamp($entity);
            $newData['trend'] = $tempData;

        } else {
            //$newData['trend'] = $campData;
            $newData['oneCampAgg'] = $this->chart->chartData($entity, "aggByCampaign",
                $campaigns, $params);

            $newData['oneCamp'] = $this->chart->chartData($entity, "campaignStatistics",
                $campaigns, $params);;
        }

        return $newData;

    }

    /**
     * @param $entity
     * @param $campaigns
     * @param null $params
     * @return array
     */
    public function campaignLocationData($entity, $campaigns, $params = null) {
        //Todo: keep data in the session
        return $this->chart->chartData($entity, "aggByCampaign", $campaigns, $params);

    }

    /**
     * @param $entity
     * @param $campaigns
     * @param null $params
     * @return array
     */
    protected function combineData($entity, $campaigns, $params = null) {
        $sourceEntity = "";
        if($entity === "both") {
            $sourceEntity = "CoverageData";
            $entity = "CatchupData";
        }

        $sourceData = $this->campaignsData($sourceEntity, $campaigns, $params);
        $secondData = $this->campaignsData($entity, $campaigns, $params);
        if(count($campaigns) > 1) {
            return ['trend'=>$this->triangulate($sourceData, $secondData, 'trend')];
        } else {

            return [
                'oneCampAgg' => $this->triangulate($sourceData, $secondData, 'oneCampAgg'),
                'oneCamp' => $this->triangulate($sourceData, $secondData, 'oneCamp')
            ];

        }
    }

    /**
     * @param $entity
     * @param $campaigns
     * @param $params
     * @return array
     */
    protected function combineClustersData($entity, $campaigns, $params) {
        $sourceEntity = "";
        if($entity === "both") {
            $sourceEntity = "CoverageData";
            $entity = "CatchupData";
        }
        $sourceData = $this->clustersData($sourceEntity, $campaigns, $params);
        $secondData = $this->clustersData($entity, $campaigns, $params);

        return $this->triangulate($sourceData, $secondData);
    }

    /**
     * @param $entity
     * @param $campaigns
     * @param $params
     * @return array|mixed
     */
    protected function clustersData($entity, $campaigns, $params) {


        /*
        $params['cluster'=>[], 'district'=>[]]
        */

        $subDistClusters = $this->subDistClusters($params['cluster']);
        $campData = array();
        if (count($subDistClusters['subDistricts']) > 0) {
            foreach ($subDistClusters['subDistricts'] as $item) {
                // find the clusters data
                $campData[] = $this->chart->chartData(
                    $entity, 'clusterAggByCampaign',
                    $campaigns, ['district'=>
                        $params['district'],
                        'param' => [
                            'subdist'=>$item,
                            'cluster'=> $subDistClusters['clusters']
                        ]
                ]);
            }
            // merge the data of all sub districts
            $campData = array_merge(...$campData);
        } else if (count($subDistClusters['subDistricts']) <= 0 ||
            $subDistClusters['subDistricts'] === null) {
            $campData = $campData[] = $this->chart->chartData(
                $entity, 'clusterAggByCampaign',
                $campaigns, ['district'=>$params['district'],
                    'param' => ['cluster'=> $subDistClusters['clusters']
                    ]
                ]);
        }

        return $campData;
    }

    /**
     * @param $data
     * @param $index
     * @param $setSes
     * @return mixed last campaign data
     */
    private function setState($data, $index, $setSes = true) {

        $sesData = [];
        foreach ($data as $datum) {
            $sesData[$datum['CID']] = $datum;
        }

        if($setSes)
            $this->session->set($index, $sesData);
        return $sesData;
    }


    /**
     * @param $entity
     * @return mixed
     */
    protected function lastCamp($entity) {
        $maxIndex = $this->setting->latestCampaign($entity);
        return $maxIndex[0]['id'];
    }

    /**
     * @param $keys
     * @param $search
     * @return bool
     */
    private function searchArray($keys, $search) {

        if(!is_array($search) || count($search) === 0) return false;

        $flag = true;
        foreach ($search as $srch) {
            if(!in_array($srch, $keys)) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }

    /**
     * @param $data
     * @param $keys
     * @return array
     */
    private function extractArray($data, $keys) {
        $newData = [];
        foreach ($keys as $key) {
            $newData[] = $data[$key];
        }
        return $newData;
    }

    /**
     * @param $data
     * @param $data1
     * @param $index
     * @return array
     */
    private function triangulate($data, $data1, $index = null) {
        // required indexes in both sources
        $indexes = ['RegMissed', 'TotalRecovered', 'TotalVac',
            'RegAbsent', 'VacAbsent',
            'RegNSS', 'VacNSS', 'RegRefusal', 'VacRefusal'];
        return Triangle::triangulateCustom(
            [
                $index===null?$data:$data[$index],
                ['data'=>$index===null?$data1:$data1[$index],
                    'indexes'=>$indexes,
                    'prefix'=>'c'
                ]
            ], 'joinkey');
    }

    /**
     * @param $clusters
     * @return array
     */
    protected function subDistClusters($clusters) {
        $clusterArray = array();
        $subDistrictArray = array();
        if(count($clusters) > 0 ) {
            foreach($clusters as $cluster) {
                if(stristr($cluster, "|") !== false) {
                    $subDistrictCluster = explode("|", $cluster);
                    $subDistrictArray[] = $subDistrictCluster[0];
                    $clusterArray[] = $subDistrictCluster[1];
                } else {
                    $clusterArray[] = $cluster;
                }
            }
        }

        if(count($subDistrictArray) > 0)
            $subDistrictArray = array_unique($subDistrictArray);

        return [
            'clusters' => $clusterArray,
            'subDistricts' => $subDistrictArray
        ];
    }

    protected function getRootDir() {
        return $this->rootDir;
    }

}
