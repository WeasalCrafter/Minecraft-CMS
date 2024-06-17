<?php
require('lock.php');

$config = include('config.php');
include_once('query.php');

$more = "[<a href='player-info.php?display=more'>show</a>]";
$less = "[<a href='player-info.php?display=less'>hide</a>]";

$button = $more;
$output = query('list',1);

if(isset($_GET['display'])){
    if($_GET['display'] == 'more'){
	$_SESSION['more-players'] = 1;
    }elseif($_GET['display'] == 'less'){
	unset($_SESSION['more-players']);
    }

    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header("Location: $referrer");
}

if(isset($_SESSION['more-players'])){
    $button = $less;
}

$currentplayers = 0;
$maxplayers = 0;

preg_match('/There are (\d+) of a max of (\d+) players online/', $output, $matches);
$currentPlayers = (int)$matches[1];
$maxPlayers = (int)$matches[2];

$outputSplit = explode(":", $output);

if($currentPlayers > 0){
    $players = explode(',', $outputSplit[1]);
    if($currentPlayers == 1){
        echo "<p><i>There is $currentPlayers player out of $maxPlayers online</i> $button </p>";
    }else{
        echo "<p><i>There are $currentPlayers players out of $maxPlayers online</i> $button </p>";
    }
    if(isset($_SESSION['more-players'])){
        echo "<div class='white'>";
        foreach ($players as $username) {
            $username = trim($username);
            echo "<p>$username [<a href='query.php?rcon=kick+$username'>kick</a>]</p>";
        }
        echo "</div>";
    }
}else{
    echo "<p><i>There are no players online</i></p>";
}

?>
