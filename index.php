<?php require('lock.php'); ?>

<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title>Minecraft CMS</title>
</head>

<body>
    <h1>Minecraft CMS</h1>

    <?php
	include('server-info.php');

	if ($isActive == True && $isStarting == false){
		include('player-info.php');
	}

	//<a href="zip.php" ><button>Create Backup</button></a> [<a href="backups.php">view</a>]
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
	    <a href="backups.php" ><button>Backups</button></a>

	<?php elseif($isActive == false && $isStarting == false): ?>
	    <form action="/query.php" method="GET">
                <input type="hidden" name="cmd" value="start">
                <button type="submit">Start Server</button>
            </form>
        <?php endif; ?>
    </div>
<?php

if ($isActive == True){

    if(isset($_SESSION['rcon_input']) && isset($_SESSION['rcon_output'])){
        $rcon_input = $_SESSION['rcon_input'];
        $rcon_output = $_SESSION['rcon_output'];

        unset($_SESSION['rcon_input']);
        unset($_SESSION['rcon_output']);

	echo "<hr>";
        echo "<p>Command Sent:</p>";
        echo "<div class='code'><code>$rcon_input</code></div>";
	if($rcon_output !== "Null"){
            echo "<p>Server Response:</p>";
            echo "<div class='code'><code>$rcon_output</code></div>";
    	}
    }
}

?>


</body>

</html>
