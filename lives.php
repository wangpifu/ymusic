<?php
require(dirname(__FILE__)."/global.php");

if(isset($_GET['id'])){
	$DB = database();

	$template = template( "lives.html" );

	$template->assign( 'loginInfo', $loginInfo );

	$template->assign( 'headerNavi', "home" );

	$template->assign( 'coninfo', $DB->fetch_array("SELECT `concert`.`conid`,`concert`.`conname`,`concert`.`concertdate`,`concert`.`sellingweb`,`concert`.`price`,`concert`.`concertstatus`,`concert`.`posttime`,`artist`.`aname`,`artist`.`type`,`venue`.`venuename`,`venue`.`lat`,`venue`.`lng` FROM `concert`,`artist`,`venue` WHERE `venue`.`venueid`=`concert`.`venueid` and `artist`.`aid`=`concert`.`aid` AND `concert`.`conid`=".$_GET['id']));

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

	$template->assign( 'postType', null );

	$DB->close();

	$template->output();
}