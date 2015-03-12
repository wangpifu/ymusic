<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php");
}
else
{
	$currentId = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

	if( !$isMobileRequest )
	{
		header("location:./?cid=".$currentId);
	}
	else
	{
		$template = template( "mobile_write.html" );

		$template->assign( 'PHPSayConfig', $PHPSayConfig );

		$template->assign( 'loginInfo', $loginInfo );

		$DB = database();

		$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );

		$template->assign( 'clubList', PHPSay::getClubList( $DB, $currentId ) );

		$DB->close();

		$template->output();
	}
}
?>