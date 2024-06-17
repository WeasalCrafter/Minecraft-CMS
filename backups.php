<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title>Minecraft CMS</title>
</head>

<?php
require('lock.php');

$config = include('config.php');
$dir = $config['backupsDirectory'];

echo "Backups in directory $dir: <br>";
echo "<div class='white'>";
$files = scandir($dir);
$file_count = count($files) - 2;

if (is_dir($dir) && is_readable($dir)) {
    $contents = glob($dir . '/backup-*.zip');

    if($file_count === 0){
	echo "No backups found, click [<a href='zip.php'>here</a>] to create one";
    }else{
    foreach ($contents as $item) {
        // Extract timestamp from the filename
        $filename = basename($item);
        preg_match('/backup-(\d+)\.zip/', $filename, $matches);
        if (isset($matches[1])) {
            $timestamp = $matches[1];

            // Convert timestamp to readable date format
            $readableDate = date("F jS, Y h:iA", $timestamp);

            // Output the readable date instead of $item
            echo "backup-" . $matches[1] . ".zip - " . $readableDate . "<br>";
        } else {
            echo "Invalid filename format: $filename <br>";
        }
    }
    }
} else {
    echo "The directory '{$dir}' does not exist or is not readable.";
}
echo "</div>";
echo "[<a href='/'>home</a>] [<a href='zip.php'>create backup</a>]";

?>
