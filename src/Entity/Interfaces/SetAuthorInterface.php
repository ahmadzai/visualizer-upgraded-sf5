<?php


namespace App\Entity\Interfaces;


use Symfony\Component\Security\Core\User\UserInterface;

interface SetAuthorInterface
{
    public function setAuthor(UserInterface $user): SetAuthorInterface;
}