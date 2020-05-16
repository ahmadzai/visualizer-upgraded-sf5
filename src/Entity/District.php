<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * District
 *
 * @ORM\Table(name="district", uniqueConstraints={@ORM\UniqueConstraint(name="district_code_UNIQUE", columns={"id"})}, indexes={@ORM\Index(name="fk_d_p_idx", columns={"province"})})
 * @ORM\Entity(repositoryClass="App\Repository\DistrictRepository")
 */
class District
{
    /**
     * @var string
     *
     * @ORM\Column(name="district_name", type="text", length=65535, nullable=true)
     */
    private $districtName;

    /**
     * @var string
     *
     * @ORM\Column(name="district_name_alt", type="text", length=65535, nullable=true)
     */
    private $districtNameAlt;

    /**
     * @var string
     *
     * @ORM\Column(name="district_name_pashtu", type="text", length=65535, nullable=true)
     */
    private $districtNamePashtu;

    /**
     * @var string
     *
     * @ORM\Column(name="district_name_dari", type="text", length=65535, nullable=true)
     */
    private $districtNameDari;

    /**
     * @var string
     *
     * @ORM\Column(name="district_lpd_status", type="text", length=65535, nullable=true)
     */
    private $districtLpdStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="district_risk_status", type="string", length=5, nullable=true)
     */
    private $districtRiskStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="district_icn_status", type="string", length=20, nullable=true)
     */
    private $districtIcnStatus;

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
     * @var Province
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Province", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="province", referencedColumnName="id")
     * })
     */
    private $province;



    /**
     * Set districtName
     *
     * @param string $districtName
     *
     * @return District
     */
    public function setDistrictName($districtName)
    {
        $this->districtName = $districtName;

        return $this;
    }

    /**
     * Get districtName
     *
     * @return string
     */
    public function getDistrictName()
    {
        return $this->districtName;
    }

    /**
     * Set districtNameAlt
     *
     * @param string $districtNameAlt
     *
     * @return District
     */
    public function setDistrictNameAlt($districtNameAlt)
    {
        $this->districtNameAlt = $districtNameAlt;

        return $this;
    }

    /**
     * Get districtNameAlt
     *
     * @return string
     */
    public function getDistrictNameAlt()
    {
        return $this->districtNameAlt;
    }

    /**
     * Set districtNamePashtu
     *
     * @param string $districtNamePashtu
     *
     * @return District
     */
    public function setDistrictNamePashtu($districtNamePashtu)
    {
        $this->districtNamePashtu = $districtNamePashtu;

        return $this;
    }

    /**
     * Get districtNamePashtu
     *
     * @return string
     */
    public function getDistrictNamePashtu()
    {
        return $this->districtNamePashtu;
    }

    /**
     * Set districtNameDari
     *
     * @param string $districtNameDari
     *
     * @return District
     */
    public function setDistrictNameDari($districtNameDari)
    {
        $this->districtNameDari = $districtNameDari;

        return $this;
    }

    /**
     * Get districtNameDari
     *
     * @return string
     */
    public function getDistrictNameDari()
    {
        return $this->districtNameDari;
    }

    /**
     * Set districtLpdStatus
     *
     * @param string $districtLpdStatus
     *
     * @return District
     */
    public function setDistrictLpdStatus($districtLpdStatus)
    {
        $this->districtLpdStatus = $districtLpdStatus;

        return $this;
    }

    /**
     * Get districtLpdStatus
     *
     * @return string
     */
    public function getDistrictLpdStatus()
    {
        return $this->districtLpdStatus;
    }

    /**
     * Set districtRiskStatus
     *
     * @param string $districtRiskStatus
     *
     * @return District
     */
    public function setDistrictRiskStatus($districtRiskStatus)
    {
        $this->districtRiskStatus = $districtRiskStatus;

        return $this;
    }

    /**
     * Get districtRiskStatus
     *
     * @return string
     */
    public function getDistrictRiskStatus()
    {
        return $this->districtRiskStatus;
    }

    /**
     * Set districtIcnStatus
     *
     * @param string $districtIcnStatus
     *
     * @return District
     */
    public function setDistrictIcnStatus($districtIcnStatus)
    {
        $this->districtIcnStatus = $districtIcnStatus;

        return $this;
    }

    /**
     * Get districtIcnStatus
     *
     * @return string
     */
    public function getDistrictIcnStatus()
    {
        return $this->districtIcnStatus;
    }

    /**
     * Set entryDate
     *
     * @param \DateTime $entryDate
     *
     * @return District
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

    /**
     * Set province
     *
     * @param Province $province
     *
     * @return District
     */
    public function setProvince(Province $province = null)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    public function __toString() {
        return (string) $this->districtName;
    }

    public function __construct()
    {
        $this->entryDate = new \DateTimeImmutable();
    }
}
