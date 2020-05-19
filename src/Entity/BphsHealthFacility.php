<?php

namespace App\Entity;

use App\Entity\District;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BphsHealthFacility
 *
 * @ORM\Table(name="bphs_health_facility")
 * @ORM\Entity(repositoryClass="App\Repository\BphsHealthFacilityRepository")
 * @UniqueEntity(fields={"facilitySlug"}, message="A facility with same name is already existed for selected district")
 */
class BphsHealthFacility
{
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
     * @var string|null
     *
     * @ORM\Column(name="facility_slug", type="string", length=255, unique=true)
     * @Gedmo\Slug(handlers={{"class":"Gedmo\Sluggable\Handler\RelativeSlugHandler",
     *      "options":{"name":"relationField", "value":"district"},{"name":"relationSlugField",
     *          "value":"districtName"},{"name":"seperator", "value":"-"},{"name":"urilize",
     *          "value":"true"},{"name":"style", "value":"lower"},
     *          "value":null}}, fields={"facilityName",
     *          "id"}, style="lower")
     *
     */
    private $facilitySlug;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

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
     * @ORM\OneToMany(targetEntity="App\Entity\BphsIndicatorReach", mappedBy="hfCode")
     */
    private $indicators;

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
     * Get facilityShortName.
     *
     * @return string|null
     */
    public function getFacilitySlug()
    {
        return $this->facilitySlug;
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
}
