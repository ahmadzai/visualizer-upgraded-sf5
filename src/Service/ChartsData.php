<?php
namespace App\Service;
/**
 * Created by PhpStorm.
 * User: Wazir Khan
 * Date: 16/06/2017
 * Time: 3:21 PM
 */
use Doctrine\ORM\EntityManager;

class ChartsData
{


    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function lineChart($data, $config = array())
    {
        $channel = array();
        if(count($data) > 0) {
            unset($channel);
            $xAxis = array();
            $legend = array();
            foreach ($data as $value) {
                # code...
                $xAxis[] = $value['xAxis'];
                $legend[] = $value['legend'];
            }

            $xAxis_ = array_unique($xAxis);
            $xAxiss = array();
            foreach ($xAxis_ as $k => $val)
                $xAxiss[] = $val;
            $legends = array_unique($legend);
            unset($xAxiss_);
            //var_dump($xAxiss);
            $series = array();
            foreach ($legends as $value) {
                # code...
                $dat = array();

                foreach ($data as $val) {
                    # code...
                    if ($value === $val['legend']) {
                        $dat[] = (int)$val['yAxis'];
                    }
                }
                $series[] = array('name' => "$value", 'data' => $dat);
            }
            $channel = array(
                'categories' => $xAxiss,
                'series' => $series
            );
        }
        return $channel;

    }

    public function pieChart($data)
    {
        $dat = array();
        foreach ($data as $value) {
            $dat[] = array($value['legend'], (int) $value['total']);
            //echo $value->form."<br>";
        }
        $series = array("type"=>"pie", "name"=>"Form Submission", "data" => $dat);
        $channel = array(
            'series' => $series
        );

        return $channel;
    }

    public function columnChart($data)
    {

    }

    public function stackChart($data, $type) {
        $ret_data = array();
        if($type == "province" && count($data) > 0) {
            $ret_data = $this->makeDataProvinceDistrict($data);
        } elseif ($type == 'region') {
            $ret_data = $this->makeDataRegion($data);
            //$ret_data['title'] = "Remaining Children By Region";
        } elseif ($type == 'district')
            $ret_data = $this->makeDataProvinceDistrict($data, 'district');

        return $ret_data;
    }

    function makeDataRegionsProvince($array, $region) {
        $prov = array();
        $month = array();
        foreach ($array as $value) {
            $prov[] = $value['d_province'];
            $month[] = $value['d_cMonth'];
        }
        $prov = array_unique($prov);
        $provs = array();
        foreach ($prov as $p) {
            $provs[] = $p;
        }
        unset($prov);

        $month = array_unique($month);
        $months = array();
        foreach ($month as $m)
            $months[] = $m;
        unset($month);
        //$this->setting = new Settings();
        $months = $this->setting->orderMonths($months);
        //$months = $this->orderMonths($months);
        $data = array();
        $cat = array('name'=>$region);
        $remaining = array();
        $sleep = array();
        $refusal = array();
        $temp_cat = array();
        foreach ($provs as $prov) {
            $sub_cat = array();
            foreach ($months as $month) {
                //$sub_cat = array();
                foreach ($array as $val) {
                    if ($val['d_province'] === $prov && $val['d_cMonth'] === $month) {
                        $sub_cat[] = $month;
                        $remaining[] = $val['d_remainingAbsent'] == 'null' ? null : (int)$val['d_remainingAbsent'];
                        $sleep[] = $val['d_remainingSleep'] == 'null' ? null : (int)$val['d_remainingSleep'];
                        $refusal[] = $val['d_remainingRefusal'] == 'null' ? null : (int)$val['d_remainingRefusal'];


                    }

                }
            }
            $temp_cat[] = array('name' => $prov, 'categories' => $sub_cat);
        }
        $cat['categories'] = $temp_cat;
        $data['categories'] = $cat;
        $data['remaining'] = $remaining;
        $data['sleep'] = $sleep;
        $data['refusal'] = $refusal;

        return $data;

    }

    function makeDataProvinceDistrict($array, $type = 'province') {
        $prov = array();
        $month = array();
        foreach ($array as $value) {
            $prov[] = $value['d_'.$type];
            $month[] = $value['d_cMonth'];
        }
        $prov = array_unique($prov);
        $provs = array();
        foreach ($prov as $p) {
            $provs[] = $p;
        }
        unset($prov);

        $month = array_unique($month);
        $months = array();
        foreach ($month as $m)
            $months[] = $m;
        unset($month);
        $months = $this->orderMonths($months);
        $data = array();
        $remaining = array();
        $sleep = array();
        $refusal = array();
        $temp_cat = array();
        foreach ($provs as $prov) {
            $sub_cat = array();
            foreach ($months as $month) {
                //$sub_cat = array();
                foreach ($array as $val) {
                    if ($val['d_'.$type] === $prov && $val['d_cMonth'] === $month) {
                        $sub_cat[] = $month;
                        $remaining[] = $val['d_remainingAbsent'] == 'null' ? null : (int)$val['d_remainingAbsent'];
                        $sleep[] = $val['d_remainingSleep'] == 'null' ? null : (int)$val['d_remainingSleep'];
                        $refusal[] = $val['d_remainingRefusal'] == 'null' ? null : (int)$val['d_remainingRefusal'];


                    }

                }
            }
            $temp_cat[] = array('name' => $prov, 'categories' => $sub_cat);
        }
        //$cat['categories'] = $temp_cat;
        $data['categories'] = $temp_cat;
        $data['series'] = array(array('name'=>'Refusal', 'data' => $refusal),
            array('name'=>'Sleep_NewBorn', 'data'=>$sleep),
            array('name'=>'Missed', 'data' => $remaining));

        return $data;

    }

    function makeDataRegion($array) {
        $reg = array();
        $month = array();
        foreach ($array as $value) {
            $reg[] = $value['d_region'];
            $month[] = $value['d_cMonth'];
        }
        $reg = array_unique($reg);
        $region = array();
        foreach ($reg as $r) {
            $region[] = $r;
        }
        unset($reg);

        $month = array_unique($month);
        $months = array();
        foreach ($month as $m)
            $months[] = $m;
        unset($month);
        $months = $this->orderMonths($months);
        $data = array();
        $cat = array();
        $remaining = array();
        $sleep = array();
        $refusal = array();
        $temp_cat = array();
        foreach ($region as $reg) {
            $sub_cat = array();
            foreach ($months as $month) {
                //$sub_cat = array();
                foreach ($array as $val) {
                    if ($val['d_region'] === $reg && $val['d_cMonth'] === $month) {
                        $sub_cat[] = $month;
                        $remaining[] = $val['d_remainingAbsent'] == 'null' ? null : (int)$val['d_remainingAbsent'];
                        $sleep[] = $val['d_remainingSleep'] == 'null' ? null : (int)$val['d_remainingSleep'];
                        $refusal[] = $val['d_remainingRefusal'] == 'null' ? null : (int)$val['d_remainingRefusal'];


                    }

                }
            }
            $temp_cat[] = array('name' => $reg, 'categories' => $sub_cat);
        }
        //$cat['categories'] = $temp_cat;
        $data['categories'] = $temp_cat;
        $data['series'] = array(array('name'=>'Refusal', 'data' => $refusal),
            array('name'=>'Sleep', 'data'=>$sleep),
            array('name'=>'Missed', 'data' => $remaining));

        return $data;

    }

    function regionProvince3LevelStackChart($data, $region) {
        $cr = array();
        $sr = array();
        $ser = array();
        $wr = array();
        $er = array();

        foreach ($data as $value) {
            if($value['d_region'] == "ER")
                $er[] = $value;
            elseif ($value['d_region'] == "WR")
                $wr[] = $value;
            elseif ($value['d_region'] == "SR")
                $sr[] = $value;
            elseif ($value['d_region'] == "SER")
                $ser[] = $value;
            elseif ($value['d_region'] == "CR")
                $cr[] = $value;
        }

        $east_r = count($er) > 0 ? $this->makeDataRegionsProvince($er, 'ER') : null;
        //$retdata = $east_r;
        $south_r = count($sr) > 0 ? $this->makeDataRegionsProvince($sr, 'SR') : null;
        $south_east_r = count($ser) > 0 ? $this->makeDataRegionsProvince($ser, 'SER') : null;
        $central_r = count($cr) > 0 ? $this->makeDataRegionsProvince($cr, 'CR') : null;
        $west_r = count($wr) > 0 ? $this->makeDataRegionsProvince($wr, 'WR') : null;

        $cat = array();
        $series = array();
        if(is_array($east_r)) {
            $cat[] = $east_r['categories'];
            $series['er_remaining'] = $east_r['remaining'];
            $series['er_sleep'] = $east_r['sleep'];
            $series['er_refusal'] = $east_r['refusal'];
        }
        if(is_array($south_r)) {
            $cat[] = $south_r['categories'];
            $series['sr_remaining'] = $south_r['remaining'];
            $series['sr_sleep'] = $south_r['sleep'];
            $series['sr_refusal'] = $south_r['refusal'];
        }
        if(is_array($south_east_r)) {
            $cat[] = $south_east_r['categories'];
            $series['ser_remaining'] = $south_east_r['remaining'];
            $series['ser_sleep'] = $south_east_r['sleep'];
            $series['ser_refusal'] = $south_east_r['refusal'];
        }
        if(is_array($central_r)) {
            $cat[] = $central_r['categories'];
            $series['cr_remaining'] = $central_r['remaining'];
            $series['cr_sleep'] = $central_r['sleep'];
            $series['cr_refusal'] = $central_r['refusal'];
        }
        if(is_array($west_r)) {
            $cat[] = $west_r['categories'];
            $series['wr_remaining'] = $west_r['remaining'];
            $series['wr_sleep'] = $west_r['sleep'];
            $series['wr_refusal'] = $west_r['refusal'];
        }

        $retdata['categories'] = $cat;
        $rem = array_merge($series['er_remaining'], $series['sr_remaining'], $series['ser_remaining'],
            $series['cr_remaining'], $series['wr_remaining']);
        $sleep = array_merge($series['er_sleep'], $series['sr_sleep'], $series['ser_sleep'],
            $series['cr_sleep'], $series['wr_sleep']);
        $ref = array_merge($series['er_refusal'], $series['sr_refusal'], $series['ser_refusal'],
            $series['cr_refusal'], $series['wr_refusal']);
        $retdata['series'] = array(array('name'=>'Refusal', 'data' => $ref),
            array('name'=>'Sleep', 'data'=>$sleep),
            array('name'=>'Missed', 'data' => $rem));

    }

    function makeClusterData($array) {
        if(is_array($array)) {
            $month = array();
            foreach ($array as $value) {
                $month[] = $value['d_cMonth'];
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
                    if($month == $value['d_cMonth']) {
                        $index = ($value['d_subDistrict']!= null || $value['d_subDistrict']!= "")? $value['d_subDistrict']."_".$value['d_cluster']:$value['d_cluster'];
                        $d[$index] = ['absent'=>$value['d_remainingAbsent'],
                            'sleep'=>$value['d_remainingSleep'],
                            'refusal'=>$value['d_remainingRefusal']];
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
                        $t_sleep[] = $d[$k]['sleep'];
                        $row[] = $d[$k]['sleep'];
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
                $row[] = "<span class='absent'>".implode(",", $t_absent)."</span> <span class='sleep'>".implode(",", $t_sleep)."</span> <span class='refusal'>".implode(",", $t_refusal)."</span>";
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

    /***
     * @param $cat1 top level category string name, where together with d_ it should be a column name
     * @param $cat2 second level category
     * @param $indicators string array indicators
     * @param $data
     * @return $r_data  formated array
     */
    function chartData2Categories($cat1, $cat2, $indicators, $data) {

        if(count($data) > 0) {
            $tmp_top_cat = array();
            $tmp_second_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat1];
                $tmp_second_cat[] = $temp_d[$cat2];
            }

            $top_cat = array_unique($tmp_top_cat);
            $second_cat = array_unique($tmp_second_cat);
            $second_cat = sort($second_cat);
            if($cat2 == 'd_cMonth' || $cat2 == 'd_month' || $cat2 == 'CMonth')
                $second_cat = $this->orderMonths($tmp_second_cat);

            $r_data = array();
            //$cat = array();
            $data_indicators = array();
            $temp_cat = array();
            foreach ($top_cat as $t_c) {
                $sub_cat = array();
                foreach ($second_cat as $s_c) {
                    //$sub_cat = array();
                    foreach ($data as $val) {
                        if ($val[$cat1] === $t_c && $val[$cat2] === $s_c) {
                            $sub_cat[] = $s_c;
                            foreach($indicators as $indicator) {
                                $data_indicators[$indicator][] = $val[$indicator] == 'null' ? null : (int)$val[$indicator];
                            }

                        }

                    }
                }
                $temp_cat[] = array('name' => $t_c, 'categories' => $sub_cat);
            }
            //$cat['categories'] = $temp_cat;
            $r_data['categories'] = $temp_cat;
//            $data['series'] = array(array('name'=>'Refusal', 'data' => $refusal),
//                array('name'=>'Sleep_NewBorn', 'data'=>$sleep),
//                array('name'=>'Missed', 'data' => $remaining));
            $ser = array();
            foreach ($indicators as $key=>$ind) {
                $ser[] = array('name'=>ucfirst($key), 'data' => $data_indicators[$ind]);
            }

            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['data'=>null];
    }

    /***
     * @param $cat1 top level category string name, where together with d_ it should be a column name
     * @param $indicators string array indicators
     * @param $data
     * @return $r_data  formated array
     */
    function chartData1Category($cat1, $indicators, $data) {

        if(count($data) > 0) {
            $tmp_top_cat = array();
            foreach($data as $temp_d) {
                $tmp_top_cat[] = $temp_d[$cat1];
            }

            $top_cat = array_unique($tmp_top_cat);

            $r_data = array();
            //$cat = array();
            $data_indicators = array();
            $temp_cat = array();
            foreach ($top_cat as $t_c) {


                //$sub_cat = array();
                foreach ($data as $val) {
                    if ($val[$cat1] === $t_c) {

                        foreach($indicators as $indicator) {
                            $data_indicators[$indicator][] = $val[$indicator] == 'null' ? null : (int)$val[$indicator];
                        }

                    }

                }

                $temp_cat[] = $t_c;
            }
            //$cat['categories'] = $temp_cat;
            $r_data['categories'] = $temp_cat;
            $ser = array();
            foreach ($indicators as $key=>$ind) {
                $ser[] = array('name'=>ucfirst($key), 'data' => $data_indicators[$ind]);
            }

            $r_data['series'] = $ser;

            return $r_data;
        }

        else
            return ['data'=>null];
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
            return ['data'=>null];
    }



}
