<?php
set_time_limit(0);
require('lock.php');
include_once('query.php');
$config = include('config.php');
$name = $config['name'];
date_default_timezone_set('America/New_York');

function logOutput($message) {
    echo($message ."\n");
    file_put_contents('command.txt', "Server> ".$message."\n", FILE_APPEND);
}

?>
<html>

<head>
    <link rel="stylesheet" href="css/main.css">
    <title>Create Backup<?php //echo $name; ?></title>
</head>
<p>Create <?php echo $name; ?> World Backup</p>
<div class="white">
<?php
$backupsDirectory = $config['backupsDirectory'];
$serverDir = $config['serverDirectory'];
$permissionsScript = $config['permissionsScript'];
$worldName = $config['worldName'];
$backupsDirectory = "$backupsDirectory/$worldName";

query('say §aBacking up the server, you may experience lag...');
query('save-off');
query('save-all');


$contentToAppend = "Minecraft CMS> Create backup\nServer> Creating backup...\n";
if(file_put_contents('command.txt', $contentToAppend, FILE_APPEND)){}else{$_SESSION['rcon_output'] = 'error';}

$output = shell_exec($permissionsScript);
if($output){
    logOutput("Permissions Set Successfully!");
}else{
    logOutput("ERROR: Permissions Not Set");
}

function addFolderToZip($folder, $zipArchive, $zipPath) {
    $handle = opendir($folder);
    if (!$handle) {
        $message = "Unable to open directory $folder";
        logOutput($message);
        throw new Exception($message);
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
$zipFileName = "$backupsDirectory/$worldName-$time.zip";
if (file_exists($zipFileName)) {
    unlink($zipFileName);
}
$zip = new ZipArchive();
if ($zip->open($zipFileName, ZipArchive::CREATE) !== TRUE) {
    $message = "Cannot open $zipFileName";
    logOutput($message);
    exit($message);
}
$foldersToAdd = [
    "$serverDir/$worldName",
    "$serverDir/$worldName"."_nether",
    "$serverDir/$worldName"."_the_end"
];
foreach ($foldersToAdd as $folder) {
    if (is_dir($folder)) {
        try {
            addFolderToZip($folder, $zip, basename($folder));
            $message = "Added folder $folder to ZIP file";
            //echo $message;
            logOutput($message);
        } catch (Exception $e) {
            $message = "Error adding folder $folder: " . $e->getMessage();
            //echo $message;
            logOutput($message);
        }
    } else {
        $message = "Folder $folder does not exist and was not added to the ZIP file";
        //echo $message;
        logOutput($message);
    }
}
$result = $zip->close();
if ($result === FALSE) {
    $message = "Error closing the ZIP file, please try again!";
    //echo $message;
    logOutput($message);
    exit("Failed to close the ZIP file");
}
$message = "ZIP file created successfully";
//echo $message;
logOutput($message);
if (file_exists($zipFileName)) {
    $message = "ZIP file exists at: $zipFileName";
    //echo $message;
    logOutput($message);
    query('say §aBackup complete!');
    $message = "Backup Successful!";
    //echo $message;
    logOutput($message);
} else {
    $message = "ZIP file was not created.";
    //echo $message;
    logOutput($message);
}
query('save-on',1);
echo"</div>";

echo "[<a href='/'>home</a>] [<a href='list-backups.php'>view backups</a>]";

?>
