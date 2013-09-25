<?php
include("settings.php");

$name = htmlspecialchars(trim($_POST['name']));
$city = htmlspecialchars(trim($_POST['company']));
$lat = htmlspecialchars(trim($_POST['lat']));
$lon = htmlspecialchars(trim($_POST['lon']));
$url = htmlspecialchars(trim($_POST['website']));
$passwd = htmlspecialchars(trim($_POST['passwd']));

$line = array($lon,$lat,$name,$city,$url,$passwd);

$fp = fopen($CSVNAME, 'a');
fputcsv($fp, $line, ";");
fclose($fp);
?>