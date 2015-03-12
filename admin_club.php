<?php
require(dirname(__FILE__)."/global.php");

require(dirname(__FILE__)."/controller/class_Admin.php");

if ( $loginInfo['group'] < 3 )
{
	header("location:./");
}
else
{
	$template = template( "admin_club.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'headerNavi', "" );

	$template->assign( 'adminNavi', "club" );

	$clubType = ( isset($_GET['type']) && $_GET['type'] == "trash" ) ? 0 : 1;

	$template->assign( 'clubType', $clubType );

	$DB = database();

	$template->assign( 'clubList', Admin::getAdminClub($DB, $clubType) );

	$DB->close();

	$template->output();
}
?>