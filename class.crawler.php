<?php
require_once dirname(__FILE__) . '/class.curl.php';
class crawler {
    public $proxy = null;
    public $kwd = null;
    public $nid = null;
    public $userAgent = null;
    public $searchBaseUrl = 'http://s.taobao.com/';
    public function __construct($kwd, $nid) {
        if (!$kwd || !$nid) {
            exit("no kwd or nid\n");
        }
        //$this->proxy = $this->_getProxy();    
        $this->kwd = $kwd;
        $this->nid = $nid;
        $this->userAgent = $this->getUserAgent();
        //save proxy
    }

    public function buildSearchUrl($data) {
        $kwd = urlencode($data['kwd']);
        switch ($data['path']) {
            case 'taobao':
            case 'taobao2tmall':
                if ($data['region'] && $data['price_from'] && $data['price_to']) {
                }
                
                break;
            case 'tmall':
                $search_url = 'http://list.tmall.com/search_product.htm?q='.$kwd.'&type=p&vmarket=&spm=3.7396704.a2227oh.d100&from=mallfp..pc_1_searchbutton';
                if ($data['price_from']) {
                    $search_url .= '&start_price=' . $data['price_from'];
                }
                if ($data['price_to']) {
                    $search_url .= '&end_price=' . $data['price_to'];
                }
                break;
        }
    }

    public function getPage($url = '') {
        if ('' === $url) {
            $url = 'http://s.taobao.com/search?&initiative_id=tbindexz_'.date('Ymd').'&spm=1.7274553.1997520841.1&sourceId=tb.index&search_type=item&ssid=s5-e&commend=all&q=' . urlencode($this->kwd) . '&suggest=0_2&_input_charset=utf-8';
        }

        $curl = new Curl(); 
        $curl->get($url, array(), $this->proxy);
        $curl->setUserAgent($this->userAgent);
        echo $curl->http_status_code . "\n";
        if (200 == $curl->http_status_code) {
            $body = $curl->response;
            $findme = 'nid="' . $this->nid . '"';
            echo $findme . "\n";
            var_dump(strpos($body, $findme));
            echo "\n";
            if (strpos($body, $findme)) {
                return $url;
            }
            else {
                //sleep();
                //$begin = microtime(true);
                $nextPagePattern = "/<\/a><a href=\"\/(.*?)\"  class=\"page-next\" trace='srp_select_pagedown'>/i";
                #$nextPagePattern = "/<a href=\"\/([_-=\.\?%&a-z0-9]+?)\"  class=\"page-next\" trace='srp_select_pagedown'>/i";
                preg_match_all($nextPagePattern, $body, $match);
                //$end = microtime(true);
                //echo "cost time: " . ($end - $begin);  
                //echo "\n";
                //echo strpos($body, 'page-next');
                //echo $body;
                //print_r($match);
                $url = $this->searchBaseUrl . $match[1][0];
                //echo $url;
                //echo "\n";
                $sleepSecond = rand(2, 4);
                sleep($sleepSecond);
                return $this->getPage($url);
            }
        }
    }

    public function getUserAgent() {
        $data = array(
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)',
            'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
            'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)',
        );
        $count = count($data);
        $rand = rand(0, $count - 1);
        return $data[$rand]; 
    }
}
