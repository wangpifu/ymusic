<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php");
}
else
{
	$template = template( $isMobileRequest ? "mobile_balance.html" : "balance.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$DB = database();

	if( !$isMobileRequest )
	{
		$template->assign( 'headerNavi', "" );

		$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

		$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );
	}

	$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );

	if( isset($_GET['type']) && $_GET['type'] == "recharge" )
	{
		$template->assign( 'balanceType', '0' );

		$template->assign( 'resultsList', PHPSay::getUserOrderForm($DB,$loginInfo['uid'],$currentPage,30) );
	}
	else
	{
		$template->assign( 'balanceType', '' );

		$template->assign( 'resultsList', PHPSay::getUserBalance($DB,$loginInfo['uid'],$currentPage,30) );
	}

	$DB->close();

	$template->output();
}
?>