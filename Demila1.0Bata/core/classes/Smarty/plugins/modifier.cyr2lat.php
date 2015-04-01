<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty cyr2lat modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cyr2lat<br>
 * Purpose:  Cyr 2 Lat
 * @author   Deyan Spasov
 * @param string
 * @return string
 */
function smarty_modifier_cyr2lat($input) {
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
			" " 
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

?>
