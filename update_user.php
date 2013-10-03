<?php
include("settings.php");
include("functions.php");

$oldname = htmlspecialchars(trim($_POST['oldname']));
$passwd = htmlspecialchars(trim($_POST['passwd']));
$name = htmlspecialchars(trim($_POST['name']));
$city = htmlspecialchars(trim($_POST['company']));
$url = htmlspecialchars(trim($_POST['website']));

$orig_file = fopen($CSVNAME, 'r+');
$orig_file = fopen($CSVNAME, 'r');
$newname = $CSVNAME . ".new";
$new_file = fopen($newname, 'w');

if ($orig_file == true) {
    while (($data = fgetcsv($orig_file)) !== FALSE) {
	$line = explode(';', $data[0]);
	if (checkArray($line, $oldname, $passwd) == false) {
	    $line = $data[0] . PHP_EOL;
	    fwrite($new_file, $line);
	} else {
	    $newline = join(';', array($line[0],$line[1],$name,$city,$url,encrypt_decrypt('encrypt',$passwd)));
	    $newline = $newline . PHP_EOL;
	    fwrite($new_file, $newline);
	}
    }
} else {
    header('HTTP/1.1 500', 'internal error');
    echo "Please check the permissions of your file";
}
fclose($orig_file);
fclose($new_file);

$newname = './' . $newname;
$origname = './' . $CSVNAME;
if (rename($newname, $origname) == false) {
    header('HTTP/1.1 500', 'internal error');
    echo "Please check the permissions of your directory";
}
?>