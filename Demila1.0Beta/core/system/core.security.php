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


/**
 * 禁止全站脚本抓取
 */
function removeXSS($val) {
	//
	// 去掉所有不可打印的字符 CR(0a)和LF(0b)以及TAB(9)除外
	// 组织类似<java\0script>这样的更改
	// 
	$val = preg_replace ( '/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val );
	
	// 
	// 阻止类似<IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>的更改
	$search = 'abcdefghijklmnopqrstuvwxyz';
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$search .= '1234567890!@#$%^&*()';
	$search .= '~`";:?+/={}[]-_|\'\\';
	for($i = 0; $i < strlen ( $search ); $i ++) {
		// ;? 匹配;,可选
		// 0{0,7} 匹配0, 可选，最多8个
		

		// &#x0040 @ 十六进制值搜索
		$val = preg_replace ( '/(&#[x|X]0{0,8}' . dechex ( ord ( $search [$i] ) ) . ';?)/i', $search [$i], $val ); //注释;
		// &#00064 @ 0{0,7} 匹配'0'为零七次
		$val = preg_replace ( '/(&#0{0,8}' . ord ( $search [$i] ) . ';?)/', $search [$i], $val ); // 注释;
	}
	
	// 注释
	$ra1 = Array (
		
		'javascript', 
		'vbscript', 
		'expression', 
		'applet', 
		'meta', 
		'xml', 
		'blink', 
		//'link', 
		'style', 
		'script', 
		'embed', 
		'object', 
		'iframe', 
		'frame', 
		'frameset', 
		'ilayer', 
		'layer', 
		'bgsound', 
		'title', 
		'base' 
	);
	$ra2 = Array (
		
		'onabort', 
		'onactivate', 
		'onafterprint', 
		'onafterupdate', 
		'onbeforeactivate', 
		'onbeforecopy', 
		'onbeforecut', 
		'onbeforedeactivate', 
		'onbeforeeditfocus', 
		'onbeforepaste', 
		'onbeforeprint', 
		'onbeforeunload', 
		'onbeforeupdate', 
		'onblur', 
		'onbounce', 
		'oncellchange', 
		'onchange', 
		'onclick', 
		'oncontextmenu', 
		'oncontrolselect', 
		'oncopy', 
		'oncut', 
		'ondataavailable', 
		'ondatasetchanged', 
		'ondatasetcomplete', 
		'ondblclick', 
		'ondeactivate', 
		'ondrag', 
		'ondragend', 
		'ondragenter', 
		'ondragleave', 
		'ondragover', 
		'ondragstart', 
		'ondrop', 
		'onerror', 
		'onerrorupdate', 
		'onfilterchange', 
		'onfinish', 
		'onfocus', 
		'onfocusin', 
		'onfocusout', 
		'onhelp', 
		'onkeydown', 
		'onkeypress', 
		'onkeyup', 
		'onlayoutcomplete', 
		'onload', 
		'onlosecapture', 
		'onmousedown', 
		'onmouseenter', 
		'onmouseleave', 
		'onmousemove', 
		'onmouseout', 
		'onmouseover', 
		'onmouseup', 
		'onmousewheel', 
		'onmove', 
		'onmoveend', 
		'onmovestart', 
		'onpaste', 
		'onpropertychange', 
		'onreadystatechange', 
		'onreset', 
		'onresize', 
		'onresizeend', 
		'onresizestart', 
		'onrowenter', 
		'onrowexit', 
		'onrowsdelete', 
		'onrowsinserted', 
		'onscroll', 
		'onselect', 
		'onselectionchange', 
		'onselectstart', 
		'onstart', 
		'onstop', 
		'onsubmit', 
		'onunload' 
	);
	$ra = array_merge ( $ra1, $ra2 );
	
	$found = true; // 持续替换直至前一个
	while ( $found == true ) {
		$val_before = $val;
		for($i = 0; $i < sizeof ( $ra ); $i ++) {
			$pattern = '/';
			for($j = 0; $j < strlen ( $ra [$i] ); $j ++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
					$pattern .= '|(&#0{0,8}([9][10][13]);?)?';
					$pattern .= ')?';
				}
				$pattern .= $ra [$i] [$j];
			}
			$pattern .= '/i';
			$replacement = substr ( $ra [$i], 0, 2 ) . '<x>' . substr ( $ra [$i], 2 ); // add in <> to nerf the tag
			$val = preg_replace ( $pattern, $replacement, $val ); // filter out the hex tags
			if ($val_before == $val) {
				// 无替换则退出循环
				$found = false;
			}
		}
	}
	return $val;
}


/**
 * URL编码
 *
 * @param string $string
 * @return string
 */
function urlsafe_b64encode($string) {
	//$rand = rand(1,9);
	//$string = $rand . $string . $rand;
	//return $string;
	$data = base64_encode ( $string );
	$data = str_replace ( array (
		
		'+', 
		'/', 
		'=' 
	), array (
		
		'-', 
		'_', 
		'' 
	), $data );
	return $data;
}

/**
 * 解码URL
 *
 * @param string $string
 * @return string
 */
function urlsafe_b64decode($string) {
	//return $string;
	$data = str_replace ( array (
		
		'-', 
		'_' 
	), array (
		
		'+', 
		'/' 
	), $string );
	$mod4 = strlen ( $data ) % 4;
	if ($mod4) {
		$data .= substr ( '====', $mod4 );
	}
	$data = base64_decode ( $data );
	// return substr($data,1,-1);
	return $data;
}

/**
 * 跳出字符
 *
 * @param string $value
 * @return string
 */
function strip_html_tags($text) {
	$text = preg_replace ( array (
		
		// 去除可见内容
		'@<head[^>]*?>.*?</head>@siu', 
		'@<style[^>]*?>.*?</style>@siu', 
		'@<script[^>]*?.*?</script>@siu', 
		'@<object[^>]*?.*?</object>@siu', 
		'@<embed[^>]*?.*?</embed>@siu', 
		'@<applet[^>]*?.*?</applet>@siu', 
		'@<noframes[^>]*?.*?</noframes>@siu', 
		'@<noscript[^>]*?.*?</noscript>@siu', 
		'@<noembed[^>]*?.*?</noembed>@siu', 
		// 区块前后添加换行符
		'@</?((address)|(blockquote)|(center)|(del)|(marquee)|(map))@iu', 
		'@</?((div)|(ins)|(isindex)|(pre))@iu', 
		'@</?((dir)|(dl)|(dt)|(dd)|(menu))@iu', 
		'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu', 
		'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu', 
		'@</?((frameset)|(frame)|(iframe))@iu' 
	), array (
		
		' ', 
		' ', 
		' ', 
		' ', 
		' ', 
		' ', 
		' ', 
		' ', 
		' ', 
		"\n\$0", 
		"\n\$0", 
		"\n\$0", 
		"\n\$0", 
		"\n\$0", 
		"\n\$0", 
		"\n\$0", 
		"\n\$0" 
	), $text );
	//    return strip_tags( $text );
	return $text;
}

function sql_quote($value, $toStrip = true) {
	
	$value = str_replace('<x>', '', $value);
	
	if ($toStrip) {
		$value = strip_html_tags ( $value );
	}
	if (get_magic_quotes_gpc ()) {
		$value = stripslashes ( $value );
	}
	//检查该函数是否存在
	$value = addslashes ( $value );
	
	return $value;
}

/*
 * 检查用户(Agent)是否机器人
 */
function isBot() {
	
	$botlist = array (
		
		"Teoma", 
		"diri", 
		"alexa", 
		"froogle", 
		"Gigabot", 
		"inktomi", 
		"looksmart", 
		"URL_Spider_SQL", 
		"Firefly", 
		"NationalDirectory", 
		"Ask Jeeves", 
		"TECNOSEEK", 
		"InfoSeek", 
		"WebFindBot", 
		"girafabot", 
		"crawler", 
		"www.galaxy.com", 
		"Googlebot", 
		"Scooter", 
		"Slurp", 
		"msnbot", 
		"appie", 
		"FAST", 
		"WebBug", 
		"Spade", 
		"ZyBorg", 
		"rabaz", 
		"Baiduspider", 
		"Feedfetcher-Google", 
		"TechnoratiSnoop", 
		"Rankivabot", 
		"Mediapartners-Google", 
		"Sogou web spider", 
		"WebAlta Crawler" 
	);
	
	foreach ( $botlist as $bot ) {
		if (ereg ( $bot, $_SERVER ['HTTP_USER_AGENT'] )) {
			return true;
		}
	}
	
	return false;
}

?>