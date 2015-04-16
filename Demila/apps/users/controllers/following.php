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

	require_once ROOT_PATH.'/apps/users/models/users.class.php';
	$usersClass = new users();
			
	$users = $usersClass->getAll(0, 0, $itemsClass->usersWhere);
	abr('users', $users);
	
	
   $categories = $categoriesClass->getAll();
   $backslash = chr();


	$text = '';

	if(check_login_bool()) {
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		$following = $usersClass->getFollowersID($_SESSION['user']['user_id']);
		if(is_array($following)) {
			$whereQuery = '';
			foreach($following as $f) {
				if($whereQuery != '') {
					$whereQuery .= ' OR ';
				}
				$whereQuery .= " `user_id` = '".intval($f['follow_id'])."' ";
			}
			
			$limit = 10;
			$start = (PAGE-1)*$limit;
			
			$text .= ' <ul id="recently-followed-items">';
			
			$followingItems = $itemsClass->getAll($start, $limit, " `status` = 'active' AND ($whereQuery) ", "`datetime` DESC");
			if(is_array($followingItems)) {
				foreach($followingItems as $f) {
				
			$a_kat = $f['categories'];
			
			$s_nazwy_kat = "";
			
			foreach ($a_kat as $a_k)
			{
				foreach ($a_k as $kat_id) 
				{
					$s_nazwy_kat .= $categories[$kat_id]['name']." \ ";

				}
			}

			$s_nazwy_kat = mysql_real_escape_string(substr($s_nazwy_kat, 0, -3));  
				
				
				  $text .= '<li class="thumbnail"><a href="/'.$languageURL.'items/'.$f['id'].'" onclick=""><img class="landscape-image-magnifier preload no_preview" data-item-author="作者 '.$users[$f['user_id']]['username'].'" data-item-category="'. $s_nazwy_kat .'" data-item-cost="'.$currency['symbol'].$f['price'].'" data-item-name="'.htmlspecialchars($f['name']).'" data-preview-url="'.DATA_SERVER.'/uploads/items/'.$f['id'].'/preview.jpg" src="'.DATA_SERVER.'/uploads/items/'.$f['id'].'/'.$f['thumbnail'].'" title="" border="0" height="80" width="80" /></a></li>';
				}
			}

		
			
			$text .= '</ul>';
			
#生成html
			if(PAGE > 1) {
				$text = '<a href="javascript: void(0);" onclick="$.ajax({complete: function(request) { screenshotPreview(); hideLoading(); }, beforeSend: function() { showLoading(); }, dataType: &quot;script&quot;, type: &quot;post&quot;, url: &quot;/'.$languageURL.'users/following/?p='.(PAGE-1).'&quot;}); return false;" class="slider-control slider-prev"></a>'.$text;
			}	
			else {
				$text = ' <span class="slider-control slider-prev-disabled"></span>'.$text;
			}		
			
			if($itemsClass->foundRows > (PAGE*$limit)) {
				$text .= '<a href="javascript: void(0);" onclick="$.ajax({complete: function(request) { screenshotPreview(); hideLoading(); }, beforeSend: function() { showLoading(); }, dataType: &quot;script&quot;, type: &quot;post&quot;, url: &quot;/'.$languageURL.'users/following/?p='.(PAGE+1).'&quot;}); return false;" class="slider-control slider-next"></a>';
			}	
			else {
				$text .= '<span class="slider-control slider-next-disabled"></span>';
			}
			
			$text = 'jQuery("#recently-followed-items").html(\''.$text.'\')';
		}
	}
	
	die($text);

?>