<?php
require(dirname(__FILE__)."/global.php");

require(dirname(__FILE__)."/controller/class_Admin.php");

if ( $loginInfo['group'] < 3 )
{
	header("location:./");
}
else
{
	$template = template( "admin_member.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'headerNavi', "" );

	$template->assign( 'adminNavi', "member" );

	$DB = database();

	$template->assign( 'memberList', Admin::getMemberList($DB,$currentPage,30) );

	$DB->close();

	$template->output();
}
?>