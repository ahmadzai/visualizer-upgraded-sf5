<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BphsIndicatorReach
 *
 * @ORM\Table(name="bphs_indicator_reach")
 * @ORM\Entity(repositoryClass="App\Repository\BphsIndicatorReachRepository")
 * @UniqueEntity(fields={"slug"}, message="A similar entry is already existed")
 */
class BphsIndicatorReach
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
     * @var BphsHfIndicator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsHfIndicator", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hf_indicator", referencedColumnName="id", fieldName="hfIndicator")
     * })
     */
    private $hfIndicator;

    /**
     * @var int|null
     * @ORM\Column(name="reach", nullable=true, type="integer")
     */
    private $reach;

    /**
     * @var string|null
     *
     * @ORM\Column(name="report_month", type="string", length=3, nullable=true)
     */
    private $reportMonth;

    /**
     * @var int|null
     *
     * @ORM\Column(name="report_year", type="integer", nullable=true)
     */
    private $reportYear;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", unique=true)
     * @Gedmo\Slug(handlers={{"class":"Gedmo\Sluggable\Handler\RelativeSlugHandler",
     *     "options":{"name":"relationField", "value":"hfIndicator"},{"name":"relationSlugField",
     *          "value":"uniqueSlug"},{"name":"seperator", "value":"-"},{"name":"urilize",
     *          "value":"true"},{"name":"style", "value":"lower"},
     *          "value":null}}, fields={"reportMonth",
     *          "reportYear"}, separator="-",
     *          style="lower", unique=false)
     *
     */
    private $slug;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsHealthFacility", inversedBy="indicators")
     */
    private $hfCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsIndicator", inversedBy="reaches")
     */
    private $indicator;


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
     * Set hfIndicator.
     *
     * @param BphsHfIndicator|null $hfIndicator
     *
     * @return BphsIndicatorReach
     */
    public function setHfIndicator($hfIndicator = null)
    {
        $this->hfIndicator = $hfIndicator;

        return $this;
    }

    /**
     * Get hfIndicator.
     *
     * @return BphsHfIndicator|null
     */
    public function getHfIndicator()
    {
        return $this->hfIndicator;
    }

    /**
     * @return int|null
     */
    public function getReach()
    {
        return $this->reach;
    }

    /**
     * @param int|null $reach
     */
    public function setReach($reach = null)
    {
        $this->reach = $reach;
    }

    /**
     * Set reportMonth.
     *
     * @param string|null $reportMonth
     *
     * @return BphsIndicatorReach
     */
    public function setReportMonth($reportMonth = null)
    {
        $this->reportMonth = $reportMonth;

        return $this;
    }

    /**
     * Get reportMonth.
     *
     * @return string|null
     */
    public function getReportMonth()
    {
        return $this->reportMonth;
    }

    /**
     * Set reportYear.
     *
     * @param int|null $reportYear
     *
     * @return BphsIndicatorReach
     */
    public function setReportYear($reportYear = null)
    {
        $this->reportYear = $reportYear;

        return $this;
    }

    /**
     * Get reportYear.
     *
     * @return int|null
     */
    public function getReportYear()
    {
        return $this->reportYear;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
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

    public function getHfCode(): ?BphsHealthFacility
    {
        return $this->hfCode;
    }

    public function setHfCode(?BphsHealthFacility $hfCode): self
    {
        $this->hfCode = $hfCode;

        return $this;
    }

    public function getIndicator(): ?BphsIndicator
    {
        return $this->indicator;
    }

    public function setIndicator(?BphsIndicator $indicator): self
    {
        $this->indicator = $indicator;

        return $this;
    }
}
