define(function (require, exports, module){
	var $ = require("jq"),
		dmu = require("plug/dmuploader/dmuploader")($),
		theFunType = "";

	function init(domid, filetype, nums, showtype, url, sessid, funtype){
		theFunType = funtype;
		for(var i = 0, len = domid.length; i < len; i++){
			var types = {
				filetype: filetype[i],
				nums: nums[i],
				showtype: showtype[i]
			}
			ajaxupload_init(domid[i], types, url, sessid);
		}
	}
	exports.init = init;

	function ajaxupload_init(objs, types, url, sessid){
		var that = $(objs),
			settings = {
			url: url,
			extraData: {
				"sessID": sessid,
				"type": that.attr("id")
			},
	        dataType: 'json',
	        allowedTypes: '*',
	        maxFileSize: 800*1024*1024,
	    	extFilter: filetypetxt[types.filetype],
	    	allowedTypes: alltypestxt[types.filetype],
	        onInit: function(){
	        },
	        onBeforeUpload: function(id){
	        	//console.log($(this).attr("upload-val"));
	        	//console.log(this);
	        	//console.log($(this).data("dmUploader").getExtraData());
	        	var exam = $(this).data("dmUploader");
	        	//console.log(exam.getSessionQueue().length);
	        	if(exam.getSessionQueue().length > 1){
	        		
	        	}
	        	var t = $(this).attr("upload-val");
	        	if(!t){
	        		t = "";
	        	}
	        	$(this).data("dmUploader").frushExtraData({"edit": t});
	        },
	        onNewFile: function(id, file){
				$(this).find(".objimg").removeClass("addNew").addClass("uploading");
				switch(types.showtype){
					case "image":
						switchImgShow(this, file);
						break;
					case "zip":
						$(this).find(".objimg").addClass("filebg").html("ZIP");
						break;
					case "prev":
						if(checkImage(file.name)){
							switchImgShow(this, file);
						}else{
							$(this).find(".objimg").addClass("filebg").html(getFileName(file.name).toUpperCase());
						}
						break;
					default:
						break;
				}
				setProgress($(this), 1);
	        },
	        onComplete: function(){

	        },
	        onUploadProgress: function(id, percent){
	        	setProgress($(this), percent);
	        },
	        onUploadSuccess: function(id, data){
	        	$(this).find(".objimg").removeClass("uploading").addClass("hasobj");
	        	hideProgress($(this));
	        	$(this).addClass("uploadcus").find("input").attr("title", "编辑文件");
	        	if(data.status == "done"){
	        		$(this).attr("upload-val", getPathFile(data.file.filename));
	        		if(theFunType == "readonly"){
	        			$(this).find(".downbtn").attr("href", data.file.filename);
	        		}
	        		frushValue($(this));
	        		var t = $(this).data("dmUploader").getExtraData();
	        		//console.log(this);
	        		//console.log(t.edit);
		        	if(types.nums == "multiple" && (!t.edit || t.edit == "")){
		        		$(this).find("input").removeAttr("multiple");
		        		addNewObj(objs, types, settings);
		        	}
	        	}else{
	        		alert(data);
	        	}
	        },
	        onUploadError: function(id, message){
	        	showError(this, message);
	        },
	        onFileTypeError: function(file){
	        	//console.log(file);
	        	showError(this, "请选择正确的文件类型");
	        },
	        onFileSizeError: function(file){
	        	showError(this, "所选择文件过大");
	        },
	        onFileExtError: function(file){
	        	showError(this, "请选择正确的文件类型");
	        },
	        onFallbackMode: function(message){
	        	showError(this, message);
	        }
	    };
	    if(theFunType == "edit"){
	    	settings.extraData["page_type"] = "edit";
	    }
	    addNewObj(objs, types, settings);
	    uploadTempInit(objs, types, settings);
		$(objs).on("mouseover", ".uploadobj", function(){
			if($(this).hasClass("uploadcus")){
				$(this).find(".objtools").show();
			}
		}).on("mouseout", ".uploadobj", function(){
			if($(this).hasClass("uploadcus")){
				$(this).find(".objtools").hide();
			}
		}).on("click", ".uploadobj .deletbtn", function(){
			//console.log(types.nums);
			var that = $(this);
			$.post(url,{
				filename: that.parent().attr("upload-val"),
				filetype: that.parent().parent().attr("id"),
				action: "a_delete",
				sessID: sessid
			}, function(data){
				if(data == "error"){
					showError(that.parent(), "删除失败，请稍后重试");
				}else{
					if(types.nums == "single"){
						that.siblings(".objimg").attr("class", "objimg addNew").html("").parent().removeAttr("upload-val").removeClass("uploadcus").find(".objtools").hide();
						frushValue(that.parent());
					}else{
						var tmp = that.parent().siblings(".hiddenvalue");
						that.parent().remove();
						frushValue(tmp);
					}
				}
			}, "text");
		});
	}

	var inputtype = {
			"thumbnail": "*.jpg,*.gif,*.png",
			"main_file": "*.zip",
			"first_preview": "*.jpg,*.png,*.gif",
			"theme_preview": "*.jpg,*.png,*.gif,*.mp3,*.mp4,*.swf"
		},
		filetypetxt = {
			"thumbnail": "jpg;gif;png",
			"main_file": "zip",
			"first_preview": "jpg;png;gif",
			"theme_preview": "jpg;png;gif;mp3;mp4;swf"
		},
		alltypestxt = {
			"thumbnail": "image/*",
			"main_file": "*",
			"first_preview": "*",
			"theme_preview": "*"
		},
		filecheck = {
			"image": /^jpg|png|gif$/i,
			"zip": /^zip$/i,
			"audio": /^mp3/i,
			"video": /^mp4|swf/i,
			"prev": /^jpg|gif|png|mp3|mp4|swf$/i
		};
	function checkMovie(file){
		return filecheck.video.test(getFileName(file));
	}
	function checkWave(file){
		return filecheck.audio.test(getFileName(file));
	}
	function checkImage(file){
		return filecheck.image.test(getFileName(file));
	}
	function getFileName(str){
		var d = /\.[^\.]+$/.exec(str.toLowerCase())[0];
		d = d.replace(".", "");
		return d;
	}
	function getPathFile(str){
		var n = str.lastIndexOf("/");
		return str.substring(n + 1);
	}
	function addNewObj(objs, types, settings){
		$(objs).append(getUploadHtml(types));
		if(theFunType != "viewonly"){
			$(objs).find(".uploadobj").last().dmUploader(settings);
		}
	}
	function setProgress(obj, percent){
		obj.find(".progress").show().css("height", percent + "%").siblings(".progresstxt").show().html(percent + "%");
	}
	function hideProgress(obj){
		obj.find(".progress").hide().css("height", "0%").siblings(".progresstxt").hide().html("0%");
	}
	function uploadTempInit(objs, types, settings){
		var val = $(objs).find(".hiddenvalue").val();
		if(val == ""){
			return;
		}
		val = val.split(",");
		for(var i = 0, len = val.length; i < len; i++){
			if(i > 0 && types.nums == "single"){
				break;
			}
			var filename = val[i];
			if(filecheck[types.showtype].test(getFileName(filename))){
				uploadObjShow($(objs), filename, types, settings, i == len - 1);
			}
		}
	}
	function uploadObjShow(dom, path, types, settings, islast){
		var tmp = $(dom).find(".uploadobj").last().addClass("uploadcus").find(".objimg").removeClass("addNew").addClass("hasobj");
		if(checkImage(path)){
			tmp.html("<img src='" + path + "' />");
		}else{
			tmp.addClass("filebg").html(getFileName(path).toUpperCase());
		}
		if(theFunType == "readonly" || theFunType == 'viewonly'){
			$(dom).find(".downbtn").last().attr("href", path);
		}
		$(dom).find(".uploadobj").last().attr("upload-val", getPathFile(path)).find("input").attr("title", "编辑文件");
		frushValue(tmp.parent());
		if(types.nums != "single" && !(islast && theFunType == "viewonly")){
			$(dom).find(".uploadobj").last().find("input").removeAttr("multiple");
		    addNewObj(dom, types, settings);
		}
	}
	function switchImgShow(dom, file){
		if(typeof FileReader !== "undefined"){
			var reader = new FileReader(),
				img = $("<img />"),
				that = $(dom).find(".objimg");

			reader.onload = function(e){
				img.attr('src', e.target.result);
				that.html(img);
			}
			reader.readAsDataURL(file);
		}else{
			$(dom).find(".objimg").addClass("filebg").html("IMG");
		}
	}
	function showError(dom, txt){
		$(dom).append("<span class='errtxt'>" + txt + "</span>");
		setTimeout(function(){$(dom).find(".errtxt").fadeOut(500, function(){$(this).remove();})}, 2000);
	}
	function frushValue($dom){
		var that = $dom.parent(),
			ups = that.find(".uploadcus");
		//console.log(that);
		if(ups.length == 1){
			that.find(".hiddenvalue").val(ups.attr("upload-val"));
		}else{
			var tmp = "";
			for(var i = 0, len = ups.length; i < len; i++){
				tmp += ups.eq(i).attr("upload-val");
				if(i != len - 1){
					tmp += ",";
				}
			}
			that.find(".hiddenvalue").val(tmp);
		}
	}
	function getUploadHtml(type){
		var t = '' + ((type.nums == 'single')? '': ' multiple="multiple"') + '';
		var tmp = '<div class="uploadobj">' + (theFunType == 'viewonly' ? '': '<input type="file" title="请选择文件" name="thumbnail_ajax" accept="' + inputtype[type.filetype] + '" />') + '<span class="progress"></span>\
			<span class="progresstxt"></span>\
			<span class="objimg addNew"></span>' + (theFunType == 'viewonly' ? '': '<span class="objtools editbtn" title="编辑"></span>') + ((theFunType == 'readonly' || theFunType == 'viewonly') ? '<a target="_blank" class="objtools downbtn" title="下载">下载</a>': '<span class="objtools deletbtn" title="删除"></span>') + '\
		</div>';
		return tmp;
	}
});