<?php

//$config = include('config.php');
//$serverDir = $config['serverDirectory'];
//§aFuture Stars Minecraft Server§r§r\n§oServer Mode\: §c§oCompetition

function switchMotd($new_level_name){
    if($new_level_name == 'freetime'){
	$motd = '§oServer Mode\: §c§oFreetime';
    }elseif($new_level_name == 'competition'){
	$motd = '§oServer Mode\: §c§oCompetition';
    }else{
	$motd = '§oServer Mode\: §c§oISSUE WITH MOTD';
    }
    $file_path = '/var/www/paper/server.properties'; //$serverDir . '/server.properties';

    $file_content = file_get_contents($file_path);
    if ($file_content === false) {
        die('Failed to read the server.properties file');
    }

    $pattern = '/^motd=.*$/m';
    $replacement = 'motd=' . $motd;

    $new_file_content = preg_replace($pattern, $replacement, $file_content);
    if ($new_file_content === null) {
        die('Failed to replace the motd in the server.properties file');
    }
    $result = file_put_contents($file_path, $new_file_content);
    if ($result === false) {
        die('Failed to write the updated server.properties file');
    }
    return 'The motd has been successfully updated!';
}

function switchMode($new_gamemode){
    $file_path = '/var/www/paper/server.properties'; //$serverDir . '/server.properties';

    $file_content = file_get_contents($file_path);
    if ($file_content === false) {
        die('Failed to read the server.properties file');
    }

    $pattern = '/^gamemode=.*$/m';
    $replacement = 'gamemode=' . $new_gamemode;

    $new_file_content = preg_replace($pattern, $replacement, $file_content);
    if ($new_file_content === null) {
        die('Failed to replace the level-name in the server.properties file');
    }
    $result = file_put_contents($file_path, $new_file_content);
    if ($result === false) {
        die('Failed to write the updated server.properties file');
    }
    return 'The gamemode has been successfully updated to "' . $new_level_name . '"!';
}

function switchWorld($new_level_name){
    $file_path = '/var/www/paper/server.properties'; //$serverDir . '/server.properties';

    $file_content = file_get_contents($file_path);
    if ($file_content === false) {
        die('Failed to read the server.properties file');
    }
    $pattern = '/^level-name=.*$/m';
    $replacement = 'level-name=' . $new_level_name;

    $new_file_content = preg_replace($pattern, $replacement, $file_content);
    if ($new_file_content === null) {
        die('Failed to replace the level-name in the server.properties file');
    }
    $result = file_put_contents($file_path, $new_file_content);
    if ($result === false) {
        die('Failed to write the updated server.properties file');
    }

    if($new_level_name == "freetime"){
	switchMode('survival');
    }elseif($new_level_name == "competition"){
        switchMode('creative');
    }else{
	switchMode('survival');
    }
    switchMotd($new_level_name);
    return 'The level-name has been successfully updated to "' . $new_level_name . '"!';
}

function getWorld() {
    $file_path = '/var/www/paper/server.properties'; //$serverDir . '/server.properties';
    // Read the file content into an array of lines
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Iterate over each line to find the level-name property
    foreach ($lines as $line) {
        if (strpos($line, 'level-name=') === 0) {
            // Extract the level name value
            list(, $levelName) = explode('=', $line, 2);
            if(trim($levelName) == 'freetime'){
	    	return 'free';
	    }else{
	    	return 'comp';
	    }
        }
    }

    // Return null if the level-name property is not found
    return null;
}
?>
