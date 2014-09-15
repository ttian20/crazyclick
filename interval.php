<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');

    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');

    $sql = "SELECT * FROM keyword";
    $result = $mysqli->query($sql);
    $data = array();
    if ($result) {
        $sql = array();
        while ($obj = $result->fetch_object()) {
            $end = $obj->click_end;
            $begin = $obj->click_start;
            $seconds = ($end - $begin) * 3600;
            $times = $obj->times;
            $interval = ceil($seconds / $times); 
            $sql[] = "UPDATE keyword SET click_interval = {$interval} WHERE id = {$obj->id}";
        }
        $sqls = implode(';', $sql);
        $mysqli->multi_query($sqls);
    }
    $mysqli->close();
