<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MainController
 * @package App\Controller
 * @IsGranted("ROLE_PARTNER")
 */
class MainController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="home")
     */
    public function indexAction() {
        return $this->render("pages/index.html.twig",
            [
                'source'=>'CoverageData',
                'url' => 'main_dashboard',
                'urlCluster' => "main_cluster_dashboard"
            ]
        );

    }

    /**
     * @param Request $request
     * @param null $district
     * @return Response
     * @Route("/main/clusters/{district}", name="main_cluster_dashboard", options={"expose"=true})
     */
    public  function clusterLevelAction(Request $request, $district = null) {

        $data = ['district' => $district===null?0:$district];
        $data['title'] = 'Triangulated Clusters Trends';
        $data['pageTitle'] = "Triangulated Data (Admin, Catchup and Refusals Committees' Data) Trends By Clusters";
        $data['source'] = 'CoverageData';
        $data['ajaxUrl'] = 'main_dashboard';
        return $this->render("pages/clusters-table.html.twig",
            $data
        );

    }

}
