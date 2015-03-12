<?php
require(dirname(__FILE__)."/global.php");

require(dirname(__FILE__)."/config/config_QQConnect.php");

require(dirname(__FILE__)."/controller/class_QQAPI.php");

if ( $loginInfo['uid'] == 0 )
{
	if ( isset($_GET['do']) )
	{
		if ( $_GET['do'] == "QQLogin" )
		{
			$qc = new QC($QQConnectConfig['appid'],$QQConnectConfig['appkey']);

			$qc->qq_login();
		}
		else if ( $_GET['do'] == "QQCallback" )
		{
			$qc = new QC($QQConnectConfig['appid'],$QQConnectConfig['appkey']);

			$access_token = $qc->qq_callback();

			$openid = $qc->get_openid();

			if( strlen($openid) == 32 )
			{
				$qc = new QC($QQConnectConfig['appid'],$QQConnectConfig['appkey'],$access_token,$openid);

				$qqInfo = $qc->get_user_info();

				$DB = database();

				$userArr = PHPSay::getMemberInfo($DB,"qqid",strAddslashes($openid));

				$DB->close();

				if( empty($userArr['uid']) )
				{
					$connectInfo = array(
										"connect"	=> "QQ",
										"openid"	=> $openid,
										"nickname"	=> isset($qqInfo['nickname']) ? $qqInfo['nickname'] : "",
										"avatar"	=> isset($qqInfo['figureurl_qq_2']) ? $qqInfo['figureurl_qq_2'] : ""
										);

					connectCookie($PHPSayConfig['ppsecure'],$connectInfo);

					header("location:./passport.php?connect=".$connectInfo['connect']);
				}
				else
				{
					loginCookie($PHPSayConfig['ppsecure'],$userArr['uid'],$userArr['nickname'],$userArr['groupid']);

					header("location:./passport.php");
				}
			}
			else
			{
				header("location:./passport.php?connect=error");
			}
		}
		else if ( $_GET['do'] == "Join" )
		{
			$connectInfo = isQQConnect($PHPSayConfig['ppsecure']);

			$joinReturn = array("status"=>"error","response"=>"");

			if ( $connectInfo['connect'] == "" )
			{
				$joinReturn['response'] = "出现异常，请刷新页面";
			}
			else
			{
				if( isset($_POST['nickname']) )
				{
					$nickname = filterCode($_POST['nickname'],true);

					$nicknameError = checkNickname($nickname);

					if ($nicknameError != "")
					{
						$joinReturn['response'] = $nicknameError;
					}
					else
					{
						$DB = database();

						if( PHPSay::getMemberCount($DB,"qqid",strAddslashes($connectInfo['openid'])) != 0 )
						{
							$joinReturn['response'] = "连接重复，请刷新页面";
						}
						else
						{
							if( PHPSay::getMemberCount($DB,"nickname",$nickname) != 0 )
							{
								$joinReturn['response'] = "昵称已被占用，换一个吧 ^_^";
							}
							else
							{
								$userID = PHPSay::memberJoin($DB,$nickname,"","",strAddslashes($connectInfo['openid']));

								if ($userID > 0)
								{
									newAvatar($userID,$connectInfo['avatar']);

									loginCookie($PHPSayConfig['ppsecure'],$userID,$nickname,1);
									
									$joinReturn["status"] = "success";
								}						
							}
						}

						$DB->close();
					}
				}				
			}

			echo json_encode($joinReturn);
		}
	}
}
?>