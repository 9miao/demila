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

header("Content-type:text/html;charset=utf-8");
function check_email($address) {
	return (preg_match ( '/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+' . '@' . '([-0-9A-Z]+\.)+' . '([0-9A-Z]){2,4}$/i', trim ( $address ) ));
}
function check_phone($num){
	return (preg_match ( "/^((\(\d{3}\))|(\d{3}\-))?1[3,5,8]{1}\d{9}?$/", $num));
}
$isInstalled = false;
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/config/config.php')) {
	$isInstalled = true;
}
//如果“/config”目录下已存在“config.php”文件，视为系统已安装。

$languages = array();
if(function_exists('scandir')) {
	$buff = scandir($_SERVER['DOCUMENT_ROOT'].'/setup/lang/');
	if(is_array($buff)) {
		foreach($buff as $f) {
			if(is_file($_SERVER['DOCUMENT_ROOT'].'/setup/lang/'.$f)) {
				$f = basename($f, '.php');
				$languages[$f] = $f;
			} 
		}
	}	
}
//获取语言文件

if(isset($_POST['install']) && !$isInstalled) {
	$error = '';

	if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/cache/')), -4) != '0777') {
		$error .= '请给这个目录设置写权限(0777)： /core/data/cache/ (包括所有子目录)<br />';
	} else {
		if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/cache/session/')), -4) != '0777') {
			$error .= '请给这个目录设置写权限(0777)： /core/data/cache/session/<br />';
		}
		if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/cache/templates_cache/')), -4) != '0777') {
			$error .= '请给这个目录设置写权限(0777)： /core/data/cache/templates_cache/<br />';
		}
	}

	if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/logs/')), -4) != '0777') {
		$error .= '请给这个目录设置写权限(0777)： /core/data/logs/<br />';
	}

	if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/static/uploads/')), -4) != '0777') {
		$error .= '请给这个目录设置写权限(0777)： /static/uploads/ (包括所有子目录)<br />';
	}

	if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/config/')), -4) != '0777') {
		$error .= '请给这个目录设置写权限(0777)： /config/<br />';
	}

	if(!$error && (!file_exists($_SERVER['DOCUMENT_ROOT'].'/config/') || !is_dir($_SERVER['DOCUMENT_ROOT'].'/config/'))) {
		if(!@mkdir($_SERVER['DOCUMENT_ROOT'].'/config/', 0777, true)) {
			$error .= '目录 "' . $_SERVER['DOCUMENT_ROOT'] . '/config/" 无法创建<br />';
		}
	}
    

    //服务器配置检查
	if(!in_array('zlib', get_loaded_extensions())) {
		$error .= 'ZipArchive 类使用 Â» zlib函数<br />';
	}

	if(!function_exists('fopen')) {
		$error .= '"fopen" 函数需要激活<br />';
	}

	if(!function_exists('curl_init')) {
		$error .= '"curl" 函数需要激活<br />';
	}

	if(!function_exists('scandir')) {
		$error .= '"scandir"函数需要激活<br />';
	}

	if(!function_exists('mysql_connect')) {
		$error .= '"mysql_connect" 函数需要激活<br />';
	}


	if(!version_compare("5.2", phpversion(), "<=")) {
		$error .= 'PHP 5 >= 5.3.28是必须的<br />';
	}




    //end

	if(!isset($_POST['mysql_host']) || trim($_POST['mysql_host']) == '') {
		$error .= '请填写MySQL host<br />';
	}
	if(!isset($_POST['mysql_user']) || trim($_POST['mysql_user']) == '') {
		$error .= '请填写MySQL用户名<br />';
	}
//	if(!isset($_POST['mysql_pass']) || trim($_POST['mysql_pass']) == '') {
//		$error .= '请填写MySQL密码<br />';
//	}
	if(!isset($_POST['mysql_db']) || trim($_POST['mysql_db']) == '') {
		$error .= '请填写MySQL数据库<br />';
	}
    if(!isset($_POST['admin_phone']) || !check_phone($_POST['admin_phone'])) {
        $error .= '请填写正确手机号<br />';
    }
	if(!isset($_POST['admin_mail']) || !check_email($_POST['admin_mail'])) {
		$error .= '请填写管理员e-mail<br />';
	}

	if(!isset($_POST['report_mail']) || !check_email($_POST['report_mail'])) {
		$error .= '请填写技术支持e-mail<br />';
	}
	if(!isset($_POST['meta_title']) || trim($_POST['meta_title']) == '') {
		$error .= '请填写meta title<br />';
	}
	if(!isset($_POST['meta_keywords']) || trim($_POST['meta_keywords']) == '') {
		$error .= '请填写meta keywords<br />';
	}
	if(!isset($_POST['meta_description']) || trim($_POST['meta_description']) == '') {
		$error .= '请填写meta description<br />';
	}
	if(!isset($_POST['lang']) || trim($_POST['lang']) == '') {
		$error .= '请选择语言<br />';
	}

	if($error == '') {
		@$dbl = mysql_connect($_POST['mysql_host'], $_POST['mysql_user'], $_POST['mysql_pass']);
		if($dbl === FALSE) {
			$error .= '无法连接MySQL，请填写正确的数据库信息<br />';
		}else{
			$s = mysql_select_db($_POST['mysql_db']);
			if($s === FALSE) {
				$error .= '数据库错误，请填写正确的数据库信息<br />';
			}
		}

		if($error == '') {
			$dm = $_SERVER['HTTP_HOST'];
			if(substr($dm, 0, 4) == 'www.') {
				$dm = substr($dm, 4);
			}

			mysql_set_charset('utf8');

			copy($_SERVER['DOCUMENT_ROOT'].'/setup/lang/'.$_POST['lang'].'.php', $_SERVER['DOCUMENT_ROOT'].'/config/lang.php');

			$handle = fopen($_SERVER['DOCUMENT_ROOT'].'/config/current.txt', 'w');
			fwrite($handle, $_POST['lang']);
			fclose($handle);

			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/config/config.php', 'w');
			fwrite($fp, "<?php \n\n");
			fwrite($fp, '$configArr = array('."\n");
			fwrite($fp, '	\'system_core\' => \''. preg_replace('/[\/]{2,}/','/', $_SERVER['DOCUMENT_ROOT'].'/core/') . '\', '."\n");
			fwrite($fp, '	\'root_path\' => \''.preg_replace('/[\/]{2,}/','/',$_SERVER['DOCUMENT_ROOT'].'/') . '\', '."\n");
			fwrite($fp, '	\'domain\' => \''.$dm.'\', '."\n");
			fwrite($fp, '	\'mysql_host\' => \''.$_POST['mysql_host'].'\', '."\n");
			fwrite($fp, '	\'mysql_user\' => \''.$_POST['mysql_user'].'\', '."\n");
			fwrite($fp, '	\'mysql_pass\' => \''.$_POST['mysql_pass'].'\', '."\n");
			fwrite($fp, '	\'mysql_db\' => \''.$_POST['mysql_db'].'\', '."\n");
			fwrite($fp, '); '."\n\n");
			fwrite($fp, '?>');											
			fclose($fp);

			$adminPassword = $_POST["admin_pwd"];

			require_once 'db.php';

			mysql_close($dbl);
            $index = 'http://demila.org/';


            require_once '../classes/Http.class.php';
            $http = new Http();
		    $url = $index.'/statistics';
		    $data = array(
                'ip'     => $_SERVER['SERVER_ADDR'] .':'.$_SERVER['SERVER_PORT'],
                'domain' => $_SERVER['HTTP_HOST'],
                'mobile' => $_POST['admin_phone'],
                'email'  => $_POST['admin_mail'],
                'server' => strpos($_SERVER['SERVER_SOFTWARE'], 'PHP')===false ? $_SERVER['SERVER_SOFTWARE'].'PHP/'.phpversion() : $_SERVER['SERVER_SOFTWARE'],
                'os'     => PHP_OS,
		    );

		    $res = $http->curlPost($url,$data);
			$complete = 'yes';
		}
	}
}else{
	$_POST['mysql_host'] = '';
	$_POST['mysql_user'] = '';
	$_POST['mysql_pass'] = '';
	$_POST['mysql_db'] = '';
	$_POST['admin_mail'] = '';
    $_POST['admin_username'] = '';
    $_POST['admin_pwd'] = '';
    $_POST['admin_pwd1'] = '';
    $_POST['admin_phone'] = '';
    $_POST['report_mail'] = '';
	$_POST['meta_title'] = '';
	$_POST['meta_keywords'] = '';
	$_POST['meta_description'] = '';
	$_POST['lang'] = '';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>安装</title>
	<link rel="stylesheet" type="text/css" href="/static/home/default/css/style.css"/>
	<link href="/static/home/default/css/custom/template.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="/static/home/default/css/custom/ucenter.css" rel="stylesheet" />
</head>
<body>
<div class="header">
	<div class="container">
		<a href="/" class="marketplace"><img alt="" src="/static/home/default/img/custom/logo.png" title="Demila"></a>
	</div>
</div>
<div class="pagetit">
	<div class="navblock">
		<ul class="titnav clr">
			<li><a title="主页" href="/">主页</a></li>
			<li class="pipe">&gt;</li>
			<li>安装</li>
		</ul>
		<h2>安装</h2>
	</div>
</div>
<div class="ucbg">
	<div class="ucblock paddingt clr">
		<?php
		if($isInstalled) {
		?>		
		<div class="notice error">
			<strong>你的系统已安装成功，请勿重复安装!</strong>
			<br /><br />
			<strong>为确保站点安全，请务必删除"/setup"目录。</strong>
		</div>		
		<?php
		}elseif(isset($complete)) {
		?>		
		<div class="notice flash">
			<strong>恭喜!你的系统已安装成功!!!</strong>
			<strong>为确保站点安全，请务必删除"/setup"目录。</strong>
			<br /><br />
<!--			用户名(你设定的用户名): <strong>--><?php //echo $_POST['admin_username']; ?><!--</strong>-->
<!--			<br /><br />-->
<!--			密码(登录修改前务必牢记): <strong>--><?php //echo $adminPassword; ?><!--</strong>-->
			<br /><br />
			<a href="/" title="" target="_blank">去网站前台</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="/index.php/admin/" title=""  target="_blank">去管理面板</a>
		</div>
		<?php
		}else{
		?>
		<?php
		if(isset($error) && $error != '') {
		?>		
		<div class="box2">
			<div class="box_error"><?php echo $error; ?></div>
		</div>
		<?php
		}
		$fas_error = false;
		?>

		<?php if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/cache/')), -4) != '0777') { ?>
		<div class="box2">
			<div class="box_error">
				<strong>错误! </strong>请给这个目录设置写权限(0777)：<?php  echo $_SERVER['DOCUMENT_ROOT'];?> <strong>/core/data/cache/</strong> (包括所有子目录)
			</div>
		</div>
		<?php $fas_error = true; } else { ?>

		<?php if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/cache/session/')), -4) != '0777') { ?>
		<div class="box2">
			<div class="box_error">
				<strong>错误! </strong>请给这个目录设置写权限(0777)： <strong>/core/data/cache/session/</strong>
			</div>
		</div>
		<?php $fas_error = true; } ?>

		<?php if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/cache/templates_cache/')), -4) != '0777') { ?>
		<div class="box2">
			<div class="box_error">
				<strong>错误! </strong>请给这个目录设置写权限(0777)： <strong>/core/data/cache/templates_cache/</strong>
			</div>
		</div>
		<?php $fas_error = true; } ?>
		<?php } ?>

		<?php if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/core/data/logs/')), -4) != '0777') { ?>
		<div class="box2">
			<div class="box_error">
				<strong>错误! </strong>请给这个目录设置写权限(0777)： <strong>/core/data/logs/</strong>
			</div>
		</div>
		<?php $fas_error = true; } ?>

		<?php if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/static/uploads/')), -4) != '0777') { ?>
		<div class="box2">
			<div class="box_error">
				<strong>错误! </strong>请给这个目录设置写权限(0777)： <strong>/static/uploads/</strong>(包括所有子目录)
			</div>
		</div>
		<?php $fas_error = true; } ?>

		<?php if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'].'/config/')), -4) != '0777') { ?>
		<div class="box2">
			<div class="box_error">
				<strong>错误! </strong>请给这个目录设置写权限(0777)： <strong>/config/</strong>
			</div>
		</div>
		<?php $fas_error = true; } ?>
		

		<?php if(!$fas_error) { ?>
		<div class="setupdetail">
			<form class="horizontal-form disable-on-submit" method="post" action="">
				<fieldset>
					<div class="sideblock">
						<div class="input-group">
							<label for="mysql_host">数据库主机</label>
							<div class="inputs">
								<input id="mysql_host" name="mysql_host" required="true" value="<?php echo htmlspecialchars($_POST['mysql_host']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="mysql_user">数据库用户名</label>
							<div class="inputs">
								<input id="mysql_user" required="true" name="mysql_user" value="<?php echo htmlspecialchars($_POST['mysql_user']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="mysql_pass">数据库密码</label>
							<div class="inputs">
								<input id="mysql_pass"  name="mysql_pass" value="<?php echo htmlspecialchars($_POST['mysql_pass']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="mysql_db">数据库名称</label>
							<div class="inputs">
								<input id="mysql_db" required="true" name="mysql_db" value="<?php echo htmlspecialchars($_POST['mysql_db']); ?>" type="text">
							</div>
						</div>
					</div>
					<div class="sideblock">
						<div class="input-group">
							<label for="admin_username">管理员用户名</label>
							<div class="inputs">
								<input id="admin_username" required="true" name="admin_username" value="<?php echo htmlspecialchars($_POST['admin_username']); ?>" type="text">
							</div>
						</div>
                        <div class="input-group">
                            <label for="admin_pwd">管理员密码</label>
                            <div class="inputs">
                                <input id="admin_pwd" required="true" name="admin_pwd" value="<?php echo htmlspecialchars($_POST['admin_pwd']); ?>" type="text">
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="admin_pwd1">管理员密码确认</label>
                            <div class="inputs">
                                <input id="admin_pwd1" onblur="checkpwd()" required="true" name="admin_pwd1" value="<?php echo htmlspecialchars($_POST['admin_pwd1']); ?>" type="text">
                                <div id="admin_pwd1_tip" class="errortxt"></div>
                            </div>
                        </div>
						<div class="input-group">
							<label for="admin_phone">管理员联系电话</label>
							<div class="inputs">
								<input id="admin_phone" required="true" name="admin_phone" value="<?php echo htmlspecialchars($_POST['admin_phone']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="admin_mail">管理员e-mail</label>
							<div class="inputs">
								<input id="admin_mail" required="true" name="admin_mail" value="<?php echo htmlspecialchars($_POST['admin_mail']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="report_mail">举报或技术支持E-mail</label>
							<div class="inputs">
								<input id="report_mail" required="true" name="report_mail" value="<?php echo htmlspecialchars($_POST['report_mail']); ?>" type="text">
							</div>
						</div>
					</div>
					<div class="sideblock">
						<div class="input-group">
							<label for="meta_title">Meta title</label>
							<div class="inputs">
								<input id="meta_title" required="true" name="meta_title" value="<?php echo htmlspecialchars($_POST['meta_title']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="meta_keywords">Meta keywords</label>
							<div class="inputs">
								<input id="meta_keywords" required="true" name="meta_keywords" value="<?php echo htmlspecialchars($_POST['meta_keywords']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="meta_description">Meta description</label>
							<div class="inputs">
								<input id="meta_description" required="true" name="meta_description" value="<?php echo htmlspecialchars($_POST['meta_description']); ?>" type="text">
							</div>
						</div>
						<div class="input-group">
							<label for="country">选择语言</label>
							<div class="inputs">
								<select name="lang">
								<?php
								if(isset($languages)) {
								foreach($languages as $l) {
								echo '<option value="'.$l.'"';
								if($_POST['lang'] == $l) echo ' selected="selected" ';
								echo '>'.$l.'</option>';									
								}
								}
								?>
								</select>
							</div>
						</div>
						<div class="form-submit">
							<button id="personal_info_submit_button" name="install" class="btntheme2 btnsize" type="submit">安装</button>
						</div>
					</div>
				</fieldset>
			</form>										
		</div>
		<script type="text/javascript">
		function checkpwd(){
			var p = document.getElementById("admin_pwd").value,
				p1 = document.getElementById("admin_pwd1").value,
				t = document.getElementById("admin_pwd1_tip");
			if(p != p1){
				t.innerHTML = "两次输入密码不一致";
			}else{
				t.innerHTML = "";
			}
		}
		</script>

		<?php } ?>

		<?php
		}
		?>			

	</div>		
</div> <!-- 页面内容结束 -->
</body>
</html>