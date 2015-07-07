define(function (require, exports, module){
	var $ = require("jq"),
		ybr = require("modules/hbrowser");

	function init(filter, opt){
		var $dom = $(filter);
		$dom.html(getScrolltopHtml(opt, ybr.versions.mobile));
		//if(!ybr.versions.mobile){
			$dom.find("li").hover(function(){
				$(this).find(".sidebox").stop().animate({"width":"124px"},200).css({"opacity":"1","filter":"Alpha(opacity=100)","background":"#ae1c1c"});
			},function(){
				$(this).find(".sidebox").stop().animate({"width":"54px"},200).css({"opacity":"0.8","filter":"Alpha(opacity=80)","background":"#000"});
			});
			$dom.find(".sidebox2").hover(function(){
				$(this).stop().css({"opacity":"1","filter":"Alpha(opacity=100)","background":"#ae1c1c"});
				$dom.find(".tdcimg").css("left", "0px").show().stop().animate({"width":"258px", "left": "-258px"},200)
			}, function(){
				$(this).stop().css({"opacity":"0.8","filter":"Alpha(opacity=80)","background":"#000"});
				$dom.find(".tdcimg").stop().animate({"width":"0px", "left": "0px"},200,function(){$(this).hide();})
			});
			$dom.find(".sidetop").on("click", goTop);
		//}else{
		//	$dom.addClass("mobile");
		//	$dom.find(".sidetop_m").on("click", goTop);
		//}
	}
	exports.init = init;

	function goTop(){
		$('html,body').animate({'scrollTop':0},300);
	}
	function getScrolltopHtml(opt, mobile){
		var tmp = "",
			qqs = opt.qq;
		//if(!mobile){
			tmp += "<ul>";
			for(var i = 0, len = qqs.length; i < len; i++){
				tmp += "<li><a href='http://wpa.qq.com/msgrd?v=3&uin=" + qqs[i] + "&site=qq&menu=yes' target='_blank'><div class='sidebox'><img src='/static/templates/default/img/custom/icon04.png'>联系我们</div></a></li>";
			}
			tmp += "<li><a><div class='sidebox2'><img class='icon' src='/static/templates/default/img/custom/icon06.png' /><span>微信帐号</span></div></a></li>\
	  			<li class='lastitem'><a title='回到顶部' class='sidetop'><img alt='回到顶部' src='/static/templates/default/img/custom/icon05.png'><span></span></a></li></ul>\
	  			<img class='tdcimg' src='/static/templates/default/img/custom/tdc.jpg'/></ul>";
		//}else{
			//tmp += "<a title='回到顶部' class='sidetop_m'><span></span></a>";
		//}
  		return tmp;
	}
});