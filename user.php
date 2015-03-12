<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php?return=".urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER["QUERY_STRING"]));
}
else
{
	//echo "<script>console.log('aha');</script>";
	$postType = ( isset($_GET['list']) &&  in_array($_GET['list'], array("history","type","ufollowing","afollowing")) ) ? $_GET['list'] : "recommend";

	$memberId = ( isset($_GET['id'])) ? $_GET['id'] : $loginInfo['uid'];

	if( !is_numeric($memberId) && checkNickname($memberId) != "" )
	{
		header("location:./user.php");
	}
	else
	{
		$DB = database();

		$memberType = PHPSay::getMemberType($DB,$memberId);
		//echo "<script>console.log('type:".$memberType."');</script>";
		$uid = PHPSay::getPartId($DB,$memberId,$memberType);

		$userInfo = PHPSay::getMemberInfo( $DB, is_numeric($memberId) ? "id" : "name", $uid , $memberType );

		if( $userInfo["uid"] < 1 )
		{
			header("location:./user.php");
		}
		else
		{
			$template = template( "user.html" );

			$template->assign( 'PHPSayConfig', $PHPSayConfig );

			$template->assign( 'loginInfo', $loginInfo );

			$template->assign( 'headerNavi', ($memberId == $loginInfo['uid']) ? "me" : "" );

			if( $userInfo['utype'] == "user")
			{
				$template->assign( 'rls', PHPSay::getMemberRLs($DB,$uid) );

				$template->assign( 'history', PHPSay::getMemberHistory($DB,$memberId) );

				$template->assign( 'ufollowing', PHPSay::getMemberUFollowing($DB,$uid) );

				$template->assign( 'artist', PHPSay::getArtist($DB) );

				$template->assign( 'afollowing', PHPSay::getMemberAFollowing($DB,$uid) );

				//$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

				//$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

				//$template->assign( 'memberBalance', $userInfo['balance'] );
			}
			//echo "<script>console.log('type:".$userInfo["type"]["mtype"][0]."');</script>";
			$template->assign( 'userInfo', $userInfo );

			$template->assign( 'postType', $postType );

			$memberInfo = PHPSay::getMemberInfo($DB,"id",$uid,$memberType);

			$template->assign( 'mtypes', PHPSay::getMusicTypes($DB) );

			/*switch ($postType)
			{
				case "reply":
					$template->assign( 'postList', PHPSay::getReply($DB,"uid",$userInfo["uid"],$currentPage,30) );
					break;
				case "favorite":
					$template->assign( 'postList', PHPSay::getUserFavorite($DB,$loginInfo["uid"],$currentPage,30) );
					break;
				default:
					$template->assign( 'postList', PHPSay::getTopic($DB,"user",$userInfo["uid"],$currentPage,30) );
			}*/

			$template->output();
		}

		$DB->close();
	}
}
?>