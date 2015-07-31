define(function (require, exports, module){
	var $ = require("jq");

	function msginit(domf){
		var $obj = $(domf);
		$obj.find(".closebtn").on("click", hidemsg);
		setTimeout(hidemsg, 5000)

		function hidemsg(){
			$obj.hide();
		}
	}
	exports.msginit = msginit;
});