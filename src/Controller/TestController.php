<?php

namespace App\Controller;

use App\Service\Settings;
use GuzzleHttp\Client;
use PhpCollection\Set;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Charts;
use App\Service\Triangle;
use App\Service\Exporter;

/**
 * Class TestController
 * @package App\Controller
 */
class TestController extends AbstractController
{

    /**
     * @Route("/mytest")
     */
    function testDir() {

        $dir = getcwd(); // Getting the current working dir
        $di = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($di);
        // Get all files and folders within the working dir

        $files = [];
        foreach ($iterator as $filename => $file) {
            $path = pathinfo($filename);
            if ($path['basename'] == 'province.json') {
                echo $path['dirname'] . '<br />';
                //echo $path['dirname'], "\n";
                //echo $path['basename'], "\n";
                //echo $path['extension'], "\n";
                //echo $path['filename'], "\n"; // since PHP 5.2.0
            }
            $files[] = $filename;
        }

        dump($files);
        die;
        //asort($files);
        //echo "<pre>"; print_r($files);die;

    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @Route("/test_api")
     */
    public function indexAction(Settings $settings) {

//        $campaign = null;
//        if($campaign === null) {
//            $campaign = $campaign ?? $settings->latestCampaign("CatchupData");
//            $campaign = $campaign[0]['id'];
//        }
//        $data = $this->getDoctrine()->getRepository("App:CatchupData")
//            ->aggByCampaign([$campaign], ['by'=>'district', 'district'=>['all']]);

//        dump($data); die;

        $client = new Client();
        $res = $client->post("http://afg-poliodb.info/api/token", [
            'auth' => ['apms_api_user', '@p!Connec7']
        ]);

        $token = json_decode($res->getBody()->getContents());

        // Test below for admin data api
//        $data = $client->get("http://afg-poliodb.info/api/admindata/by_district/30", [
//           'headers' => ['Authorization' => 'Bearer '.$token->token]
//        ]);

        // Test below for campaign api
        $data = $client->get("http://afg-poliodb.info/api/roc_data/by_district", [
            'headers' => ['Authorization' => 'Bearer '.$token->token]
        ]);

        // Test below for catchup data api
//        $data = $client->get("http://localhost/visualizer/web/app_dev.php/api/catchup/by_district", [
//            'headers' => ['Authorization' => 'Bearer '.$token->token]
//        ]);

        echo $data->getBody(); die;
    }


    /**
     * @param Request $request
     * @param Charts $charts
     * @param Settings $settings
     * @param Triangle $triangle
     * @return mixed
     * @Route("/export", name="data-export")
     */
    public function exportData(Request $request, Charts $charts, Settings $settings, Triangle $triangle) {
        $campaigns = [37, 38, 39];
        $entity = "CatchupData";
        $method = "aggBySubDistrict";
        $by = ['by' => 'district', 'district'=>[101]];

        $data = $charts->chartData($entity, $method, $campaigns, $by);

        return Exporter::exportCSV($data);
        //return new JsonResponse($data);
    }
    /**
     * @Route("/test", name="testing")
     * @param Request $request
     * @param Charts $charts
     * @param Settings $settings
     * @param Triangle $triangle
     * @return Response
     */
    public function testAction(Request $request, Charts $charts, Settings $settings, Triangle $triangle) {


        //Test for the General campaignStatistics Function
        $province = ['by' => 'province', 'value' => [33, 6], 'district'=>['Focus']];

        $condition = ['3301Kandahar City1', '3301Kandahar City2', '3301Dand2', '6011', '6012'];
        $district2 = ['by' => 'district', 'district'=>[3301, 601], 'extra'=>$condition];


        $district = ['by' => 'district', 'district' =>
                        [1701,1702,1703,1704,1705,1706,1707]
                    ];
        $region = ['by' => 'region', 'value' => ['ER'], 'district'=>null];
//        $regionState = $charts->chartData('RefusalComm', 'aggBySubDistrict',
//            [33, 34], $district2);

        $data = $charts->chartData('RefusalComm', 'selectClustersByCondition', [33, 34], $province);
        dump($data);


//        $missedByReasonPie = $charts->pieData(['RemAbsent'=>'Absent',
//            'RemNSS'=>'NSS',
//            'RemRefusal'=>'Refusal'], $regionState);
//        $missedByReasonPie['title'] = "Remaining Children By Reason";
//        $data['missed_by_reason_pie_1'] = $missedByReasonPie;
//
//        dump($data);
        die;
        /*


        // Test for the General aggByCampaign Function
        $province = ['by' => 'province', 'value' => [33]];
        $district = ['by' => 'district', 'district' =>
                        [3301, 3302]
                    ];
        $region = ['by' => 'region', 'value' => ['SR']];
        $regionState = $charts->chartData('CatchupData', 'aggByCampaign',
            [23, 22], $region);
        dump($regionState);
        $aggData = $charts->chartData('CatchupData', 'aggByLocation',
            [23, 22], $region);
        dump($aggData);
        die;
        */


        /*
        $clusterAgg = $charts->chartData("CatchupData", 'clusterAggByLocation',
                                         [23, 22], ['district'=>[3302, 3303]]);
        dump($clusterAgg); die;
        */

//        $regionData = $charts->chartData('CatchupData', 'regionAggByCampaignRegion',
//            [22], ['SR']);
//
//        return new Response(json_encode(['statistics'=>$regionState,
//            'region'=>$regionData]));

//
//        $xAxises = ['attendance', 'profile', 'tallying'];
//        $yAxis = ['col'=>'pcode', 'label'=>'provinceName'];
//
//        $source = $settings->getMonths("OdkSmMonitoring");
//
//        $tenCampCatchupData = $charts->chartData("CatchupData",
//            'campaignsStatisticsByRegion', [24, 23, 22], ['SER']);
//


//        $tenCampCatchupData = $charts->chartData("CoverageData",
//            'clusterAggByCampaign', [16, 17, 18, 19, 20, 21], ['district'=>[601, 3301]]);

//        $lastCampStackChart = $charts->chartData1Category(['column'=>'CID', 'substitute'=>['col1'=>'CMonth', 'col2'=>'CYear', 'short'=>'my']],
//            ['RemAbsent' => 'Absent',
//                'RemNSS' => 'NSS',
//                'RemRefusal' => 'Refusal'], $tenCampCatchupData);
//        $lastCampStackChart['title'] = 'Missed Children By Campaign';
//        $lastCampStackChart['subTitle'] = null;

//
//        //$source = $charts->heatMap($source, $xAxises, $yAxis, 'percent');
//
//
//        return new Response(json_encode($tenCampCatchupData));


//        return Exporter::exportCSV($tenCampCatchupData);


//        $map = Maps::loadGeoJson($this->getParameter('kernel.root_dir'));
//        return new JsonResponse($map);

    }

    /**
     * @Route("/test/repo")
     */
    public function testRepoMethod() {
//        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
//            ->findProvinces(["2020-Jul"]);
//        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
//            ->findYearMonth();
//        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
//            ->findDistricts(["2020-Feb"], [32]);
//        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
//            ->findHealthFacilities(["2020-Jul"], [32]);
//        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
//            ->findMappedIndicators();


//        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')->checkArrayDimension($result);
        $result = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
            ->getReachByMonths();

        $rows = [];
        foreach ($result as $item) {
            $rows[] = array_slice($item, 0, 2);
        }
        $rows = array_unique($rows, SORT_REGULAR);

        dd($result);
        foreach ($rows as $index => $row) {
            foreach ($result as $value) {

            }
        }
    }
}
