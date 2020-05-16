<?php
namespace App\Service;
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 11/12/2016
 * Time: 6:44 PM
 */

/**
 * Class Triangle
 * @package App\Service
 * To be used for data triangulation
 */
class Triangle
{

    /**
     * @param $arrays
     * @param string $commonIndex
     * @return array
     */
    public static function triangulate($arrays, $commonIndex) {
        $arrObj = new \ArrayObject($arrays[0]);
        $resultArray = $arrObj->getArrayCopy();

        foreach ($resultArray as $index=>$sourceValue) {

            for($i = 1; $i < count($arrays); $i++ ) {

                foreach ($arrays[$i]  as $array) {
                    if($sourceValue[$commonIndex] === $array[$commonIndex]) {
                        $resultArray[$index] = array_merge($resultArray[$index], $array);
                    } else {
                        $temp = array();
                        foreach($array as $key=>$value)
                            $temp[$key] = null;
                        $resultArray[$index] = array_merge($temp, $resultArray[$index]);
                    }
                }

            }
        }
        return $resultArray;
    }

    /**
     * Source Array as first index, further arrays in this format:
     * array(data=>array, indexes=>array, prefix=>string), where indexes is the array of
     * indexes that should be added to the source array in case there was a match
     * And prefix will rename the indexes with this prefix if it was set.
     * @param $arrays (sourceArray, [data=>Array, indexes=>Array])
     * @param $commonIndex
     * @return array
     */
    public static function triangulateCustom($arrays, $commonIndex) {

        $sourceArray = $arrays[0];

        $arrObj = new \ArrayObject($sourceArray);

        $resultArray = $arrObj->getArrayCopy();

        for($i = 1; $i < count($arrays); $i++ ) {
            $data = isset($arrays[$i]['data'])?$arrays[$i]['data']:null;
            $indexes = isset($arrays[$i]['indexes'])?$arrays[$i]['indexes']:null;
            $prefix = isset($arrays[$i]['prefix'])?$arrays[$i]['prefix']:false;
            $notFound = array();
            // if the second array was bigger than 0
            if(count($data) > 0) {
                foreach ($resultArray as $sourceIndex => $sourceValue) {
                    $flagFound = false;
                    foreach ($data as $array) {

                        if ($sourceValue[$commonIndex] === $array[$commonIndex]) {
                            $flagFound = true;
                            if ($indexes === 'all') {
                                foreach ($array as $key => $value) {
                                    $newIndex = $prefix === false ? $key : $prefix . $key;
                                    $resultArray[$sourceIndex][$newIndex] = $value;
                                }
                            } else {
                                foreach ($indexes as $index) {
                                    foreach ($array as $key => $value) {
                                        if ($key == $index) {
                                            $newIndex = $prefix === false ? $index : $prefix . $index;
                                            $resultArray[$sourceIndex][$newIndex] = $value;
                                            break;
                                        }
                                    }
                                }
                            }
                            break;
                        }

                    }

//                    dump($resultArray);
//                    die;

                    if (!$flagFound)
                        $notFound[] = $sourceIndex;
                }
                // check if some row were not matching
                if (count($notFound) > 0) {
                    foreach ($notFound as $item) {
                        if ($indexes === 'all') {
                            foreach ($data[0] as $key => $value) {
                                $newIndex = $prefix === false ? $key : $prefix . $key;
                                $resultArray[$item][$newIndex] = null;
                            }
                        } else {
                            foreach ($indexes as $index) {
                                $newIndex = $prefix === false ? $index : $prefix . $index;
                                $resultArray[$item][$newIndex] = null;
                            }
                        }
                    }
                }
            } else { // if the second array count was 0
                foreach ($resultArray as $sourceIndex => $sourceValue) {
                    if(count($indexes) > 0 && $indexes !== 'all') {
                        foreach ($indexes as $index) {
                            $newIndex = $prefix === false ? $index : $prefix . $index;
                            $resultArray[$sourceIndex][$newIndex] = null;
                        }
                    } else {
                        $resultArray[$sourceIndex]['noCatchup'] = true;
                    }
                }
            }
        }

        return $resultArray;

    }


    /**
     * @param array $data
     * @param array $columns (firstCol is the first operand always)
     * @param string $operation (+, -, /)
     * @param string $newCol (indexName to store new info)
     * @param string $checkCol (name of the column to check first before operation)
     * @return array
     */
    public static function mathOps(array $data, array $columns, $operation, $newCol, $checkCol = 'colName') {
        $resultArray = $data;
        foreach($resultArray as $key=>$item) {
            // the first column should be the first operand
            $firstOperand = $item[$columns[0]];
            $secondOperand = 0;
            for($i=1; $i<count($columns); $i++) {
                $secondOperand += $item[$columns[$i]];
            }

            if($operation === '+') {
                $resultArray[$key][$newCol] = $firstOperand + $secondOperand;
            } elseif ($operation === '-') {

                if($checkCol !== 'colName' && ( $item[$checkCol] === null || $item[$checkCol] == 0)) {
                    $resultArray[$key][$newCol] = null;
                } else {
                    $resultArray[$key][$newCol] = ($firstOperand - $secondOperand) < 0 ? 0 :
                                                            ($firstOperand - $secondOperand);
                }
            } elseif ($operation === '/') {
                if($secondOperand !== 0)
                    $resultArray[$key][$newCol] = round(($firstOperand / $secondOperand), 2);
                else
                    $resultArray[$key][$newCol] = 0;
            } elseif($operation === '%') {
                if($secondOperand !== 0)
                    $resultArray[$key][$newCol] = round(($firstOperand / $secondOperand) * 100, 2);
                else
                    $resultArray[$key][$newCol] = 0;
            } elseif($operation === '*') {
                $resultArray[$key][$newCol] = round(($firstOperand * $secondOperand), 0);
            }
        }
        return $resultArray;
    }

    /**
     * @param $indicator
     * @return string
     */
    public static function trIndicators($indicator) {

        $nIndicator['cIndicator'] = 'cTotalRecovered';
        $nIndicator['fIndicator'] = 'fTotalRemaining';
        switch ($indicator) {
            case "RemAbsent":
            case "RemAbsentPer":
                $nIndicator['cIndicator'] = 'cVacAbsent';
                $nIndicator['fIndicator'] = 'fTotalAbsent';
                break;
            case "RemNSS":
            case "RemNSSPer":
                $nIndicator['cIndicator'] = 'cVacNSS';
                $nIndicator['fIndicator'] = 'fTotalNSS';
                break;
            case "RemRefusal":
            case "RemRefusalPer":
                $nIndicator['cIndicator'] = 'cVacRefusal';
                $nIndicator['fIndicator'] = 'fTotalRefusal';
                break;
        }

        return $nIndicator;
    }

}
