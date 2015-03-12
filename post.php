<?php
require(dirname(__FILE__)."/global.php");

require(dirname(__FILE__)."/controller/class_Picture.php");

if( isset($_POST['COOKIE']) )
{
	$loginInfo = isLogin($PHPSayConfig['ppsecure'],json_decode(urldecode($_POST['COOKIE']),true));
}

if ( $loginInfo['uid'] >= 1 )
{
	if ( isset($_POST['do']) )
	{
		if($_POST['do'] == "addTopic")
		{
			if( isset($_POST['cid'],$_POST['msg']) && is_numeric($_POST['cid']) )
			{
				$postArray = array( "uid"		=> $loginInfo['uid'],
									"nickname"	=> $loginInfo['nickname'],
									"cid"		=> intval($_POST['cid']),
									"clubname"	=> "",
									"message"	=> filterCode($_POST['msg'],true),
									"picture"	=> "",
									"posttime"	=> time(),
									"lasttime"	=> time() );

				if( !PHPSay::checkNotificationSelf($postArray["message"],$postArray["nickname"]) )
				{
					die('{"result":"error","message":"您不能 @自己"}');
				}

				$uploadFile = ( isset($_FILES['picture']) && !empty($_FILES['picture']['name']) ) ? $_FILES['picture'] : "";

				if( $uploadFile != "" )
				{
					$saveResult = Picture::Upload($uploadFile,"",$loginInfo['uid']);

					if( $saveResult['error'] != "" )
					{
						die('{"result":"error","message":"'.$saveResult['error'].'"}');
					}
					else
					{
						$postArray["picture"] = $saveResult['name'];
					}
				}

				if( (empty($postArray["message"]) && empty($postArray["picture"])) || getStrlen($postArray["message"]) > 200 )
				{
					Picture::Delete($postArray["picture"]);
					
					die('{"result":"error","message":"内容长度不合法"}');
				}

				$DB = database();

				$checkArray = PHPSay::checkIfCanTopic($DB,$postArray["cid"],$postArray["uid"]);

				if( $checkArray["error"] != "" )
				{
					switch ($checkArray["error"]) {
						case '-3':
							echo '{"result":"error","message":"该讨论组已关闭"}';
							break;
						case '-2':
							echo '{"result":"error","message":"您的社区币余额不足"}';
							break;
						case '-1':
							echo '{"result":"error","message":"您没有权限发言"}';
							break;
						case '0':
							echo '{"result":"error","message":"您发言太快了"}';
							break;
					}

					Picture::Delete($postArray["picture"]);
				}
				else
				{
					$postArray["clubname"] = $checkArray['club'];

					$postResult = PHPSay::postTopic($DB,$postArray,$checkArray['expense']);

					if( isset($postResult['tid']) && $postResult['tid'] > 0 )
					{
						if( $isMobileRequest )
						{
							echo '{"result":"success","message":""}';
						}
						else
						{
							$template = template("_stream_item.html");

							$template->assign( 'loginInfo', $loginInfo );

							if( isset($_POST['from']) && $_POST['from'] == 0 )
							{
								$postArray['cid'] = 0;
							}

							$template->assign( 'clubList', array("current" => array("cid" => $postArray['cid'])) );

							$template->assign( 'topicList', array("list" => array($postResult)) );

							echo json_encode( array("result" => "success", "message" => $template->result() ,"balance" => $checkArray['expense']) );
						}
					}
					else
					{
						Picture::Delete($postArray["picture"]);

						echo '{"result":"error","message":"发布失败"}';
					}
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "deleteTopic")
		{
			if ( $loginInfo['group'] > 1 && isset($_POST['tid']) && is_numeric($_POST['tid']) )
			{
				$deletePics = array();

				$DB = database();

				$postInfo = $DB->fetch_one_array("SELECT `tid`,`picture` FROM `phpsay_topic` WHERE `tid` = ".$_POST['tid']);

				if( !empty($postInfo['picture']) )
				{
					array_push($deletePics, $postInfo["picture"]);
				}

				if( !empty($postInfo['tid']) )
				{
					$DB->query("DELETE FROM `phpsay_topic` WHERE `tid` = ".$postInfo['tid']);

					$Result = $DB->query("SELECT `picture` FROM `phpsay_reply` WHERE `tid` = ".$postInfo['tid']);

					while($Re = $DB->fetch_array($Result))
					{
						if( !empty($Re['picture']) )
						{
							array_push($deletePics, $Re["picture"]);
						}
					}

					$DB->query("DELETE FROM `phpsay_reply` WHERE `tid` = ".$postInfo['tid']);

					$DB->query("DELETE FROM `phpsay_favorite` WHERE `tid` = ".$postInfo['tid']);

					$DB->query("DELETE FROM `phpsay_notification` WHERE `tid` = ".$postInfo['tid']);

					$DB->query("UPDATE `phpsay_balance` SET `tid` = 0 WHERE `tid` = ".$postInfo['tid']);
				}

				$DB->close();

				foreach ($deletePics as $deletePic)
				{
					Picture::Delete($deletePic);
				}

				echo '{"result":"success"}';
			}
		}
		else if($_POST['do'] == "deleteTopicPicture")
		{
			if ( $loginInfo['group'] > 1 && isset($_POST['tid']) && is_numeric($_POST['tid']) )
			{
				$DB = database();

				$topicInfo = $DB->fetch_one_array("SELECT `message`,`picture` FROM `phpsay_topic` WHERE `tid` = ".$_POST['tid']);

				if( $topicInfo['message'] != "" && $topicInfo['picture'] != "" )
				{
					$DB->query("UPDATE `phpsay_topic` SET `picture`='' WHERE `tid` = ".$_POST['tid']);
					
					$DB->query("UPDATE `phpsay_favorite` SET `picture`='' WHERE `tid` = ".$_POST['tid']);
					
					$DB->query("UPDATE `phpsay_notification` SET `picture`='' WHERE `tid` = ".$_POST['tid']);

					Picture::Delete($topicInfo["picture"]);

					echo '{"result":"success"}';
				}
				else
				{
					echo '{"result":"error"}';
				}

				$DB->close();
			}
		}
		if($_POST['do'] == "replyTopic")
		{
			if( isset($_POST['tid'],$_POST['msg']) && is_numeric($_POST['tid']) )
			{
				$postArray = array( "tid"		=> $_POST['tid'],
									"uid"		=> $loginInfo['uid'],
									"nickname"	=> $loginInfo['nickname'],
									"message"	=> filterCode($_POST['msg'],true),
									"picture"	=> "",
									"posttime"	=> time() );

				if( !PHPSay::checkNotificationSelf($postArray["message"],$postArray["nickname"]) )
				{
					die('{"result":"error","message":"您不能 @自己"}');
				}

				$uploadFile = ( isset($_FILES['picture']) && !empty($_FILES['picture']['name']) ) ? $_FILES['picture'] : "";

				if( $uploadFile != "" )
				{
					$saveResult = Picture::Upload($uploadFile,"",$loginInfo['uid']);
					
					if( $saveResult['error'] != "" )
					{
						die('{"result":"error","message":"'.$saveResult['error'].'"}');
					}
					else
					{
						$postArray["picture"] = $saveResult['name'];
					}
				}

				if( (empty($postArray["message"]) && empty($postArray["picture"])) || getStrlen($postArray["message"]) > 200 )
				{
					Picture::Delete($postArray["picture"]);

					die('{"result":"error","message":"内容长度不合法"}');
				}

				$DB = database();

				$checkArray = PHPSay::checkIfCanReply($DB,$postArray["tid"],$postArray["uid"]);

				if( $checkArray['error'] != "" )
				{
					switch ($checkArray['error']) {
						case '-5':
							echo '{"result":"error","message":"该主题所在讨论组已关闭"}';
							break;
						case '-4':
							echo '{"result":"error","message":"该主题已被删除"}';
							break;
						case '-3':
							echo '{"result":"error","message":"该主题已被关闭"}';
							break;
						case '-2':
							echo '{"result":"error","message":"您的社区币余额不足"}';
							break;
						case '-1':
							echo '{"result":"error","message":"您没有权限进行回复"}';
							break;
						case '0':
							echo '{"result":"error","message":"您回复的太快了"}';
							break;
					}

					Picture::Delete($postArray["picture"]);
				}
				else
				{
					$postResult = PHPSay::replyTopic( $DB, $postArray, $checkArray['topic'], $checkArray['expense'] );

					if( isset($postResult['pid']) && $postResult['pid'] > 0 )
					{
						if( $isMobileRequest )
						{
							echo '{"result":"success","message":'.$postResult['pid'].'}';
						}
						else
						{
							$template = template("_reply_item.html");

							$template->assign( 'loginInfo', $loginInfo );

							$template->assign( 'replyList', array("list" => array($postResult)) );

							echo json_encode( array("result" => "success", "message" => $template->result(), "balance" => $checkArray['expense']) );
						}
					}
					else
					{
						Picture::Delete($postArray["picture"]);

						echo '{"result":"error","message":"回复失败"}';
					}
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "deleteReply")
		{
			if ( $loginInfo['group'] > 1 && isset($_POST['pid']) && is_numeric($_POST['pid']) )
			{
				$DB = database();

				$deleteResult = PHPSay::deleteReply($DB,$_POST['pid']);

				$DB->close();

				Picture::Delete($deleteResult["picture"]);

				echo '{"result":"success"}';
			}
		}
		else if($_POST['do'] == "addPrivateMessage")
		{
			if( isset($_POST['nickname'],$_POST['msg']) && checkNickname($_POST['nickname']) == "" )
			{
				$messageBody = filterCode($_POST['msg'],true);

				if( empty($messageBody) || getStrlen($messageBody) > 200 )
				{
					die('{"result":"error","message":"内容长度不合法"}');
				}

				$DB = database();

				$atUid = $DB->fetch_one("SELECT `uid` FROM `phpsay_member` WHERE `nickname` = '".$_POST["nickname"]."'");

				if( empty($atUid) || $atUid == $loginInfo['uid'] )
				{
					echo '{"result":"error","message":"发送受限"}';
				}
				else
				{
					$myBalance = PHPSay::getMemberBalance($DB,$loginInfo['uid']);

					if( $myBalance < 15 )
					{
						echo '{"result":"error","message":"您的社区币余额不足"}';
					}
					else
					{
						echo '{"result":"success","balance":'.PHPSay::addPrivateMessage($DB,$atUid,$loginInfo['uid'],$loginInfo['nickname'],$messageBody,-15,$myBalance).'}';
					}
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "userGroup")
		{
			if ( $loginInfo['group'] > 1 && isset($_POST['uid']) && is_numeric($_POST['uid']) )
			{
				if( $loginInfo['uid'] == $_POST['uid'] )
				{
					die('{"result":"error"}');
				}

				$DB = database();

				$userGroup = $DB->fetch_one("SELECT `groupid` FROM `phpsay_member` WHERE `uid` = ".$_POST["uid"]);

				if( $userGroup == 0 )
				{
					$DB->query("UPDATE `phpsay_member` SET `groupid`=1 WHERE `uid` = ".$_POST['uid']);

					echo '{"result":"success","message":1}';
				}
				else if( $userGroup == 1 )
				{
					$DB->query("UPDATE `phpsay_member` SET `groupid`=0 WHERE `uid` = ".$_POST['uid']);

					echo '{"result":"success","message":0}';
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "topicStatus")
		{
			if ( $loginInfo['group'] > 1 && isset($_POST['tid']) && is_numeric($_POST['tid']) )
			{
				$DB = database();

				$lastPostTime = $DB->fetch_one("SELECT `lasttime` FROM `phpsay_topic` WHERE `tid` = ".$_POST["tid"]);

				if( $lastPostTime != "" )
				{
					if( $lastPostTime == 0 )
					{
						$lastTime = PHPSay::getTopicLastTime($DB,$_POST['tid']);

						if( !empty($lastTime) )
						{
							$DB->query("UPDATE `phpsay_topic` SET `lasttime`=".$lastTime." WHERE `tid` = ".$_POST['tid']);

							echo '{"result":"success","message":1}';
						}
						else
						{
							echo '{"result":"error"}';
						}
					}
					else
					{
						$DB->query("UPDATE `phpsay_topic` SET `lasttime` = 0 WHERE `tid` = ".$_POST['tid']);

						echo '{"result":"success","message":0}';
					}
				}
				else
				{
					echo '{"result":"error"}';
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "moveTopic")
		{
			if( $loginInfo['group'] > 1 && isset($_POST['tid'],$_POST['cid']) && is_numeric($_POST['tid']) && is_numeric($_POST['cid']) )
			{
				$DB = database();

				$clubName = $DB->fetch_one("SELECT `clubname` FROM `phpsay_club` WHERE `cid` = ".$_POST["cid"]." AND `position` > 0");

				if ( $clubName != "" )
				{
					$DB->query($DB->update_sql("phpsay_topic",array("cid"=>$_POST["cid"],"clubname"=>$clubName),"`tid`=".$_POST['tid']));

					echo '{"result":"success"}';
				}
				else
				{
					echo '{"result":"error"}';
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "deleteNotification")
		{
			if ( isset($_POST['nid']) && is_numeric($_POST['nid']) )
			{
				$DB = database();

				$deleteNumber = $DB->affected_rows("DELETE FROM `phpsay_notification` WHERE `nid`=".$_POST['nid']." AND `atuid`=".$loginInfo['uid']);

				$DB->close();

				if( $deleteNumber == 1 )
				{
					echo '{"result":"success"}';
				}
				else
				{
					echo '{"result":"error"}';
				}	
			}
		}
		else if($_POST['do'] == "favTopic")
		{
			if ( isset($_POST['tid']) && is_numeric($_POST['tid']) )
			{
				$DB = database();

				$favId = PHPSay::addUserFavorite($DB,$_POST['tid'],$loginInfo['uid']);

				$DB->close();

				if( $favId != 0 )
				{
					echo '{"result":"success","message":'.$favId.'}';
				}
				else
				{
					echo '{"result":"error"}';
				}
			}
		}
		else if($_POST['do'] == "unFavTopic")
		{
			if ( isset($_POST['tid']) && is_numeric($_POST['tid']) )
			{
				$DB = database();

				if( $DB->affected_rows("DELETE FROM `phpsay_favorite` WHERE `tid`=".$_POST['tid']." AND `fuid`=".$loginInfo['uid']) == 1 )
				{
					echo '{"result":"success"}';
				}
				else
				{
					echo '{"result":"error"}';
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "likeReply")
		{
			if ( isset($_POST['pid']) && is_numeric($_POST['pid']) )
			{
				$DB = database();

				$likeResult = PHPSay::likeReply($DB,$_POST['pid'],$loginInfo['uid']);

				$DB->close();

				if( $likeResult >= "0" )
				{
					echo '{"result":"success","message":'.$likeResult.'}';
				}
				else
				{
					echo '{"result":"error","message":'.$likeResult.'}';
				}
			}
		}
		else if($_POST['do'] == "unLikeReply")
		{
			if ( isset($_POST['pid']) && is_numeric($_POST['pid']) )
			{
				$DB = database();

				if( PHPSay::unLikeReply($DB,$_POST['pid'],$loginInfo['uid']) == 1 )
				{
					echo '{"result":"success"}';
				}
				else
				{
					echo '{"result":"error"}';
				}

				$DB->close();
			}
		}
		else if($_POST['do'] == "webkitNotify")
		{
			$DB = database();

			$unReadCount = PHPSay::getUnreadNotificationNumber($DB,$loginInfo['uid']);

			$userArray = array();

			if( $unReadCount > 0 )
			{
				$userArray = PHPSay::getUnreadNotificationUser($DB,$loginInfo['uid']);
			}

			$DB->close();

			echo '{"count":"'.$unReadCount.'","result":"'.implode(", ", $userArray).'"}';
		}
		else if($_POST['do'] == "buySponsor")
		{
			if( isset($_POST['linkText'],$_POST['linkURL'],$_POST['linkId']) )
			{
				$linkText = filterCode(str_replace("\n","",$_POST['linkText']));

				$linkURL = filterCode(str_replace(array("http://".$_SERVER['SERVER_NAME'],"https://".$_SERVER['SERVER_NAME']), "", $_POST['linkURL']));

				if( strlen($linkText) > 1 && getStrlen($linkText) < 32 && strlen($linkURL) >= 1 && strlen($linkURL) <= 255 )
				{
					$DB = database();

					if( $loginInfo['group'] >= 3 )
					{
						if( is_numeric($_POST['linkId']) && $_POST['linkId'] >= 1 )
						{
							PHPSay::editSponsor($DB,$linkText,$linkURL,$_POST['linkId']);
						}
						else
						{
							PHPSay::addSponsor($DB,$linkText,$linkURL);
						}

						echo '{"result":"success"}';
					}

					$DB->close();
				}
				else
				{
					echo '{"result":"error","message":"输入不合法"}';
				}
			}
		}
		else if($_POST['do'] == "settingAvatar")
		{
			if( isset($_FILES['avatar']) && !empty($_FILES['avatar']['name']) )
			{
				if( $_FILES['avatar']['size'] > 1048576 )
				{
					if ( $isMobileRequest )
					{
						setcookie("upload_avatar_result","图片不能超过1MB");

						header("location:./settings.php");

						exit;
					}

					die('{"result":"error","message":"图片不能超过1MB"}');
				}

				$imgInfo = @getimagesize($_FILES['avatar']['tmp_name']);

				if( !$imgInfo || !in_array($imgInfo[2], array(1,2,3)) )
				{
					if ( $isMobileRequest )
					{
						setcookie("upload_avatar_result","您上传的图片文件不合法");

						header("location:./settings.php");

						exit;
					}

					die('{"result":"error","message":"您上传的图片文件不合法"}');
				}

				if( $imgInfo[0] < 100 || $imgInfo[1] < 100 )
				{
					if ( $isMobileRequest )
					{
						setcookie("upload_avatar_result","图片尺寸不能小于 100 x 100");

						header("location:./settings.php");

						exit;
					}

					die('{"result":"error","message":"图片尺寸不能小于 100 x 100"}');
				}

				mkDirs(avatarPath($loginInfo['uid']));

				$avatarFile = getAvatarURL($loginInfo['uid'],100);

				if( @move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarFile) )
				{
					if( $imgInfo[0] != 100 || $imgInfo[1] != 100 )
					{
						createAvatar($avatarFile,$imgInfo,100,$avatarFile);
					}
					
					if ( $isMobileRequest )
					{
						setcookie("upload_avatar_result","SUCCESS");

						header("location:./settings.php");

						exit;
					}

					die('{"result":"success","message":""}');
				}
				else
				{
					if ( $isMobileRequest )
					{
						setcookie("upload_avatar_result","图片上传失败");

						header("location:./settings.php");

						exit;
					}

					die('{"result":"error","message":"图片上传失败"}');
				}
			}
		}
		else if($_POST['do'] == "settingEmail")
		{
			if( isset($_POST['password'],$_POST['email']) )
			{
				$currentPasswd = stripslashes($_POST['password']);

				$newEmail = trim($_POST['email']);

				if( strlen($currentPasswd) < 6 || strlen($currentPasswd) > 26 || substr_count($currentPasswd," ") > 0 )
				{
					die('{"result":"error","message":"当前密码无效","position":1}');
				}

				if( !emailCheck($newEmail) )
				{
					die('{"result":"error","message":"邮件地址不合法","position":2}');
				}

				$DB = database();

				$userInfo = PHPSay::getMemberInfo($DB,"uid",$loginInfo['uid']);

				if( $userInfo['password'] != md5($currentPasswd) )
				{
					echo '{"result":"error","message":"当前密码不正确","position":1}';
				}
				else
				{
					if( $userInfo['email'] == $newEmail )
					{
						echo '{"result":"error","message":"邮箱未修改","position":2}';
					}
					else
					{
						if( PHPSay::getMemberCount($DB,"email",$newEmail) > 0 )
						{
							echo '{"result":"error","message":"该邮件地址已被其它账号占用","position":2}';
						}
						else
						{
							$DB->query("UPDATE `phpsay_member` SET email='".$newEmail."' WHERE `uid`=".$loginInfo['uid']);

							echo '{"result":"success","message":""}';
						}
					}
				}

				$DB->close();			
			}
		}
		else if($_POST['do'] == "settingPassword")
		{
			if( isset($_POST['currentPasswd'],$_POST['userPasswd']) )
			{
				$currentPasswd = stripslashes($_POST['currentPasswd']);

				$userPasswd = stripslashes($_POST['userPasswd']);

				if( substr_count($currentPasswd," ") > 0 || substr_count($userPasswd," ") > 0 )
				{
					die('{"result":"error","message":"密码不能使用空格","position":2}');
				}

				if( strlen($userPasswd) < 6 )
				{
					die('{"result":"error","message":"密码长度不能少于6位","position":2}');
				}

				if( strlen($currentPasswd) > 26 || strlen($userPasswd) > 26 )
				{
					die('{"result":"error","message":"密码长度不能超出26位","position":2}');
				}

				$DB = database();

				$currentPassword = $DB->fetch_one("SELECT `password` FROM `phpsay_member` WHERE `uid`=".$loginInfo['uid']);

				if( $currentPassword != "" && md5($currentPasswd) != $currentPassword )
				{
					echo '{"result":"error","message":"当前密码不正确","position":1}';
				}
				else
				{
					if( $currentPassword == md5($userPasswd) )
					{
						echo '{"result":"error","message":"新密码不能与当前密码相同","position":2}';
					}
					else
					{
						$DB->query("UPDATE `phpsay_member` SET password='".md5($userPasswd)."' WHERE `uid`=".$loginInfo['uid']);

						echo '{"result":"success","message":""}';
					}
				}

				$DB->close();
			}
		}
	}
}
else
{
	if ( $isMobileRequest )
	{
		if ( isset($_POST['do']) )
		{
			if($_POST['do'] == "settingAvatar")
			{
				header("location:./passport.php");

				exit;
			}
		}
	}

	echo '{"result":"login"}';
}
?>