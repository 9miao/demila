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
_setLayout('blank');
	
	$commentID = get_id(2);
	
	$commentsClass = new comments();
	
	$comment = $commentsClass->get($commentID);
	if(!is_array($comment)) {
		addErrorMessage($langArray['wrong_comment'], '', 'error');
	}
	else {
		abr('show_form', 'yes');
		abr('comment', $comment);
	}

?>