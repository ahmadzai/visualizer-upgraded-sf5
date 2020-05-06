<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCreateListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(User $user, LifecycleEventArgs $args)
    {
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        if($loggedInUser instanceof UserInterface &&
            $user->getUsername() !== $loggedInUser->getUsername()) {
            // just to make sure in update profile this should not be set again
            $user->setAuthor($loggedInUser);
        }
    }

}