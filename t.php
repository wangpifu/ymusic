<?php
require(dirname(__FILE__)."/global.php");

if( !isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] < 1 )
{
	header("location:./");
}
else
{
	$DB = database();

	$topicInfo = PHPSay::getOneTopic($DB,$_GET['id']);

	if( $topicInfo['tid'] < 1 )
	{
		header("location:./");
	}
	else
	{
		$clubList = PHPSay::getClubList($DB,$topicInfo['cid']);

		if( $topicInfo['cid'] > 0 && $clubList['current']['cid'] == 0 )
		{
			header("location:./");
		}
		else
		{
			$template = template( $isMobileRequest ? "mobile_t.html" : "t.html" );

			$template->assign( 'PHPSayConfig', $PHPSayConfig );

			$template->assign( 'loginInfo', $loginInfo );

			$template->assign( 'topicInfo', $topicInfo );

			if ( $loginInfo['uid'] >= 1 )
			{
				$template->assign( 'favoriteId', PHPSay::getUserFavoriteId($DB,$topicInfo['tid'],$loginInfo['uid']) );

				$template->assign( 'memberBalance', PHPSay::getMemberBalance($DB,$loginInfo['uid']) );
			}
			else
			{
				$template->assign( 'favoriteId', '' );
			}

			$template->assign( 'replyList', PHPSay::getReply($DB,"tid",$topicInfo["tid"],$currentPage,90) );

			if( !$isMobileRequest )
			{
				$template->assign( 'headerNavi', "" );

				$template->assign( 'clubList', $clubList );

				if ( $loginInfo['uid'] >= 1 )
				{
					$template->assign( 'notificationNumber', PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']) );

					$template->assign( 'favoriteNumber', PHPSay::getUserFavoriteNumber($DB,$loginInfo['uid']) );
				}

				$template->assign( 'sponsorList', PHPSay::getSponsor($DB,"show") );
			}

			$template->output();
		}
	}

	$DB->close();
}
?>