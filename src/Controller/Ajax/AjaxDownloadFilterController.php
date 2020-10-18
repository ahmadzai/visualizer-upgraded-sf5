<?php


namespace App\Controller\Ajax;


use App\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxDownloadFilterController extends AbstractController
{
    /**
     * @param Request $request
     * @param string $source
     * @param Settings $settings
     * @Route("/downloadFilter/{source}", name="download_filter", options={"expose"=true}, methods={"GET"})
     * @return Response
     */
    public function downloadFilterAction(Request $request, $source="CoverageData", Settings $settings)
    {

        $em = $this->getDoctrine()->getManager();

        $campaigns = $em->getRepository('App:Campaign')->selectCampaignBySource($source);

        return $this->render("download_filter/campaign.html.twig",
            ['campaigns' => $campaigns]
        );
    }

}