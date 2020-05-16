<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 1/24/2018
 * Time: 12:07 PM
 */

namespace App\Controller\Ajax;

use App\Service\HtmlTable;
use App\Service\Triangle;
use App\Service\Settings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxRefusalCommitteesController extends CommonDashboardController
{
    /**
     * @Route("/refusal_comm/test")
     */
    public function testTrend() {
        $titles = ['midTitle' => "mid title", 'subTitle' => "sub title", 'locTrendIds' => [40, 39], 'aggType'=>'Region'];
        $data = $this->trendAction("RefusalComm", [40, 39], null, $titles);
        //dump($data); die;
        return $data;
        //dump($data); die;
    }
    /**
     * @param Request $request
     * @param Settings $settings
     * @return mixed
     * @Route("/ajax/ref_committees", name="ajax_ref_committees", options={"expose"=true})
     */
    public function mainAction(Request $request, Settings $settings)
    {
        return parent::mainAction($request, $settings);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Route("/ajax/cluster/ref_committees/",
     *     name="ajax_cluster_ref_committees",
     *     options={"expose"=true})
     */
    public function clusterAction(Request $request, Settings $settings)
    {
        return parent::clusterAction($request, $settings);
    }

    protected function trendAction($entity, $campaigns, $params, $titles)
    {

        // location trends, default for three campaigns
        $locTrends = $this->campaignLocationData($entity, $titles['locTrendIds'], $params);

        //var_dump($locTrends); die;

        $trends =  $this->campaignsData($entity, $campaigns, $params);

        $trends = $this->loadAndMixData($trends, $campaigns, $params, "campaignsData", 'trend');

        $locTrends = $this->loadAndMixData($locTrends, $campaigns, $params, "campaignLocationData", null);

        $trends = $this->allMathOps($trends);
        $locTrends = $this->allMathOps($locTrends);

        //dump($locTrends); die;
        $category = [['column'=>'Region'],
            ['column'=>'CID', 'substitute'=>
                ['col1'=>'CMonth', 'col2'=>'CYear', 'short'=>'my']
            ]
        ];
        $subTitle = $titles['subTitle'];
        $during = $titles['midTitle'];

        // --------------------------- Loc Trend of Missed Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>$titles['aggType']],
            $category[1],
            [
                'cmpVacRefusal'=>'In Campaign',
                'chpVacRefusal'=>'In Catchup',
                'totalRefusalVacByRefComm'=>'By Committees',
                'totalRemainingRefusal'=>'Remaining'
            ],
            $locTrends
        );
        $locTrendAllType['title'] = 'Refusals Recovery';
        $locTrendAllType['subTitle'] = $subTitle;
        $data['loc_trend_general'] = $locTrendAllType;

        // --------------------------- Loc Trend of Absent Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>$titles['aggType']],
            $category[1],
            [
                'chpVacRefusal'=>'In Catchup',
                'refusalVacByCRC'=>'By CRC',
                'refusalVacByRC'=>'By RC',
                'refusalVacByCIP'=>'By CIP',
                'refusalVacBySenior'=>'By Senior',
                'totalRemainingRefusal'=>'Remaining'
            ],
            $locTrends
        );
        $locTrendAllType['title'] = 'Refusal Recovery After Revisit Day';
        $locTrendAllType['subTitle'] = $subTitle;
        $data['loc_trend_detail'] = $locTrendAllType;



        // --------------------------- Trend of Vaccinated Children --------------------------------
        $vacChildTrend = $this->chart
            ->chartData1Category($category[1],
                [
                    'cmpVacRefusal'=>'Campaign',
                    'chpVacRefusal'=>'Catchup',
                    'totalRefusalVacByRefComm'=>'Committees',
                    'totalRemainingRefusal'=>'Remaining'
                ],
                $trends);
        $vacChildTrend['title'] = 'Trend of Refusals Recovery '.$during;
        $vacChildTrend['subTitle'] = $subTitle;
        $data['general_refusal_recovery_trend'] = $vacChildTrend;



        // --------------------------- Trend of Missed Children By Type -----------------------------
        $missedByTypeTrend = $this->chart->chartData1Category($category[1],
            [

                'cmpVacRefusal'=>'Campaign',
                'chpVacRefusal'=>'Catchup',
                'refusalVacByCRC'=>'CRC',
                'refusalVacByRC'=>'RC',
                'refusalVacByCIP'=>'CIP',
                'refusalVacBySenior'=>'Senior',
                'totalRemainingRefusal'=>'Remaining'

            ], $trends);
        $missedByTypeTrend['title'] = 'Trends of Refusals Recovery By Category '.$during;
        $missedByTypeTrend['subTitle'] = $subTitle;
        $data['detail_refusal_recovery_trend'] = $missedByTypeTrend;


        return new JsonResponse($data);

        //return new JsonResponse(['data'=>null]);
    }

    protected function latestInfoAction($entity, $campaigns, $params, $titles)
    {

        $info =  $this->campaignsData($entity, $campaigns, $params);

        $campInfo = $this->loadAndMixData($info, $campaigns, $params, "campaignsData", 'oneCamp');

        $campAgg = $this->loadAndMixData($info, $campaigns, $params, "campaignsData", 'oneCampAgg');

        //dump($campInfo); dump($campAgg); die;
        $campInfo = $this->allMathOps($campInfo);
        $campAgg = $this->allMathOps($campAgg);
        //return $campAgg;
        $subTitle = $titles['subTitle'];
        $type = $titles['aggType'];

        // ---------------------------- Pie Chart one campaign missed by reason ----------------------------
        $missedByReasonPie = $this->chart->pieData([
            'cmpVacRefusal'=>'Campaign',
            'chpVacRefusal'=>'Catchup',
            'totalRefusalVacByRefComm'=>'Committees',
            'totalRemainingRefusal'=>'Remaining'
        ], $campInfo);
        $missedByReasonPie['title'] = "Refusals Recovery Breakdown";
        $data['refusal_recovery_pie_1'] = $missedByReasonPie;

        // ---------------------------- one campaign recovered all type by Catchup -------------------------
        $recoveredAllType = $this->chart->pieData([
            'totalVacRefusal'=>'Recovered',
            'totalRemainingRefusal'=>'Remaining'
        ], $campInfo);
        $recoveredAllType['title'] = "Recovered vs Remaining Refusals";
        $recoveredAllType['subTitle'] = $subTitle;
        $data['refusal_recovered_1'] = $recoveredAllType;

        // ---------------------------- last campaign Absent recovered by Catchup  -------------------------
        $recoveredAbsent = $this->chart->pieData([
            'cmpVacRefusal'=>'Campaign',
            'chpVacRefusal'=>'Catchup',
            'totalRefusalVacByRefComm'=>'Committees',
            'totalRemainingRefusal'=>'Remaining'
        ],
            $campInfo);
        $recoveredAbsent['title'] = "Refusals Recovery in Campaign, Catchup & after Catchup";
        $recoveredAbsent['subTitle'] = $subTitle;
        $data['refusal_recovered_general_1'] = $recoveredAbsent;

        // ---------------------------- last campaign NSS recovered by Catchup -----------------------------
        $recoveredNss = $this->chart->pieData([
            'cmpVacRefusal'=>'Campaign',
            'chpVacRefusal'=>'Catchup',
            'refusalVacByCRC'=>'CRC',
            'refusalVacByRC'=>'RC',
            'refusalVacByCIP'=>'CIP',
            'refusalVacBySenior'=>'Senior',
            'totalRemainingRefusal'=>'Remaining'
        ],
            $campInfo);
        $recoveredNss['title'] = "Refusals Recovery in Campaign, Catchup & By Refusal Committees";
        $recoveredNss['subTitle'] = $subTitle;
        $data['refusal_recovered_detail_1'] = $recoveredNss;


        // ---------------------------- last campaign total missed by region -------------------------------
        $totalRemaining = $this->chart->chartData1Category(['column'=>$titles['aggType']],
            ['totalRemainingRefusal'=>'Remaining'], $campAgg);
        $totalRemaining['title'] = 'Final Remaining Refusals';
        $totalRemaining['subTitle'] = $subTitle;
        $data['total_remaining_refusal_1'] = $totalRemaining;

        /*
        // ---------------------------- last campaign total missed by location -------------------------------
        $oneCat = $titles['aggType'] === 'Region' ? true : false;
        if($oneCat) {
            $totalRemaining = $this->chart->chartData1Category(['column' => $titles['aggType']],
                [
                    'TotalRecovered' => 'Recovered',
                    'TotalRemaining' => 'Remaining',
                ], $campAgg);
            $totalRemaining['title'] = 'ICN Reduced Missed Children';
            $totalRemaining['subTitle'] = $subTitle;
            $data['total_recovered_remaining_1'] = $totalRemaining;
        } else {
            $cat1 = ['column' => 'Region'];
            if($titles['aggType'] === "District")
                $cat1 = ['column' => 'Province'];
            $totalRemaining = $this->chart->chartData2Categories(
                $cat1,
                ['column' => $titles['aggType']],
                ['TotalRemaining' => 'Remaining',
                    'TotalRecovered' => 'Recovered'], $campAgg);
            $totalRemaining['title'] = 'ICN Reduced Missed Children';
            $totalRemaining['subTitle'] = $subTitle;
            $data['total_recovered_remaining_1'] = $totalRemaining;
        }
        */

        // ---------------------------- Tabular information of the campaign -------------------------------

        $table = HtmlTable::tableForRefusalComm($campAgg, $type);
        $data['info_table'] = $table;

        // just for the map data
        //$data['map_data'] = json_encode($campAgg);


        // ---------------------------- Header Tiles Information of the campaign --------------------------
        $info_header = HtmlTable::infoForRefusalComm($campInfo);
        $data['info_box'] = $info_header;

        // ---------------------------- Title of the one campaign information -----------------------------
        $campaign = "No data for this campaign as per current filter";
        if(count($campInfo) > 0)
            $campaign = $campInfo[0]['CName'];
        $data['campaign_title'] = $campaign;

        return new JsonResponse($data);
    }

    protected function clustersInfoAction($entity, $campaigns, $params, $controlParams = null)
    {
        $oneCampData = $this->clustersData($entity, $campaigns, $params);

        $oneCampData = $this->loadAndMixData($oneCampData, $campaigns, $params, "clustersData");

        $oneCampData = $this->allMathOps($oneCampData);

        //dump($oneCampData); die;
        $xAxises = [
            ['col'=>'Cluster', 'label'=>'Cluster', 'calc'=>'none'],
            ['col'=>'cmpRemRefusal', 'label'=>'Ref After Camp', 'calc'=>'none'],
            ['col'=>'refusalAfterDay5', 'label'=>'Ref After Camp (ROC)', 'calc'=>'none'],
            ['col'=>'chpVacRefusal', 'label'=>'Vac in Catchp', 'calc'=>'none'],
            ['col'=>'refusalVacInCatchup', 'label'=>'Vac in Catchp (ROC)', 'calc'=>'none'],
            ['col'=>'refusalVacByCRC', 'label'=>'Vac CRC', 'calc'=>'none'],
            ['col'=>'refusalVacByRC', 'label'=>'Vac RC', 'calc'=>'none'],
            ['col'=>'refusalVacByCIP', 'label'=>'Vac CIP', 'calc'=>'none'],
            ['col'=>'refusalVacBySenior', 'label'=>'Vac Senior', 'calc'=>'none'],
            ['col'=>'totalRefusalVacByRefComm', 'label'=>'Total Vac', 'calc'=>'none'],
            ['col'=>'totalRemainingRefusal', 'label'=>'Remaining', 'calc'=>'none'],
        ];

        $campaign = "No data for this campaign as per current filter";
        if(count($oneCampData) > 0)
            $campaign = $oneCampData[0]['CName']." Recovered and Remaining Refusal Children";

        $table = HtmlTable::tableODK($oneCampData, $xAxises, null, null, $campaign);

//        $oneCampBarChart = $this->chart->chartData1Category(['column'=>'Cluster'],
//            [
//                'cmpVacRefusal'=>'Campaign',
//                'chpVacRefusal'=>'Catchup',
//                'totalRefusalVacByRefComm'=>'Committees',
//                'totalRemainingRefusal'=>'Remaining'
//            ],
//            $oneCampData, true);
        $data['refusal_recovery_table_1'] = $table;
        return new JsonResponse($data);
    }

    protected function clustersTrendAction($entity, $campaigns, $params, $controlParams)
    {
        // fetch the data
        $heatMapData = $this->clustersData($entity, $campaigns, $params);

        //$locTrends = $this->clustersData($entity, $controlParams['locTrendIds'], $params);

        $heatMapData = $this->loadAndMixData($heatMapData, $campaigns, $params, "clustersData");
        //$locTrends = $this->loadAndMixData($locTrends, $controlParams['locTrendIds'], $params, "clustersData");

        $heatMapData = $this->allMathOps($heatMapData);
        //$locTrends = $this->allMathOps($locTrends);

        // get the clusters from the params as they needed for the table
        $clusters = $params['cluster'];

        // get the selectType from controlParams
        $selectType = $controlParams["selectType"] === 'TotalRemaining' ?
                      'totalRefusalVacByRefComm' : $controlParams['selectType'];

        // set the calcTypeArray
        $calcTypeArray = ['type'=>'number'];
        if($controlParams['calcType'] === 'percent') {
            $col = '';
            if($selectType == 'totalRefusalVacByRefCommPer')
                $col = 'cmpRegRefusal';
            elseif($selectType == 'chpVacRefusalPer')
                $col = 'cmpRegRefusal';
            elseif($selectType == 'totalVacRefusalPer') // here the denominator will be registered refusals in campaign
                $col = 'cmpRegRefusal'; // because totalVacRefusal includes vaccinated refusals in campaign as well
            elseif($selectType == 'totalRemainingRefusalPer')
                $col = 'cmpRegRefusal';

            $calcTypeArray = ['type'=>'percent', 'column'=>$col];
        }

        $rawData = $this->chart->clusterDataForHeatMap($heatMapData,
            str_replace("Per", "", $selectType),
            ['column' => 'CID', 'substitute' => 'shortName'],
            $clusters, $calcTypeArray, 'table');
        // get the entity manager for the benchmarks
        $em = $this->getDoctrine()->getManager();
        $stops= $em->getRepository("App:HeatmapBenchmark")
            ->findOne($entity, $selectType);

        // create columns for the table
        $cols = array(['col' => 'rowName', 'label' => 'Cluster', 'calc' => 'none']);
        foreach ($rawData['xAxis'] as $axi) {
            $cols[] = ['col' => $axi, 'label' => $axi, 'calc' => 'normal'];
        }

        $table = HtmlTable::heatMapTable($rawData['data'],
            $cols, HtmlTable::heatMapTableHeader($selectType),
            $stops['minValue'],
            $stops['maxValue'], 'normal', false);

        $data['cluster_trend'] = $table;

        /*
        //======================================== New Cluster Level Trends Charts ================
        $category = [['column'=>'Region'],
            ['column'=>'CID', 'substitute'=>
                ['col1'=>'CMonth', 'col2'=>'CYear', 'short'=>'my']
            ]
        ];
        // --------------------------- Loc Trend of Missed Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['TotalRecovered'=>'Recovered', 'TotalRemaining'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'Missed Children Recovery';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_all_type'] = $locTrendAllType;

        // --------------------------- Loc Trend of Absent Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['VacAbsent'=>'Recovered', 'RemAbsent'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'Absent Children Recovery';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_absent'] = $locTrendAllType;

        // --------------------------- Loc Trend of NSS Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['VacNSS'=>'Recovered', 'RemNSS'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'NSS Children Recovery';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_nss'] = $locTrendAllType;

        // --------------------------- Loc Trend of Refusal Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['VacRefusal'=>'Recovered', 'RemRefusal'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'Refusal Children Recovery';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_refusal'] = $locTrendAllType;

        */

        //return new Response($table);
        return new JsonResponse($data);
    }

    private function loadAndMixData($data, $campaigns, $params, $method, $index = null) {

        // clusters needed for extracting catchup and coverage data
        $clusters = $this->chart->chartData("RefusalComm", 'selectClustersByCondition', $campaigns, $params);
        //Todo: For multiple campaigns, the result is wrong
        $params['extra'] = $clusters;
        //$catchup = $this->combineData("both", $campaigns, $params['extra'] = $clusters);
        $catchup = $this->$method("CatchupData", $campaigns, $params);
        $coverage = $this->$method("CoverageData", $campaigns, $params);

        // Below indices required from other data sources
        $coverageIndices = ['CalcTarget', 'RegRefusal', 'RemRefusal', 'VacRefusal', 'VacRefusal3Days', 'VacRefusalDay4'];
        $catchupIndices = ['RegRefusal', 'VacRefusal'];

        $triangulatedData = Triangle::triangulateCustom([$index===null?$data:$data[$index],
            ['data'=>$index===null?$coverage:$coverage[$index], 'indexes'=>$coverageIndices, 'prefix'=>'cmp' ]],
            'joinkey');
        $triangulatedData = Triangle::triangulateCustom([$triangulatedData,
            ['data'=>$index===null?$catchup:$catchup[$index], 'indexes'=>$catchupIndices, 'prefix'=>'chp']], 'joinkey');

        return $triangulatedData;
    }

    private function allMathOps($data) {

        $data = Triangle::mathOps($data, ['cmpVacRefusal', 'chpVacRefusal'],
            '+', 'refusalVacInCampCatchup');
        $data = Triangle::mathOps($data, ['refusalVacInCampCatchup', 'totalRefusalVacByRefComm'],
            '+', 'totalVacRefusal');
        $data = Triangle::mathOps($data, ['cmpRegRefusal', 'totalVacRefusal'], '-',
            'totalRemainingRefusal');

        return $data;

    }
}
