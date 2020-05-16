<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Covid19Cases
 *
 * @ORM\Table(name="covid19_cases")
 * @ORM\Entity(repositoryClass="App\Repository\Covid19CasesRepository")
 */
class Covid19Cases
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
     * @ORM\Column(name="no_of_confirmed_cases", type="integer", nullable=true)
     */
    private $noOfConfirmedCases;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_suspected_cases", type="integer", nullable=true)
     */
    private $noOfSuspectedCases;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_recovered_cases", type="integer", nullable=true)
     */
    private $noOfRecoveredCases;

    /**
     * @var int|null
     *
     * @ORM\Column(name="no_of_deaths", type="integer", nullable=true)
     */
    private $noOfDeaths;


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
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated", type="datetime")
     */
    private $lastUpdated;


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
     * @return int|null
     */
    public function getNoOfConfirmedCases()
    {
        return $this->noOfConfirmedCases;
    }

    /**
     * @param int|null $noOfConfirmedCases
     */
    public function setNoOfConfirmedCases(int $noOfConfirmedCases=null)
    {
        $this->noOfConfirmedCases = $noOfConfirmedCases == 0 ? null : $noOfConfirmedCases;
    }

    /**
     * @return int|null
     */
    public function getNoOfSuspectedCases()
    {
        return $this->noOfSuspectedCases;
    }

    /**
     * @param int|null $noOfSuspectedCases
     */
    public function setNoOfSuspectedCases(int $noOfSuspectedCases=null)
    {
        $this->noOfSuspectedCases = $noOfSuspectedCases == 0 ? null : $noOfSuspectedCases;
    }

    /**
     * @return int|null
     */
    public function getNoOfRecoveredCases()
    {
        return $this->noOfRecoveredCases;
    }

    /**
     * @param int|null $noOfRecoveredCases
     */
    public function setNoOfRecoveredCases(int $noOfRecoveredCases=null)
    {
        $this->noOfRecoveredCases = $noOfRecoveredCases == 0 ? null : $noOfRecoveredCases;
    }

    /**
     * @return int|null
     */
    public function getNoOfDeaths()
    {
        return $this->noOfDeaths;
    }

    /**
     * @param int|null $noOfDeaths
     */
    public function setNoOfDeaths(int $noOfDeaths=null)
    {
        $this->noOfDeaths = $noOfDeaths == 0 ? null : $noOfDeaths;
    }

    /**
     * @return Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param Province $province
     */
    public function setProvince(Province $province)
    {
        $this->province = $province;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @param \DateTime $lastUpdated
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }


}
