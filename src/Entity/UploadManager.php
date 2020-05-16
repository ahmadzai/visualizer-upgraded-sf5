<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 9/7/2017
 * Time: 5:54 PM
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class UploadManager
 * @package App\Entity
 * @ORM\Table(name="upload_manager")
 * @ORM\Entity
 */
class UploadManager
{

    function __construct()
    {
        $this->modifiedAt = new \DateTime('now');
    }

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $hasTemp;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $modifiedAt;

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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }




}
