<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.crawler.php';
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
        $proxy = $proxyObj->getProxy();

        $rand = rand(1, 100);    
            /*
            $search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2';
            $search_selector = ".item[nid='" . $nid . "'] h3 a";
            $next_selector = ".page-next";
        
            $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl/process.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            */

        /*if ($rand <= $path1) {
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
        else {*/
            //$search_url = 'http://list.tmall.com/search_product.htm?q=%C2%EA%BF%A7%BE%AB%C6%AC&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton';      
            $search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton';      
            $search_selector = ".product[data-id=' " . $nid . "'] div .productTitle a";
            $next_selector = "bbb";
            //$cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." --cookies-file=/tmp/cookies /var/html/casperjs/pcntl_tmall.js --ignore-ssl-errors=true \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            $cmd = "/usr/bin/casperjs /var/html/casperjs/pcntl_tmall.js --ignore-ssl-errors=true --proxy=42.97.57.79:80 --output-encoding=gbk --script-encoding=gbk \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            //$cmd = "/usr/bin/casperjs /var/html/casperjs/pcntl_tmall.js --ignore-ssl-errors=true --output-encoding=gbk --script-encoding=gbk \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
        //}
    
        echo $cmd . "\n";
        system($cmd);
        //$sql = "UPDATE keyword SET clicked_times = clicked_times + 1, run_status = 'free' WHERE id = " . $obj->id;
        //$mysqli->query($sql);
    //}
}