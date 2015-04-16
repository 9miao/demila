define(function (require, exports, module){
	var $ = require("jq");

	function btninit(btnfilter, formfilter){
		var $dom = $(btnfilter),
			$form = $(formfilter);
		$dom.on("click", function(){
			var tmp = $(this).attr("list-show");
			if(tmp == "0"){
				$(this).attr("list-show", "1").html("添加至已有书签集");
				$form.show();
			}else{
				$(this).attr("list-show", "0").html("创建新书签集");
				$form.hide();
			}
			return false;
		});
	}
	exports.btninit = btninit;
});