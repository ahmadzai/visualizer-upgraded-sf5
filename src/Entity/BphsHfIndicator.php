<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BphsHfIndicator
 *
 * @ORM\Table(name="bphs_hf_indicator")
 * @ORM\Entity(repositoryClass="App\Repository\BphsHealthFacilityRepository")
 * @UniqueEntity(fields={"uniqueSlug"})
 */
class BphsHfIndicator
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
     * @var BphsHealthFacility
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsHealthFacility", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="health_facility", referencedColumnName="id", fieldName="healthFacility")
     * })
     */
    private $healthFacility;

    /**
     * @var BphsIndicator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsIndicator", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicator", referencedColumnName="id")
     * })
     */
    private $indicator;

    /**
     * @var int|null
     *
     * @ORM\Column(name="annual_target", type="integer", nullable=true)
     */
    private $annualTarget;

    /**
     * @var int|null
     *
     * @ORM\Column(name="monthly_target", type="integer", nullable=true)
     */
    private $monthlyTarget;

    /**
     * @var int|null
     *
     * @ORM\Column(name="target_year", type="integer", nullable=true)
     */
    private $targetYear;

    /**
     * @var string
     * @ORM\Column(name="indicator_slug")
     */
    private $indicatorSlug;

    /**
     * @var string
     * @ORM\Column(name="unique_slug", type="string", unique=true)
     * @Gedmo\Slug(handlers={{"class":"Gedmo\Sluggable\Handler\RelativeSlugHandler",
     *     "options":{"name":"relationField", "value":"healthFacility"},{"name":"relationSlugField",
     *          "value":"id"},{"name":"seperator", "value":"-"},{"name":"urilize",
     *          "value":"true"},{"name":"style", "value":"lower"},
     *          "value":null}}, fields={"indicatorSlug",
     *          "targetYear"}, separator="-",
     *          style="lower", unique=false)
     *
     */
    private $uniqueSlug;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author", referencedColumnName="id")
     * })
     * @Gedmo\Blameable(on="create")
     */
    private $author;


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
     * Set healthFacility.
     *
     * @param BphsHealthFacility|null $healthFacility
     *
     * @return BphsHfIndicator
     */
    public function setHealthFacility($healthFacility = null)
    {
        $this->healthFacility = $healthFacility;
        return $this;
    }

    /**
     * Get healthFacility.
     *
     * @return BphsHealthFacility|null
     */
    public function getHealthFacility()
    {
        return $this->healthFacility;
    }

    /**
     * Set indicator.
     *
     * @param BphsIndicator|null $indicator
     *
     * @return BphsHfIndicator
     */
    public function setIndicator($indicator = null)
    {
        $this->indicator = $indicator;
        $this->indicatorSlug = strtolower($indicator->getShortName());
        // set the indicator slug here
        return $this;
    }

    /**
     * Get indicator.
     *
     * @return BphsIndicator|null
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * Set annualTarget.
     *
     * @param int|null $annualTarget
     *
     * @return BphsHfIndicator
     */
    public function setAnnualTarget($annualTarget = null)
    {
        $this->annualTarget = $annualTarget;

        return $this;
    }

    /**
     * Get annualTarget.
     *
     * @return int|null
     */
    public function getAnnualTarget()
    {
        return $this->annualTarget;
    }

    /**
     * Set monthlyTarget.
     *
     * @param int|null $monthlyTarget
     *
     * @return BphsHfIndicator
     */
    public function setMonthlyTarget($monthlyTarget = null)
    {
        $this->monthlyTarget = $monthlyTarget;

        return $this;
    }

    /**
     * Get monthlyTarget.
     *
     * @return int|null
     */
    public function getMonthlyTarget()
    {
        return $this->monthlyTarget;
    }

    /**
     * Set targetYear.
     *
     * @param int|null $targetYear
     *
     * @return BphsHfIndicator
     */
    public function setTargetYear($targetYear = null)
    {
        $this->targetYear = $targetYear;

        return $this;
    }

    /**
     * Get targetYear.
     *
     * @return int|null
     */
    public function getTargetYear()
    {
        return $this->targetYear;
    }

    /**
     * @return string
     */
    public function getIndicatorSlug(): string
    {
        return $this->indicatorSlug;
    }

    /**
     * @return string
     */
    public function getUniqueSlug(): string
    {
        return $this->uniqueSlug;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get author.
     *
     * @return User|null
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function __toString()
    {
        return
            $this->getHealthFacility()."-".
            $this->getIndicator();
    }
}
