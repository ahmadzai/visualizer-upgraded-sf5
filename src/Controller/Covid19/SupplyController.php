<?php


namespace App\Controller\Covid19;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SupplyController
 * @package App\Controller\Covid19
 * @Route("/covid-19/supplies")
 */
class SupplyController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/", name="covid19_supplies")
     */
    public function indexAction(Request $request) {
        return $this->render(
            'covid19/pages/supplies.html.twig',
            []
        );
    }

}
