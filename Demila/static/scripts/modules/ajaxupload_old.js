define(function (require, exports, module){
	var $ = require("jq"),
		dmu = require("plug/dmuploader/ajaxfileupload")($),
		posturl = "",
		sid = "",
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
		posturl = url;
		sid = sessid;
		addNewObj(objs, types);
		uploadTempInit(objs, types);
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
				sessID: sessid,
				page_type: theFunType == "edit" ? "edit": ""
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
		filecheck = {
			"image": /^jpg|png|gif$/i,
			"zip": /^zip$/i,
			"audio": /^mp3/i,
			"video": /^mp4|swf/i,
			"prev": /^jpg|gif|png|mp3|mp4|swf$/i
		};
	function uploadTempInit(objs, types){
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
			//console.log(types);
			if(filecheck[types.showtype].test(getFileName(filename))){
				uploadObjShow($(objs), filename, types, i == len - 1);
			}
		}
	}
	function uploadObjShow(dom, path, types, islast){
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
		    addNewObj(dom, types);
		}
	}
	function fileUpload($obj, edtxt, callback){
		var part = $obj.parent();
		part.find(".objimg").removeClass("addNew").removeClass("filebg").addClass("onuploading").html("").siblings(".objtools").hide();
		$.ajaxFileUpload({
            url: posturl, //用于文件上传的服务器端请求地址
            secureuri: false, //一般设置为false
            data: {
            	"sessID": sid,
				"type": part.parent().attr("id"),
				"edit": edtxt
			},
            fileElementId: $obj.attr("id"), //文件上传空间的id属性  <input type="file" id="file" name="file" />
            dataType: 'JSON', //返回值类型 一般设置为json
            success: function(data, status){//服务器成功响应处理函数
                //alert(data.url);
                part.find(".objimg").removeClass("onuploading");
                data = eval('(' + data + ')');
                //console.log(data);
                callback(data);
            },
            error: function(data, status, e){//服务器响应失败处理函数
                showError($obj, "上传出错：" + e);
            }
        })
        return false;
	}
	function showError(dom, txt){
		var that = $(dom).parent();
		that.append("<span class='errtxt'>" + txt + "</span>");
		setTimeout(function(){that.find(".errtxt").fadeOut(500, function(){$(this).remove();})}, 2000);
	}
	function addNewObj(objs, types){
		$(objs).append(getUploadHtml(types));
		if(theFunType == "viewonly"){
			return;
		}
		$(objs).find(".uploadobj").last().on("change", "input[type='file']", function(){
			var that = $(this),
				thatpar = that.parent();
				edval = thatpar.attr("upload-val");
			thatpar.attr("isedit", !edval ? "false": "true");
			if(that.val().length <= 0){
                return;
            }else if(!fileChange(this, types)){
            	return;
            }else{ 
            	var edits = thatpar.attr("upload-val");
            	if(!edits){
            		edits = "";
            	}
            	fileUpload(that, edits, function(data){
            		if(data.status == "done"){

            			thatpar.addClass("uploadcus").attr("upload-val", getPathFile(data.file.filename)).find(".objimg").addClass("hasobj");
		        		frushValue(thatpar);

						if(theFunType == "readonly"){
		        			thatpar.find(".downbtn").attr("href", data.file.filename);
		        		}
		        		switch(types.showtype){
							case "image":
								switchImgShow(thatpar, data.file.filename);
								break;
							case "zip":
								thatpar.find(".objimg").addClass("filebg").html("ZIP");
								break;
							case "prev":
								if(checkImage(data.file.filename)){
									switchImgShow(thatpar, data.file.filename);
								}else{
									thatpar.find(".objimg").addClass("filebg").html(getFileName(data.file.name).toUpperCase());
								}
								break;
							default:
								break;
						}
			        	if(types.nums == "multiple" && thatpar.attr("isedit") == "false"){
			        		//$(this).find("input").removeAttr("multiple");
			        		addNewObj(objs, types);
			        	}
            		}else{
		        		showError(that, "上传状态：" + data);
		        	}
            	});
            }
		});
	}
	function switchImgShow(dom, file){
		dom.find(".objimg").html("<img src='" + file + "' />");
	}
	function fileChange(target, types, callback){
		//检测上传文件的类型
		if(!(filecheck[types.showtype].test(getFileName(target.value)))){
			showError(target, "请选择正确的文件类型");
			if(window.ActiveXObject) {//for IE
				target.select();//select the file ,and clear selection
				document.selection.clear();
			}else if(window.opera){//for opera
				target.type = "text";
				target.type = "file";
			}else{
				target.value = "";//for FF,Chrome,Safari
			}
			return false;
		}

		//检测上传文件的大小
		var isIE = /msie/i.test(navigator.userAgent) && !window.opera;
		var fileSize = 0;
		if(isIE) return true;
		if(isIE && !target.files){
			var filePath = target.value,
				fileSystem = new ActiveXObject("Scripting.FileSystemObject"),
				file = fileSystem.GetFile(filePath);
			fileSize = file.Size;
		}else{
			fileSize = target.files[0].size;
		}
		var size = fileSize / 1024;
		if(size > (800 * 1024 * 1024)){
			showError(target, "所选择文件过大");
			if(window.ActiveXObject) {//for IE
				target.select();//select the file ,and clear selection
				document.selection.clear();
			}else if(window.opera){//for opera
				target.type = "text";
				target.type = "file";
			}else{
				target.value = "";//for FF,Chrome,Safari
			}
			return false;
		}
		return true;
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
	function getUploadHtml(type){
		var num = type.nums != 'single' ? getRandomStr(10): "",
			t = '' + ((type.nums == 'single')? '': ' multiple="multiple"') + '',
			tmp = '<div class="uploadobj">' + (theFunType == 'viewonly' ? '': '<input type="file" title="请选择文件" id="' + type.filetype + '_ajax' + num + '" name="file" accept="' + inputtype[type.filetype] + '" />') + '<span class="progress"></span>\
			<span class="progresstxt"></span>\
			<span class="objimg addNew"></span>' + (theFunType == 'viewonly' ? '': '<span class="objtools editbtn" title="编辑"></span>') + ((theFunType == 'readonly' || theFunType == 'viewonly') ? '<a target="_blank" class="objtools downbtn" title="下载">下载</a>': '<span class="objtools deletbtn" title="删除"></span>') + '\
		</div>';
		return tmp;
	}
	function getRandomStr(len){
		len = len || 32;
		var txt = "ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678",
			maxPos = txt.length,
			pwd = "";
		for(i = 0; i < len; i++){
			pwd += txt.charAt(Math.floor(Math.random() * maxPos));
		}
		return pwd;
	}
});