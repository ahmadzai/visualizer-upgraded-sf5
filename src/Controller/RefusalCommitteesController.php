<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 11:20 AM
 */

namespace App\Controller;


use App\Datatables\RefusalCommDatatable;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CatchupDataController
 * @package App\Controller
 * @Security("is_granted('ROLE_NORMAL_USER')")
 */
class RefusalCommitteesController extends AbstractController
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
     * @return Response
     * @Route("/ref_committees", name="ref_committees")
     */
    public function indexAction() {

//        $data = $this->getDoctrine()
//            ->getRepository('App:District')
//            ->selectDistrictBySourceProvinceAndCampaign("CatchupData", [33, 6], [32, 33, 34]);
//
//        dump($data); die;

        return $this->render("pages/refusal_comm/index.html.twig", [
            'source'=>'RefusalComm',
            'url' => 'ref_committees',
            'urlCluster' => 'cluster_ref_committees',
        ]);
    }

    /**
     * @param null $district
     * @return Response
     * @Route("/ref_committees/clusters/{district}", name="cluster_ref_committees", options={"expose"=true})
     */
    public  function clusterLevelAction($district = null) {

        $data = ['district' => $district===null?0:$district];
        $data['title'] = 'Refusals Committees\'s Clusters Level Data';
        $data['pageTitle'] = "Refusals Committees's Data Trends and Analysis, Cluster Level";
        $data['source'] = 'RefusalComm';
        $data['ajaxUrl'] = 'ref_committees';
        return $this->render("pages/refusal_comm/clusters-table.html.twig",
            $data
        );

    }

    /**
     * @param Request $request
     * @Route("/ref_committees/download", name="ref_committees_data_download", options={"expose"=true})
     * @Security("is_granted('ROLE_EDITOR')")
     * @return Response
     */
    public function download(Request $request) {

        return $this->render("pages/catchup_data/download.html.twig", [
            'source'=>'RefusalComm',
            'url' => 'ref_committees',
        ]);
    }


    public function downloadAction(Request $request) {

        $isAjax = $request->isXmlHttpRequest();

        // Get your Datatable ...
        //$datatable = $this->get('app.datatable.post');
        //$datatable->buildDatatable();

        // or use the DatatableFactory
        $datatable = $this->factory->create(RefusalCommDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $this->responseService->setDatatable($datatable);
            $dbQueryBuilder = $this->responseService->getDatatableQueryBuilder();

            $qb = $dbQueryBuilder->getQb();

            $qb->addOrderBy('campaign.id', 'DESC');
            $qb->addOrderBy('district.province');
            $qb->addOrderBy('district.id');
            return $this->responseService->getResponse();
        }


        // creating buttons
        $buttons = array(
            'a' => ['route'=>'#', 'title'=>'New Record', 'class'=>'btn-info'],
        );
        return $this->render('pages/table.html.twig',
            ['datatable'=>$datatable,'title'=>'Refusals Committees Data', 'buttons' => $buttons]);
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete/ref_committees",
     *     name="refusal_committees_bulk_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @return Response
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
            $repository = $em->getRepository('App:RefusalComm');

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
