<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 10/13/2017
 * Time: 10:05 PM
 */

namespace App\Repository\CoverageCatchup;

use App\Entity\CatchupData;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;


class CatchupRepository extends ChartRepo {



    protected $DQL = " (SUM(COALESCE(cvr.regAbsent, 0)) + 
                               SUM(COALESCE(cvr.regNSS, 0)) + 
                               SUM(COALESCE(cvr.regRefusal,0))
                               ) AS RegMissed, 
                              SUM(COALESCE(cvr.regAbsent, 0)) as RegAbsent,
                              SUM(COALESCE(cvr.vacAbsent, 0)) as VacAbsent,
                              (SUM(COALESCE(cvr.regAbsent, 0)) - 
                               SUM(COALESCE(cvr.vacAbsent, 0))
                               ) as RemAbsent,
                              (SUM(COALESCE(cvr.vacAbsent, 0))/
                               SUM(COALESCE(cvr.regAbsent, 0))*100
                               ) as PerVacAbsent,
                              SUM(COALESCE(cvr.regNSS, 0)) as RegNSS,
                              SUM(COALESCE(cvr.vacNSS, 0)) as VacNSS,
                              (SUM(COALESCE(cvr.regNSS, 0)) -
                               SUM(COALESCE(cvr.vacNSS, 0))
                               )as RemNSS,
                              (SUM(COALESCE(cvr.vacNSS, 0))/
                               SUM(COALESCE(cvr.regNSS, 0))*100) as PerVacNSS,
                              SUM(COALESCE(cvr.regRefusal, 0)) as RegRefusal,
                              SUM(COALESCE(cvr.vacRefusal, 0)) as VacRefusal,
                              SUM(COALESCE(cvr.regRefusal, 0)) - SUM(COALESCE(cvr.vacRefusal, 0)) as RemRefusal,
                              (SUM(COALESCE(cvr.vacRefusal, 0))/SUM(COALESCE(cvr.regRefusal, 0))*100) as PerVacRefusal,
                              SUM(COALESCE(cvr.unRecorded, 0)) as UnRecorded,
                              SUM(COALESCE(cvr.vacUnRecorded, 0)) as VacUnRecorded,
                              SUM(COALESCE(cvr.unRecorded, 0)) - SUM(COALESCE(cvr.vacUnRecorded, 0)) as RemUnRecorded,
                              SUM(COALESCE(cvr.vacGuest, 0)) as VacGuest,
                              SUM(COALESCE(cvr.vacAbsent, 0)) + SUM(COALESCE(cvr.vacNSS, 0)) + SUM(COALESCE(cvr.vacRefusal, 0)) AS
                              TotalRecovered,
                              ((SUM(COALESCE(cvr.regAbsent, 0)) + 
                               SUM(COALESCE(cvr.regNSS, 0)) + 
                               SUM(COALESCE(cvr.regRefusal,0))
                               ) - (SUM(COALESCE(cvr.vacAbsent, 0)) + 
                                    SUM(COALESCE(cvr.vacNSS, 0)) + 
                                    SUM(COALESCE(cvr.vacRefusal, 0)))
                               ) As TotalRemaining,     
                              (SUM(COALESCE(cvr.vacAbsent, 0)) + 
                               SUM(COALESCE(cvr.vacNSS, 0)) + 
                               SUM(COALESCE(cvr.vacRefusal, 0)) +
                               SUM(COALESCE(cvr.vacUnRecorded, 0)) + 
                               SUM(COALESCE(cvr.vacGuest, 0))
                               ) AS TotalVac,
                              ((SUM(COALESCE(cvr.vacAbsent, 0)) + 
                                SUM(COALESCE(cvr.vacNSS, 0)) + 
                                SUM(COALESCE(cvr.vacRefusal, 0)))/
                                (SUM(COALESCE(cvr.regAbsent, 0)) + 
                                 SUM(COALESCE(cvr.regNSS, 0)) + 
                                 SUM(COALESCE(cvr.regRefusal, 0))) * 100)
                               AS PerRecovered
                            ";



    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatchupData::class);
        $this->setDQL($this->DQL);
        $this->setEntity("CatchupData");
    }

}
