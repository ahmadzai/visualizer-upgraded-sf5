<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TablesManager
 *
 * @ORM\Table(name="tables_manager")
 * @ORM\Entity
 */
class TablesManager
{
    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", length=100, nullable=false)
     */
    private $tableName;

    /**
     * @var string
     *
     * @ORM\Column(name="table_long_name", type="string", length=100, nullable=false)
     */
    private $tableLongName;

    /**
     * @var string
     *
     * @ORM\Column(name="table_type", type="string", length=100, nullable=false)
     */
    private $tableType;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=100, nullable=true)
     */
    private $source;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dashboard", type="boolean", nullable=false)
     */
    private $dashboard;

    /**
     * @var boolean
     *
     * @ORM\Column(name="upload_form", type="boolean", nullable=false)
     */
    private $uploadForm;

    /**
     * @var boolean
     *
     * @ORM\Column(name="entry_form", type="boolean", nullable=false)
     */
    private $entryForm;

    /**
     * @var boolean
     *
     * @ORM\Column(name="download_form", type="boolean", nullable=false)
     */
    private $downloadForm;

    /**
     * @var string
     *
     * @ORM\Column(name="data_level", type="string", length=100, nullable=false)
     */
    private $dataLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort_no", type="integer", nullable=false)
     */
    private $sortNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entry_date", type="datetime", nullable=true)
     */
    private $entryDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set tableName
     *
     * @param string $tableName
     *
     * @return TablesManager
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get tableName
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set tableLongName
     *
     * @param string $tableLongName
     *
     * @return TablesManager
     */
    public function setTableLongName($tableLongName)
    {
        $this->tableLongName = $tableLongName;

        return $this;
    }

    /**
     * Get tableLongName
     *
     * @return string
     */
    public function getTableLongName()
    {
        return $this->tableLongName;
    }

    /**
     * Set tableType
     *
     * @param string $tableType
     *
     * @return TablesManager
     */
    public function setTableType($tableType)
    {
        $this->tableType = $tableType;

        return $this;
    }

    /**
     * Get tableType
     *
     * @return string
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return TablesManager
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set dashboard
     *
     * @param boolean $dashboard
     *
     * @return TablesManager
     */
    public function setDashboard($dashboard)
    {
        $this->dashboard = $dashboard;

        return $this;
    }

    /**
     * Get dashboard
     *
     * @return boolean
     */
    public function getDashboard()
    {
        return $this->dashboard;
    }

    /**
     * Set uploadForm
     *
     * @param boolean $uploadForm
     *
     * @return TablesManager
     */
    public function setUploadForm($uploadForm)
    {
        $this->uploadForm = $uploadForm;

        return $this;
    }

    /**
     * Get uploadForm
     *
     * @return boolean
     */
    public function getUploadForm()
    {
        return $this->uploadForm;
    }

    /**
     * Set entryForm
     *
     * @param boolean $entryForm
     *
     * @return TablesManager
     */
    public function setEntryForm($entryForm)
    {
        $this->entryForm = $entryForm;

        return $this;
    }

    /**
     * Get entryForm
     *
     * @return boolean
     */
    public function getEntryForm()
    {
        return $this->entryForm;
    }

    /**
     * Set downloadForm
     *
     * @param boolean $downloadForm
     *
     * @return TablesManager
     */
    public function setDownloadForm($downloadForm)
    {
        $this->downloadForm = $downloadForm;

        return $this;
    }

    /**
     * Get downloadForm
     *
     * @return boolean
     */
    public function getDownloadForm()
    {
        return $this->downloadForm;
    }

    /**
     * Set dataLevel
     *
     * @param string $dataLevel
     *
     * @return TablesManager
     */
    public function setDataLevel($dataLevel)
    {
        $this->dataLevel = $dataLevel;

        return $this;
    }

    /**
     * Get dataLevel
     *
     * @return string
     */
    public function getDataLevel()
    {
        return $this->dataLevel;
    }

    /**
     * Set sortNo
     *
     * @param integer $sortNo
     *
     * @return TablesManager
     */
    public function setSortNo($sortNo)
    {
        $this->sortNo = $sortNo;

        return $this;
    }

    /**
     * Get sortNo
     *
     * @return integer
     */
    public function getSortNo()
    {
        return $this->sortNo;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return TablesManager
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set entryDate
     *
     * @param \DateTime $entryDate
     *
     * @return TablesManager
     */
    public function setEntryDate($entryDate)
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    /**
     * Get entryDate
     *
     * @return \DateTime
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
