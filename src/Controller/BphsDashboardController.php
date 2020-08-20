<?php


namespace App\Controller;


use App\Service\BphsReachIndicator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BphsDashboardController
 * @package App\Controller
 * @Route("/bphs")
 */
class BphsDashboardController extends AbstractController
{
    /**
     * @Route("/", name="bphs_dashboard")
     */
    public function index(BphsReachIndicator $reachIndicator)
    {
        $yearMonths = $this->getDoctrine()->getRepository("App:BphsIndicatorReach")->findReportedMonths();
        $params = [
                'yearMonth' => $yearMonths,
                'province' => null,
                'district' => null,
                'facility' => null,
                'isCumulative' => null
            ];
        list($table, $fixedCols) = $reachIndicator->tableColsIndicatorReach($params);
        list($tableMonths) = $reachIndicator->tableColsMonthReach($params);

        return $this->render('bphs_plus/index.html.twig', [
            'tableCumulative' => $table,
            'noFixedCols' => $fixedCols,
            'tableMonths' => $tableMonths,
        ]);
    }

    /**
     * @return Response
     * @Route("/upload", name="bphs_bulk_upload")
     */
    public function upload() {
        return $this->render('bphs_plus/import.html.twig', [

        ]);
    }

}