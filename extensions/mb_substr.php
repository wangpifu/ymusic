<?php
function _mb_substr($str,$len)
{
	if( $str == "" )
	{
		return "图片";
	}

	$text = str_replace('"',"'",strip_tags($str));

	$text = preg_replace("/[\s]+/is"," ",$text);

	$text = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '[链接]', $text);

	$str = mb_substr($text,0,$len,'UTF-8');

	if( $str != $text )
	{
		$str .= "...";
	}

	return trim($str);
}
?>