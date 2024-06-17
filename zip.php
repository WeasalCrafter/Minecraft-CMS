<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title>Minecraft CMS</title>
</head>
<p>Create World Backup</p>
<div class="white">

<?php
require('lock.php');

date_default_timezone_set('America/New_York');

include_once('query.php');
$config = include('config.php');

$world = $config['worldName'];
$backupsDirectory = $config['backupsDirectory'];
$serverDir = $config['serverDirectory'];
$permissionsScript = $config['permissionsScript'];

query('say Backing up the server, you may experience lag...',1);

$output = shell_exec($permissionsScript);
echo "$output <br>";

function addFolderToZip($folder, $zipArchive, $zipPath) {
    $handle = opendir($folder);
    if (!$handle) {
        throw new Exception("Unable to open directory $folder <br>");
    }

    $zipArchive->addEmptyDir($zipPath);

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $fullPath = $folder . "/" . $entry;
            $zipEntryPath = $zipPath . "/" . $entry;
            if (is_dir($fullPath)) {
                addFolderToZip($fullPath, $zipArchive, $zipEntryPath);
            } else {
                $zipArchive->addFile($fullPath, $zipEntryPath);
            }
        }
    }
    closedir($handle);
}
$time = time();
$zipFileName = "$backupsDirectory/backup-$time.zip";
if (file_exists($zipFileName)) {
    unlink($zipFileName);
}
$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
    exit("Cannot open $zipFileName <br>");
}
$foldersToAdd = [
    "$serverDir/$world",
    "$serverDir/$world"."_nether",
    "$serverDir/$world"."_the_end"
];
foreach ($foldersToAdd as $folder) {
    if (is_dir($folder)) {
        try {
            addFolderToZip($folder, $zip, basename($folder));
            echo "Added folder $folder to ZIP file <br>";
        } catch (Exception $e) {
            echo "Error adding folder $folder: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Folder $folder does not exist and was not added to the ZIP file<br>";
    }
}
$result = $zip->close();

if ($result === FALSE) {
    echo "Error closing the ZIP file, please try again!<br>";
    exit("Failed to close the ZIP file<br>");
}

echo "ZIP file created successfully<br>";
if (file_exists($zipFileName)) {
    echo "ZIP file exists at: $zipFileName<br>";
    query('say Backup complete!',1);
    echo "Backup Successful!";
} else {
    echo "ZIP file was not created.<br>";
}

echo"</div>";

echo "[<a href='/'>home</a>] [<a href='backups.php'>view backups</a>]";

?>


