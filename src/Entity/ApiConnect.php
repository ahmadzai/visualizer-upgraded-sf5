<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * ApiConnect
 * @ORM\Entity()
 * @ORM\Table(name="api_connect")
 * @package App\Entity
 */
class ApiConnect
{
    /**
     * @var string
     *
     * @ORM\Column(name="api_service_name", type="string", nullable=true)
     */
    private $apiServiceName;

    /**
     * @var string
     *
     * @ORM\Column(name="api_service_url", type="string", nullable=true)
     */
    private $apiServiceUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", nullable=true)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="api_user", type="string",nullable=true)
     */
    private $apiUser;

    /**
     * @var string
     *
     * @ORM\Column(name="api_pass", type="string", nullable=true)
     */
    private $apiPass;

    /**
     * @var string
     *
     * @ORM\Column(name="api_login_type", type="string", nullable=true)
     */
    private $apiLoginType;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @return string
     */
    public function getApiServiceName()
    {
        return $this->apiServiceName;
    }

    /**
     * @param string $apiServiceName
     */
    public function setApiServiceName($apiServiceName)
    {
        $this->apiServiceName = $apiServiceName;
    }

    /**
     * @return string
     */
    public function getApiServiceUrl()
    {
        return $this->apiServiceUrl;
    }

    /**
     * @param string $apiServiceUrl
     */
    public function setApiServiceUrl($apiServiceUrl)
    {
        $this->apiServiceUrl = $apiServiceUrl;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiUser()
    {
        return $this->apiUser;
    }

    /**
     * @param string $apiUser
     */
    public function setApiUser($apiUser)
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @return string
     */
    public function getApiPass()
    {
        return $this->apiPass;
    }

    /**
     * @param string $apiPass
     */
    public function setApiPass($apiPass)
    {
        $this->apiPass = $apiPass;
    }

    /**
     * @return string
     */
    public function getApiLoginType()
    {
        return $this->apiLoginType;
    }

    /**
     * @param string $apiLoginType
     */
    public function setApiLoginType($apiLoginType)
    {
        $this->apiLoginType = $apiLoginType;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
