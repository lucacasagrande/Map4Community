<?php
include("settings.php");
include("functions.php");

$name = htmlspecialchars(trim($_POST['name']));
$passwd = htmlspecialchars(trim($_POST['passwd']));

$orig_file = fopen($CSVNAME, 'r');
$newname = $CSVNAME . ".new";
$new_file = fopen($newname, 'w');

if ($new_file == false) {
    header('HTTP/1.1 500', 'internal error');
    echo "Please check the permissions of your directory";
}

if ($orig_file == true) {
    while (($data = fgetcsv($orig_file)) !== FALSE) {
	$line = explode(';', $data[0]);
	if (checkArray($line, $name, $passwd) == false) {
	    fputcsv($new_file, $line, ";");
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