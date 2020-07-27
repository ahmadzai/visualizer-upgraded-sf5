<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 1/23/2018
 * Time: 7:50 PM
 */

namespace App\Controller\Ajax;

use App\Service\Settings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class AjaxBphsFilterController extends AbstractController
{

    /**
     * @param Request $request
     * @param string $source
     * @param Settings $settings
     * @Route("bphs/filter/", name="bphs_filter", options={"expose"=true}, methods={"GET"})
     * @return Response
     */
    public function bphsFilterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $campaigns = $em->getRepository('App:BphsIndicatorReach')->findYearMonth();

        $provinces = $em->getRepository('App:BphsIndicatorReach')->findProvinces($campaigns);

        $selectedCampaigns = count($campaigns) > 0 ? $campaigns[0]['yearMonth'] : null;

        return $this->render("bphs_plus/bphs-filter.html.twig", [
            'campaigns' => $campaigns,
            'provinces' => $provinces,
            'selectedCampaign' => $selectedCampaigns
        ]);
    }


    /**
     * @Route("filter/bphs_district", name="bphs_filter_district", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function bphsFilterDistrictAction(Request $request) {

        $province = $request->get('province');
        $campaign = $request->get('campaign');

        $district = $this->getDoctrine()->getRepository('App:BphsIndicatorReach')
            ->findDistricts($campaign, $province);

        $response = array();
        foreach ($province as $prov) {
            $temp = array();
            $pname = '';
            foreach ($district as $option) {

                if($option['pid'] == $prov[0]) {
                    $pname = $option['provinceName'];
                    $temp[] = array('label' => $option['districtName'], 'value'=>$option['id']);
                }
            }

            $response[] = array('label'=>$pname, 'children'=>$temp);
        }

        return new Response(json_encode($response));

    }


    /**
     * @Route("filter/bphs_province", name="bphs_filter_province", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function bphsFilterProvinceAction(Request $request) {

        $campaign = $request->get('campaign');
        //$requestData = json_decode($content);
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('App:BphsIndicatorReach')
            ->findProvinces($campaign);

        $response = array();

        foreach ($data as $option) {
            $response[] = array('label' => $option['provinceName'], 'value' => $option['id']);
            //$response .= "<option value='".$option['p_provinceCode']."'>".$option['p_provinceName']."</option>";
        }

        return new Response(
            json_encode($response)
        );

    }


    /**
     * @Route("filter/bphs_facility", name="bphs_filter_facility", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function bphsFilterFacilityAction(Request $request, Settings $settings) {

        $district = $request->get('district');
        $campaign = $request->get('campaign');

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('App:BphsIndicatorReach')
            ->findHealthFacilities($campaign, null, $district);

        return new Response($this->facilitiesJSON($data));

    }

    private function facilitiesJSON($facilities, $firstLoad = true) {
        $districts = array();
        foreach($facilities as $facility) {

            $districts[] = $facility['districtName'];

        }

        $districts = array_unique($districts);
        $response = array();
        foreach ($districts as $district) {
            $temp = array();
            foreach ($facilities as $option) {
                if($option['districtName'] == $district) {
                    $temp[] = array('label' => $option['facilityName'], 'value' => $option['id']);
                }
            }

            $response[] = array('label'=>$district, 'children'=>$temp);
        }

        return json_encode($response);

    }





}
