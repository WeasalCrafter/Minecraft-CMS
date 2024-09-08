<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require('lock.php');
$config = require('config.php');
$name = $config['name'];

$isTunnel = shell_exec('systemctl is-active tunnel 2>&1');
if(trim($isTunnel) == 'active'){
    $public_url = $config['google'];
}

?>
<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title>Server Address<?php echo " - $config[hostname]" ?></title>
</head>

<body class="dirt">

<div class="center">

<?php

$address = "0.0.0.0";
$addressCommand = "hostname -I | awk '{print $1}'";
$address = shell_exec($addressCommand);
$address = trim($address);

$hostname = trim($config['hostname']);

if(isset($_GET['hostname'])){
    echo "<p class='displayBtn'>[<a href='/display.php'>show IPv4</a>]</p>";
    echo "<p class='ip'>$hostname.local</p>";
}elseif($public_url){
    echo "<p class='ip'>$public_url</p>";
}else{
    echo "<p class='ip'>$address</p>";
    echo "<p class='displayBtn'>[<a href='/display.php?hostname'>show hostname</a>]</p>";
}
echo "<p class='topLeft'>[<a href='/'>home</a>]</p>";
?>

</div>

</body>
