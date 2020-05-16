<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 11:20 AM
 */

namespace App\Controller\Lookup;


use App\Datatables\ImportedFilesDatatable;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class ImportedFilesController
 * @package App\Controller
 * @Security("is_granted('ROLE_EDITOR')")
 */
class ImportedFilesController extends AbstractController
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
     * @Route("/uploaded/files/manage", name="manage_uploaded_files",
     *     options={"expose"=true}, methods={"GET"})
     * @throws \Exception
     */
    public function indexAction(Request $request) {

        $isAjax = $request->isXmlHttpRequest();

        $datatable = $this->factory->create(ImportedFilesDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {

            $this->responseService->setDatatable($datatable);
            $dbQueryBuilder = $this->responseService->getDatatableQueryBuilder();

            $qb = $dbQueryBuilder->getQb();
            $qb->addOrderBy('importedfiles.id', 'DESC');
            return $this->responseService->getResponse();
        }



        return $this->render('pages/table.html.twig',
            ['datatable'=>$datatable, 'title'=>'Uploaded Files Management',
             'change_breadcrumb' => true,
             'breadcrumb_text' => 'Manage<small> uplaoded files</small>']);
    }

    /**
     * Bulk delete action.
     *
     * @param Request $request
     *
     * @Route("/bulk/delete/files", name="imported_files_bulk_delete", methods={"POST"})
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
            $repository = $em->getRepository('App:ImportedFiles');

            foreach ($choices as $choice) {
                $entity = $repository->find($choice['id']);
                $em->remove($entity);
            }

            $em->flush();

            return new Response('Success', 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * @param $id
     * @param DownloadHandler $downloadHandler
     * @return StreamedResponse
     * @internal param ImportedFiles $file
     * @Route("/uploaded/files/download/{id}", name="uploaded_files_download")
     */
    public function downloadFileAction($id, DownloadHandler $downloadHandler)
    {
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository("App:ImportedFiles")->find($id);
        if($file !== null) {

            return $downloadHandler->downloadObject($file, $fileField = 'importedFile');
        } else
            throw new FileNotFoundException("Sorry you have requested a bad file");
    }




}
