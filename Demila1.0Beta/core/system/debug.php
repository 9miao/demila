<?
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------


if (check_debug ()) {
	//if(array_search($_SERVER['REMOTE_ADDR'], $admin_ips) && $config['debug'] == 1){
	

	echo "<div style='clear: both;'>";
	echo @$debug;
	//$template->dump();
	echo "Session: ";
	print_r ( $_SESSION );
	echo '<BR /><BR />Cookies: ';
	print_r ( @$_COOKIE );
	echo '<BR /><BR />GET:';
	var_dump ( @$_GET );
	echo '<BR /><BR />';
	
	echo 'Page Generate: ' . $execute->get () . '<BR />';
	
	echo $mysql->print_queries ();
	
	echo '</div>';

}

?>