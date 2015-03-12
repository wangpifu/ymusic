<?php
require(dirname(__FILE__)."/config/config_PHPSay.php");

$textArray = array('A','B','C','D','E','F','G','H','i','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');

$keyindex = count($textArray)-1;

$verifyNum = "";

for ( $i=0;$i<5;$i++ )
{
	$verifyNum .= $textArray[rand(0,$keyindex)];
}

if( isset($_GET['referer']) )
{
	if( $_GET['referer'] == "send_password" )
	{
		session_start();

		$_SESSION['identifying_code'] = md5($verifyNum.$PHPSayConfig['ppsecure']);
	}
}

header("Content-type: image/png");

$im = imagecreate(300, 80);

imagecolorallocatealpha($im, 255, 255, 255, 100);

$color = imagecolorallocate($im, rand(0,230), rand(0,230), rand(0,230));

imagettftext($im, 43, 0, 5, 70, $color, 'static/OctemberScript.ttf', $verifyNum);

imagepng($im);

imagedestroy($im); 
?>