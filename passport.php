<?php
require(dirname(__FILE__)."/global.php");

if ( isset($_GET['do']) )
{
	if ( $_GET['do'] == "Login" )
	{
		if( isset($_POST['account'],$_POST['password'],$_POST['usertype']) )
		{
			if ( $loginInfo['uid'] > 0 )
			{
				die('{"result":"login","message":""}');
			}

			$loginAccount	= strtolower(stripslashes(trim($_POST['account'])));

			$loginPassword	= stripslashes($_POST['password']);

			$userType = $_POST['usertype'];

			if( strlen($loginAccount) < 2 )
			{
				die('{"result":"error","message":"账号无效","position":1}');
			}
				
			if( strlen($loginPassword) < 6 || strlen($loginPassword) > 26 || substr_count($loginPassword," ") > 0 )
			{
				die('{"result":"error","message":"密码无效","position":2}');
			}

			if( checkNickname($loginAccount) != "" )
			{
				die('{"result":"error","message":"账号不合法","position":1}');
			}

			$DB = database();

			$userInfo = PHPSay::getMemberInfo($DB,"name",$loginAccount,$userType);

			if( empty($userInfo['uid']) )
			{
				echo '{"result":"error","message":"账号不存在","position":1}';
			}
			else
			{
				if( md5($loginPassword) == $userInfo['password'] )
				{

					$uid=PHPSay::getAllId($DB,$userInfo['uid'],$userType);

					loginCookie($PHPSayConfig['ppsecure'],$uid,$userInfo['nickname'],$userType);

					echo '{"result":"success","message":"登录成功","position":0}';
				}
				else
				{
					if( $userInfo['password'] == "" )
					{
						echo '{"result":"error","message":"该账号不支持密码登录","position":2}';
					}
					else
					{
						echo '{"result":"error","message":"账号与密码不匹配","position":2}';
					}
				}
			}

			$DB->close();
		}
	}
	else if ( $_GET['do'] == "logout" )
	{
		singOut();

		header("location:".$_SERVER['PHP_SELF']);
	}
	else if($_GET['do'] == "loginTime")
	{
		if ( $loginInfo['uid'] > 0 )
		{
			if( isset($_COOKIE['logintime']) )
			{
				die('{"result":"success","balance":0}');
			}

			setcookie("logintime",time(),mktime(23,59,59,date('n'),date('j'),date('Y'))+1,"/");

			$DB = database();

			$uid=PHPSay::getAllId($DB,$userInfo['uid'],$userType);

			$updateRes = PHPSay::updateMemberLogin( $DB, $uid, rand(10,50), $loginInfo['utype'] );

			$DB->close();

			loginCookie($PHPSayConfig['ppsecure'],$uid,$loginInfo['nickname'],$updateRes['utype'],time());

			echo '{"result":"success","balance":'.$updateRes['coin'].'}';
		}
	}
	else if ( $_GET['do'] == "SignUp" )
	{
		if( isset($_POST['email'],$_POST['nickname'],$_POST['password'],$_POST['usertype']) )
		{
			if ( $loginInfo['uid'] > 0 )
			{
				die('{"result":"login","message":""}');
			}

			if ( !$PHPSayConfig['emailjoin'] )
			{
				die('{"result":"error","message":"您暂时不能使用邮箱注册","position":0}');
			}			

			$email	= strtolower(stripslashes(trim($_POST['email'])));

			$nickname = filterCode($_POST['nickname'],true);

			$password	= stripslashes($_POST['password']);

			$userType = $_POST['usertype'];

			if( !emailCheck($email) )
			{
				die('{"result":"error","message":"邮件地址不正确","position":1}');
			}

			$nicknameError = checkNickname($nickname);

			if( $nicknameError != "" )
			{
				die('{"result":"error","message":"'.$nicknameError.'","position":2}');
			}

			if( substr_count($password," ") > 0 )
			{
				die('{"result":"error","message":"密码不能使用空格","position":3}');
			}

			if( strlen($password) < 6 || strlen($password) > 26 )
			{
				die('{"result":"error","message":"密码长度不合法","position":3}');
			}

			$DB = database();

			$log = PHPSay::memberJoin($DB,$nickname,$email,md5($password),$userType);
			//echo "0:".$log[0].";1:".$log[1];
			//log[0] is userId,log[1] is errorlog
			if ($log[1] == "")
			{
				newAvatar($log[0],"");

				loginCookie($PHPSayConfig['ppsecure'],$log[0],$nickname,$userType);

				echo '{"result":"success","message":"注册成功"}';
			}
			else
			{
				echo '{"result":"error","message":"'.$log[1].'","position":0}';
			}

			$DB->close();
		}
	}
	else if ( $_GET['do'] == "sendPassword" )
	{
		if( isset($_POST['email'],$_POST['verify_code']) )
		{
			session_start();

			$sessionCode = isset($_SESSION['identifying_code']) ? $_SESSION['identifying_code'] : "";

			session_destroy();

			if( $sessionCode == "" || $sessionCode != md5(strtoupper($_POST['verify_code']).$PHPSayConfig['ppsecure']) )
			{
				die('{"result":"error","message":"验证码不正确","position":2}');
			}

			if( !emailCheck($_POST['email']) )
			{
				die('{"result":"error","message":"邮件地址不合法","position":1}');
			}

			$DB = database();

			$userInfo = PHPSay::getMemberInfo($DB,"email",$_POST['email']);

			if( $userInfo['email'] == "" )
			{
				echo '{"result":"error","message":"该邮件地址尚未登记","position":1}';
			}
			else
			{
				$codeArr = PHPSay::getResetPasswordCode($DB,"uid",$userInfo['uid']);

				$resetCode = $codeArr['code'];

				if( time() - $codeArr['dateline'] >= 1800 )
				{
					$resetCode = $userInfo['uid'].createSecureKey(1,false).createSecureKey(9);

					$DB->query("REPLACE INTO `phpsay_resetpassword` VALUE(".$userInfo['uid'].",'".$resetCode."',".time().")");
				}

				if( time() - $codeArr['dateline'] > 120 )
				{
					if( $codeArr['dateline'] > 0 )
					{
						$DB->query("UPDATE `phpsay_resetpassword` SET `dateline`=".time()." WHERE `uid`=".$userInfo['uid']);
					}

					sendPasswordEmail($PHPSayConfig['sitename'],$PHPSayConfig['sitemail'],$userInfo['email'],$userInfo['nickname'],$resetCode);
				}

				echo '{"result":"success","message":""}';
			}

			$DB->close();
		}
	}
	else if ( $_GET['do'] == "resetPassword" )
	{
		if( isset($_GET['code'],$_POST['password']) )
		{
			$newPassword = stripslashes($_POST['password']);

			if( substr_count($newPassword," ") > 0 )
			{
				die('{"result":"error","message":"密码不能使用空格"}');
			}

			if( strlen($newPassword) < 6 || strlen($newPassword) > 26 )
			{
				die('{"result":"error","message":"密码长度不合法"}');
			}

			$DB = database();

			$codeArr = PHPSay::getResetPasswordCode($DB,"code",strAddslashes($_GET['code']));

			if( empty($codeArr['uid']) || time() - $codeArr['dateline'] > 2000 )
			{
				echo '{"result":"error","message":"链接已失效，请刷新页面"}';
			}
			else
			{
				$DB->query("UPDATE `phpsay_member` SET password='".md5($newPassword)."' WHERE `uid`=".$codeArr['uid']);

				$DB->query("DELETE FROM `phpsay_resetpassword` WHERE `uid`=".$codeArr['uid']);

				$userInfo = PHPSay::getMemberInfo($DB,"uid",$codeArr['uid']);

				loginCookie($PHPSayConfig['ppsecure'],$userInfo['uid'],$userInfo['nickname'],$userInfo['groupid']);

				echo '{"result":"success","message":""}';
			}

			$DB->close();
		}
	}
	else if ( $_GET['do'] == "password" )
	{
		if( $loginInfo['uid'] > 0 )
		{
			header("location:./");

			exit;
		}

		$resetCode = "";

		if( isset($_GET['code']) )
		{
			if( $_GET['code'] != "" )
			{
				$DB = database();

				$codeArray = PHPSay::getResetPasswordCode($DB,"code",strAddslashes($_GET['code']));

				$DB->close();

				if( time() - $codeArray['dateline'] < 1800 )
				{
					$resetCode = $codeArray['code'];
				}
			}

			if( $resetCode == "" )
			{
				header("location:./passport.php?do=password");

				exit;
			}
		}

		$template = template( $isMobileRequest ? "mobile_reset_password.html" : "reset_password.html" );

		$template->assign( 'PHPSayConfig', $PHPSayConfig );

		$template->assign( 'resetCode', $resetCode );

		$template->output();
	}
	else
	{
		header("location:".$_SERVER['PHP_SELF']);
	}
}
else
{
	if ( $loginInfo['uid'] > 0 )
	{
		$locationURL = "./";
		
		if( isset($_COOKIE['returnURL']) )
		{
			if( substr($_COOKIE['returnURL'], 0 ,1) == "/" )
			{
				$locationURL = $_COOKIE['returnURL'];
			}

			setcookie( 'returnURL', '', 0, "/" );
		}
		
		header("location:".$locationURL);
	}
	else
	{
		if( isset($_GET['return']) )
		{
			if( substr($_GET['return'], 0 ,1) == "/" )
			{
				setcookie( 'returnURL', $_GET['return'], time()+3600, "/" );
			}
		}

		$template = template( $isMobileRequest ? "mobile_login.html" : "login.html" );

		$template->assign( 'PHPSayConfig', $PHPSayConfig );

		$template->assign( 'connectArray', isQQConnect($PHPSayConfig['ppsecure']) );

		$template->output();
	}
}
?>