<?php
    $mysqli = new mysqli('localhost', 'admin', 'txg19831210', 'crawler');
    $mysqli->query('SET NAMES gbk');
    $sql = "UPDATE keyword SET status = 'expired' WHERE status = 'active' AND end_time <= " . (time() - 86400);
    echo $sql . "\n";
    $mysqli->query($sql);
