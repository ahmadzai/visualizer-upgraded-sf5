<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IcnStaffController
 * @package App\Controller
 * @Route("/staff")
 */
class IcnStaffController extends AbstractController
{
    /**
     * @Route("/", name="staff_dashboard")
     */
    public function index()
    {
        return $this->render('staff/index.html.twig', [

        ]);
    }

}