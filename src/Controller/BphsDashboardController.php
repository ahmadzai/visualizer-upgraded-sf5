<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
    public function index()
    {
        return $this->render('bphs_plus/index.html.twig', []);
    }

    /**
     * @return Response
     * @Route("/upload", name="bphs_bulk_upload")
     * @Security("is_granted('ROLE_RESTRICTED_EDITOR')")
     */
    public function upload() {
        return $this->render('bphs_plus/import.html.twig', [

        ]);
    }

}