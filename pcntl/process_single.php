<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(dirname(__FILE__)) . '/class.crawler.php';
require_once dirname(dirname(__FILE__)) . '/class.proxy.php';

$nid = $argv[1];

function crawler() {
    global $nid;
    $proxyObj = new proxy();
    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');

    //for (;;) {
        $hour = date('G');
        $current = time();
        //echo "Id {$id}\n";
        $sql = "SELECT * FROM keyword WHERE id = " . $nid;
        $result = $mysqli->query($sql);
        $data = array();
        if ($result) {
            while ($obj = $result->fetch_object()) {
                $data[] = $obj;
            }
        }
        if (!$data) {
            echo "zz\n";
            //$mysqli->rollback();
            //sleep(60);
            continue ;
        }

    
        foreach ($data as $obj) {
            $kwd = urlencode($obj->kwd);
            $nid = $obj->nid;

            $date = date('Ymd');
            $sleep_time = $obj->sleep_time;
            
            //$crawler = new crawler($kwd, $nid);
            //$proxy = $crawler->proxy;
            //$ua = $crawler->userAgent;
            //echo $proxy . "\n";
            $ua = 'aa';
            $proxy = $proxyObj->getProxy();
            //echo $proxy . "\n";

            if ($obj->path1_page <= $obj->path2_page) {
                $search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2';
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";


                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process_single.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            else {
                $search_url = 'http://s.taobao.com/search?spm=a230r.1.0.0.9nMSJu&initiative_id=tbindexz_'.$date.'&tab=mall&q='.$kwd.'&suggest=0_2';      
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";

                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process_single.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }

            //echo $cmd . "\n";
            system($cmd);
            $sql = "UPDATE keyword SET clicked_times = clicked_times + 1, last_click_time = " . time() .", run_status = 'free' WHERE id = " . $obj->id;
            $mysqli->query($sql);
        }
    //}
}

crawler();
