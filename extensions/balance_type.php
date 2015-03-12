<?php
function _balance_type($type)
{
	$typeName = "";

	switch ($type) {
		case '1':
			$typeName = "系统赠送";
			break;
		case '2':
			$typeName = "登录奖励";
			break;
		case '3':
			$typeName = "创建主题";
			break;
		case '4':
			$typeName = "回复主题";
			break;
		case '5':
			$typeName = "主题收益";
			break;
		case '6':
			$typeName = "回复收益";
			break;
	}

	return $typeName;
}
?>