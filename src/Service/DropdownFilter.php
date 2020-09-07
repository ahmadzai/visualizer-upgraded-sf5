<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DropdownFilter
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(EntityManagerInterface $manager, SessionInterface $session)
    {

        $this->manager = $manager;
        $this->session = $session;
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

    public function bphsIndicatorYears() {
        $q = $this->manager
            ->createQuery("SELECT DISTINCT hf.targetYear
                                FROM App:BphsHfIndicator as hf ORDER BY hf.targetYear DESC");

        $result = $q->getArrayResult();
        $resArray = array();
        foreach ($result as $item)
            $resArray[$item['targetYear']] = $item['targetYear'];

        return $resArray;
    }

    public function monthsArray() {
        return [
            'January' => 'Jan',
            'February' => 'Feb',
            'March' => 'Mar',
            'April' => 'Apr',
            'May' => 'May',
            'June' => 'Jun',
            'July' => 'Jul',
            'August' => 'Aug',
            'September' => 'Sep',
            'October' => 'Oct',
            'November' => 'Nov',
            'December' => 'Dec'
        ];
    }

    public function hfIndicators() {
        if(!$this->session->get('facilityYear'))
            return null;
        $params = $this->session->get('facilityYear');
        $facility = $this->getEntityObject('BphsHealthFacility', ['id'=>$params[0]]);
        $indicators = $this->filter('BphsHfIndicator',
            ['bphsHealthFacility'=>$facility, 'targetYear'=>$params[1]]);

        return $indicators;
    }

}