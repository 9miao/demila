define(function (require, exports, module){
	var $ = require("jq"),
		$iframe = null;

	function init(e){
		$iframe = $('#preview-frame');
		calcHeight();
		$('#header-bar a.close').on("mouseover", function(){
			$('#header-bar a.close').addClass('activated');
		}).on("mouseout", function(){
			$('#header-bar a.close').removeClass('activated');
		});

		$(window).resize(function(){
			calcHeight();
		}).load(function(){
			calcHeight();
		});
		$("#computer").on("click", function(){
			$iframe.css("width", "100%");
		});
		$("#phone").on("click", function(){
			$iframe.css("width", "375px");
		});
		$("#pad").on("click", function(){
			$iframe.css("width", "1024px");
		});
	}
	exports.init = init;

	function calcHeight(){
		var headerDimensions = $('#header-bar').height();
		$iframe.height($(window).height() - headerDimensions);
	}
});