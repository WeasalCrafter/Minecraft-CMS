<?php
require('lock.php');

function isServerOnline() {

    $config = include('config.php');

    $host = $config['host'];
    $port = $config['port'];
    $password = $config['password'];

    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    if (!$socket) {
  	return false;
    }

    fclose($socket);
    return true; // Server is online
}

