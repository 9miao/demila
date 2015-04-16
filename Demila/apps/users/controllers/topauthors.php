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

_setView(__FILE__);

	$text = '';

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
			
	$limit = 9;
	$start = (PAGE-1)*$limit;
	
	//$text .= '<ul class="thumb_list w276 left">';
	
	$topAuthors = $usersClass->getAll($start, $limit, " `status` = 'activate' ", "`sales` DESC");
	if(is_array($topAuthors)) {
		foreach($topAuthors as $a) {
		  $text .= '<a href="/'.$languageURL.'users/'.$a['username'].'" class="user" title="'.htmlspecialchars($a['username']).'">';
		 	if($a['avatar'] != '') {
		 		$text .= '<img src="'.DATA_SERVER.'/uploads/users/'.$a['user_id'].'/A_'.$a['avatar'].'" width="80"  height="80" border="0" alt="" title="" />';	
		 	} 
		 	else {
		 		$text .= '<img src="'.DATA_SERVER.'/images/common/default-user.jpg" width="80"  height="80" border="0" alt="" title="" />';
		 	} 	
		  $text .= '</a>'; 
		}

	$emptyThumb = (9-$usersClass->foundRows);
	for($i=0; $i<$emptyThumb; $i++) {
	//	$text .= '<li></li>';
	}
	
	//$text .= '</ul>';
	
#生成HTML
		if(PAGE > 1) {
				$text = '<a href="javascript: void(0);" onclick="$.ajax({complete: function(request) { screenshotPreview(); hideLoading(); }, beforeSend: function() { showLoading(); }, dataType: &quot;script&quot;, type: &quot;post&quot;, url: &quot;/'.$languageURL.'users/topauthors/?p='.(PAGE-1).'&quot;}); return false;" class="left-arrow"><img src="'.DATA_SERVER.'images/left-arrow.png" alt="" /></a>'.$text;
			}	
			else {
				$text = '<a href="javascript: void(0);" title="" class="left-arrow"><img src="'.DATA_SERVER.'images/left-arrow.png" alt="" /></a>'.$text;
			}		
			
			if($itemsClass->foundRows > (PAGE*$limit)) {
				$text .= '<a href="javascript: void(0);" onclick="$.ajax({complete: function(request) { screenshotPreview(); hideLoading(); }, beforeSend: function() { showLoading(); }, dataType: &quot;script&quot;, type: &quot;post&quot;, url: &quot;/'.$languageURL.'users/topauthors/?p='.(PAGE+1).'&quot;}); return false;" class="right-arrow"><img src="'.DATA_SERVER.'images/right-arrow.png" alt="" /></a>';
			}	
			else {
				$text .= '<a href="javascript: void(0);" title="" class="right-arrow"><img src="'.DATA_SERVER.'images/right-arrow.png" alt="" /></a>';
			}
			
	$text = 'jQuery("#top_authors_container").html(\''.$text.'\')';
	}
	die($text);
	
?>