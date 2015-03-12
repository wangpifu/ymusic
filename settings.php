<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php");
}
else
{
	$settingType  = ( isset($_GET['with']) && in_array($_GET['with'], array("email","password")) ) ? $_GET['with'] : "avatar";

	$template = template( $isMobileRequest ? "mobile_settings.html" : "settings.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'settingType', $settingType );
	
	$DB = database();

	$userInfo = PHPSay::getMemberInfo($DB,"uid",$loginInfo['uid']);

	$template->assign( 'userInfo', $userInfo );

	if( !$isMobileRequest )
	{
		$template->assign( 'headerNavi', "settings" );

		$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

		$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

		$template->assign( 'memberBalance', $userInfo['balance'] );
	}	

	$DB->close();

	$template->output();
}
?>