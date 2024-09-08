<?php
require('lock.php');

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

function packRconPacket($id, $type, $payload) {
    $packet = pack('VV', $id, $type) . $payload . "\x00\x00";
    return pack('V', strlen($packet)) . $packet;
}

function unpackRconPacket($data) {
    $length = unpack('V', substr($data, 0, 4))[1];
    $packet = substr($data, 4, $length);
    $id = unpack('V', substr($packet, 0, 4))[1];
    $type = unpack('V', substr($packet, 4, 4))[1];
    $payload = substr($packet, 8, -2);
    return ['id' => $id, 'type' => $type, 'payload' => $payload];
}

function query($command){
    $config = include('config.php');

    $host = $config['host'];
    $port = $config['port'];
    $password = $config['password'];

    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    if (!$socket) {
        die("<p><i>Unable to connect: $errstr ($errno)</p></i><p><i>If the server is starting, please wait!</i></p>");
    }

    $requestId = 1;
    $loginPacket = packRconPacket($requestId, 3, $password);

    fwrite($socket, $loginPacket);
    $response = fread($socket, 4096);
    $responsePacket = unpackRconPacket($response);

    if ($responsePacket['id'] != $requestId || $responsePacket['type'] != 2) {
        die('<p><i>Authentication failed</p></i>');
    }

    $requestId++;
    $commandPacket = packRconPacket($requestId, 2, $command);

    fwrite($socket, $commandPacket);
    $response = fread($socket, 4096);
    $responsePacket = unpackRconPacket($response);

    $output = $responsePacket['payload'];
    if(trim($output) == Null){
	$output = "Command sent, no response";
    }
    fclose($socket);

    return $output;
}

if(isset($_GET['rcon'])){
    $rcon = $_GET['rcon'];
    $output = query($rcon);

    $contentToAppend = "Minecraft CMS> $rcon\nServer> $output\n";
    if(file_put_contents('command.txt', $contentToAppend, FILE_APPEND)){}else{$_SESSION['rcon_output'] = 'error';}

    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header("Location: $referrer");
}

if (isset($_GET['cmd'])) {
    $cmd = $_GET['cmd'];

    $config = include('config.php');
    $service = $config['serviceName'];

    switch ($cmd) {
        case 'start':
            $output = shell_exec("sudo systemctl start $service 2>&1");
	    $fileHandle = fopen('command.txt', 'w');
	    fclose($fileHandle);
            break;
        case 'stop':
            $output = shell_exec("sudo systemctl stop $service 2>&1");
	    $fileHandle = fopen('command.txt', 'w');
	    fclose($fileHandle);
            break;
        default:
            $output = "Invalid command";
    }
    $_SESSION['rcon_input'] = $cmd;
    $_SESSION['rcon_output'] = $output;
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header("Location: $referrer");
}

if (isset($_GET['tunnel'])) {
    $cmd = $_GET['tunnel'];
    $service = 'tunnel';

    switch ($cmd) {
        case 'start':
            $output = shell_exec("sudo systemctl start $service 2>&1");
            break;
        case 'stop':
            $output = shell_exec("sudo systemctl stop $service 2>&1");
            break;
        default:
            $output = "Invalid command";
    }
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header("Location: $referrer");
}
