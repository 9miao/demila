<?
function smarty_modifier_url(&$url) {
	
	//remove html tags
	$url = strip_tags($url);
	
	trim($url);
	$url = preg_replace ( '%[.,:\'"/\\\\[\]{}\%\-_!?]%simx', ' ', $url );
	$url = str_ireplace ( " ", "-", $url );
	return $url;
}
?>