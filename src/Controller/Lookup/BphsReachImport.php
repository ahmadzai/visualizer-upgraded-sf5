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
use App\Service\ReachImporter;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Mapping\Entity;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
class BphsReachImport extends AbstractController
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
            //Todo: Here we need to do the following steps before going to the normal upload flow
            // 1. Extract indicators names from the uploaded file - and confirm them - if not cancel the process
            // 2. replace the indicators names with their respective ids and unpivot the data
            //    So for each indicator (and HF, Year, Month) there should be one row
            // 3. Once that data got ready, modify the processData function to receive a data array and insert it
            //    to the table.

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
            $form = $this->createMapperForm($result['cols_excel'], $uploaderSettings['cols_required']);
            // when user click map button (submit)
            if ($request->getMethod() == "POST") {
                $form->handleRequest($request);
                if ($form->isValid()) {

                    $mappedArray = $form->getData();
                    $excelData = $result['excel_data'];
                    $flashMessage = "";
                    $file_id = -1;
                    $table = 'table';
                    if ($uploaderSettings['has_temp'] === true) {
                        $entityClass = "\\App\\Entity\\Temp" . $orgImporter->remove_($entity, true);
                        $flashMessage = ", please synchronize it with main table!";
                        $file_id = $fileId;
                        $table = 'temporary table';
                    }

                    // get entity and unique cols
                    $uniqueCols = $uploaderSettings['cols_unique'];
                    $entityCols = $orgImporter->cleanDbColumns($uploaderSettings['cols_entity']);
                    $result = $importer->processData($entityClass, $excelData, $mappedArray,
                        $file_id, $uniqueCols, $entityCols);

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
                        // set the columns in session that are required for sync function
                        $session = $request->getSession();
                        $session->set("requiredCols", $mappedArray);
                        $session->set("uniqueCols", $uniqueCols);
                        $session->set("entityCols", $entityCols);
                        return $this->redirectToRoute("sync_bphs_reach_data",
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
     * @Route("/bphs/reach/sync/{entity}/{fileId}", name="sync_bphs_reach_view")
     * @param Request $request
     * @param $entity
     * @param $fileId
     * @param Importer $importer
     * @return Response
     */
    public function createSyncViewAction(Request $request, $entity, $fileId, Importer $importer) {

//        $referrer = $request->headers->get("referer");
//        $match = "(import)(".$entity.")(".$fileId.")(handle)";
//        if(!preg_match("/$match/", $referrer))
//            throw $this->createNotFoundException("You can't access this route directly!");
        $breadcrumb = $importer->remove_($entity, true);
        return $this->render("bphs_plus/import/import_sync.html.twig", [
            'breadcrumb' => $breadcrumb, 'entity'=>$entity, 'file'=>$fileId
        ]);
    }

    /**
     * @Route("/cancel/bphs/reach/upload/{entity}/{fileId}/{del}", name="cancel_bphs_reach_upload")
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
     * @Route("/do-sync/{entity}/{fileId}", name="sync_bphs_reach_data")
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
            // create path to the entity (normally every uploading entity will have
            // another entity with the same name prefixed by Temp
            $sourceEntity = "App\\Entity\\Temp" . $entityName;

            // get the data from the Temp entity by fileId (the recent file uploaded)
            $sourceData = $em->getRepository($sourceEntity)->findBy(['file' => $fileId]);

            // again just for the caution, if there was any data
            if ($sourceData !== null) {
                //$sourceEntity = new $sourceEntity();
                // now create a path to the target entity
                $targetEntity = "App\\Entity\\" . $entityName;

                // session to receive the variables set in another controller
                $session = $request->getSession();
                $columns = $session->get("requiredCols");
                $uniqueCols = $session->get("uniqueCols");
                $entityCols = $session->get("entityCols");

                // set batch size for cleaning the entity manager time by time
                $batchSize = 50;
                $errors = null; // store exceptions
                $updated = 0;   // variable to keep track of the updated rows
                $inserted = 0;  // variable to keep track of inserted rows
                // set SQL logger off, for the performance purpose
                $em->getConnection()->getConfiguration()->setSQLLogger(null);
                $counter = 0;   // just for the batch counter

                $user = $this->getUser();
                // loop through the uploaded data to shift to the dest table
                foreach ($sourceData as $index => $data) {

                    $criteria = array();
                    // make a criteria from the columns set in upload manager to
                    // define a row as a unique
                    // loop over those columns
                    foreach ($uniqueCols as $uniqueCol) {
                        $uniqueCol = $importer->remove_($uniqueCol, true);
                        // prepare the get function of those columns
                        $getFuncUniqueCol = "get" . $uniqueCol;
                        // initialize the array ['columnName'] = value (value from the data)
                        $criteria[lcfirst($uniqueCol)] = $data->$getFuncUniqueCol();
                    }

                    // now create the object of target entity
                    // first check if the record is already there by criteria
                    // we created above
                    $tEntity = $em->getRepository($targetEntity)->findOneBy($criteria);
                    $updated += 1;  // we assume that this is an update
                    // check if there is any data, if not then create an empty object
                    if ($tEntity === null) {
                        $tEntity = new $targetEntity();  // create an empty object
                        $updated = $updated > 0 ? $updated-1 : 0;   // decrease the update back
                        $inserted += 1;  // increase the new inserted rows
                    }
                    // loop through the required columns
                    foreach ($columns as $column) {
                        // make an entity field out of the column by removing _
                        $column = $importer->remove_($column, true);
                        $getFunc = "get" . $column;    // make a get function for this field
                        $setFunc = "set" . $column;    // make a set function for this field

                        $newData = $data->$getFunc();  // now get the data from the source data

                        // if this column was an entity column (FK)
                        $isEntityCol = in_array(lcfirst($column), $entityCols);
                        if ($isEntityCol === true) {
                            // path to that entity
                            $entityPath = "App\\Entity\\" . $column;
                            // get the object of that entity by id of that column (should be id)
                            $entityCol = $em->getRepository($entityPath)->findOneById($newData);
                            // so in this case (if the column is entity), update the newdata to be an object
                            $newData = $entityCol;
                        }
                        // now set this new data in the target entity
                        $tEntity->$setFunc($newData);
                        // please handle the user/author information somewhere else.
//                        // add user information.
//                        $userId = $em->getRepository("App:User")->find($user);
//                        $tEntity->setUser($userId);

                    }

                    //setting blameable columns (createdby and updatedby)
                    if(method_exists($tEntity, 'setCreatedBy')) {
                        $tEntity->setCreatedBy($em->getRepository(User::class)->findOneBy(['username'=>$user->getUsername()]));
                    }
                    if(method_exists($tEntity, 'setUpdatedBy')) {

                        $tEntity->setUpdatedBy($em->getRepository(User::class)->findOneBy(['username'=>$user->getUsername()]));
                    }

                    // at this point all the columns would be set, so let's presist it to the db
                    $em->persist($tEntity);
                    // clear the entity manager when this condition matched
                    if (($counter % $batchSize) === 0) {
                        $em->flush();
                        $em->clear();
                    }

                    $counter++;

                }

                $em->flush();
                $em->clear();

                // deleting the uploaded records from Temp table
                // we have to truncate/delete all the records from temp table now

                $query = $em->createQuery("Delete from " . $sourceEntity . " temp Where temp.file = " . $fileId);
                $numDeleted = $query->execute();

                $this->addFlash("success", "In total " . $inserted . " rows inserted and " . $updated . " have updated as they were already existed");

            }

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

    /**
     * @param $excelCols
     * @param $entityCols
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    private function createMapperForm($excelCols, $entityCols) {

        if($excelCols > $entityCols)
            $entityCols['Exclude this field'] = '-1';
        $formBuilder = $this->createFormBuilder($excelCols);
        foreach ($excelCols as $index=>$column)
        {
            //$fieldLabel = preg_split("|", $column);
            $formBuilder->add($index, ChoiceType::class, array('label'=> $column, 'choices' => $entityCols,
                'data' => $this->compareColumns($column, $entityCols), 'attr' => ['class'=>'form-control select2',
                    'style'=>"width:100%"]) );
        }
        $form = $formBuilder->getForm();


        return $form;

    }

    private function compareColumns($col, $cols)
    {
        $prev = 0;
        $key = 0;
        foreach ($cols as $k=>$v) {

            similar_text($col, $v, $per);
            if($per > $prev) {
                $prev = $per;
                $key = $v;
            }

        }

        return $key;

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
