<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;

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
        } catch (Exception $e) {
            echo 'Exception occurred: ', $e->getMessage(), "\n";
        }
        //dd($fileData);
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
                'has_temp' => $uploadMgr->getHasTemp()
            ];
    }


    /**
     * @param $className
     * @param $data
     * @param $mappedArray
     * @param int $fileId
     * @param array $uniqueCols
     * @param array $entityCols
     * @return array|bool|null
     */
    public function processData($className, $data, $mappedArray, $fileId, $uniqueCols = null, $entityCols = null) {

        $readyData = $this->importer->replaceKeys($data, $mappedArray);
        $user = $this->importer->getUser();
        $exceptions = null;
        $batchSize = 50;

        $counter = 0;
        $noRowAdded = 0;
        $noRowUpdated = 0;
        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);

        $types = $this->manager->getClassMetadata($className)->fieldMappings;

        foreach($readyData as $index => $dataRow) {

            //$entity = new $className();
            $entity = null;
            if($fileId === -1) {

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

                // now create the object of target entity
                // first check if the record is already there by criteria
                // we created above
                $entity = $this->manager->getRepository($className)->findOneBy($criteria);
                $noRowUpdated += 1;
            }
            if($entity === null) {
                $entity = new $className();
                $noRowUpdated = $noRowUpdated > 0 ? $noRowUpdated-1 : 0;
                $noRowAdded += 1;
            }

            foreach($dataRow as $col=>$value) {
                $func = "set" . ucfirst($col);
                $dataValue = trim($value) == '' ? null : trim($value);
                $type = in_array(lcfirst($col), $entityCols) === true ? 'integer': $types[$col]['type'];

                if ($type == "integer" || $type == "float" || $type == "double") {
                    if (preg_match("/^-?[0-9]+$/", $dataValue) == false ||
                        preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $dataValue) == false ||
                        !is_numeric($dataValue)
                    )
                        $dataValue = $dataValue === null ? null : 0;
                }

                if($fileId === -1) {
                    $isEntityCol = in_array(lcfirst($col), $entityCols);
                    if ($isEntityCol === true) {
                        // path to that entity
                        if($col === 'hfCode')
                            $col = "bphsHealthFacility";
                        else if($col === 'indicator')
                            $col = 'bphsIndicator';
                        $entityPath = "App\\Entity\\" . ucfirst($col);
                        // get the object of that entity by id of that column (should be id)
                        $entityCol = $this->manager->getRepository($entityPath)->findOneById($dataValue);
                        // so in this case (if the column is entity), update the newdata to be an object
                        $dataValue = $entityCol;
                    }
                }
                $entity->$func($dataValue);

            }

            //setting blameable columns (createdby and updatedby)
            if(method_exists($entity, 'setCreatedBy') && $entity->getCreatedBy() === null) {
                $entity->setCreatedBy($this->manager->getRepository(User::class)->findOneBy(['username'=>$user->getUsername()]));
            }
            if(method_exists($entity, 'setUpdatedBy')) {

                $entity->setUpdatedBy($this->manager->getRepository(User::class)->findOneBy(['username'=>$user->getUsername()]));
            }

            // here setting the hfIndicator column
            $hfIndicator = $this->manager->getRepository('App:BphsHfIndicator')
                                            ->findOneBy(['indicator'=>$entity->getIndicator(),
                                                         'healthFacility'=>$entity->getHfCode(),
                                                         'targetYear'=>$entity->getReportYear()
                                                        ]);
            // as we are sure that this id will be existed (as we already checked).
            $entity->setHfIndicator($hfIndicator);

            // now setting the file id if file was not equal to -1, which means no file field in entity
            if($fileId !== -1)
                $entity->setFile($fileId);

            try {
                $this->manager->persist($entity);
            } catch (Exception $exception) {
                $exceptions[] = "Exception occurred and escaped at row: ".$index. ". Exception: ".$exception;
                continue;
            }

            //todo: update is an issue in the uploader, fixing that!
            if($counter%$batchSize == 0) {
                $this->manager->flush();
                $this->manager->clear();
            }

            $counter ++;
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
        foreach($mainKeys as $mainRow) {
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


}