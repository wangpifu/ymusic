<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['group'] < 3 )
{
	header("location:./");
}
else
{
	$template = template( "admin_cache.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'headerNavi', "" );

	$template->assign( 'adminNavi', "cache" );

	$template->output();
}
?>