<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 11:20 AM
 */

namespace App\Controller;


use App\Datatables\CatchupDataDatatable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CatchupDataController
 * @package App\Controller
 * @IsGranted("ROLE_PARTNER")
 */
class CatchupDataController extends AbstractController
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
     * @Route("/catchup_data", name="catchup_data")
     */
    public function indexAction() {

        return $this->render("pages/catchup_data/index.html.twig", [
            'source'=>'CatchupData',
            'url' => 'catchup_data',
            'urlCluster' => 'catchup_data_cluster',
        ]);
    }

    /**
     * @param null $district
     * @return Response
     * @Route("/catchup_data/clusters/{district}", name="catchup_data_cluster", options={"expose"=true})
     */
    public  function clusterLevelAction($district = null) {

        $data = ['district' => $district===null?0:$district];
        $data['title'] = 'Catchup Data Clusters Trends';
        $data['pageTitle'] = "Catchup Data Trends and Analysis, Cluster Level";
        $data['source'] = 'CatchupData';
        $data['ajaxUrl'] = 'catchup_data';
        return $this->render("pages/catchup_data/clusters-table.html.twig",
            $data
        );

    }

    /**
     * @param Request $request
     * @Route("/catchup_data/download", name="catchup_data_download", options={"expose"=true})
     * @Security("is_granted('ROLE_EDITOR')")
     * @return Response
     */
    public function download(Request $request) {

        return $this->render("pages/catchup_data/download.html.twig", [
            'source'=>'CatchupData',
            'url' => 'catchup_data_data',
        ]);
    }


    public function downloadAction(Request $request, $type='all') {

        $isAjax = $request->isXmlHttpRequest();

        $datatable = $this->factory->create(CatchupDataDatatable::class);
        $datatable->buildDatatable(['type'=>$type]);

        if ($isAjax) {
            $this->responseService->setDatatable($datatable);
            $dbQueryBuilder = $this->responseService->getDatatableQueryBuilder();

            $qb = $dbQueryBuilder->getQb();
            if($type != "all") {
                $qb->where("catchupdata.dataSource = :type");
                $qb->setParameter('type', $type);
            }
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
            ['datatable'=>$datatable,'title'=>'Catchup (Fieldbook) Data', 'buttons' => $buttons]);
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete/catchup_data", name="catchup_data_bulk_delete", methods={"POST"})
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
            $repository = $em->getRepository('App:CatchupData');

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
