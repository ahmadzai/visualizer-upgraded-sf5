<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CatchupData
 *
 * @ORM\Table(name="catchup_data", indexes={@ORM\Index(name="campaign_catchup_idx", columns={"campaign"}), @ORM\Index(name="district_catchup_idx", columns={"district"})})
 * @ORM\Entity(repositoryClass="App\Repository\CoverageCatchup\CatchupRepository")
 */
class CatchupData
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="data_source", type="text", length=20, nullable=true)
     */
    private $dataSource;

    /**
     * @var string
     *
     * @ORM\Column(name="cluster_name", type="text", length=50, nullable=true)
     */
    private $clusterName;

    /**
     * @var string
     *
     * @ORM\Column(name="cluster_no", type="text", length=50, nullable=true)
     */
    private $clusterNo;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_district", type="text", length=50, nullable=true)
     */
    private $subDistrict;

    /**
     * @var string
     *
     * @ORM\Column(name="area_name", type="text", length=100, nullable=true)
     */
    private $areaName;


    /**
     * @var integer
     *
     * @ORM\Column(name="no_sm", type="integer", nullable=true)
     */
    private $noSM;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_hh", type="integer", nullable=true)
     */
    private $noHH;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_u5", type="integer", nullable=true)
     */
    private $noU5;

    /**
     * @var integer
     *
     * @ORM\Column(name="reg_absent", type="integer", nullable=true)
     */
    private $regAbsent;

    /**
     * @var integer
     *
     * @ORM\Column(name="vac_absent", type="integer", nullable=true)
     */
    private $vacAbsent;

    /**
     * @var integer
     *
     * @ORM\Column(name="reg_nss", type="integer", nullable=true)
     */
    private $regNSS;

    /**
     * @var integer
     *
     * @ORM\Column(name="vac_nss", type="integer", nullable=true)
     */
    private $vacNSS;

    /**
     * @var integer
     *
     * @ORM\Column(name="reg_refusal", type="integer", nullable=true)
     */
    private $regRefusal;

    /**
     * @var integer
     *
     * @ORM\Column(name="vac_refusal", type="integer", nullable=true)
     */
    private $vacRefusal;

    /**
     * @var integer
     *
     * @ORM\Column(name="un_recorded", type="integer", nullable=true)
     */
    private $unRecorded;

    /**
     * @var integer
     *
     * @ORM\Column(name="vac_un_recorded", type="integer", nullable=true)
     */
    private $vacUnRecorded;

    /**
     * @var integer
     *
     * @ORM\Column(name="vac_guest", type="integer", nullable=true)
     */
    private $vacGuest;

    /**
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Campaign")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign", referencedColumnName="id")
     * })
     */
    private $campaign;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $entryDate;

    /**
     * @return string
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param string $dataSource
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return string
     */
    public function getClusterName()
    {
        return $this->clusterName;
    }

    /**
     * @param string $clusterName
     */
    public function setClusterName($clusterName)
    {
        $this->clusterName = $clusterName;
    }

    /**
     * @return string
     */
    public function getClusterNo()
    {
        return $this->clusterNo;
    }

    /**
     * @param string $clusterNo
     */
    public function setClusterNo($clusterNo)
    {
        $this->clusterNo = $clusterNo;
    }

    /**
     * @return string
     */
    public function getSubDistrict()
    {
        return $this->subDistrict;
    }

    /**
     * @param string $subDistrict
     */
    public function setSubDistrict($subDistrict)
    {
        $this->subDistrict = $subDistrict;
    }

    /**
     * @return string
     */
    public function getAreaName()
    {
        return $this->areaName;
    }

    /**
     * @param string $areaName
     */
    public function setAreaName($areaName)
    {
        $this->areaName = $areaName;
    }

    /**
     * @return int
     */
    public function getNoSM()
    {
        return $this->noSM;
    }

    /**
     * @param int $noSM
     */
    public function setNoSM($noSM)
    {
        $this->noSM = $noSM;
    }

    /**
     * @return int
     */
    public function getNoHH()
    {
        return $this->noHH;
    }

    /**
     * @param int $noHH
     */
    public function setNoHH($noHH)
    {
        $this->noHH = $noHH;
    }

    /**
     * @return int
     */
    public function getNoU5()
    {
        return $this->noU5;
    }

    /**
     * @param int $noU5
     */
    public function setNoU5($noU5)
    {
        $this->noU5 = $noU5;
    }

    /**
     * @return int
     */
    public function getRegAbsent()
    {
        return $this->regAbsent;
    }

    /**
     * @param int $regAbsent
     */
    public function setRegAbsent($regAbsent)
    {
        $this->regAbsent = $regAbsent;
    }

    /**
     * @return int
     */
    public function getVacAbsent()
    {
        return $this->vacAbsent;
    }

    /**
     * @param int $vacAbsent
     */
    public function setVacAbsent($vacAbsent)
    {
        $this->vacAbsent = $vacAbsent;
    }

    /**
     * @return int
     */
    public function getRegNSS()
    {
        return $this->regNSS;
    }

    /**
     * @param int $regNSS
     */
    public function setRegNSS($regNSS)
    {
        $this->regNSS = $regNSS;
    }

    /**
     * @return int
     */
    public function getVacNSS()
    {
        return $this->vacNSS;
    }

    /**
     * @param int $vacNSS
     */
    public function setVacNSS($vacNSS)
    {
        $this->vacNSS = $vacNSS;
    }

    /**
     * @return int
     */
    public function getRegRefusal()
    {
        return $this->regRefusal;
    }

    /**
     * @param int $regRefusal
     */
    public function setRegRefusal($regRefusal)
    {
        $this->regRefusal = $regRefusal;
    }

    /**
     * @return int
     */
    public function getVacRefusal()
    {
        return $this->vacRefusal;
    }

    /**
     * @param int $vacRefusal
     */
    public function setVacRefusal($vacRefusal)
    {
        $this->vacRefusal = $vacRefusal;
    }

    /**
     * @return int
     */
    public function getUnRecorded()
    {
        return $this->unRecorded;
    }

    /**
     * @param int $unRecorded
     */
    public function setUnRecorded($unRecorded)
    {
        $this->unRecorded = $unRecorded;
    }

    /**
     * @return int
     */
    public function getVacUnRecorded()
    {
        return $this->vacUnRecorded;
    }

    /**
     * @param int $vacUnRecorded
     */
    public function setVacUnRecorded($vacUnRecorded)
    {
        $this->vacUnRecorded = $vacUnRecorded;
    }

    /**
     * @return int
     */
    public function getVacGuest()
    {
        return $this->vacGuest;
    }

    /**
     * @param int $vacGuest
     */
    public function setVacGuest($vacGuest)
    {
        $this->vacGuest = $vacGuest;
    }

    /**
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param District $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entryDate;
    }

    public function setEntryDate(?\DateTimeInterface $entryDate): self
    {
        $this->entryDate = $entryDate;

        return $this;
    }





}
