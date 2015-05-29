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



class files extends base {

    public function addFile() {
        global $mysql, $config;

        $this->uploadFileDirectory = 'temporary/'.$_SESSION['user']['user_id'].'/';
        $this->maxFileSize = $config['max_upload_size'];
        $this->fileExt = $config['upload_ext'];

        $file = $this->upload('file', '', true);
        if(substr($file, 0, 6) == 'error_') {
            $error['file'] = $file;
        }

        if(isset($error)) {
            return $error;
        }

        $fileArr = array(
            'filename' => '/static/uploads/temporary/'.$_SESSION['user']['user_id'].'/'.$file,
            'name' => $_FILES['file']['name'],
            'size' => number_format($_FILES['file']['size'] / 1024 / 1024, 2),
            'uploaded' => time()
        );

        $type = $_POST['type'];
        $edit = $_POST['edit'];

        if(!isset($_POST['page_type']) || $_POST['page_type'] != 'edit'){
            //上传作品
            if($type != 'theme_preview'){
                unset($_SESSION['temp']['uploaded_files'][$type]);
            }
            if(isset($edit) && !empty($edit)){
                $is_edit_file = DATA_SERVER_PATH.'uploads/temporary/'.$_SESSION['user']['user_id'].'/'.$edit;
                @unlink($is_edit_file);
                unset($_SESSION['temp']['uploaded_files'][$type][$edit]);
            }
            $_SESSION['temp']['uploaded_files'][$type][$file] = $fileArr;

        }else{
            //编辑作品
            //zip文件记录文件名
            $file_name = pathinfo($fileArr['filename']);
            if(strtolower($file_name['extension']) == 'zip'){
                $_SESSION['temp']['edit_item']['main_file_name'] = $fileArr['name'];
            }

        }

        return $fileArr;
    }

}


?>