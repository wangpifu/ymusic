<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['group'] < 3 )
{
	header("location:./");
}
else
{
	$template = template( "admin_sponsor.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'headerNavi', "" );

	$template->assign( 'adminNavi', "sponsor" );

	$DB = database();

	$sponsorList = PHPSay::getSponsor($DB,"show");

	$template->assign( 'sponsorList', $sponsorList );

	$template->assign( 'sponsorCount', count($sponsorList) );

	$sponsorAll = PHPSay::getSponsor($DB);

	$template->assign( 'sponsorAll', $sponsorAll );

	$template->assign( 'sponsorAllCount', count($sponsorAll) );

	$DB->close();

	$template->output();
}
?>