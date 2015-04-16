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
_setTitle($langArray['downloads_setTitle']);

if(!check_login_bool()) {
	$_SESSION['temp']['golink'] = '/'.$languageURL.'download/';
	refresh('/'.$languageURL.'sign_in/');
}

	require_once ROOT_PATH.'/apps/items/models/orders.class.php';
	$ordersClass = new orders();

#下载作品
	$itemID = get_id(2);
	if(is_numeric($itemID)) {
		
		require_once ROOT_PATH.'/apps/items/models/items.class.php';
		$itemsClass = new items();
		
		$item = $itemsClass->get($itemID);
		if(!is_array($item) || (check_login_bool() && $item['status'] == 'unapproved' && $item['user_id'] != $_SESSION['user']['user_id']) || $item['status'] == 'queue') {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/error");
		}
		
		if(isset($_POST['rating'])) {
			$_GET['rating'] = $_POST['rating'];
		}
			
		if(isset($_GET['rating'])) {
			if(!isset($_GET['rating']) || !is_numeric($_GET['rating']) || $_GET['rating'] > 5) {
				$_GET['rating'] = 5;
			}
			elseif($_GET['rating'] < 1) {
				$_GET['rating'] = 1;
			}
			
			$item = $itemsClass->rate($itemID, $_GET['rating']);
			
			$stars = '';
			for($i=1;$i<6;$i++) {
				if($item['rating'] >= $i) {
					$stars .= '<img src="/static/img/star-on.png" alt="" />';
				}
				else {
					$stars .= '<img src="/static/img/star-off.png" alt="" />';
				}
			}
			die('
				jQuery("#stars_div_'.$itemID.'").html(\''.$stars.'\');
			');
		}
		elseif(isset($_GET['certificate'])) {
			if($ordersClass->isBuyed($item['id'])) {
				if($ordersClass->row['extended'] == 'true') {
					$licence = $langArray['one_extended_licence'];
				}
				else {
					$licence = $langArray['one_regular_licence'];
				}
				$usersClass = new users();
				$user = $usersClass->get($item['user_id']);
                //许可证号
                #  作者后4位
                $auth=substr($user['username'],-4);
                #买家后4位
                $buy=substr($_SESSION['user']['username'],-4)
                ;
                $pdfcontent=langMessageReplace($langArray['licence_text'], array(
                    'licence_type'=>'共享许可',
                    'DOMAIN' => $config['domain'],
                    'LICENCE' => $licence,
                    'USERNAME' => $user['username'],
                    'nickname' => $_SESSION['user']['username'],
                    'ITEMNAME' => $item['name'],
                    'ITEMID' => $item['id'],
                    'LANGUAGEURL' => $languageURL,
                    'ORDERID' =>$auth.'-'.$buy.'-'.$ordersClass->row['id']
                ));
                require_once $config['system_core'].'classes/tcpdf/tcpdf.php';
                $pdf=new TCPDF();
                // 设置文档信息
                $pdf->SetCreator('Demila.org');
                $pdf->SetAuthor('Demila.org');
                $pdf->SetSubject('TCPDF Tutorial');
                $pdf->SetKeywords('TCPDF, PDF, PHP');

                // 设置页眉和页脚字体
                $pdf->setHeaderFont(Array('stsongstdlight', '', '10'));
                $pdf->setFooterFont(Array('helvetica', '', '8'));

                // 设置默认等宽字体
                $pdf->SetDefaultMonospacedFont('courier');
                // 设置间距
                $pdf->SetMargins(15, 26, 15);
                $pdf->SetHeaderMargin(5);
                $pdf->SetFooterMargin(10);
                //设置字体
                $pdf->SetFont('stsongstdlight', '', 14);
                $pdf->AddPage();
                $pdf->Write(0,$pdfcontent,'', 0, 'L', true, 0, false, false, 0);
                $pdf->Image($config['root_path']."static/img/logo.png",'13','16',50, 16,'PNG', '', '', true, 150, '', false, false, 0, false, false, true);
                //输出PDF
                $pdf->Output('item-licence-'.$item['id'].'.pdf', 'D');
                die();
			}
			else {
				refresh('/'.$languageURL.'download/', $langArray['error_certificate'], 'error');
			}
		}
		if($ordersClass->isBuyed($item['id']) || $item['free_file'] == 'true') {
			if(file_exists(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file'])) {				
				$fileInfo = pathinfo(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file']);
				
				$mimeTypes = array (
						'zip' => 'application/zip'
				);
				
				if(isset($mimeTypes[$fileInfo['extension']])) {
					header('Content-Type: '.$mimeTypes[$fileInfo['extension']]);
				} else {
					header('Content-Type: application/octet-stream');
				}
				
				header('Content-Disposition: attachment; filename="'.$item['main_file_name'].'"');
				header("Content-Length:".filesize(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file']));
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header("Content-Transfer-Encoding: binary");
				header('Expires: 0');
				header('Content-Description: '.$config['domain'].' Download');
				@ob_clean();
				@flush();
				readfile(DATA_SERVER_PATH.'/uploads/items/'.$item['id'].'/'.$item['main_file']) or die("ERROR!");
				die();
			}
			
		}
		else {
		header("HTTP/1.0 404 Not Found");
        header("Location: http://". DOMAIN ."/error");
		}
		
	}

#加载作品
	$items = $ordersClass->getAllBuyed(" `user_id` = '".intval($_SESSION['user']['user_id'])."' AND `paid` = 'true' ");
	abr('items', $items);
	
	
	$ratedItems = $itemsClass->getRates(str_replace('`id`', '`item_id`', $ordersClass->whereQuery));
	abr('ratedItems', $ratedItems);
	
#面包屑	
	abr('breadcrumb', '<a href="/'.$languageURL.'" title="">'.$langArray['home'].'</a> \ <a href="/'.$languageURL.'users/dashboard/" title="">'.$langArray['my_account'].'</a> \ <a href="/'.$languageURL.'users/downloads/" title="">'.$langArray['downloads'].'</a>');		
	

?>