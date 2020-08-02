<?php

namespace App\Repository;

use App\Entity\StaffPco;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * StaffPcoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StaffPcoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaffPco::class);
    }

    public function staffByRegion($region = 'all', $monthYear = null) {
        $condition = $region === 'all' ? '' : ' WHERE p.provinceRegion in (:region) ';
        $condition2 = $monthYear === null ? '' : ' pco.asOfMonth = :month AND pco.asOfYear = :year ';

        $finalCondition = '';
        if($condition === '' && $condition2 !== '') {
            $finalCondition = ' WHERE '.$condition2;
        } elseif ($condition !== '' && $condition2 === '') {
            $finalCondition = $condition;
        } elseif ($condition !== '' && $condition2 !== '') {
            $finalCondition = $condition . ' AND ' . $condition2;
        }

        $query = $this->getEntityManager()
            ->createQuery("SELECT CONCAT(p.provinceRegion, Concat(pco.asOfMonth, pco.asOfYear)) as joinKey,
                    p.provinceRegion as region, SUM(pco.noOfPco) as pcos, 
                        SUM(pco.noOfFemalePco) as femalePco
                     FROM AppBundle:StaffPco pco JOIN pco.province p ". $finalCondition."
                     GROUP BY pco.asOfMonth, pco.asOfYear, p.provinceRegion
                  ");

        if($region !== 'all') {
            $query->setParameter('region', $region);
        }

        if($monthYear !== null) {
            // $monthYear should come Mon-YEAR e.g. Mar-2020, let us divide it into two vars.
            $monthYear = explode("-", $monthYear);
            $query->setParameter('month',$monthYear[0]);
            $query->setParameter('year', $monthYear[1]);
        }

        return $query->getResult(Query::HYDRATE_SCALAR);
    }
}