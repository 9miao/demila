define(function (require, exports, module){
	var $ = require("jq"),
		yBrowser = require("modules/hbrowser");

	var $ = require("jq"),
		mz = require("mz"),
		yBrowser = require("modules/hbrowser"),
		theDef = require("default"),
		audioContext = null;

	function init(maindomfilter, btnfilter, preview){
		var maindom = $(maindomfilter),
			previewobj = maindom.find("#previewobj"),
			tmp = "";

		if(theDef.checkImage(preview.preurl)){
			tmp = getPrevImgHtml(preview);
		}else if(theDef.checkMovie(preview.preurl)){
			tmp = getPrevVideoHtml(preview);
		}else if(theDef.checkWave(preview.preurl)){
			tmp = getPrevAudioHtml(preview);
		}
		if(tmp != ""){
			previewobj.html(tmp);
		}
		if(yBrowser.versions.isIE6){
			return;
		}
		if(previewobj.find("canvas").length != 0){
			audioCanInit(previewobj);
		}
		require.async(["demilamedia"], function(demilamedia){
			demilamedia($);

			maindom.find(btnfilter).on("click", function(){
				var tmp = getImgsJson($(this).attr("screenshot-img"), $(this).attr("screenshot-tit"));
				//$.demilamedia("df");
				$.demilamedia.open(tmp);
				//$(this).demilamedia.open(tmp);
				return false;
			});
		});
	}
	exports.init = init;

	function getImgsJson(imgs, tit){
		var tmp = [],
			t = imgs.split("|");
		for(var i in t){
			tmp.push({
				href: t[i],
				title: tit + " 预览" + (parseInt(i) + 1)
			});
		}
		return tmp;
	}
	function getPrevImgHtml(preview){
		var tmp = '<a class="screenbtn" href="' + preview.prelink + '" target="_blank" screenshot-img="' + preview.preimgs + '" screenshot-tit="' + preview.prename + '"><img src="' + preview.preurl + '" alt="' + preview.prename + '" /></a>';
		return tmp;
	}
	function getPrevVideoHtml(preview){
		var tmp = "";
		if(mz.video){
			tmp = "<video class='previewobj' controls='controls' autoplay='autoplay'><source src='" + preview.preurl + "' type='video/" + theDef.getFileName(preview.preurl) + "' /></video>";
		}else{
			var flash_arg = {},
				params = {
					allowfullscreen: "true",
					allowscriptaccess: "always",
					seamlesstabbing: "true",
					autostart: "true"
				};
			$.extend(flash_arg, {file: preview.preurl});
			theDef.flash_add("previewobj", theDef.flashpath, "100%", "330px", params, flash_arg);
			return "";
		}
		return tmp;
	}
	function getPrevAudioHtml(preview){
		var tmp = '';
		window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext || window.msAudioContext;
		try{
			//audioContext = new window.AudioContext();
		}catch(e){
			console.log(e);
		}
		if(audioContext && mz.canvas){
			tmp = "<canvas id='waveform' class='previewaudio' data-url='" + preview.preurl + "'></canvas>";
		}else if(mz.audio){
			tmp = "<audio class='previewobj' controls='controls' autoplay='autoplay'><source src='" + preview.preurl + "' type='audio/" + theDef.getFileName(preview.preurl) + "' /><embed src='" + preview.preurl + "' /></audio>";
		}else{
			var flash_arg = {},
				params = {
					allowfullscreen: "true",
					allowscriptaccess: "always",
					seamlesstabbing: "true",
					autostart: "true"
				};
			$.extend(flash_arg, {file: preview.preurl});
			theDef.flash_add("previewobj", theDef.flashpath, "100%", "330px", params, flash_arg);
			return "";
		}
		return tmp;
	}
	function audioCanInit(dom){
		/*require.async(["plug/wavesurfer/wavesurfer"], function(WaveSurfer){
			//console.log(wavesurfer);
			var wavesurfer = Object.create(WaveSurfer);
			// Init & load audio file
			document.addEventListener('DOMContentLoaded', function () {
			    var options = {
			        container     : document.querySelector('#waveform'),
			        waveColor     : 'violet',
			        progressColor : 'purple',
			        loaderColor   : 'purple',
			        cursorColor   : 'navy'
			    };

			    if (location.search.match('scroll')) {
			        options.minPxPerSec = 100;
			        options.scrollParent = true;
			    }

			    // Init
			    wavesurfer.init(options);
			    // Load audio from URL
			    //wavesurfer.load('example/media/demo.wav');
			    wavesurfer.load(dom.find("canvas").attr("data-url"));

			    // Regions
			    if (wavesurfer.enableDragSelection) {
			        wavesurfer.enableDragSelection({
			            color: 'rgba(0, 255, 0, 0.1)'
			        });
			    }
			});

			// Play at once when ready
			// Won't work on iOS until you touch the page
			wavesurfer.on('ready', function () {
			    //wavesurfer.play();
			});

			// Report errors
			wavesurfer.on('error', function (err) {
			    console.error(err);
			});

			// Do something when the clip is over
			wavesurfer.on('finish', function () {
			    console.log('Finished playing');
			});

			document.addEventListener('DOMContentLoaded', function () {
			    var progressDiv = document.querySelector('#progress-bar');
			    var progressBar = progressDiv.querySelector('.progress-bar');

			    var showProgress = function (percent) {
			        progressDiv.style.display = 'block';
			        progressBar.style.width = percent + '%';
			    };

			    var hideProgress = function () {
			        progressDiv.style.display = 'none';
			    };

			    wavesurfer.on('loading', showProgress);
			    wavesurfer.on('ready', hideProgress);
			    wavesurfer.on('destroy', hideProgress);
			    wavesurfer.on('error', hideProgress);
			});


			// Drag'n'drop
			document.addEventListener('DOMContentLoaded', function () {
			    var toggleActive = function (e, toggle) {
			        e.stopPropagation();
			        e.preventDefault();
			        toggle ? e.target.classList.add('wavesurfer-dragover') :
			            e.target.classList.remove('wavesurfer-dragover');
			    };

			    var handlers = {
			        // Drop event
			        drop: function (e) {
			            toggleActive(e, false);

			            // Load the file into wavesurfer
			            if (e.dataTransfer.files.length) {
			                wavesurfer.loadBlob(e.dataTransfer.files[0]);
			            } else {
			                wavesurfer.fireEvent('error', 'Not a file');
			            }
			        },

			        // Drag-over event
			        dragover: function (e) {
			            toggleActive(e, true);
			        },

			        // Drag-leave event
			        dragleave: function (e) {
			            toggleActive(e, false);
			        }
			    };

			    var dropTarget = document.querySelector('#drop');
			    Object.keys(handlers).forEach(function (event) {
			        dropTarget.addEventListener(event, handlers[event]);
			    });
			});
		});*/
	}
});