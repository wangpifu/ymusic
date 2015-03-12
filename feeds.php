<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php");
}
else
{
	$postType = ( isset($_GET['list']) &&  in_array($_GET['list'], array("feeds")) ) ? $_GET['list'] : "allfeeds";

	$template = template( "feeds.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$DB = database();

	//echo "<script>console.log('uid:".$loginInfo["uid"]."');</script>";
	$memberType = PHPSay::getMemberType($DB,$loginInfo["uid"]);
	//echo "<script>console.log('type:".$memberType."');</script>";
	$uid = PHPSay::getPartId($DB,$loginInfo["uid"],$memberType);

	$userInfo = PHPSay::getMemberInfo($DB,"id",$uid,$memberType);
	//echo "<script>console.log('pid:".$uid."');</script>";
	$allFeeds=PHPSay::getAllFeeds($DB);

	$template->assign( 'allfeeds', $allFeeds);
	//echo "<script>console.log('".implode(",",$allFeeds[0])."');</script>";
	if( $memberType == 'user' )
	{	
		$ufollowing=PHPSay::getMemberUFollowing($DB,$uid);

		$afollowing=PHPSay::getMemberAFollowing($DB,$uid);

		$template->assign( 'afollowing', $afollowing );

		$template->assign( 'ufollowing', $ufollowing );

		$template->assign( 'feeds', PHPSay::getFeeds($allFeeds,$ufollowing,$afollowing));
		//$temp=PHPSay::getLikedConcert($allConcerts,$userInfo["type"]);
		//foreach($temp as $k=>$v)echo "<script>console.log('".$k."=>".$v."');</script>";
		//echo "<script>console.log('".$temp[0][0]."');</script>";
		
		//$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

		//$template->assign( 'sponsorList', PHPSay::getSponsor($DB,"show") );
	}
	$template->assign( 'userInfo', $userInfo);

	$template->assign( 'headerNavi', "feeds" );

	$template->assign( 'postType', $postType );

	/*$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

	$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );

	$clubList = PHPSay::getClubList( $DB, isset($_GET['cid']) ? intval($_GET['cid']) : 0 );

	$template->assign( 'clubList', $clubList );

	$template->assign( 'topicList', PHPSay::getTopic($DB,"club",$clubList,$currentPage,30) );*/

	$DB->close();

	$template->output();
}
?>