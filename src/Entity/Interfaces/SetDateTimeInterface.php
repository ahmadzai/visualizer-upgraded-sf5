<?php


namespace App\Entity\Interfaces;


interface SetDateTimeInterface
{
    public function setCreatedAt(\DateTimeInterface $dateTime): SetDateTimeInterface;
}
