<?php


namespace App\Service;

class DataManipulation
{
    /**
     * @param array $data  doctrine result - as array
     * @param int $noDefaultIndex number of indices starting from 0 index which could be the common columns e.g. 2
     * @param string $columnIndex 'index name' from the data which will be converted to columns e.g. 'indicator'
     * @param string $matchingIndex  'index name' which should be in the default index and in the main data e.g. 'id'
     * @param array $desiredIndexes
     * e.g. [
        'default' => 'totalReach',
        'default_value1' => 'target'
     ] 'default' is the value of 'columnIndex', value1 is the value of this index e.g. here it's 'target' so the
     * new name could be for example if indicator is Penta1 so the index would be Penta1_target = 'its value'
     *
     * @return array tablized data|wide array
     */
    public function tablizeData($data, $noDefaultIndex, $columnIndex, $matchingIndex, $desiredIndexes)
    {
        if(!is_array($data) || $noDefaultIndex < 1 || !isset($columnIndex) ||
            !isset($matchingIndex) || !is_array($desiredIndexes) )
            return null;

        list($rows, $indicators) = $this->extractColumns($data, $noDefaultIndex, $columnIndex);
        $newData = [];
        foreach ($rows as $row) {
            $tmpNewRow = array();
            foreach ($data as $item) {
                if ($row[$matchingIndex] === $item[$matchingIndex])
                    // this index should always be in the result set/provided array
                {
                    foreach ($indicators as $indicator) {
                        if($indicator === $item[$columnIndex]) {
                            $tmp = array();
                            foreach($desiredIndexes as $key=>$name) {
                                $newIndex = $key;
                                if(strpos($key, 'value') !== false)
                                    $newIndex = $item[$columnIndex]."_".$name;
                                elseif(strpos($key, 'default') !== false)
                                    $newIndex = $item[$columnIndex];
                                $tmp[$newIndex] = $item[$name];
                            }
                            $tmpNewRow = array_merge($tmpNewRow, $tmp);
                            break;
                        }
                    }
                }
            }

            $newData[] = array_merge($row, $tmpNewRow);
        }

        return $newData;
    }

    /**
     * @param $result
     * @param $noDefaultIndex
     * @param $columnIndex
     * @return array
     */
    private function extractColumns($result, $noDefaultIndex, $columnIndex): array
    {
        $rows = [];
        $indicators = [];
        foreach ($result as $item) {
            $rows[] = array_slice($item, 0, $noDefaultIndex);
            $indicators[] = $item[$columnIndex];
        }
        $rows = array_unique($rows, SORT_REGULAR);
        $indicators = array_unique($indicators);
        return array($rows, $indicators);
    }

}