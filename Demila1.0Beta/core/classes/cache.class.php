<?
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------


/**
@version 2
@author Venko007

例如:

$cache = new cache;
// Get var if not 60 seconds old
$variable = $cache->Get("variable_with_id", 60);

if (!$variable) {
    // Cache file expired or is inexistant
    // Do something to get new data
    $cache->Set("variable_with_id", $newdata);
    $variable = $newdata;
}

echo $variable;

 **/

class cache {
	// 缓存路径(必须包含尾斜杠!)
	var $cacheDir = "../cache/phpcache/";
	// 缓存时间，单位秒，默认1小时
	var $defaultCacheLife = "3600";
	
	function __construct() {
		//global $config;
		$this->cacheDir = ROOT_PATH . $this->cacheDir;
	}
	/**
        Set($varId, $varValue) --
        Creates a file named "cache.VARID.TIMESTAMP"
        and fills it with the serialized value from $varValue.
        If a cache file with the same varId exists, Delete()
        will remove it.
	 **/
	function Set($varId, $varValue) {
		global $config;
		
		if ($config ['debug'] == 1) {
			add_debug ( "recache: " . $varId . " = " . $varValue );
		}
		
		// 清除旧的缓存，相同变量ID
		// $this->Delete($varId);
		// 创建新文件
		// $fileHandler = fopen($this->cacheDir . "cache." . $varId . "." . time(), "a");
		$file = $this->cacheDir . "cache." . $varId;
		//echo $file.'<br />';
		$fileHandler = fopen ( $file, "w" );
		// 写入序列化的数据
		$s = fwrite ( $fileHandler, serialize ( $varValue ) );
		fclose ( $fileHandler );
		
		return $s;
	}
	
	/**
        Get($varID, $cacheLife) --
        Retrives the value inside a cache file
        specified by $varID if the expiration time
        (specified by $cacheLife) is not over.
        If expired, returns FALSE
	 **/
	function Get($varId, $cacheLife = "") {
		global $config;
		// 设置默认缓存周期
		//$cacheLife = (! empty ( $cacheLife )) ? $cacheLife : $this->defaultCacheLife;
		

		if ($cacheLife !== false && $cacheLife != '0' && ! is_numeric ( $cacheLife )) {
			$cacheLife = $this->defaultCacheLife;
		}
		
		if($cacheLife == 0) {
			$cacheLife = false;
		}
		
		/* 循环查找缓存文件 */
		/* $dirHandler = dir($this->cacheDir);
        while ($file = $dirHandler->read()) {
            /* 用请求的变量ID查找缓存文件 * /
            if (preg_match("/cache.$varId.[0-9]/", $file)) {
                $cacheFileName = explode(".", $file);
                // 缓存文件创建时间
                $cacheFileLife = $cacheFileName[2];
                // 完整存放位置
                $cacheFile = $this->cacheDir . $file;

                /* 检查缓存文件是否过期 * /
                if ( $cacheLife == 0 || (time() - $cacheFileLife) <= $cacheLife) {
                    $fileHandler = fopen($cacheFile, "r");
                    $varValueResult = fread($fileHandler, filesize($cacheFile));
                    fclose($fileHandler);
                    // 未过期则返回为序列化的数据
                    return unserialize($varValueResult);
                } else {
                    // 缓存过期，终止循环
                    break;
                }
            }
        }
        $dirHandler->close();
        */
		$file = $this->cacheDir . "cache." . $varId;
		//echo '<br />';
		
		if (file_exists ( $file ) && filesize ( $file ) > 0) {
			if ($cacheLife === false || (time () - filemtime ( $file )) <= $cacheLife) {
				$fileHandler = fopen ( $file, "r" );
				$varValueResult = fread ( $fileHandler, filesize ( $file ) );
				fclose ( $fileHandler );
				// 未过期则返回为序列化的数据
				return unserialize ( $varValueResult );
			} else {
				
				if ($config ['debug'] == 1) {					
					//add_debug ( "cache expire: " . $varId . " TimeExpire: " . unix_time ( filectime ( $file ) ) );
				}
				
				return false;
			}
		} else {			
			if ($config ['debug'] == 1) {
				add_debug ( "cache file not exists! " . $varId );
			}
			
			return false;
		}
		
		return FALSE;
	}
	
	/**
        Delete($varId) --
        Loops through the cache directory and
        removes any cache files with the varId
        specified in $varID
	 **/
	function Delete($varId) {
		/*$dirHandler = dir($this->cacheDir);
        while ($file = $dirHandler->read()) {
            if (preg_match("/cache.$varId.[0-9]/", $file)) {
                unlink($this->cacheDir . $file); // Delete cache file
            }
        }
        $dirHandler->close();
   		*/
		$file = $this->cacheDir . "cache." . $varId;
		if (file_exists ( $file )) {
			unlink ( $file );
			return true;
		}
		return false;
	}

}

?>