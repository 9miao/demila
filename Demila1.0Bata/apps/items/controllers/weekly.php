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
	
	$itemsClass = new items();
	
	$limit = 4;
	$start = (PAGE-1) * $limit;	
	
	$text = '';
	
	$items = $itemsClass->getAll($start, $limit, " `status` = 'active' AND `weekly_to` >= '".date('Y-m-d')."' ", "`datetime` DESC");
	
	$categories = $categoriesClass->getAll();
	
	if(PAGE > 1) {
		$text .= '<a href="javascript: void(0);" onclick="$.ajax({complete: function(request) { screenshotPreview(); hideLoading(); }, beforeSend: function() { showLoading(); }, dataType: &quot;script&quot;, type: &quot;post&quot;, url: &quot;/'.$languageURL.'items/weekly/?p='.(PAGE-1).'&quot;}); return false;" title="" class="slider-control slider-prev"></a>';
	}
	else {
		$text .= '<span class="slider-control slider-prev-disabled"></span>';		
	}
	
	$backslash = chr();

	if(is_array($items)) {
		$text .= '<ul id="weekly-featured-items">';
		foreach($items as $i) {
		
			$a_kat = $i['categories'];
			
			$s_nazwy_kat = "";
			
			foreach ($a_kat as $a_k)
			{
				foreach ($a_k as $kat_id) 
				{
					$s_nazwy_kat .= $categories[$kat_id]['name']." \ ";

				}
			}

			$s_nazwy_kat = mysql_real_escape_string(substr($s_nazwy_kat, 0, -3));  

			$text .= '<li class="thumbnail"><a href="/'.$languageURL.'items/'.$i['id'].'" title="'.htmlspecialchars($i['name']).'"><img data-tooltip="'.htmlspecialchars($i['name']).'" alt="'.htmlspecialchars($i['name']).'" class="landscape-image-magnifier preload no_preview" data-item-author="作者 '.$users[$i['user_id']]['username'].'" data-item-category="'. $s_nazwy_kat .'" data-item-cost="'.$currency['symbol'].$i['price'].' " data-item-name="'.htmlspecialchars($i['name']).'" data-preview-height="" data-preview-url="'.DATA_SERVER.'/uploads/items/'.$i['id'].'/preview.jpg" data-preview-width="" src="'.DATA_SERVER.'/uploads/items/'.$i['id'].'/'.$i['thumbnail'].'" title="" border="0" width="80" height="80"></a></li>';
		}
		$text .= '</ul>';
	}
	
	if((PAGE*$limit) <= $itemsClass->foundRows) {
		$text .= '<a href="javascript: void(0);" title="" onclick="$.ajax({complete: function(request) { screenshotPreview(); hideLoading(); }, beforeSend: function() { showLoading(); }, dataType: &quot;script&quot;, type: &quot;post&quot;, url: &quot;/'.$languageURL.'items/weekly/?p='.(PAGE+1).'&quot;}); return false;" class="slider-control slider-next"></a>';
	}
	else {
		$text .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="slider-control slider-next-disabled"></span>';		
	}
	

	die('
		jQuery("#weekly-featured-items").html(\''.$text.'\');
	');
	
?>