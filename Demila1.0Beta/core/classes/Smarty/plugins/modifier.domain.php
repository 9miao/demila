<?
function smarty_modifier_domain($url) {
	$data = parse_url($url);
	return $data['host'];
}

?>