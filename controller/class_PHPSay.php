<?php
class PHPSay
{
	public static function getSponsor($DB,$type="",$uid=0)
	{
		$SQL = "SELECT * FROM `phpsay_sponsor`";

		switch ($type)
		{
			case 'show':
				$SQL .= " WHERE `endtime` > 0 AND `endtime` > ".time()." ORDER BY `position` ASC";
				break;
			default:
				$SQL .= " ORDER BY `sid` ASC";
				break;
		}

		$sponsorArray = array();

		$Result = $DB->query($SQL);

		while($Re = $DB->fetch_array($Result))
		{
			$sponsorArray[] = array(
									"sid"		=> $Re['sid'],
									"text"		=> stripslashes($Re['text']),
									"link"		=> stripslashes($Re['link']),
									"endtime"	=> ($Re['endtime'] > 0) ? remainingTime($Re['endtime']) : 0
									);
		}

		return $sponsorArray;
	}

	public static function getSponsorCount($DB)
	{
		return $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_sponsor` WHERE `endtime` = 0");
	}

	public static function addSponsor($DB,$text,$link)
	{
		$addDBArray = array(
							"text"		=> $text,
							"link"		=> $link,
							"uid"		=> 0,
							"endtime"	=> 1609387932,
							"position"	=> 0
							);

		$DB->query( $DB->insert_sql("`phpsay_sponsor`",$addDBArray) );

		return $DB->insert_id();
	}

	public static function editSponsor($DB,$text,$link,$id)
	{
		return $DB->query( $DB->update_sql("`phpsay_sponsor`", array("text" => $text,"link" => $link), "`sid`=".$id) );
	}
	//modified
	public static function getMemberInfo($DB,$key,$value,$userType)
	{
		$memberArray = array( "uid" => 0, "nickname" => "", "email" => "", "password" => "", "userType"	=> $userType );
		if($userType=='user'){
			if($key=='id'){
				$key='uid';
				$Re = $DB->fetch_one_array("SELECT * FROM `user` WHERE `".$key."`=".$value);
			}else if($key=='name'){
				$key='uname';
				$Re = $DB->fetch_one_array("SELECT * FROM `user` WHERE `".$key."`='".$value."'");
			}
			if(!empty($Re['uid'])){
				$memberArray = array(
								"uid"		=> $Re['uid'],
								//"avatar"	=> getAvatarURL($Re['uid']),
								"nickname"	=> $Re['uname'],
								"password"	=> $Re['upassword'],
								"type"		=> $DB->typeSplit($Re['type_like']),
								"hobby"		=> $Re['hobby'],
								"dob"		=> $Re['udob'],
								"email"		=> $Re['emailaddr'],
								"regtime"	=> $Re['registertime'],
								"logintime"	=> $Re['lastlogtime'],
								"score"		=> $Re['trustscore'],
								"utype"	=> $userType
								);
			}
		}else{
			if($key=='id'){
				$key='aid';
				$Re = $DB->fetch_one_array("SELECT * FROM `artist` WHERE `".$key."`=".$value);
			}else if($key=='name'){
				$key='aname';
				$Re = $DB->fetch_one_array("SELECT * FROM `artist` WHERE `".$key."`='".$value."'");
			}
			if(!empty($Re['aid'])){
				$memberArray = array(
								"uid"		=> $Re['aid'],
								//"avatar"	=> getAvatarURL($Re['uid']),
								"nickname"	=> $Re['aname'],
								"password"	=> $Re['apassword'],
								"type"		=> $DB->typeSplit($Re['type']),
								"vstatus"	=> $Re['verifystatus'],
								"hobby"		=> $Re['hobby'],
								"dob"		=> $Re['adob'],
								"email"		=> $Re['emailaddr'],
								"regtime"	=> $Re['registertime'],
								"logintime"	=> $Re['lastlogtime'],
								"utype"	=> $userType
								);
			}
		}
		//echo "<script>console.log('db:".$memberArray["type"][0]."');</script>";
		return $memberArray;
	}

	public static function getMemberBalance($DB,$uid)
	{
		return $DB->fetch_one("SELECT `balance` FROM `phpsay_member` WHERE `uid`=".$uid);
	}

	public static function getMemberRecharge($DB,$uid)
	{
		$sumCoin = $DB->fetch_one("SELECT SUM(`coin`) FROM `phpsay_orderform` WHERE `uid`=".$uid." AND `status` = 4");

		if( empty($sumCoin) )
		{
			$sumCoin = 0;
		}

		return $sumCoin;
	}

	public static function getMemberCount($DB,$key,$value)
	{
		return $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_member` WHERE `".$key."`='".$value."'");
	}

	//new
	public static function memberJoin($DB,$username,$email,$password,$group){
		$log=$DB->signUp($username,$email,$password,$group);
		return $log; 
	}

	/*public static function memberJoin($DB,$nickname,$email,$password,$qqid,$groupid=1)
	{
		$addDBArray = array("nickname"	=> $nickname,
							"email"		=> $email,
							"password"	=> $password,
							"balance"	=> 60,
							"regtime"	=> time(),
							"qqid"		=> $qqid,
							"groupid"	=> $groupid);

		$DB->query( $DB->insert_sql("`phpsay_member`",$addDBArray) );

		$uid = $DB->insert_id();

		self::addBalanceLog($DB,$uid,1,60,60);

		return $uid;
	}*/

	public static function updateMemberLogin($DB,$uid,$coin,$userType)
	{
		$Re = self::getMemberInfo($DB,"uid",$uid,$userType);

		$deadline = time() - 259200;
		if($userType=='user'){
			$DB->query( $DB->update_sql("`user`", array( "`lastlogtime`" => 'NOW()' ), "`uid`=".$uid) );
		}else{
			$DB->query( $DB->update_sql("`artist`", array( "`lastlogtime`" => 'NOW()' ), "`uid`=".$uid) );
		}
		return array( "coin" => 0, "utype" => $Re['utype'] );
	}

	public static function getUserBalance($DB,$uid,$page,$per)
	{
		$Total = $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_balance` WHERE `uid`=".$uid);

		$LogList = array();

		if ( $Total > 0 )
		{
			$Result = $DB->query("SELECT * FROM `phpsay_balance` WHERE `uid`=".$uid." ORDER BY `id` DESC LIMIT ".($page-1)*$per.",".$per);

			while($Re = $DB->fetch_array($Result))
			{
				$LogList[] = array(	"id"		=> $Re['id'],
									"uid"		=> $Re['uid'],
									"dateline"	=> date('Y-m-d H:i',$Re['dateline']),
									"typeid"	=> $Re['typeid'],
									"amount"	=> $Re['amount'],
									"balance"	=> $Re['balance'],
									"tid"		=> $Re['tid'] );
			}
		}

		return array("count"=>$Total,"list"=>$LogList,"page"=>self::getPage($Total,$per,$page));
	}

	private static function addBalanceLog($DB,$uid,$typeid,$amount,$balance,$tid=0)
	{
		$balanceArr = array("uid"		=> $uid,
							"dateline"	=> time(),
							"typeid"	=> $typeid,
							"amount"	=> $amount,
							"balance"	=> $balance,
							"tid"		=> $tid);

		$DB->query( $DB->insert_sql("`phpsay_balance`",$balanceArr) );

		return $DB->insert_id();
	}

	public static function updateMemberBalance($DB,$uid,$typeid,$amount,$balance="",$tid=0)
	{
		$updateArray = array( "balance" => array("`balance`+".$amount) );

		if( $typeid == 2 )
		{
			$updateArray['logintime'] = time();
		}
		else if( $typeid == 3 )
		{
			$updateArray['topictime'] = time();
		}
		else if( $typeid == 4 )
		{
			$updateArray['replytime'] = time();
		}

		$DB->query($DB->update_sql("`phpsay_member`", $updateArray, "`uid`=".$uid));

		if( $balance == "" )
		{
			$balance = self::getMemberBalance($DB,$uid);
		}
		else
		{
			$balance = $balance + $amount;
		}

		return self::addBalanceLog($DB,$uid,$typeid,$amount,$balance,$tid);
	}

	public static function getUserOrderForm($DB,$uid,$page,$per)
	{
		return array("count"=>0,"list"=>array(),"page"=>self::getPage(0,$per,$page));
	}
	
	public static function getResetPasswordCode($DB,$key,$value)
	{
		$codeArr = array( "uid" => 0, "code" => "", "dateline" => 0 );

		$getArr = $DB->fetch_one_array("SELECT * FROM `phpsay_resetpassword` WHERE `".$key."`='".$value."'");

		if( $getArr )
		{
			$codeArr = array( "uid" => $getArr['uid'], "code" => $getArr['code'], "dateline" => $getArr['dateline'] );
		}

		return $codeArr;
	}

	private static function checkMemberIfCanPost($DB,$uid,$balance,$post)
	{
		$userInfo = $DB->fetch_one_array("SELECT `balance`,`groupid`,`topictime`,`replytime` FROM `phpsay_member` WHERE `uid`=".$uid);

		if( empty($userInfo['groupid']) )
		{
			return "-1";
		}

		if( $userInfo['balance'] < $balance )
		{
			return "-2";
		}

		if( $userInfo['groupid'] == 1 )
		{
			if( $post == "topic" )
			{
				if( (time() - $userInfo['topictime']) <= 60 )
				{
					return "0";
				}
			}
			else if( $post == "reply" )
			{
				if( (time() - $userInfo['replytime']) <= 30 )
				{
					return "0";
				}
			}
		}

		return "";
	}

	public static function getNotification($DB,$atuid,$uid,$isread,$page,$per)
	{
		$WHERE = " WHERE `atuid` = ".$atuid;
		
		if( $uid > 0 )
		{
			$WHERE .= " AND `uid` = ".$uid." AND `tid` = 0";
		}

		if( $isread == "0" || $isread == "1" )
		{
			$WHERE .= " AND `isread` = ".$isread;
		}

		$Total = $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_notification`".$WHERE);

		$notificationList = array();

		$unReadNotification = array();

		if ( $Total > 0 )
		{
			$Result = $DB->query("SELECT * FROM `phpsay_notification`".$WHERE." ORDER BY `nid` DESC LIMIT ".($page-1)*$per.",".$per);

			while($Re = $DB->fetch_array($Result))
			{
				$notificationList[] = array("nid"		=> $Re['nid'],
											"atuid"		=> $Re['atuid'],
											"uid"		=> $Re['uid'],
											"avatar"	=> getAvatarURL($Re['uid']),
											"nickname"	=> $Re['nickname'],
											"tid"		=> $Re['tid'],
											"pid"		=> $Re['pid'],
											"message"	=> filterHTML($Re['message']),
											"smallimg"	=> getImageURL($Re["picture"],"s"),
											"bigimage"	=> getImageURL($Re["picture"],"b"),
											"posttime"	=> countDownTime($Re["posttime"]),
											"isread"	=> $Re['isread']);

				if ( $Re['isread'] == 0 )
				{
					array_push($unReadNotification, $Re['nid']);
				}
			}

			if( count($unReadNotification) >= 1 && $uid == 0 )
			{
				$DB->query("UPDATE `phpsay_notification` SET `isread` = 1 WHERE `nid` IN (".implode(",",$unReadNotification).")");
			}

			unset($unReadNotification);
		}

		$notificationsArray = array( "count" => $Total, "list" => $notificationList, "page" => array() );

		if ( $isread == "0" )
		{
			$notificationsArray["page"]["Total"] = ceil($Total/$per);
		}
		else
		{
			$notificationsArray["page"] = self::getPage($Total,$per,$page);
		}

		return $notificationsArray;
	}

	public static function getUnreadNotificationNumber($DB,$atuid)
	{
		return $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_notification` WHERE `atuid`=".$atuid." AND `isread`=0");
	}

	public static function getUnreadNotificationUser($DB,$atuid)
	{
		$notificationUser = array();

		$Result = $DB->query("SELECT `nickname` FROM `phpsay_notification` WHERE `atuid` = ".$atuid." AND `isread` = 0 LIMIT 30");

		while($Re = $DB->fetch_array($Result))
		{
			if ( !in_array($Re['nickname'], $notificationUser) )
			{
				array_push($notificationUser, $Re['nickname']);
			}
		}

		return $notificationUser;
	}

	public static function checkNotificationSelf($message,$nickname)
	{
		preg_match_all("@\@(.*?)([\s]+)@is",strtolower($message)." ",$nameArray);

		if( in_array(strtolower($nickname), $nameArray[1]) )
		{
			return false;
		}

		return true;
	}

	private static function replaceNotificationUser($DB,$message)
	{
		$notifyUid = array();

		preg_match_all("@\@(.*?)([\s]+)@is",$message." ",$nameArray);

		if( isset($nameArray[1]) )
		{
			$writeName = array();

			foreach( $nameArray[1] as $name )
			{
				if( in_array(strtolower($name), $writeName) )
				{
					continue;
				}

				array_push($writeName, strtolower($name));

				$userInfo = $DB->fetch_one_array("SELECT `uid`,`nickname` FROM `phpsay_member` WHERE `nickname`='".$name."'");

				if( empty($userInfo['nickname']) )
				{
					$message = str_ireplace("@".$name." ", "＠".$name." ", $message." ");
				}
				else
				{
					$message = str_ireplace("@".$name." ", "@".$userInfo['nickname']." ", $message." ");

					array_push($notifyUid, $userInfo['uid']);
				}
			}
		}

		return array( "message" => $message, "notify" => $notifyUid );
	}

	private static function writeNotification($DB,$PR,$UR)
	{
		$notifyUser = array();

		foreach ($UR as $uid)
		{
			array_push($notifyUser,"(".$uid.",".$PR['uid'].",'".$PR['nickname']."',".$PR['tid'].",".$PR['pid'].",'".$PR['message']."','".$PR["picture"]."',".$PR["posttime"].")");
		}

		if( count($notifyUser) > 0 )
		{
			$DB->query("INSERT INTO `phpsay_notification` (`atuid`,`uid`,`nickname`,`tid`,`pid`,`message`,`picture`,`posttime`) VALUES ".implode(",",$notifyUser));
		}

		return true;
	}

	public static function addPrivateMessage($DB,$atUid,$uid,$nickname,$message,$price,$balance)
	{
		$writeArray = array("atuid"		=> $atUid,
							"uid"		=> $uid,
							"nickname"	=> $nickname,
							"tid"		=> 0,
							"pid"		=> 0,
							"message"	=> $message,
							"picture"	=> "",
							"posttime"	=> time());

		$DB->query( $DB->insert_sql("`phpsay_notification`",$writeArray) );

		if( $price != 0 )
		{
			self::updateMemberBalance( $DB, $uid, 9, $price, $balance );
		}

		return $price;
	}

	public static function getUserFavoriteNumber($DB,$fuid)
	{
		return $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_favorite` WHERE `fuid`=".$fuid);
	}

	public static function getUserFavorite($DB,$fuid,$page,$per)
	{
		$Total = self::getUserFavoriteNumber($DB,$fuid);

		$postList = array();

		if ( $Total > 0 )
		{
			$Result = $DB->query("SELECT * FROM `phpsay_favorite` WHERE `fuid`=".$fuid." ORDER BY `fid` DESC LIMIT ".($page-1)*$per.",".$per);

			while($Re = $DB->fetch_array($Result))
			{
				$postList[] = array("fid"		=> $Re['fid'],
									"fuid"		=> $Re['fuid'],
									"uid"		=> $Re['uid'],
									"avatar"	=> getAvatarURL($Re['uid']),
									"nickname"	=> $Re['nickname'],
									"tid"		=> $Re['tid'],
									"message"	=> filterHTML($Re['message']),
									"smallimg"	=> getImageURL($Re["picture"],"s"),
									"bigimage"	=> getImageURL($Re["picture"],"b"),
									"posttime"	=> countDownTime($Re["posttime"]));
			}
		}

		return array("count"=>$Total,"list"=>$postList,"page"=>self::getPage($Total,$per,$page));
	}

	public static function getUserFavoriteId($DB,$tid,$fuid)
	{
		return $DB->fetch_one("SELECT `fid` FROM `phpsay_favorite` WHERE `tid`=".$tid." AND `fuid`=".$fuid);
	}

	public static function addUserFavorite($DB,$tid,$fuid)
	{
		$favId = self::getUserFavoriteId($DB,$tid,$fuid);

		if( !empty($favId) )
		{
			return -1;
		}

		$postArray = $DB->fetch_one_array("SELECT * FROM `phpsay_topic` WHERE `tid`=".$tid);

		if( empty($postArray['tid']) )
		{
			return 0;
		}

		$addFaveArr = array("fuid"		=>	$fuid,
							"uid"		=>	$postArray['uid'],
							"nickname"	=>	$postArray['nickname'],
							"tid"		=>	$postArray['tid'],
							"message"	=>	addslashes(stripcslashes($postArray['message'])),
							"picture"	=>	$postArray['picture'],
							"posttime"	=>	$postArray['posttime']);

		$DB->query( $DB->insert_sql("`phpsay_favorite`",$addFaveArr) );

		return $DB->insert_id();
	}

	public static function likeReply($DB,$pid,$uid)
	{
		$likeStatus = $DB->fetch_one("SELECT `status` FROM `phpsay_liker` WHERE `pid`=".$pid." AND `uid`=".$uid);

		if( $likeStatus == "1" )
		{
			return "0";
		}
		else if( $likeStatus == "0" )
		{
			if( $DB->affected_rows("UPDATE `phpsay_liker` SET `status` = 1 WHERE `pid`=".$pid." AND `uid`=".$uid) == 1 )
			{
				$DB->query($DB->update_sql("`phpsay_reply`",array( "likes" => array("`likes`+1") ),"`pid`=".$pid));
			}

			return "1";
		}

		$replyInfo = $DB->fetch_one_array("SELECT `tid`,`uid` FROM `phpsay_reply` WHERE `pid`=".$pid);

		if( empty($replyInfo['uid']) )
		{
			return "-1";
		}

		if( $uid == $replyInfo['uid'] )
		{
			return "-2";
		}

		$DB->query( $DB->insert_sql("`phpsay_liker`", array( "pid" => $pid, "uid" => $uid, "status" => 1 )) );

		$DB->query($DB->update_sql("`phpsay_reply`",array( "likes" => array("`likes`+1") ),"`pid`=".$pid));

		if( $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_liker` WHERE `pid` = ".$pid) == 6 )
		{
			self::updateMemberBalance( $DB, $replyInfo['uid'], 6, 10, "", $replyInfo['tid'] );
		}
		
		return "1";
	}

	public static function unLikeReply($DB,$pid,$uid)
	{
		$updateNum = $DB->affected_rows("UPDATE `phpsay_liker` SET `status` = 0 WHERE `pid`=".$pid." AND `uid`=".$uid);

		if( $updateNum == 1 )
		{
			$DB->query($DB->update_sql("`phpsay_reply`",array( "likes" => array("`likes`-1") ),"`pid`=".$pid));
		}

		return $updateNum;
	}

	public static function getClubOne($DB,$cid)
	{
		$clubArray = array( "cid" => 0, "cname" => "" );

		$DBArray = $DB->fetch_one_array("SELECT * FROM `phpsay_club` WHERE `cid` = ".$cid);

		if( !empty($DBArray['cid']) )
		{
			$clubArray['cid']	= $DBArray['cid'];

			$clubArray['cname']	= $DBArray['cname'];
		}

		return $clubname;
	}

	public static function getClubList($DB,$currentCid)
	{
		$clubList = array( "list" => array(), "current" => array( "cid" => 0 , "cname" => "" ) );

		$Result = $DB->query("SELECT * FROM `phpsay_club` WHERE `position` > 0 ORDER BY `position` ASC");

		while($Re = $DB->fetch_array($Result))
		{
			$clubList["list"][] = array( "cid" => $Re['cid'], "cname" => $Re['clubname'] );

			if( $Re['cid'] == $currentCid )
			{
				$clubList["current"] = array( "cid" => $Re['cid'], "cname" => $Re['clubname'] );
			}
		}

		return $clubList;
	}

	public static function checkIfCanTopic($DB,$clubId,$uid,$expense=-10)
	{
		$clubName = $DB->fetch_one("SELECT `clubname` FROM `phpsay_club` WHERE `cid` = ".$clubId." AND `position` > 0");

		if( $clubName == "" )
		{
			return array( "error" => "-3" );
		}

		$userStatus = self::checkMemberIfCanPost($DB,$uid,abs($expense),"topic");

		if( $userStatus != "" )
		{
			return array( "error" => $userStatus );
		}

		return array( "error" => "","club" => $clubName, "expense" => $expense );
	}

	public static function postTopic($DB,$array,$expense)
	{
		$replaceArray = self::replaceNotificationUser($DB,$array['message']);

		$array['message'] = $replaceArray['message'];

		$DB->query( $DB->insert_sql("`phpsay_topic`",$array) );

		$array["tid"] = $DB->insert_id();
		
		$array["pid"] = 0;

		if( count($replaceArray['notify']) > 0 )
		{
			self::writeNotification($DB,$array,$replaceArray['notify']);
		}

		self::updateMemberBalance($DB,$array['uid'],3,$expense,"",$array['tid']);
		
		$array["avatar"] = getAvatarURL($array['uid']);

		$array["message"] = filterHTML($array['message']);

		$array["smallimg"] = getImageURL($array["picture"],"s");

		$array["bigimage"] = getImageURL($array["picture"],"b");

		$array["posttime"] = "刚刚";

		$array["lasttime"] = 0;

		$array["comments"] = 0;

		return $array;
	}

	public static function getOneTopic($DB,$tid)
	{
		$topicArray = array( "tid" => 0 );

		$Re = $DB->fetch_one_array("SELECT * FROM `phpsay_topic` WHERE `tid` = ".$tid);

		if(!empty($Re['tid']))
		{
			$topicArray = array("tid"		=> $Re['tid'],
								"uid"		=> $Re['uid'],
								"avatar"	=> getAvatarURL($Re['uid']),
								"nickname"	=> $Re['nickname'],
								"cid"		=> $Re['cid'],
								"clubname"	=> $Re['clubname'],
								"message"	=> filterHTML($Re['message']),
								"smallimg"	=> getImageURL($Re["picture"],"s"),
								"bigimage"	=> getImageURL($Re["picture"],"b"),
								"posttime"	=> countDownTime($Re["posttime"]),
								"lasttime"	=> $Re["lasttime"],
								"comments"	=> $Re["comments"]);
		}

		return $topicArray;
	}

	public static function getTopicLastTime($DB,$tid)
	{
		$lastTime = $DB->fetch_one("SELECT `posttime` FROM `phpsay_reply` WHERE `tid`=".$tid." ORDER BY `pid` DESC LIMIT 1");

		if (empty($lastTime))
		{
			$lastTime = $DB->fetch_one("SELECT `posttime` FROM `phpsay_topic` WHERE `tid`=".$tid);
		}

		return $lastTime;
	}

	public static function getTopic($DB,$type,$value,$page,$per)
	{
		$whereSQL = "";

		if( $type == "club" )
		{
			if( $value["current"]["cid"] > 0 )
			{
				$whereSQL = "WHERE `cid` = ".$value["current"]["cid"]." ORDER BY `lasttime` DESC";
			}
			else
			{
				$clubIds = array();

				foreach($value['list'] as $clubInfo)
				{
					array_push($clubIds, $clubInfo['cid']);
				}

				if( count($clubIds) > 0 )
				{
					$whereSQL = "WHERE `cid` IN (".implode(",", $clubIds).") ORDER BY `lasttime` DESC";
				}
			}
		}
		else if( $type == "user" )
		{
			$whereSQL = "WHERE `uid` = ".$value." ORDER BY `tid` DESC";
		}

		$Total = $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_topic` ".$whereSQL);

		$postList = array();

		if ( $Total > 0 )
		{
			$Result = $DB->query("SELECT * FROM `phpsay_topic` ".$whereSQL." LIMIT ".($page-1)*$per.",".$per);

			while($Re = $DB->fetch_array($Result))
			{
				$postList[] = array("tid"		=> $Re['tid'],
									"uid"		=> $Re['uid'],
									"avatar"	=> getAvatarURL($Re['uid']),
									"nickname"	=> $Re['nickname'],
									"cid"		=> $Re['cid'],
									"clubname"	=> $Re['clubname'],
									"message"	=> filterHTML($Re['message']),
									"smallimg"	=> getImageURL($Re["picture"],"s"),
									"bigimage"	=> getImageURL($Re["picture"],"b"),
									"posttime"	=> countDownTime($Re["posttime"]),
									"comments"	=> $Re["comments"]);
			}
		}

		return array("count"=>$Total,"list"=>$postList,"page"=>self::getPage($Total,$per,$page));
	}

	public static function checkIfCanReply($DB,$topicId,$replyUid)
	{
		$topicInfo = $DB->fetch_one_array("SELECT `tid`,`uid`,`cid`,`posttime`,`lasttime` FROM `phpsay_topic` WHERE `tid` = ".$topicId);

		if( empty($topicInfo['tid']) )
		{
			return array( "error" => "-4" );
		}

		if( $topicInfo['lasttime'] == "0" )
		{
			return array( "error" => "-3" );
		}

		$clubPosition = $DB->fetch_one("SELECT `position` FROM `phpsay_club` WHERE `cid` = ".$topicInfo['cid']);

		if( empty($clubPosition) )
		{
			return array( "error" => "-5" );
		}

		$expense = -5;

		if( $topicInfo['uid'] == $replyUid )
		{
			if( (time() - $topicInfo['posttime']) < 1296000 )
			{
				$expense = 0;
			}
			else
			{
				$expense = -2;
			}
		}

		if( $expense < 0 )
		{
			$userStatus = self::checkMemberIfCanPost($DB,$replyUid,abs($expense),"reply");

			if( $userStatus != "" )
			{
				return array( "error" => $userStatus );
			}
		}

		return array( "error" => "","topic" => $topicInfo, "expense" => $expense );
	}

	public static function replyTopic($DB,$array,$topic,$expense)
	{
		$replaceArray = self::replaceNotificationUser($DB,$array['message']);

		$array['message'] = $replaceArray['message'];

		$DB->query( $DB->insert_sql("`phpsay_reply`",$array) );
		
		$array['pid'] = $DB->insert_id();

		if( $array['uid'] != $topic['uid'] && !in_array($topic['uid'],$replaceArray['notify']) )
		{
			array_push($replaceArray['notify'], $topic['uid']);
		}

		if( count($replaceArray['notify']) > 0 )
		{
			self::writeNotification($DB,$array,$replaceArray['notify']);
		}
		
		$replyArray = array("pid"		=> $array['pid'],
							"tid"		=> $array['tid'],
							"uid"		=> $array['uid'],
							"avatar"	=> getAvatarURL($array['uid']),
							"nickname"	=> $array['nickname'],
							"message"	=> filterHTML($array['message']),
							"smallimg"	=> getImageURL($array["picture"],"s"),
							"bigimage"	=> getImageURL($array["picture"],"b"),
							"posttime"	=> "刚刚",
							"likes"		=> 0,
							"earnings"	=> 1);

		$updateArray = array( "comments" => array("`comments`+1") );

		if( ($array['posttime'] - $topic['posttime']) < 1296000 )
		{
			$updateArray['lasttime'] = $array['posttime'];

			$replyArray['earnings'] = 3;
		}

		$DB->query($DB->update_sql("`phpsay_topic`",$updateArray,"`tid`=".$array['tid']));

		if( $array['uid'] != $topic['uid'] )
		{
			self::updateMemberBalance( $DB, $topic['uid'], 5, $replyArray['earnings'], "", $array['tid'] );
		}

		if( $expense != 0 )
		{
			self::updateMemberBalance( $DB, $array['uid'], 4, $expense, "", $array['tid'] );
		}

		return $replyArray;
	}

	public static function deleteReply($DB,$pid)
	{
		$postInfo = $DB->fetch_one_array("SELECT `pid`,`tid`,`picture` FROM `phpsay_reply` WHERE `pid` = ".$pid);

		if( !empty($postInfo['pid']) )
		{
			$deleteNumber = $DB->affected_rows("DELETE FROM `phpsay_reply` WHERE `pid`=".$postInfo['pid']);

			if ($deleteNumber == 1)
			{
				$DB->query("DELETE FROM `phpsay_notification` WHERE `pid`=".$postInfo['pid']);

				$DB->query("DELETE FROM `phpsay_liker` WHERE `pid`=".$postInfo['pid']);

				$lastPostTime = $DB->fetch_one("SELECT `lasttime` FROM `phpsay_topic` WHERE `tid` = ".$postInfo["tid"]);

				if( $lastPostTime != "" )
				{
					$updateArray = array( "comments" => array("`comments`-1") );

					if( $lastPostTime > 0 )
					{
						$updateArray['lasttime'] = self::getTopicLastTime($DB,$postInfo['tid']);
					}

					$DB->query($DB->update_sql("`phpsay_topic`",$updateArray,"`tid`=".$postInfo['tid']));
				}
			}
		}

		return array( "picture" => $postInfo['picture'] );
	}

	public static function getReply($DB,$key,$id,$page,$per)
	{
		$Total = $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_reply` WHERE `".$key."`=".$id);

		$postList = array();

		if ( $Total > 0 )
		{
			$dORa = ($key == "uid") ? "DESC" : "ASC";

			$Result = $DB->query("SELECT * FROM `phpsay_reply` WHERE `".$key."` = ".$id." ORDER BY `pid` ".$dORa." LIMIT ".($page-1)*$per.",".$per);

			while($Re = $DB->fetch_array($Result))
			{
				$postList[] = array("pid"		=> $Re['pid'],
									"tid"		=> $Re['tid'],
									"uid"		=> $Re['uid'],
									"avatar"	=> getAvatarURL($Re['uid']),
									"nickname"	=> $Re['nickname'],
									"message"	=> filterHTML($Re['message']),
									"smallimg"	=> getImageURL($Re['picture'],"s"),
									"bigimage"	=> getImageURL($Re['picture'],"b"),
									"posttime"	=> countDownTime($Re['posttime']),
									"likes"		=> $Re['likes']);
			}
		}

		return array("count"=>$Total,"list"=>$postList,"page"=>self::getPage($Total,$per,$page));
	}

	public static function searchPost($DB,$type,$word,$page,$per)
	{
		$whereSQL = "";

		if( $word != "" )
		{
			$whereSQL = " WHERE `message` LIKE '%".$word."%'";
		}

		$Total = $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_".$type."`".$whereSQL);

		$postList = array();

		if ( $Total > 0 )
		{
			$columnName = ( $type == "reply" ) ? "pid" : "tid";
			
			$Result = $DB->query("SELECT * FROM `phpsay_".$type."`".$whereSQL." ORDER BY `".$columnName."` DESC LIMIT ".($page-1)*$per.",".$per);

			while($Re = $DB->fetch_array($Result))
			{
				$postList[] = array("pid"		=> ( $type == "reply" ) ? $Re['pid'] : 0,
									"cid"		=> ( $type == "topic" ) ? $Re['cid'] : "",
									"clubname"	=> ( $type == "topic" ) ? $Re['clubname'] : "",
									"comments"	=> ( $type == "topic" ) ? $Re['comments'] : "",
									"tid"		=> $Re['tid'],
									"uid"		=> $Re['uid'],
									"avatar"	=> getAvatarURL($Re['uid']),
									"nickname"	=> $Re['nickname'],
									"message"	=> filterHTML($Re['message']),
									"smallimg"	=> getImageURL($Re["picture"],"s"),
									"bigimage"	=> getImageURL($Re["picture"],"b"),
									"posttime"	=> countDownTime($Re["posttime"]));
			}
		}

		return array("count"=>$Total,"list"=>$postList,"page"=>self::getPage($Total,$per,$page));
	}
	//new
	public static function getMusicTypes($DB){
		$mtypes=$DB->fetch_all("SELECT `mtype` FROM `mtype`");
		return $mtypes;
	}
	//new
	public static function getMemberType($DB,$uid){
		$a=$DB->fetch_one_array("SELECT `uid`,`aid` FROM `uall` WHERE `id`='$uid'");
		//echo "<script>console.log('id:".$a["uid"].",".$a["aid"]."');</script>";
		if(empty($a["uid"])){
			return "artist";
		}
		return "user";
	}

	public static function getAllId($DB,$uid,$userType){
		if($userType=='user'){
			$result=$DB->fetch_one("SELECT `id` FROM `uall` WHERE `uid`='$uid'");
			return $result;
		}else{
			$result=$DB->fetch_one("SELECT `id` FROM `uall` WHERE `aid`='$uid'");
			return $result;
		}
	}

	public static function getPartId($DB,$uid,$userType){
		if($userType=='user'){
			$result=$DB->fetch_one("SELECT `uid` FROM `uall` WHERE `id`='$uid'");
			return $result;
		}else{
			$result=$DB->fetch_one("SELECT `aid` FROM `uall` WHERE `id`='$uid'");
			return $result;
		}
	}

	public static function getMemberRLs($DB,$uid){
		$result=$DB->fetch_all("SELECT * FROM recommendlist WHERE `uid`='$uid'");
		for($i=0;$i<count($result);$i++){
			$row=$result[$i];
			foreach($row as $k => $v){
				if($k=="conidlist"){
					$v=$DB->conIdSplit($v);
					/*$conname[]=array();
					for($i=0;$i<count($v);$i++){
						$t=$v[i];
						$conname['$t']=>$DB->fetch_one("SELECT `conname` FROM `concert` WHERE `conid`='$t'");
					}
					$result["connamelist"]=>$conname;*/
				}
			}
		}
		return $result;
	}

	public static function getMemberHistory($DB,$uid){
		$result=$DB->fetch_all("SELECT `concert`.`conid`,`concert`.`conname`,`concert`.`concertdate` FROM `twitter`,`concert` WHERE 
			`twittertype`=1 and `uid`='$uid' and `concert`.`conid`=`twitter`.`conid` and `concert`.`concertdate`<NOW()");
		return $result;
	}

	public static function getMemberUFollowing($DB,$uid){
		$result=$DB->fetch_all("SELECT `follow`.`leaduid`,`user`.`uname`,`follow`.`followtime` FROM `follow`,`user` WHERE 
			`followuid`='$uid' and `follow`.`leaduid`=`user`.`uid`");
		return $result;
	}

	public static function getMemberAFollowing($DB,$uid){
		$result=$DB->fetch_all("SELECT `fan`.`aid`,`fan`.`ftime`,`artist`.`aname` FROM `fan`,`artist` WHERE 
			`uid`='$uid' and `fan`.`aid`=`artist`.`aid`");
		return $result;
	}

	public static function getAllConcert($DB){
		$result=$DB->fetch_all("SELECT `concert`.`conid`,`concert`.`conname`,`concert`.`concertdate`,`concert`.`sellingweb`,
			`concert`.`price`,`concert`.`concertstatus`,`concert`.`posttime`,`artist`.`aname`,`artist`.`type`,`venue`.`venuename` 
			FROM `concert`,`artist`,`venue` 
			WHERE `concertdate`>NOW() and `venue`.`venueid`=`concert`.`venueid` and `artist`.`aid`=`concert`.`aid` 
			ORDER BY `concertdate` DESC");
		//echo "<script>console.log('num rows:".count($result)."');</script>";
		return $result;
	}

	public static function getLikedConcert($concerts,$types){
		$concertliked=array();
		//echo "<script>console.log('types:".implode(";",$types)."');</script>";
		if(empty($types[0]))return null;
		for($i=0;$i<count($concerts);$i++){
			if(in_array($concerts[$i]["type"],$types[0])){
				$concertliked[]=$concerts[$i];
			}
			
		}
		return $concertliked;
	}

	public static function getSearchedConcert($DB,$condition){
		$condition=preg_replace("#_#",".",$condition);
		$condition.="`venue`.`venueid`=`concert`.`venueid` and `artist`.`aid`=`concert`.`aid`";
		$query="SELECT `concert`.`conid`,`concert`.`conname`,`concert`.`concertdate`,`concert`.`sellingweb`,
			`concert`.`price`,`concert`.`concertstatus`,`concert`.`posttime`,`artist`.`aname`,`artist`.`type`,`venue`.`venuename` 
			FROM `concert`,`artist`,`venue` 
			WHERE ".$condition.
			"ORDER BY `concertdate`";
		$result=$DB->fetch_all($query);
		//echo "query:$query";
		return $result;
	}

	public static function getArtist($DB){
		$result=$DB->fetch_all("SELECT `aid`,`aname` FROM `artist`");
		return $result;
	}

	public static function getAllFeeds($DB){
		$query="SELECT `twitter`.`conid`,`twitter`.`uid`,`twitter`.`twittertime`,`twitter`.`message`,`twitter`.`twittertype` FROM `twitter` ORDER BY `twitter`.`twittertime` DESC";
		$result1=$DB->fetch_all($query);
		//echo "<script>console.log('num:".count($result1)."');</script>";
		for($i=0;$i<count($result1);$i++){
			$allid=$result1[$i]["uid"];
			$type=PHPSay::getMemberType($DB,$allid);
			$uid=PHPSay::getPartId($DB,$allid,$type);
			$result2=PHPSay::getMemberInfo($DB,"id",$uid,$type);
			$result1[$i]["uname"]=$result2["nickname"];
			$result1[$i]["utype"]=$type;
			if($result1[$i]["twittertype"]==1){
				$result1[$i]["type"]="plan";
			}elseif($result1[$i]["twittertype"]==2){
				$result1[$i]["type"]="review&rating";
			}elseif($result1[$i]["twittertype"]==3){
				$result1[$i]["type"]="report missed";
			}elseif($result1[$i]["twittertype"]==4){
				$result1[$i]["type"]="live info";
			}elseif($result1[$i]["twittertype"]==5){
				$result1[$i]["type"]="0";
			}
		}
		return $result1;
	}

	public static function getFeeds($allfeeds,$ufollowing,$afollowing){
		$feeds=array();
		$uids=array();
		for($i=0;$i<count($ufollowing);$i++){
			//echo "<script>console.log('k:".$ufollowing[$i]["leaduid"]."');</script>";
			$uids[]=$ufollowing[$i]["leaduid"];
		}
		$aids=array();
		for($i=0;$i<count($afollowing);$i++){
			//echo "<script>console.log('k:".$afollowing[$i]["aid"]."');</script>";
			$uids[]=$afollowing[$i]["aid"];
		}
		for($i=0;$i<count($allfeeds);$i++){
			if(in_array($allfeeds[$i]["uid"],$uids)||in_array($allfeeds[$i]["uid"],$aids)){
				$feeds[]=$allfeeds[$i];
			}
		}
		//echo "<script>console.log('u:".implode(",",$uids)."');</script>";
		//echo "<script>console.log('a:".implode(",",$aids)."');</script>";
		return $feeds;
	}

	protected static function getPage($total,$per,$page)
	{
		$pageCount = ceil($total/$per);

		$string = str_replace("index.php","",$_SERVER['PHP_SELF'])."?";

		foreach( $_GET as $k => $v )
		{
			if( $k != "page" )
			{
				$string .= "".$k."=".urlencode($v)."&";
			}
		}

		$pagePrev = "";

		$pageNext = "";

		if ( $page > 1 )
		{
			$pagePrev = $page - 1;
		}

		if ( $page < $pageCount )
		{
			$pageNext = $page + 1;
		}
		
		return array( "Prefix" => $string."page=", "Prev" => $pagePrev, "Next" => $pageNext, "Total"=> $pageCount, "Current" => $page );
	}
}
?>