<?php

class sitemap {
		
	private $xmlChar = array('&', '<', '>', '"', "'");
	private $xmlReplace = array('&amp;', '&lt;', '&gt', '&quot;', '&apos;');
	
	private $langs = array('bg','en');
		
	function regenerateSiteMap() {
		global $mysql, $config;
		
		/*
		 * XML文件
		 */

		$fh = fopen($config['root_path'].'sitemap.xml', 'w') or die('ERROR WITH FILE OPEN');
		
		$file = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ($this->langs as $language) {
			$file .= '
				<url>
	        <loc>http://'.$config['domain'].'/'.$language.'/</loc> 
	        <lastmod>'.date('Y-m-d').'T00:00:00+00:00</lastmod> 
	        <changefreq>monthly</changefreq> 
	        <priority>1.0</priority> 
	   		</url>
				<url>
	        <loc>http://'.$config['domain'].'/'.$language.'/sitemap/</loc> 
	        <lastmod>'.date('Y-m-d').'T00:00:00+00:00</lastmod> 
	        <changefreq>monthly</changefreq> 
	        <priority>0.5</priority> 
	   		</url>
				<url>
	        <loc>http://'.$config['domain'].'/'.$language.'/contacts/</loc> 
	        <lastmod>'.date('Y-m-d').'T00:00:00+00:00</lastmod> 
	        <changefreq>monthly</changefreq> 
	        <priority>0.9</priority> 
	   		</url>
	   	';
		}
		
		/*
		 * 匹配语言
		 */
		foreach ($this->langs as $language) {
	
			/*
			 * 页面类
			 */
			require_once ROOT_PATH.'/apps/pages/models/pages.class.php';
			$pagesClass = new pages();
	
			$all = $pagesClass->getAll(0, 0, " AND `visible` = 'true' ");
			
			if(is_array($all)) {				
				foreach($all as $k=>$v) {
					
					$file .= '
						<url>
			        <loc>http://'.$config['domain'].'/'.$language.'/pages/'.$v['key'].'.html'.'</loc> 
			        <lastmod>'.date('Y-m-d').'T00:00:00+00:00</lastmod> 
			        <changefreq>monthly</changefreq> 
			        <priority>0.9</priority> 
		    		</url>
					';
								
				}
			}
			
			/*
			 * 
			 */
			require_once ROOT_PATH.'/apps/news/models/news.class.php';
			$newsClass = new news();
	
			$all = $newsClass->getAll(0, 0, " AND `visible` = 'true' ");
			
			if(is_array($all)) {				
				foreach($all as $k=>$v) {
					
					$file .= '
						<url>
			        <loc>http://'.$config['domain'].'/'.$language.'/news/view/'.$v['id'].'/'.url(htmlspecialchars($v['name'])).'.html'.'</loc> 
			        <lastmod>'.date('Y-m-d').'T00:00:00+00:00</lastmod> 
			        <changefreq>monthly</changefreq> 
			        <priority>0.8</priority> 
		    		</url>
					';
								
				}
			}
			
		}
		
		/*
		 * XML文件End
		 */
		$file .= '</urlset>';
		
		fwrite($fh, $file);
		
		fclose($fh);
		
		return $file;
	
	}
		
}

?>