<?php
require('lock.php');

$config = include('config.php');
include_once('query.php');
include('ping.php');

$serviceName = $config['serviceName'];
$serverType = ucfirst($serviceName);

$text = "";
$isActive = false;
$isStarting = false;
$activeCommand = "systemctl is-active $serviceName 2>&1";

$address = "0.0.0.0";
$addressCommand = "hostname -I | awk '{print $1}'";
$address = shell_exec($addressCommand);

$more = "[<a href='server-info.php?display=more'>more</a>]";
$less = "[<a href='server-info.php?display=less'>less</a>]";

$output = shell_exec($activeCommand);
if(trim($output) == "active"){
    $isActive = True;
}

if($isActive == True){
    if (isServerOnline()) {
	$text = "The Server is <b>Online</b> at <b>$address</b>";
	if(isset($_SESSION['more-info'])){
   	    $text = $text . " $less";
    	}else{
    	    $text = $text . " $more";
        }
    } else {
	$text = "The Server is <b>Starting</b>, please refresh!";
	$isStarting = True;
    }
}else{
    $text = "The Server is <b>Offline</b>";
}

echo "<p>$text</p>";

if($isActive == True){

if(isset($_SESSION['more-info'])){
    echo "<div class='white'>";
    echo "<p>Server Type: $serverType</p>";
    if($serverType == "paper" || $serverType == "spigot" || $serverType = "bukkit"){
	$plugins = query('plugins',1);
	$plugins = preg_replace('/§[a-x0-9]/i', '', $plugins);
	$plugins = str_replace("Bukkit Plugins:", "", $plugins);
	$plugins = str_replace("-", "", $plugins);
    	echo "<p>$plugins</p>";
    }
    echo "</div>";
}

}

if(isset($_GET['display'])){

    if($_GET['display'] == 'more'){
	$_SESSION['more-info'] = 1;
    }elseif($_GET['display'] == 'less'){
	unset($_SESSION['more-info']);
    }

    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header("Location: $referrer");
}

?>
