<?php


namespace App\Controller;


use App\Service\Charts;
use App\Service\Exporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DownloadController extends AbstractController
{
    /**
     * @param Request $request
     * @param Charts $charts
     * @param string|null $source
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/download/{source}", name="download_data")
     */
    public function download(Request $request, Charts $charts, string $source = null) {
        if($request->getMethod() == "POST") {

            //dd($request->request->get('filterCampaign'));
            if($request->request->get('download')) {
                $aggType = $request->request->get('filterAggType');
                $campaign = $request->request->get('filterCampaign');

                $function = "campaignStatistics";
                $params = ['by'=>'campaign', 'district'=>null];
                if($aggType === "region") {
                    $function = "aggByCampaign";
                    $params = ['by'=>'region', 'value'=>$this->selectRegions($campaign, $source)];
                } else if($aggType === "province") {
                    $function = "aggByCampaign";
                    $params = ['by'=>'province', 'value'=>$this->selectProvinces($campaign, $source)];
                } else if($aggType === "district") {
                    $function = "aggByCampaign";
                    $params = ['by'=>'district', 'district'=>['All']];
                } else if($aggType === "cluster") {
                    $function = "clusterAggByCampaigns";
                    $params = null;
                }

                $data = $charts->chartData($source, $function, $campaign, $params);
                return Exporter::exportCSV($data);
            }
        }
        // after completing the download, go back to the last visited page
        if($request->headers->get('referer'))
            return $this->redirect($request->headers->get('referer'));
        // if someone call this controller direclty, bounce them back to home page
        return $this->redirectToRoute("home");
    }

    private function selectRegions($campaigns, $source) {
        $regions = $this->getDoctrine()->getRepository('App:Province')
            ->selectRegionsBySourceAndCampaign($source, $campaigns);
        if(is_array($regions)) {
            $regionsArray = [];
            foreach ($regions as $region)
                $regionsArray[] = $region['provinceRegion'];

            return $regionsArray;
        }
        return null;
    }

    private function selectProvinces($campaigns, $source) {
        $provinces = $this->getDoctrine()->getRepository('App:Province')
            ->selectProvinceBySourceAndCampaign($source, $campaigns);
        if(is_array($provinces)) {
            $provincesArray = [];
            foreach ($provinces as $province)
                $provincesArray[] = $province['id'];

            return $provincesArray;
        }
        return null;
    }


}