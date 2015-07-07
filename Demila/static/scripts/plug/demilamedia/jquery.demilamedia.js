define(function (require, exports, module){
	var mz = require("mz"),
		swfobject = null,
		tmpU = navigator.userAgent,
		yBrowser = {
			versions:function(){
				return {
					trident: tmpU.indexOf('Trident') > -1,
					presto: tmpU.indexOf('Presto') > -1,
					webKit: tmpU.indexOf('AppleWebKit') > -1,
					gecko: tmpU.indexOf('Gecko') > -1 && tmpU.indexOf('KHTML') == -1,
					mobile: !!tmpU.match(/AppleWebKit.*Mobile.*/) || !!tmpU.match(/Mobile/),
					ios: tmpU.indexOf('ios') > -1,
					android: tmpU.indexOf('Android') > -1,
					linux: tmpU.indexOf('Linux') > -1,
					iPhone: tmpU.indexOf('iPhone') > -1,
					mac: tmpU.indexOf('Mac') > -1,
					iPad: tmpU.indexOf('iPad') > -1,
					safari: tmpU.indexOf('Safari') > -1,
					maxthon: tmpU.indexOf('Maxthon') > -1,
					isIE: tmpU.indexOf("MSIE") > -1,
					isIE6: tmpU.indexOf("MSIE 6.0") > -1,
					isIE7: tmpU.indexOf("MSIE 7.0") > -1
				};
			}()
		};

return function (jquery){
(function (WIN, DOC, $, UND){
	var demilamedia = function(){
		},
		cacheArray = {
		},
		currentStr = "",
		domHtml_mask = "<div id='demilamedia_mask'></div>",
		domHtml_tit = "<div id='demilamedia_tit'></div>",
		domHtml_tip = "<div id='demilamedia_tips'></div>",
		domHtml_box = "<div id='demilamedia'>\
		<div class='demilamedia_top'><div class='hlb_left'></div><div class='hlb_middle'></div><div class='hlb_right'></div></div>\
		<div id='demilamedia_objs'><div class='objleftbg'><div class='objrightbg'><div id='demilamedia_main'></div></div></div><span id='demilamedia_loading'></span></div>\
		<div id='demilamedia_arrbtn'><a id='demilamedia_leftbtn' title='上一张'></a><a id='demilamedia_rightbtn' title='下一张'></a><div id='demilamedia_pagenum'></div></div>\
		<a id='demilamedia_closebtn' title='关闭'>关闭</a>\
		<div class='demilamedia_bottom'><div class='hlb_left'></div><div class='hlb_middle'></div><div class='hlb_right'></div></div>\
		</div>",
		defaultOption = {
			soundW: 650,
			soundH: 80,
			videoW: 650,
			videoH: 330,
			iframeW: 650,
			iframeH: 480,
			defW: 120,
			defH: 100,
			minImgW: 200,
			minImgH: 200,
			maxW: $(WIN).width(),
			maxH: $(WIN).height(),
			maxImgW: $(WIN).width() - 100,
			maxImgH: $(WIN).height() - 120,
			tips: "按ESC键返回 按方向键切换",
			flashpath: "/static/scripts/plug/demilamedia/videoplayer/player.swf"
		},
		$hxlb_mask = null,
		$hxlb = null,
		$hxlb_objs = null,
		$hxlb_arrs = null,
		$hxlb_close = null,
		$hxlb_loading = null,
		$hxlb_main = null,
		$hxlb_tit = null,
		$hxlb_tip = null,
		$hxlb_num = null,
		filecheck = {
			img: /^jpg|png|gif$/i,
			video: /^mp4|flv|wmv$/i,
			sound: /^wma|wav|mp3$/i,
			flash: /^swf$/i
		},
		videoFileCheck = /^mp4$/i,
		soundFileCheck = /^mp3|ogg|wav$/i,
		flashplayer = {need: false, statue: ""},
		cacheObj = function(){
			this.mainObjects = [];
			this.loadedObjects = [];
			this.mainObj_num = 0;
			this.loaded_num = 0;
			this.currentNum = -1;
		},
		msgopened = false;
	cacheObj.prototype = {
	}

	demilamedia.open = function(objs){
		msgopened = true;
		if(isString(objs)){
			currentStr = objs;
		}else{
			currentStr = "mainObjs";
			objInit(objs);
		}
		//debug($hxlb);
		$hxlb_mask.show();
		$hxlb.show();
		loadshow();
		makeMainSize(defaultOption.defW, defaultOption.defH, function(){
			demilamedia.currentObj(cacheArray[currentStr].currentNum);
		}, true);
		if(cacheArray[currentStr].mainObj_num <= 1){
			$hxlb_arrs.hide();
		}else{
			$hxlb_arrs.show();
			var $events = $._data($hxlb_lbtn[0], "events");
			if(!($events && $events["click"])){
				$hxlb_lbtn.on("click", function(){
					if($(this).hasClass("disable")){
						return;
					}
					demilamedia.prevObj();
				});
			}
			$events = $._data($hxlb_rbtn[0], "events");
			if(!($events && $events["click"])){
				$hxlb_rbtn.on("click", function(){
					if($(this).hasClass("disable")){
						return;
					}
					demilamedia.nextObj();
				});
			}
		}
		return false;
	}
	demilamedia.init = function(options){
		$.extend(defaultOption, options);

		$("body").append($(domHtml_box)).append($(domHtml_mask)).append($(domHtml_tit)).append($(domHtml_tip));
		$hxlb_mask = $("#demilamedia_mask");
		$hxlb = $("#demilamedia");
		$hxlb_objs = $hxlb.find("#demilamedia_objs");
		$hxlb_arrs = $hxlb.find("#demilamedia_arrbtn");
		$hxlb_lbtn = $hxlb_arrs.find("#demilamedia_leftbtn");
		$hxlb_rbtn = $hxlb_arrs.find("#demilamedia_rightbtn");
		$hxlb_close = $hxlb.find("#demilamedia_closebtn");
		$hxlb_main = $hxlb.find("#demilamedia_main");
		$hxlb_loading = $hxlb.find("#demilamedia_loading");
		$hxlb_tit = $("#demilamedia_tit");
		$hxlb_tip = $("#demilamedia_tips");
		$hxlb_num = $hxlb.find("#demilamedia_pagenum");
		hxboxResize();
		$(WIN).on("resize", hxboxResize).on("keyup", function(e){
			switch(e.keyCode){
				case 37:
					demilamedia.prevObj();
					break;
				case 39:
					demilamedia.nextObj();
					break;
				case 27:
					demilamedia.close();
					break;
			}
		});
		$hxlb_close.on("click", function(){
			demilamedia.close();
		});
		$hxlb_mask.on("click", function(){
			demilamedia.close();
		});
		$hxlb_tip.html(defaultOption.tips);
		//$hxlb_tit.css({"margin-left": -0.5 * defaultOption.maxW});
		makeTxtPos();
	}
	demilamedia.prevObj = function(){
		cacheArray[currentStr].currentNum--;
		if(cacheArray[currentStr].currentNum < 0){
			cacheArray[currentStr].currentNum++;
		}else{
			demilamedia.currentObj(cacheArray[currentStr].currentNum);
		}
	}
	demilamedia.nextObj = function(){
		cacheArray[currentStr].currentNum++;
		if(cacheArray[currentStr].currentNum > cacheArray[currentStr].mainObj_num - 1){
			cacheArray[currentStr].currentNum--;
		}else{
			demilamedia.currentObj(cacheArray[currentStr].currentNum);
		}
	}
	demilamedia.close = function(){
		if(!msgopened) return;
		msgopened = false;
		$hxlb_main.html("");
		$hxlb_arrs.children().hide();
		txthide();
		cacheArray[currentStr].currentNum = 0;
		makeMainSize(0, 0, function(){
			$hxlb.hide();
			$hxlb_mask.hide();
			loadhide();
		}, true);
	}
	demilamedia.currentObj = function(idx){
		loadshow();
		txthide();
		if(idx < 0){
			debug("demilamedia.currentObj: currentNum error");
			idx = 0;
		}
		if(cacheArray[currentStr].loadedObjects[idx]){
			var obj = cacheArray[currentStr].loadedObjects[idx];
			if(isImage(obj)){
				obj = checkImgSize(obj);
				makeMainSize(obj.width, obj.height, function(){
					$hxlb_main.html(getImgHtml(obj));
					makeTxtPos();
					makeThePage();
				});
			}else if(isSound(obj)){
				makeMainSize(defaultOption.soundW, defaultOption.soundH, function(){
					var ss = getSoundHtml(obj);
					if(ss.flash){
						$hxlb_main.html(ss.html);
						video_add("demilamedia_flash", defaultOption.soundW, defaultOption.soundH, obj);
					}else{
						$hxlb_main.html(ss);
					}
					makeTxtPos();
					makeThePage();
				});
			}else if(isVideo(obj)){
				makeMainSize(defaultOption.videoW, defaultOption.videoH, function(){
					var ss = getVideoHtml(obj);
					if(ss.flash){
						$hxlb_main.html(ss.html);
						video_add("demilamedia_flash", defaultOption.videoW, defaultOption.videoH, obj);
					}else{
						$hxlb_main.html(ss);
					}
					makeTxtPos();
					makeThePage();
				});
			}else if(isFlash(obj)){
				makeMainSize(defaultOption.videoW, defaultOption.videoH, function(){
					$hxlb_main.html(getFlashHtml());
					flash_add("demilamedia_flash", obj, defaultOption.videoW, defaultOption.videoH, {}, {});
					makeTxtPos();
					makeThePage();
				});
			}else{
				makeMainSize(defaultOption.iframeW, defaultOption.iframeH, function(){
					$hxlb_main.html(getIframeHtml(obj));
					makeTxtPos();
					makeThePage();
				});
			}
		}else{
			var paths = cacheArray[currentStr].mainObjects[idx].href;
			if(isImage(paths)){
				var img = new Image();
				img.src = paths;
				cacheArray[currentStr].loadedObjects[idx] = img;
				img.onload = function(){
					img = checkImgSize(img);
					makeMainSize(img.width, img.height, function(){
						$hxlb_main.html(getImgHtml(img));
						makeTxtPos();
						makeThePage();
					});
				}
				img.onabort = function(){
					cacheArray[currentStr].loadedObjects.splice(idx, 1);
				}
			}else if(isSound(paths)){
				cacheArray[currentStr].loadedObjects[idx] = paths;
				makeMainSize(defaultOption.soundW, defaultOption.soundH, function(){
					var ss = getSoundHtml(paths);
					if(ss.flash){
						$hxlb_main.html(ss.html);
						video_add("demilamedia_flash", defaultOption.soundW, defaultOption.soundH, paths);
					}else{
						$hxlb_main.html(ss);
					}
					makeTxtPos();
					makeThePage();
				});
			}else if(isVideo(paths)){
				cacheArray[currentStr].loadedObjects[idx] = paths;
				makeMainSize(defaultOption.videoW, defaultOption.videoH, function(){
					var ss = getVideoHtml(paths);
					if(ss.flash){
						$hxlb_main.html(ss.html);
						video_add("demilamedia_flash", defaultOption.videoW, defaultOption.videoH, paths);
					}else{
						$hxlb_main.html(ss);
					}
					makeTxtPos();
					makeThePage();
				});
			}else if(isFlash(paths)){
				cacheArray[currentStr].loadedObjects[idx] = paths;
				makeMainSize(defaultOption.videoW, defaultOption.videoH, function(){
					$hxlb_main.html(getFlashHtml());
					flash_add("demilamedia_flash", paths, defaultOption.videoW, defaultOption.videoH, {}, {});
					makeTxtPos();
					makeThePage();
				});
			}else{
				cacheArray[currentStr].loadedObjects[idx] = paths;
				makeMainSize(defaultOption.iframeW, defaultOption.iframeH, function(){
					$hxlb_main.html(getIframeHtml(paths));
					makeTxtPos();
					makeThePage();
				});
			}
		}
		$hxlb_tit.html(cacheArray[currentStr].mainObjects[idx].title);
		cacheArray[currentStr].currentNum = idx;
		if(cacheArray[currentStr].currentNum == 0){
			$hxlb_lbtn.addClass("disable");
		}else{
			$hxlb_lbtn.removeClass("disable");
		}
		if(cacheArray[currentStr].currentNum == cacheArray[currentStr].mainObj_num - 1){
			$hxlb_rbtn.addClass("disable");
		}else{
			$hxlb_rbtn.removeClass("disable");
		}
	}
	$.fn.demilamedia = function(options){
		$.fn.demilamedia.defaults = $.extend($.fn.demilamedia.defaults, options);
		return this.each(function(e){
			var $this = $(this),
				tmp = $this.attr("rel"),
				objs = [];
			if(!cacheArray[tmp]){
				cacheArray[tmp] = new cacheObj();
			}
			objInit(tmp, {href: $this.attr("href"), title: $this.attr("title")});
			$this.on("click", function(){
				cacheArray[$(this).attr("rel")].currentNum = e;
				demilamedia.open(tmp);
				return false;
			});
		});
	}
	$.fn.demilamedia.open = function(objs){
		//alert(objs.length);
		debug("building...");
	}
	$.fn.demilamedia.defaults = defaultOption;
	$.extend({
		demilamedia: demilamedia
	});
	demilamedia.init();

	function checkImgSize(img){
		//debug(img.width + "," + img.height);
		//debug(defaultOption);
		if(img.width < defaultOption.minImgW){
			img.height = parseInt(img.height * defaultOption.minImgW / img.width);
			img.width = defaultOption.minImgW;
			return img;
		}
		if(img.height < defaultOption.minImgH){
			img.width = parseInt(img.width * defaultOption.minImgH / img.height);
			img.height = defaultOption.minImgH;
			return img;
		}
		if(img.width > defaultOption.maxImgW){
			img.height = parseInt(img.height * defaultOption.maxImgW / img.width);
			img.width = defaultOption.maxImgW;
		}
		if(img.height > defaultOption.maxImgH){
			img.width = parseInt(img.width * defaultOption.maxImgH / img.height);
			img.height = defaultOption.maxImgH;
			return img;
		}
		return img;
	}
	function makeThePage(){
		$hxlb_num.html((cacheArray[currentStr].currentNum + 1) + "/" + cacheArray[currentStr].mainObj_num);
	}
	function makeMainSize(w, h, callback, arrhide){
		$hxlb_arrs.children().hide();
		$hxlb.animate({
			"margin-left": -1 * (w + 40) / 2,
			"margin-top": -1 * (h + 60) / 2
		});
		$hxlb_main.animate({
			width: w,
			height: h
		}, function(){
			if(callback){
				callback();
			}
			if(!arrhide){
				$hxlb_arrs.children().show();
				txtshow();
			}
		});
	}
	function objInit(objs, t){
		if(isArray(objs)){
			if(!cacheArray[currentStr]){
				cacheArray[currentStr] = new cacheObj();
			}
			cacheArray[currentStr].mainObj_num = objs.length;
			cacheArray[currentStr].loaded_num = 0;
			cacheArray[currentStr].mainObjects = objs;
			cacheArray[currentStr].currentNum = 0;
		}else if(isString(objs)){
			if(!cacheArray[objs]){
				cacheArray[objs] = new cacheObj();
			}
			cacheArray[objs].mainObj_num++;
			cacheArray[objs].loaded_num = 0;
			cacheArray[objs].mainObjects.push(t);
		}
	}
	function txthide(){
		$hxlb_tit.hide();
		$hxlb_tip.hide();
	}
	function txtshow(){
		$hxlb_tit.slideDown();
		$hxlb_tip.show();
	}
	function makeTxtPos(){
		$hxlb_tit.css("top", (defaultOption.maxH - $hxlb_main.height() - 60) / 2 - 20 - 3);
		$hxlb_tip.css("top", (defaultOption.maxH + $hxlb_main.height() + 60) / 2 + 1);
	}
	function getImgHtml(img){
		return "<img src='" + img.src + "' width='" + img.width + "' height='" + img.height + "' />";
	}
	function getSoundHtml(path){
		var tmp = "";
		if(yBrowser.versions.mobile || yBrowser.versions.iPad){
			if(soundFileCheck.test(getFileName(path))){
			 	tmp = "<audio id='demilamedia_sound' controls='controls' autoplay='autoplay'>\
					<source src='" + path + "' type='audio/" + getFileName(path) + "' />\
					<embed src='" + path + "' />\
					</audio>";
			}
			return tmp;
		}else if(mz.audio){
		 	tmp = "<audio id='demilamedia_sound' controls='controls' autoplay='autoplay'>\
				<source src='" + path + "' type='audio/" + getFileName(path) + "' />\
				<embed src='" + path + "' />\
				</audio>";
			return tmp;
		}else{
			tmp = getFlashHtml();
			return {flash: true, html: tmp};
		}
	}
	function getVideoHtml(path){
		var tmp = "";
		if(yBrowser.versions.mobile || yBrowser.versions.iPad){
			if(videoFileCheck.test(getFileName(path))){
				tmp = "<video id='demilamedia_video' controls='controls' autoplay='autoplay'>\
					<source src='" + path + "' type='video/" + getFileName(path) + "' />\
					</video>";
			}
			return tmp;
		}else if(mz.video){
			tmp = "<video id='demilamedia_video' controls='controls' autoplay='autoplay'>\
					<source src='" + path + "' type='video/" + getFileName(path) + "' />\
					</video>";
			return tmp;
		}else{
			tmp = getFlashHtml();
			return {flash: true, html: tmp};
		}
	}
	function getFlashHtml(){
		return "<div id='demilamedia_flash'></div>";
	}
	function getLoadHtml(){
		return "<span class='loading'></span>";
	}
	function getIframeHtml(path){
		return "<iframe frameborder='0' noresize='noresize' src='" + path + "' id='demilamedia_iframe' width='100%' height='100%'></iframe>";
	}
	function loadhide(){
		//$hxlb_loading.hide();
		$hxlb_main.html("");
	}
	function loadshow(){
		//$hxlb_loading.show();
		$hxlb_main.html(getLoadHtml());
	}
	function hxboxResize(){
		var tW = $(WIN).width(),
			tH = $(WIN).height();
		defaultOption.maxW = tW;
		defaultOption.maxH = tH;
		if(defaultOption.videoW > tW - 10){
			defaultOption.videoW = tW - 10;
		}
		if(defaultOption.videoH > tH - 120){
			defaultOption.videoH = tH - 120;
		}
		if(defaultOption.soundW > tW - 10){
			defaultOption.soundW = tW - 10;
		}
		if(defaultOption.soundH > tH - 120){
			defaultOption.soundH = tH - 120;
		}
		makeTxtPos();
	}
	function isVideo(obj){
		if(isString(obj)){
			return filecheck.video.test(getFileName(obj));
		}
		return false;
	}
	function isSound(obj){
		if(isString(obj)){
			return filecheck.sound.test(getFileName(obj));
		}
		return false;
	}
	function isImage(obj){
		if(isObject(obj)){
			return obj.tagName.toLowerCase() === "img";
		}else if(isString(obj)){
			return filecheck.img.test(getFileName(obj));
		}
		return null;
	}
	function isFlash(obj){
		if(isString(obj)){
			return filecheck.flash.test(getFileName(obj));
		}
		return false;
	}
	function isArray(obj){
		return Object.prototype.toString.call(obj) === '[object Array]';
	}
	function isObject(obj){
		return typeof obj === 'object';
	}
	function isString(s){
		return typeof s === 'string';
	}
	function isNumber(n){
		return typeof n === 'number';
	}
	function getFileName(str){
		var tmp = /\.[^\.]+$/.exec(str.toLowerCase());
		if(!tmp || tmp.length == 0){
			return null;
		}
		var d = tmp[0];
		d = d.replace(".", "");
		return d;
	}
	function video_add(domid, flash_w, flash_h, videopath){
		/*if(flashplayer.need && flashplayer.statue != "finish"){
			setTimeout(function(){
				video_add(domid, flash_w, flash_h, flash_arg);
			}, 500);
			return;
		}*/
		var flash_arg = {},
			params = {
				allowfullscreen: "true",
				allowscriptaccess: "always",
				seamlesstabbing: "true",
				autostart: "true"
			};
		$.extend(flash_arg, {file: videopath});
		flash_add(domid, defaultOption.flashpath, flash_w, flash_h, params, flash_arg);
	}
	function flash_add(domid, flashpath, flash_w, flash_h, params, flash_arg){
	    var mainDom = document.getElementById(domid),
	    	main_id = mainDom.id,
	    	n = "opaque",
			fv = "";
		for(var j in flash_arg){
			fv += j + "=" + flash_arg[j];
		}
	    if(yBrowser.versions.isIE){
	    	var t = "";
	    	for(var i in params){
	    		t += "<param name='" + i + "' value='" + params[i] + "'>";
	    	}
	    	mainDom.innerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%" id="' + main_id + '" name="' + main_id + '" tabindex="0">\
	    	<param name="movie" value="' + flashpath + '">' + t + '\
	    	<param name="bgcolor" value="#000000">\
	    	<param name="wmode" value="' + n + '">\
	    	<param name="flashvars" value="' + fv + '">\
	    	</object>';
	    }else{
	    	var f = document.createElement("object");
	    	f.setAttribute("type", "application/x-shockwave-flash");
	    	f.setAttribute("width", "100%");
	    	f.setAttribute("height", "100%");
	    	f.setAttribute("bgcolor", "#000000");
	    	f.setAttribute("data", flashpath);
	    	f.setAttribute("id", main_id);
	    	f.setAttribute("name", main_id);
	    	f.setAttribute("flashvars", fv);
	    	for(var i in params){
	    		setParam(f, i, params[i]);
	    	}
	    	//setParam(f, "file", flash_arg.file);
	    	setParam(f, "wmode", n);
	    	mainDom.parentNode.replaceChild(f, mainDom);
	    }
	    function setParam(a, b, c){
			var e = document.createElement("param");
			e.setAttribute("name", b);
			e.setAttribute("value", c);
			a.appendChild(e)
		}
	}
	function debug(txt){
		if(window.console && window.console.log){
			console.log(txt);
		}
	}
})(window, document, jquery);
}
});