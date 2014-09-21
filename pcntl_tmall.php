<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.keyword.php';
require_once dirname(__FILE__) . '/class.proxy.php';

crawler();

function crawler() {
    $proxyObj = new proxy();
    $mysqli = new mysqli('10.168.45.191', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');

    //for (;;) {
        $hour = date('G');
        $current = time();

        //$sql = "SELECT * FROM keyword WHERE status = 'active' AND clicked_times < times AND ((last_click_time + click_interval) < {$current}) AND ((path1_page < 5 AND path1_page > 0) OR (path2_page < 5 AND path2_page > 0)) ORDER BY last_click_time ASC LIMIT 1";
        $sql = "SELECT * FROM keyword WHERE id = 16";
        $result = $mysqli->query($sql);
        $data = array();
        if ($result) {
            $obj = $result->fetch_object();
            $result->close();
        }

        if (!$obj->id) {
            echo "zz\n";
            sleep(1);
            continue ;
        }
        else {
            //$sql = "UPDATE keyword SET last_click_time = {$current} WHERE id = {$obj->id}";
            //$mysqli->query($sql);
        }
    
        $kwd = urlencode($obj->kwd);
        $nid = $obj->nid;
    
        $date = date('Ymd');
        $sleep_time = $obj->sleep_time;

        $path1 = (int)$obj->path1;
        $path2 = $path1 + (int)$obj->path2;
        if ($obj->path1_page > 5) {
            $path1 = 0;
            $path2 = 100;
        }
    
        $ua = 'aa';
        $proxy = $proxyObj->getProxy(true);

        $rand = rand(1, 100);    

    //}
}
