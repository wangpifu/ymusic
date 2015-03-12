<?php
function _date($time)
{
	return ($time > 0) ? date('Y年n月j日',$time) : "---";
}
?>