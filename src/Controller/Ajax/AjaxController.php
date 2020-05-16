<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 9/29/2017
 * Time: 4:23 PM
 */

namespace App\Controller\Ajax;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\Query;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AjaxController extends AbstractController
{
    /**
     * Get all Campaigns from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/campaigns", name="select2_campaign", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllCampaign(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $term = $request->get('q');

            $campaigns = $em->getRepository('App:Campaign')->searchCampaigns($term);

            $result = array();

            foreach ($campaigns as $campaign) {
                $result[$campaign->getId()] = $campaign->getCampaignName();
            }

            //$result = array_reverse($result, true);

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }

    /**
     * Get all Districts from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/districts", name="select2_district", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllDistricts(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $term = $request->get('q');
            $districts = $em->getRepository('App:District')->searchDistricts($term);

            $result = array();

            foreach ($districts as $district) {
                $result[$district->getId()] = $district->getDistrictName();
            }

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }

    /**
     * Get all Provinces from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/provinces", name="select2_province", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllProvinces(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $term = $request->get('q');

            $provinces = $em->getRepository('App:Province')->searchProvinces($term);

            $result = array();

            foreach ($provinces as $province) {
                $result[$province->getId()] = $province->getProvinceName();
            }

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }


    /**
     * Get all Campaigns from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/campaigns_names", name="select2_campaign_names", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllCampaignNames(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            //$campaigns = $em->getRepository('App:Campaign')->findAll();
            $campaigns = $em->createQuery(
                "SELECT cmp.id, cmp.campaignName FROM App:Campaign cmp ORDER BY cmp.id DESC"
            )
                ->getResult(Query::HYDRATE_SCALAR);
            $result = array();

            foreach ($campaigns as $campaign) {
                $result[$campaign['campaignName']] = $campaign['campaignName'];
            }

            //$result = array_reverse($result, true);

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }

    /**
     * Get all Districts from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/districts_names", name="select2_district_names", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllDistrictsNames(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $districts = $em->getRepository('App:District')->findAll();

            $result = array();

            foreach ($districts as $district) {
                $result[$district->getDistrictName()] = $district->getDistrictName();
            }

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }

    /**
     * Get all Provinces from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/provinces_names", name="select2_province_names", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllProvincesNames(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $provinces = $em->getRepository('App:Province')->findAll();

            $result = array();

            foreach ($provinces as $province) {
                $result[$province->getProvinceName()] = $province->getProvinceName();
            }

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }


    /**
     * Get all Regions from Database to show in Select2-Filter.
     *
     * @param Request $request
     *
     * @Route("/regions", name="select2_region", options={"expose"=true})
     *
     * @return JsonResponse|Response
     */
    public function select2AllRegions(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $regions = $em->createQuery(
                    "SELECT p.provinceRegion FROM App:Province p
                     GROUP BY p.provinceRegion ORDER BY p.provinceRegion"
                )
                ->getResult(Query::HYDRATE_SCALAR);
            $result = array();
            foreach ($regions as $region) {
                $result[$region['provinceRegion']] = $region['provinceRegion'];
            }

            return new JsonResponse($result);
        }

        return new Response('Bad request.', 400);
    }


}
