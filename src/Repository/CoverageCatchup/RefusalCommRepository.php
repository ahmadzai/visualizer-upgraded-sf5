<?php


namespace App\Repository\CoverageCatchup;

use App\Entity\RefusalComm;
use Doctrine\Persistence\ManagerRegistry;

class RefusalCommRepository extends ChartRepo
{

    protected $DQL = " cvr.campaignPhase as Campaign_Phase,
                        sum(COALESCE(cvr.regRefusal, 0)) as refusalAfterDay5, 
                       sum(COALESCE(cvr.refusalVacInCatchup, 0)) as refusalVacInCatchup,
                       sum(COALESCE(cvr.refusalVacByCRC, 0)) as refusalVacByCRC, 
                       sum(COALESCE(cvr.refusalVacByRC, 0)) as refusalVacByRC,
                       sum(COALESCE(cvr.refusalVacByCIP, 0)) as refusalVacByCIP, 
                       sum(COALESCE(cvr.refusalVacBySeniorStaff, 0)) as refusalVacBySenior,
                       sum((COALESCE(cvr.refusalVacByCRC, 0)) + 
                        (COALESCE(cvr.refusalVacByRC, 0)) + 
                        (COALESCE(cvr.refusalVacByCIP, 0)) + 
                        (COALESCE(cvr.refusalVacBySeniorStaff, 0))
                        ) as totalRefusalVacByRefComm
                    ";



    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefusalComm::class);
        $this->setDQL($this->DQL);
        $this->setEntity("RefusalComm");
    }

}
