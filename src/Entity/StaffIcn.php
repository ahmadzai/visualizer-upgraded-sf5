<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * StaffIcn
 *
 * @ORM\Table(name="staff_icn")
 * @ORM\Entity(repositoryClass="App\Repository\StaffIcnRepository")
 */
class StaffIcn
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_dco", type="integer", nullable=true)
     */
    private $noOfDco;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_female_dco", type="integer", nullable=true)
     */
    private $noOfFemaleDco;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_ccs", type="integer", nullable=true)
     */
    private $noOfCcs;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_female_ccs", type="integer", nullable=true)
     */
    private $noOfFemaleCcs;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_sm", type="integer", nullable=true)
     */
    private $noOfSm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_female_sm", type="integer", nullable=true)
     */
    private $noOfFemaleSm;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_fmv", type="integer", nullable=true)
     */
    private $noOfFmv;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_ext", type="integer", nullable=true)
     */
    private $noOfExt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_female_ext", type="integer", nullable=true)
     */
    private $noOfFemaleExt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="as_of_month", type="string", length=50, nullable=true)
     */
    private $asOfMonth;

    /**
     * @var int|null
     *
     * @ORM\Column(name="as_of_year", type="integer", nullable=true)
     */
    private $asOfYear;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $updatedAt;

    /**
     * @var District
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="district", referencedColumnName="id")
     * })
     */
    private $district;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set noOfDco.
     *
     * @param int|null $noOfDco
     *
     * @return StaffIcn
     */
    public function setNoOfDco($noOfDco = null)
    {
        $this->noOfDco = $noOfDco;

        return $this;
    }

    /**
     * Get noOfDco.
     *
     * @return int|null
     */
    public function getNoOfDco()
    {
        return $this->noOfDco;
    }

    /**
     * Set noOfFemaleDco.
     *
     * @param int|null $noOfFemaleDco
     *
     * @return StaffIcn
     */
    public function setNoOfFemaleDco($noOfFemaleDco = null)
    {
        $this->noOfFemaleDco = $noOfFemaleDco;

        return $this;
    }

    /**
     * Get noOfFemaleDco.
     *
     * @return int|null
     */
    public function getNoOfFemaleDco()
    {
        return $this->noOfFemaleDco;
    }

    /**
     * Set noOfCcs.
     *
     * @param int|null $noOfCcs
     *
     * @return StaffIcn
     */
    public function setNoOfCcs($noOfCcs = null)
    {
        $this->noOfCcs = $noOfCcs;

        return $this;
    }

    /**
     * Get noOfCcs.
     *
     * @return int
     */
    public function getNoOfCcs()
    {
        return $this->noOfCcs;
    }

    /**
     * Set noOfFemaleCcs.
     *
     * @param int|null $noOfFemaleCcs
     *
     * @return StaffIcn
     */
    public function setNoOfFemaleCcs($noOfFemaleCcs = null)
    {
        $this->noOfFemaleCcs = $noOfFemaleCcs;

        return $this;
    }

    /**
     * Get noOfFemaleCcs.
     *
     * @return int|null
     */
    public function getNoOfFemaleCcs()
    {
        return $this->noOfFemaleCcs;
    }

    /**
     * Set noOfSm.
     *
     * @param int|null $noOfSm
     *
     * @return StaffIcn
     */
    public function setNoOfSm($noOfSm = null)
    {
        $this->noOfSm = $noOfSm;

        return $this;
    }

    /**
     * Get noOfSm.
     *
     * @return int|null
     */
    public function getNoOfSm()
    {
        return $this->noOfSm;
    }

    /**
     * Set noOfFemaleSm.
     *
     * @param int|null $noOfFemaleSm
     *
     * @return StaffIcn
     */
    public function setNoOfFemaleSm($noOfFemaleSm = null)
    {
        $this->noOfFemaleSm = $noOfFemaleSm;

        return $this;
    }

    /**
     * Get noOfFemaleSm.
     *
     * @return int|null
     */
    public function getNoOfFemaleSm()
    {
        return $this->noOfFemaleSm;
    }

    /**
     * Set noOfFmv.
     *
     * @param int|null $noOfFmv
     *
     * @return StaffIcn
     */
    public function setNoOfFmv($noOfFmv = null)
    {
        $this->noOfFmv = $noOfFmv;

        return $this;
    }

    /**
     * Get noOfFmv.
     *
     * @return int|null
     */
    public function getNoOfFmv()
    {
        return $this->noOfFmv;
    }

    /**
     * Set noOfExt.
     *
     * @param int|null $noOfExt
     *
     * @return StaffIcn
     */
    public function setNoOfExt($noOfExt = null)
    {
        $this->noOfExt = $noOfExt;

        return $this;
    }

    /**
     * Get noOfExt.
     *
     * @return int|null
     */
    public function getNoOfExt()
    {
        return $this->noOfExt;
    }

    /**
     * Set noOfFemaleExt.
     *
     * @param int|null $noOfFemaleExt
     *
     * @return StaffIcn
     */
    public function setNoOfFemaleExt($noOfFemaleExt = null)
    {
        $this->noOfFemaleExt = $noOfFemaleExt;

        return $this;
    }

    /**
     * Get noOfFemaleExt.
     *
     * @return int|null
     */
    public function getNoOfFemaleExt()
    {
        return $this->noOfFemaleExt;
    }

    /**
     * Set asOfMonth.
     *
     * @param string|null $asOfMonth
     *
     * @return StaffIcn
     */
    public function setAsOfMonth($asOfMonth = null)
    {
        $this->asOfMonth = $asOfMonth;

        return $this;
    }

    /**
     * Get asOfMonth.
     *
     * @return string
     */
    public function getAsOfMonth()
    {
        return $this->asOfMonth;
    }

    /**
     * Set asOfYear.
     *
     * @param int|null $asOfYear
     *
     * @return StaffIcn
     */
    public function setAsOfYear($asOfYear = null)
    {
        $this->asOfYear = $asOfYear;

        return $this;
    }

    /**
     * Get asOfYear.
     *
     * @return int|null
     */
    public function getAsOfYear()
    {
        return $this->asOfYear;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set district.
     *
     * @param District|null $district
     *
     * @return StaffIcn
     */
    public function setDistrict($district = null)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district.
     *
     * @return District|null
     */
    public function getDistrict()
    {
        return $this->district;
    }
}
