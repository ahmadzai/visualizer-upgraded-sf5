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

        $category = [['column'=>'province', 'substitute' => 'Province'],
            ['column'=>'yearMonth']
        ];

        list($tableCum, $fixedCols) = $reachIndicator->tableColsIndicatorReach($params);
        list($tableMonth, $noCols, $chartsData) = $reachIndicator->tableColsMonthReach($params);

        $ancTrends = $charts->chartData1Category($category[1],
            ['ANC_totalReach'=>'Monthly Reach'],
            $chartsData['ANC'], false);

        return new JsonResponse([
            'bphs_table_cum'=>$tableCum,
            'bphs_table_months'=>$tableMonth]
        );

    }


}
