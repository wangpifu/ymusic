<?php
require(dirname(__FILE__)."/global.php");

require(dirname(__FILE__)."/controller/class_Admin.php");

if( isset($_POST['do']) )
{
	if ( $loginInfo['group'] < 3 )
	{
		echo '{"result":"error","message":"没有权限"}';
	}
	else
	{
		if( $_POST['do'] == "sponsor_delete" )
		{
			if( isset($_POST['Id']) && is_numeric($_POST['Id']) )
			{
				$DB = database();

				echo '{"result":"success","message":"'.Admin::sponsorDelete($DB,$_POST['Id']).'"}';

				$DB->close();
			}
		}
		else if( $_POST['do'] == "sponsor_status" )
		{
			if( isset($_POST['Id'],$_POST['Status']) && is_numeric($_POST['Id']) )
			{
				$DB = database();

				Admin::sponsorExpire($DB,$_POST['Id'],$_POST['Status']);

				$DB->close();

				echo '{"result":"success","message":""}';
			}
		}
		else if( $_POST['do'] == "sponsor_order" )
		{
			if( isset($_POST['Ids']) )
			{
				$sponsorIds = explode(",",$_POST['Ids']);

				if ( count($sponsorIds) > 1 )
				{
					$DB = database();
					
					echo '{"result":"success","message":"'.Admin::sponsorOrder($DB,$sponsorIds).'"}';

					$DB->close();
				}

				echo $position - 1;
			}
		}
		else if( $_POST['do'] == "club_order" )
		{
			if( isset($_POST['clubOrder']) )
			{
				$clubIds = explode(",",$_POST['clubOrder']);

				if( count($clubIds) > 1 )
				{
					$DB = database();

					echo '{"result":"success","message":"'.Admin::clubOrder($DB,$clubIds).'"}';

					$DB->close();
				}
			}
		}
		else if( $_POST['do'] == "club_trash" )
		{
			if( isset($_POST['trashId']) && is_numeric($_POST['trashId']) )
			{
				$DB = database();

				echo '{"result":"success","message":"'.Admin::trashClub($DB, $_POST['trashId']).'"}';

				$DB->close();
			}
		}
		else if( $_POST['do'] == "club_edit" )
		{
			if( isset($_POST['clubId'],$_POST['clubName']) && is_numeric($_POST['clubId']) )
			{
				$clubName = stripslashes(trim($_POST['clubName']));

				if( getStrlen($clubName) < 1 || getStrlen($clubName) > 16 || !preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-\s]+$/u', $clubName) )
				{
					echo '{"result":"error","message":"名称不合法"}';
				}
				else
				{
					$DB = database();

					$editClubResult = Admin::editClub($DB,$_POST['clubId'],$clubName);

					$DB->close();

					if( $editClubResult == "-1" )
					{
						echo '{"result":"error","message":"名称已存在"}';
					}
					else if( $editClubResult > 0 )
					{
						echo '{"result":"success","message":""}';
					}
					else
					{
						echo '{"result":"error","message":"系统异常"}';
					}
				}
			}
		}
		else if( $_POST['do'] == "cache_delete" )
		{
			$deleteNumber = 0;

			$pathName = dirname(__FILE__)."/./_cache";

			if ( false != ($handle = opendir($pathName)) )
			{
				while ( false !== ($file = readdir($handle)) )
				{
					if ( substr($file,-9) == "_html.php" )
					{
						unlink($pathName."/".$file);

						$deleteNumber++;
					}
				}
			}

			echo '{"result":"success","message":"'.$deleteNumber.'"}';
		}
	}
}
?>