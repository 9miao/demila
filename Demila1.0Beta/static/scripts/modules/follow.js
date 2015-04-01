define(function (require, exports, module){
	var $ = require("jq");

	function followBtnInit($dom, purl){
		$dom.on("click",function(){
			$.ajax({
				complete:function(request){
					//jQuery("#ajax-feedback").animate({"height": "toggle", "opacity": "toggle"}, "slow");
				},
				dataType:'script',
				type:'post',
				url:purl
			});
			//jQuery("#ajax-feedback").hide();
			//jQuery("#ajax-feedback").animate({"height": "toggle", "opacity": "toggle"}, "slow");
			return false;
		});
	}
	exports.followBtnInit = followBtnInit;
});