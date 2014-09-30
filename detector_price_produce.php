<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.crawler.php';
require_once dirname(__FILE__) . '/class.proxy.php';
require_once dirname(__FILE__) . '/class.detector.php';
$kid = 0;
if (isset($argv[1])) {
    $kid = $argv[1];
}

$mysqli = new mysqli('10.168.45.191', 'admin', 'txg19831210', 'crawler');
$mysqli->query('SET NAMES gbk');
$sql = "SELECT * FROM keyword WHERE status = 'active'";
if ($kid) {
    $sql .= " AND id = {$kid}";
}
$result = $mysqli->query($sql);
if (!$result) {
    exit("no record\n");
}

$params = array('host' =>'10.168.45.191',  
                'port' => 5672,  
                'login' => 'guest',  
                'password' => 'guest',  
                'vhost' => '/kwd');  

$conn = new AMQPConnection($params);  
$conn->connect();
$channel = new AMQPChannel($conn);
$exchange = new AMQPExchange($channel);
$exchange->setName('e_price');
while($obj = $result->fetch_object()) {
    $exchange->publish(serialize($obj), 'r_price');
}
$conn->disconnect();
