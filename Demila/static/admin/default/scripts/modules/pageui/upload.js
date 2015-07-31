define(function (require, exports, module){
	var $ = require("jq");

	function uploadInit(url, sessid, funtype){
		var domid = [],
			filetype = ["thumbnail", "main_file", "first_preview", "theme_preview"],
			nums = ["single", "single", "single", "multiple"],
			showtype = ["image", "zip", "prev", "prev"],
			issubmit = false;
		if(funtype === "edit"){
			window.onbeforeunload = function(){
				if(!issubmit){
			    	return ("您的修改尚未保存，确定离开此页面？");
				}
			}
			$("form").on("submit", function(){
				issubmit = true;
			});
			domid = ["#thumbnail_edit", "#main_file_edit", "#first_preview_edit", "#theme_preview_edit"];
		}else{
			domid = ["#thumbnail", "#main_file", "#first_preview", "#theme_preview"];
		}
		if(window.FormData === undefined){
			require.async(["modules/upload/ajaxupload_old"], function(upload){
				upload.init(domid, filetype, nums, showtype, url, sessid, funtype);
			});
		}else{
			require.async(["modules/upload/ajaxupload"], function(upload){
				upload.init(domid, filetype, nums, showtype, url, sessid, funtype);
			});
		}
	}
	exports.uploadInit = uploadInit;
});