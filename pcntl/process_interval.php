<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(dirname(__FILE__)) . '/class.crawler.php';
require_once dirname(dirname(__FILE__)) . '/class.proxy.php';

$totalProcess = 1;
//echo posix_getpid() . "\n";

for ($i = 0; $i < $totalProcess; $i++) {
    $pid = pcntl_fork();
    set_time_limit(0);

    if ($pid == -1) {
         die("could not fork\n");
    }
    elseif ($pid) {
         //echo "parent pid is " . posix_getpid() . "\n";
    }
    else {
         //echo "child pid is " . posix_getpid() . "\n";
         crawler();
    }
}

function crawler() {
    $proxyObj = new proxy();
    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');
    for (;;) {
        $hour = date('G');
        $current = time();
        $sql = "SELECT * FROM keyword WHERE status = 'active' AND clicked_times < times AND ((last_click_time + click_interval) > {$current}) AND ((path1_page < 5 AND path1_page > 0) OR (path2_page < 5 AND path2_page > 0)) ORDER BY last_click_time ASC LIMIT 1";
        $result = $mysqli->query($sql);
        $data = array();
        if ($result) {
            //while ($obj = $result->fetch_object()) {
            //    $data[] = $obj;
            //}
            $obj = $result->fetch_object();
            $result->close();

        }
        if (!$obj->id) {
            echo "zz\n";
            //$mysqli->rollback();
            //sleep(60);
            continue ;
        }
        else {
            $sql = "UPDATE keyword SET last_click_time = {$current} WHERE id = {$obj->id}";
            $mysqli->query($sql);
        }

    
            $kwd = urlencode($obj->kwd);
            $nid = $obj->nid;

            $date = date('Ymd');
            $sleep_time = $obj->sleep_time;
            
            $ua = 'aa';
            $proxy = $proxyObj->getProxy();

            if ($obj->path1_page <= $obj->path2_page) {
                $search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2';
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";


                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            else {
                $search_url = 'http://s.taobao.com/search?spm=a230r.1.0.0.9nMSJu&initiative_id=tbindexz_'.$date.'&tab=mall&q='.$kwd.'&suggest=0_2';      
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";

                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }

            //echo $cmd . "\n";
            system($cmd);
            $sql = "UPDATE keyword SET clicked_times = clicked_times + 1, last_click_time = " . time() .", run_status = 'free' WHERE id = " . $obj->id;
            $mysqli->query($sql);
        //}
    }
}
