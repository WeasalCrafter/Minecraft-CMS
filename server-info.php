
<?php
require('lock.php');

$config = include('config.php');
include_once('query.php');
include('ping.php');

$serviceName = $config['serviceName'];
$serverType = ucfirst($serviceName);

$isTunnel = shell_exec('systemctl is-active tunnel 2>&1');
$tunnelStatus = false;
$enableTunnel = "[<a href='query.php?tunnel=start'>make public</a>]";
$disableTunnel = "[<a href='query.php?tunnel=stop'>make private</a>]";
$tunnelButton = $disableTunnel;

if(trim($isTunnel) == 'active'){
    $tunnelStatus = true;
    $tunnelButton = $disableTunnel;
}else{
    $tunnelStatus = false;
    $tunnelButton = $enableTunnel;
}

$text = "";
$isActive = false;
$isStarting = false;
$activeCommand = "systemctl is-active $serviceName 2>&1";

$address = "0.0.0.0";
$addressCommand = "hostname -I | awk '{print $1}'";
$address = shell_exec($addressCommand);
$address = trim($address);

$version = shell_exec('unzip -p /var/www/paper/paper.jar version.json | grep "name"');
$version = trim($version);
$parts = explode('"', $version);
$version = $parts[3];

$more = "[<a href='server-info.php?display=more'>more info</a>]";
$less = "[<a href='server-info.php?display=less'>less info</a>]";

$showAddress = "[<a href='display.php'>show address</a>]";


$output = shell_exec($activeCommand);
if(trim($output) == "active"){
    $isActive = True;
}

if($isActive == True){
    if (isServerOnline()) {
        if($tunnelStatus){
            $serverText = "The Server is <b>Public</b>, anyone can join";
	}else{
            $serverText = "The Server is <b>Private</b>, only local users can join";
	}
	if(isset($_SESSION['more-info'])){
   	    $serverText = $serverText . " $less" . " $tunnelButton" . " $showAddress";
    	}else{
    	    $serverText = $serverText . " $more" . " $tunnelButton" . " $showAddress";
        }
    } else {
	$serverText = "The Server is <b>Starting</b>, please refresh in a moment!";
	$isStarting = True;
    }
}else{
    $serverText = "The Server is <b>Offline</b>";
}
$serverText = "<p>$serverText</p>";

$globalActive = $isActive;
$globalSarting = $isStarting;
$globalType = $serverType;
$globalVersion = $version;

function displayMore(){
	global $globalActive, $globalStarting, $globalType, $globalVersion;
	if($globalActive == True && $globalStarting == false){
	if(isset($_SESSION['more-info'])){
	    echo "<div class='white'>";
	    echo "<p>Server Type: $globalType on Minecraft version [<b>$globalVersion</b>]</p>";
	    if($globalType == "Paper"){
		$plugins = query('plugins');
		$plugins = preg_replace('/§[a-x0-9]/i', '', $plugins);
		$plugins = str_replace("Bukkit Plugins:", "", $plugins);
		$plugins = str_replace("-", "", $plugins);
	    	echo "<p>$plugins</p>";
	    }
	    echo "</div>";
	}
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
