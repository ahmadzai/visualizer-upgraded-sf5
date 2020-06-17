<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BphsHfIndicator
 * @ORM\Entity(repositoryClass="App\Repository\BphsHfIndicatorRepository")
 * @UniqueEntity(fields={"healthFacility", "indicator", "targetYear"},
 *     message="You have already set this indicator for the selected HF and year",
 *     repositoryMethod="findOneBy")
 */
class BphsHfIndicator implements TimestampableInterface, BlameableInterface
{
    use TimestampableTrait,
        BlameableTrait;

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
     * @var District
     */
    private $district;

    /**
     * @var Province
     */
    private $province;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BphsIndicatorReach", mappedBy="hfIndicator")
     */
    private $indicatorReaches;

    public function __construct()
    {
        $this->indicatorReaches = new ArrayCollection();
    }

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
     * @return int|null
     */
    public function getAnnualTarget(): ?int
    {
        return $this->annualTarget;
    }

    /**
     * @param int|null $annualTarget
     */
    public function setAnnualTarget(?int $annualTarget): void
    {
        $this->annualTarget = $annualTarget;
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

    public function __toString()
    {
        return
            $this->getHealthFacility()."-".
            $this->getIndicator();
    }

    /**
     * @return District
     */
    public function getDistrict(): ?District
    {
        return $this->district;
    }

    /**
     * @return Province
     */
    public function getProvince(): ?Province
    {
        return $this->province;
    }

    /**
     * @return Collection|BphsIndicatorReach[]
     */
    public function getIndicatorReaches(): Collection
    {
        return $this->indicatorReaches;
    }




}
