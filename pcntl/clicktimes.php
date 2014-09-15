<?php
    date_default_timezone_set('Asia/Shanghai');
    $date = date('Ymd', time()-86400);
    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');
    $sql = "SELECT * FROM clicks WHERE date = {$date}";
    $res = $mysqli->query($sql);
    if ($res) {
        $obj = $res->fetch_object();
        if ($obj) {
            exit("¼ÇÂ¼ÒÑ´æÔÚ\n");
        }
    }

    $sql = "SELECT * FROM keyword WHERE clicked_times > 0"; 
    $res = $mysqli->query($sql);
    $kwds = array();
    if ($res) {
        while ($obj = $res->fetch_object()) {
           $kwds[] = $obj; 
        }
    }

    if (!$kwds) {
        exit();
    }

    foreach($kwds as $k) {
        $sql = "REPLACE INTO clicks (kid, date, clicks) VALUES ({$k->id}, {$date}, {$k->clicked_times})";
        $mysqli->query($sql);
    }

    $sql = "UPDATE keyword SET clicked_times = 0";
    $mysqli->query($sql);

