<?php
/**
 * Created by PhpStorm.
 * User: Wazir Khan Ahmadzai
 * Date: 8/12/2018
 * Time: 9:08 PM
 */

namespace App\Controller\Ajax;


use App\Service\Settings;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\HtmlTable;

class AjaxCatchupController extends CommonDashboardController
{
    /**
     * @param Request $request
     * @param Settings $settings
     * @return mixed
     * @Route("/ajax/catchup_data", name="ajax_catchup_data",
     *     options={"expose"=true})
     */
    public function mainAction(Request $request, Settings $settings)
    {
        return parent::mainAction($request, $settings);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Route("/ajax/cluster/catchup_data/", name="ajax_cluster_catchup_data",
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

        $trends =  $this->campaignsData($entity, $campaigns, $params);

        $trends = $trends['trend']; // it comes in the array index = trend
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
                    'TotalRecovered'=>'Recovered',
                    'TotalRemaining'=>'Remaining',
                ],
                $locTrends
            );
        $locTrendAllType['title'] = 'ICN Reduced Missed Children';
        $locTrendAllType['subTitle'] = $subTitle;
        $data['loc_trend_all_type'] = $locTrendAllType;

        // --------------------------- Loc Trend of Absent Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>$titles['aggType']],
            $category[1],
            [
                'VacAbsent'=>'Recovered',
                'RemAbsent'=>'Remaining',
            ],
            $locTrends
        );
        $locTrendAllType['title'] = 'ICN Reduced Absent Children';
        $locTrendAllType['subTitle'] = $subTitle;
        $data['loc_trend_absent'] = $locTrendAllType;

        // --------------------------- Loc Trend of NSS Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>$titles['aggType']],
            $category[1],
            [
                'VacNSS'=>'Recovered',
                'RemNSS'=>'Remaining',
            ],
            $locTrends
        );
        $locTrendAllType['title'] = 'ICN Reduced NSS Children';
        $locTrendAllType['subTitle'] = $subTitle;
        $data['loc_trend_nss'] = $locTrendAllType;

        // --------------------------- Loc Trend of Refusal Children --------------------------------
        $locTrendAllType = $this->chart->chartData2Categories(
            ['column'=>$titles['aggType']],
            $category[1],
            [
                'VacRefusal'=>'Recovered',
                'RemRefusal'=>'Remaining',
            ],
            $locTrends
        );
        $locTrendAllType['title'] = 'ICN Reduced Refusal Children';
        $locTrendAllType['subTitle'] = $subTitle;
        $data['loc_trend_refusal'] = $locTrendAllType;


        // --------------------------- Trend of Vaccinated Children --------------------------------
        $vacChildTrend = $this->chart
            ->chartData1Category($category[1],
                                  [
                                      'TotalRecovered'=>'Recovered Children'
                                  ],
                                  $trends);
        $vacChildTrend['title'] = 'Recovered Children '.$during;
        $vacChildTrend['subTitle'] = $subTitle;
        $data['vac_child_trend'] = $vacChildTrend;

        // --------------------------- Trend of Missed Children -------------------------------------
        $missedChildTrend = $this->chart->chartData1Category($category[1],
                                      ['TotalRemaining'=>'Remaining Children'], $trends);
        $missedChildTrend['title'] = 'Remaining Children During '.$during;
        $missedChildTrend['subTitle'] = $subTitle;
        $data['missed_child_trend'] = $missedChildTrend;

        // --------------------------- Trend of Missed Children By Type -----------------------------
        $missedByTypeTrend = $this->chart->chartData1Category($category[1],
            [
                'RemAbsent'=>'Absent',
                'RemNSS'=>'NSS',
                'RemRefusal'=>'Refusal'
            ], $trends);
        $missedByTypeTrend['title'] = 'Remaining Children By Reason '.$during;
        $missedByTypeTrend['subTitle'] = $subTitle;
        $data['missed_by_type_trend'] = $missedByTypeTrend;

        // --------------------------- Trend of Recovering Missed -----------------------------------
        $missedRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'TotalRecovered'=>'Recovered', 'TotalRemaining'=>'Remaining',
            ],
            $trends);
        $missedRecoveredTrend['title'] = "Missed Children Recovery in Catchup";
        $missedRecoveredTrend['subTitle'] = $subTitle;
        $data['missed_recovered_trend'] = $missedRecoveredTrend;

        // ----------------------------- Trend of Absent Recovering --------------------------------
        $absentRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'VacAbsent'=>'Recovered', 'RemAbsent'=>'Remaining',
            ],
            $trends);
        $absentRecoveredTrend['title'] = "Absent Children Recovery in Catchup";
        $absentRecoveredTrend['subTitle'] = $subTitle;
        $data['absent_recovered_trend'] = $absentRecoveredTrend;

        // ----------------------------- Trend of NSS Recovering -----------------------------------
        $nssRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'VacNSS'=>'Recovered', 'RemNSS'=>'Remaining',
            ],
            $trends);
        $nssRecoveredTrend['title'] = "NSS Children Recovery in Catchup";
        $nssRecoveredTrend['subTitle'] = $subTitle;
        $data['nss_recovered_trend'] = $nssRecoveredTrend;
        // ------------------------------ Trend of Refusal Recovering -----------------------------
        $refusalRecoveredTrend = $this->chart->chartData1Category($category[1],
            [
                'VacRefusal'=>'Recovered', 'RemRefusal'=>'Remaining',
            ],
            $trends);
        $refusalRecoveredTrend['title'] = "Refusal Children Recovery in Catchup";
        $refusalRecoveredTrend['subTitle'] = $subTitle;
        $data['refusal_recovered_trend'] = $refusalRecoveredTrend;

        // ------------------------------ Trend of Missed Recovery -------------------------------
        $missedChildRecoveryTrend = $this->chart->chartData1Category($category[1],
            [
                'VacRefusal'=>'Recovered Refusal',
                'VacNSS'=>'Recovered NSS',
                'VacAbsent'=>'Recovered Absent',
                'TotalRemaining'=>'Remaining',
            ],
            $trends);
        $missedChildRecoveryTrend['title'] = "Recovering Missed Children By Reason";
        $missedChildRecoveryTrend['subTitle'] = $subTitle;
        $data['missed_child_recovery_trend'] = $missedChildRecoveryTrend;

        return new JsonResponse($data);
    }

    protected function latestInfoAction($entity, $campaigns, $params, $titles)
    {
        //echo "Catchup Data Class is Called"; die;
        $info =  $this->campaignsData($entity, $campaigns, $params);
        $campInfo = $info['oneCamp']; // it comes in the array index = trend
        $campAgg = $info['oneCampAgg']; // get the aggregated data

        $subTitle = $titles['subTitle'];
        $type = $titles['aggType'];

        // ---------------------------- Pie Chart one campaign missed by reason ----------------------------
        $missedByReasonPie = $this->chart->pieData(['RemAbsent'=>'Absent',
            'RemNSS'=>'NSS',
            'RemRefusal'=>'Refusal'], $campInfo);
        $missedByReasonPie['title'] = "Remaining Children By Reason";
        $data['missed_by_reason_pie_1'] = $missedByReasonPie;

        // ---------------------------- one campaign recovered all type by Catchup -------------------------
        $recoveredAllType = $this->chart->pieData(['TotalRecovered'=>'Recovered',
                                                    'TotalRemaining'=>'Remaining'],
                                                  $campInfo);
        $recoveredAllType['title'] = "Missed Children Recovery in Catchup";
        $recoveredAllType['subTitle'] = $subTitle;
        $data['recovered_all_type_1'] = $recoveredAllType;

        // ---------------------------- last campaign Absent recovered by Catchup  -------------------------
        $recoveredAbsent = $this->chart->pieData(['VacAbsent'=>'Recovered',
                                                            'RemAbsent'=>'Remaining'],
                                                            $campInfo);
        $recoveredAbsent['title'] = "Absent Children Recovery in Catchup";
        $recoveredAbsent['subTitle'] = $subTitle;
        $data['recovered_absent_1'] = $recoveredAbsent;

        // ---------------------------- last campaign NSS recovered by Catchup -----------------------------
        $recoveredNss = $this->chart->pieData(['VacNSS'=>'Recovered', 'RemNSS'=>'Remaining'],
            $campInfo);
        $recoveredNss['title'] = "NSS Children Recovery in Catchup";
        $recoveredNss['subTitle'] = $subTitle;
        $data['recovered_nss_1'] = $recoveredNss;

        // --------------------------- last campaign Refusal recovered by Catchup --------------------------
        $recoveredRefusal = $this->chart->pieData(['VacRefusal'=>'Recovered',
                                                           'RemRefusal'=>'Remaining'],
                                                           $campInfo);
        $recoveredRefusal['title'] = "Refusal Children Recovery in Catchup";
        $recoveredRefusal['subTitle'] = $subTitle;
        $data['recovered_refusal_1'] = $recoveredRefusal;

        // ---------------------------- last campaign total missed by region -------------------------------
        $totalRemaining = $this->chart->chartData1Category(['column'=>$titles['aggType']],
                                               ['TotalRemaining'=>'Remaining'], $campAgg);
        $totalRemaining['title'] = 'Remaining children after catchup';
        $totalRemaining['subTitle'] = $subTitle;
        $data['total_remaining_1'] = $totalRemaining;

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

        // ---------------------------- Tabular information of the campaign -------------------------------
        $table = HtmlTable::tableForCatchupData($campAgg, $type);
        $data['info_table'] = $table;

        // just for the map data
        $data['map_data'] = json_encode($campAgg);

        // ---------------------------- Header Tiles Information of the campaign --------------------------
        $info_header = HtmlTable::infoForCatchup($campInfo);
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

        $oneCampBarChart = $this->chart->chartData1Category(['column'=>'Cluster'],
            [
                'TotalRecovered' => 'Recovered',
                'RemAbsent' => 'Absent',
                'RemNSS' => 'NSS',
                'RemRefusal' => 'Refusal',
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
        $heatMapData = $this->clustersData($entity, $campaigns, $params);

        $locTrends = $this->clustersData($entity, $controlParams['locTrendIds'], $params);

        // get the clusters from the params as they needed for the table
        $clusters = $params['cluster'];

        // get the selectType from controlParams
        $selectType = $controlParams["selectType"];

        // set the calcTypeArray
        $calcTypeArray = ['type'=>'number'];
        if($controlParams['calcType'] === 'percent') {
            $col = 'RegMissed';
            if($selectType == 'RemAbsentPer')
                $col = 'RegAbsent';
            elseif($selectType == 'RemNSSPer')
                $col = 'RegNSS';
            elseif($selectType == 'RemRefusalPer')
                $col = 'RegRefusal';

            $calcTypeArray = ['type'=>'percent', 'column'=>$col];
        }

        $rawData = $this->chart->clusterDataForHeatMap($heatMapData,
                                str_replace("Per", "", $selectType),
                                ['column' => 'CID', 'substitute' => 'shortName'],
                                $clusters, $calcTypeArray, 'table');
        // get the entity manager for the benckmarks
        $em = $this->getDoctrine()->getManager();
        $stops= $em->getRepository("App:HeatmapBenchmark")
            ->findOne($entity, $selectType);

        // create columns for the table
        $cols = array(['col' => 'rowName', 'label' => 'Cluster', 'calc' => 'none']);
        foreach ($rawData['xAxis'] as $axi) {
            $cols[] = ['col' => $axi, 'label' => $axi, 'calc' => 'rev'];
        }

        $table = HtmlTable::heatMapTable($rawData['data'],
            $cols, HtmlTable::heatMapTableHeader($selectType),
            $stops['minValue'],
            $stops['maxValue']);

        $data['cluster_trend'] = $table;

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


        //return new Response($table);
        return new JsonResponse($data);
    }



}
