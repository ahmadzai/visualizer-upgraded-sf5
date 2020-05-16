<?php


namespace App\Controller\Covid19;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Covid19Controller
 * @package App\Controller\Covid19
 * @Route("/covid-19")
 */
class Covid19Controller extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) {
        return $this->render(
            'covid19/pages/home.html.twig',
            []
        );
    }

}
