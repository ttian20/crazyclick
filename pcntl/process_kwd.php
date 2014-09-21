<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(dirname(__FILE__)) . '/class.keyword.php';
require_once dirname(dirname(__FILE__)) . '/class.proxy.php';

/*
$totalProcess = 50;
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
         sleep(1);
         crawler();
    }
}
*/
crawler();

function crawler() {
    $proxyObj = new proxy();
    $mysqli = new mysqli('10.168.45.191', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');

    //for (;;) {
        $hour = date('G');
        $current = time();

        #$sql = "SELECT * FROM keyword WHERE status = 'active' AND clicked_times < times AND ((last_click_time + click_interval) < {$current}) AND ((path1_page < 5 AND path1_page > 0) OR (path2_page < 5 AND path2_page > 0) OR (path3_page < 5 AND path3_page > 0)) ORDER BY last_click_time ASC LIMIT 1";
        $sql = "SELECT * FROM keyword WHERE id = 48";
        $result = $mysqli->query($sql);
        $data = array();
        if ($result) {
            $obj = $result->fetch_object();
            $result->close();
        }

        if (!$obj || !$obj->id) {
            echo "zz\n";
            sleep(1);
            continue ;
        }
        else {
            $kwd = urlencode($obj->kwd);
            $nid = $obj->nid;
            $date = date('Ymd');
            $sleep_time = $obj->sleep_time;

            $path1 = (int)$obj->path1;
            $path2 = $path1 + (int)$obj->path2;
            $path3 = $path2 + (int)$obj->path3;

            $ua = 'aa';

            $keyword = new keyword();

            $rand = rand(1, 100);
            if ($rand <= $path1) {
                //taobao search
                $data = array(
                    'path' => 'taobao',
                    'kwd' => $kwd,
                    'date' => $date,
                    'region' => $obj->path1_region,
                    'price_from' => $obj->path1_price_from,
                    'price_to' => $obj->path1_price_to,
                );
                if ($obj->path1_page >= 5) {
                    continue;
                }
                $proxy = $proxyObj->getProxy();
                #$search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2';
                $search_url = $keyword->buildSearchUrl($data);
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";
            
                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            elseif ($rand <= $path2) {
                //taobao search tmall tab
                $data = array(
                    'path' => 'taobao2tmall',
                    'kwd' => $kwd,
                    'date' => $date,
                    'region' => $obj->path2_region,
                    'price_from' => $obj->path2_price_from,
                    'price_to' => $obj->path2_price_to,
                );
                if ($obj->path2_page >= 5) {
                    continue;
                }
                $proxy = $proxyObj->getProxy();
                #$search_url = 'http://s.taobao.com/search?spm=a230r.1.0.0.9nMSJu&initiative_id=tbindexz_'.$date.'&tab=mall&q='.$kwd.'&suggest=0_2';      
                $search_url = $keyword->buildSearchUrl($data);
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";
            
                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            else {
                //tmall search
                $data = array(
                    'path' => 'tmall',
                    'kwd' => $kwd,
                    'date' => $date,
                    'region' => $obj->path3_region,
                    'price_from' => $obj->path3_price_from,
                    'price_to' => $obj->path3_price_to,
                );
                if ($obj->path3_page >= 5) {
                    continue;
                }
                $proxy = $proxyObj->getProxy(true);

                #$search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton';
                $search_url = $keyword->buildSearchUrl($data);
                $search_selector = ".product[data-id=' " . $nid . "'] div .productTitle a";
                $next_selector = "a.ui-page-s-next";

                $cmd = "/usr/bin/casperjs /var/html/casperjs/pcntl/process.js --ignore-ssl-errors=true --proxy=".$proxy." --output-encoding=gbk --script-encoding=gbk \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            
            $sql = "UPDATE keyword SET last_click_time = {$current} WHERE id = {$obj->id}";
            $mysqli->query($sql);
        }

    
        echo $cmd . "\n";
        system($cmd);
        $sql = "UPDATE keyword SET clicked_times = clicked_times + 1 WHERE id = " . $obj->id;
        $mysqli->query($sql);
    //}
}
