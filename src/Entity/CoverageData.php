<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CoverageData
 * @ORM\Table(name="coverage_data")
 * @ORM\Entity(repositoryClass="App\Repository\CoverageCatchup\CoverageRepository")
 *
 */
class CoverageData
{
    /**
     * @var string
     *
     * @ORM\Column(name="sub_district", type="text", length=100, nullable=true)
     */
    private $subDistrict;

    /**
     * @var string
     *
     * @ORM\Column(name="cluster_no", type="text", length=50, nullable=true)
     */
    private $clusterNo;

    /**
     * @var string
     *
     * @ORM\Column(name="cluster_name", type="text", length=100, nullable=true)
     */
    private $clusterName;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_teams", type="integer", nullable=true)
     */
    private $noTeams;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_houses", type="integer", nullable=true)
     */
    private $noHouses;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_children", type="integer", nullable=true)
     */
    private $targetChildren;

    /**
     * @var integer
     *
     * @ORM\Column(name="vials_received", type="integer", nullable=true)
     */
    private $vialsReceived;

    /**
     * @var integer
     *
     * @ORM\Column(name="vials_used", type="integer", nullable=true)
     */
    private $vialsUsed;

    /**
     * @var integer
     * @ORM\Column(name="no_child_vac_by_tt", type="integer", nullable=true)
     */
    private $noChildVacByTT;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_houses_visited", type="integer", nullable=true)
     */
    private $noHousesVisited;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_resident_children", type="integer", nullable=true)
     */
    private $noResidentChildren;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_guest_children", type="integer", nullable=true)
     */
    private $noGuestChildren;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_child_in_house_vac", type="integer", nullable=true)
     */
    private $noChildInHouseVac;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_child_outside_vac", type="integer", nullable=true)
     */
    private $noChildOutsideVac;

    /**
     * @var integer
     * @ORM\Column(name="no_vac_nomad", type="integer", nullable=true)
     */
    private $noVacNomad;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_absent_same_day", type="integer", nullable=true)
     */
    private $noAbsentSameDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_absent_same_day_found_vac", type="integer", nullable=true)
     */
    private $noAbsentSameDayFoundVac;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_absent_same_day_vac_by_team", type="integer", nullable=true)
     */
    private $noAbsentSameDayVacByTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_absent_not_same_day", type="integer", nullable=true)
     */
    private $noAbsentNotSameDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_absent_not_same_day_found_vac", type="integer", nullable=true)
     */
    private $noAbsentNotSameDayFoundVac;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_absent_not_same_day_vac_by_team", type="integer", nullable=true)
     */
    private $noAbsentNotSameDayVacByTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_nss", type="integer", nullable=true)
     */
    private $noNSS;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_nss_found_vac", type="integer", nullable=true)
     */
    private $noNSSFoundVac;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_nss_vac_by_team", type="integer", nullable=true)
     */
    private $noNSSVacByTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_refusal", type="integer", nullable=true)
     */
    private $noRefusal;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_refusal_found_vac", type="integer", nullable=true)
     */
    private $noRefusalFoundVac;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_refusal_vac_by_team", type="integer", nullable=true)
     */
    private $noRefusalVacByTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="afp_case", type="integer", nullable=true)
     */
    private $afpCase;

    /**
     * @var integer
     *
     * @ORM\Column(name="vac_day", type="integer", nullable=true)
     */
    private $vacDay;

    /**
     * @var string
     *
     * @ORM\Column(name="tally_type", type="text", length=100, nullable=true)
     */
    private $tallyType;

    /**
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Campaign", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="campaign", referencedColumnName="id")
     * })
     */
    private $campaign;

    /**
     * @var District
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\District", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="district", referencedColumnName="id")
     * })
     */
    private $district;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @return int
     */
    public function getNoTeams()
    {
        return $this->noTeams;
    }

    /**
     * @param int $noTeams
     */
    public function setNoTeams($noTeams)
    {
        $this->noTeams = $noTeams;
    }

    /**
     * @return int
     */
    public function getNoHouses()
    {
        return $this->noHouses;
    }

    /**
     * @param int $noHouses
     */
    public function setNoHouses($noHouses)
    {
        $this->noHouses = $noHouses;
    }

    /**
     * @return int
     */
    public function getTargetChildren()
    {
        return $this->targetChildren;
    }

    /**
     * @param int $targetChildren
     */
    public function setTargetChildren($targetChildren)
    {
        $this->targetChildren = $targetChildren;
    }

    /**
     * @return int
     */
    public function getVialsReceived()
    {
        return $this->vialsReceived;
    }

    /**
     * @param int $vialsReceived
     */
    public function setVialsReceived($vialsReceived)
    {
        $this->vialsReceived = $vialsReceived;
    }

    /**
     * @return int
     */
    public function getVialsUsed()
    {
        return $this->vialsUsed;
    }

    /**
     * @param int $vialsUsed
     */
    public function setVialsUsed($vialsUsed)
    {
        $this->vialsUsed = $vialsUsed;
    }

    /**
     * @return int
     */
    public function getNoHousesVisited()
    {
        return $this->noHousesVisited;
    }

    /**
     * @param int $noHousesVisited
     */
    public function setNoHousesVisited($noHousesVisited)
    {
        $this->noHousesVisited = $noHousesVisited;
    }

    /**
     * @return int
     */
    public function getNoResidentChildren()
    {
        return $this->noResidentChildren;
    }

    /**
     * @param int $noResidentChildren
     */
    public function setNoResidentChildren($noResidentChildren)
    {
        $this->noResidentChildren = $noResidentChildren;
    }

    /**
     * @return int
     */
    public function getNoGuestChildren()
    {
        return $this->noGuestChildren;
    }

    /**
     * @param int $noGuestChildren
     */
    public function setNoGuestChildren($noGuestChildren)
    {
        $this->noGuestChildren = $noGuestChildren;
    }

    /**
     * @return int
     */
    public function getNoChildInHouseVac()
    {
        return $this->noChildInHouseVac;
    }

    /**
     * @param int $noChildInHouseVac
     */
    public function setNoChildInHouseVac($noChildInHouseVac)
    {
        $this->noChildInHouseVac = $noChildInHouseVac;
    }

    /**
     * @return int
     */
    public function getNoChildOutsideVac()
    {
        return $this->noChildOutsideVac;
    }

    /**
     * @param int $noChildOutsideVac
     */
    public function setNoChildOutsideVac($noChildOutsideVac)
    {
        $this->noChildOutsideVac = $noChildOutsideVac;
    }

    /**
     * @return int
     */
    public function getNoAbsentSameDay()
    {
        return $this->noAbsentSameDay;
    }

    /**
     * @param int $noAbsentSameDay
     */
    public function setNoAbsentSameDay($noAbsentSameDay)
    {
        $this->noAbsentSameDay = $noAbsentSameDay;
    }

    /**
     * @return int
     */
    public function getNoAbsentSameDayFoundVac()
    {
        return $this->noAbsentSameDayFoundVac;
    }

    /**
     * @param int $noAbsentSameDayFoundVac
     */
    public function setNoAbsentSameDayFoundVac($noAbsentSameDayFoundVac)
    {
        $this->noAbsentSameDayFoundVac = $noAbsentSameDayFoundVac;
    }

    /**
     * @return int
     */
    public function getNoAbsentSameDayVacByTeam()
    {
        return $this->noAbsentSameDayVacByTeam;
    }

    /**
     * @param int $noAbsentSameDayVacByTeam
     */
    public function setNoAbsentSameDayVacByTeam($noAbsentSameDayVacByTeam)
    {
        $this->noAbsentSameDayVacByTeam = $noAbsentSameDayVacByTeam;
    }

    /**
     * @return int
     */
    public function getNoAbsentNotSameDay()
    {
        return $this->noAbsentNotSameDay;
    }

    /**
     * @param int $noAbsentNotSameDay
     */
    public function setNoAbsentNotSameDay($noAbsentNotSameDay)
    {
        $this->noAbsentNotSameDay = $noAbsentNotSameDay;
    }

    /**
     * @return int
     */
    public function getNoAbsentNotSameDayFoundVac()
    {
        return $this->noAbsentNotSameDayFoundVac;
    }

    /**
     * @param int $noAbsentNotSameDayFoundVac
     */
    public function setNoAbsentNotSameDayFoundVac($noAbsentNotSameDayFoundVac)
    {
        $this->noAbsentNotSameDayFoundVac = $noAbsentNotSameDayFoundVac;
    }

    /**
     * @return int
     */
    public function getNoAbsentNotSameDayVacByTeam()
    {
        return $this->noAbsentNotSameDayVacByTeam;
    }

    /**
     * @param int $noAbsentNotSameDayVacByTeam
     */
    public function setNoAbsentNotSameDayVacByTeam($noAbsentNotSameDayVacByTeam)
    {
        $this->noAbsentNotSameDayVacByTeam = $noAbsentNotSameDayVacByTeam;
    }

    /**
     * @return int
     */
    public function getNoNSS()
    {
        return $this->noNSS;
    }

    /**
     * @param int $noNSS
     */
    public function setNoNSS($noNSS)
    {
        $this->noNSS = $noNSS;
    }

    /**
     * @return int
     */
    public function getNoNSSFoundVac()
    {
        return $this->noNSSFoundVac;
    }

    /**
     * @param int $noNSSFoundVac
     */
    public function setNoNSSFoundVac($noNSSFoundVac)
    {
        $this->noNSSFoundVac = $noNSSFoundVac;
    }

    /**
     * @return int
     */
    public function getNoNSSVacByTeam()
    {
        return $this->noNSSVacByTeam;
    }

    /**
     * @param int $noNSSVacByTeam
     */
    public function setNoNSSVacByTeam($noNSSVacByTeam)
    {
        $this->noNSSVacByTeam = $noNSSVacByTeam;
    }

    /**
     * @return int
     */
    public function getNoRefusal()
    {
        return $this->noRefusal;
    }

    /**
     * @param int $noRefusal
     */
    public function setNoRefusal($noRefusal)
    {
        $this->noRefusal = $noRefusal;
    }

    /**
     * @return int
     */
    public function getNoRefusalFoundVac()
    {
        return $this->noRefusalFoundVac;
    }

    /**
     * @param int $noRefusalFoundVac
     */
    public function setNoRefusalFoundVac($noRefusalFoundVac)
    {
        $this->noRefusalFoundVac = $noRefusalFoundVac;
    }

    /**
     * @return int
     */
    public function getNoRefusalVacByTeam()
    {
        return $this->noRefusalVacByTeam;
    }

    /**
     * @param int $noRefusalVacByTeam
     */
    public function setNoRefusalVacByTeam($noRefusalVacByTeam)
    {
        $this->noRefusalVacByTeam = $noRefusalVacByTeam;
    }

    /**
     * @return int
     */
    public function getAfpCase()
    {
        return $this->afpCase;
    }

    /**
     * @param int $afpCase
     */
    public function setAfpCase($afpCase)
    {
        $this->afpCase = $afpCase;
    }

    /**
     * @return int
     */
    public function getVacDay()
    {
        return $this->vacDay;
    }

    /**
     * @param int $vacDay
     */
    public function setVacDay($vacDay)
    {
        $this->vacDay = $vacDay;
    }

    /**
     * @return string
     */
    public function getTallyType()
    {
        return $this->tallyType;
    }

    /**
     * @param string $tallyType
     */
    public function setTallyType($tallyType)
    {
        $this->tallyType = $tallyType;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getNoChildVacByTT()
    {
        return $this->noChildVacByTT;
    }

    /**
     * @param int $noChildVacByTT
     */
    public function setNoChildVacByTT($noChildVacByTT)
    {
        $this->noChildVacByTT = $noChildVacByTT;
    }

    /**
     * @return int
     */
    public function getNoVacNomad()
    {
        return $this->noVacNomad;
    }

    /**
     * @param int $noVacNomad
     */
    public function setNoVacNomad($noVacNomad)
    {
        $this->noVacNomad = $noVacNomad;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set campaign
     *
     * @param \App\Entity\Campaign $campaign
     *
     * @return AdminData
     */
    public function setCampaign(\App\Entity\Campaign $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \App\Entity\Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Set district
     *
     * @param \App\Entity\District $district
     *
     * @return AdminData
     */
    public function setDistrict(\App\Entity\District $district = null)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return \App\Entity\District
     */
    public function getDistrict()
    {
        return $this->district;
    }
}
