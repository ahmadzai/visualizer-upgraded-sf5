<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 9/1/2017
 * Time: 2:04 PM
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;


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
    /**
     * @var Security
     */
    private $security;
    /**
     * @var FormBuilderInterface
     */
    private $builder;


    function __construct(EntityManagerInterface $_em, Security $security, FormFactoryInterface $builder)
    {
        $this->_em = $_em;
        $this->security = $security;
        $this->builder = $builder;
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


    protected function _serializeEntity($tableName)
    {
        $tableName = $this->tableize($tableName);
        return $this->_em->getConnection()->getSchemaManager()->listTableColumns($tableName);
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

    public function cleanDbColumns($dbColumns) {
        $cleanedCols = array();
        foreach($dbColumns as $column) {
            $cleanedCols[] = $this->remove_($column);
        }

        return $cleanedCols;
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
    public function checkType($var) {
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
                        $row[$this->remove_($value)] = $this->checkType($celValue);
                    }
                }
            }
            $newData[] = $row;
        }

        return $newData;
    }

    /**
     * @param string $tableName
     */
    public function truncate($tableName) {

        $connection = $this->_em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($tableName);
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
        // if mappedArray was null it mean the data is fetched from Temp Table
        $readyData = $mappedArray === null ? $data : $this->replaceKeys($data, $mappedArray);
        $user = $this->getUser();
        $exceptions = null; // for storing exceptions
        // for tracking batch upload, counter for checking batch size
        $batchSize = 1; $counter = 0; $noRowAdded = 0; $noRowUpdated = 0;
        // set logger to null for performance reason
        $this->_em->getConnection()->getConfiguration()->setSQLLogger(null);
        // get the types of all variables
        $types = $this->_em->getClassMetadata($className)->fieldMappings;
        $recordsForUpdate = [];
        foreach($readyData as $index => $dataRow) {

            $isRecordAlreadyExist = false;

            if($fileId === -1) {

                $criteria =$this->createSingleRowCriteria($uniqueCols, $dataRow);
                // now create the object of target entity
                // first check if the record is already there by criteria
                // we created above
                $isRecordAlreadyExist = $this->checkIfRecordAlreadyExist($className,$criteria);
                if($isRecordAlreadyExist) {
                    $recordsForUpdate[] = ['criteria' => $criteria, 'record' => $dataRow];
                }
                $noRowUpdated += $isRecordAlreadyExist ? 1 : 0;
            }

            if($isRecordAlreadyExist !== true) {

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
                    $this->_em->persist($entity);
                } catch (\PhpOffice\PhpSpreadsheet\Exception $exception) {
                    $exceptions[] = "Exception occurred and escaped at row: " . $index . ". Exception: " . $exception;
                    continue;
                }
                if ($counter % $batchSize == 0) {
                    $this->_em->flush();
                    $this->_em->clear();
                }
                $counter++;
            }
        }
        $this->_em->flush();
        $this->_em->clear();

        $noRowUpdated = $this->updateRecords($className, $recordsForUpdate, $updateAbleCols);

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

    public function getUser() {
        return $this->security->getUser();
    }

    public function updateRecords($classPath, $updateData, $colsForUpdate) {

        $tableName = $this->tableize(substr(strrchr($classPath, "\\"), 1));
        $allFields = $this->_em->getConnection()->getSchemaManager()->listTableColumns($tableName);
        $userId = $this->getUser()->getId();
        $noRowsUpdated = 0;

        foreach($updateData as $updateDatum) {

            $criteria = $updateDatum['criteria'];
            $record = $updateDatum['record'];

            $where = [];
            $params = [];
            $counter = 0;
            foreach ($criteria as $column => $value) {
                $condition = $this->tableize($column);
                if (($value === null || $value === ""))
                    $condition .= " IS NULL ";
                else {
                    $param = "param" . $counter;
                    $condition .= " = (:" . $param . ")";
                    $params[$param] = $value;
                }
                $where[] = $condition;
                $counter++;
            }
            $whereCondition = implode(" AND ", $where);
            $updateParts = [];
            $counter = 0;
            $record = array_change_key_case($record, CASE_LOWER); // change array key case for easy comparison
            foreach ($colsForUpdate as $setCol) {
                $updateParts[] = $setCol . " = (:up" . $counter . ") ";
                $params['up' . $counter] = $record[strtolower($this->remove_($setCol))];
                $counter++;
            }
            // check for updated_by and updated_at columns
            if(array_key_exists("updated_by_id", $allFields) || array_key_exists("updated_by", $allFields)) {
                $updateParts[] = array_key_exists("updated_by", $allFields) ?
                    "updated_by = (:usr) " :
                    "updated_by_id = (:usr) ";
                $params['usr'] = $userId;
            }
            if(array_key_exists("updated_at", $allFields)) {
                $updateParts[] = "updated_at = (:dt) ";
                $params['dt'] = date('Y-m-d H:i:s');
            }
            $updatePart = implode(", ", $updateParts);
            $query = "UPDATE ".$tableName." SET " . $updatePart . " WHERE (" . $whereCondition . ")";

            $noRowsUpdated += $this->_em->getConnection()->executeUpdate($query, $params);

        }
        return $noRowsUpdated;
    }

    public function checkIfRecordAlreadyExist($classPath, $selectionCriteria) {
        return $this->_em->getRepository("App:UploadManager")
            ->findOneByCriteria($classPath, $selectionCriteria);
    }

    public function createSingleRowCriteria(?array $uniqueCols, $dataRow)
    {
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

        return $criteria;
    }

    /**
     * @param object $entity
     * @param UserInterface|null $user
     */
    private function checkSetBlameable(object $entity, ?UserInterface $user): object
    {
        if (method_exists($entity, 'setCreatedBy') && $entity->getCreatedBy() === null) {
            $entity->setCreatedBy($this->_em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]));
        }
        if (method_exists($entity, 'setUpdatedBy')) {

            $entity->setUpdatedBy($this->_em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]));
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
        if (is_array($columnSubstitutes) && array_key_exists($col, $columnSubstitutes))
            $col = $columnSubstitutes[$col];

        $entityPath = "App\\Entity\\" . ucfirst($col);
        // get the object of that entity by id of that column (should be id)
        return $this->_em->getRepository($entityPath)->findOneById($dataValue);

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
        $types = array_change_key_case($types, CASE_LOWER);

        $dataValue = trim($value) == '' ? null : trim($value);
        $type = in_array(lcfirst($col), $entityCols) === true ? 'integer' : $types[strtolower($col)]['type'];

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
        if($excludedAssociationArray !== null && count($excludedAssociationArray) > 0) {
            foreach($excludedAssociationArray as $key => $item) {
                // key should be the name of associated variable (all the time it should be same as entity name)
                $selectionCriteria = [];
                // go through the sub array
                foreach ($item as $index => $value) {
                    $getFunc = "get".ucfirst($value); // this should always be same as entity variables
                    $selectionCriteria[$index] = $entity->$getFunc();
                }
                // by this point we should have selection criteria ready
                $tempRecord = $this->_em->getRepository("App:".ucfirst($key))
                    ->findOneBy($selectionCriteria);
                // now set the record that we just fetched
                $setFunc = "set".ucfirst($key); // again this should be name of excluded column (association)
                $entity->$setFunc($tempRecord);
            }
        }

        return $entity;
    }

    /**
     * @param $excelCols
     * @param $entityCols
     * @return FormInterface
     */
    public function createMapperForm($excelCols, $entityCols): FormInterface
    {

        if($excelCols > $entityCols)
            $entityCols['Exclude this field'] = '-1';
        $formBuilder = $this->builder->createBuilder(FormType::class, $excelCols);
        foreach ($excelCols as $index=>$column)
        {
            //$fieldLabel = preg_split("|", $column);
            $formBuilder->add($index, ChoiceType::class, array('label'=> $column, 'choices' => $entityCols,
                'data' => $this->compareColumns($column, $entityCols), 'attr' => ['class'=>'form-control select2',
                    'style'=>"width:100%"]) );
        }
        return $formBuilder->getForm();
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

    public function listTableColumns($tableName) {
        $columns = $this->_serializeEntity($tableName);
        $columnsWithKeys = [];
        foreach ($columns as $col => $dbal) {
            $columnsWithKeys[$this->remove_($col, true)] = $col;
        }

        return $columnsWithKeys;
    }

    public function mapColumnsToProperties(string $entityName, ?array $getExcludedColumns)
    {
        $tempProperties = $this->getEntityProperties($entityName, true);
        $properties = [];
        foreach ($tempProperties as $key => $value) {
            $properties[strtolower($key)] = $key;
        }

        foreach($getExcludedColumns as $column) {
            if(array_key_exists(strtolower($this->remove_($column)), $properties)) {
                unset($properties[strtolower($this->remove_($column))]);
            }
        }

        return $properties;
    }

    protected function getEntityProperties($entity, $name = false)
    {
        $className = $entity;
        if($name === true)
            $entity = new $className;
        $className = get_class($entity);
        $metadata = $this->_em->getClassMetadata($className);
        $data = array();
        foreach ($metadata->fieldMappings as $field => $mapping) {
            $value = $metadata->reflFields[$field]->getValue($entity);
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
            $key = $field;
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

}
