<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.crawler.php';

$totalProcess = 20;
echo posix_getpid() . "\n";

for ($i = 0; $i < $totalProcess; $i++) {
    $pid = pcntl_fork();
    set_time_limit(0);

    if ($pid == -1) {
         die("could not fork\n");
    }
    elseif ($pid) {
         echo "parent pid is " . posix_getpid() . "\n";
         //echo "parent exit\n";
         //pcntl_wait($status); //Protect against Zombie children
    }
    else {
         echo "child pid is " . posix_getpid() . "\n";
         //sleep(10);
         //echo "child exit\n";
         // we are the child
         crawler();
    }
    //$pid = pcntl_fork();
}

function crawler() {
    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');
    for (;;) {
        $hour = date('G');
        $mysqli->autocommit(false);

        $current = time();
        $sql = "SELECT * FROM keyword "
             . "WHERE status = 'active' AND run_status = 'free' AND times > clicked_times AND begin_time < {$current} AND end_time > {$current} AND click_start < {$hour} AND click_end > {$hour} "
             //. "WHERE status = 'active' AND times > clicked_times AND begin_time < {$current} AND end_time > {$current} "
             . "ORDER BY last_click_time ASC LIMIT 1 FOR UPDATE";
        //$sql = "SELECT * FROM keyword LIMIT 1";
        $result = $mysqli->query($sql);
        $data = array();
        if ($result) {
            while ($obj = $result->fetch_object()) {
                $data[] = $obj;
            }
        }
        if (!$data) {
            echo "zz\n";
            $mysqli->rollback();
            sleep(60);
            continue ;
        }

    
        foreach ($data as $obj) {
            #$kwd = urlencode(iconv('UTF-8', 'GBK', $obj->kwd));
            $sql = "UPDATE keyword SET run_status = 'locked' WHERE id = " . $obj->id;
            $mysqli->query($sql);
            $mysqli->commit();

            $kwd = urlencode($obj->kwd);
            $nid = $obj->nid;

            $date = date('Ymd');
            
            $path1 = (int)$obj->path1;
            $path2 = $path1 + (int)$obj->path2;
            $path3 = $path2 + (int)$obj->path3;

            $sleep_time = $obj->sleep_time;
            
            $crawler = new crawler($kwd, $nid);
            $proxy = $crawler->proxy;
            $ua = $crawler->userAgent;
            echo $proxy . "\n";

            $rand = rand(1, 100);
            if ($rand <= $path1) {
                #$search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2&_input_charset=utf-8';
                $search_url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.$date.'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q='.$kwd.'&suggest=0_2';
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";


                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            elseif ($rand <= $path2) {
                #$search_url = 'http://s.taobao.com/search?spm=a230r.1.0.0.9nMSJu&initiative_id=tbindexz_'.$date.'&tab=mall&q='.$kwd.'&suggest=0_2';      
                $search_url = 'http://s.taobao.com/search?spm=a230r.1.0.0.9nMSJu&initiative_id=tbindexz_'.$date.'&tab=mall&q='.$kwd.'&suggest=0_2';      
                $search_selector = ".item[nid='" . $nid . "'] h3 a";
                $next_selector = ".page-next";

                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }
            else {
                #$search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton&_input_charset=utf-8';
                #$search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton';
                //$search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&user_action=initiative&at_topsearch=1&sort=st&type=p&cat=all&vmarket=';
                $search_url = iconv('UTF-8', 'GBK', $obj->kwd);
                $search_selector = ".product[data-id=' " . $nid . "'] div .productTitle a";
                $next_selector = ".ui-page-next";

                $cmd = "/usr/bin/casperjs --output-encoding=gbk --script-encoding=gbk --proxy=".$proxy." /var/html/casperjs/pcntl_tmall.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time . " \"" . $ua . "\"";
            }


            #$cmd = "/usr/bin/casperjs --proxy=".$proxy." /var/html/casperjs/pcntl.js \"".$search_url."\" "." \"" . $search_selector . "\" " . "\"" . $next_selector . "\" " . $sleep_time;
            echo $cmd . "\n";
            system($cmd);
            $sql = "UPDATE keyword SET clicked_times = clicked_times + 1, last_click_time = " . time() .", run_status = 'free' WHERE id = " . $obj->id;
            $mysqli->query($sql);
        }
    }
}

//crawler();
