define(function (require, exports, module){
	return function(jquery){

(function(jQuery){
	if(jQuery.browser) return;
	jQuery.browser = {};
	jQuery.browser.mozilla = false;
	jQuery.browser.webkit = false;
	jQuery.browser.opera = false;
	jQuery.browser.msie = false;
	var nAgt = navigator.userAgent;
	jQuery.browser.name = navigator.appName;
	jQuery.browser.fullVersion = ''+parseFloat(navigator.appVersion);
	jQuery.browser.majorVersion = parseInt(navigator.appVersion,10);
	var nameOffset,verOffset,ix;
	if((verOffset = nAgt.indexOf("Opera")) != -1){// In Opera, the true version is after "Opera" or after "Version"
		jQuery.browser.opera = true;
		jQuery.browser.name = "Opera";
		jQuery.browser.fullVersion = nAgt.substring(verOffset+6);
		if((verOffset=nAgt.indexOf("Version"))!=-1){
			jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
		}
	}else if((verOffset=nAgt.indexOf("MSIE"))!=-1){// In MSIE, the true version is after "MSIE" in userAgent
		jQuery.browser.msie = true;
		jQuery.browser.name = "Microsoft Internet Explorer";
		jQuery.browser.fullVersion = nAgt.substring(verOffset+5);
	}else if((verOffset=nAgt.indexOf("Chrome"))!=-1){// In Chrome, the true version is after "Chrome"
		jQuery.browser.webkit = true;
		jQuery.browser.name = "Chrome";
		jQuery.browser.fullVersion = nAgt.substring(verOffset+7);
	}else if((verOffset=nAgt.indexOf("Safari"))!=-1){// In Safari, the true version is after "Safari" or after "Version"
		jQuery.browser.webkit = true;
		jQuery.browser.name = "Safari";
		jQuery.browser.fullVersion = nAgt.substring(verOffset+7);
		if((verOffset=nAgt.indexOf("Version"))!=-1){
			jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
		}
	}else if((verOffset=nAgt.indexOf("Firefox"))!=-1){// In Firefox, the true version is after "Firefox"
		jQuery.browser.mozilla = true;
		jQuery.browser.name = "Firefox";
		jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
	}else if((nameOffset=nAgt.lastIndexOf(' ')+1) < (verOffset=nAgt.lastIndexOf('/'))){// In most other browsers, "name/version" is at the end of userAgent
		jQuery.browser.name = nAgt.substring(nameOffset,verOffset);
		jQuery.browser.fullVersion = nAgt.substring(verOffset+1);
		if(jQuery.browser.name.toLowerCase() == jQuery.browser.name.toUpperCase()){
			jQuery.browser.name = navigator.appName;
		}
	}
	if((ix=jQuery.browser.fullVersion.indexOf(";"))!=-1){// trim the fullVersion string at semicolon/space if present
		jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,ix);
	}
	if((ix=jQuery.browser.fullVersion.indexOf(" "))!=-1){
		jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,ix);
	}
	jQuery.browser.majorVersion = parseInt(''+jQuery.browser.fullVersion,10);
	if(isNaN(jQuery.browser.majorVersion)) {
		jQuery.browser.fullVersion = ''+parseFloat(navigator.appVersion);
		jQuery.browser.majorVersion = parseInt(navigator.appVersion,10);
	}
	jQuery.browser.version = jQuery.browser.majorVersion;
})(jquery); 


	}
});