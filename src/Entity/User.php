<?php

namespace App\Entity;

use App\Entity\Interfaces\SetAuthorInterface;
use App\Entity\Interfaces\SetDateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, SetDateTimeInterface, SetAuthorInterface
{
    const ROLES = [
        'COVID_VIEWER' => 'ROLE_COVID19_VIEWER',
        'PARTNER' => 'ROLE_PARTNER',
        'VIEWER' => 'ROLE_NORMAL_USER',
        'COVID_EDITOR' => 'ROLE_COVID19_EDITOR',
        'RESTRICTED_EDITOR' => 'ROLE_RESTRICTED_EDITOR',
        'EDITOR' => 'ROLE_EDITOR',
        'ADMIN' => 'ROLE_ADMIN',
        'SUPER_ADMIN' => 'ROLE_SUPER_ADMIN'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *     message="Passwords does not match"
     * )
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Assert\Email(message="Please enter a valid email")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $jobTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $mobileNo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasApiAccess;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"password"})
     */
    private $lastPasswordChangeAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="accounts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="author")
     */
    private $accounts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $fullName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastInteractiveLogin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastAuthentication;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    /**
     * @param string $retypedPassword
     */
    public function setRetypedPassword($retypedPassword): self
    {
        $this->retypedPassword = $retypedPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param UserInterface $email
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /**
     * @param UserInterface $jobTitle
     */
    public function setJobTitle($jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param UserInterface $country
     */
    public function setCountry($country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param UserInterface $city
     */
    public function setCity($city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getMobileNo(): ?string
    {
        return $this->mobileNo;
    }

    /**
     * @param UserInterface $mobileNo
     */
    public function setMobileNo($mobileNo): self
    {
        $this->mobileNo = $mobileNo;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHasApiAccess(): ?bool
    {
        return $this->hasApiAccess;
    }

    /**
     * @param UserInterface $hasApiAccess
     */
    public function setHasApiAccess($hasApiAccess): self
    {
        $this->hasApiAccess = $hasApiAccess;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastPasswordChangeAt(): ?\DateTime
    {
        return $this->lastPasswordChangeAt;
    }

    /**
     * @param UserInterface $lastPasswordChangeAt
     */
    public function setLastPasswordChangeAt($lastPasswordChangeAt): self
    {
        $this->lastPasswordChangeAt = $lastPasswordChangeAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param UserInterface $createdAt
     */
    public function setCreatedAt($createdAt): SetDateTimeInterface
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor($author): SetAuthorInterface
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getLastInteractiveLogin(): ?\DateTimeInterface
    {
        return $this->lastInteractiveLogin;
    }

    public function setLastInteractiveLogin(?\DateTimeInterface $lastInteractiveLogin): self
    {
        $this->lastInteractiveLogin = $lastInteractiveLogin;

        return $this;
    }

    public function getLastAuthentication(): ?\DateTimeInterface
    {
        return $this->lastAuthentication;
    }

    public function setLastAuthentication(?\DateTimeInterface $lastAuthentication): self
    {
        $this->lastAuthentication = $lastAuthentication;

        return $this;
    }
}
