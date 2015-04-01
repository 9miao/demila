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
_setLayout('newsletter');

	$bulletinID = get_id(2);
	
	$bulletinClass = new bulletin();
	
	$bulletin = $bulletinClass->get($bulletinID);
	if(!is_array($bulletin)) {
		refresh('/'.$languageURL);
	}
	abr('bulletin', $bulletin);
	
	$template = $bulletinClass->getTemplate();
	abr('bulletin', langMessageReplace($template, array(
    'DOMAIN' => $config['domain'],
    'BULLETINID' => $bulletinID,
    'EMAIL' => 'noemail',
    'CONTENT' => $bulletin['text']
  )));

?>