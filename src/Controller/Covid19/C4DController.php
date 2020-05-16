<?php


namespace App\Controller\Covid19;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class C4DController
 * @package App\Controller\Covid19
 * @Route("/covid-19/c4d")
 */
class C4DController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/", name="covid19_c4d")
     */
    public function indexAction(Request $request) {
        return $this->render(
            'covid19/pages/c4d.html.twig',
            []
        );
    }

}
