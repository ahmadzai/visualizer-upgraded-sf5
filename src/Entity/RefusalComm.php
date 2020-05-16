<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RefusalComm
 *
 * @ORM\Table(name="refusal_comm")
 * @ORM\Entity(repositoryClass="App\Repository\CoverageCatchup\RefusalCommRepository")
 */
class RefusalComm
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
     * @var integer
     *
     * @ORM\Column(name="reg_refusal", type="integer", nullable=true)
     */
    private $regRefusal;

    /**
     * @var integer
     *
     * @ORM\Column(name="refusal_vac_in_catchup", type="integer", nullable=true)
     */
    private $refusalVacInCatchup;

    /**
     * @ORM\Column(type="integer", nullable=true, name="refusal_vac_by_crc")
     */
    private $refusalVacByCRC;

    /**
     * @ORM\Column(type="integer", nullable=true, name="refusal_vac_by_rc")
     */
    private $refusalVacByRC;

    /**
     * @ORM\Column(type="integer", nullable=true, name="refusal_vac_by_cip")
     */
    private $refusalVacByCIP;

    /**
     * @ORM\Column(type="integer", nullable=true, name="refusal_vac_by_senior_staff")
     */
    private $refusalVacBySeniorStaff;

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
     * @var string
     * @ORM\Column(type="string", nullable=true, name="campaign_phase", length=100)
     */
    private $campaignPhase;

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
     * @return int
     */
    public function getRegRefusal(): int
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
    public function getRefusalVacInCatchup(): int
    {
        return $this->refusalVacInCatchup;
    }

    /**
     * @param int $refusalVacInCatchup
     */
    public function setRefusalVacInCatchup($refusalVacInCatchup)
    {
        $this->refusalVacInCatchup = $refusalVacInCatchup;
    }

    /**
     * @return mixed
     */
    public function getRefusalVacByCRC()
    {
        return $this->refusalVacByCRC;
    }

    /**
     * @param mixed $refusalVacByCRC
     */
    public function setRefusalVacByCRC($refusalVacByCRC)
    {
        $this->refusalVacByCRC = $refusalVacByCRC;
    }

    /**
     * @return mixed
     */
    public function getRefusalVacByRC()
    {
        return $this->refusalVacByRC;
    }

    /**
     * @param mixed $refusalVacByRC
     */
    public function setRefusalVacByRC($refusalVacByRC)
    {
        $this->refusalVacByRC = $refusalVacByRC;
    }

    /**
     * @return mixed
     */
    public function getRefusalVacByCIP()
    {
        return $this->refusalVacByCIP;
    }

    /**
     * @param mixed $refusalVacByCIP
     */
    public function setRefusalVacByCIP($refusalVacByCIP)
    {
        $this->refusalVacByCIP = $refusalVacByCIP;
    }

    /**
     * @return mixed
     */
    public function getRefusalVacBySeniorStaff()
    {
        return $this->refusalVacBySeniorStaff;
    }

    /**
     * @param mixed $refusalVacBySeniorStaff
     */
    public function setRefusalVacBySeniorStaff($refusalVacBySeniorStaff)
    {
        $this->refusalVacBySeniorStaff = $refusalVacBySeniorStaff;
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
     * @return string
     */
    public function getCampaignPhase(): string
    {
        return $this->campaignPhase;
    }

    /**
     * @param string $campaignPhase
     */
    public function setCampaignPhase($campaignPhase)
    {
        $this->campaignPhase = $campaignPhase;
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

}
