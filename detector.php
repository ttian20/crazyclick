<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.crawler.php';
require_once dirname(__FILE__) . '/class.proxy.php';

function detector() {
    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');
    $proxyObj = new proxy();

        $hour = date('G');

        $current = time();
        $sql = "SELECT * FROM keyword WHERE is_detected = 0";
        $result = $mysqli->query($sql);
        $data = array();
        if ($result) {
            while ($obj = $result->fetch_object()) {
                $data[] = $obj;
            }
        }
        if (!$data) {
            echo "zz\n";
            exit("Done\n");
        }
    
        foreach ($data as $obj) {
            echo $obj->kwd . "\n";
            $kwd = urlencode($obj->kwd);
            $nid = $obj->nid;

            $date = date('Ymd');
            
            $path1 = (int)$obj->path1;
            $path2 = $path1 + (int)$obj->path2;
            $path3 = $path2 + (int)$obj->path3;

            $sleep_time = $obj->sleep_time;
            
            $proxy = $proxyObj->getProxy();
            $httpsProxy = $proxyObj->getProxy(true);
            $ua = 'aa';

                //taobao search
                $search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2';
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";

                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/detector.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
                $path1_page = system($cmd);
                echo $path1_page . "\n";
                if (!preg_match('/^[0-9]/', $path1_page)) {
                    echo "error\n";
                }
                else {
                    $depth = (int)$path1_page + 1;
                    $sql = "UPDATE keyword SET path1_page ={$depth} WHERE id = " . $obj->id;
                    $mysqli->query($sql);               
                }

                //taobao search tmall tab
                $search_url = 'http://s.taobao.com/search?spm=a230r.1.0.0.9nMSJu&initiative_id=tbindexz_'.$date.'&tab=mall&q='.$kwd.'&suggest=0_2';      
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";

                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/detector.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
                $path2_page = system($cmd);
                echo $path2_page . "\n";
                if (!preg_match('/^[0-9]/', $path2_page)) {
                    echo "error\n";
                }
                else {
                    $depth = (int)$path2_page + 1;
                    $sql = "UPDATE keyword SET path2_page ={$depth} WHERE id = " . $obj->id;
                    $mysqli->query($sql);               
                }

                //tmall search
                $search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton';
                $search_selector = ".product[data-id=' " . $nid . "'] div .productTitle a";
                $next_selector = "a.ui-page-s-next";

                $cmd = "/usr/bin/casperjs /var/html/casperjs/detector.js --ignore-ssl-errors=true --proxy=".$httpsProxy." --output-encoding=gbk --script-encoding=gbk \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
                $path3_pate = system($cmd);
                echo $path3_page . "\n";
                if (!preg_match('/^[0-9]/', $path3_page)) {
                    echo "error\n";
                }
                else {
                    $depth = (int)$path3_page + 1;
                    $sql = "UPDATE keyword SET path3_page ={$depth} WHERE id = " . $obj->id;
                    $mysqli->query($sql);               
                }

                $sql = "UPDATE keyword SET is_detected = 1 WHERE id = " . $obj->id;
                $mysqli->query($sql);

        }
}
detector();
