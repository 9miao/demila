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


class watermark {
	
	public $watermarkFile = NULL; //水印文件(PNG)
	private $imageType = "jpg";
	private $position='downright'; //水印位置，“downright”和“center”
	
	/**
	 * function constructor
	 *
	 * @param string $watermark
	 */
	function __construct($watermark, $position='') {
		if(!isset($watermark)) {
			die('Error with load watermark');
		}
		
		if($position!='') {
			$this->position = $position;
		}
		
		$this->watermarkFile = $watermark;
	}
	
	/**
	 * function getImageType
	 *
	 * @param string $imageFile
	 */
	private function getImageType($imageFile) {
		$imageInfo = getimagesize($filename);
		
		$imageInfo[2] = strtolower($imageInfo[2]);
		
		switch($imageInfo[2]) {
			case 'jpg': 
				$this->imageType = "jpg";
				break;
			
			case 'gif': 
				$this->imageType = "gif";
				break;
				
			case 'png': 
				$this->imageType = "png";
				break;
				
			default: 
				die('Wrong file type.');
				break;
		}
	}

	/**
	 * function addWatermark
	 *
	 * @param string $imageFile
	 * @param string $destinationFile
	 */
	function addWatermark($imageFile, $destinationFile=true) {
		
		if($destinationFile) {
			$destinationFile = $imageFile;
		}
		
		$watermark = @imagecreatefrompng($this->watermarkFile) or exit('Cannot open the watermark file.');
	  imageAlphaBlending($watermark, false);
	  imageSaveAlpha($watermark, true);
	
    $image_string = @file_get_contents($imageFile) or exit('Cannot open image file.');
    $image = @imagecreatefromstring($image_string) or exit('Not a valid image format.');

    $imageWidth=imageSX($image);
	  $imageHeight=imageSY($image);
	
    $watermarkWidth=imageSX($watermark);
    $watermarkHeight=imageSY($watermark);
	
    if($this->position == 'center') {
    	$coordinate_X = ( $imageWidth - $watermarkWidth ) / 2;
	    $coordinate_Y = ( $imageHeight - $watermarkHeight ) / 2;
    }
    else {
	    $coordinate_X = ( $imageWidth - 5) - ( $watermarkWidth);
	    $coordinate_Y = ( $imageHeight - 5) - ( $watermarkHeight);
    }
	
    imagecopy($image, $watermark, $coordinate_X, $coordinate_Y, 0, 0, $watermarkWidth, $watermarkHeight);
	
    if($this->imageType == 'jpg') {
    	imagejpeg ($image, $destinationFile, 100);
    }
    elseif($this->imageType == 'gif') {
    	imagegif ($image, $destinationFile);
    }
    elseif($this->imageType == 'png') {
    	imagepng ($image, $destinationFile, 100);
    }
    
    imagedestroy($image);
    imagedestroy($watermark);
	
	}
	
} 

?>