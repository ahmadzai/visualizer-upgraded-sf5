<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * RefusalComm
 *
 * @ORM\Table(name="polio_cases")
 * @ORM\Entity()
 */
class PolioCases
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $epid;

    /**
     *
     * @ORM\Column(type="date", nullable=true, name="onset_date")
     */
    private $onsetDate;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $sex;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $ageInMonths;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    private $noRoutineDoses;

    /**
     * @ORM\Column(type="integer", length=2, nullable=true)
     */
    private $noSiaDoses;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastOpvDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $stoolDate;

    /**
     * @ORM\Column(type="string", nullable=true, length=50)
     */
    private $cluster;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $linkage;

    /**
     * @ORM\Column(type="integer", length=5, nullable=true)
     */
    private $year;

    /**
     * @var District
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
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
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @return mixed
     */
    public function getEpid()
    {
        return $this->epid;
    }

    /**
     * @param mixed $epid
     */
    public function setEpid($epid)
    {
        $this->epid = $epid;
    }

    /**
     * @return mixed
     */
    public function getOnsetDate()
    {
        return $this->onsetDate;
    }

    /**
     * @param mixed $onsetDate
     */
    public function setOnsetDate($onsetDate)
    {
        $this->onsetDate = $onsetDate;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return mixed
     */
    public function getAgeInMonths()
    {
        return $this->ageInMonths;
    }

    /**
     * @param mixed $ageInMonths
     */
    public function setAgeInMonths($ageInMonths)
    {
        $this->ageInMonths = $ageInMonths;
    }

    /**
     * @return mixed
     */
    public function getNoRoutineDoses()
    {
        return $this->noRoutineDoses;
    }

    /**
     * @param mixed $noRoutineDoses
     */
    public function setNoRoutineDoses($noRoutineDoses)
    {
        $this->noRoutineDoses = $noRoutineDoses;
    }

    /**
     * @return mixed
     */
    public function getNoSiaDoses()
    {
        return $this->noSiaDoses;
    }

    /**
     * @param mixed $noSiaDoses
     */
    public function setNoSiaDoses($noSiaDoses)
    {
        $this->noSiaDoses = $noSiaDoses;
    }

    /**
     * @return mixed
     */
    public function getLastOpvDate()
    {
        return $this->lastOpvDate;
    }

    /**
     * @param mixed $lastOpvDate
     */
    public function setLastOpvDate($lastOpvDate)
    {
        $this->lastOpvDate = $lastOpvDate;
    }

    /**
     * @return mixed
     */
    public function getStoolDate()
    {
        return $this->stoolDate;
    }

    /**
     * @param mixed $stoolDate
     */
    public function setStoolDate($stoolDate)
    {
        $this->stoolDate = $stoolDate;
    }

    /**
     * @return mixed
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @param mixed $cluster
     */
    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
    }

    /**
     * @return mixed
     */
    public function getLinkage()
    {
        return $this->linkage;
    }

    /**
     * @param mixed $linkage
     */
    public function setLinkage($linkage)
    {
        $this->linkage = $linkage;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return District
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param District $district
     */
    public function setDistrict(District $district)
    {
        $this->district = $district;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}
