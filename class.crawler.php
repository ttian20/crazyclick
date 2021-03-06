<?php
require_once dirname(__FILE__) . '/class.curl.php';
require_once dirname(__FILE__) . '/class.keyword.php';

class crawler {
    public $proxy = null;
    public $kwd = null;
    public $nid = null;
    public $userAgent = null;
    public $kwdObj = null;
    public $taobaoSearchBaseUrl = 'http://s.taobao.com/';
    public $db = null;
    public function __construct() {
        $this->kwdObj = new keyword();
        $this->db = new mysqli('10.168.45.191', 'admin', 'txg19831210', 'crawler');
        $this->db->query('SET NAMES gbk');
    }

    public function run($data) {
        $priceStr = explode(".", $data['price']);
        if ($priceStr[1] = '00') {
            $data['price_from'] = $priceStr[0];
            $data['price_to'] = $priceStr[0] + 1;
        }
        else {
            $data['price_from'] = floor($priceStr[0]);
            $data['price_to'] = floor($priceStr[0]) + 1;
        }
        $data['date'] = date('Ymd');
        $data['kwd'] = urlencode($data['kwd']);

        $this->nid = $data['nid'];
        if ($data['path'] == 'tmall') {

        }
        else {
            //4种条件搜索
            //1. 无附加搜索条件
            //2. 单纯价格作搜索条件
            //3. 单纯地区作搜索条件
            //4  地区和价格同时作搜索条件
            $url = $this->kwdObj->buildSearchUrl($data);
            echo $url . "\n";
            $page = $this->getPage($url);
            echo $page . " found \n";
            if ($page == -1) {
                unset($data['price_from']);
                unset($data['price_to']);
                $url = $this->kwdObj->buildSearchUrl($data);
                echo $url . "\n";
                $page = $this->getPage($url);
            }
            $this->update($data, $page);
        }
    }

    public function getPage($url, $i = 1) {
        $curl = new Curl(); 
        //$curl->get($url, array(), $this->proxy);
        $curl->get($url, array());
        $curl->setUserAgent($this->getUserAgent());
        echo $curl->http_status_code . "\n";
        if (200 == $curl->http_status_code) {
            $body = $curl->response;
            $findme = 'nid="' . $this->nid . '"';
            //echo $findme . "\n";
            //var_dump(strpos($body, $findme));
            //echo "\n";
            if (strpos($body, $findme)) {
                return $i;
            }
            else {
                if ($i >= 20) {
                    return -1;
                }
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
                if (!$match[1][0]) {
                    print_r($match);
                    return -1;
                }
                $url = $this->taobaoSearchBaseUrl . $match[1][0];
                $sleepSecond = rand(2, 4);
                sleep($sleepSecond);
                $i++;
                echo $i . " not found\n";
                return $this->getPage($url, $i);
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

    public function update($data, $page) {
        switch ($data['path']) {
            case 'taobao':
                $path = 'path1';
                break;
            case 'taobao2tmall':
                $path = 'path2';
                break;
            case 'tmall':
                $path = 'path3';
                break;
        }

        $upData = array();
        if (isset($data['region'])) {
            $upData[$path . '_region'] = $data['region']; 
        }
        if (isset($data['price_from'])) {
            $upData[$path . '_price_from'] = $data['price_from']; 
        }
        if (isset($data['price_to'])) {
            $upData[$path . '_price_to'] = $data['price_to']; 
        }
        $upData[$path . '_page'] = $page;

        $sqlArr = array();
        foreach ($upData as $k => $v) {
            $sqlArr[] = $k . " = '" . $v . "'"; 
        }
        $sqlStr = implode(',', $sqlArr);
        if ($page != -1) {
            $sql = "UPDATE keyword SET " . $sqlStr . " WHERE id = {$data['id']} AND " . $path . "_page > " . $page;
            echo $sql . "\n";
        }
        $this->db->query($sql);
    }
}
