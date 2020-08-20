<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BphsReachIndicator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var DataManipulation
     */
    private $manipulator;

    public function __construct(EntityManagerInterface $entityManager, DataManipulation $manipulator)
    {
        $this->entityManager = $entityManager;
        $this->manipulator = $manipulator;
    }

    public function colsReachByLocation($data, $noItemsIgnoreFromBeginning, $noLocCols, $topLevelColSpan = 4)
    {
        $array = array_slice($data, $noItemsIgnoreFromBeginning);
        $array = array_keys($array);

        $columnsArray = [];
        $indicators = [];
        foreach ($array as $item) {
            $row = array();
            $row['col'] = $item;
            $row['calc'] = 'none';
            if(strpos($item, 'province') !== false) {
                $row['label'] = 'Province';
            }
            else if(strpos($item, 'district') !== false) {
                $row['label'] = 'District';
            }
            else if(strpos($item, 'facility') !== false) {
                $row['label'] = 'Facility';
            }
            else if(strpos($item, 'indicator') !== false) {
                $row['label'] = 'Indicator';
            }
            else if(strpos($item, 'target') !== false) {
                $row['label'] = 'Target';
            }
            else if(strpos($item, 'monthlyTarget') !== false) {
                $row['label'] = 'Target';
            }
            else if(strpos($item, 'currentProgress') !== false) {
                $row['label'] = 'Curr Coverage';
                $row['calc'] = 'normal';
            }
            else if(strpos($item, 'overallProgress') !== false) {
                $row['label'] = 'Overall Progress';
                $row['calc'] = 'depend';
                $row['dependency'] = $this->getDependent($item);
            }
            else if(strpos($item, 'noMonthsReported') !== false) {
                $row['label'] = 'noMonthsReported';
                $row['calc'] = 'none';
                $row['hidden'] = true;
            }
            else {
                $indicators[] = $item;
                $row['label'] = 'Cum Reach';
                $row['calc'] = 'none';
            }
            $columnsArray[] = $row;
        }

        $topLevelCols = [];
        foreach ($indicators as $indicator) {
            $topLevelCols[] = ['col' => $indicator, 'colspan' => $topLevelColSpan];
        }

        $rowsSpan = [];
        for($i = 0; $i < $noLocCols; $i++) {
            //$rowsSpan[] = ['col' => $columnsArray[$i]['label'], 'colspan'=>1];
            $rowsSpan[] = ['col' => '', 'colspan'=>1];
        }
        $topLevelCols = array_merge($rowsSpan, $topLevelCols);

        return [$columnsArray, $topLevelCols];
    }

    public function tableColsIndicatorReach($params) {
        $yearMonth = $params['yearMonth']; $province = $params['province'];
        $district = $params['district']; $facility = $params['facility'];
        $cumulative = $params['isCumulative'];

        $result = $this->entityManager->getRepository('App:BphsIndicatorReach')
            ->getReachByMonthsFacilities($yearMonth, $province, $district, $facility);
        $desiredCols = [
            'default' => 'totalReach',
            'default_value1' => 'target',
            'default_value2' => 'currentProgress',
            'default_value3' => 'overallProgress',
            'default_value4' => 'noMonthsReported'
        ];
        $noLocCols = $this->findNoOfLocationColumns($district, $facility);
        $newData = $this->manipulator->tablizeData($result, $noLocCols, 'indicator', 'id', $desiredCols);

        list($cols, $topCols) = $this->colsReachByLocation($newData[0], 1, $noLocCols-1);

        //$cols = array_slice($cols, 3);
        return [
                HtmlTable::tableMultiHeaders($newData, $cols, $topCols),
                $noLocCols-1
            ];

    }

    public function tableColsMonthReach($params) {
        $yearMonth = $params['yearMonth']; $province = $params['province'];
        $district = $params['district']; $facility = $params['facility'];

        $result = $this->entityManager->getRepository('App:BphsIndicatorReach')
            ->getReachByMonths($yearMonth, $province, $district, $facility);
        //dd($result);
        $desiredCols = [
            'default' => 'totalReach',
            'default_value1' => 'monthlyTarget',
            'default_value2' => 'currentProgress',
        ];
        $noLocCols = $this->findNoOfLocationColumns($district, $facility);
        $newData = $this->manipulator->tablizeData($result, $noLocCols+1, 'yearMonth', 'id', $desiredCols);
        //dd($newData);
        list($cols, $topCols) = $this->colsReachByLocation($newData[0], 1, $noLocCols, 3);
        //$cols = array_slice($cols, 3);
        return [
            HtmlTable::tableMultiHeaders($newData, $cols, $topCols),
            $noLocCols
        ];

    }

    /**
     * @param $item
     * @return false|string
     */
    private function getDependent($item)
    {
        $explodedString = explode("_", $item);
        if($explodedString !== false && count($explodedString) > 0) {
            return $explodedString[0]."_noMonthsReported";
        }
        return false;
    }

    private function findNoOfLocationColumns($district = null, $facility = null) : int
    {
        $noCols = 2;
        if($facility !== null)
            $noCols = 4;
        else if ($district !== null)
            $noCols = 3;

        return $noCols;
    }

}