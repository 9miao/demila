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
		
		$this->uploadFileDirectory = 'temporary/';
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
			'filename' => $file,
			'name' => $_FILES['file']['name'],
			'size' => number_format($_FILES['file']['size'] / 1024 / 1024, 2),
			'uploaded' => time()
		);
		
		$_SESSION['temp']['uploaded_files'][$file] = $fileArr;
		
		return $fileArr;
	}
	
}

?>