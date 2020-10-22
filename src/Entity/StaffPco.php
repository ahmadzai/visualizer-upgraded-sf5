<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * StaffPco
 *
 * @ORM\Table(name="staff_pco")
 * @ORM\Entity(repositoryClass="App\Repository\StaffPcoRepository")
 */
class StaffPco
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
     * @ORM\Column(name="no_of_pco", type="integer", nullable=true)
     */
    private $noOfPco;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_female_pco", type="integer", nullable=true)
     */
    private $noOfFemalePco;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set noOfPco.
     *
     * @param int|null $noOfPco
     *
     * @return StaffPco
     */
    public function setNoOfPco($noOfPco = null)
    {
        $this->noOfPco = $noOfPco;

        return $this;
    }

    /**
     * Get noOfPco.
     *
     * @return int|null
     */
    public function getNoOfPco()
    {
        return $this->noOfPco;
    }

    /**
     * Set noOfFemalePco.
     *
     * @param int|null $noOfFemalePco
     *
     * @return StaffPco
     */
    public function setNoOfFemalePco($noOfFemalePco = null)
    {
        $this->noOfFemalePco = $noOfFemalePco;

        return $this;
    }

    /**
     * Get noOfFemalePco.
     *
     * @return int|null
     */
    public function getNoOfFemalePco()
    {
        return $this->noOfFemalePco;
    }

    /**
     * Set province.
     *
     * @param Province|null $province
     *
     * @return StaffPco
     */
    public function setProvince($province = null)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province.
     *
     * @return Province|null
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set asOfMonth.
     *
     * @param string|null $asOfMonth
     *
     * @return StaffPco
     */
    public function setAsOfMonth($asOfMonth = null)
    {
        $this->asOfMonth = strtolower(strlen($asOfMonth) > 3 ? substr($asOfMonth, 0, 3) : $asOfMonth);

        return $this;
    }

    /**
     * Get asOfMonth.
     *
     * @return string|null
     */
    public function getAsOfMonth()
    {
        return ucfirst($this->asOfMonth);
    }

    /**
     * Set asOfYear.
     *
     * @param int|null $asOfYear
     *
     * @return StaffPco
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
}
