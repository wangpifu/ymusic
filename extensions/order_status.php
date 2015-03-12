<?php
function _order_status($status)
{
	$statusName = "";

	switch ($status) {
		case '0':
			$statusName = "交易关闭";
			break;
		case '1':
			$statusName = "等待付款";
			break;
		case '2':
			$statusName = "等待发货";
			break;
		case '3':
			$statusName = "待您确认";
			break;
		case '4':
			$statusName = "交易完成";
			break;
	}

	return $statusName;
}
?>