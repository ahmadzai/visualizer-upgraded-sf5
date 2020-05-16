<?php
namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 11/12/2016
 * Time: 6:44 PM
 */

class Charts
{


    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $entity Entity Class Name (within the current bundle)
     * @param $function function in that entity
     * @param $parameters parameters for that function
     * @return mixed
     */
    public function chartData($entity, $function, $parameters, $secondParam = null) {
        $data = $this->em->getRepository('App:'.$entity)
            ->callMe($function, $parameters, $secondParam);
        return $data;
    }

    /***
     * @param $months array of months
     * @return order_months
     */
    function orderMonths($months) {
        $order_months = array();
        if(is_array($months) && count($months) > 0) {
            $temp = array();
            foreach($months as $month) {
                $m = date_parse($month);
                $temp[$m['month']] = $month;
            }
            ksort($temp);
            $order_months = $temp;
        }

        return $order_months;
    }

    function shortMonth($month) {
        $months = array('January'=>'Jan',
            'February'=>'Feb',
            'March'=>'Mar',
            'April'=>'Apr',
            'May'=>'May',
            'June'=>'Jun',
            'July'=>'Jul',
            'August'=>'Aug',
            'September'=>'Sept',
            'October'=>'Oct',
            'November'=>'Nov',
            'December'=>'Dec');
        return array_key_exists($month, $months) ? $months[$month] : $month;
    }

    function shortYear($year)
    {
        return strlen($year) == 4 ? substr($year, 2, 2) : $year;
    }

    /***
     * @param mixed: $cat1 array('column'=>'NameOfColumn', 'substitute'=>'Substitute') top level category, only array of length 2
     * @param mixed: $cat2 array('column'=>'NameOfColumn', 'substitute'=>'Substitute') level category, the substitute should be an
     * array which max length should be 3, the first index name will be col1, the second should be col2 and the third one should be short:true/false.
     * it's possible to have only one index: e.g. col1.
     * @param mixed: $indicators array('column'=>'NameOfColumn', 'substitute'=>'Substitute') string array indicators
     * @param mixed: $data
     * @param bool: $sortCategories
     * @return mixed: formatted array
     */
    function chartData2Categories($cat1, $cat2, $indicators, $data, $sortCategories = true) {

        // check if all the parameters are okay
        if(count($data) > 0 && count($cat1)>0 && count($cat2)>0 && count($indicators)>0) {
            // as the data is flat we have to find first the two categories
            $tmp_top_cat = array();
            $tmp_second_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat1['column']]; // $cat1/2[0] must be the column name of the categories
                $tmp_second_cat[] = $temp_d[$cat2['column']];
            }

            // we just need the unique categories
            $top_cat = array_unique($tmp_top_cat);
            $second_cat = array_unique($tmp_second_cat);

            // by default the categories will be sorted ASC
            if($sortCategories) {
                sort($top_cat);
                sort($second_cat);
            }

            // the data that should be returned
            $r_data = array();

            // this array will keep the data for each indicator
            $data_indicators = array();

            // temp variable for keeping the category
            $temp_cat = array();
            foreach ($top_cat as $t_c) {
                $sub_cat = array();
                // set the substitute for cat1
                $top_cat_substitute = $t_c;
                foreach ($second_cat as $s_c) {
                    //$sub_cat = array();
                    foreach ($data as $val) {
                        if ($val[$cat1['column']] === $t_c && $val[$cat2['column']] === $s_c) {
                            $top_cat_substitute = array_key_exists('substitute',$cat1) && !is_array($cat1['substitute'])
                                                  ? $val[$cat1['substitute']] : $t_c;

                            $substitue = array_key_exists('substitute', $cat2)?$cat2['substitute']:$s_c;
                            $col1 = is_array($substitue) ? (array_key_exists('col1', $substitue)? $substitue['col1'] : null) : $substitue;
                            $col2 = is_array($substitue) ? (array_key_exists('col2', $substitue)? $substitue['col2'] : null) : null;
                            $short = is_array($substitue) ? (array_key_exists('short', $substitue)? $substitue['short'] : null) : null;

                            // the value of short should be: my (month/year), m(month), y(year)
                            $second_part = $col2!==null?"-".($short=='my'||$short=='y'?$this->shortYear($val[$col2]):$val[$col2]):'';
                            $first_part = ($short=='my'||$short=='m'? $this->shortMonth($val[$col1]):$col1);
                            $sub_cat[] = $first_part.$second_part;
                                         //
                            foreach($indicators as $key=>$indicator) {
                                $finalValue = $val[$key] == 'null' ? null : (int)$val[$key];
                                if($finalValue !== null)
                                    $finalValue = $finalValue < 0 ? 0 : $finalValue;
                                $data_indicators[$key][] = $finalValue;

                                //$data_indicators[$key][] = $val[$key] == 'null' ? null : (int)$val[$key];
                            }

                        }

                    }
                }
                $temp_cat[] = array('name' => $top_cat_substitute, 'categories' => $sub_cat);
            }
            //$cat['categories'] = $temp_cat;
            $r_data['categories'] = $temp_cat;
//            $data['series'] = array(array('name'=>'Refusal', 'data' => $refusal),
//                array('name'=>'Sleep_NewBorn', 'data'=>$sleep),
//                array('name'=>'Missed', 'data' => $remaining));
            $ser = array();
            foreach ($indicators as $key=>$ind) {
                $ser[] = array('name'=>ucfirst($ind), 'data' => $data_indicators[$key]);
            }

            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['series'=>null];
    }

    /***
     * @param mixed: $cat1 array('column'=>'NameOfColumn', 'substitute'=>'Substitute') top level category, only array of length 2
     * @param mixed: $cat2 array('column'=>'NameOfColumn', 'substitute'=>'Substitute')
     * @param mixed: $cat3 array('column'=>'NameOfColumn', 'substitute'=>'Substitute') level category, the substitute should be an
     * array which max length should be 3, the first index name will be col1, the second should be col2 and the third one should be short:true/false.
     * it's possible to have only one index: e.g. col1.
     * @param mixed: $indicators array('column'=>'NameOfColumn', 'substitute'=>'Substitute') string array indicators
     * @param mixed: $data
     * @param bool: $sortCategories
     * @return mixed: formatted array
     */
    function chartData3Categories($cat1, $cat2, $cat3, $indicators, $data, $sortCategories = true) {

        // check if all the parameters are okay
        if(count($data) > 0 && count($cat1)>0 && count($cat2)>0 && count($indicators)>0 && count($cat3)>0) {
            // as the data is flat we have to find first the two categories
            $tmp_top_cat = array();
            $tmp_second_cat = array();
            $temp_first_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat2['column']]; // $cat1/2[0] must be the column name of the categories
                $temp_first_cat[] = $temp_d[$cat1['column']];
                $tmp_second_cat[] = $temp_d[$cat3['column']];
            }

            // we just need the unique categories
            $first_cat = array_unique($temp_first_cat);
            $top_cat = array_unique($tmp_top_cat);
            $second_cat = array_unique($tmp_second_cat);

            // by default the categories will be sorted ASC
            if($sortCategories) {
                sort($first_cat);
                sort($top_cat);
                sort($second_cat);
            }

            // the data that should be returned
            $r_data = array();

            // this array will keep the data for each indicator
            $data_indicators = array();

            // temp variable for keeping the category

            $final_cat = array();
            foreach ($first_cat as $f_c) {
                $temp_cat = array();
                $first_cat_substitute = $f_c;
                foreach ($top_cat as $t_c) {
                    $sub_cat = array();
                    // set the substitute for cat1
                    $top_cat_substitute = $t_c;
                    foreach ($second_cat as $s_c) {
                        //$sub_cat = array();
                        foreach ($data as $val) {
                            if ($val[$cat1['column']] == $f_c && $val[$cat2['column']] === $t_c && $val[$cat3['column']] === $s_c) {
                                $top_cat_substitute = array_key_exists('substitute', $cat2) && !is_array($cat2['substitute'])
                                    ? $val[$cat2['substitute']] : $t_c;

                                $first_cat_substitute = array_key_exists('substitute', $cat1) && !is_array($cat1['substitute'])
                                    ? $val[$cat1['substitute']] : $f_c;

                                $substitue = array_key_exists('substitute', $cat3) ? $cat3['substitute'] : $s_c;
                                $col1 = is_array($substitue) ? (array_key_exists('col1', $substitue) ? $substitue['col1'] : null) : $substitue;
                                $col2 = is_array($substitue) ? (array_key_exists('col2', $substitue) ? $substitue['col2'] : null) : null;
                                $short = is_array($substitue) ? (array_key_exists('short', $substitue) ? $substitue['short'] : null) : null;

                                // the value of short should be: my (month/year), m(month), y(year)
                                $second_part = $col2 !== null ? "-" . ($short == 'my' || $short == 'y' ? $this->shortYear($val[$col2]) : $val[$col2]) : '';
                                $first_part = ($short == 'my' || $short == 'm' ? $this->shortMonth($val[$col1]) : $val[$col1]);
                                $sub_cat[] = $first_part . $second_part;
                                //
                                foreach ($indicators as $key => $indicator) {
                                    $finalValue = $val[$key] == 'null' ? null : (int)$val[$key];
                                    if($finalValue !== null)
                                        $finalValue = $finalValue < 0 ? 0 : $finalValue;
                                    $data_indicators[$key][] = $finalValue;
                                    //$data_indicators[$key][] = $val[$key] == 'null' ? null : (int)$val[$key];
                                }

                            }

                        }
                    }
                    $temp_cat[] = array('name' => $top_cat_substitute, 'categories' => $sub_cat);
                }
                $final_cat[] = array('name'=>$first_cat_substitute, 'categories'=>$temp_cat);
            }
            //$cat['categories'] = $temp_cat;
            $r_data['categories'] = $final_cat;
//            $data['series'] = array(array('name'=>'Refusal', 'data' => $refusal),
//                array('name'=>'Sleep_NewBorn', 'data'=>$sleep),
//                array('name'=>'Missed', 'data' => $remaining));
            $ser = array();
            foreach ($indicators as $key=>$ind) {
                $ser[] = array('name'=>ucfirst($ind), 'data' => $data_indicators[$key]);
            }

            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['series'=>null];
    }

    /***
     * @param mixed: $cat1 array('column'=>'NameOfColumn', 'substitute'=>'Substitute') top level category
     * @param mixed: $indicators array('column'=>'NameOfColumn', 'substitute'=>'Substitute') string array indicators
     * @param mixed: $data
     * @return mixed: formatted array
     */
    function chartData1Category($cat1, $indicators, $data, $sortCategories = true) {

        if(count($data) > 0) {
            $tmp_top_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat1['column']];
            }

            $top_cat = array_unique($tmp_top_cat);
            if($sortCategories) {
                natsort($top_cat);
            }
            $r_data = array();
            //$cat = array();
            $data_indicators = array();
            $temp_cat = array();
            foreach ($top_cat as $t_c) {


                //$sub_cat = array();
                $new_cat = $t_c;
                foreach ($data as $val) {
                    if ($val[$cat1['column']] === $t_c) {

                        $substitute = array_key_exists('substitute', $cat1)?$cat1['substitute']:$t_c;
                        $new_cat = $substitute;
                        if($substitute !== $t_c) {
                            $col1 = is_array($substitute) ? (array_key_exists('col1', $substitute) ? $substitute['col1'] : null) : $substitute;
                            $col2 = is_array($substitute) ? (array_key_exists('col2', $substitute) ? $substitute['col2'] : null) : null;
                            $short = is_array($substitute) ? (array_key_exists('short', $substitute) ? $substitute['short'] : null) : null;

                            // the value of short should be: my (month/year), m(month), y(year)
                            $second_part = $col2 !== null ? "-" . ($short == 'my' || $short == 'y' ? $this->shortYear($val[$col2]) : $val[$col2]) : '';
                            $first_part = $short !== null ? ($short == 'my' || $short == 'm' ? $this->shortMonth($val[$col1]) : $val[$col1]) : $val[$col1];
                            $new_cat = $first_part.$second_part;
                        }

                        foreach($indicators as $key=>$indicator) {
                            $finalValue = $val[$key] == 'null' ? null : (int)$val[$key];
                            if($finalValue !== null)
                                $finalValue = $finalValue < 0 ? 0 : $finalValue;
                            $data_indicators[$key][] = $finalValue;
                        }

                    }

                }

                $temp_cat[] = $new_cat;
            }
            //$cat['categories'] = $temp_cat;
            $r_data['categories'] = $temp_cat;
            $ser = array();
            foreach ($indicators as $key=>$ind) {
                $ser[] = array('name'=>ucfirst($ind), 'data' => $data_indicators[$key]);
            }

            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['series'=>null];
    }

    /***
     * @param mixed: $cat1 array('column'=>'NameOfColumn', 'substitute'=>'Substitute') top level category
     * @param mixed: $indicator array('name'=>'NameOfColumn', 'label'=>'labelName') string array indicators
     * @param mixed: $data
     * @param bool: $sortCategories
     * @return mixed: formatted array
     */
    function pieChartData($cat1, $indicator, $data, $sortCategories = true) {

        if(count($data) > 0) {
            $tmp_top_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat1['column']];
            }

            $top_cat = array_unique($tmp_top_cat);
            if($sortCategories) {
                sort($top_cat);
            }
            $r_data = array();
            //$cat = array();
            $data_indicators = array();

            foreach ($top_cat as $t_c) {

                foreach ($data as $val) {
                    if ($val[$cat1['column']] === $t_c) {

                        $substitute = array_key_exists('substitute', $cat1)?$cat1['substitute']:$t_c;
                        $col1 = is_array($substitute) ? (array_key_exists('col1', $substitute)? $substitute['col1'] : null) : $substitute;
                        $col2 = is_array($substitute) ? (array_key_exists('col2', $substitute)? $substitute['col2'] : null) : null;
                        $short = is_array($substitute) ? (array_key_exists('short', $substitute)? $substitute['short'] : null) : null;

                        // the value of short should be: my (month/year), m(month), y(year)
                        $second_part = $col2!==null?"-".($short=='my'||$short=='y'?$this->shortYear($val[$col2]):$val[$col2]):'';
                        $first_part = ($short=='my'||$short=='m'? $this->shortMonth($val[$col1]):$val[$col1]);
                        $new_cat = $first_part.$second_part;

                        $data_indicators[] = array('name'=>$new_cat, 'y'=>$val[$indicator['name']] == 'null' ? null : (int)$val[$indicator['name']]);

                    }

                }

            }

            $ser[] = array('type'=>'pie', 'name'=>ucfirst($indicator['label']), 'data' => $data_indicators);
            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['series'=>null];
    }


    function pieData($indicators, $data) {

        if(count($data) > 0) {

            //$cat = array();
            $data_indicators = array();


                $value = 0;
                foreach($indicators as $key=>$indicator) {
                    //if(array_sum(array_column($data, $key)) !== 0)
                        $data_indicators[] = array('name'=>$indicator,
                                                   'y'=>array_sum(array_column($data, $key)));
                }


            $ser[] = array('type'=>'pie', 'name'=>"value", 'data' => $data_indicators);
            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['series'=>null];
    }

    /***
     * @param $cat1 top level category string name, where together with d_ it should be a column name
     * @param $indicator string array indicators
     * @param $data
     * @return $r_data  formated array
     */
    function pieData1Category($cat1, $indicator, $substitute, $data) {

        if(count($data) > 0) {
            $tmp_top_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat1];
            }

            $top_cat = array_unique($tmp_top_cat);

            $r_data = array();
            $data_indicators = array();
            foreach ($top_cat as $t_c) {


                //$sub_cat = array();
                foreach ($data as $val) {
                    if ($val[$cat1] === $t_c) {

                        //foreach($indicators as $indicator) {
                            $data_indicators[] = array('name'=>$t_c, 'y'=>$val[$indicator] == 'null' ? null : (int)$val[$indicator]);
                        //}

                    }

                }

                //$temp_cat[] = $t_c;
            }
            //$cat['categories'] = $temp_cat;
            //$r_data['categories'] = $temp_cat;
            $ser[] = array('type'=>'pie', 'name'=>ucfirst($substitute), 'data' => $data_indicators);

            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['series'=>null];
    }

    /**
 * @param $array
 * @return array
 */

    function clusterDataForTable($array) {
        if(is_array($array)) {
            $month = array();
            foreach ($array as $value) {
                $month[] = $value['CMonth'];
            }
            $month = array_unique($month);
            $months = array();
            foreach ($month as $m)
                $months[] = $m;
            unset($month);
            $months = $this->orderMonths($months);
            $data = array();
            $larger_index = array();
            foreach ($months as $month) {
                $c_data = array();
                $d = array();
                foreach ($array as $value) {
                    if($month == $value['CMonth']) {
                        $index = ($value['Subdistrict']!= null || $value['Subdistrict']!= "")? $value['Subdistrict']."_".$value['ClusterNo']:$value['ClusterNo'];
                        $d[$index] = ['absent'=>$value['RemAbsent'],
                            'NSS'=>$value['RemNSS'],
                            'refusal'=>$value['RemRefusal']];
                        $c_data = $d;
                    }
                }
                $larger_index[$month] = count($c_data);
                $data[$month] = $c_data;
            }
            $large_key = array_keys($larger_index, max($larger_index));
            if(is_array($large_key))
                $large_key = $large_key[0];
            $columns_top = array();
            $columns = array(['title'=>'Cluster']);
            foreach($data as $key=>$value) {
                $columns_top[] = $key;
                $columns[] = ['title'=>'Absent'];
                $columns[] = ['title'=>'Sleep'];
                $columns[] = ['title'=>'Refusal'];

            }
            $large = $data[$large_key];
            //die(count($large));
            $rows = array();
            //unset($data[$key]);
            foreach ($large as $k=>$value) {
                $row = array($k);
                $t_sleep = array();
                $t_absent = array();
                $t_refusal = array();
                foreach($data as $d) {
                    //$row = array();
                    if(array_key_exists($k, $d)) {
                        $t_absent[] = $d[$k]['absent'];
                        $row[] = $d[$k]['absent'];
                        $t_sleep[] = $d[$k]['NSS'];
                        $row[] = $d[$k]['NSS'];
                        $t_refusal[] = $d[$k]['refusal'];
                        $row[] = $d[$k]['refusal'];
                        //$row[] = 'chart';
                    } else {
                        $row[] = null;
                        $row[] = null;
                        $row[] = null;
                        $t_absent[] = null;
                        $t_sleep[] = null;
                        $t_refusal[] = null;
                        //$row[] = 'chart';
                    }
                    //$rows[$k] = [$k.",".implode(",", $row)];
                }
                //$row[] = "<span class='absent'>".implode(",", $t_absent)."</span> <span class='sleep'>".implode(",", $t_sleep)."</span> <span class='refusal'>".implode(",", $t_refusal)."</span>";
                //$row[] = array('absent'=>$t_absent, 'sleep'=>$t_sleep, 'refusal'=>$t_refusal);
                $rows[] = $row;

            }

            $final_data = array('top_cols'=>$columns_top, 'cols'=>$columns, 'data'=>$rows);
            return $final_data;
            //return $large;


//            $data['larger_key'] = $key;
//            return $data;
        }
        else return ['error'=>'No data'];
    }

    /**
     * @param array $array (data)
     * @param string $indicator
     * @param array $filterBy
     * @param array $clusters
     * @param array $calcType ['type' => 'number|percent', 'column'=>'columnName']
     * @param $resultType (heatmap|table)
     * @param bool $sort
     * @return array
     */
    function clusterDataForHeatMap($array, $indicator, $filterBy,
                                   $clusters = array(), $calcType = ['type'=>'number'],
                                   $resultType = 'heatmap', $sort = true) {
        if(is_array($array)) {
            $tmp_top_cat = array();
            foreach($array as $temp_d) {
                $tmp_top_cat[] = $temp_d[$filterBy['column']];
            }

            $filters = array_unique($tmp_top_cat);
            if($sort) {
                sort($filters);
            }
            $data = array();

            foreach ($filters as $filter) {
                $c_data = array();
                $d = array();
                foreach ($array as $value) {
                    if($filter == $value[$filterBy['column']]) {
                        $index = ($value['Subdistrict']!== null || $value['Subdistrict']!= "")?
                                  $value['Subdistrict']."|".$value['ClusterNo']:
                                  $value['ClusterNo'];

                        $index_data = $value[$indicator];
                        // if the calc type was percent
                        if($calcType['type'] == 'percent') {
                            $divider = $value[$calcType['column']];
                            if($divider > 0)
                                $index_data = round(($index_data/$divider)*100, 2);
                            else
                                $index_data = 0;
                        }

                        $d[$index] = [$indicator=>$index_data];
                        $c_data = $d;
                    }
                }
                $data[$filter] = $c_data;
            }

            natsort($clusters);


//            dump($data);
//            dump($filters);
//            dump($clusters);
//
//            die;


            $heatMapData = $this->formatData4HeatMap($data, $filters, $clusters, $calcType, $indicator);
            // replacing filter column with substitute
            $xAxis = $this->shortenNames($filters, $array, $filterBy);

            if($resultType == 'table') {
                $colsA = $this->shortenNames($filters, $array, $filterBy, true);

                $heatMapData = $this->formatData4Table($data, $colsA, $clusters, $indicator);
            }
            // replace | in the subdistrict and cluster name with -
            $yAxis = $this->myStrReplace(array_reverse($clusters), "-", "|");

            $final_data = array('yAxis'=>$yAxis, 'xAxis'=>$xAxis, 'data'=>$heatMapData);
            //$final_data = array('yAxis'=>$yAxisReverse, 'xAxis'=>$xAxis, 'data'=>$heatMapDataReverse);
            return $final_data;
            //return $large;


//            $data['larger_key'] = $key;
//            return $data;
        }
        else return ['error'=>'No data'];
    }


    /**
     * @param $data
     * @param $xAxises [col1, col2, ..., coln]
     * @param $yAxis [col, label]
     * @param $calcType (percent)
     * @return array
     */
    public function heatMap($data, $xAxises, $yAxis, $calcType = '') {

        $yAxises = array();
        foreach ($data as $datum) {
            $yAxises[$datum[$yAxis['col']]] = $datum[$yAxis['label']];
        }

        ksort($yAxises);

        if(is_array($data)) {
            $rdata = array();
            foreach ($xAxises as $axise) {
                $tmp_data = array();
                $d = array();
                foreach ($data as $datum) {

                    $index = $datum[$yAxis['col']];
                    $index_value = $datum[$axise];

                    if($calcType == 'percent') {

                        $index_value = $index_value === null ? null: round(($index_value * 100), 0);
                    }
                    $d[$index] = [$axise=>$index_value];
                    $tmp_data = $d;
                }

                $rdata[$axise] = $tmp_data;
            }

            $newRows = array();
            $r = 0;
//
//            dump($rdata);
//            dump($yAxises);
//            dump($xAxises);
//            die;
            foreach($yAxises as $yAxis=>$val) {

                $newRow = array();

                foreach($xAxises as $xAxis) {

                    $c = 0;
                     foreach($rdata as $datum) {

                        if(array_key_exists($yAxis, $datum)) {
                            $value = $datum[$yAxis];
                            if(array_key_exists($xAxis, $value)) {
                                $newRow[] = [$c, $r, ($value[$xAxis] === null) ? null :
                                    $calcType == 'percent' ? $value[$xAxis] :
                                        (int) $value[$xAxis]];
                            }
                        }
                        $c ++;
                    }

                }
                $newRows[] = $newRow;
                $r++;

            }

            $heatMapData = array();
            foreach($newRows as $row) {
                foreach ($row as $ro)
                    $heatMapData[] = $ro;
            }

            return array('yAxis'=>array_values($yAxises), 'xAxis'=> $xAxises, 'data'=> $heatMapData);

        }
    }

    /**
     * @param $data
     * @param $filters
     * @param $clusters
     * @param $calcType
     * @param $indicator
     * @return array
     */
    private function formatData4HeatMap($data, $filters, $clusters, $calcType, $indicator) {

        $newRows = array();
        $r = 0;
        //Reverse the clusters, just to make heatmap from top-to-bottom aligned
        $rClusters = array_reverse($clusters);
        foreach($rClusters as $cluster) {

            $newRow = array();

            foreach($filters as $filter) {

                $c = 0;
                foreach($data as $datum) {

                    if(array_key_exists($cluster, $datum)) {
                        $newRow[] = [$c, $r, ($datum[$cluster][$indicator]=== null)? null :
                            $calcType['type']=='percent'?$datum[$cluster][$indicator]:
                                (int)$datum[$cluster][$indicator]];
                    }
                    else
                        $newRow[] = [$c, $r, null];

                    $c++;
                }
                break;
            }
            $newRows[] = $newRow;
            $r++;

        }

        // making data for heatmap as a one array
        $heatMapData = array();

        foreach($newRows as $row) {
            foreach ($row as $ro)
                $heatMapData[] = $ro;
        }

        return $heatMapData;

    }

    /**
     * @param $filters
     * @param $array
     * @param $filterBy
     * @return array
     */
    private function shortenNames($filters, $array, $filterBy, $asoc=false) {

        // replacing filter column with substitute
        $xAxis = array();
        if($asoc === true) {
            foreach ($filters as $filter) {
                foreach ($array as $item) {
                    if ($filter == $item[$filterBy['column']]) {
                        if ($filterBy['substitute'] === 'shortName') {
                            $index = $item['CType'] . "-" .
                                $this->shortMonth($item['CMonth']) . "-" .
                                $this->shortYear($item['CYear']);
                            $xAxis[$index] = $filter;
                        } else {
                            $index = $item[$filterBy['substitute']];
                            $xAxis[$index] = $filter;
                        }
                        break;
                    }
                }
            }
        } else {
            foreach ($filters as $filter) {
                foreach ($array as $item) {
                    if ($filter == $item[$filterBy['column']]) {
                        if ($filterBy['substitute'] === 'shortName') {
                            $xAxis[] = $item['CType'] . "-" .
                                $this->shortMonth($item['CMonth']) . "-" .
                                $this->shortYear($item['CYear']);
                        } else
                            $xAxis[] = $item[$filterBy['substitute']];
                        break;
                    }
                }
            }
        }

        return $xAxis;
    }

    /**
     * @param $items
     * @param $replacement
     * @param $term
     * @return array
     */
    private function myStrReplace($items, $replacement, $term) {
        $newItems = array();
        foreach($items as $item) {
            $newItems[] = str_replace($term, $replacement, $item);
        }

        return $newItems;
    }

    private function formatData4Table($data, $colsAssocArray, $rNames, $indicator = false) {
        $tableData = array();
        foreach ($rNames as $rName) {
            $row = array();
            $row['rowName'] = str_replace("|", "-", $rName);
            foreach ($colsAssocArray as $colName=>$col) {
                $found = false;
                foreach ($data[$col] as $key=>$val) {
                    if($rName == $key) {
                       $found = true;
                       $row[$colName] = ($indicator === false) ? $val:$val[$indicator];
                    }
                }

                if(!$found) {
                    $row[$colName] = null;
                }
            }
            $tableData[] = $row;
        }

        return $tableData;
    }



}
