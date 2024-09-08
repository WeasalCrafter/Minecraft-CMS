<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require('lock.php');
include('world.php');

$config = require('config.php');
//echo($config['hostname']);

$name = $config['name'];
$selectedWorld = getWorld();

if($_SESSION['serverType'] != $selectedWorld){
    $_SESSION['serverType'] = $selectedWorld;
    header("Location: index.php");
}


if(isset($_GET['switch']) && $ping == false){
    if(!isset($_SESSION['serverType'])){
    	$_SESSION['serverType'] = 'comp';
    }else{
    	if($_SESSION['serverType'] == 'comp'){
            $_SESSION['serverType'] = 'free';
	    switchWorld("freetime");
 	}else{
	    $_SESSION['serverType'] = 'comp';
	    switchWorld("competition");
	}
    }
    header("Location: /");
}

if(isset($_GET['clear'])){
    $fileHandle = fopen('command.txt', 'w');
    fclose($fileHandle);
    header('Location: index.php');
}
?>

<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title><?php echo "$name - $config[hostname]";?></title>
</head>

<body>
    <div class='flex'>
    <?php
	if(trim(shell_exec('systemctl is-active paper')) == 'active'){
	    echo "<h1>$name - $config[hostname]</h1>";
	}else{
	    echo "<h1>$name - $config[hostname]</h1><p class='swapWorld'>[<a href='index.php?switch'>change world</a>]</p>";
	}
    ?>
    </div>

    <?php
	include('server-info.php');
	if($isActive == False && $isStarting == false){
	    echo '<div class="flex">';
	    echo $serverText;
            echo '<form action="/query.php" method="GET" style="margin: auto 10px">';
            	echo '<input type="hidden" name="cmd" value="start">';
            	echo '<button type="submit" >Start Server</button>';
            echo '</form>';
	}elseif($isActive == True && $isStarting == True){
	    echo $serverText;
	}

	if ($isActive == True && $isStarting == false){
		echo $serverText;
		displayMore();
		include('player-info.php');
	}
    ?>

    <div class="container">
        <?php if($isActive == True && $isStarting == false): ?>
            <form id="commandForm" action="/query.php" method="GET">
                <input type="text" name="rcon" placeholder="Type a Command" required>
                <button type="submit">Send Command</button>
            </form>

	    <form action="/query.php" method="GET">
		<input type="hidden" name="cmd" value="stop">
		<button type="submit" class="stop">Stop Server</button>
	    </form>
	    <a href="list-backups.php" ><button>Backups</button></a>
        <?php endif; ?>

	<p class='logout'>[<a href="logout.php">Logout</a>]</p>
    </div>


    <?php if($isActive == True && $isStarting == false): ?>
    <div class='flex'>
    	<p>Server Console:</p>
    	<p style='margin-left:auto;'>[<a href='index.php?clear'>clear</a>]</p>
    </div>
    <div class="terminal" id="terminal" style='height:300px' readonly><?php
       	$content = file_get_contents('command.txt');
       	$content = str_ireplace('Minecraft CMS', '<span class="input">Minecraft CMS</span>', $content);
        $content = str_ireplace('Server', '<span class="output">Server</span>', $content);

	if($isActive == True && $isStarting == false){
	    if(trim($content) != Null){
	        echo $content;
	    }else{
	    	echo '<i style="color: darkgrey;">No content to display<i>';
	    }
	}
    ?></div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const terminal = document.getElementById('terminal');
            terminal.scrollTop = terminal.scrollHeight;
        });
    </script>

</body>

</html>
