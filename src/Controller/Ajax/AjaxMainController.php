<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 1/24/2018
 * Time: 12:07 PM
 */

namespace App\Controller\Ajax;


use App\Service\Exporter;
use App\Service\HtmlTable;
use App\Service\Triangle;
use App\Service\Settings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxMainController extends CommonDashboardController
{
    /**
     * @param Request $request
     * @param Settings $settings
     * @return mixed
     * @Route("/ajax/main_dashboard", name="ajax_main_dashboard", options={"expose"=true})
     */
    public function mainAction(Request $request, Settings $settings)
    {
        return parent::mainAction($request, $settings);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Route("/ajax/cluster/main_dashboard/",
     *     name="ajax_cluster_main_dashboard",
     *     options={"expose"=true})
     */
    public function clusterAction(Request $request, Settings $settings)
    {
        return parent::clusterAction($request, $settings);
    }

    /**
     * @param $entity
     * @param $campaigns
     * @param $params
     * @param $titles
     * @return mixed
     */
    protected function trendAction($entity, $campaigns, $params, $titles)
    {
        $trends =  $this->combineData("both", $campaigns, $params);
        $refCommInfo = $this->campaignsData("RefusalComm", $campaigns, $params);

        $trends = $this->triangulate($trends, $refCommInfo, "refusalComm", 'trend');
        //$trends = $trends['trend']; // it comes in the array index = trend
        //dump($trends); die;

        $category = [['column'=>'Region'],
            ['column'=>'CID', 'substitute'=>
                ['col1'=>'CMonth', 'col2'=>'CYear', 'short'=>'my']
            ]
        ];
        $subTitle = $titles['subTitle'];
        $during = $titles['midTitle'];

        // all the math operations is done here
        $trends = $this->allMathOps($trends);

        // --------------------------- Trend of Vaccinated Children --------------------------------
        $vacChildTrend = $this->chart->chartData1Category($category[1],
            ['TotalVac'=>'Vac Camp',
                'cTotalVac'=>'Vac Catchup',
                'rfc_totalRefusalVacByRefComm'=>'Vac By Committees'],
            $trends);
        $vacChildTrend['title'] = 'Vaccinated Children During '.$during.', Catchup and By Committees';
        $vacChildTrend['subTitle'] = $subTitle;
        $data['vac_child_trend'] = $vacChildTrend;

        // --------------------------- Trend of Missed Children -------------------------------------
        $missedChildTrend = $this->chart->chartData1Category($category[1],
            ['TotalRemaining'=>'Rem After Campaign',
                'FinalRemaining' => 'Final Remaining After Catchup'], $trends);
        $missedChildTrend['title'] = 'Remaining Children After '.$during.', Catchup and Committees Work';
        $missedChildTrend['subTitle'] = $subTitle;
        $data['missed_child_trend'] = $missedChildTrend;

        // --------------------------- Trend of Missed Children By Type -----------------------------
        $missedByTypeTrend = $this->chart->chartData1Category($category[1],
            ['Disc'=>'Discrip',
                'DiscRemainingAbsent'=>'Absent',
                'DiscRemainingNSS'=>'NSS',
                'DiscFinalRemainingRefusal'=>'Refusal'], $trends);
        $missedByTypeTrend['title'] = 'Remaining Children By Reason After '.$during.', Catchup and Committees';
        $missedByTypeTrend['subTitle'] = $subTitle;
        $data['missed_by_type_trend'] = $missedByTypeTrend;

        // --------------------------- Trend of Recovering Missed -----------------------------------
        $missedRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'Recovered3Days'=>'3Days' ,
                'RecoveredDay4'=>'Day5',
                'cTotalRecovered'=>'Catchup',
                'rfc_totalRefusalVacByRefComm'=>'Committees',
                'Disc'=>'Discrip',
                'DiscFinalRemaining'=>'Remaining',
                ],
            $trends);
        $missedRecoveredTrend['title'] = "Remaining Children Recovery Camp/Revisit/Catchup/Committees";
        $missedRecoveredTrend['subTitle'] = $subTitle;
        $data['missed_recovered_trend'] = $missedRecoveredTrend;

        // ----------------------------- Trend of Absent Recovering --------------------------------
        $absentRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'VacAbsent3Days'=>'3Days' ,
                'VacAbsentDay4'=>'Day5',
                'cVacAbsent'=>'Catchup',
                'DiscAbsent'=>'Discrip',
                'DiscRemainingAbsent'=>'Remaining',
            ],

            $trends);
        $absentRecoveredTrend['title'] = "Absent Children Recovery Camp/Revisit/Catchup";
        $absentRecoveredTrend['subTitle'] = $subTitle;
        $data['absent_recovered_trend'] = $absentRecoveredTrend;

        // ----------------------------- Trend of NSS Recovering -----------------------------------
        $nssRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'VacNSS3Days'=>'3Days' ,
                'VacNSSDay4'=>'Day5',
                'cVacNSS'=>'Catchup',
                'DiscNSS'=>'Discrip',
                'DiscRemainingNSS'=>'Remaining',

                ],
            $trends);
        $nssRecoveredTrend['title'] = "NSS Children Recovery Camp/Revisit/Catchup";
        $nssRecoveredTrend['subTitle'] = $subTitle;
        $data['nss_recovered_trend'] = $nssRecoveredTrend;
        // ------------------------------ Trend of Refusal Recovering -----------------------------
        $refusalRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'VacRefusal3Days'=>'3Days' ,
                'VacRefusalDay4'=>'Day5',
                'cVacRefusal'=>'Catchup',
                'rfc_totalRefusalVacByRefComm'=>'Committees',
                'DiscRefusal'=>'Discrip',
                'DiscFinalRemainingRefusal'=>'Remaining',
                ],
            $trends);
        $refusalRecoveredTrend['title'] = "Refusal Children Recovery Camp/Revisit/Catchup/Committees";
        $refusalRecoveredTrend['subTitle'] = $subTitle;
        $data['refusal_recovered_trend'] = $refusalRecoveredTrend;

        // ------------------------------ Trend of Missed Recovery -------------------------------
        $missedChildRecoveryTrend = $this->chart->chartData1Category($category[1],
            [
                'FinalVaccinatedRefusal'=>'Recovered Refusal',
                'FinalVacNSS'=>'Recovered NSS',
                'FinalVacAbsent'=>'Recovered Absent',
                'DiscRemaining'=>'Remaining',
                ],
            $trends);
        $missedChildRecoveryTrend['title'] = "Recovering Missed Children By Reason during ".
                                              $during.", Catchup and by Committees";
        $missedChildRecoveryTrend['subTitle'] = $subTitle;
        $data['missed_child_recovery_trend'] = $missedChildRecoveryTrend;

        return new JsonResponse($data);
    }

    /**
     * @param $entity
     * @param $campaigns
     * @param $params
     * @param $titles
     * @return mixed
     */
    protected function latestInfoAction($entity, $campaigns, $params, $titles)
    {
        $info =  $this->combineData('both', $campaigns, $params);

        $refCommInfo = $this->campaignsData("RefusalComm", $campaigns, $params);

        $campInfo = $this->triangulate($info, $refCommInfo, "refusalComm", 'oneCamp');
        $campAgg = $this->triangulate($info, $refCommInfo, "refusalComm", 'oneCampAgg');

        $campInfo = $this->allMathOps($campInfo);
        $campAgg = $this->allMathOps($campAgg);

        $subTitle = $titles['subTitle'];
        $type = $titles['aggType'];

        // ---------------------------- Pie Chart one campaign missed by reason ----------------------------
        $missedByReasonPie = $this->chart->pieData(['DiscRemainingAbsent'=>'Absent',
            'DiscRemainingNSS'=>'NSS',
            'DiscFinalRemainingRefusal'=>'Refusal'], $campInfo);
        $missedByReasonPie['title'] = "Remaining Children By Reason";
        $data['missed_by_reason_pie_1'] = $missedByReasonPie;

        // ---------------------------- one campaign recovered all type -----------------------------------
        $recoveredAllType = $this->chart->pieData(
            [
                'Recovered3Days'=>'3Days',
                'RecoveredDay4'=>'Day5',
                'cTotalRecovered'=>'Catchup',
                'rfc_totalRefusalVacByRefComm'=>'Committees',
                'DiscFinalRemaining'=>'Remaining',
                'Disc' => 'Discrep'
            ],
            $campInfo);
        $recoveredAllType['title'] = "Missed Children Recovery Camp/Revisit/Catchup/Committees";
        $recoveredAllType['subTitle'] = $subTitle;
        $data['recovered_all_type_1'] = $recoveredAllType;

        // ---------------------------- last campaign Absent recovered by campaign  ----------------------
        $recoveredAbsent = $this->chart->pieData(
            ['VacAbsent3Days'=>'3Days',
            'VacAbsentDay4'=>'Day5',
            'cVacAbsent'=>'Catchup',
            'DiscRemainingAbsent'=>'Remaining',
            'DiscAbsent' => 'Discrep'
            ],
            $campInfo);
        $recoveredAbsent['title'] = "Absent Children Recovery Camp/Revisit";
        $recoveredAbsent['subTitle'] = $subTitle;
        $data['recovered_absent_1'] = $recoveredAbsent;

        // ---------------------------- last campaign NSS recovered in Campaign ----------------------------
        $recoveredNss = $this->chart->pieData(
            ['VacNSS3Days'=>'3Days',
             'VacNSSDay4'=>'Day5',
             'cVacNSS'=>'Catchup',
             'DiscRemainingNSS'=>'Remaining',
             'DiscNSS'=>'Discrep'
            ],
            $campInfo);
        $recoveredNss['title'] = "NSS Children Recovery Camp/Revisit";
        $recoveredNss['subTitle'] = $subTitle;
        $data['recovered_nss_1'] = $recoveredNss;

        // --------------------------- last campaign Refusal recovered in Campaign -------------------------
        $recoveredRefusal = $this->chart->pieData(
            ['VacRefusal3Days'=>'3Days',
            'VacRefusalDay4'=>'Day5',
            'cVacRefusal'=>'Catchup',
            'rfc_totalRefusalVacByRefComm'=>'Committees',
            'DiscFinalRemainingRefusal'=>'Remaining',
            'DiscRefusal' => 'Discrep'
            ],
            $campInfo);
        $recoveredRefusal['title'] = "Refusal Children Recovery Camp/Revisit/Committees";
        $recoveredRefusal['subTitle'] = $subTitle;
        $data['recovered_refusal_1'] = $recoveredRefusal;

        // ---------------------------- last campaign total missed by region -------------------------------

        $vacWastage = $this->chart->chartData1Category(['column'=>$titles['aggType']],
            ['DiscFinalRemaining'=>'Still Remaining'], $campAgg);
        $vacWastage['title'] = 'Remaining children after Camp/Revisit';
        $vacWastage['subTitle'] = $subTitle;
        $data['total_remaining_1'] = $vacWastage;

        // ---------------------------- last campaign total missed by location -------------------------------
        $oneCat = $titles['aggType'] === 'Region' ? true : false;
        if($oneCat) {
            $totalRemaining = $this->chart->chartData1Category(['column' => $titles['aggType']],
                [
                    'Recovered3Days'=>'3Days',
                    'RecoveredDay4'=>'Day5',
                    'cTotalRecovered'=>'Catchup',
                    'rfc_totalRefusalVacByRefComm'=>'Committees',
                    'Disc' => 'Discrep',
                    'DiscFinalRemaining'=>'Remaining',

                ], $campAgg);
            $totalRemaining['title'] = 'Missed Children Recovery During Campaign, Catchup and Committees';
            $totalRemaining['subTitle'] = $subTitle;
            $data['total_recovered_remaining_1'] = $totalRemaining;
        } else {
            $cat1 = ['column' => 'Region'];
            if($titles['aggType'] === "District")
                $cat1 = ['column' => 'Province'];
            $totalRemaining = $this->chart->chartData2Categories(
                $cat1,
                ['column' => $titles['aggType']],
                [
                    'Recovered3Days'=>'3Days',
                    'RecoveredDay4'=>'Day5',
                    'cTotalRecovered'=>'Catchup',
                    'rfc_totalRefusalVacByRefComm'=>'Committees',
                    'Disc' => 'Discrep',
                    'DiscFinalRemaining'=>'Remaining',

                ], $campAgg);
            $totalRemaining['title'] = 'Missed Children Recovery During Campaign, Catchup and Committees';
            $totalRemaining['subTitle'] = $subTitle;
            $data['total_recovered_remaining_1'] = $totalRemaining;
        }

        // ---------------------------- Tabular information of the campaign -------------------------------
        $table = HtmlTable::tableForDashboard($campAgg, $type);
        $data['info_table'] = $table;

        // just for the map data
        $data['map_data'] = json_encode($campAgg);

        // ---------------------------- Header Tiles Information of the campaign --------------------------
        $info_header = HtmlTable::infoForDashboard($campInfo);
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
        $oneCampData = $this->combineClustersData('both', $campaigns, $params);
        $refusalCommOneCampData = $this->clustersData("RefusalComm", $campaigns, $params);
        $oneCampData = $this->triangulate($oneCampData, $refusalCommOneCampData, "refusalComm");

        $oneCampData = $this->allMathOps($oneCampData);

        $oneCampBarChart = $this->chart->chartData1Category(['column'=>'Cluster'],
            [
                'Recovered3Days'=>'3Days',
                'RecoveredDay4'=>'Day5',
                'cTotalRecovered'=>'Catchup',
                'rfc_totalRefusalVacByRefComm'=>'Committees',
                'Disc' => 'Discrep',
                'DiscFinalRemaining'=>'Remaining',
            ],
            $oneCampData, true);
        $campaign = "No data for this campaign as per current filter";
        if(count($oneCampData) > 0)
            $campaign = $oneCampData[0]['CName']." Recovered and Remaining Children";
        $oneCampBarChart['title'] = $campaign;
        $data['missed_recovery_chart_1'] = $oneCampBarChart;
        return new JsonResponse($data);
    }

    protected function clustersTrendAction($entity, $campaigns, $params, $controlParams)
    {
        // fetch the data
        $heatMapData = $this->combineClustersData('both', $campaigns, $params);
        // load refusal committees clusters data
        $refusalCommHeatMapData = $this->clustersData("RefusalComm", $campaigns, $params);

        $heatMapData = $this->triangulate($heatMapData, $refusalCommHeatMapData, "refusalComm");

        $heatMapData = $this->allMathOps($heatMapData);
        $clusters = $params['cluster'];
        // set the calcTypeArray
        $calcTypeArray = ['type'=>'number'];

        $selectType = $controlParams["selectType"] === 'TotalRemaining' ?
            'FinalRemaining' : $controlParams['selectType'];

        $rawData = $this->chart->clusterDataForHeatMap($heatMapData,
                                            $selectType,
                                            ['column' => 'CID', 'substitute' => 'shortName'],
                                            $clusters,
                                            $calcTypeArray, 'table');
        // get the entity manager for the benckmarks
        $em = $this->getDoctrine()->getManager();
        $stops= $em->getRepository("App:HeatmapBenchmark")
            ->findOne("TriangleData", $selectType);

        // create columns for the table
        $cols = array(['col' => 'rowName', 'label' => 'Cluster', 'calc' => 'none']);
        foreach ($rawData['xAxis'] as $axi) {
            $cols[] = ['col' => $axi, 'label' => $axi, 'calc' => 'rev'];
        }

        $table = HtmlTable::heatMapTable($rawData['data'],
            $cols, HtmlTable::heatMapTableHeader($selectType),
            $stops['minValue'],
            $stops['maxValue'], "normal", true);

        $data['cluster_trend'] = $table;
        //==================================== Clusters Trend Charts ===========================
        $locTrends = $this->combineClustersData(
            'both',
            $controlParams['locTrendIds'], $params
        );

        $refusalCommLocTrends = $this->clustersData("RefusalComm", $controlParams['locTrendIds'], $params);

        $locTrends = $this->triangulate($locTrends, $refusalCommLocTrends, "refusalComm");

        $locTrends = $this->allMathOps($locTrends);

        $category = [['column'=>'Region'],
            ['column'=>'CID', 'substitute'=>
                ['col1'=>'CMonth', 'col2'=>'CYear', 'short'=>'my']
            ]
        ];
        // --------------------------- Loc Trend of Missed Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['FinalTotalVaccinated'=>'Recovered', 'DiscFinalRemaining'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'Missed Children Recovery';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_all_type'] = $locTrendAllType;

        // --------------------------- Loc Trend of Absent Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['FinalVacAbsent'=>'Recovered', 'DiscRemainingAbsent'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'ICN Reduced Absent Children';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_absent'] = $locTrendAllType;

        // --------------------------- Loc Trend of NSS Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['FinalVacNSS'=>'Recovered', 'DiscRemainingNSS'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'ICN Reduced NSS Children';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_nss'] = $locTrendAllType;

        // --------------------------- Loc Trend of Refusal Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>'Cluster'],
            $category[1],
            ['FinalVaccinatedRefusal'=>'Recovered', 'DiscFinalRemainingRefusal'=>'Remaining'],
            $locTrends
        );
        $locTrendAllType['title'] = 'ICN Reduced Refusal Children';
        $locTrendAllType['subTitle'] = null;
        $data['loc_trend_refusal'] = $locTrendAllType;

        return new JsonResponse($data);
    }

    private function allMathOps($data) {

        $data = Triangle::mathOps($data, ['TotalRemaining', 'cRegMissed'],
            '-', 'Disc', 'cRegMissed');
        $data = Triangle::mathOps($data, ['TotalRemaining', 'cTotalRecovered'],
            '-', 'Remaining');
        $data = Triangle::mathOps($data, ['Remaining', 'Disc'], '-',
            'DiscRemaining');
        $data = Triangle::mathOps($data, ['TotalVac', 'cTotalVac'], '+',
            'FinalTotalVac');
        // refusals calculation after refusal committees
        $data = Triangle::mathOps($data, ['Remaining', 'rfc_totalRefusalVacByRefComm'],
            "-", "FinalRemaining");
        $data = Triangle::mathOps($data, ['DiscRemaining', 'rfc_totalRefusalVacByRefComm'],
            "-", "DiscFinalRemaining");
        $data = Triangle::mathOps($data, ['FinalTotalVac', 'rfc_totalRefusalVacByRefComm'], '+',
            'FinalTotalVaccinated');
        // For Absent
        $data = Triangle::mathOps($data, ['RemAbsent', 'cRegAbsent'],
            '-', 'DiscAbsent', 'cRegMissed');
        $data = Triangle::mathOps($data, ['RemAbsent', 'cVacAbsent'],
            '-', 'RemainingAbsent');
        $data = Triangle::mathOps($data, ['RemainingAbsent', 'DiscAbsent'],
            '-', 'DiscRemainingAbsent');
        $data = Triangle::mathOps($data, ['VacAbsent', 'cVacAbsent'], '+',
            'FinalVacAbsent');

        // For NSS
        $data = Triangle::mathOps($data, ['RemNSS', 'cRegNSS'],
            '-', 'DiscNSS', 'cRegNSS');
        $data = Triangle::mathOps($data, ['RemNSS', 'cVacNSS'],
            '-', 'RemainingNSS');
        $data = Triangle::mathOps($data, ['RemainingNSS', 'DiscNSS'],
            '-', 'DiscRemainingNSS');
        $data = Triangle::mathOps($data, ['VacNSS', 'cVacNSS'], '+',
            'FinalVacNSS');

        // For Refusal
        $data = Triangle::mathOps($data, ['RemRefusal', 'cRegRefusal'],
            '-', 'DiscRefusal', 'cRegRefusal');
        $data = Triangle::mathOps($data, ['RemRefusal', 'cVacRefusal'],
            '-', 'RemainingRefusal');
        $data = Triangle::mathOps($data, ['RemainingRefusal', 'DiscRefusal'],
            '-', 'DiscRemainingRefusal');
        $data = Triangle::mathOps($data, ['VacRefusal', 'cVacRefusal'], '+',
            'FinalVacRefusal');
        // refusals calculation after refusal committees
        $data = Triangle::mathOps($data, ['RemainingRefusal', 'rfc_totalRefusalVacByRefComm'],
            "-", "FinalRemainingRefusal");
        $data = Triangle::mathOps($data, ['DiscRemainingRefusal', 'rfc_totalRefusalVacByRefComm'],
            "-", "DiscFinalRemainingRefusal");
        $data = Triangle::mathOps($data, ['FinalVacRefusal', 'rfc_totalRefusalVacByRefComm'], '+',
            'FinalVaccinatedRefusal');
        // For Percentage Calculation
        // Percentage Absent
        $data = Triangle::mathOps($data, ['DiscRemainingAbsent', 'CalcTarget'],
            '%', 'PerAbsent');
        // Percentage NSS
        $data = Triangle::mathOps($data, ['DiscRemainingNSS', 'CalcTarget'],
            '%', 'PerNSS');
        // Percentage Refusal
        $data = Triangle::mathOps($data, ['DiscFinalRemainingRefusal', 'CalcTarget'],
            '%', 'PerRefusal');
        // Percentage Missed
        $data = Triangle::mathOps($data, ['DiscFinalRemaining', 'CalcTarget'],
            '%', 'PerRemaining');

        return $data;

    }

    /**
     * @param Request $request
     * @return mixed
     * @Route("/get_csv/district")
     * @Security("is_granted('RROLE_ADMIN')")
     */
    public function csvAction(Request $request) {
        $campaigns = [32, 33, 34, 35, 36, 37, 38, 39];
        $params['by'] = 'campaign';
        //$params['value'] = [6, 13, 33, 34, 23, 24, 26, 12, 25];
//        $infoAdmin =  $this->chart->chartData("CoverageData", 'aggBySubDistrict', $campaigns, $params);
//        $infoCatch =  $this->chart->chartData("CatchupData", 'aggBySubDistrict', $campaigns, $params);

        /*
         * Aggregation By Region and Campaign
         *
         */
        $admin = $this->chart->chartData("CoverageData", 'aggByCampaign', $campaigns,$params);
        $catchup = $this->chart->chartData("CatchupData", 'aggByCampaign', $campaigns,$params);

        $refCommInfo = $this->chart->chartData("RefusalComm", "aggByCampaign", $campaigns, $params);


        /*
         * Aggregation by Campaign
         *
        $admin = $this->chart->chartData("CoverageData", 'campaignStatistics', $campaigns,$params);
        $catchup = $this->chart->chartData("CatchupData", 'campaignStatistics', $campaigns,$params);

        $refCommInfo = $this->chart->chartData("RefusalComm", "campaignStatistics", $campaigns, $params);
        */


//        dump($admin);
//        dump($catchup);
//        dump($refCommInfo); die;

        $admin_catchup = $this->triangulate($admin, $catchup);
        $campAgg = $this->triangulate($admin_catchup, $refCommInfo, "refusalComm");

        $campAgg = $this->allMathOps($campAgg);

//        if(count($campAgg) > 0) {
//            $keys = array_keys($campAgg[0]);
//            array_unshift($campAgg, $keys);
//        }

        return Exporter::exportCSV($campAgg);
    }


    private function triangulate($data, $data1, $source = "catchup", $index = null) {
        // required indexes in both sources
        $indexes = [];
        $prefix = "c";
        if($source === "catchup") {
            $indexes = ['RegMissed', 'TotalRecovered', 'TotalVac',
                'RegAbsent', 'VacAbsent',
                'RegNSS', 'VacNSS', 'RegRefusal', 'VacRefusal'];
        } else if($source === "refusalComm") {
            $indexes = ['refusalAfterDay5', 'refusalVacByCRC', 'refusalVacByRC',
                'refusalVacByCIP', 'refusalVacBySenior',
                'totalRefusalVacByRefComm'];
            $prefix = "rfc_";
        }
        return Triangle::triangulateCustom(
            [
                $index===null?$data:$data[$index],
                ['data'=>$index===null?$data1:$data1[$index],
                    'indexes'=>$indexes,
                    'prefix'=>$prefix
                ]
            ], 'joinkey');
    }
}
