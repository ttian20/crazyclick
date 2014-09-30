<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.crawler.php';
require_once dirname(__FILE__) . '/class.proxy.php';
require_once dirname(__FILE__) . '/class.detector.php';

$mysqli = new mysqli('10.168.45.191', 'admin', 'txg19831210', 'crawler');
$mysqli->query('SET NAMES gbk');

$queueName = 'q_crawler';
$params = array('host' =>'10.168.45.191',  
                'port' => 5672,  
                'login' => 'guest',  
                'password' => 'guest',  
                'vhost' => '/kwd');  
$conn = new AMQPConnection($params);  
$conn->connect();
$channel = new AMQPChannel($conn);
$queue = new AMQPQueue($channel);
$queue->setName($queueName);

$crawler = new crawler();
while ($message = $queue->get(AMQP_AUTOACK)) {
    $kwd = $message->getBody();
    $kwdArr = unserialize($kwd);
    $crawler->run($kwdArr);
    
#    print_r($kwdObj);
#    $price = $detector->run($kwdObj);
#    if ($price['start_price'] && $price['end_price']) {
#        $sql = "SELECT * FROM price WHERE kid = {$kwdObj->id} LIMIT 1";
#        $result = $mysqli->query($sql);    
#        if ($result->num_rows) {
#            $sql = "UPDATE price SET min_price = '{$price['start_price']}', max_price = '{$price['end_price']}', region = '{$price['region']}', crawl_status = 2, last_update = " . time(). " WHERE kid = " . $kwdObj->id;
#        }
#        else {
#            $sql = "INSERT INTO price SET kid = {$kwdObj->id}, shop_type = '{$kwdObj->shop_type}', min_price = '{$price['start_price']}', max_price = '{$price['end_price']}', region = '{$price['region']}', crawl_status = 2, last_update = " . time();
#        }
#        echo $sql . "\n";
#        $mysqli->query($sql);
#    }
}
$conn->disconnect();
exit;

