<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 11:20 AM
 */

namespace App\Controller;

use App\Datatables\CoverageDataDatatable;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Security("is_granted('ROLE_NORMAL_USER') or is_granted('ROLE_PARTNER')")
 */
class CoverageDataController extends AbstractController
{

    /**
     * @var DatatableFactory
     */
    private $factory;
    /**
     * @var DatatableResponse
     */
    private $responseService;

    public function __construct(DatatableFactory $factory, DatatableResponse $responseService)
    {
        $this->factory = $factory;
        $this->responseService = $responseService;
    }

    /**
     * @Route("/coverage_data", name="coverage_data")
     * @return Response
     */
    public function indexAction() {

        return $this->render("pages/coverage_data/index.html.twig", [
            'source'=>'CoverageData',
            'url' => 'coverage_data',
            'urlCluster' => 'coverage_data_cluster'
        ]);

    }


    /**
     * @param Request $request
     * @param string $type
     * @Route("/coverage_data/download/{type}", name="coverage_data_download", options={"expose"=true})
     * @Security("is_granted('ROLE_EDITOR')")
     * @return Response
     * @throws \Exception
     */
    public function downloadAction(Request $request, $type='all') {

        $isAjax = $request->isXmlHttpRequest();

        $title = "Coverage (Admin) Data";
        $alink = ['route'=>'#', 'title'=>'New Record', 'class'=>'btn-info'];
        $info = null;
        $datatable = null;
        if($type == 'all')
            $datatable = $this->factory->create(CoverageDataDatatable::class);
        //Todo: define a downloader for downloading summary data
//        else if($type == 'summary') {
//            $datatable = $this->get('sg_datatables.factory')->create(CoverageDataSummaryDatatable::class);
//            $alink = null;
//            $title = "Coverage (Admin) Data Summary";
//            $info = "Please wait as the summary calculation will take some time!";
//        }
        $datatable->buildDatatable();


        if ($isAjax) {
            $this->responseService->setDatatable($datatable);
            $dbQueryBuilder = $this->responseService->getDatatableQueryBuilder();
            $dbQueryBuilder->useQueryCache(true);            // (1)
            $dbQueryBuilder->useCountQueryCache(true);       // (2)
            $dbQueryBuilder->useResultCache(true, 60);       // (3)
            $dbQueryBuilder->useCountResultCache(true, 60);  // (4)
            $qb = $dbQueryBuilder->getQb();
            if($type == 'all')
            {

                $qb->addOrderBy('campaign.id', 'DESC');
                $qb->addOrderBy('district.province');
                $qb->addOrderBy('district.id');
                $qb->addOrderBy('coveragedata.subDistrict');
                $qb->addOrderBy('coveragedata.clusterNo');
                $qb->addOrderBy('coveragedata.vacDay');

            }
            //Part of above todo
//            else if($type == 'summary')
//            {
//
//                $qb->addOrderBy('coverageclustersummary.campaign', 'DESC');
//                $qb->addOrderBy('coverageclustersummary.province');
//                $qb->addOrderBy('coverageclustersummary.district');
//            }

            return $this->responseService->getResponse();
        }

        // creating buttons
        $buttons = array(
            'a' => $alink,
            'btn-group' =>['class'=>'btn-default', 'title'=>'Options', 'options' => array(
                ['route' => 'coverage_data_download',
                    'params' => [], 'title'=>'Raw Data'],
                ['route' => 'coverage_data_download',
                    'params' => ['type'=>'summary'], 'title'=>'Summary Data'],
            )]
        );

        return $this->render('pages/table.html.twig',
            ['datatable'=>$datatable,'title'=>$title,'buttons'=>$buttons, 'info' => $info]);
    }

    /**
     * @param null $district
     * @return Response
     * @Route("/coverage_data/clusters/{district}", name="coverage_data_cluster",
     *     options={"expose"=true})
     */
    public  function clusterLevelAction($district = null) {
        $data = ['district' => $district===null?0:$district];
        $data['title'] = 'Admin Data Clusters Trends';
        $data['pageTitle'] = "Coverage Data Trends and Analysis, Cluster Level";
        $data['source'] = 'CoverageData';
        $data['ajaxUrl'] = 'coverage_data';
        return $this->render("pages/coverage_data/clusters-table.html.twig",
            $data
        );

    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete/coverage_data", name="coverage_data_bulk_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return Response
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function bulkDeleteAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $choices = $request->request->get('data');
            $token = $request->request->get('token');

            if (!$this->isCsrfTokenValid('multiselect', $token)) {
                throw new AccessDeniedException('The CSRF token is invalid.');
            }

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('App:CoverageData');

            foreach ($choices as $choice) {
                $entity = $repository->find($choice['id']);
                $em->remove($entity);
            }

            $em->flush();

            return new Response('Success', 200);
        }

        return new Response('Bad Request', 400);
    }




}
