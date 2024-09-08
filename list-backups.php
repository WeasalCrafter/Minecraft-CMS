<?php
require('lock.php');
$config = include('config.php');
$name = $config['name'];
?>
<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title>Backups <?php echo(" - $config[hostname]");?></title><?php //echo $name; ?></title>
</head>

<?php

$dir = $config['backupsDirectory'];
$worldName = $config['worldName'];
$dir = "$dir/$worldName";

echo "$name Backups in directory $dir: <br>";
echo "<div class='white'>";
$files = scandir($dir);
$file_count = count($files) - 2;


function getFileSizeFormatted($filename) {
    // Check if file exists
    if (!file_exists($filename)) {
        return "File does not exist";
    }

    // Command to get the file size in bytes
    $command = "stat -c%s " . escapeshellarg($filename);

    // Execute the command and capture the output
    $fileSizeBytes = shell_exec($command);

    // Convert the output to a float
    $fileSizeBytes = (float)$fileSizeBytes;

    // Determine the size in MB and GB
    $sizeInMB = $fileSizeBytes / 1024 / 1024;
    $sizeInGB = $fileSizeBytes / 1024 / 1024 / 1024;

    // Return formatted string based on size
    if ($sizeInGB >= 1) {
        return round($sizeInGB) . " GB";
    } else {
        return round($sizeInMB) . " MB";
    }
}


if (is_dir($dir) && is_readable($dir)) {
    $contents = glob($dir . '/' . $worldName . '-*.zip');
    if($file_count === 0){
	echo "No backups found, click [<a href='create-backup.php'>here</a>] to create one";
    }else{
    foreach ($contents as $item) {
        // Extract timestamp from the filename
        $filename = basename($item);
	$fileSize = getFileSizeFormatted("$dir/$filename");
        preg_match('/'. $worldName . '-(\d+)\.zip/', $filename, $matches);
        if (isset($matches[1])) {
            $timestamp = $matches[1];

            // Convert timestamp to readable date format
            $readableDate = date("F jS, Y h:iA", $timestamp);

            // Output the readable date instead of $item
            echo "$worldName-" . $matches[1] . ".zip ($fileSize) - " . $readableDate . "<br>";
        } else {
            echo $filename . " ($fileSize)";
        }
    }
    }
} else {
    echo "The directory '{$dir}' does not exist or is not readable.";
}
echo "</div>";
echo "[<a href='/'>home</a>] [<a href='create-backup.php'>create backup</a>]";

?>
