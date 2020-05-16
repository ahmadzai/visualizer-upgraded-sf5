<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Province
 *
 * @ORM\Table(name="province", uniqueConstraints={@ORM\UniqueConstraint(name="province_code_UNIQUE", columns={"id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProvinceRepository")
 */
class Province
{
    /**
     * @var string
     *
     * @ORM\Column(name="province_region", type="string", length=10, nullable=true)
     */
    private $provinceRegion;

    /**
     * @var string
     *
     * @ORM\Column(name="province_name", type="string", length=30, nullable=true)
     */
    private $provinceName;

    /**
     * @var string
     *
     * @ORM\Column(name="province_name_pashtu", type="string", length=45, nullable=true)
     */
    private $provinceNamePashtu;

    /**
     * @var string
     *
     * @ORM\Column(name="province_name_dari", type="string", length=45, nullable=true)
     */
    private $provinceNameDari;


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
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;



    /**
     * Set provinceRegion
     *
     * @param string $provinceRegion
     *
     * @return Province
     */
    public function setProvinceRegion($provinceRegion)
    {
        $this->provinceRegion = $provinceRegion;

        return $this;
    }

    /**
     * Get provinceRegion
     *
     * @return string
     */
    public function getProvinceRegion()
    {
        return $this->provinceRegion;
    }

    /**
     * Set provinceName
     *
     * @param string $provinceName
     *
     * @return Province
     */
    public function setProvinceName($provinceName)
    {
        $this->provinceName = $provinceName;

        return $this;
    }

    /**
     * Get provinceName
     *
     * @return string
     */
    public function getProvinceName()
    {
        return $this->provinceName;
    }

    /**
     * Set provinceNamePashtu
     *
     * @param string $provinceNamePashtu
     *
     * @return Province
     */
    public function setProvinceNamePashtu($provinceNamePashtu)
    {
        $this->provinceNamePashtu = $provinceNamePashtu;

        return $this;
    }

    /**
     * Get provinceNamePashtu
     *
     * @return string
     */
    public function getProvinceNamePashtu()
    {
        return $this->provinceNamePashtu;
    }

    /**
     * Set provinceNameDari
     *
     * @param string $provinceNameDari
     *
     * @return Province
     */
    public function setProvinceNameDari($provinceNameDari)
    {
        $this->provinceNameDari = $provinceNameDari;

        return $this;
    }

    /**
     * Get provinceNameDari
     *
     * @return string
     */
    public function getProvinceNameDari()
    {
        return $this->provinceNameDari;
    }


    /**
     * Set entryDate
     *
     * @param \DateTime $entryDate
     *
     * @return Province
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

    public function __toString() {
        return (string) $this->id;
    }
}
