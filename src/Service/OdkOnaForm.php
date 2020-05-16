<?php
namespace App\Service;
/**
 * Created by PhpStorm.
 * User: Wazir Khan
 * Date: 13/07/2018
 * Time: 10:22 AM
 */
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class OdkOnaForm
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

    }

    /**
     * @param $serviceName
     * @return int
     */
    public function apiKey($serviceName)
    {
        $data = $this->em->createQuery(
            "SELECT api FROM App:ApiConnect api WHERE api.apiServiceName =:service"
            )
            ->setParameter('service', $serviceName)
            ->setMaxResults(1)
            ->getResult(Query::HYDRATE_OBJECT);

        return count($data)>0?$data[0]->getApiKey():0;
    }

    /**
     * @param $serviceName
     * @return int
     */
    public function apiUrl($serviceName)
    {
        $data = $this->em->createQuery(
            "SELECT api FROM App:ApiConnect api WHERE api.apiServiceName =:service"
            )
            ->setParameter('service', $serviceName)
            ->setMaxResults(1)
            ->getResult(Query::HYDRATE_OBJECT);

        return count($data)>0?$data[0]->getApiServiceUrl():0;
    }

    /**
     * @param $data
     * @param $table
     * @throws \Doctrine\DBAL\DBALException
     * @return boolean
     */
    public function saveData($data, $table) {
        // get connection
        $conn = $this->em->getConnection();
        $conn->exec("SET NAMES utf8");
        $sql = "INSERT INTO $table(odk_data) VALUES";
        //$values = "('".implode("'),('", $data)."')";
        $values = "";
        $data = str_replace("'", "", $data);
        $data = str_replace("/", "-", $data);
        $data = json_decode($data);
        $flag = 0;
        if(count($data) > 0) {
            foreach ($data as $datum) {
                $values .= "('" . str_replace('\\', '_',
                        json_encode($datum, JSON_UNESCAPED_UNICODE)) . "'),";
            }
            // just remove the last comma
            $values = rtrim($values, ",");
            $stmt = $conn->prepare($sql . $values);
            //$stmt->bindValue(1, $values);
            $flag = $stmt->execute();
        }
        $conn->close();
        return $flag;

    }

    /**
     * @param $table
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function checkTable($table)
    {
        $conn = $this->em->getConnection();
        $query = "SELECT 1
                  FROM information_schema.TABLES
                  WHERE (TABLE_SCHEMA = 'poliodb') AND (TABLE_NAME = '$table')";
        $res = $conn->prepare($query);
        $res->execute();
        $result = $res->fetchAll();
        $conn->close();
        return count($result) > 0 ? true: false;

    }

    /**
     * @param $table
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countRows($table) {
        $query = "SELECT count(*) as total_rows, 
                  max(timestamp(odk_data->>\"$._submission_time\")) as last_submission_date,
                  max(last_updated) as last_updated
                  FROM $table";
        $conn = $this->em->getConnection();
        $res = $conn->prepare($query);
        $res->execute();
        $result = $res->fetchAll();
        $conn->close();
        return $result;
    }

    /**
     * @param $table
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createTable($table) {
        $index = $table."_index_odk_data";
        $query = "CREATE TABLE $table (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    odk_data JSON DEFAULT NULL,
                    last_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
                    PRIMARY KEY (id),
                    KEY $index (id)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $conn = $this->em->getConnection();
        $res = $conn->exec($query);

        $conn->close();
        return $res;
    }

    /**
     * @param $url
     * @param $apiKey
     * @return mixed
     */
    public function getApiData($url, $apiKey)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true,         // return web page
            CURLOPT_HEADER         => false,        // don't return headers
            CURLOPT_FOLLOWLOCATION => true,         // follow redirects
            CURLOPT_HTTPHEADER	   => [
                'Authorization: Token '.$apiKey,
                'Accept: application/json',
                'Content-Type: application/x-json; Charset=utf-8'
            ],
            CURLOPT_ENCODING       => "",           // handle all encodings
            CURLOPT_USERAGENT      => "polio_vaccine",     // who am i
            CURLOPT_AUTOREFERER    => true,         // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
            CURLOPT_TIMEOUT        => 120,          // timeout on response
            CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
            //CURLOPT_POST            => 0,            // i am sending post data
            //CURLOPT_POSTFIELDS  =>  $curl_data,    // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false,        //
            CURLOPT_VERBOSE        => 1                //
        );

        $ch = curl_init($url);
        curl_setopt_array($ch,$options);
        $content = curl_exec($ch);
        //$err     = curl_errno($ch);
        //$errmsg  = curl_error($ch) ;
        //$header  = curl_getinfo($ch);


        //  $header['errno']   = $err;
        //  $header['errmsg']  = $errmsg;
        //  $header['content'] = $content;
        //$output = curl_exec($ch);
        curl_close($ch);
        return $content;
    }



}
