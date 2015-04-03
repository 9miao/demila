define(function (require, exports, module){
	var $ = require("jq"),
		filecheck = {
			thumbnail: /^jpg|png|gif$/i,
			sources: /^zip$/i,
			fir_preview: /^jpg|gif|png|wma|mp3|wav|mp4|flv|wmv|swf$/i,
			preview: /^jpg|gif|png|wma|mp3|wav|mp4|flv|wmv|swf$/i
		},
		selects = [
			"temporary_files_to_assign_thumbnail",
			"temporary_files_to_assign_source",
			"temporary_files_to_assign_first_preview",
			"temporary_files_to_assign_gallery_preview"
		],
		hasextend = false;
	function getFileName(str){
		var d = /\.[^\.]+$/.exec(str.toLowerCase())[0];
		d = d.replace(".", "");
		return d;
	}
	function selectCheck($dom, exr){
		var t = $dom.find("option");
		if(t.length > 1){
			t.each(function(){
				if($(this).val() == null || $(this).val() == ""){
					$(this).remove();
				}else if(!exr.test(getFileName($(this).val()))){
					$(this).remove();
				}
			});
			if(t.parent().attr("id") != "temporary_files_to_assign_gallery_preview"){
				t.before("<option class='tmpoption' value='-1' selected='selected' style='display:none;'>请选择...</option>");
			}
		}
	}
	function addToSel($dom, obj){
		//console.log($dom);
		//console.log(obj);
		$dom.find("option.placeholder").each(function(){
			if($(this).val() == ""){
				$(this).remove();
			}
		});
		$dom.append(obj);
	}
	function hideOption($doms, obj){
		if(obj == null || obj == ""){
			return;
		}
		for(var i in $doms){
			$doms[i].find("option").show().each(function(){
				if($(this).val() == obj){
					$(this).hide();
				}
			});
		}
	}
/* Demo Note:  This demo uses a FileProgress class that handles the UI for displaying the file name and percent complete.
The FileProgress class is not part of SWFUpload.
*/


/* **********************
   Event Handlers
   These are my custom event handlers to make my
   web application behave the way I went when SWFUpload
   completes different tasks.  These aren't part of the SWFUpload
   package.  They are part of my application.  Without these none
   of the actions SWFUpload makes will show up in my application.
   ********************** */
exports = {
selectClickInit: function(){
	$("#" + selects[0]).on("change", function(){
		//console.log($(this).val());
		hideOption([$("#" + selects[2]), $("#" + selects[3])], $(this).val());
	});
	//$("#" + selects[0]).trigger("change");
},
fileQueued: function (file) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("Pending...");
		progress.toggleCancel(true, this);

	} catch (ex) {
		this.debug(ex);
	}

},

fileQueueError: function (file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
},

fileDialogComplete: function (numFilesSelected, numFilesQueued) {	
	try {
		if (numFilesSelected > 0) {
			document.getElementById(this.customSettings.cancelButtonId).disabled = false;
		}
		
		/* I want auto start the upload and I can do that here */
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
},

uploadStart: function (file) {
	try {
		/* I don't want to do any file validation or anything,  I'll just update the UI and
		return true to indicate that the upload should start.
		It's important to update the UI here because in Linux no uploadProgress events are called. The best
		we can do is say we are uploading.
		 */
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setStatus("上传中");
		progress.toggleCancel(true, this);
	}
	catch (ex) {}
	
	return true;
},

uploadProgress: function (file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setProgress(percent);
		progress.setStatus("上传中");
	} catch (ex) {
		this.debug(ex);
	}
},

uploadSuccess: function (file, serverData) {
	//console.log(serverData);
  try {
	  var json_data_object = eval("(" + serverData + ")");
	  // upload.php returns the thumbnail id in the server_data, use that to retrieve the thumbnail for display
	  if(json_data_object.status == "done") {
	  		var fname = json_data_object.file.name,
	  			hz = getFileName(fname);
  			//console.log(hz);
  			//console.log(filecheck);

  			//console.log(filecheck.sources.test(hz));
	  		if(filecheck.thumbnail.test(hz)){
				var elOptNew = $("<option></option>");
				elOptNew.html(fname);
				elOptNew.val(json_data_object.file.filename);
				addToSel($("#" + selects[0]), elOptNew);
	  		}
	  		if(filecheck.sources.test(hz)){
				var elOptNew = $("<option></option>");
				elOptNew.html(fname);
				elOptNew.val(json_data_object.file.filename);
				if(hasextend){
					addToSel($("#" + selects[1]), elOptNew);
				}else{
					addToSel($("#" + selects[3]), elOptNew);
				}
			}
	  		if(filecheck.fir_preview.test(hz)){
				var elOptNew = $("<option></option>");
				elOptNew.html(fname);
				elOptNew.val(json_data_object.file.filename);
				if(hasextend){
					addToSel($("#" + selects[3]), elOptNew);
				}else{
					addToSel($("#" + selects[2]), elOptNew);
				}
			}
	  		if(filecheck.preview.test(hz)){
				var elOptNew = $("<option></option>");
				elOptNew.html(fname);
				elOptNew.val(json_data_object.file.filename);
				addToSel($("#" + selects[3]), elOptNew);
		  	}
		  
	  }else{
		  alert(json_data_object.status);
	  }
	
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus("完成");
		progress.toggleCancel(false);

	} catch (ex) {
		alert(ex);
		//this.debug(ex);
	}
},

uploadError: function (file, errorCode, message) {
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			if (this.getStats().files_queued === 0) {
				document.getElementById(this.customSettings.cancelButtonId).disabled = true;
			}
			progress.setStatus("上传取消");
			progress.setCancelled();
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
},

uploadComplete: function (file) {
	if (this.getStats().files_queued === 0) {
		document.getElementById(this.customSettings.cancelButtonId).disabled = true;
	}
},

// This event comes from the Queue Plugin
queueComplete: function (numFilesUploaded) {
	var status = document.getElementById("divStatus");
	status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
},


AddImage: function (src, hide) {
	var new_img = document.createElement("img");
	new_img.style.margin = "5px";

	if (hide == 1) {
		new_img.style.visibility = "hidden";
		new_img.style.width = "1px";
		new_img.style.height = "1px";
	}

	document.getElementById("thumbnails").appendChild(new_img);
	if (new_img.filters) {
		try {
			new_img.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			// If it is not set initially, the browser will throw an error. This
			// will set it if it is not set yet.
			new_img.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		new_img.style.opacity = 0;
	}

	new_img.onload = function() {
		FadeIn(new_img, 0);
	};
	new_img.src = src;
},

FadeIn: function (element, opacity) {
	var reduce_opacity_by = 15;
	var rate = 30; // 15 fps

	if (opacity < 100) {
		opacity += reduce_opacity_by;
		if (opacity > 100)
			opacity = 100;

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.
				// This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {
		setTimeout( function() {
			FadeIn(element, opacity);
		}, rate);
	}
},
checkSelect: function(){
	selectCheck($("#" + selects[0]), filecheck.thumbnail);
	selectCheck($("#" + selects[1]), filecheck.sources);
	selectCheck($("#" + selects[2]), filecheck.fir_preview);
	if(hasextend){
		selectCheck($("#" + selects[3]), filecheck.preview);
	}else{
		selectCheck($("#" + selects[3]), filecheck.sources);
	}
},
setExtend: function(tmp){
	hasextend = tmp;
}

}
return exports;


});