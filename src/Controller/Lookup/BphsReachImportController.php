<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 11:20 AM
 */

namespace App\Controller\Lookup;


use App\Entity\ImportedFiles;
use App\Service\ReachImporter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Importer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Note: The Import/Upload Workflow:
 * 1: import(DataTable)Action is called,
 * 2: importDataTable)HandleAction
 * 3: createSyncViewAction is called (just to create the sync view),
 * 4: if the user click the Sync button
 * syncDataAction is called, if cancel, then cancelUploadAction() is called
 * @Security("is_granted('ROLE_EDITOR')")
 */
class BphsReachImportController extends AbstractController
{

    /**
     * @Route("/import/bphs/reach", name="import_bphs_reach_data")
     * @param Request $request
     * @return Response
     */
    public function importDataAction(Request $request) {

        /*
         * First checking if the provided entity has upload enable?
         */
        $entity = "bphs_indicator_reach";

        $file = new ImportedFiles();
        $form = $this->createImportForm($file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->persistFileAndReturn($file, $entity,
                'import_bphs_reach_data_handle');
        }

        return $this->render('bphs_plus/import/import.html.twig', array(
            'file' => $file,
            'form' => $form->createView(),
            'entity' => $entity
        ));

    }

    /**
     * @param Request $request
     * @param string $entity
     * @param integer $fileId
     * @param ReachImporter $importer
     * @param Importer $orgImporter
     * @return Response
     * @Route("/import/bphs/reach/{entity}/{fileId}/handle", name="import_bphs_reach_data_handle")
     */

    public function importDataHandleAction(Request $request, $entity, $fileId,
                                           ReachImporter $importer,
                                           Importer $orgImporter)
    {
        $em = $this->getDoctrine()->getManager();

        $uploadMgr = $em->getRepository("App:UploadManager")->findOneBy(['tableName'=>$entity]);
        if($uploadMgr !== null) {

            $result = $importer->readReachData($fileId);
            if($result['result'] === 'error') {
                $this->addFlash('error', $result['excel_data']);
                $importer->deleteFile($fileId);
                return $this->redirectToRoute('import_bphs_reach_data');
            }
            $entityClass = "App\\Entity\\" . $orgImporter->remove_($entity, true);
            $uploaderSettings = $importer->getUploaderSettings();
            //dd($uploaderSettings);

            // this function call create the whole form for mapping columns (excel to database)
            $form = $orgImporter->createMapperForm($result['cols_excel'], $uploaderSettings['cols_required']);
            // when user click map button (submit)
            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $mappedArray = $form->getData();
                    $excelData = $result['excel_data'];
                    $flashMessage = "";
                    $file_id = -1;
                    if ($uploaderSettings['has_temp'] === true) {
                        $entityClass = "\\App\\Entity\\Temp" . $orgImporter->remove_($entity, true);
                        $flashMessage = ", please synchronize it with main table!";
                        $file_id = $fileId;
                    }

                    // get entity and unique cols
                    $uniqueCols = $uploaderSettings['cols_unique'];
                    $entityCols = $orgImporter->cleanDbColumns($uploaderSettings['cols_entity']);
                    $updateAbleCols = $uploaderSettings['cols_update_able'];
                    $result = $orgImporter->processData(
                        $entityClass, $excelData, $mappedArray,
                        $file_id,
                        ['uniqueCols'=>$uniqueCols, 'entityCols'=>$entityCols, 'updateAbleCols' => $updateAbleCols],
                        ['hfCode' => 'bphsHealthFacility', 'indicator' => 'bphsIndicator'],
                        [
                            'bphsHfIndicator' => [
                                'indicator' => 'indicator',
                                'healthFacility' => 'hfCode',
                                'targetYear' => 'reportYear'
                            ]
                        ]
                    );

                    if (isset($result['success'])) {
                        $this->addFlash("success", $result['success'] . $flashMessage);
                    } else {
                        $message = "<ul>";
                        foreach ($result['error'] as $exception) {
                            $message .= "<li>" . $exception . "</li>";
                        }
                        $message .= "</ul>";

                        $this->addFlash("warning", $message);
                    }
                    // redirect based on the process of the upload
                    if($uploaderSettings['has_temp'] === true) {
                        return $this->redirectToRoute("sync_data_view",
                            ['entity' => $entity, 'fileId' => $fileId]);
                    }
                    elseif ($uploaderSettings['has_temp'] !== true)
                        return $this->redirectToRoute("import_bphs_reach_data");
                }
            }

            return $this->render('import/import_handle.html.twig',
                ['form' => $form->createView(),
                    'cols_excel' => $result['cols_excel'],
                    'entity' => $entity,
                    'file' => $fileId]);


        } else
            throw new FileNotFoundException("Sorry you have requested a bad file");

    }


    /**
     * @param ImportedFiles $importedFiles
     * @return FormInterface
     * Create Upload form
     */
    private function createImportForm(ImportedFiles $importedFiles) {
        return $this->createForm('App\Form\UploadType', $importedFiles);
    }

    /**
     * @param ImportedFiles $importedFiles
     * @param $entity
     * @param null $redirectUrl
     * @return RedirectResponse
     */
    private function persistFileAndReturn(ImportedFiles $importedFiles, $entity, $redirectUrl = null) {
        $em = $this->getDoctrine()->getManager();

        $importedFiles->setDataType($entity);
        $em->persist($importedFiles);
        $em->flush();

        $this->addFlash('success', 'The files '.$importedFiles->getFileName().' stored successfully');

        return $this->redirectToRoute($redirectUrl,
            ['fileId'=>$importedFiles->getId(), 'entity'=>$entity]
        );
    }

    /**
     * @Route("/download/bphs-reach/template", name="download_bphs_reach_template")
     * @param Importer $importer
     * @return mixed
     * @throws
     */
    public function downloadTemplateAction(Importer $importer)
    {
        // ask the service for a Excel5
        $phpExcelObject = new Spreadsheet();

        $em = $this->getDoctrine()->getManager();

        // manually set the columns
        $cols = array(
            'HF Code',
            'Report Year',
            'Report Month'
        );

        // dynamically load the assigned indicators of the current year (the year is taken from current date)
        $indicators = $em->getRepository('App:BphsHfIndicator')->getAssignedIndicators();
        foreach ($indicators as $indicator) {
            $cols[] = $indicator['indicatorName'];
        }


        $phpExcelObject->getProperties()->setCreator("PolioDB")
            ->setLastModifiedBy("Polio DB Server")
            ->setTitle("Data Upload Template for BPHS Indicators Reach")
            ->setSubject("Upload data using this template")
            ->setKeywords("Microsoft Excel Generated by PHP SpreadSheet")
            ->setCategory("Template");
        foreach($cols as $key=>$col) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key+1)."1", $col);
        }

        $phpExcelObject->getActiveSheet()->setTitle('template');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        $sheet = $phpExcelObject->getActiveSheet();
        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        foreach ($cellIterator as $cell) {
            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }
        // create the writer
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($phpExcelObject, 'Xlsx');

        // create the response
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "template_bphs_indicator_reach.xlsx"
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }



}
