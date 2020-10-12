<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BphsHealthFacility
 *
 * @ORM\Table(name="bphs_health_facility")
 * @ORM\Entity(repositoryClass="App\Repository\BphsHealthFacilityRepository")
 * @UniqueEntity(fields={"id"}, message="A facility with same id is already existed")
 */
class BphsHealthFacility implements  TimestampableInterface, BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\CustomIdGenerator()
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="facility_name", type="string", length=255, nullable=true)
     */
    private $facilityName;

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
     * @ORM\OneToMany(targetEntity="App\Entity\BphsIndicatorReach", mappedBy="hfCode")
     */
    private $indicators;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $facilityType;


    public function __construct()
    {
        $this->indicators = new ArrayCollection();
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

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set facilityName.
     *
     * @param string|null $facilityName
     *
     * @return BphsHealthFacility
     */
    public function setFacilityName($facilityName = null)
    {
        $this->facilityName = $facilityName;

        return $this;
    }

    /**
     * Get facilityName.
     *
     * @return string|null
     */
    public function getFacilityName()
    {
        return $this->facilityName;
    }

    /**
     * Set district.
     *
     * @param District|null $district
     *
     * @return BphsHealthFacility
     */
    public function setDistrict($district = null)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district.
     *
     * @return District|null
     */
    public function getDistrict()
    {
        return $this->district;
    }


    public function __toString()
    {
        return $this->getDistrict()->getDistrictName()."-".$this->getFacilityName();
    }

    /**
     * @return Collection|BphsIndicatorReach[]
     */
    public function getIndicators(): Collection
    {
        return $this->indicators;
    }

    public function addIndicator(BphsIndicatorReach $indicator): self
    {
        if (!$this->indicators->contains($indicator)) {
            $this->indicators[] = $indicator;
            $indicator->setHfCode($this);
        }

        return $this;
    }

    public function removeIndicator(BphsIndicatorReach $indicator): self
    {
        if ($this->indicators->contains($indicator)) {
            $this->indicators->removeElement($indicator);
            // set the owning side to null (unless already changed)
            if ($indicator->getHfCode() === $this) {
                $indicator->setHfCode(null);
            }
        }

        return $this;
    }

    public function getFacilityType(): ?string
    {
        return $this->facilityType;
    }

    public function setFacilityType(?string $facilityType): self
    {
        $this->facilityType = $facilityType;

        return $this;
    }



}
