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
    public function indexAction(Request $request, DataManipulation $manipulator, BphsReachIndicator $reachIndicator) {

        $selectedMonths = $request->get('campaign');
        $provinces = $request->get('province');
        $districts = $request->get('district');
        $facilities = $request->get('facility');
        $cumulative = $request->get('cumulative');

        list($table, $fixedCols) = $reachIndicator->tableColsIndicatorReach([
            'yearMonth' => $selectedMonths,
            'province' => $provinces,
            'district' => $districts,
            'facility' => $facilities,
            'isCumulative' => $cumulative
        ]);

        return new JsonResponse(['bphs_table'=>$table, 'noFixedCols'=>$fixedCols]);

    }

    /**
     * @return Response
     * @Route("api/int_icn_monitoring/sm", name="int_ajax_icn_monitoring_sm", options={"expose"=true})
     */
    public function InternalIndexAction(Request $request, Charts $charts) {

        $selectedMonths = $request->get('campaign');
        $regions = $request->get('region');
        $provinces = $request->get('province');
        $districts = $request->get('district');
        $clusters = $request->get('cluster');
        $cumulative = $request->get('cumulative');

        $xAxises = [
            ['col'=>'mName', 'label'=>'Month', 'calc'=>'none'],
            ['col'=>'provinceName', 'label'=>'Prov', 'calc'=>'none'],
            ['col'=>'attendance', 'label'=>'Attend', 'calc'=>'normal'],
            ['col'=>'profile', 'label'=>'Profile', 'calc'=>'normal'],
            ['col'=>'preparedness', 'label'=>'Prepared', 'calc'=>'normal'],
            ['col'=>'fieldbook', 'label'=>'FB', 'calc'=>'normal'],
            ['col'=>'mobilization', 'label'=>'Mobiliz', 'calc'=>'normal'],
            ['col'=>'campPerform', 'label'=>'Camp', 'calc'=>'normal'],
            ['col'=>'catchupPerform', 'label'=>'Catchup', 'calc'=>'normal'],
            ['col'=>'refusalChallenge', 'label'=>'Refusal', 'calc'=>'rev'],
            ['col'=>'higherSup', 'label'=>'Super', 'calc'=>'none'],
            ['col'=>'comSupport', 'label'=>'Community', 'calc'=>'normal'],
            ['col'=>'coldchain', 'label'=>'Coldchian', 'calc'=>'normal'],
            ['col'=>'accessChallenge', 'label'=>'Access', 'calc'=>'normal'],
            ['col'=>'overallPerform', 'label'=>'Overall', 'calc'=>'normal'],
        ];

        //$function = "aggByRegionMonth";
        $funcPart1 = "aggBy";
        $funcPart2 = "Region";

        $firstArg = $selectedMonths;

        if($clusters !== null && count($clusters)>0) {
            $firstArg = ['district'=> $districts, 'cluster'=>$clusters];
            //$function = "aggBySmMonth";
            $funcPart2 = "Sm";
            array_splice($xAxises, 2, 0,
                [['col'=>'districtName', 'label'=>'Dist', 'calc'=>'none'],
                    ['col'=>'cluster', 'label'=>'Clstr', 'calc'=>'none'],
                    ['col'=>'ccsName', 'label'=>'CCS', 'calc'=>'none'],
                    ['col'=>'smName', 'label'=>'SM', 'calc'=>'none']]);

        } else if($districts !== null && count($districts)>0) {
            $firstArg = $districts;
            //$function = "aggByClusterMonth";
            $funcPart2 = "Cluster";
            array_splice($xAxises, 2, 0,
                [['col'=>'districtName', 'label'=>'Dist', 'calc'=>'none'],
                    ['col'=>'cluster', 'label'=>'Clstr', 'calc'=>'none']]);
        } else if($provinces !== null && count($provinces)>0) {
            $firstArg = $provinces;
            //$function = "aggByDistrictMonth";
            $funcPart2 = "District";
            array_splice($xAxises, 2, 0,
                [['col'=>'districtName', 'label'=>'Dist', 'calc'=>'none']]);
        } else if($regions !== null && count($regions)>0) {
            //$function = "aggByProvinceMonth";
            $funcPart2 = "Province";
            $firstArg = $regions;
        }

        $function = $funcPart1;
        if($cumulative === true || $cumulative == "true") {
            unset($xAxises[0]); // remove month name
            $function .= "Month" . $funcPart2;
        }
        else
            $function .= $funcPart2."Month";

        $source = $charts->chartData('IntOdkSmMonitoring', $function, $firstArg, $selectedMonths);

        $table = HtmlTable::tableODK($source, $xAxises);

        return new JsonResponse(['icn_table'=>$table]);
        //return $this->render("pages/icn/clusters.html.twig", ['table'=>$table, 'source'=>'OdkCcsMonitoring']);
    }



}
