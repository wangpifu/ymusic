<?php
require(dirname(__FILE__)."/global.php");

if(isset($_GET['lat'])&&isset($_GET['lng'])){
	$template = template( "map.html" );
	$template->assign( 'lat', $_GET['lat'] );
	$template->assign( 'lng', $_GET['lng'] );
	$template->output();
}