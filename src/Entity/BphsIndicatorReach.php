<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\BlameableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Blameable\BlameableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BphsIndicatorReach
 *
 * @ORM\Table(name="bphs_indicator_reach")
 * @ORM\Entity(repositoryClass="App\Repository\BphsIndicatorReachRepository")
 * @UniqueEntity(fields={"hfIndicator", "reportMonth", "reportYear"},
 *     message="A similar entry is already existed", repositoryMethod="findOneBy")
 */
class BphsIndicatorReach implements TimestampableInterface, BlameableInterface
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
     * @var BphsHfIndicator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsHfIndicator", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hf_indicator", referencedColumnName="id", fieldName="hfIndicator")
     * })
     */
    private $hfIndicator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsHealthFacility", inversedBy="indicators")
     */
    private $hfCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BphsIndicator", inversedBy="reaches")
     */
    private $indicator;

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
