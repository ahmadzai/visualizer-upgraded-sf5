<?php
/**
 * Created by PhpStorm.
 * User: Awesome
 * Date: 9/13/2018
 * Time: 2:30 PM
 */

namespace App\Service;


use Symfony\Component\Finder\Finder;

class Maps
{


    /**
     * @param $path
     * @param string $shapeFile (province|district)
     * @return mixed
     */
    public static function loadGeoJson($path, $shapeFile = "province") {

        $finder = new Finder();
        $finder->files()->in($path.'/public/geojson');

        $name = 'province.json';
        if($shapeFile === 'district')
            $name = 'district.json';
        $finder->files()->name($name);
        //dd($finder);
        $contents = null;
        foreach ($finder as $file) {
            $contents = $file->getContents();
        }

        return json_decode($contents);
    }

}
