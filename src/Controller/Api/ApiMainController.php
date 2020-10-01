<?php
/**
 * Created by PhpStorm.
 * User: Awesome
 * Date: 1/29/2019
 * Time: 11:16 AM
 */

namespace App\Controller\Api;


use App\Service\Settings;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiMainController
 * @package App\Controller\Api
 * @Route("api")
 * @Security("is_granted('ROLE_USER')")
 */
class ApiMainController extends AbstractController
{
    /**
     * @Route("/admindata/by_district/{campaign}", methods={"GET"})
     * @param Settings $settings
     * @param int|null $campaign
     * @return Response
     */
    public function getAdminDataByDistricts(Settings $settings, $campaign = null) {

        if($campaign === null) {
            $campaign = $campaign ?? $settings->latestCampaign("CoverageData");
            $campaign = $campaign[0]['id'];
        }
        $data = $this->getDoctrine()
            ->getRepository("App:CoverageData")
            ->aggByCampaign([$campaign], ['by'=>'district', 'district'=>['All']]);
        $data = $this->jsonEncode([
            'campaign'=>$campaign,
            'districts'=>$data
        ]);
        return new Response($data, 200);
    }

    /**
     * @param Settings $settings
     * @param string $campaign
     * @return Response
     * @Route("/campaign/{campaign}", methods={"GET"})
     */
    public function getCampaign(Settings $settings, $campaign = "latest") {

        if($campaign === "all") {
            $campaigns = $settings->campaignMenu("CoverageData");
            $campaigns = $this->jsonEncode(['campaigns'=>$campaigns]);

            return new Response($campaigns, 200);
        } else if($campaign === "latest") {
            $campaign = $settings->latestCampaign("CoverageData");
            $campaign = $this->jsonEncode(['campaign'=>$campaign]);

            return new Response($campaign, 200);
        }

    }

    /**
     * @param Settings $settings
     * @param null $campaign
     * @return Response
     * @Route("/catchup/by_district/{campaign}", methods={"GET"})
     */
    public function getCatchupDataByDistrict(Settings $settings, $campaign = null) {
        if($campaign === null) {
            $campaign = $campaign ?? $settings->latestCampaign("CatchupData");
            $campaign = $campaign[0]['id'];
        }
        $data = $this->getDoctrine()
            ->getRepository("App:CatchupData")
            ->aggByCampaign([$campaign], ['by'=>'district', 'district'=>['All']]);
        $data = $this->jsonEncode([
            'campaign'=>$campaign,
            'districts'=>$data
        ]);
        return new Response($data, 200);
    }

    /**
     * @param Settings $settings
     * @param null $campaign
     * @return Response
     * @Route("/roc_data/by_district/{campaign}", methods={"GET"})
     */
    public function getROCDataByDistrict(Settings $settings, $campaign = null) {
        if($campaign === null) {
            $campaign = $campaign ?? $settings->latestCampaign("RefusalComm");
            $campaign = $campaign[0]['id'];
        }
        $data = $this->getDoctrine()
            ->getRepository("App:RefusalComm")
            ->aggByCampaign([$campaign], ['by'=>'district', 'district'=>['All']]);
        $data = $this->jsonEncode([
            'campaign'=>$campaign,
            'districts'=>$data
        ]);
        return new Response($data, 200);
    }

    private function jsonEncode($data) {
        return $this->get('serializer')->serialize($data, "json");
    }


}
