<?php
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------

function kshuffle(&$array) {
	    if(!is_array($array) || empty($array)) {
	        return false;
	    }
    	$tmp = array();
	    foreach($array as $key => $value) {
	        $tmp[] = array('k' => $key, 'v' => $value);
	    }
    	shuffle($tmp);
    	$array = array();
	    foreach($tmp as $entry) {
	        $array[$entry['k']] = $entry['v'];
	    }
    	return true;
	}	
?>