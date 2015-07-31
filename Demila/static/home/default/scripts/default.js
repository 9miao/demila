define(function (require, exports, module){
	var yBrowser = require("modules/hbrowser"),
		filecheck = {
		"image": /^jpg|png|gif$/i,
		"zip": /^zip$/i,
		"audio": /^wma|mp3|wav/i,
		"video": /^mp4|flv|wmv|swf/i,
		"prev": /^jpg|gif|png|wma|mp3|wav|mp4|flv|wmv|swf$/i
	};
	exports.flashpath = "/static/scripts/plug/demilamedia/videoplayer/player.swf";

	function checkMovie(file){
		return filecheck.video.test(getFileName(file));
	}
	exports.checkMovie = checkMovie;

	function checkWave(file){
		return filecheck.audio.test(getFileName(file));
	}
	exports.checkWave = checkWave;

	function checkImage(file){
		return filecheck.image.test(getFileName(file));
	}
	exports.checkImage = checkImage;

	function getFileName(str){
		if(!str){
			return "";
		}
		var d = /\.[^\.]+$/.exec(str.toLowerCase())[0];
		d = d.replace(".", "");
		return d;
	}
	exports.getFileName = getFileName;

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
	    	mainDom.innerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="' + flash_w + '" height="' + flash_h + '" id="' + main_id + '" name="' + main_id + '" tabindex="0">\
	    	<param name="movie" value="' + flashpath + '">' + t + '\
	    	<param name="bgcolor" value="#000000">\
	    	<param name="wmode" value="' + n + '">\
	    	<param name="flashvars" value="' + fv + '">\
	    	</object>';
	    }else{
	    	var f = document.createElement("object");
	    	f.setAttribute("type", "application/x-shockwave-flash");
	    	f.setAttribute("width", flash_w);
	    	f.setAttribute("height", flash_h);
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
	    function setParam(a, b, c) {
			var e = document.createElement("param");
			e.setAttribute("name", b);
			e.setAttribute("value", c);
			a.appendChild(e)
		}
	}
	exports.flash_add = flash_add;
});