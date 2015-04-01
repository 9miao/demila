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

/****************************************************************************
* imageTransform.php v 1.3.5
*
* v 1.3.5
*
* This script allow to resize yours JPG, PNG or GIF images
* You can reduce or enlarge the images size maintaining its proportions
* You can crop the image with your desired size also maintaining its proportions
*
* Example:
*
* Reduce 800x600 image:
*
* $imageTransform->resize('car.jpg', 500, 500);
*
* The result is the image image resized to 500x375 (maintaining proportions)
*
* ----------------------------------------------------------------
*
* Create a resized thumb from a 800x600 image:
*
* $imageTransform->resize('car.jpg', 500, 500, 'tb_car.jpg');
*
* The result is a new image with sizes 500x375
*
* ----------------------------------------------------------------
*
* Cut 800x600 image:
*
* $imageTransform->crop('car.jpg', 500, 500);
*
* The result is the image image crop to 500x500 (first reduction, then crop)
*
* ----------------------------------------------------------------
*
* Create a crop image from a 800x600 image:
*
* $imageTransform->crop('car.jpg', 500, 500, 'tb_car.jpg');
*
* The result is a new image with size 500x500
*
* ----------------------------------------------------------------
*
* Create a flip vertical or flop horizontal image:
*
* $imageTransform->flipflop('car.jpg', 'flip', 'tb_car.jpg');
*
* The result is a new image with vertical flip
*
* $imageTransform->flipflop('car.jpg', 'flop', 'tb_car.jpg');
*
* The result is a new image with horizontal flop
*
* $imageTransform->flipflop('car.jpg', 'flipflop', 'tb_car.jpg');
*
* The result is a new image with vertical and horizontal flipflop
*
* ----------------------------------------------------------------
*
* Create a grayscale image (slow function):
*
* $imageTransform->gray('car.jpg', 'tb_car.jpg');
*
* The result is a new grayscale image
*
* ----------------------------------------------------------------
*
* Change image quality:
*
* $imageTransform->quality('car.jpg', 70);
*
* ----------------------------------------------------------------
*
* You also can use this class into an online image transformation.
*
* You can find examples in the view.php script (with this package)
*

* KNOWN BUGS:

* Bad transparency when resize/crop some GIF images
* Bad transparency when rotate some GIF images
* Bad transparency when grayscale some GIF and PNG images
*
* working around...

First publish: http://www.phpclasses.org/browse/package/4268.html

Copyright (C) 2008 Lito <lito@eordes.com>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

SEE HERE FOR MORE ==>> http://www.gnu.org/copyleft/gpl.html
*******************************************************************************/

class Image {
	var $image;
	var $target;
	var $size;
	var $debug = true; // Show errors
	var $quality = 100; // Result image quality
//	var $cache = 'cache/'; // Cache folder
	var $cache = false; // Cache disabled
	var $expire = 172800; // Cache expiration time in seconds
	var $enlarge = true; // Enlarge image if width or height needed is bigger than original
	var $data = array(); // Image data array
	var $forceType = false; // Force image type create
	var $allowed = array( // Valid image formats
		1 => 'gif',
		2 => 'jpg',
		3 => 'png'
	);

	/**
	 * function Image ()
	 * 
	 * Constructor
	 */
	function Image() {
		ini_set ( "memory_limit", "512M" );
	}
	
	/**
	* function debug (string $message)
	*
	* Print the error messages
	*/
	function debug ($message) {
		if ($this->debug) {
			$debug = debug_backtrace();

			rsort($debug);

			echo $message;

			foreach ($debug as $k => $v) {
				echo "\n\n".'<br /><br />'.str_repeat('&nbsp;', (2 + $k * 2)).'FILE: '.$v['file'];
				echo "\n".'<br />'.str_repeat('&nbsp;', (2 + $k * 2)).'LINE: '.$v['line'];
			}

			echo "\n".'<br />';
		}

		return false;
	}

	/**
	* function enlarge (boolean $value)
	*
	* When you resize or crop an image, you need the width and height
	* You can disable enlarge an image if the original size is lower than
	* the needed width and height
	*/
	function enlarge ($value) {
		$this->enlarge = $value;
	}

	/**
	* function forceType (integer $type)
	*
	* Force to generate the new image with a concrete type
	*
	* (1 => GIF, 2 => JPEG, 3 => PNG)
	*/
	function forceType ($type) {
		$this->forceType = $type;
	}

	/**
	* function itsImage (string $image)
	*
	* Check if it's a valid image
	*
	* return false:array
	*/
	function itsImage ($image) {
		if ($data = @getImageSize($image)) {
			return empty($this->allowed[$data[2]])?false:$data;
		} else {
			return false;
		}
	}

	/**
	* function cache (string $value)
	*
	* Enable/disable the image cache
	* To enable cache, needs the folder cache path
	*/
	function cache ($value) {
		if ($value === false) {
			$this->cache = false;
		} elseif (($value === true) && (empty($this->cache) || ($this->cache === true) || ($this->cache === false))) {
			$this->cache = false;
			
			$this->debug('The cache folder haven\'t a valid path: '.$this->cache);
		} elseif (!is_dir($value) || !is_writable($value)) {
			$this->cache = false;

			$this->debug('The cache folder isn\'t writable to me: '.$value);
		} else {
			$this->cache = preg_match('#/$#', $value)?$value:($value.'/');
		}
	}

	/**
	* function inlineHeaders (string $name)
	*
	* Print the inline headers to view function
	*/
	function inlineHeaders ($name) {
		header('Content-type: image/'.$this->allowed[$this->data[2]]);
		header('Content-Disposition: inline; filename="'.$name.'"');
	}

	/**
	* function view (string $mode, string $param, boolean $cache = true)
	*
	* View online an resize/crop image. The image result can be cached.
	*/
	function view ($mode, $image, $param, $cache = true) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		$name = explode('/', $image);
		$name = end($name);
		$ext = explode('.', $name);
		$ext = strtolower(end($ext));

		if (($mode == 'resize') || ($mode == 'crop')) {
			list($width, $height) = explode('x', $param);

			$width = intval($width);
			$height = intval($height);

			if (($width <= 0) || ($height <= 0)) {
				return $this->debug('No size valid: '.$width.'x'.$height);
			}

			if ((($this->enlarge == false) && ($this->data[0] <= $width) && ($this->data[1] <= $height))
			|| (($this->data[0] == $width) && ($this->data[1] == $height))) {
				$this->inlineHeaders($name);

				echo file_get_contents($image);

				return true;
			}
		}

		if ($cache && $this->cache) {
			if (!$this->recursive_path($this->cache) || !is_writable($this->cache)) {
				return $this->debug('Can\'t write in the cahe folder: '.$this->cache);
			}

			$target = $this->cache.md5($mode.'x'.$image.'x'.$param).'.'.$ext;

			if (is_file($target) && (filemtime($target) > (time() - $this->expire))) {
				$this->inlineHeaders($name);

				echo file_get_contents($target);

				return true;
			}
		} else {
			$target = false;
		}

		$this->inlineHeaders($name);

		switch($mode) {
			case 'gray':
				$ok = $this->gray($image, $target, true);
				break;
			case 'rotate':
				$ok = $this->rotate($image, $param, $target, true);
				break;
			case 'resize':
			case 'crop':
				$ok = $this->$mode($image, $width, $height, $target, true);
				break;
			case 'flip':
			case 'flop':
			case 'flipflop':
				$ok = $this->flipflop($image, $mode, $target, true);
				break;
			default:
				return $this->debug($mode.' isn\'t a valid mode');
		}

		if ($target == false) {
			return $ok;
		} elseif ($ok == true) {
			echo file_get_contents($target);
		} else {
			return false;
		}
	}

	/**
	* function defineTarget (string $thumb, boolean $view)
	*
	* Assign a valid value to target image
	*
	* return boolean
	*/
	function defineTarget ($thumb, $view) {
		if ($view) {
			$this->target = $thumb?$thumb:false;
		} elseif (empty($thumb)) {
			if (!is_writable($this->image)) {
				return $this->debug('The original image haven\'t write permissions to me: '.$this->image);
			}

			$this->target = $this->image;
		} else {
			$this->target = $thumb;
			$folder = (dirname($this->target) == '')?'./':dirname($this->target);

			if (!$this->recursive_path($folder) || !is_writable($folder)) {
				return $this->debug('Can\'t write in the target folder: '.$folder);
			}
		}

		return true;
	}

	/**
	* function gray (string $image, string $thumb = '', string $view = false)
	*
	* Transform an image to grayscale
	*
	* If the parameter $thumb it's set, instead transform the image image,
	* create a new with the location in set.
	*
	* $view condition is used in the online image view funcion
	*
	* return boolean
	*/
	function gray ($image, $thumb = '', $view = false) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		$this->image = $image;

		$this->defineTarget($thumb, $view);

		$thumb = $this->imageDefine();

		$quality = true;

		if ($quality) {
			// More slower but best quality

			for ($c = 0; $c < 256; $c++) {
				$palette[$c] = imageColorAllocate($thumb, $c, $c, $c);
			}

			for ($x = 0; $x < $this->data[0]; $x++) {
				for ($y = 0; $y < $this->data[1]; $y++) {
					$gray = (ImageColorAt($thumb, $x, $y) >> 8) & 0xFF;
					$rgb = imagecolorat($thumb, $x, $y);
					$r = ($rgb >> 16) & 0xFF;
					$g = ($rgb >> 8) & 0xFF;
					$b = $rgb & 0xFF;

					$gs = ($r * 0.299) + ($g * 0.587) + ($b * 0.114);

					imagesetpixel($thumb, $x, $y, $palette[$gs]);
				}
			}
		} else {
			// More faster but less quality

			for ($x = 0; $x < $this->data[0]; $x++) {
				for ($y = 0; $y < $this->data[1]; $y++) {
					$gray = (ImageColorAt($thumb, $x, $y) >> 8) & 0xFF;

					imagesetpixel($thumb, $x, $y, imageColorAllocate($thumb, $gray,$gray,$gray));
				}
			}
		}

		if ($this->imageCreate($thumb)) {
			return true;
		} else {
			return $this->debug('A problem occours creating the image. I can\'t finish the task.');
		}
	}

	/**
	* function resize (string $image, integer $width, integer $height, string $thumb = '', boolean $view = false)
	*
	* This function resize an image maintaining the image proportions.
	*
	* If the parameter $thumb it's set, instead transform the image image,
	* create a new with the location in set.
	*
	* $whith and $height set the max sizes allowed to width and height, not set the
	* sizes in the result image, the result sizes are calculated with this values.
	*
	* $view condition is used in the online image view funcion
	*
	* return boolean
	*/
	function resize ($image, $width, $height, $thumb = '', $view = false) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		if ((($this->enlarge == false) && ($this->data[0] <= $width) && ($this->data[1] <= $height))
		|| (($this->data[0] == $width) && ($this->data[1] == $height))) {
			if (($thumb != '') && ($image != $thumb)) {
				if ($this->defineTarget($thumb, false)) {
					@copy($image, $thumb);
				} else {
					return false;
				}
			}

			return true;
		}

		$this->image = $image;
		$this->size = array($width, $height);

		$this->defineTarget($thumb, $view);

		return $this->_mainTransform();
	}

	/**
	* function flip (string $image, string $mode, string $thumb = '', boolean $view = false)
	*
	* This function create and vertical flip or horizontal flop image.
	*
	* If the parameter $thumb it's set, instead transform the image image,
	* create a new with the location in set.
	*
	* $view condition is used in the online image view funcion
	*
	* return boolean
	*/
	function flipflop ($image, $mode, $thumb = '', $view = false) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		$this->image = $image;

		$this->defineTarget($thumb, $view);

		$newImage = $this->imageDefine();

		$w = $this->data[0];
		$h = $this->data[1];
		$thumb = $this->transparency($newImage, imageCreateTrueColor($w, $h));

		if ($mode == 'flip') {
			for ($x = 0; $x < $w; $x++) {
				//imagecopy($thumb, $newImage, $x, 0, $w - $x - 1, 0, 1, $h); // Little more slow
				imagecopy($thumb, $newImage, $w - $x - 1, 0, $x, 0, 1, $h);
			}
		} elseif ($mode == 'flop') {
			for ($x = 0; $x < $h; $x++) {
				//imagecopy($thumb, $newImage, 0, $x, 0, $h - $x - 1, $w, 1); // Little more slow
				imagecopy($thumb, $newImage, 0, $h - $x - 1, 0, $x, $w, 1);
			}
		} else {
			for ($x = 0; $x < $w; $x++) {
				imagecopy($thumb, $newImage, $w - $x - 1, 0, $x, 0, 1, $h);
			}

			$buffer = $this->transparency($newImage, imageCreateTrueColor($w, 1));

			for ($y = 0; $y < ($h / 2); $y++) {
				imagecopy($buffer, $thumb, 0, 0, 0, $h - $y - 1, $w, 1);
				imagecopy($thumb, $thumb, 0, $h - $y - 1, 0, $y, $w, 1);
				imagecopy($thumb, $buffer, 0, $y, 0, 0, $w, 1);
			}

			imagedestroy($buffer);
		}

		if ($this->imageCreate($thumb)) {
			return true;
		} else {
			return $this->debug('A problem occours creating the image. I can\'t finish the task.');
		}
	}

	/**
	* function quality (string $image, integer $quality, string $thumb = '', boolean = boolean)
	*
	* Create or change one image with different quality
	*
	* return boolean
	*/

	function quality ($image, $quality, $thumb = '', $view = false) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		$this->image = $image;

		$this->defineTarget($thumb, $view);

		$newImage = $this->imageDefine();

		$thumb = $this->transparency($newImage, imageCreateTrueColor($this->data[0], $this->data[1]));

		imagecopy($thumb, $newImage, 0, 0, 0, 0, $this->data[0], $this->data[1]);

		$old_quality = $this->quality;

		$this->quality = intval($quality);

		if ($this->forceType !== false) {
			$this->data[2] = $this->forceType;
		}

		$ok = $this->imageCreate($thumb);

		$this->quality = $old_quality;

		if ($ok) {
			return true;
		} else {
			return $this->debug('A problem occours creating the image. I can\'t finish the task.');
		}
	}

	/**
	* function crop (string $image, integer $width, integer $height, string $thumb = '', boolean $view = false)
	*
	* Cut the image with the desired size. First the image is reduced or enlarged and after it's crop.
	*
	* If the parameter $thumb it's set, instead transform the image image,
	* create a new with the location in set.
	*
	* $view condition is used in the online image view funcion
	*
	* return boolean
	*/
	function crop ($image, $width, $height, $thumb = '', $view = false) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		if ((($this->enlarge == false) && ($this->data[0] <= $width) && ($this->data[1] <= $height))
		|| (($this->data[0] == $width) && ($this->data[1] == $height))) {
			if (($thumb != '') && ($image != $thumb)) {
				if ($this->defineTarget($thumb, false)) {
					@copy($image, $thumb);
				} else {
					return false;
				}
			}

			return true;
		}

		$this->image = $image;
		$this->size = array($width, $height);

		$this->defineTarget($thumb, $view);

		if ($width == $height) {
			$less = ($this->data[0] > $this->data[1]) ? $this->data[1] : $this->data[0];
			$width = $height = $less;
			$posX = intval(($this->data[0] / 2) - ($less / 2));
			$posY = intval(($this->data[1] / 2) - ($less / 2));
		} elseif ($width > $height) {
			list($height, $width, $posX, $posY) = $this->cropSize($this->data[1], $this->data[0], $height, $width);

			if ($posY < 0) {
				list($width, $height, $posY, $posX) = $this->cropSize($this->data[0], $this->data[1], $this->size[0], $this->size[1]);
			}
		} elseif ($width < $height) {
			list($width, $height, $posY, $posX) = $this->cropSize($this->data[0], $this->data[1], $width, $height);

			if ($posX < 0) {
				list($height, $width, $posX, $posY) = $this->cropSize($this->data[1], $this->data[0], $this->size[1], $this->size[0]);
			}
		}

		return $this->_mainTransform($posX, $posY, $width, $height, false);
	}

	/**
	* function cropSize (integer $sourceX, integer $sourceY, integer $targetX, integer $targetY)
	*
	* Calculate the size and coords to the crop action
	*
	* return array
	*/
	function cropSize ($sourceX, $sourceY, $targetX, $targetY) {
		$sizeX = round(($sourceX * $targetX) / (($sourceX * $targetY) / $sourceY));
		$sizeY = $sourceY;
		$coordX = 0;
		$coordY = intval(($sourceX / 2) - ($sizeX / 2));

		return array($sizeX, $sizeY, $coordX, $coordY);
	}

	/**
	* function rotate (string $image, integer $degrees, string $thumb = '', boolean $view = false)
	*
	* Rotate an image
	*
	* return boolean
	*/
	function rotate ($image, $degrees, $thumb = '', $view = false) {
		if (!($this->data = $this->itsImage($image))) {
			return $this->debug($image.' isn\'t a valid image');
		}

		$this->image = $image;

		$this->defineTarget($thumb, $view);

		$newImage = $this->imageDefine();

		if (function_exists('imagerotate')) {
			$thumb = $this->transparency($newImage, imagerotate($newImage, $degrees, 0));
		} else {
			$src_x = imagesx($newImage);
			$src_y = imagesy($newImage);

			if ($degrees == 180) {
				$dest_x = $src_x;
				$dest_y = $src_y;
			} elseif ($src_x <= $src_y) {
				$dest_x = $src_y;
				$dest_y = $src_x;
			} elseif ($src_x >= $src_y) {
				$dest_x = $src_y;
				$dest_y = $src_x;
			}

			$thumb = $this->transparency($newImage, imageCreateTrueColor($dest_x, $dest_y));

			imagealphablending($thumb, false);

			switch ($degrees) {
				case 270:
					for ($y = 0; $y < ($src_y); $y++) {
						for ($x = 0; $x < ($src_x); $x++) {
							$color = imagecolorat($newImage, $x, $y);
							imagesetpixel($thumb, $dest_x - $y - 1, $x, $color);
						}
					}

					break;
				case 90:
					for ($y = 0; $y < ($src_y); $y++) {
						for ($x = 0; $x < ($src_x); $x++) {
							$color = imagecolorat($newImage, $x, $y);
							imagesetpixel($thumb, $y, $dest_y - $x - 1, $color);
						}
					}

					break;
				case 180:
					for ($y = 0; $y < ($src_y); $y++) {
						for ($x = 0; $x < ($src_x); $x++) {
							$color = imagecolorat($newImage, $x, $y);
							imagesetpixel($thumb, $dest_x - $x - 1, $dest_y - $y - 1, $color);
						}
					}

					break;
				default:
					$thumb = $newImage;
			}
		}

		if ($this->imageCreate($thumb)) {
			return true;
		} else {
			return $this->debug('A problem occours creating the image. I can\'t finish the task.');
		}
	}

	/**
	* function _mainTransform (integer $posX=0, integer $posY=0, integer $width=0, integer $height=0, boolean $proportions = true)
	*
	* Process the image transform
	*
	* return boolean
	*/
	function _mainTransform ($posX=0, $posY=0, $width=0, $height=0, $proportions = true) {
		$newImage = $this->imageDefine();

		if (!$newImage) {
			return $this->debug($this->image.' isn\'t JPG, GIF nor PGN');
		}

		if ($proportions) {
			list($dX, $dY) = $this->proportions($this->data[0], $this->data[1]);
			list($width, $height) = array($this->data[0], $this->data[1]);
		} else {
			list($dX, $dY) = array($this->size[0], $this->size[1]);
		}

		list($posX, $posY) = $this->maxSize($posX, $posY, $width, $height);

		$thumb = $this->transparency($newImage, imageCreateTrueColor($dX, $dY));

		imageCopyResampled($thumb, $newImage, 0, 0, $posX, $posY, $dX, $dY, $width, $height);
		
		if ($this->imageCreate($thumb)) {
			return true;
		} else {
			return $this->debug('A problem occours creating the image. I can\'t finish the task.');
		}
	}

	/**
	* function imageDefine (void)
	*
	* Check the image format and create a new image with the same format
	*
	* return resource
	*/
	function imageDefine () {
		switch ($this->data[2]) {
			case 1:
				return imageCreateFromGIF($this->image);
			case 2:
				return imageCreateFromJPEG($this->image);
			case 3:
				return imageCreateFromPNG($this->image);
			default:
				return false;
		}
	}

	/**
	* function imageCreate (resource $thumb)
	*
	* Create a new image from the $thumb resource
	*
	* return booelan
	*/
	function imageCreate ($thumb) {
		if ($this->data[2] == 1) {
			$ok = imageGIF($thumb, $this->target, $this->quality);
		} elseif ($this->data[2] == 3) {
			$quality = 10 - round($this->quality / 10);
			$quality = (($quality < 0)?0:(($quality > 9)?9:$quality));

			$ok = imagePNG($thumb, $this->target, $quality);
		} else {
			$ok = imageJPEG($thumb, $this->target, $this->quality);
		}

		if ($ok) {
			imageDestroy($thumb);

			return true;
		} else {
			return false;
		}
	}

	/**
	* function transparency (resource $original, resource $new)
	*
	* Check and aply the transparency to an image
	*
	* return resource
	*/
	function transparency ($original, $new) {
		if (($this->data[2] !== 1) && ($this->data[2] !== 3)) {
			return $new;
		}

		$trans_index = imagecolortransparent($original);

		if ($trans_index >= 0) {
			$trans_color = imagecolorsforindex($original, $trans_index);
			$trans_index = imagecolorallocate($new, $trans_index['red'], $trans_index['green'], $trans_index['blue']);

			imagefill($new, 0, 0, $trans_index);
			imagecolortransparent($new, $trans_index);
		} else if ($this->data[2] === 3) {
			imagealphablending($new, false);

			$colorTransparent = imagecolorallocatealpha($new, 0, 0, 0, 127);

			imagefill($new, 0, 0, $colorTransparent);
			imagesavealpha($new, true);
		}

		return $new;
	}

	/**
	* function proportions (integer $width, integer $height)
	*
	* Calculate the image proportions to a scalable image
	*
	* return array
	*/
	function proportions ($width, $height) {
		if ($width > $height) {
			$n_width = $this->size[0];
			$n_height = round(($n_width * $height) / $width);
		} elseif ($height > $width) {
			$n_height = $this->size[1];
			$n_width = round(($n_height * $width) / $height);
		} elseif ($this->size[0] > $this->size[1]) {
			$n_width = $this->size[1];
			$n_height = $this->size[1];
		} else {
			$n_width = $this->size[0];
			$n_height = $this->size[0];
		}

		if ($n_width > $this->size[0]) {
			$n_width = $this->size[0];
			$n_height = round(($n_width * $height) / $width);
		}

		if ($n_height > $this->size[1]) {
			$n_height = $this->size[1];
			$n_width = round(($n_height * $width) / $height);
		}

		return array($n_width,$n_height);
	}

	/**
	* function maxSize (integer $posX, integer $posY, integer $width, integer $height)
	*
	* Calculate the image position from the image area
	*
	* return array
	*/
	function maxSize ($posX, $posY, $width, $height) {
		$posX = (($posX + $width) > $this->data[0])?($this->data[0] - $width):$posX;
		$posX = ($posX < 0)?0:$posX;
		$posY = (($posY + $height) > $this->data[1])?($this->data[1] - $height):$posY;
		$posY = ($posY < 0)?0:$posY;
		
		return array($posX,$posY);
	}

	/**
	* function recursive_path (string $target)
	*
	* Create a recursive folder
	*
	* return boolean
	*/
	function recursive_path ($target) {
		if (is_dir($target)) {
			return true;
		}

		$dirs = explode('/', $target);
		$dir = '';

		foreach ($dirs as $part) {
			if (empty($part) || ($part == '.')) {
				continue;
			}

			$dir .= '/'.$part;

			if (($part == '..') || is_dir($dir)) {
				continue;
			} else if (!@mkdir($dir, 0755)) {
				return $this->debug('Can\'t write in the target folder: '.$dir);
			}
		}

		clearstatcache();

		return is_dir($target);
	}
}
?>
