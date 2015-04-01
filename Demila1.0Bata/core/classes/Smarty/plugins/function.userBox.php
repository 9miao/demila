<?php
function smarty_function_userBox($params, &$smarty) {
	if (! isset ( $params ['type'] )) {
		return userBox ( $params ['data'] );
	}
	
	return $params ['type'] ( $params ['data'] );
}
?>