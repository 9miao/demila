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
_setLayout('rss');

header('Content-type: application/xml; charset="utf-8"',true);

mb_internal_encoding("UTF-8");

	$name = '';	
	$link = '';
	$whereQuery = '';
	if(isset($_GET['category'])) {
		$whereQuery .= " AND `categories` LIKE '%,".intval($_GET['category']).",%' ";
		
		require_once ROOT_PATH.'/apps/categories/models/categories.class.php';
		$categoriesClass = new categories();
		
		$category = $categoriesClass->get($_GET['category']);
		
		$name = $category['name'];
		$link = '?category='.$_GET['category'];
	}
	if(isset($_GET['user'])) {
		$whereQuery .= " AND `user_id` = '".intval($_GET['user'])."' ";
		
		require_once ROOT_PATH.'/apps/users/models/users.class.php';
		$usersClass = new users();
		
		$user = $usersClass->get($_GET['user']);
		
		$name = $user['username'];
		if($link == '') {
			$link = '?user='.$_GET['user'];
		}
		else {
			$link .= '&user='.$_GET['user'];
		}	
	}
	
echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<rss version=\"2.0\">
<channel>
<title>".$config['domain']." - ".$name." RSS</title>
<link>http://".$config['domain']."/rss/".$link."</link>
<description></description>
";

#####加载	
	require_once ROOT_PATH.'/apps/items/models/items.class.php';
	$itemsClass = new items();
	
	$rows = $itemsClass->getAll(0, 20, " `status` = 'active' ".$whereQuery, "`datetime` DESC");
	if(is_array($rows)) {
		foreach($rows as $r) {
			echo "
				<item>
					<title><![CDATA[ ".$r['name']." ]]></title>
					<link>http://".$config['domain']."/".$languageURL."items/".$r['id']."</link>
					<description><![CDATA[
			";

			if($r['thumbnail'] != '') {
				echo "<a href=\"http://".$config['domain']."/".$languageURL."items/".$r['id']."\"><img src=\"".$config['data_server']."/uploads/items/".$r['id']."/".$r['thumbnail']."\" alt=\"\" border=\"0\" style=\"float:left; margin:0 10px 0 0;\" /></a>";
			}
			
			echo "<br />".mb_substr(strip_tags($r['description']), 0, 200)."";
			
			echo "]]></description>
		         <guid>http://".$config['domain']."/".$languageURL."items/".$r['id']."</guid>		          
		      </item>
			";
		}
	}

echo "
</channel>
</rss>
";		

?>