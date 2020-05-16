<?php
/**
 * Created by PhpStorm.
 * User: Awesome
 * Date: 9/9/2018
 * Time: 9:29 AM
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Exporter
{
    public static function exportCSV($data) {

        $rows = array();
        $first_row = $data[0];

        $cols_names = array_keys($first_row);
        $cols_names = implode(",", $cols_names);
        $rows[] = $cols_names;
        foreach ($data as $datum) {

            $rows[] = implode(',', $datum);
        }

        $fileName = date('Y-m-d-h-i-s');
        $fileName = "data-".$fileName.".csv";
        $content = implode("\n", $rows);
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );

        $response->headers->set('Content-Disposition', $disposition);


        return $response;
    }

}
