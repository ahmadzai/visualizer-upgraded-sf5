<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class ReachImporter
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Importer
     */
    private $importer;

    private const UPLOAD_DIR = "data_files/";

    public function __construct(EntityManagerInterface $manager, Importer $importer)
    {
        $this->manager = $manager;
        $this->importer = $importer;
    }

    public function readReachData(int $fileId) {
        $fileRecord = $this->manager->getRepository('App:ImportedFiles')->find($fileId);
        $file = self::UPLOAD_DIR.$fileRecord->getFileName();

        try {
            $fileData = $this->importer->readExcel($file);
            if($fileData['data'] && count($fileData['data']) > 0) {

                $unpivotData = $this->unpivotData($fileData);
                $newColumnNames = array_slice($fileData['columns'], 0, 3);
                $newColumnNames['indicator'] = 'Indicator';
                $newColumnNames['reach'] = 'Reach';
                $this->importer->cleanExcelColumns($newColumnNames);
                return [
                    'result' => $unpivotData['result'],
                    'excel_data' => $unpivotData['data'],
                    'cols_excel' => $newColumnNames,
                    'rows' => $fileData['rows']
                ];

            }
        } catch (Exception $e) {
            echo 'Exception occurred: ', $e->getMessage(), "\n";
        }
    }

    public function deleteFile(int $fileId) {

        $file = $this->manager->getRepository('App:ImportedFiles')
                                ->find($fileId);
        if($file !== null) {
            $this->manager->remove($file);
            $this->manager->flush();
        }
    }

    public function getUploaderSettings($entity = null) {
        $entity = $entity ?? 'bphs_indicator_reach';
        $uploadMgr = $this->manager->getRepository('App:UploadManager')
            ->findOneBy(['tableName'=>$entity]);
        $excludedCols = $uploadMgr->getExcludedColumns();
        $entityClass = "App\\Entity\\" . $this->importer->remove_($entity, true);
        $entityObject = new $entityClass();

        return [
                'cols_required' => $this->importer->toDropDownArray($entityObject, $excludedCols),
                'cols_unique' => $uploadMgr->getUniqueColumns(),
                'cols_entity' => $uploadMgr->getEntityColumns(),
                'cols_update_able' => $uploadMgr->getUpdateAbleColumns(),
                'has_temp' => $uploadMgr->getHasTemp()
            ];
    }

    /**
     * @param $className
     * @param $data
     * @param $mappedArray
     * @param int $fileId
     * @param array $entityAndExcludedCols
     * @param array|null $colsSubstitutes
     * @param array|null $excludedAssociatedCols
     * @return array|bool|null
     */
    public function processData($className, $data, $mappedArray, $fileId,
                                ?array $entityAndExcludedCols,
                                ?array $colsSubstitutes,
                                ?array $excludedAssociatedCols)
    {
        $entityCols = $entityAndExcludedCols['entityCols'];
        $uniqueCols = $entityAndExcludedCols['uniqueCols'];
        $updateAbleCols = $entityAndExcludedCols['updateAbleCols'];

        $readyData = $this->importer->replaceKeys($data, $mappedArray);
        $user = $this->importer->getUser();
        $exceptions = null; // for storing exceptions
        // for tracking batch upload, counter for checking batch size
        $batchSize = 1; $counter = 0; $noRowAdded = 0; $noRowUpdated = 0;
        // set logger to null for performance reason
        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);
        // get the types of all variables
        $types = $this->manager->getClassMetadata($className)->fieldMappings;
        foreach($readyData as $index => $dataRow) {

            $entity = null;
            $isUpdate = false;
            if($fileId === -1) {

                $criteria =$this->createSingleRowCriteria($uniqueCols, $dataRow);
                // now create the object of target entity
                // first check if the record is already there by criteria
                // we created above
                $isUpdate = $this->updateRecord($className,$criteria, $updateAbleCols, $dataRow);
                $noRowUpdated += $isUpdate ? 1 : 0;
            }

            if($isUpdate === false) {
                $entity = new $className();
                $noRowAdded += 1;
                foreach ($dataRow as $col => $value) {
                    $func = "set" . ucfirst($col);
                    $dataValue = $this->checkTypeCleanValue($value, $col, $entityCols, $types);

                    if ($fileId === -1) {
                        $isEntityCol = in_array(lcfirst($col), $entityCols);
                        if ($isEntityCol === true) {
                            // fetch the entity
                            $dataValue = $this->fetchAssociatedEntity($col, $dataValue,
                                $colsSubstitutes);
                        }
                    }
                    $entity->$func($dataValue);
                }
                $entity = $this->checkSetBlameable($entity, $user);

                $entity = $this->checkSetExcludedAssociation($entity, $excludedAssociatedCols);

                // now setting the file id if file was not equal to -1, which means no file field in entity
                if ($fileId !== -1)
                    $entity->setFile($fileId);

                try {
                    $this->manager->persist($entity);
                } catch (Exception $exception) {
                    $exceptions[] = "Exception occurred and escaped at row: " . $index . ". Exception: " . $exception;
                    continue;
                }
                if ($counter % $batchSize == 0) {
                    $this->manager->flush();
                    $this->manager->clear();
                }
                $counter++;
            }
        }

        $this->manager->flush();
        $this->manager->clear();

        $result = array();
        if($exceptions == null)
            $result['success'] = $noRowAdded." new rows inserted and ".$noRowUpdated." rows updated!";
        else
            $result['error'] = $exceptions;

        return $result;

    }

    private function unpivotData($fileData)
    {

        $mainKeys = [];
        $repeatingItems = [];
        foreach ($fileData['data'] as $item) {
            $mainKeys[] = array_slice($item, 0, 3);
            $repeatingItems[] = array_slice($item, 3, null,true);
        }

        // temp solution - B key represents Year, so as second column to below function
        $indicators = $this->replaceNamesToIds($fileData['columns'], $mainKeys[0]['B']);
        // if there's any error (can't find indicators names)
        if($indicators['result'] === 'error') {
            return $indicators;
        }

        $checkResult = $this->checkHealthFacilitiesCodes($mainKeys);
        if($checkResult !== true) {
            return $checkResult;
        }

        // check and change month names to 3 chars months
        $mainRows = [];
        foreach($mainKeys as $key) {
            $month = strlen($key['C']) > 3 ?
                     ucfirst(substr($key['C'], 0, 3)):
                     ucfirst($key['C']);
            $mainRows[] = ['A' => $key['A'], 'B'=>$key['B'], 'C'=>$month];
        }

        // now we have the names converted to ids for the indicators
        // here we have to replace the keys of the repeatingItems array to the ids of the indicators
        // note, the current keys (D, E, F, etc) should be same as the keys of indicators array
        // here we need triple foreach loops

        $dataArray = []; // this is the unpivotred array
        $tempArray = [];
        foreach ($repeatingItems as $items) {
            $newRow = [];
            foreach ($items as $key => $item) {
                $newRow[$indicators['data'][$key]] =  $item;
            }
            $tempArray[] = $newRow;
        }

        // now unpivot and mix the two arrays
        $indicatorRowIndex = 0; // variable for changing/tracking the $tempArray index (which has the indicators and reach
        foreach($mainRows as $mainRow) {
            $break = 0;         // for breaking the while loop, so it should not repeat for all rows for a single main row
            while ($indicatorRowIndex < count($tempArray)) {

                $indicatorReach = $tempArray[$indicatorRowIndex];
                //dump($indicatorReach);
                foreach($indicatorReach as $ind=>$reach) {

                    //dump($reach);
                    $dataArray[] = array_merge($mainRow, ['indicator'=>$ind, 'reach'=>$reach]);

                    $break += 1;
                }
                $indicatorRowIndex ++;
                if($break === count($indicatorReach))
                    break;

            }
            //dump($indicatorRowIndex);
        }

        return ['result'=>'success', 'data' => $dataArray];

    }

    private function replaceNamesToIds($columns, $year)
    {
        $indicatorNames = array_slice($columns, 3, null, true);
        $dbIndicators = $this->manager->getRepository('App:BphsHfIndicator')
                                                    ->findIndicators($indicatorNames, $year);
        if(count($indicatorNames) !== count($dbIndicators)) {
            $namesNotFound = [];
            foreach ($indicatorNames as $name) {
                if(array_search($name, array_column($dbIndicators, 'indicatorName')) === false)
                    $namesNotFound[] = $name;
            }
            return ['result' => 'error', 'data' => 'Indicators '.implode(", ", $namesNotFound).' for year: '.
                $year.' not exist, please check the spelling or upload correct file. The file has been deleted'];
        }

        $resultArray = [];
        foreach($indicatorNames as $key=>$name) {
            $index = array_search($name, array_column($dbIndicators, 'indicatorName'));
            $resultArray[$key] = $dbIndicators[$index]['id'];
        }

        return ['result' => 'success', 'data' => $resultArray];
    }

    private function checkHealthFacilitiesCodes($data) {
        // we have to be sure that the uploaded file always have health facility code as first column
        $hfCodes = [];
        foreach ($data as $item)
            $hfCodes[] = $item['A'];
        $hfCodes = array_unique($hfCodes);
        $year = $data[0]['B']; // always one year data should be uploaded.
        $dbFacilities = $this->manager->getRepository('App:BphsHfIndicator')
            ->findHealthFacilities($hfCodes, $year);

        if(count($dbFacilities) !== count($hfCodes)) {
            $namesNotFound = [];
            foreach ($hfCodes as $code) {
                if(array_search($code, array_column($dbFacilities, 'id')) === false)
                    $namesNotFound[] = $code;
            }
            $message = 'HF Codes '.implode(", ", $namesNotFound).' for year: '.
                        $year.' not exist, please link the indicators with those 
                        facilities or write facilities codes correctly. The file has been deleted.';
            return ['result' => 'error', 'data' => $message];
        }

        return true;
    }

    public function updateRecord($classPath, $selectionCriteria, $colsForUpdate, $updates) {
        $entityRecord = $this->manager->getRepository($classPath)->findOneBy($selectionCriteria);
        if($entityRecord) {
            $anyUpdates = false;
            foreach($colsForUpdate as $col) {
                $getFunc = "get".ucfirst($col);
                if($entityRecord->$getFunc() !== $updates[$col]) {
                    $anyUpdates = true;
                    $setFunc = "set".ucfirst($col);
                    $entityRecord->$setFunc($updates[$col]);
                }
            }
            if($anyUpdates) {
                // update the user information
                //$this->checkSetBlameable($entityRecord, $this->importer->getUser());
                $this->manager->flush();
                $this->manager->clear();
            }

            return true;
        }
        return false;
    }

    public function createSingleRowCriteria(?array $uniqueCols, $dataRow)
    {
        $criteria = array();
        // make a criteria from the columns set in upload manager to
        // define a row as a unique
        // loop over those columns
        foreach ($uniqueCols as $uniqueCol) {
            $uniqueCol = $this->importer->remove_($uniqueCol, true);
            // prepare the get function of those columns
            // initialize the array ['columnName'] = value (value from the data)
            $datum = trim($dataRow[lcfirst($uniqueCol)]);
            if(strpos(strtolower($uniqueCol), 'date') !== false) {
                $datum = \DateTimeImmutable::createFromFormat('Y-m-d', $datum);
            }
            $criteria[lcfirst($uniqueCol)] = $datum;

        }

        return $criteria;
    }

    /**
     * @param object $entity
     * @param UserInterface|null $user
     */
    private function checkSetBlameable(object $entity, ?UserInterface $user): object
    {
        if (method_exists($entity, 'setCreatedBy') && $entity->getCreatedBy() === null) {
            $entity->setCreatedBy($this->manager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]));
        }
        if (method_exists($entity, 'setUpdatedBy')) {

            $entity->setUpdatedBy($this->manager->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]));
        }

        return $entity;
    }

    /**
     * @param $col
     * @param $dataValue
     * @param $columnSubstitutes
     * @return object
     */
    private function fetchAssociatedEntity($col, $dataValue, $columnSubstitutes): object
    {
        if (array_key_exists($col, $columnSubstitutes))
            $col = $columnSubstitutes[$col];

        $entityPath = "App\\Entity\\" . ucfirst($col);
        // get the object of that entity by id of that column (should be id)
        return $this->manager->getRepository($entityPath)->findOneById($dataValue);

    }

    /**
     * @param $value
     * @param $col
     * @param array|null $entityCols
     * @param array|null $types
     * @return int|string|null
     */
    public function checkTypeCleanValue($value, $col, ?array $entityCols, ?array $types)
    {
        $dataValue = trim($value) == '' ? null : trim($value);
        $type = in_array(lcfirst($col), $entityCols) === true ? 'integer' : $types[$col]['type'];;

        if ($type == "integer" || $type == "float" || $type == "double") {
            if (preg_match("/^-?[0-9]+$/", $dataValue) == false ||
                preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $dataValue) == false ||
                !is_numeric($dataValue)
            )
                $dataValue = $dataValue === null ? null : 0;
        }
        return $dataValue;
    }

    /**
     * @param object $entity
     * @param $excludedAssociationArray
     * @return object
     */
    public function checkSetExcludedAssociation(object $entity, $excludedAssociationArray): object
    {
        // here setting the excluded association columns (selection done as per the array provided)
        // the array should always be in the below template:
        /**
         ['nameOfTheAssociatedColumn' =>                                  // this name should be similar to entity name
                ['columnInAssociatedEntity' => 'columnInCurrentEntity']   // this is for the selection criteria
         ]
         */
        if(count($excludedAssociationArray) > 0) {
            foreach($excludedAssociationArray as $key => $item) {
                // key should be the name of associated variable (all the time it should be same as entity name)
                $selectionCriteria = [];
                // go through the sub array
                foreach ($item as $index => $value) {
                    $getFunc = "get".ucfirst($value); // this should always be same as entity variables
                    $selectionCriteria[$index] = $entity->$getFunc();
                }
                // by this point we should have selection criteria ready
                $tempRecord = $this->manager->getRepository("App:".ucfirst($key))
                    ->findOneBy($selectionCriteria);
                // now set the record that we just fetched
                $setFunc = "set".ucfirst($key); // again this should be name of excluded column (association)
                $entity->$setFunc($tempRecord);
            }
        }

        return $entity;
    }


}