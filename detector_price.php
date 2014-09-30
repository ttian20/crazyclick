<?php
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
require_once dirname(__FILE__) . '/class.crawler.php';
require_once dirname(__FILE__) . '/class.proxy.php';
require_once dirname(__FILE__) . '/class.detector.php';

$id = 0;
if ($argv[1]) {
    $id = $argv[1];
}
$detector = new detector();
$detector->run($id);
