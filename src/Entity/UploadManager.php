<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 9/7/2017
 * Time: 5:54 PM
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * Class UploadManager
 * @package App\Entity
 * @ORM\Table(name="upload_manager")
 * @ORM\Entity(repositoryClass="App\Repository\UploadManagerRepository")
 */
class UploadManager implements BlameableInterface, TimestampableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tableName;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $excludedColumns;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $entityColumns;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $uniqueColumns;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected $updateAbleColumns;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $hasTemp;


    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     */
    public function getExcludedColumns()
    {
        return $this->excludedColumns;
    }

    /**
     * @param mixed $excludedColumns
     */
    public function setExcludedColumns($excludedColumns)
    {
        $this->excludedColumns = $excludedColumns;
    }

    /**
     * @return mixed
     */
    public function getEntityColumns()
    {
        return $this->entityColumns;
    }

    /**
     * @param mixed $entityColumns
     */
    public function setEntityColumns($entityColumns)
    {
        $this->entityColumns = $entityColumns;
    }

    /**
     * @return mixed
     */
    public function getUniqueColumns()
    {
        return $this->uniqueColumns;
    }

    /**
     * @param mixed $uniqueColumns
     */
    public function setUniqueColumns($uniqueColumns)
    {
        $this->uniqueColumns = $uniqueColumns;
    }

    /**
     * @return mixed
     */
    public function getUpdateAbleColumns()
    {
        return $this->updateAbleColumns;
    }

    /**
     * @param mixed $updateAbleColumns
     */
    public function setUpdateAbleColumns($updateAbleColumns): void
    {
        $this->updateAbleColumns = $updateAbleColumns;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getHasTemp()
    {
        return $this->hasTemp;
    }

    /**
     * @param mixed $hasTemp
     */
    public function setHasTemp($hasTemp=0)
    {
        $this->hasTemp = $hasTemp;
    }

}
