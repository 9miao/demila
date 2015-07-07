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


define ('USING_LANGUAGE', false);
require_once 'config.php';
require_once $config ['root_path'] . '/core/functions.php';
include_once $config ['system_core'] . "/initEngine.php";
admin_login();
header("Content-Type:text/html;charset=utf-8");
if (isset($_POST["update_version"]) && $_POST["update_version"] == 'true') {
    $copyright = str_replace(' ', '', file_get_contents(TEMPLATE_PATH . 'footer.html'));
    $str = $langArray['copyright_link'];
    $res = strpos($copyright, $str);
    if (!$res) {
        require_once ROOT_PATH.'/classes/Http.class.php';
        $http = new Http();
        $index=  'http://demila.org/checkempower ';
        $data["data"]= $_SERVER['HTTP_HOST'];
        $news1 = $http->curlPost($index,$data);
        $status=false;
        if(strstr($news1,"true"))
            $status=true;
        if(!$status){
            $data["msg"] = $langArray['is_copyright'];
        }else{
            if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'])), -4) != '0777'){
                $data["msg"] = $langArray['update_server'];
            }
            else{
                require_once ROOT_PATH.'/classes/Http.class.php';
                $http = new Http();
                $url = $langArray['index'].'/getversion';
                $version = $http->curlGet($url);
                $version=strstr($version,'[{');
                $version = json_decode($version,1);
                $version = $version[0]["content"];
                $link=langMessageReplace($langArray['update_add'], array(
                    'version'=>$version,
                ));
                if (file_exists("update.zip"))
                    unlink('update.zip');
                $state=getFile($link,$config['root_path'],'update.zip',0);
                if ($state) {
                    $data["msg"] = $langArray['update_none'];
                    require_once $config ['root_path'] . '/core/classes/pclzip.lib.php';
                    $zip = new PclZip($config ['root_path'] . '/update.zip');
                    $zip->extract();
                    if ($zip->extract() == 0) {
                        $data["msg"] = $langArray['update_none'];
                    } else {
                        unlink('update.zip');
                        if (file_exists("updatedb.php")) {
                            require_once 'updatedb.php';
                            unlink('updatedb.php');
                            $data["msg"] = $langArray['update_ok'];
                        } else {
                            $data["msg"] = $langArray['update_ok'];
                        }
                    }
                } else {
                    $data["msg"] = $langArray['update_none'];
                }
            }
        }
    } else {
        if(substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'])), -4) != '0777'){
            $data["msg"] = $langArray['update_server'];
        }
        else{
            require_once ROOT_PATH.'/classes/Http.class.php';
            $http = new Http();
            $url = $langArray['index'].'/getversion';
            $version = $http->curlGet($url);
            $version=strstr($version,'[{');
            $version = json_decode($version,1);
            $version = $version[0]["content"];
            $link=langMessageReplace($langArray['update_add'], array(
                'version'=>$version,
            ));
            if (file_exists("update.zip"))
                unlink('update.zip');
            $state=getFile($link,$config['root_path'],'update.zip',0);
            if ($state) {
                $data["msg"] = $langArray['update_none'];
                require_once $config ['root_path'] . '/core/classes/pclzip.lib.php';
                $zip = new PclZip($config ['root_path'] . '/update.zip');
                $zip->extract();
                if ($zip->extract() == 0) {
                    $data["msg"] = $langArray['update_none'];
                } else {
                    unlink('update.zip');
                    if (file_exists("updatedb.php")) {
                        require_once 'updatedb.php';
                        unlink('updatedb.php');
                        $data["msg"] = $langArray['update_ok'];
                    } else {
                        $data["msg"] = $langArray['update_ok'];
                    }
                }
            } else {
                $data["msg"] = $langArray['update_none'];
            }
        }
    }
    echo json_encode($data);

}

?>