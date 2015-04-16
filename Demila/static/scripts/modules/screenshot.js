define(function (require, exports, module){
	var $ = require("jq"),
		yBrowser = require("modules/hbrowser");

	function init(filter){
		require.async(["swipebox"], function(swipebox){
			swipebox($);

			$(filter).on("click", function(){
				var tmp = getImgsJson($(this).attr("screenshot-img"), $(this).attr("screenshot-tit"));
				$.swipebox(tmp);
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
				title: tit + " 预览" + (parseInt(i) + 1) + "   按ESC键退出  左右箭头翻页"
			});
		}
		return tmp;
	}
});