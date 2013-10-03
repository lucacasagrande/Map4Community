<?php
include("settings.php");
include("functions.php");

$name = htmlspecialchars(trim($_POST['name']));
$city = htmlspecialchars(trim($_POST['company']));
$lat = htmlspecialchars(trim($_POST['lat']));
$lon = htmlspecialchars(trim($_POST['lon']));
$url = htmlspecialchars(trim($_POST['website']));
$passwd = encrypt_decrypt('encrypt',htmlspecialchars(trim($_POST['passwd'])));

$line = join(';', array($lon,$lat,$name,$city,$url,$passwd));
$line = $line . PHP_EOL;

$fp = fopen($CSVNAME, 'a');
if ($fp == true) {
    fwrite($fp, $line);
    fclose($fp);
} else {
    header('HTTP/1.1 500', 'internal error');
    echo "Please check the permissions of your file";
}
?>