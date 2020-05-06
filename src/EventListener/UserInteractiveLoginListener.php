<?php


namespace App\EventListener;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserInteractiveLoginListener
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        if($user instanceof UserInterface) {
            $user->setLastInteractiveLogin(new \DateTime());
            $this->manager->persist($user);
            $this->manager->flush();
        }
    }
}