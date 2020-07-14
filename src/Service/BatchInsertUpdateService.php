<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class BatchInsertUpdateService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    public function batchInsert($tableName, $records) {
        $conn = $this->manager->getConnection();
        $query = "INSERT INTO bphs_indicator_reach 
                  (bphs_hf_indicator, hf_code_id, indicator_id, reach, report_month, report_year, created_at, 
                  created_by_id, updated_by_id, updated_at) VALUES (?), (?), (?), (?), (?), (?), (?), (?), (?), (?)";



    }

}