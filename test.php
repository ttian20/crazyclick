<?php
require_once dirname(__FILE__) . '/class.crawler.php';

$mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

for(;;) {
    $sql = "SELECT * FROM keyword WHERE id = 6 AND times > clicked_times ORDER BY last_click_time ASC LIMIT 1";
    $result = $mysqli->query($sql);
    $data = array();
    if ($result) {
        while ($obj = $result->fetch_object()) {
            $data[] = $obj;
        }
    }

    foreach ($data as $obj) {
        $kwd = $obj->kwd;
        $nid = $obj->nid;
        //$nid = '37770555506';
        $crawler = new crawler($kwd, $nid);
        $proxy = $crawler->proxy;
        echo $proxy . "\n";
        $url = $crawler->getPage();
        echo $url . "\n";
        
        $cmd = "/usr/bin/casperjs --proxy=".$proxy." /var/html/casperjs/tb.js \"".$url."\" ".$nid;
        echo $cmd . "\n";
        system($cmd);
        $sql = "UPDATE keyword SET clicked_times = clicked_times + 1, last_click_time = " . time() ." WHERE id = " . $obj->id;
        $mysqli->query($sql);
    }
}


//$kwd = '负重绑腿';
//$nid = '36962206480';
//$crawler = new crawler($kwd, $nid);
//$proxy = $crawler->proxy;
//echo $proxy . "\n";
//$url = $crawler->getPage();
//echo $url . "\n";
//
//$cmd = "/usr/bin/casperjs --proxy=".$proxy." /var/html/casperjs/tb.js \"".$url."\" ".$nid;
//system($cmd);
