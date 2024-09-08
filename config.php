<?php

$hostName = shell_exec('hostname');

$free = [
    'google' => '35.231.92.252',
    'name' => 'Freetime Server',
    'host' => '127.0.0.1',
    'port' => 25575,
    'password' => 'strong-password',
    'serviceName' => 'paper',
    'serverDirectory' => '/var/www/paper',
    'backupsDirectory' => '/var/www/backups',
    'permissionsScript' => '/var/www/html/perms.sh',
    'worldName' => 'freetime',
    'hostname' => "$hostName"
];

$comp = [
    'google' => '35.231.92.252',
    'name' => 'Competition Server',
    'host' => '127.0.0.1',
    'port' => 25575,
    'password' => 'strong-password',
    'serviceName' => 'paper',
    'serverDirectory' => '/var/www/paper',
    'backupsDirectory' => '/var/www/backups',
    'permissionsScript' => '/var/www/html/perms.sh',
    'worldName' => 'competition',
    'hostname' => "$hostName"
];

if(!isset($_SESSION['serverType'])){
    return $comp;
}else{
    if($_SESSION['serverType'] == 'comp'){
	return $comp;
    }elseif($_SESSION['serverType'] == 'free'){
	return $free;
    }
}
