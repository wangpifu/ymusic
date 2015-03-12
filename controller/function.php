<?php
function template($htmlFile)
{
	require(dirname(__FILE__)."/class_Template.php");
	
	$template  =  new Template($htmlFile);
	
	return $template;
}

function database()
{
	require(dirname(__FILE__)."/class_MySQL.php");

	require(dirname(__FILE__)."/../config/config_MySQL.php");
	
	$connect = new mysqli($mysql_host,$mysql_user,$mysql_pass,$mysql_dbname);

	if (mysqli_connect_errno()) {
	    echo "Connect failed: %s\n". mysqli_connect_error();
	    exit();
	}else{
		//echo "success";
	}

	$DB = new DB_MySQL($connect);
	
	$DB->query("SET NAMES 'utf8'");
	
	return $DB;
}

function loginCookie($sKey,$uid,$name,$userType,$time="")
{
	if( $time == "" )
	{
		$time = time();
	}
	
	setcookie("uname",$name,$time+604800,"/");

	$loginEncode = Xxtea::encrypt(json_encode(array($uid,$name,$userType,$time)),$sKey);
	
	setcookie("secure",$loginEncode,$time+604800,"/");

	setcookie("connect","");

	setcookie("utype",$userType);
}

function isLogin($sKey,$cookie)
{
	$userInfo = array( "uid" => 0, "nickname" => "", "group" => 0, "logintime" => 0, "avatar" => "" );

	if( isset($cookie['uname'],$cookie['secure']) )
	{
		$userArr = json_decode(Xxtea::decrypt($cookie['secure'],$sKey),true);

		if( count($userArr) == 4 )
		{
			if( $cookie['uname'] == $userArr[1] )
			{
				$userInfo['uid']		= $userArr[0];

				$userInfo['nickname']	= $userArr[1];
				
				$userInfo['utype']		= $userArr[2];
				
				$userInfo['logintime']	= $userArr[3];
				
				$userInfo['avatar']		= getAvatarURL($userArr[0]);

			}
		}
	}
	
	return $userInfo;
}

function singOut()
{
	setcookie("secure","",1,"/");

	setcookie("logintime","",1,"/");
}

function connectCookie($sKey,$connectArray)
{
	$loginEncode = Xxtea::encrypt(json_encode($connectArray),$sKey);
	
	setcookie("connect",$loginEncode);
}

function isQQConnect($sKey)
{
	$connectArray = array( "connect" => "", "openid" => "", "nickname" => "", "avatar" => "" );
	
	if( isset($_COOKIE['connect']) )
	{
		$cookieArray = json_decode(Xxtea::decrypt($_COOKIE['connect'],$sKey),true);

		if( isset($cookieArray['connect'],$cookieArray['openid'],$cookieArray['nickname'],$cookieArray['avatar']) )
		{
			if ( $cookieArray['connect'] == "QQ" && strlen($cookieArray['openid']) == 32 )
			{
				return $cookieArray;
			}
		}
	}
	
	return $connectArray;
}

function createSecureKey($len,$num=true)
{
	$arr = array(
				'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
				'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
				);
	if( $num )
	{
		array_push($arr,'0','1','2','3','4','5','6','7','8','9');
	}

	$keyindex = count($arr)-1;
	
	$keystr = "";
	
	for ( $i=0;$i<$len;$i++ )
	{
		$keystr .= $arr[rand(0,$keyindex)];
	}

	return $keystr;
}

function avatarPath($uid)
{
	return "avatar/".($uid%32)."/".($uid%257)."/".$uid."/";
}

function getAvatarURL($uid,$size=100)
{
	return avatarPath($uid).$size;
}

function getImageURL($file,$size)
{
	if ($file == "")
	{
		return "";
	}
	
	return "./pictures/".$file."_".$size;
}

function emailCheck($email)
{
	$ret = false;
	
	if( strlen($email) >= 6 && strlen($email) <= 36 )
	{
		if( strstr($email, '@') && strstr($email, '.') )
		{
			if( preg_match("/^([_a-z0-9]+([\._a-z0-9-]+)*)@([a-z0-9]{1,}(\.[a-z0-9-]{2,})*\.[a-z]{2,4})$/i", $email) )
			{
				$ret = true;
			}
		}
	}

	return $ret; 
}

function getStrlen($str)
{
	return mb_strlen($str,"utf-8");
}

function textSubstr($str,$start,$len)
{
	return mb_substr($str,$start,$len,"utf-8");
}

function textCount($str,$needle)
{
	return mb_substr_count($str,$needle,"utf-8");
}

function checkNickname($nickname)
{
	if( strlen($nickname) < 3 || getStrlen($nickname) < 2 )
	{
		return "昵称太短了 ^_^";
	}
	
	if( getStrlen($nickname) > 10 )
	{
		return "昵称太长了 ^_^";
	}
	
	if( is_numeric(substr($nickname,0,1)) || substr($nickname,0,1) == "_" )
	{
		return "昵称不能以数字和下划线开头";
	}
	
	if( substr($nickname,-1,1) == "_" )
	{
		return "昵称不能以下划线结尾";
	}
	
	if( !preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $nickname) )
	{
		return "昵称只能用汉字、英文、数字及下划线";
	}

	for( $i=0,$l=getStrlen($nickname); $i<$l; $i++ )
	{
		if( textCount($nickname,textSubstr($nickname,$i,1)) > 3 )
		{
			return "昵称内重复字符太多";
		}
	}
	
	return "";
}

function isMobileRequest()
{
	$HTTP_USER_AGENT = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : "";

	if( preg_match('/(symbian|smartphone|opera mini|windows phone|iphone|ipad|ipod|android|blackberry|kindle|ucweb)/i',$HTTP_USER_AGENT) )
	{
		return true;
	}

	return false;
}

function countDownTime($unixTime)
{
	if( $unixTime == 0 )
	{
		return "";
	}

	$showTime = date('Y',$unixTime)."年".date('n',$unixTime)."月".date('j',$unixTime)."日 ".date('H:i',$unixTime);

	if( date('Y',$unixTime) == date('Y') )
	{
		$showTime = date('n',$unixTime)."月".date('j',$unixTime)."日 ".date('H:i',$unixTime);

		if( date('n.j',$unixTime) == date('n.j') )
		{
			$timeDifference = time() - $unixTime + 1;

			if( $timeDifference < 60 )
			{
				return $timeDifference."秒前";
			}

			if( $timeDifference >= 60 && $timeDifference < 3600 )
			{
				return floor($timeDifference/60)."分钟前";
			}
			
			return date('H:i',$unixTime);
		}

		if( date('n.j',($unixTime+86400)) == date('n.j') )
		{
			return "昨天 ".date('H:i',$unixTime);
		}
	}

	return $showTime;
}

function remainingTime($unixTime)
{
	$nowTime = time();

	if( $nowTime >= $unixTime )
	{
		return "";
	}

	$second = $unixTime - $nowTime;

	if( $second < 61 )
	{
		return $second."秒";
	}

	$day = floor($second/86400);

	if( $day >= 3 )
	{
		$hour = floor($second%86400/3600);

		if( $hour > 0 )
		{
			return $day."天".$hour."小时";
		}

		return $day."天";
	}

	$hour = floor($second/3600);

	$minute = floor($second%3600/60);

	if( $minute > 0 )
	{
		return $hour."小时".$minute."分钟";
	}

	return $hour."小时";
}

function parseUrl($str)
{
	$auto_arr = array(
					"/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp):\/\/)([a-z0-9\/\-_+=.~!%@?#%&;:$\\│]+)/i",
					"/(?<=[^\]a-z0-9\/\-_.~?=:.])([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4}))/i"
					);

	$auto_url = array(
					'<a href="\\1\\3" target="_blank">\\1\\3</a>',
					'<a href="mailto:\\0">\\0</a>'
					);

	return preg_replace($auto_arr,$auto_url," ".$str);
}

function strAddslashes($str)
{
	if ( !get_magic_quotes_gpc() )
	{
		$str = addslashes($str);
	}

	return $str;
}

function filterCode($str,$filter=true)
{
	if( $filter )
	{
		$str = preg_replace("/[\n]+/is","\n",str_replace(array("\r","\t"),"",$str));
	}
	
	return trim(strAddslashes(strip_tags($str)));
}

function filterHTML($str)
{
	$str = stripslashes($str);

	$str = htmlspecialchars($str);

	$str = parseUrl($str);

	$str = preg_replace_callback("/\@([^[:punct:]\s]{3,39})([\s]+)/",'atName', $str." ");
	
	return trim(nl2br($str));
}

function atName($str)
{
	if( in_array($str[1], array("。","？","，","！")) )
	{
		return $str[0];
	}

	return '<a href="user.php?id='.urlencode($str[1]).'">@'.$str[1].'</a>'.$str[2];
}

function mkDirs($path)
{
	$array_path = explode("/",$path);

	$_path = "";
		
	for($i=0;$i<count($array_path);$i++)
	{
		$_path .= $array_path[$i]."/";

		if( $array_path[$i] != "" && !file_exists($_path))
		{
			mkdir($_path,0777);
		}
	}

	return true;
}

function newAvatar($userId,$avatarURL="",$defaultAvatar = "static/avatar.jpg")
{
	mkDirs(avatarPath($userId));

	$avatarFile = getAvatarURL($userId,100);

	if( $avatarURL != "" )
	{
		$avatarData = @file_get_contents($avatarURL,false,stream_context_create(array('http'=>array('method'=>'GET','timeout'=>3))));

		file_put_contents($avatarFile, $avatarData);

		$imgInfo = @getimagesize($avatarFile);

		if( isset($imgInfo[0],$imgInfo[1],$imgInfo[2]) && in_array($imgInfo[2], array(1,2,3)) )
		{
			createAvatar($avatarFile,$imgInfo,100,$avatarFile);
		}
		else
		{
			unlink($avatarFile);

			copy($defaultAvatar, $avatarFile);
		}
	}
	else
	{
		copy($defaultAvatar, $avatarFile);
	}
}

function createAvatar($source,$imgInfo,$wh,$destination)
{
	$image_p = imagecreatetruecolor($wh, $wh);
	
	switch($imgInfo[2])
	{
		case 1:
			$image = imagecreatefromgif($source);
			break;
		case 2:
			$image = imagecreatefromjpeg($source);
			break;
		case 3:
			$image = imagecreatefrompng($source);
			break;
	}
	
	if( $imgInfo[0] > $imgInfo[1] )
	{
		$imgInfo[0] = $imgInfo[0]-($imgInfo[0]-$imgInfo[1]);
	}
	
	if( $imgInfo[0] < $imgInfo[1] )
	{
		$imgInfo[1] = $imgInfo[1]-($imgInfo[1]-$imgInfo[0]);
	}
	
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $wh, $wh, $imgInfo[0], $imgInfo[1]);
	
	imagejpeg($image_p, $destination,100);
	
	imagedestroy($image_p);
	
	imagedestroy($image);
}

function sendPasswordEmail($sendName,$SendMail,$receiveMail,$receiveName,$resetCode)
{
	$headers  = 'MIME-Version: 1.0' . "\r\n";

	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

	$headers .= 'From: '.$sendName.' <'.$SendMail.'>' . "\r\n";

	$resetUrl = "http://".$_SERVER['HTTP_HOST']."/passport.php?do=password&code=".$resetCode;

	$mailBody = '<b>'.$receiveName.'</b>，请点击以下链接重设密码（<u>'.date('Y-m-d H:i',time()+1800).'</u>前有效）<br>';

	$mailBody .= '<a href="'.$resetUrl.'" target="_blank">'.$resetUrl.'</a>';

	@mail($receiveMail,$sendName."-密码重置",$mailBody,$headers);
}
?>