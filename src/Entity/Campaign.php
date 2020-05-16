<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign
 *
 * @ORM\Table(name="campaign")
 * @ORM\Entity(repositoryClass="App\Repository\CampaignRepository")
 */
class Campaign
{
    /**
     * @var integer
     *
     * @ORM\Column(name="campaign_sort_no", type="integer", nullable=true)
     */
    private $campaignSortNo;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_name", type="text", length=65535, nullable=true)
     */
    private $campaignName;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_type", type="text", length=65535, nullable=true)
     */
    private $campaignType;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_start_date", type="text", length=65535, nullable=true)
     */
    private $campaignStartDate;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_end_date", type="text", length=65535, nullable=true)
     */
    private $campaignEndDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entry_date", type="datetime", nullable=true)
     */
    private $entryDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="campaign_year", type="integer", nullable=true)
     */
    private $campaignYear;

    /**
     * @var string
     *
     * @ORM\Column(name="campaign_month", type="string", length=20, nullable=true)
     */
    private $campaignMonth;

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
     * Set campaignSortNo
     *
     * @param integer $campaignSortNo
     *
     * @return Campaign
     */
    public function setCampaignSortNo($campaignSortNo)
    {
        $this->campaignSortNo = $campaignSortNo;

        return $this;
    }

    /**
     * Get campaignSortNo
     *
     * @return integer
     */
    public function getCampaignSortNo()
    {
        return $this->campaignSortNo;
    }

    /**
     * Set campaignName
     *
     * @param string $campaignName
     *
     * @return Campaign
     */
    public function setCampaignName($campaignName)
    {
        $this->campaignName = $campaignName;

        return $this;
    }

    /**
     * Get campaignName
     *
     * @return string
     */
    public function getCampaignName()
    {
        return $this->campaignName;
    }

    /**
     * Set campaignType
     *
     * @param string $campaignType
     *
     * @return Campaign
     */
    public function setCampaignType($campaignType)
    {
        $this->campaignType = $campaignType;

        return $this;
    }

    /**
     * Get campaignType
     *
     * @return string
     */
    public function getCampaignType()
    {
        return $this->campaignType;
    }

    /**
     * Set campaignStartDate
     *
     * @param string $campaignStartDate
     *
     * @return Campaign
     */
    public function setCampaignStartDate($campaignStartDate)
    {
        $this->campaignStartDate = $campaignStartDate;

        return $this;
    }

    /**
     * Get campaignStartDate
     *
     * @return string
     */
    public function getCampaignStartDate()
    {
        return $this->campaignStartDate;
    }

    /**
     * Set campaignEndDate
     *
     * @param string $campaignEndDate
     *
     * @return Campaign
     */
    public function setCampaignEndDate($campaignEndDate)
    {
        $this->campaignEndDate = $campaignEndDate;

        return $this;
    }

    /**
     * Get campaignEndDate
     *
     * @return string
     */
    public function getCampaignEndDate()
    {
        return $this->campaignEndDate;
    }

    /**
     * Set entryDate
     *
     * @param \DateTime $entryDate
     *
     * @return Campaign
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
     * Set campaignYear
     *
     * @param integer $campaignYear
     *
     * @return Campaign
     */
    public function setCampaignYear($campaignYear)
    {
        $this->campaignYear = $campaignYear;

        return $this;
    }

    /**
     * Get campaignYear
     *
     * @return integer
     */
    public function getCampaignYear()
    {
        return $this->campaignYear;
    }

    /**
     * Set campaignMonth
     *
     * @param string $campaignMonth
     *
     * @return Campaign
     */
    public function setCampaignMonth($campaignMonth)
    {
        $this->campaignMonth = $campaignMonth;

        return $this;
    }

    /**
     * Get campaignMonth
     *
     * @return string
     */
    public function getCampaignMonth()
    {
        return $this->campaignMonth;
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
}
