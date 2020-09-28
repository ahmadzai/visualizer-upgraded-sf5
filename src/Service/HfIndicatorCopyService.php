<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class HfIndicatorCopyService
{
    protected $em;
    protected $importer;

    public function __construct(EntityManagerInterface $em, Importer $importer)
    {
        $this->em = $em;
        $this->importer = $importer;
    }

    public function doCopy(?array $data) {

        $selectOld = $this->em->getRepository('App:BphsHfIndicator')
            ->findByYear($data['oldYear']);

        $targetYear = $data['newYear'];
        $newArray = [];
        foreach($selectOld as $item) {
            $newItem = [];
            foreach($item as $index=>$value)
            {
                $newItem[$index] = $index === "targetYear" ? $targetYear : $value;

            }
            $newArray[] = $newItem;
        }

        $className = "App\\Entity\\BphsHfIndicator";

        return $this->importer->processData($className, $newArray, null, -1,
            [
                'uniqueCols' => ["bphs_health_facility", "bphs_indicator", "target_year"],
                'entityCols' => ["bphs_health_facility", "bphs_indicator"],
                'updateAbleCols'=>['annual_target']
            ],
            null,
            null
        );

    }

}