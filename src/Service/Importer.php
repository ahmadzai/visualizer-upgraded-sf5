<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 9/1/2017
 * Time: 2:04 PM
 */

namespace App\Service;

use App\Entity\ImportedFiles;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use RuntimeException;


class Importer
{

    /**
     * @var EntityManagerInterface
     */
    protected $_em;
    /**
     * @var int
     */
    protected $_recursionDepth = 0;
    /**
     * @var int
     */
    protected $_maxRecursionDepth = 0;

    //protected $entity;

    function __construct(EntityManagerInterface $_em)
    {
        $this->_em = $_em;
    }

    /**
     * @param array|null $except
     * @param bool $allowTemp
     * @return array
     */
    public function listAllTables($except = null, $allowTemp = false)
    {
        $tables = $this->_em->getConnection()->getSchemaManager()->listTableNames();
        $data = array();
        foreach ($tables as $table) {

            $name = $table; //->getName();
            // remove the doctrine migration table
            if(strpos($name, "migration") === false)
                $data[$name] = $name;
        }

        // remove the excluded tables
        if($except !== null and is_array($except)) {
            foreach ($except as $item) {

                if(array_key_exists($item, $data))
                    unset($data[$item]);
            }
        }

        // remove the temp tables
        if($allowTemp === false) {
            foreach ($data as $datum) {
                $tempPos = strpos($datum, "temp");
                if($tempPos !== false)
                    unset($data[$datum]);
            }
        }

        return $data;
    }

    /**
     * @param $entity
     * @param string $entityBundle
     * @return array
     */
    public function listColumns($entity, $entityBundle = "App\\Entity\\") {
        $entity = $this->remove_($entity, true);
        $entityName = $entityBundle.$entity;

        return $this->toDropDownArray(new $entityName());
    }

    protected function _serializeEntity($entity, $name = false)
    {
        $className = $entity;
        if($name === true)
            $entity = new $className;
        if($name === false)
            $className = get_class($entity);
        $metadata = $this->_em->getClassMetadata($className);
        $data = array();
        foreach ($metadata->fieldMappings as $field => $mapping) {
            $value = $metadata->reflFields[$field]->getValue($entity);
            $field = $this->tableize($field);
            if ($value instanceof \DateTime) {
                // We cast DateTime to array to keep consistency with array result
                $data[$field] = (array)$value;
            } elseif (is_object($value)) {
                $data[$field] = (string)$value;
            } else {
                $data[$field] = $value;
            }
        }
        foreach ($metadata->associationMappings as $field => $mapping) {
            $key = $this->tableize($field);
            if ($mapping['isCascadeDetach']) {
                $data[$key] = $metadata->reflFields[$field]->getValue($entity);
                if (null !== $data[$key]) {
                    $data[$key] = $this->_serializeEntity($data[$key]);
                }
            } elseif ($mapping['isOwningSide'] && $mapping['type'] & ClassMetadata::TO_ONE) {
                if (null !== $metadata->reflFields[$field]->getValue($entity)) {
                    if ($this->_recursionDepth < $this->_maxRecursionDepth) {
                        $this->_recursionDepth++;
                        $data[$key] = $this->_serializeEntity(
                            $metadata->reflFields[$field]
                                ->getValue($entity)
                        );
                        $this->_recursionDepth--;
                    } else {
                        $data[$key] = $this->_em
                            ->getUnitOfWork()
                            ->getEntityIdentifier(
                                $metadata->reflFields[$field]
                                    ->getValue($entity)
                            );
                    }
                } else {
                    // In some case the relationship may not exist, but we want
                    // to know about it
                    $data[$key] = null;
                }
            }
        }
        return $data;
    }

    /**
     * Serialize an entity to an array
     *
     * @param string entity $entity
     * @param array excluded columns $excludedColumns
     * @return array
     */
    public function toArray($entity, $excludedColumns = null)
    {
        $data = $this->_serializeEntity($entity);

        if($excludedColumns !== null) {
            foreach ($excludedColumns as $col) {
                unset($data[$col]);
            }
        }
        return $data;
    }

    /**
     * @param $entity
     * @param null $excludedColumns
     * @return array
     */
    public function toDropDownArray($entity, $excludedColumns = null)
    {
        $entityCols = $this->toArray($entity, $excludedColumns);
        $entityDropDown = array();
        foreach(array_keys($entityCols) as $key) {
            $entityDropDown[ucwords(str_replace("_", " ", $key))] = $key;
        }

        return $entityDropDown;
    }

    /**
     * @param $excelCols
     * @return array
     */
    public function cleanExcelColumns($excelCols)
    {
        $readableCols = array();
        foreach ($excelCols as $key=>$value) {
            if($value !== null and (!ctype_digit($value) or !is_numeric($value))) {
                $readable = preg_replace('/[^A-Za-z0-9\ -]/', '', $value);
                $readable = ucfirst($readable);
                $readable = preg_replace('/\\s+/', '-', $readable);
                //$optionValue = preg_replace("/[^A-Za-z0-9]/", "", $value);
                $readableCols[$key] = $readable;
            }
        }

        return $readableCols;
    }


    /**
     * Convert an entity to a JSON object
     *
     * @param string entity $entity
     * @param array excluded columns $excludedColumns
     * @return string
     */
    public function toJson($entity, $excludedColumns = null)
    {
        $data = $this->toArray($entity, $excludedColumns);
        return json_encode($data);
    }

    /**
     * @param string path to Excel file $excel
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function readExcel($excel)
    {

        $excelWb = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel);

        $highestRow = $excelWb->getActiveSheet()->getHighestRow();
        $highestCol = $excelWb->getActiveSheet()->getHighestColumn();

        $headers = $excelWb->getActiveSheet()
            ->rangeToArray(
                'A1:' . $highestCol . '1',
                null,
                false,
                false,
                true
            );
        $data['rows'] = "There're " . $highestRow . " rows including header-row in your file";
        $data['columns'] = $headers[1];


        $excelData = $excelWb->getActiveSheet()->rangeToArray(
            "A2:" . $highestCol . $highestRow,
            null,
            false,
            true,
            true
        );

        $data['data'] = $excelData;

        return $data;
    }

    /**
     * @param $var
     * @return int|null|string
     */
    public function chekType($var) {
        if($var === null)
            return null;
        $var = trim($var);
        if(is_numeric($var))
            $var = (int) $var;
        return $var;
    }

    /**
     * @param $text
     * @param bool $capitalize
     * @return mixed|string
     */
    public function remove_($text, $capitalize = false) {
        if($capitalize === false) {
            if (strpos($text, "_") === false)
                return $text;
            else {
                $text = str_replace("_", " ", $text);
                $text = ucwords($text);
                //return $text;
                return lcfirst(preg_replace("/\\s+/", "", $text));
            }
        } else if($capitalize === true) {
            if (strpos($text, "_") === false)
                return ucfirst($text);
            else {
                $text = str_replace("_", " ", $text);
                $text = ucwords($text);
                //return $text;
                return preg_replace("/\\s+/", "", $text);
            }
        }
    }

    /**
     * @param $excelData
     * @param $mappingKeys
     * @return array
     */
    public function replaceKeys($excelData, $mappingKeys) {
        $newData = array();

        foreach($excelData as $excelRows) {
            $row = array();
            foreach ($excelRows as $excelCol=>$celValue) {
                foreach ($mappingKeys as $key=>$value) {
                    if($excelCol == $key) {
                        $row[$this->remove_($value)] = $this->chekType($celValue);
                    }
                }
            }
            $newData[] = $row;
        }

        return $newData;
    }

    /**
     * @param $className
     */
    public function truncate($className) {
        $className = get_class($className);
        $classMetaData = $this->_em->getClassMetadata($className);
        $connection = $this->_em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollback();
        }
    }

    /**
     * @param $className
     * @param $data
     * @param $mappedArray
     * @param ImportedFiles $fileId
     * @param array $uniqueCols
     * @param array $entityCols
     * @return array|bool|null
     */
    public function processData($className, $data, $mappedArray, $fileId, $uniqueCols = null, $entityCols = null) {

        $readyData = $this->replaceKeys($data, $mappedArray);

        $exceptions = null;
        $batchSize = 50;

        $counter = 0;
        $noRowAdded = 0;
        $noRowUpdated = 0;
        $this->_em->getConnection()->getConfiguration()->setSQLLogger(null);

        $types = $this->_em->getClassMetadata($className)->fieldMappings;

        foreach($readyData as $index => $dataRow) {

            //$entity = new $className();
            $entity = null;
            if($fileId === -1) {

                $criteria = array();
                // make a criteria from the columns set in upload manager to
                // define a row as a unique
                // loop over those columns
                foreach ($uniqueCols as $uniqueCol) {
                    $uniqueCol = $this->remove_($uniqueCol, true);
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
                $entity = $this->_em->getRepository($className)->findOneBy($criteria);
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
                        $entityPath = "App\\Entity\\" . ucfirst($col);
                        // get the object of that entity by id of that column (should be id)
                        $entityCol = $this->_em->getRepository($entityPath)->findOneById($dataValue);
                        // so in this case (if the column is entity), update the newdata to be an object
                        $dataValue = $entityCol;
                    }
                }

                $entity->$func($dataValue);

            }

            // now setting the file id if file was not equal to -1, which means no file field in entity
            if($fileId !== -1)
                $entity->setFile($fileId);

            try {
                $this->_em->persist($entity);
            } catch (Exception $exception) {
                $exceptions[] = "Exception occurred and escaped at row: ".$index. ". Exception: ".$exception;
                continue;
            }

            if($counter%$batchSize == 0) {
                $this->_em->flush();
                $this->_em->clear();
            }

            $counter ++;
        }

        $this->_em->flush();
        $this->_em->clear();

        $result = array();
        if($exceptions == null)
            $result['success'] = $noRowAdded." new rows inserted and ".$noRowUpdated." rows updated!";
        else
            $result['error'] = $exceptions;

        return $result;


    }

    /**
     * Converts a word into the format for a Doctrine table name. Converts 'ModelName' to 'model_name'.
     * Copied from Inflector class, just for the simplicity, no need for Inflector object.
     */
    protected function tableize(string $word) : string
    {
        $tableized = preg_replace('~(?<=\\w)([A-Z])~u', '_$1', $word);

        if ($tableized === null) {
            throw new RuntimeException(sprintf(
                'preg_replace returned null for value "%s"',
                $word
            ));
        }

        return mb_strtolower($tableized);
    }




}
