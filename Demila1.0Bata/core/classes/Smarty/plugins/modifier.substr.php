<?php 
/** 
 * Smarty plugin 
 * @package Smarty 
 * @subpackage plugins 
 * @author Manuel Polacek / Hitflip 
 */ 


/** 
 * Smarty regex_replace modifier plugin 
 * 
 * Type:     modifier<br> 
 * Name:     substring 
 * Purpose:  substring like in php 
 * @param string 
 * @return string 
 */ 
function smarty_modifier_substr($sString, $dFirst = 0, $dLast = 0) { 
    if($dLast == 0) { 
       return substr($sString, $dFirst); 
    } else { 
       return substr($sString, $dFirst, $dLast); 
    } 
} 

?>