<?php


namespace App\EventSubscriber;


use Symfony\Contracts\EventDispatcher\Event;

class CopyHfIndicatorEvent extends Event
{
    const NAME = "hfindicator.copied";

    private $data;

    public function __construct(?array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

}