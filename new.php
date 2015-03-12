<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php?return=".urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER["QUERY_STRING"]));
}
else
{
	$listType = ( isset($_GET['list']) && $_GET['list'] == "reply" ) ? "reply" : "topic";

	$template = template( "new.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'headerNavi', "" );

	$template->assign( 'listType', $listType );

	$DB = database();

	$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

	$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

	$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );

	$template->assign( 'postList', PHPSay::searchPost($DB,$listType,"",$currentPage,30) );

	$DB->close();

	$template->output();
}
?>