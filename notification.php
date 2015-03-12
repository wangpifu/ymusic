<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php");
}
else
{
	$template = template( $isMobileRequest ? "mobile_notification.html" : "notification.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$notifyStatus = ( isset($_GET['status']) && $_GET['status'] == "unread" ) ? "0" : "";

	$template->assign( 'notifyStatus', $notifyStatus );

	$DB = database();

	$notificationList = PHPSay::getNotification($DB,$loginInfo['uid'],0,$notifyStatus,$currentPage,30);

	$template->assign( 'notificationList', $notificationList );

	if(!$isMobileRequest)
	{
		$template->assign( 'headerNavi', "at" );

		if( $notifyStatus == "0" )
		{
			$template->assign( 'notificationNumber', $notificationList['count'] );
		}
		else
		{
			$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );
		}

		$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

		$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );
	}

	$DB->close();
	
	$template->output();
}
?>