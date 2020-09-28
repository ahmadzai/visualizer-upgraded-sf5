<?php


namespace App\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class HfIndicatorCopyListener
{
    protected $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onHfIndicatorCopied(Event $event) {
        $data = $event->getData();

        $selectOld = $this->em->getRepository('App:BphsHfIndicator')->getAssignedIndicators($data['oldYear']);
        dd($selectOld);

    }

}