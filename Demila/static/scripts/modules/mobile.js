define(function (require, exports, module){
	var $ = require("jq"),
		hbrow = require("modules/hbrowser");

	function pagenavInit(btnfilter, listfilter){
		//if(!checkMobile()){
		//	return;
		//}
		var $btn = $(btnfilter),
			$list = $(listfilter),
			$navmask = getmask($list);
		$btn.on("click", function(){
			if($list.hasClass("active")){
				$list.hide();
				$navmask.hide();
				$list.removeClass("active");
			}else{
				$list.show();
				$navmask.show();
				$list.addClass("active");
			}
		});
		$navmask.on("click", function(){
			$btn.trigger("click");
		});
		//$list.css("height", getWinWH().h);
	}
	exports.pagenavInit = pagenavInit;

	function pagetabInit(tabfilter){
		var $dom = $(tabfilter);
			w = $dom.find("li").width(),
			n = $dom.find("li").length;
		$dom.css("width", (w + 10) * n);
	}
	exports.pagetabInit = pagetabInit;

	function tabtipInit(btnfilter, listfilter, maskfilter){
		var $btn = $(btnfilter),
			$list = $(listfilter),
			$mask = $(maskfilter);
		$btn.on("click", function(){
			$list.show();
			$mask.show();
		});
		$mask.on("click", function(){
			$list.hide();
			$mask.hide();
		});
	}
	exports.tabtipInit = tabtipInit;

	function getWinWH(){
		return {w: $(window).width(), h: $(window).height()};
	}
	function checkMobile(){
		return hbrow.versions.mobile || hbrow.versions.iPad;
	}
	function getmask($obj){
		var tmp = $("#" + $obj.attr("id") + "_mask");
		if(tmp.length == 0){
			$("body").append("<div id='" + $obj.attr("id") + "_mask' class='mobile_pagemask'></div>");
			tmp = $("#" + $obj.attr("id") + "_mask");
		}
		return tmp;
	}
});