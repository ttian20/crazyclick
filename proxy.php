<?php


require 'class.crawler.php';
$crawler = new crawler('a', 'b');
echo $crawler->proxy;









exit;
$url = 'http://www.tkdaili.com/api/getiplist.aspx?vkey=2C777C9751352F3D8C99355ED68252A2&num=1&country=CN&high=1&style=2';
$proxy = trim(file_get_contents($url));
echo $proxy . "\n";

$requestUrl = 'http://www.aymoo.com/d.php';
$ch = curl_init();
$timeout = 5;
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_PROXY, $proxy);
$info = curl_exec($ch);
if(curl_errno($ch))
{
    echo 'Curl error: ' . curl_error($ch);
}
curl_close($ch);
