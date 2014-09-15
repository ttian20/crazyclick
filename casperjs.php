<?php
$url = 'http://www.tkdaili.com/api/getiplist.aspx?vkey=2C777C9751352F3D8C99355ED68252A2&num=1&country=CN&high=1&style=2';
$proxy = trim(file_get_contents($url));

$kwd = urlencode('负重绑腿');
$nid = '36962206480';

$cmd = "/usr/bin/casperjs --proxy=".$proxy." /var/html/casperjs/tb.js ".$kwd." ".$nid;
//echo $cmd . "\n";
system($cmd);
