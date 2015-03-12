<?php
require(dirname(__FILE__)."/global.php");

if ( $loginInfo['uid'] < 1 )
{
	header("location:./passport.php?return=".urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER["QUERY_STRING"]));
}
else
{
	$postType = ( isset($_GET['list']) &&  in_array($_GET['list'], array("concertlike","favorite")) ) ? $_GET['list'] : "postconcert";

	$memberId = ( isset($_GET['id']) && $postType != "systemrecommand" ) ? $_GET['id'] : $loginInfo['uid'];

	if( !is_numeric($memberId) && checkNickname($memberId) != "" )
	{
		header("location:./concert.php");
	}
	else
	{
		$DB = database();

		$userInfo = PHPSay::getMemberInfo( $DB, is_numeric($memberId) ? "uid" : "nickname", $memberId );

		if( $userInfo["uid"] < 1 )
		{
			header("location:./concert.php");
		}
		else
		{
			$template = template( "concert.html" );

			$template->assign( 'PHPSayConfig', $PHPSayConfig );

			$template->assign( 'loginInfo', $loginInfo );

			if( !$isMobileRequest )
			{
				$template->assign( 'headerNavi', ($memberId == $loginInfo['uid']) ? "me" : "" );

				if( $userInfo['uid'] == $loginInfo['uid'] )
				{
					$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

					$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );

					$template->assign( 'memberBalance', $userInfo['balance'] );
				}
			}

			$template->assign( 'userInfo', $userInfo );

			$template->assign( 'postType', $postType );

			switch ($postType)
			{
				case "concertlike":
					$template->assign( 'postList', PHPSay::getReply($DB,"uid",$userInfo["uid"],$currentPage,30) );
					break;
				case "systemrecommand":
					$template->assign( 'postList', PHPSay::getUserFavorite($DB,$loginInfo["uid"],$currentPage,30) );
					break;
				default:
					$template->assign( 'postList', PHPSay::getTopic($DB,"user",$userInfo["uid"],$currentPage,30) );
			}

			$template->output();
		}

		$DB->close();
	}
}
?>