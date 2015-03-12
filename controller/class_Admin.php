<?php
class Admin extends PHPSay 
{
	public static function getMemberList($DB,$page,$per)
	{
		$memberArr = array();

		$memberCount = $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_member`");

		$Result = $DB->query("SELECT * FROM `phpsay_member` ORDER BY `uid` DESC LIMIT ".($page-1)*$per.",".$per);

		while($Re = $DB->fetch_array($Result))
		{
			$memberArr[] = array(
								"uid"		=> $Re['uid'],
								"avatar"	=> getAvatarURL($Re['uid']),
								"nickname"	=> $Re['nickname'],
								"email"		=> $Re['email'],
								"password"	=> $Re['password'],
								"balance"	=> $Re['balance'],
								"regtime"	=> date('Y-m-d H:i:s',$Re['regtime']),
								"qqid"		=> $Re['qqid'],
								"groupid"	=> $Re['groupid'],
								"logintime"	=> ($Re['logintime'] > 0) ? date('Y-m-d H:i:s',$Re['logintime']) : "",
								"topictime"	=> ($Re['topictime'] > 0) ? date('Y-m-d H:i:s',$Re['topictime']) : "",
								"replytime"	=> ($Re['replytime'] > 0) ? date('Y-m-d H:i:s',$Re['replytime']) : ""
								);
		}

		return array("count"=>$memberCount,"list"=>$memberArr,"page"=>PHPSay::getPage($memberCount,$per,$page));
	}

	public static function getAdminClub($DB,$Trash)
	{
		$Sql = "SELECT * FROM `phpsay_club` WHERE ";

		if( $Trash == 0 )
		{
			$Sql .= "`position` = 0";
		}
		else
		{
			$Sql .= "`position` > 0 ORDER BY `position` ASC";
		}

		$Result = $DB->query($Sql);

		$clubList = array();

		while($Re = $DB->fetch_array($Result))
		{
			$clubList[] = array("cid"		=> $Re['cid'],
								"cname"		=> $Re['clubname'],
								"position"	=> $Re['position']
								);
		}

		return $clubList;
	}

	public static function trashClub($DB,$clubId)
	{
		$Position = $DB->fetch_one("SELECT `position` FROM `phpsay_club` WHERE `cid`=".$clubId);

		if( $Position ==  "" )
		{
			return "";
		}

		if( $Position ==  0 )
		{
			$DB->query("UPDATE `phpsay_club` SET `position` = 1 WHERE `cid`=".$clubId);

			return "1";
		}

		if( $Position >  0 )
		{
			$DB->query("UPDATE `phpsay_club` SET `position` = 0 WHERE `cid`=".$clubId);

			return "0";
		}
	}

	public static function editClub($DB,$clubId,$clubName)
	{
		if( $clubId >= 1 )
		{
			if( $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_club` WHERE `clubname`='".$clubName."' AND `cid` != ".$clubId) )
			{
				return "-1";
			}

			if( $DB->fetch_one("SELECT `clubname` FROM `phpsay_club` WHERE `cid` = ".$clubId." AND `position` > 0") == "" )
			{
				return "";
			}

			$DB->query( $DB->update_sql("`phpsay_club`",array("clubname" => $clubName),"`cid`=".$clubId) );

			$DB->query( $DB->update_sql("`phpsay_topic`",array("clubname" => $clubName),"`cid`=".$clubId) );

			return $clubId;
		}

		if( $DB->fetch_one("SELECT COUNT(*) FROM `phpsay_club` WHERE `clubname`='".$clubName."'") )
		{
			return "-1";
		}

		$DB->query( $DB->insert_sql("`phpsay_club`",array("clubname" => $clubName)) );

		return $DB->insert_id();
	}

	public static function clubOrder($DB,$clubIds)
	{
		$position = 1;

		foreach($clubIds as $cid)
		{
			if( is_numeric($cid) )
			{
				$position++;

				$DB->query("UPDATE `phpsay_club` SET `position` = ".$position." WHERE `cid`=".$cid);
			}
		}

		return $position - 1;
	}

	public static function sponsorOrder($DB,$Ids)
	{
		$position = 1;

		foreach($Ids as $sid)
		{
			if( is_numeric($sid) )
			{
				$position++;

				$DB->query("UPDATE `phpsay_sponsor` SET `position` = ".$position." WHERE `sid`=".$sid);
			}
		}

		return $position - 1;
	}

	public static function sponsorDelete($DB,$Id)
	{
		return $DB->affected_rows("DELETE FROM `phpsay_sponsor` WHERE `sid`=".$Id);
	}

	public static function sponsorExpire($DB,$Id,$Status)
	{
		$sponsorUid = $DB->fetch_one("SELECT `uid` FROM `phpsay_sponsor` WHERE `sid`=".$Id);

		if( $sponsorUid == "" )
		{
			return false;
		}

		$endTime = 0;

		if( $Status == 1 )
		{
			if( $sponsorUid > 0 )
			{
				$endTime = time() + 86460;
			}
			else
			{
				$endTime = time() + 315360000;
			}
		}

		return $DB->query("UPDATE `phpsay_sponsor` SET `endtime` = ".$endTime." WHERE `sid`=".$Id);
	}
}
?>