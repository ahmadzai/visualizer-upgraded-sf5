<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign
 *
 * @ORM\Table(name="heatmap_benchmark")
 * @ORM\Entity(repositoryClass="App\Repository\HeatmapRepository")
 */
class HeatmapBenchmark
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $minValue;

    /**
     * @ORM\Column(type="float")
     */
    private $maxValue;

    /**
     * @ORM\Column(type="float")
     */
    private $midStop;

    /**
     * @ORM\Column(type="string")
     */
    private $minColor;

    /**
     * @ORM\Column(type="string")
     */
    private $maxColor;

    /**
     * @ORM\Column(type="string")
     */
    private $midColor;

    /**
     * @ORM\Column(type="string")
     */
    private $dataSource;

    /**
     * @ORM\Column(type="string")
     */
    private $indicator;

    /**
     * @return mixed
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param mixed $minValue
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @return mixed
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param mixed $maxValue
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @return mixed
     */
    public function getMidStop()
    {
        return $this->midStop;
    }

    /**
     * @param mixed $midStop
     */
    public function setMidStop($midStop)
    {
        $this->midStop = $midStop;
    }

    /**
     * @return mixed
     */
    public function getMinColor()
    {
        return $this->minColor;
    }

    /**
     * @param mixed $minColor
     */
    public function setMinColor($minColor)
    {
        $this->minColor = $minColor;
    }

    /**
     * @return mixed
     */
    public function getMaxColor()
    {
        return $this->maxColor;
    }

    /**
     * @param mixed $maxColor
     */
    public function setMaxColor($maxColor)
    {
        $this->maxColor = $maxColor;
    }

    /**
     * @return mixed
     */
    public function getMidColor()
    {
        return $this->midColor;
    }

    /**
     * @param mixed $midColor
     */
    public function setMidColor($midColor)
    {
        $this->midColor = $midColor;
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param mixed $dataSource
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return mixed
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @param mixed $indicator
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



}
