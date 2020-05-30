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

/**
 * BphsIndicator
 *
 * @ORM\Table(name="bphs_indicator")
 * @ORM\Entity(repositoryClass="App\Repository\BphsIndicatorRepository")
 * @UniqueEntity(fields={"shortName"}, message="An indicator with this shortname is already existed")
 */
class BphsIndicator implements TimestampableInterface, BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=true, unique=true)
     */
    private $shortName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BphsIndicatorReach", mappedBy="indicator")
     */
    private $reaches;

    public function __construct()
    {
        $this->reaches = new ArrayCollection();
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
     * Set name.
     *
     * @param string|null $name
     *
     * @return BphsIndicator
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set shortName.
     *
     * @param string|null $shortName
     *
     * @return BphsIndicator
     */
    public function setShortName($shortName = null)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string|null
     */
    public function getShortName()
    {
        return $this->shortName;
    }


    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|BphsIndicatorReach[]
     */
    public function getReaches(): Collection
    {
        return $this->reaches;
    }

    public function addReach(BphsIndicatorReach $reach): self
    {
        if (!$this->reaches->contains($reach)) {
            $this->reaches[] = $reach;
            $reach->setIndicator($this);
        }

        return $this;
    }

    public function removeReach(BphsIndicatorReach $reach): self
    {
        if ($this->reaches->contains($reach)) {
            $this->reaches->removeElement($reach);
            // set the owning side to null (unless already changed)
            if ($reach->getIndicator() === $this) {
                $reach->setIndicator(null);
            }
        }

        return $this;
    }
}
