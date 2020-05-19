<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('bphs_plus/index.html.twig', [

        ]);
    }

}