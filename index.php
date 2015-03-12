<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php");
}
else
{
	$postType = ( isset($_GET['list']) &&  in_array($_GET['list'], array("likedconcerts")) ) ? $_GET['list'] : "allconcerts";

	$template = template( "index.html" );

	$template->assign( 'PHPSayConfig', $PHPSayConfig );

	$template->assign( 'loginInfo', $loginInfo );

	$DB = database();

	//echo "<script>console.log('uid:".$loginInfo["uid"]."');</script>";
	$memberType = PHPSay::getMemberType($DB,$loginInfo["uid"]);
	//echo "<script>console.log('type:".$memberType."');</script>";
	$uid = PHPSay::getPartId($DB,$loginInfo["uid"],$memberType);

	$userInfo = PHPSay::getMemberInfo($DB,"id",$uid,$memberType);

	$uid = PHPSay::getPartId($DB,$loginInfo["uid"],$memberType);
	//echo "<script>console.log('pid:".$uid."');</script>";
	if( $memberType == 'user' )
	{
		$allConcerts=PHPSay::getAllConcert($DB);

		$template->assign( 'allconcerts', $allConcerts);
		
		//$temp=PHPSay::getLikedConcert($allConcerts,$userInfo["type"]);
		//foreach($temp as $k=>$v)echo "<script>console.log('".$k."=>".$v."');</script>";
		$template->assign( 'likedconcerts', PHPSay::getLikedConcert($allConcerts,$userInfo["type"]));
		//echo "<script>console.log('".$temp[0][0]."');</script>";
		$template->assign( 'ufollowing', PHPSay::getMemberUFollowing($DB,$uid) );

		$template->assign( 'afollowing', PHPSay::getMemberAFollowing($DB,$uid) );
		//$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

		//$template->assign( 'sponsorList', PHPSay::getSponsor($DB,"show") );
	}
	$template->assign( 'userInfo', $userInfo);

	$template->assign( 'headerNavi', "home" );

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