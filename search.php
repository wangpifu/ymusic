<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php?return=".urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER["QUERY_STRING"]));
}
else
{
	$searchType = ( isset($_GET['t']) && $_GET['t'] == "reply" ) ? $_GET['t'] : "topic";

	$searchWord = isset($_GET['q']) ? filterCode($_GET['q']) : "";

	if( strlen($searchWord) < "2" || getStrlen($searchWord) > "15" )
	{
		header("location:./");
	}
	else
	{
		$template = template( "search.html" );

		$template->assign( 'PHPSayConfig', $PHPSayConfig );

		$template->assign( 'loginInfo', $loginInfo );

		$template->assign( 'headerNavi', "" );

		$template->assign( 'searchType', $searchType );

		$template->assign( 'searchWord', stripslashes($searchWord) );

		$DB = database();

		$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

		$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

		$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );
		
		$template->assign( 'searchList', PHPSay::searchPost($DB,$searchType,$searchWord,$currentPage,30) );

		$DB->close();

		$template->output();
	}
}
?>