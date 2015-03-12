<?php
require(dirname(__FILE__)."/global.php");

if ( isset($_GET['do']) )
{
	if ( $_GET['do'] == "addtype" ){
		$addtypelist="";
		$data='';
		$i=0;
		foreach($_POST as $k => $v){
			//$data.='"'.$i.'":{"k":"'.$k.'","v":"'.$v.'"},';
			$addtypelist.=";".$k;
			$i++;
		}
		$DB=database();
		$row=$DB->fetch_one_array("SELECT `type_like` FROM `user` WHERE `uid`=".$loginInfo['uid']);
		if(empty($row)){
			$typelist=preg_replace("^;","",$addtypelist);
		}else{
			$typelist=$row['type_like'].$addtypelist;
		}
		$DB->query("UPDATE `user` SET `type_like`='$typelist' WHERE `uid`=".$loginInfo['uid']);
		$DB->close();
		header("user.php?list=type");
	}
	elseif ( $_GET['do'] == "addartist" ){
		$DB=database();
		foreach($_POST as $k => $v){
			//$data.='"'.$i.'":{"k":"'.$k.'","v":"'.$v.'"},';
			$DB->query("INSERT INTO `fan`(`aid`,`uid`) VALUE ($k,".$loginInfo['uid'].")");
		}
		
		$DB->close();
		header("user.php?list=afollowing");
	}
	elseif( $_GET['do'] == "searchconcerts" ){
		$condition="";
		foreach($_POST as $k => $v){
			if($v!=null){
				if($k=="startdate"){
					$condition.="`concertdate`>'$v' AND ";
				}elseif($k=="enddate"){
					$condition.="`concertdate`<'$v' AND ";
				}elseif($k=="maxprice"){
					$condition.="`price`<=$v AND ";
				}else{
					$condition.="$k like '%$v%' AND ";
				}
			}
		}
		$DB=database();
		$concerts=PHPSay::getSearchedConcert($DB,$condition);
		//$concerts=$concerts[0];
		$data='{"len":'.count($concerts);
		for($i=0;$i<count($concerts);$i++){
			$data.=",";
			$data.='"'."row$i".'"'.':{"conid":'.$concerts[$i]["conid"].',"conname":"'.$concerts[$i]["conname"].'","venuename":"'.$concerts[$i]["venuename"].'","concertdate":"'.$concerts[$i]["concertdate"].'","aname":"'.$concerts[$i]["aname"].'","mtype":"'.$concerts[$i]["type"].'"}';
		}
		$data.='}';
		echo $data;
	}elseif( $_GET['do'] == "post" ){
		$DB=database();

		$query=$DB->query("INSERT INTO `twitter` (`uid`,`message`,`twittertype`) VALUE (".$loginInfo['uid'].",'".$_POST['message']."',5)");
		$uname=$DB->fetch_one("SELECT `uname` FROM `user` WHERE `uid`=".$loginInfo['uid']);

		$time=$DB->fetch_one("SELECT NOW()");

		$allid=PHPSay::getAllId($DB,$loginInfo['uid'],'user');

		$data='{"uid":'.$allid.',"uname":"'.$uname.'","twittertime":"'.$time.'","type":"0","message":"'.$_POST['message'].'"}';

		$DB->close();

		echo $data;
	}
}elseif( isset($_GET['rsvp']) ){
	$DB=database();
	$uname=$DB->fetch_one("SELECT `uname` FROM `user` WHERE `uid`=".$loginInfo['uid']);
	$conname=$DB->fetch_one("SELECT `conname` FROM `concert` WHERE `conid`=".$_GET['rsvp']);
	$message=$uname." is going to ".$conname;
	$DB->query("INSERT INTO `twitter`(`uid`,`conid`,`twittertype`,`message`) VALUE (".$loginInfo['uid'].",".$_GET['rsvp'].",1,"."'$message'".")");
	$DB->close();
}

?>