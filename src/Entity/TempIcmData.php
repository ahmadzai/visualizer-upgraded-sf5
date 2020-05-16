<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TempIcmData
 *
 * @ORM\Table(name="temp_icm_data")
 * @ORM\Entity
 */
class TempIcmData
{
    /**
     * @var integer
     *
     * @ORM\Column(name="district_code", type="integer", nullable=true)
     */
    private $districtCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_team_monitored", type="integer", nullable=true)
     */
    private $noTeamMonitored;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_resident_area", type="integer", nullable=true)
     */
    private $teamResidentArea;

    /**
     * @var integer
     *
     * @ORM\Column(name="vaccinator_trained", type="integer", nullable=true)
     */
    private $vaccinatorTrained;

    /**
     * @var integer
     *
     * @ORM\Column(name="vacc_stage_3", type="integer", nullable=true)
     */
    private $vaccStage3;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_supervised", type="integer", nullable=true)
     */
    private $teamSupervised;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_with_chw", type="integer", nullable=true)
     */
    private $teamWithChw;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_with_female", type="integer", nullable=true)
     */
    private $teamWithFemale;

    /**
     * @var integer
     *
     * @ORM\Column(name="team_accom_sm", type="integer", nullable=true)
     */
    private $teamAccomSm;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_missed_no_team_visit", type="integer", nullable=true)
     */
    private $noMissedNoTeamVisit;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_child_seen", type="integer", nullable=true)
     */
    private $noChildSeen;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_child_with_fm", type="integer", nullable=true)
     */
    private $noChildWithFm;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_missed_child", type="integer", nullable=true)
     */
    private $noMissedChild;

    /**
     * @var integer
     *
     * @ORM\Column(name="no_missed_10", type="integer", nullable=true)
     */
    private $noMissed10;

    /**
     * @var integer
     *
     * @ORM\Column(name="campaign_id", type="integer", nullable=true)
     */
    private $campaignId;

    /**
     * @var integer
     *
     * @ORM\Column(name="data_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $dataId;



    /**
     * Set districtCode
     *
     * @param integer $districtCode
     *
     * @return TempIcmData
     */
    public function setDistrictCode($districtCode)
    {
        $this->districtCode = $districtCode;

        return $this;
    }

    /**
     * Get districtCode
     *
     * @return integer
     */
    public function getDistrictCode()
    {
        return $this->districtCode;
    }

    /**
     * Set noTeamMonitored
     *
     * @param integer $noTeamMonitored
     *
     * @return TempIcmData
     */
    public function setNoTeamMonitored($noTeamMonitored)
    {
        $this->noTeamMonitored = $noTeamMonitored;

        return $this;
    }

    /**
     * Get noTeamMonitored
     *
     * @return integer
     */
    public function getNoTeamMonitored()
    {
        return $this->noTeamMonitored;
    }

    /**
     * Set teamResidentArea
     *
     * @param integer $teamResidentArea
     *
     * @return TempIcmData
     */
    public function setTeamResidentArea($teamResidentArea)
    {
        $this->teamResidentArea = $teamResidentArea;

        return $this;
    }

    /**
     * Get teamResidentArea
     *
     * @return integer
     */
    public function getTeamResidentArea()
    {
        return $this->teamResidentArea;
    }

    /**
     * Set vaccinatorTrained
     *
     * @param integer $vaccinatorTrained
     *
     * @return TempIcmData
     */
    public function setVaccinatorTrained($vaccinatorTrained)
    {
        $this->vaccinatorTrained = $vaccinatorTrained;

        return $this;
    }

    /**
     * Get vaccinatorTrained
     *
     * @return integer
     */
    public function getVaccinatorTrained()
    {
        return $this->vaccinatorTrained;
    }

    /**
     * Set vaccStage3
     *
     * @param integer $vaccStage3
     *
     * @return TempIcmData
     */
    public function setVaccStage3($vaccStage3)
    {
        $this->vaccStage3 = $vaccStage3;

        return $this;
    }

    /**
     * Get vaccStage3
     *
     * @return integer
     */
    public function getVaccStage3()
    {
        return $this->vaccStage3;
    }

    /**
     * Set teamSupervised
     *
     * @param integer $teamSupervised
     *
     * @return TempIcmData
     */
    public function setTeamSupervised($teamSupervised)
    {
        $this->teamSupervised = $teamSupervised;

        return $this;
    }

    /**
     * Get teamSupervised
     *
     * @return integer
     */
    public function getTeamSupervised()
    {
        return $this->teamSupervised;
    }

    /**
     * Set teamWithChw
     *
     * @param integer $teamWithChw
     *
     * @return TempIcmData
     */
    public function setTeamWithChw($teamWithChw)
    {
        $this->teamWithChw = $teamWithChw;

        return $this;
    }

    /**
     * Get teamWithChw
     *
     * @return integer
     */
    public function getTeamWithChw()
    {
        return $this->teamWithChw;
    }

    /**
     * Set teamWithFemale
     *
     * @param integer $teamWithFemale
     *
     * @return TempIcmData
     */
    public function setTeamWithFemale($teamWithFemale)
    {
        $this->teamWithFemale = $teamWithFemale;

        return $this;
    }

    /**
     * Get teamWithFemale
     *
     * @return integer
     */
    public function getTeamWithFemale()
    {
        return $this->teamWithFemale;
    }

    /**
     * Set teamAccomSm
     *
     * @param integer $teamAccomSm
     *
     * @return TempIcmData
     */
    public function setTeamAccomSm($teamAccomSm)
    {
        $this->teamAccomSm = $teamAccomSm;

        return $this;
    }

    /**
     * Get teamAccomSm
     *
     * @return integer
     */
    public function getTeamAccomSm()
    {
        return $this->teamAccomSm;
    }

    /**
     * Set noMissedNoTeamVisit
     *
     * @param integer $noMissedNoTeamVisit
     *
     * @return TempIcmData
     */
    public function setNoMissedNoTeamVisit($noMissedNoTeamVisit)
    {
        $this->noMissedNoTeamVisit = $noMissedNoTeamVisit;

        return $this;
    }

    /**
     * Get noMissedNoTeamVisit
     *
     * @return integer
     */
    public function getNoMissedNoTeamVisit()
    {
        return $this->noMissedNoTeamVisit;
    }

    /**
     * Set noChildSeen
     *
     * @param integer $noChildSeen
     *
     * @return TempIcmData
     */
    public function setNoChildSeen($noChildSeen)
    {
        $this->noChildSeen = $noChildSeen;

        return $this;
    }

    /**
     * Get noChildSeen
     *
     * @return integer
     */
    public function getNoChildSeen()
    {
        return $this->noChildSeen;
    }

    /**
     * Set noChildWithFm
     *
     * @param integer $noChildWithFm
     *
     * @return TempIcmData
     */
    public function setNoChildWithFm($noChildWithFm)
    {
        $this->noChildWithFm = $noChildWithFm;

        return $this;
    }

    /**
     * Get noChildWithFm
     *
     * @return integer
     */
    public function getNoChildWithFm()
    {
        return $this->noChildWithFm;
    }

    /**
     * Set noMissedChild
     *
     * @param integer $noMissedChild
     *
     * @return TempIcmData
     */
    public function setNoMissedChild($noMissedChild)
    {
        $this->noMissedChild = $noMissedChild;

        return $this;
    }

    /**
     * Get noMissedChild
     *
     * @return integer
     */
    public function getNoMissedChild()
    {
        return $this->noMissedChild;
    }

    /**
     * Set noMissed10
     *
     * @param integer $noMissed10
     *
     * @return TempIcmData
     */
    public function setNoMissed10($noMissed10)
    {
        $this->noMissed10 = $noMissed10;

        return $this;
    }

    /**
     * Get noMissed10
     *
     * @return integer
     */
    public function getNoMissed10()
    {
        return $this->noMissed10;
    }

    /**
     * Set campaignId
     *
     * @param integer $campaignId
     *
     * @return TempIcmData
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;

        return $this;
    }

    /**
     * Get campaignId
     *
     * @return integer
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * Get dataId
     *
     * @return integer
     */
    public function getDataId()
    {
        return $this->dataId;
    }
}
