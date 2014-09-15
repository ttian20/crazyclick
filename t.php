<?php
        $params = array('host' =>'127.0.0.1',
                        'port' => 5672,
                        'login' => 'guest',
                        'password' => 'guest',
                        'vhost' => '/');
        $conn = new AMQPConnection($params);
        $conn->connect();
        var_dump($conn);
        $channel = new AMQPChannel($conn);
        var_dump($channel);
