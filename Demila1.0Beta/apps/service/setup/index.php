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


$service=new service();
$is_setup=$service->is_setup();
$isInstalled = false;
if($is_setup) {
    $isInstalled = true;
}
if($error == '') {
    $dbl = mysql_connect($_POST['mysql_host'], $_POST['mysql_user'], $_POST['mysql_pass']);
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

        $adminPassword = rand(0,9999).rand(0,9999).rand(0,9999);

        require_once 'sphp';

        mysql_close($dbl);
    }
}else{
    $_POST['mysql_host'] = '';
    $_POST['mysql_user'] = '';
    $_POST['mysql_pass'] = '';
    $_POST['mysql_db'] = '';
    $_POST['admin_mail'] = '';
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
    <title>客户服务管理安装</title>
    <link rel="stylesheet" type="text/css" href="/static/css/style.css"/>
    <link href="/static/css/custom/template.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/static/css/custom/ucenter.css" rel="stylesheet" />
</head>
<body>
<div class="header">
    <div class="container">
        <a href="/" class="marketplace"><img alt="" src="/static/img/custom/logo.png" title="Demila"></a>
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
                <strong>该扩展功能已安装成功，请勿重复安装!</strong>
                <br /><br />
                <strong>为确保站点安全，请务必删除"setup"目录。</strong>
            </div>
        <?php
        }else{
        if(isset($error) && $error != '') {
            ?>
            <div class="box2">
                <div class="box_error"><?php echo $error; ?></div>
            </div>
        <?php
        }
        $fas_error = false;
        }
        ?>



    </div>
</div> <!-- 页面内容结束 -->
</body>
