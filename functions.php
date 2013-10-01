<?php

function encrypt_decrypt($action, $string) {
   $output = false;

   $key = 'My strong random secret key';

   // initialization vector 
   $iv = md5(md5($key));

   if( $action == 'encrypt' ) {
       $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
       $output = base64_encode($output);
   }
   else if( $action == 'decrypt' ){
       $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
       $output = rtrim($output);
   }
   return $output;
}

function checkArray($array, $user, $password) {
    if ($array[2] == $user) {
	$passwd = encrypt_decrypt('decrypt', $array[5]);
	if ($password == $passwd) {
	    return true;
	} else {
	    return false;
	}
    } else {
	return false;
    }
}

?>