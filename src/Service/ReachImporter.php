<?php


namespace App\Service;

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

        return [
                'cols_required' => $this->importer->toDropDownArray($this->importer->remove_($entity, true), $excludedCols),
                'cols_unique' => $uploadMgr->getUniqueColumns(),
                'cols_entity' => $uploadMgr->getEntityColumns(),
                'cols_update_able' => $uploadMgr->getUpdateAbleColumns(),
                'has_temp' => $uploadMgr->getHasTemp()
            ];
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

}