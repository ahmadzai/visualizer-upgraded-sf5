<?php
/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 10/13/2017
 * Time: 10:05 PM
 */

namespace App\Repository\CoverageCatchup;

use App\Entity\CoverageData;
use Doctrine\Persistence\ManagerRegistry;


class CoverageRepository extends ChartRepo {


    protected $DQL = " cvr.tallyType as Tally_Type,
                    sum(cvr.vialsReceived) as RVials, sum(cvr.vialsUsed) as UVials,
                   ((COALESCE(sum(cvr.vialsUsed), 0)*20 -
                   (COALESCE(sum(cvr.noChildInHouseVac), 0) +COALESCE(sum(cvr.noChildOutsideVac), 0)+
                    COALESCE(sum(cvr.noChildVacByTT), 0) + COALESCE(sum(cvr.noVacNomad), 0) +
                    COALESCE(sum(cvr.noAbsentSameDayVacByTeam), 0) +
                    COALESCE(sum(cvr.noAbsentNotSameDayVacByTeam), 0) +
                    COALESCE(sum(cvr.noNSSVacByTeam),0) +
                    COALESCE(sum(cvr.noRefusalVacByTeam),0))) /
                    (COALESCE(sum(cvr.vialsUsed), 0) *20) * 100)
                    as VacWastage,
                  (COALESCE(sum(cvr.targetChildren),0))/4 as Target,
                  (COALESCE(sum(cvr.noChildInHouseVac),0)+
                    COALESCE(sum(cvr.noChildOutsideVac),0)+
                    sum( case WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                        THEN 
                        (COALESCE(cvr.noAbsentSameDay,0) + 
                        COALESCE(cvr.noAbsentNotSameDay,0) + 
                        COALESCE(cvr.noNSS,0) + 
                        COALESCE(cvr.noRefusal,0))
                        ELSE 0
                    end)
                    ) as CalcTarget,
                  (COALESCE(sum(cvr.noChildVacByTT), 0) + COALESCE(sum(cvr.noVacNomad), 0) +
                    COALESCE(sum(cvr.noChildInHouseVac),0)+COALESCE(sum(cvr.noChildOutsideVac),0)+
                    COALESCE(sum(cvr.noAbsentSameDayVacByTeam),0)+ 
                    COALESCE(sum(cvr.noAbsentNotSameDayVacByTeam),0) +
                    COALESCE(sum(cvr.noNSSVacByTeam),0) + 
                    COALESCE(sum(cvr.noRefusalVacByTeam),0)) as TotalVac,  
                  sum(cvr.noChildInHouseVac) as InHouseVac, sum(cvr.noChildOutsideVac) as OutsideVac,
                  sum(COALESCE(cvr.noVacNomad, 0)) as VacNomad, 
                  sum(COALESCE(cvr.noChildVacByTT, 0)) as VacInPTTs, 
                  (COALESCE(sum(cvr.noAbsentSameDayFoundVac),0) + COALESCE(sum(cvr.noAbsentSameDayVacByTeam),0)+
                   COALESCE(sum(cvr.noAbsentNotSameDayFoundVac),0) + COALESCE(sum(cvr.noAbsentNotSameDayVacByTeam),0) + 
                   COALESCE(sum(cvr.noNSSFoundVac),0) + COALESCE(sum(cvr.noNSSVacByTeam),0) +
                   COALESCE(sum(cvr.noRefusalFoundVac),0) + COALESCE(sum(cvr.noRefusalVacByTeam),0)) as MissedVaccinated,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN (COALESCE(cvr.noAbsentSameDay,0) + COALESCE(cvr.noAbsentNotSameDay,0)) ELSE 0
                    END
                  ) as RegAbsent,
                  sum(COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                      COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                      COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                      COALESCE(cvr.noAbsentNotSameDayVacByTeam,0)) as VacAbsent,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN
                      (COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                       COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                       COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                       COALESCE(cvr.noAbsentNotSameDayVacByTeam,0))
                      ELSE
                       0
                    END   
                       ) as VacAbsent3Days,    
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 4)
                      THEN
                      (COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                       COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                       COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                       COALESCE(cvr.noAbsentNotSameDayVacByTeam,0))
                      ELSE
                       0
                    END   
                       ) as VacAbsentDay4,     
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN (COALESCE(cvr.noAbsentSameDay,0) + COALESCE(cvr.noAbsentNotSameDay,0)) ELSE 0
                    END
                  ) -
                  sum(COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                      COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                      COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                      COALESCE(cvr.noAbsentNotSameDayVacByTeam,0)) as RemAbsent,    
                      
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN COALESCE(cvr.noNSS,0) ELSE 0
                    END
                  ) as RegNSS,
                  sum(COALESCE(cvr.noNSSFoundVac,0) + COALESCE(cvr.noNSSVacByTeam,0)) as VacNSS,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN (COALESCE(cvr.noNSSFoundVac,0) + COALESCE(cvr.noNSSVacByTeam,0))
                      ELSE 0
                    END     
                   ) as VacNSS3Days,
                   sum(
                    CASE
                      WHEN (cvr.vacDay = 4)
                      THEN (COALESCE(cvr.noNSSFoundVac,0) + COALESCE(cvr.noNSSVacByTeam,0))
                      ELSE 0
                    END     
                   ) as VacNSSDay4,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN COALESCE(cvr.noNSS,0) ELSE 0
                    END
                  ) -
                  sum(COALESCE(cvr.noNSSFoundVac,0) + COALESCE(cvr.noNSSVacByTeam,0)) as RemNSS,

                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN COALESCE(cvr.noRefusal, 0) ELSE 0
                    END
                  ) as RegRefusal,
                  sum(COALESCE(cvr.noRefusalFoundVac,0) + COALESCE(cvr.noRefusalVacByTeam,0)) as VacRefusal,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN (COALESCE(cvr.noRefusalFoundVac,0) + COALESCE(cvr.noRefusalVacByTeam,0))
                      ELSE 0
                    END     
                   ) as VacRefusal3Days,
                   sum(
                    CASE
                      WHEN (cvr.vacDay = 4)
                      THEN (COALESCE(cvr.noRefusalFoundVac,0) + COALESCE(cvr.noRefusalVacByTeam,0))
                      ELSE 0
                    END     
                   ) as VacRefusalDay4,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN COALESCE(cvr.noRefusal, 0) ELSE 0
                    END
                  ) -
                  sum(COALESCE(cvr.noRefusalFoundVac,0) + COALESCE(cvr.noRefusalVacByTeam,0)) as RemRefusal,
                  sum(
                    CASE
                      WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                      THEN (COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                            COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                            COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                            COALESCE(cvr.noAbsentNotSameDayVacByTeam,0) + 
                            COALESCE(cvr.noNSSFoundVac,0) + 
                            COALESCE(cvr.noNSSVacByTeam,0) + 
                            COALESCE(cvr.noRefusalFoundVac,0) +
                            COALESCE(cvr.noRefusalVacByTeam,0))
                      ELSE 0
                    END     
                   ) as Recovered3Days,
                   sum(
                    CASE
                      WHEN (cvr.vacDay = 4)
                      THEN (COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                            COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                            COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                            COALESCE(cvr.noAbsentNotSameDayVacByTeam,0) + 
                            COALESCE(cvr.noNSSFoundVac,0) + 
                            COALESCE(cvr.noNSSVacByTeam,0) + 
                            COALESCE(cvr.noRefusalFoundVac,0) +
                            COALESCE(cvr.noRefusalVacByTeam,0))
                      ELSE 0
                    END     
                   ) as RecoveredDay4,
                  sum( case WHEN (cvr.vacDay = 1 OR cvr.vacDay = 2 OR cvr.vacDay = 3)
                            THEN 
                            (COALESCE(cvr.noAbsentSameDay,0) + 
                            COALESCE(cvr.noAbsentNotSameDay,0) + 
                            COALESCE(cvr.noNSS,0) + 
                            COALESCE(cvr.noRefusal,0))
                            ELSE 0
                        end) - 
                   sum ( 
                         COALESCE(cvr.noAbsentSameDayFoundVac,0) +
                         COALESCE(cvr.noAbsentSameDayVacByTeam,0) +
                         COALESCE(cvr.noAbsentNotSameDayFoundVac,0) +
                         COALESCE(cvr.noAbsentNotSameDayVacByTeam,0) + 
                         COALESCE(cvr.noNSSFoundVac,0) + 
                         COALESCE(cvr.noNSSVacByTeam,0) + 
                         COALESCE(cvr.noRefusalFoundVac,0) +
                         COALESCE(cvr.noRefusalVacByTeam,0))
                    as TotalRemaining     
                    ";


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoverageData::class);
        $this->setDQL($this->DQL);
        $this->setEntity("CoverageData");
    }

}
