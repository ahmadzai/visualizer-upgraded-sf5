<?php
/**
 * Created by PhpStorm.
 * User: Wazir Khan Ahmadzai
 * Date: 8/16/2020
 * Time: 11:20 AM
 */

namespace App\Controller\Bphs;


use App\Service\BphsReachIndicator;
use App\Service\Charts;
use App\Service\DataManipulation;
use App\Service\HtmlTable;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AjaxBphsDashboardController
 * @package App\Controller
 */
class AjaxBphsDashboardController extends AbstractController
{

    /**
     * @return Response
     * @Route("ajax/bphs_dashboard", name="ajax_bphs_dashboard", options={"expose"=true})
     */
    public function indexAction(Request $request, BphsReachIndicator $reachIndicator, Charts $charts) {

        $params['yearMonth'] = $request->get('campaign');
        $params['province'] = $request->get('province');
        $params['district'] = $request->get('district');
        $params['facility'] = $request->get('facility');
        $params['isCumulative'] = $request->get('cumulative');

        list($tableCum, $fixedCols) = $reachIndicator->tableColsIndicatorReach($params);
        list($tableMonth, $noCols, $chartsData) = $reachIndicator->tableColsMonthReach($params);

        $dataToBeReturned = $this->createCharts($chartsData, $charts, $noCols);
        $dataToBeReturned['bphs_table_cum'] = $tableCum;
        $dataToBeReturned['bphs_table_months'] = $tableMonth;

        return new JsonResponse($dataToBeReturned);

    }


    private function createCharts(array $data, Charts $charts, $noLocCols = 1) {

        if(is_array($data)) {

            $category = [['column' => 'provinceName', 'substitute' => 'Province'],
                ['column' => 'yearMonth']
            ];

            $highCharts = [];
            $indicators = $data['indicators'];
            $chartData = $data['data'];
            $title = " Monthly Coverage";
            $counter = 1;
            foreach ($indicators as $indicator) {
                // below conditions are static, as we already know the structure of location (1.General, 2.Province, 3.District, 4.Facility)
                if($noLocCols <= 1) {
                    $chart = $charts->chartData1Category($category[1],
                        [$indicator . '_totalReach' => 'Monthly Coverage'],
                        $chartData[$indicator], false);
                } elseif($noLocCols == 2) {
                    $chart = $charts->chartData2Categories(['column' => 'provinceName'], $category[1],
                        [$indicator . '_totalReach' => 'Monthly Coverage'],
                        $chartData[$indicator], false);
                } elseif($noLocCols == 3) {
//                    $chart = $charts->chartData3Categories(['column' => 'provinceName'], ['column'=>'districtName'], $category[1],
//                        [$indicator . '_totalReach' => 'Monthly Reach'],
//                        $chartData[$indicator], false);
                    $chart = $charts->chartData2Categories(['column'=>'districtName'], $category[1],
                        [$indicator . '_totalReach' => 'Monthly Coverage'],
                        $chartData[$indicator], false);
                } elseif($noLocCols == 4) {
                    $chart = $charts->chartData2Categories(['column'=>'facilityName'], $category[1],
                        [$indicator . '_totalReach' => 'Monthly Coverage'],
                        $chartData[$indicator], false);
                }
                $chart['title'] = $indicator . $title;
                $highCharts['chart'.$counter] = $chart;
                $counter++;
            }

            return $highCharts;

        }

    }


}
