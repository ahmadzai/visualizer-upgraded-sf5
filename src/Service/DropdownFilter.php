<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class DropdownFilter
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {

        $this->manager = $manager;
    }

    public function filter(string $entity, array $filterBy = null)
    {
        if($filterBy === null)
            return $this->manager->getRepository("App:".$entity)->findAll();

       return $this->manager->getRepository("App:".$entity)->findBy($filterBy);
    }

    public function getEntityObject(string $entity, array $searchBy) {
        return $this->manager->getRepository("App:".$entity)->findOneBy($searchBy);
    }

}