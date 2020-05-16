<?php

namespace App\Controller\Ona;

use App\Service\OdkOnaForm;
use App\Service\Settings;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class OnaMainController extends AbstractController
{
    /**
     * @param OdkOnaForm $onaForm
     * @param integer $project
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ona/connect/{project}", name="ona_connect", defaults={"project"=0})
     */
    public function indexAction(OdkOnaForm $onaForm, $project)
    {
        $url = $onaForm->apiUrl('ONA.IO').'/projects';
        $view = "ona_connect.html.twig";
        if($project > 0) {
            $url .= "/" . $project;
            $view = "ona_forms.html.twig";
        }

        $apiData = $onaForm->getApiData($url, $onaForm->apiKey('ONA.IO'));
        $apiData = json_decode($apiData);

        return $this->render(':ona:'.$view,
            [
                'data' => $apiData
            ]);
    }

    /**
     * @param OdkOnaForm $onaForm
     * @param integer $form
     * @param string $table
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\DBAL\DBALException
     * @Route("/ona/sync/{form}/{table}", name="ona_sync_form")
     */
    public function syncFormAction(OdkOnaForm $onaForm, $form, $table)
    {
        // check if this form is already mapped to the database
        $check = $onaForm->checkTable($table);
        // retrieve new information about the form from ONA.IO
        $newInfo = $onaForm->getApiData($onaForm->apiUrl('ONA.IO')."/forms/".$form,
                                            $onaForm->apiKey('ONA.IO'));
        $dbInfo = 0;
        if($check) {
            $dbInfo = $onaForm->countRows($table);
        }

        return $this->render(':ona:ona_sync_form.html.twig',
            [
                'form_info' => json_decode($newInfo),
                'db_info' => $dbInfo,
            ]);

    }


    /**
     * @param Request $request
     * @param Settings $settings
     * @param OdkOnaForm $onaForm
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\DBALException
     * @Route("ona/save/form", name="ona_save_data")
     */
    public function saveDataAction(Request $request, Settings $settings, OdkOnaForm $onaForm)
    {
        //dump($request); die;
        $table = $request->get('table_name');
        $form = $request->get('formid');
        $limit = $request->get('download_chunk');

        $check = $onaForm->checkTable($table);
        $start = 0;
        if($check) {
            $dbInfo = $onaForm->countRows($table);
            $start = count($dbInfo) > 0 ? $dbInfo[0]['total_rows'] : 0;
        }
        //echo "we are just here"; die;
        // Create new table if not created
        if(!$check)
        {
            $onaForm->createTable($table);
            // check if the table is created by now
            $check = $onaForm->checkTable($table);
        }
        // if table was already created by now
        if($check) {
            // Request new data and save it in the database table
            $url = $onaForm->apiUrl('ONA.IO');
            $url .= '/data/'.$form.'?start='.$start.'&limit='.$limit;
            $apiData = $onaForm->getApiData($url, $onaForm->apiKey('ONA.IO'));
            $flag = $onaForm->saveData($apiData, $table);
        }

        return $this->redirectToRoute("ona_sync_form", ['form'=>$form, 'table'=>$table]);

    }

    /**
     * @Route("ona/test", name="ona_test")
     */
    public function testOnaAction(Settings $settings, OdkOnaForm $onaForm) {

        $apiKey = $onaForm->apiUrl("ONA.IO");
        dump($apiKey);die;
        $url = $settings::ONA_BASE_URL."/forms/230255";
        $data = $onaForm->getApiData($url, $settings::ONA_API);
        dump(json_decode($data));
        die;
    }

}
