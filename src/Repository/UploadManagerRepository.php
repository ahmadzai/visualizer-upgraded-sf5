<?php


namespace App\Repository;


use App\Entity\UploadManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class UploadManagerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadManager::class);
    }

    public function findByFile(string $entityName, int $fileId, array $columns) {
        $queryString = [];
        foreach($columns as $column) {
            $queryString[] = 'targetEntity.'.$column;
        }

        $queryString = implode(", ", $queryString);
        $query = $this ->getEntityManager()
            ->createQuery("SELECT ".$queryString." FROM App:".$entityName." as targetEntity 
                            WHERE targetEntity.file = (:file)");

        $query->setParameter('file', $fileId);
        return $query->getResult(Query::HYDRATE_ARRAY);
    }

    public function findOneByCriteria(string $entityName, $criteria, $returnBool = true)
    {
        $where = [];
        $params = [];
        $counter = 0;
        foreach ($criteria as $column=>$value) {
            $condition = "targetEntity.".$column;
            if(($value === null || $value === ""))
                $condition .= " IS NULL ";
            else {
                $param = "param".$counter;
                $condition .= " = (:".$param.")";
                $params[$param] = $value;
            }
            $where[] = $condition;
            $counter ++;
        }
        $whereCondition = implode(" AND ", $where);

        if($returnBool) {
            $query = $this ->getEntityManager()
                ->createQuery("SELECT count(targetEntity.id) FROM ".$entityName." as targetEntity 
                            WHERE ".$whereCondition);

            $query->setParameters($params);
            return $query->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR) > 0;
        } else {
            $query = $this ->getEntityManager()
                ->createQuery("SELECT targetEntity FROM ".$entityName." as targetEntity 
                            WHERE ".$whereCondition);

            $query->setParameters($params);
            $query->setMaxResults(1);
            return $query->getResult();
        }

    }

}