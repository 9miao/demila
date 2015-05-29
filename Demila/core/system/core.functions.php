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


/*
 * 自动包含类文件
 */
function __autoload($class_name) {
	if (class_exists ( $class_name )) {
		return true;
	}
	
	$file = ENGINE_PATH . "classes/" . $class_name . ".class.php";
	if (file_exists ( $file )) {
		require_once ($file);
	} elseif (isset ( $_GET ['module'] ) && file_exists ( ROOT_PATH . "/apps/" . $_GET ['module'] . "/models/" . $class_name . ".class.php" )) {
		require_once ROOT_PATH . "/apps/" . $_GET ['module'] . "/models/" . $class_name . ".class.php";
	} elseif (isset ( $_GET ['module'] ) && isAdministration ( $_GET ['module'] ) && isset ( $_GET ['m'] ) && file_exists ( ROOT_PATH . "/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php" )) {
		//自动包含administration
		require_once ROOT_PATH . "/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php";
	} elseif (isset ( $_GET ['module'] ) && isAdministration ( $_GET ['module'] ) && isset ( $_GET ['m'] ) && file_exists ( ROOT_PATH . "/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php" )) {
		require_once ROOT_PATH . "/apps/" . $_GET ['m'] . "/models/" . $class_name . ".class.php";
	} /*else {
				require_once ROOT_PATH . "classes/" . $class_name . ".class.php";
	}*/
	
	return true;
}

function check_email($address) {
	return (preg_match ( '/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+' . '@' . '([-0-9A-Z]+\.)+' . '([0-9A-Z]){2,4}$/i', trim ( $address ) ));
}

/*
 * 返回执行时间
 */
function execute_time() {
	global $start_time;
	return (microtime ( true ) - $start_time);
}

/*
 * smarty引用赋值
 */
function abr($var, $value) {
	global $smarty;
	$smarty->assign_by_ref ( $var, $value );
}


/*
 * 替换文本中的变量
 */
function langMessageReplace($message, $vars) {
	if (preg_match_all ( '/\{\$(?P<named>[0-9A-Z_\-]*)\}/simx', $message, $result, PREG_PATTERN_ORDER )) {
		foreach ( $result ['named'] as $v ) {
			if (isset ( $vars [$v] )) {
				//echo $v . "<br/>";
				$message = str_ireplace ( '{$' . $v . '}', $vars [$v], $message );
			}
		}
	}
	unset ( $vars );
	return $message;
}


/*
 * 获取URL变量
 */
function get_id($level) {
	global $config;
	
	if($config['use_language']) {
		$level = $level + 1;
	}
	if (! isset ( $_GET ['array_url'] [$level] )) {
		return false;
	}

    if (strstr ( $_GET ['array_url'] [$level], "." )) {
        $all = explode ( ".", $_GET ['array_url'] [$level] );
        return $all;
    } else {
        $all [0] = $_GET ['array_url'] [$level];
    }
    return $all [0];
}
/*
 * url过滤
 */
function get_new_str($str){
    $str=str_replace("'", "",  $str);
    $str=str_replace('"', "",  $str);
    $str=str_replace(",", "",  $str);
    $str=str_replace("(", "",  $str);
    $str=str_replace(")", "",  $str);
    $str=str_replace("`", "",  $str);
    $str=str_replace(" ", "",  $str);
    $str=str_replace("<", "",  $str);
    $str=str_replace(">", "",  $str);
    $str=str_replace("and", "",  $str);
    $str=str_replace("AND", "",  $str);
    $str=str_replace("delete", "",  $str);
    $str=str_replace("DELETE", "",  $str);
    $str=str_replace("update", "",  $str);
    $str=str_replace("UPDATE", "",  $str);
    $str=str_replace("WHERE", "",  $str);
    $str=str_replace("where", "",  $str);
    $str=str_replace("INSERT", "",  $str);
    $str=str_replace("insert", "",  $str);
    $str=str_replace("CREATE", "",  $str);
    $str=str_replace("create", "",  $str);
    $str=str_replace("modify", "",  $str);
    $str=str_replace("MODIFY", "",  $str);
    $str=str_replace("DROP", "",  $str);
    $str=str_replace("drop", "",  $str);
    $str=str_replace("ALTER", "",  $str);
    $str=str_replace("alter", "",  $str);
    $str=str_replace("select", "",  $str);
    $str=str_replace("SELECT", "",  $str);
    return $str;
}

/*
 * 创建文本编辑器
 */
function createTextAreaEditor($params, &$smarty) {
	global $config;
	
	require_once $config ['data_server_path'] . '/js/fckeditor/fckeditor.php';
	
	$params ['name'] = str_replace ( "\\'", '', $params ['name'] );
	
	$oFCKeditor = new FCKeditor ( $params ['name'] );
	$oFCKeditor->BasePath = $config ['data_server'] . '/js/fckeditor/';
	$oFCKeditor->Width = $params ['width'];
	$oFCKeditor->Height = $params ['height'];
	$oFCKeditor->Value = $params ['value'];
	$oFCKeditor->ToolbarSet = 'e7Toolbar';
	$oFCKeditor->AutoDetectPasteFromWord = true ;	
	$oFCKeditor->Create ();
}

function remove_whitespace($str) {
	
	$str = str_replace ( ' ', '+', $str );
	$str = str_replace ( '%20', '+', $str );
	$str = str_replace ( '%', '', $str );
	$str = str_replace ( '/', '+', $str );
	return $str;
}

//刷新页面
function refresh($url = '', $message = '', $type = 'succes') {
	global $sitemapClass;

	if ($url == '') {
		$_SERVER ['REQUEST_URI'] = str_ireplace ( "//", "/", $_SERVER ['REQUEST_URI'] );
		$url = 'http://' . $_SERVER ["SERVER_NAME"] . '' . $_SERVER ['REQUEST_URI'];
	}

	if ($message != '') {
		$_SESSION ['temp'] ['message_title'] = $message;
	}

	$_SESSION ['temp'] ['message_type'] = $type;

	//如果定义了站点地图的类
	if(isset($sitemapClass) && $_GET['module'] == 'admin' && (isset($_POST['add']) || isset($_POST['edit']))) {
		$sitemapClass->regenerateSiteMap();
	}

    echo "<script language='javascript'
type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";
//    header ("Location: $url");
	die ();
}

//重定向到登录
function login_redirect($url, $fromUrl = '', $message='') {
	if ($fromUrl == '')
		$_SESSION ['redirectUrl'] = '/' . @$_GET ['url'];
	else
		$_SESSION ['redirectUrl'] = $fromUrl;
	
	if ($message != '') {
		$_SESSION ['temp'] ['message_title'] = $message;
		$_SESSION ['temp'] ['message_type'] = 'error';
	}
		
	header ( "Location: $url" );
	die ();
}

function getRefreshMessage() {
	
	if (isset ( $_SESSION ['temp'] ['message_title'] )) {
		$message['title'] = $_SESSION ['temp'] ['message_title'];
		unset ( $_SESSION ['temp'] ['message_title'] );
		
		if (isset ( $_SESSION ['temp'] ['message_text'] )) {
			$message['text'] = $_SESSION ['temp'] ['message_text'];
			unset ( $_SESSION ['temp'] ['message_text'] );
		}
		else {
			$message['text'] = '';
		}
				
		if (isset ( $_SESSION ['temp'] ['message_type'] )) {
			$message['type'] = $_SESSION ['temp'] ['message_type'];
			unset ( $_SESSION ['temp'] ['message_type'] );
		}
		else {
			$message['type'] = '';
		}
				
		return $message;
	}
	
	return false;
}

function truncate($str, $length = 10, $trailing = '...') {
	
	// 去除尾部字符
	$length -= mb_strlen ( $trailing );
	if (mb_strlen ( $str ) > $length) {
		// 字符串超长，截断并在尾部添加点号
		return mb_substr ( $str, 0, $length ) . $trailing;
	} else {
		// 字符串已经够短，返回字符串
		$res = $str;
	}
	return $res;
}



function addErrorMessage($title, $msg = "", $type = "notice") {
	global $smarty;
	
/*	$msg = $smarty->get_template_vars ( 'errorMessage' ) . '
	<div class="errorMessage errorMessage_' . $type . '">
		<div class="close"></div>
		<div class="errorContent">
			<div class="em-title">' . $title . '</div>
			' . $msg . '
		</div>
	</div>';*/
		
	switch($type) {
		case 'notice':
			$class = 'warning';
			break;
		case 'error':
			$class = 'error';
			break;
		case 'info':
			$class = 'info';
			break;

		default:
			$class = 'success';
			break;
	}
	
		$msg = $smarty->get_template_vars ( 'errorMessage' ) . '
			<div class="box box-'.$class.'">'.$title.' '.$msg.'</div>';
		
	
	$smarty->assign_by_ref ( "errorMessage", $msg );
	
	return true;
}

function check_login($type = 'user') {
	global $_SESSION, $langArray;
	
	if (! isset ( $_SESSION [$type] )) {
		refresh ( '/sign_in/' );
	}
	
	return true;
}

function check_login_bool($type = 'user') {
	global $_SESSION;
	
	if (! isset ( $_SESSION [$type] )) {
		return false;
	}
	return true;
}

/*
 * 检查管理员的session
 */
function admin_login() {
	global $config, $languageURL;
	
	if (! isset ( $_SESSION ['user'] ['is_admin'] )) {
		refresh ( "/" . $languageURL . adminURL . "/login/?" . __FUNCTION__ );
		die ( 'Do not access !!!' );
	}
	
	return true;
}

function admin_login_bool() {
	global $_SESSION;
	
	if (! isset ( $_SESSION ['user'] ['is_admin'] )) {
		return false;
	}
	return true;
}


function url($url) {
	$url = strip_tags ( $url );
	$url = trim ( $url );
	$url = preg_replace ( '%[.,:\'"/\\\\[\]{}\%\-_!?]%simx', ' ', $url );
	$url = str_ireplace ( " ", "-", $url );
	
	return $url;
}

function isAdministration($module) {
	if ($module == adminURL) {
		return true;
	} else {
		return false;
	}
}

function recursive_mkdir($folder) {
	$folder = explode ( DIRECTORY_SEPARATOR, $folder );
	$mkfolder = '';
	for($i = 0; isset ( $folder [$i] ); $i ++) {
		if ($folder [$i] != "") {

			$mkfolder .= $folder [$i];
			if (!@is_dir ( $mkfolder )) {
				@mkdir ( $mkfolder, 0775 );
			}
			$mkfolder .= DIRECTORY_SEPARATOR;
		} else {
			$mkfolder .= DIRECTORY_SEPARATOR;
		}
	}
	return true;
}

function recursive_rmdir($dir, $DeleteMe = true) {
	if (! $dh = @opendir ( $dir )) {
		return;
	}
	
	while ( false !== ($obj = readdir ( $dh )) ) {
		if ($obj == '.' || $obj == '..') {
			continue;
		}
		
		if (! @unlink ( $dir . '/' . $obj )) {
			recursive_rmdir ( $dir . '/' . $obj, true );
		}
	}
	
	closedir ( $dh );
	if ($DeleteMe) {
		@rmdir ( $dir );
	}
}
//删除文件夹
function deldir($dir) {
    //先删除目录下的文件：
    $dh=opendir($dir);
    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    //删除当前文件夹：
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

function check_debug() {
	global $config;
	
	if (in_array ( $_SERVER ['REMOTE_ADDR'], $config ['debug_ips'] ) && $config ['debug'] === true) {		
		return true;
	}
	return false;
}

function add_debug($val) {
	global $debug;
	$debug .= $val . "<BR /><BR />";
	return true;
}

function recursiveChmod($path, $filePerm=0644, $dirPerm=0755)
{
    // Check if the path exists
    if(!file_exists($path))
    {
        return(FALSE);
    }
    // See whether this is a file
    if(is_file($path))
    {
        // Chmod the file with our given filepermissions
        chmod($path, $filePerm);
        // If this is a directory...
    } elseif(is_dir($path)) {
        // Then get an array of the contents
        $foldersAndFiles = scandir($path);
        // Remove "." and ".." from the list
        $entries = array_slice($foldersAndFiles, 2);
        // Parse every result...
        foreach($entries as $entry)
        {
            // And call this function again recursively, with the same permissions
            recursiveChmod($path."/".$entry, $filePerm, $dirPerm);
        }
        // When we are done with the contents of the directory, we chmod the directory itself
        chmod($path, $dirPerm);
    }
    // Everything seemed to work out well, return TRUE
    return(TRUE);
}

function getFile($url,$save_dir='',$filename='',$type=0){
    if(trim($url)==''){
        return false;
    }
    if(trim($save_dir)==''){
        $save_dir='./';
    }
    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return false;
    }
    //获取远程文件所采用的方法
    if($type){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $content=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $content=ob_get_contents();
        ob_end_clean();
    }
    $size=strlen($content);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$content);
    fclose($fp2);
    unset($content,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename);
}
?>