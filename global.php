<?php
error_reporting(E_ALL);

date_default_timezone_set('PRC');

require(dirname(__FILE__)."/config/config_PHPSay.php");

require(dirname(__FILE__)."/controller/class_Xxtea.php");

require(dirname(__FILE__)."/controller/class_PHPSay.php");

require(dirname(__FILE__)."/controller/function.php");

$loginInfo = isLogin($PHPSayConfig['ppsecure'],$_COOKIE);

$isMobileRequest = isMobileRequest();

$currentPage = ( isset($_GET['page']) && intval($_GET['page']) > 0 ) ? intval($_GET['page']) : 1;
?>