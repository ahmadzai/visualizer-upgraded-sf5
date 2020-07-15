<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 11:20 AM
 */

namespace App\Controller\Lookup;


use App\Entity\ImportedFiles;
use App\Entity\User;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Query;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
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
class ImportController extends AbstractController
{

    /**
     * @Route("/import/{entity}", name="import_data")
     * @param Request $request
     * @param Entity $entity
     * @return Response
     */
    public function importDataAction(Request $request, $entity) {

        /*
         * First checking if the provided entity has upload enable?
         */
        $em = $this->getDoctrine()->getManager();

        $uploadMgr = $em->getRepository("App:UploadManager")
            ->findOneBy(['tableName'=>$entity]);
        if($uploadMgr !== null) {
            if($uploadMgr->getEnabled()) {
                $file = new ImportedFiles();
                $form = $this->createImportForm($file);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    return $this->persistFileAndReturn($file, $entity, 'import_data_handle');
                }

                return $this->render('import/import.html.twig', array(
                    'file' => $file,
                    'form' => $form->createView(),
                    'entity' => $entity
                ));
            } else
                throw new FileNotFoundException("Bad Request! this entity doesn't support upload");

        } else
            throw new FileNotFoundException("Bad Request! this entity doesn't support upload");
    }

    /**
     * @param int $fileId
     * @param Importer $importer
     * @param Request $request
     * @param string $entity
     * @Route("/import/{entity}/{fileId}/handle", name="import_data_handle")
     * @return Response
     */

    public function importDataHandleAction(Request $request, $entity, $fileId, Importer $importer)
    {
        $em = $this->getDoctrine()->getManager();

        $uploadMgr = $em->getRepository("App:UploadManager")->findOneBy(['tableName'=>$entity]);
        if($uploadMgr !== null) {
            $excludedCols = $uploadMgr->getExcludedColumns();
            $hasTemp = $uploadMgr->getHasTemp();

            $entityName = $importer->remove_($entity, true);
            $entityClass = "App\\Entity\\" . $entityName;

            // below function call performing huge task regarding reading file and giving us back the data
            $data = $this->checkFileData($entityName, $excludedCols, $fileId, $importer);
            // if no data or any errors (flash messages also set above) redirect
            if ($data === false) {
                return $this->redirectToRoute('import_data', ['entity' => $entity]);
            }
            // this function call create the whole form for mapping columns (excel to database)
            $form = $importer->createMapperForm($data['cols_excel'], $data['cols_entity']);
            // when user click map button (submit)
            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $mappedArray = $form->getData();
                    $excelData = $data['excel_data'];
                    $flashMessage = "";
                    $file_id = -1;

                    if ($hasTemp) {
                        $entityClass = "\\App\\Entity\\Temp" . $entityName;
                        $flashMessage = ", please synchronize it with main table!";
                        $file_id = $fileId;

                    }
                    // get entity and unique cols
                    $uniqueCols = $uploadMgr->getUniqueColumns();
                    $entityCols = $uploadMgr->getEntityColumns();

                    $updateAbleCols = $uploadMgr->getUpdateAbleColumns();
                    $result = $importer->processData(
                        $entityClass, $excelData, $mappedArray, $file_id,
                        ['uniqueCols' => $uniqueCols, 'entityCols' => $entityCols, 'updateAbleCols'=>$updateAbleCols],
                        null,
                        null
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
                    if($hasTemp)
                        return $this->redirectToRoute("sync_data_view", ['entity' => $entity, 'fileId' => $fileId]);
                    elseif (!$hasTemp)
                        return $this->redirectToRoute("import_data", ['entity'=>$entity]);
                }
            }

            return $this->render('import/import_handle.html.twig',
                    ['form' => $form->createView(),
                     'cols_excel' => $data['cols_excel'],
                     'entity' => $entity,
                     'file' => $fileId]);
        } else
            throw new FileNotFoundException("Sorry you have requested a bad file");

    }


    /**
     * @Route("/sync/{entity}/{fileId}", name="sync_data_view")
     * @param Request $request
     * @param $entity
     * @param $fileId
     * @param Importer $importer
     * @return Response
     */
    public function createSyncViewAction(Request $request, $entity, $fileId, Importer $importer) {

        $breadcrumb = $importer->remove_($entity, true);
        return $this->render("import/import_sync.html.twig", [
            'breadcrumb' => $breadcrumb, 'entity'=>$entity, 'file'=>$fileId
        ]);
    }

    /**
     * @Route("/cancel/upload/{entity}/{fileId}/{del}", name="cancel_upload")
     * @param $entity
     * @param $fileId
     * @param int $del
     * @param Importer $importer
     * @return RedirectResponse
     */
    public function cancelUploadAction($entity, $fileId, $del = 2, Importer $importer) {


        //TODO: Add fileId to all those entities that can have upload
        $em = $this->getDoctrine()->getManager();

        if($del == 1) {
            $file = $em->getRepository("App:ImportedFiles")->find($fileId);
            if ($file !== null)
                $em->remove($file);
            $em->flush();
            $this->addFlash("warning", "As you have canceled the process so your uploaded file has been deleted, upload new file if required!");
            return $this->redirectToRoute("import_data", ['entity'=>$entity]);
        }

        $uploadMgr = $em->getRepository("App:UploadManager")->findOneBy(['tableName'=>$entity]);
        if($uploadMgr !== null) {
            $entityClass = $importer->remove_($entity, true);
            $sourceEntity = "App\\Entity\\" . $entityClass;
            if($uploadMgr->getHasTemp())
                $sourceEntity = "App\\Entity\\Temp" . $entityClass;

            $sourceData = $em->getRepository($sourceEntity)->findBy(['file' => $fileId]);

            if ($sourceData !== null) {

                $query = $em->createQuery("Delete from " . $sourceEntity . " temp Where temp.file = " . $fileId);
                $query->execute();

                // Delete the file
                $file = $em->getRepository("App:ImportedFiles")->find($fileId);
                if ($file !== null)
                    $em->remove($file);
                $em->flush();

            }

            $this->addFlash("warning", "As you have canceled the process so your uploaded file has been deleted, upload new file if required!");
            return $this->redirectToRoute("import_data", ['entity'=>$entity]);
        } else
            throw  new FileNotFoundException("You have requested a bad file");
    }


    /**
     * @Route("/do-sync/{entity}/{fileId}", name="sync_entity_data")
     * @param Request $request
     * @param $entity
     * @param $fileId
     * @param Importer $importer
     * @return RedirectResponse|Response
     */
    public function syncDataAction(Request $request, $entity, $fileId, Importer $importer) {

        $em = $this->getDoctrine()->getManager();
        // check the provided entity name in the upload manager
        $uploadMgr = $em->getRepository("App:UploadManager")->findOneBy(['tableName'=>$entity]);

        // if the entity has an uploader activated
        if($uploadMgr !== null) {
            // create symfony slandered entity name from
            // the provided entity that has _
            $entityName = $importer->remove_($entity, true);
            $entityClass = "App\\Entity\\" . $entityName;
            $columns = $importer->mapColumnsToProperties($entityClass, $uploadMgr->getExcludedColumns());
            $sourceData = $em->getRepository("App:UploadManager")
                ->findByFile("Temp".$entityName, $fileId, $columns);
            //EntityName, each entity having temp should have same name as final entity with a Temp prefix.

            $uniqueCols = $uploadMgr->getUniqueColumns();
            $entityCols = $uploadMgr->getEntityColumns();
            //dd($uploadMgr->getUpdateAbleColumns());
            $updateAbleCols = $uploadMgr->getUpdateAbleColumns();

            // to processData function we are not passing mappedArray, because data is already mapped
            // filed id = -1, so no further storage in temp.
            $result = $importer->processData(
                $entityClass, $sourceData, null, -1,
                ['uniqueCols' => $uniqueCols, 'entityCols' => $entityCols, 'updateAbleCols'=>$updateAbleCols],
                null,
                null
            );

            $this->addFlash("success", $result['success']);

            $importer->truncate("temp_".$entity);

            return $this->redirectToRoute("import_data", ['entity'=>$entity]);
        } else
            throw new FileNotFoundException("You have requested a bad file");

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

    private function checkFileData($entity, $excludedCols, $fileId, Importer $importer) {
        $uploadDir = "data_files/";

        $em = $this->getDoctrine()->getManager();

        $file = $em->getRepository('App:ImportedFiles')->find($fileId);

        // path to uploaded file
        $uploadedFile = $uploadDir.$file->getFileName();

        // read all the required fields of entity
        $colsEntity = $importer->toDropDownArray($entity, $excludedCols);

        // return all the information about the file including cols and rows
        $uploadedFileInfo = $importer->readExcel($uploadedFile);

        // clean the columns of the uploaded file
        $colsExcel = $importer->cleanExcelColumns($uploadedFileInfo['columns']);

        // check if the columns were not matching with database or if the columns header were not there.
        if(count($colsExcel) < count($colsEntity)) {

            $this->addFlash("error", "The uploaded file doesn't have all the required information, 
            the file has been deleted. Please upload the correct file");

            if(count($colsExcel) < count($uploadedFileInfo['columns']))
                $this->addFlash('warning', "The uploaded file doesn't have columns headers, or some of the 
                columns headers are incorrect. Please upload the file with correct headers that match the information in
                the database");

            $em->remove($file);
            $em->flush();

            // redirect it to the upload page
            return false;

        } elseif (count($colsExcel) > count($colsEntity))
            $this->addFlash("warning", "The uploaded file have more columns, please map all the columns
            correctly, map the extra columns with Exclude this field");

        $this->addFlash('info', $uploadedFileInfo['rows']." and ".count($colsExcel)." valid columns");
        $data['cols_excel'] = $colsExcel;
        $data['cols_entity'] = $colsEntity;
        $data['excel_data'] = $uploadedFileInfo['data'];


        return $data;

    }


    /**
     * @Route("/download/{entity}/template", name="download_template")
     * @param $entity
     * @param Importer $importer
     * @return mixed
     * @throws
     */
    public function downloadTemplateAction($entity, Importer $importer)
    {
        // ask the service for a Excel5
        $phpExcelObject = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $em = $this->getDoctrine()->getManager();
        $uploadMgr = $em->getRepository("App:UploadManager")->findOneBy(['tableName'=>$entity]);
        $excludedCols = $uploadMgr->getExcludedColumns();

        $table = $importer->remove_($entity, true);

        $columns = $importer->toDropDownArray($table, $excludedCols);
        $cols = array();
        $index = 0;
        foreach ($columns as $name=>$column) {
            $cols[$index] = $name;
            $index ++;
        }

        unset($columns);
        unset($excludedCols);

        $phpExcelObject->getProperties()->setCreator("PolioDB")
            ->setLastModifiedBy("Polio DB Server")
            ->setTitle("Data Upload Template for ".$importer->remove_($entity, true))
            ->setSubject("Upload data using this template")
            ->setKeywords("Microsoft Excel Generated by PHP Office")
            ->setCategory("Template");
        foreach($cols as $key=>$col) {
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($key+1)."1", $col);
        }

        $phpExcelObject->getActiveSheet()->setTitle('template_'.$entity);
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
        //$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // create the response
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "template_".$entity.'.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }



}
