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


if(isset($_GET['url']) && $_GET['url'] != 'admin/') {

	function paging($frontUrl, $backUrl, $currentPage, $rowsPerPage, $allRows) {
		global $langArray;
		
		if ($allRows <= $rowsPerPage && ! check_debug ()) {
			return '';
		}
		
	//	$paging = '<div class="pagination"><div class="page_numbers"><span class="page-first">'.$langArray['page'].'</span>';
		$paging = FALSE;
		$pages = ceil ( $allRows / $rowsPerPage );
		
		if ($currentPage > 1)
			$paging .= '<a href="' . $frontUrl . '' . ($currentPage - 1) . '' . $backUrl . '" title="前一页" class="previous_page">往前</a>';
		
		if ($pages > 10) {
			if ($currentPage == 1) {
				for($i = 1; $i <= 10; $i ++)
					$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="current"' : ' ') . '>' . $i . '</a>';
			} elseif ($currentPage == $pages) {
				for($i = $pages - 10; $i <= $pages; $i ++)
					$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="current"' : ' ') . '>' . $i . '</a>';
			} else {
				$start = $currentPage - 5;
				
				if ($start < 1) {
					$start_at = 1;
					$end = (- 1) * $start + 5 + $currentPage;
				} else {
					$start_at = $start;
					$end = $start_at + 10;
					if ($end > $pages) {
						$start_at = $start_at - ($end - $pages);
						$end = $pages;
					}
				}
				
				for($i = $start_at; $i <= $end; $i ++)
					$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="current"' : ' ') . '>' . $i . '</a>';
			}
		} else {
			for($i = 1; $i <= $pages; $i ++)
				$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="current"' : ' ') . '>' . $i . '</a>';
		}
		
		if ($currentPage < $pages)
			$paging .= '<a href="' . $frontUrl . '' . ($currentPage + 1) . '' . $backUrl . '"  title="后一页" class="next_page">往后</a>';
		
		$toPages = $currentPage*$rowsPerPage;
		if($toPages > $allRows) {
			$toPages = $allRows;
		}	
			
		return $paging; //. '</div><p class="page_items">'.((($currentPage-1)*$rowsPerPage)+1).' - '.$toPages.' '.$langArray['of'].' '.$allRows.' '.$langArray['themes'].'</p></div>';
	}

} else {
	
	function paging($frontUrl, $backUrl, $currentPage, $rowsPerPage, $allRows) {
		global $langArray;
		
		if ($allRows <= $rowsPerPage && ! check_debug ()) {
			return '';
		}
		
		$paging = '<div class="pagination clr"><div class="page_numbers clr">';
		
		$pages = ceil ( $allRows / $rowsPerPage );
		

		if ($currentPage > 1)
			$paging .= '<a href="' . $frontUrl . '' . ($currentPage - 1) . '' . $backUrl . '" title="前一页" class="previous_page"><i class="fa fa-chevron-left"></i></a>';
		
		if ($pages > 10) {
			if ($currentPage == 1) {
				for($i = 1; $i <= 10; $i ++)
					$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="current"' : ' ') . '>' . $i . '</a>';
			} elseif ($currentPage == $pages) {
				for($i = $pages - 10; $i <= $pages; $i ++)
					$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="current"' : ' ') . '>' . $i . '</a>';
			} else {
				$start = $currentPage - 5;
				
				if ($start < 1) {
					$start_at = 1;
					$end = (- 1) * $start + 5 + $currentPage;
				} else {
					$start_at = $start;
					$end = $start_at + 10;
					if ($end > $pages) {
						$start_at = $start_at - ($end - $pages);
						$end = $pages;
					}
				}
				
				for($i = $start_at; $i <= $end; $i ++)
					$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="selected"' : ' ') . '>' . $i . '</a>';
			}
		} else {
			for($i = 1; $i <= $pages; $i ++)
				$paging .= '<a href="' . $frontUrl . '' . $i . '' . $backUrl . '" ' . (($i == $currentPage) ? ' class="selected"' : ' ') . '>' . $i . '</a>';
		}
		
		if ($currentPage < $pages)
			$paging .= '<a href="' . $frontUrl . '' . ($currentPage + 1) . '' . $backUrl . '"  title="后一页" class="next_page"><i class="fa fa-chevron-right"></i></a>';
		
		$toPages = $currentPage*$rowsPerPage;
		if($toPages > $allRows) {
			$toPages = $allRows;
		}	
			
		return $paging . '</div><div class="page_tips">'.((($currentPage-1)*$rowsPerPage)+1).' - '.$toPages.' '.$langArray['of'].' '.$allRows.'</div></div>';
	}
	
}
	
//剩余时间函数
function timeLeft($integer) {
	$days = '';
	$weeks = '';
	$hours = '';
	$minutes = '';
	$return = '';
	
	$seconds = $integer;
	if ($seconds / 60 >= 1) {
		$minutes = floor ( $seconds / 60 );
		if ($minutes / 60 >= 1) { # Hours
			$hours = floor ( $minutes / 60 );
			if ($hours / 24 >= 1) { #days
				$days = floor ( $hours / 24 );
				if ($days / 7 >= 1) { #weeks
					$weeks = floor ( $days / 7 );
					if ($weeks >= 2)
						return "$weeks weeks";
					else
						return "$weeks week";
				} #end of weeks
				$days = $days - (floor ( $days / 7 )) * 7;
				//          if ($weeks>=1 && $days >=1) $return="$return, ";
				if ($days >= 2)
					return "$days days";
				if ($days == 1)
					return "$days day";
			} #end of days
			$hours = $hours - (floor ( $hours / 24 )) * 24;
			//        if ($days>=1 && $hours >=1) $return="$return, ";
			if ($hours >= 2)
				$return = "$hours hours";
			if ($hours == 1)
				$return = "$hours hour";
		} #end of Hours
		$minutes = $minutes - (floor ( $minutes / 60 )) * 60;
		//      if ($hours>=1 && $minutes >=1) $return="$return, ";
		if ($minutes >= 2)
			return "$return $minutes minutes";
		if ($minutes == 1)
			return "$return $minutes minute";
	} #end of minutes
	$seconds = $integer - (floor ( $integer / 60 )) * 60;
	//    if ($minutes>=1 && $seconds >=1) $return="$return, ";
	if ($seconds >= 2)
		return "$seconds seconds";
	if ($seconds == 1)
		return "$seconds second";
	
	return "0 seconds";
}

function yearsOld($birthday) {
	if (($birthday = strtotime ( $birthday )) === false) {
		return false;
	}
	for($i = 0; strtotime ( "-$i year" ) > $birthday; ++ $i);
	return $i - 1;
}

function getSignByDate($day, $month) {
	if (($month == 1 && $day >= 21) or ($month == 2 && 19 >= $day)) {
		$sign = 1;
	} elseif (($month == 2 && 20 <= $day) or ($month == 3 && $day <= 20)) {
		$sign = 2;
	} elseif (($month == 3 && 21 <= $day) or ($month == 4 && $day <= 20)) {
		$sign = 3;
	} elseif (($month == 4 && 21 <= $day) or ($month == 5 && $day <= 20)) {
		$sign = 4;
	} elseif (($month == 5 && 21 <= $day) or ($month == 6 && $day <= 21)) {
		$sign = 5;
	} elseif (($month == 6 && 22 <= $day) or ($month == 7 && $day <= 22)) {
		$sign = 6;
	} elseif (($month == 7 && 23 <= $day) or ($month == 8 && $day <= 22)) {
		$sign = 7;
	} elseif (($month == 8 && 23 <= $day) or ($month == 9 && $day <= 22)) {
		$sign = 8;
	} elseif (($month == 9 && 23 <= $day) or ($month == 10 && $day <= 23)) {
		$sign = 9;
	} elseif (($month == 10 && 24 <= $day) or ($month == 11 && $day <= 22)) {
		$sign = 10;
	} elseif (($month == 11 && 23 <= $day) or ($month == 12 && $day <= 21)) {
		$sign = 11;
	} elseif (($month == 12 && 22 <= $day) or ($month == 1 && $day <= 20)) {
		$sign = 12;
	}
	
	return $sign;
}

function deleteFile($path) {
	if (is_file ( $path )) {
		return unlink ( $path );
	} else {
		return false;
	}
}

function cyr2lat($input) {
	$cyr = array (
			"Щ", 
			"Ш", 
			"Ч", 
			"Ц", 
			"Ю", 
			"Я", 
			"Ж", 
			"А", 
			"Б", 
			"В", 
			"Г", 
			"Д", 
			"Е", 
			"З", 
			"И", 
			"Й", 
			"К", 
			"Л", 
			"М", 
			"Н", 
			"О", 
			"П", 
			"Р", 
			"С", 
			"Т", 
			"У", 
			"Ф", 
			"Х", 
			"Ь", 
			"Ъ", 
			"щ", 
			"ш", 
			"ч", 
			"ц", 
			"ю", 
			"я", 
			"ж", 
			"а", 
			"б", 
			"в", 
			"г", 
			"д", 
			"е", 
			"з", 
			"и", 
			"й", 
			"к", 
			"л", 
			"м", 
			"н", 
			"о", 
			"п", 
			"р", 
			"с", 
			"т", 
			"у", 
			"ф", 
			"х", 
			"ь", 
			"ъ", 
			' ' 
	);
	
	$lat = array (
		
		"Sht", 
		"Sh", 
		"Ch", 
		"Tz", 
		"Yu", 
		"Ya", 
		"Zh", 
		"A", 
		"B", 
		"V", 
		"G", 
		"D", 
		"E", 
		"Z", 
		"I", 
		"J", 
		"K", 
		"L", 
		"M", 
		"N", 
		"O", 
		"P", 
		"R", 
		"S", 
		"T", 
		"U", 
		"F", 
		"H", 
		"Io", 
		"Y", 
		"sht", 
		"sh", 
		"ch", 
		"tz", 
		"yu", 
		"ya", 
		"zh", 
		"a", 
		"b", 
		"v", 
		"g", 
		"d", 
		"e", 
		"z", 
		"i", 
		"j", 
		"k", 
		"l", 
		"m", 
		"n", 
		"o", 
		"p", 
		"r", 
		"s", 
		"t", 
		"u", 
		"f", 
		"h", 
		"io", 
		"y", 
		"_" 
	);
	
	$cyrCount = count ( $cyr );
	
	for($i = 0; $i < $cyrCount; $i ++) {
		$current_cyr = $cyr [$i];
		$current_lat = $lat [$i];
		$input = str_replace ( $current_cyr, $current_lat, $input );
	}
	
	return $input;
}

//更改编码
function cp1251_to_utf8($s) {
	$t = '';
	$c209 = chr ( 209 );
	$c208 = chr ( 208 );
	$c129 = chr ( 129 );
	
	for($i = 0; $i < strlen ( $s ); $i ++) {
		$c = ord ( $s [$i] );
		if ($c >= 192 and $c <= 239)
			$t .= $c208 . chr ( $c - 48 );
		elseif ($c > 239)
			$t .= $c209 . chr ( $c - 112 );
		elseif ($c == 184)
			$t .= $c209 . $c209;
		elseif ($c == 168)
			$t .= $c208 . $c129;
		else
			$t .= $s [$i];
	}
	
	return $t;
}

function utf8_to_cp1251($s) {
	$out = '';
	$byte2 = false;
	for($c = 0; $c < strlen ( $s ); $c ++) {
		$i = ord ( $s [$c] );
		
		if ($i <= 127)
			$out .= $s [$c];
		if ($byte2) {
			$new_c2 = ($c1 & 3) * 64 + ($i & 63);
			$new_c1 = ($c1 >> 2) & 5;
			$new_i = $new_c1 * 256 + $new_c2;
			
			if ($new_i == 1025) {
				$out_i = 168;
			} else {
				if ($new_i == 1105) {
					$out_i = 184;
				} else {
					$out_i = $new_i - 848;
				}
			}
			
			$out .= chr ( $out_i );
			$byte2 = false;
		}
		if (($i >> 5) == 6) {
			$c1 = $i;
			$byte2 = true;
		}
	}
	
	return $out;
}


//表情函数
function replaceEmoticons($text) {
	global $config;
	
	$codes = array_keys($config['emoticons']);
	$images = array_values($config['emoticons']);
	
	//设置表情
	$tmp = array ();
	foreach ( $images as $k => $v ) {
		$tmp [$k] = '<img src="'.DATA_SERVER.'/smileys/' . $v . '" />';
	}
	
	return str_ireplace ( $codes, $tmp, $text );
}

?>