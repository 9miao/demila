define(function (require, exports, module){
	var $ = require("jq");
	
	function tabsinit($btn, $tab, opt){
		$tab.eq(0).siblings().hide();
		$btn.each(function(e){
			$(this).click(function(){
				$btn.eq(e).addClass(opt.active).siblings().removeClass(opt.active);
				$tab.eq(e).show().siblings().hide();
			});
		});
		$btn.eq(0).click();
	}
	exports.tabsinit = tabsinit;
});