<?php
$name = htmlspecialchars(trim($_POST['name']));
$city = htmlspecialchars(trim($_POST['company']));
$lat = htmlspecialchars(trim($_POST['lat']));
$lon = htmlspecialchars(trim($_POST['lon']));

$line = array($lon,$lat,$name,$city);

$fp = fopen('user.csv', 'a');
fputcsv($fp, $line, ";");
fclose($fp);
?>