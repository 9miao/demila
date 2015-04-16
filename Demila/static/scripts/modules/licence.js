define(function (require, exports, module){
	var $ = require("jq");
	function init(opt){
		$("div.fancy-purchase-panel input[type=submit]").remove();
		$("#purchase-form").submit(function() {
			return animatePanel(), !1
		});
		//$("#purchase-form > button").click(function(e) {
		//	return e.preventDefault(), animatePanel(), !1
		//});
		$("div.fancy-purchase-panel a.close-panel, div.account-required.panel a.close-panel").click(function() {
			return animatePanel("hide"), !1
		});
		$("a.buynow-submit").click(function() {
			return submit_purchase_form(this), !1
		});
		$("a.prepaid-submit").click(function() {
			return confirm_purchase($("#stored-item-name").val(), $("#stored-item-category").val()) && submit_purchase_form(this), !1
		});
		$("#purchase_button").click(function(){
			chooseLicence('regular', opt.price, opt.prepaid_price, 'block');
			animatePanel();
			return false;
		});
		$("#purchase_button2").click(function(){
			chooseLicence('extended', opt.price, opt.prepaid_price, 'none');
			animatePanel();
			return false;
		});
		$("#lchoose").change(function(){
			if($(this).val() === "1"){
				$("#licenitem-price").show();
				$("#licenitem-extend").hide();
				$("#purchase_button").trigger("click");
				//chooseLicence('regular', opt.price, opt.prepaid_price, 'block')
			}else{
				$("#licenitem-price").hide();
				$("#licenitem-extend").show();
				$("#purchase_button2").trigger("click");
				//chooseLicence('extended', opt.price, opt.prepaid_price, 'none')
			}
		});
		if($("#lchoose").parent().siblings(".licenitem").length === 2){
			$("#licenitem-extend").hide();
		}
		function chooseLicence(licence, price, prepaidprice, display) {
			$("#buynow-form input[name=licence], #prepaid-form input[name=licence]").val(licence);
			$("strong.buynow-figure").text("" + price);
			$("strong.prepaid-figure").text("" + prepaidprice);
        }

	}
	exports.init = init;

	function animatePanel(e){
		var t = e ? e : "show";
		$("div.fancy-purchase-panel, div.account-required.panel").animate({
			height: t,
			opacity: t,
			marginBottom: t
		}, "slow")
	}
	function confirm_purchase(e, t) {
		return confirm("您即将使用您的预付款余额购买 " + e + " (来自 " + t + " 分类)。\n\n请认真查看该作品的属性以确保该作品满足您的需求。当且仅当您尚未下载该作品而作品已经被删除的情形我们才接受退款。\n\n点击确定后您将立即获得该作品的下载权限。")
	}
	function submit_purchase_form(e) {
		var t = $(e).parent().siblings("form"),
		n = $("input[name=webtrends_si_n]", t),
		r = $("input[name=webtrends_si_x]", t);
		n.length === 1 && r.length === 1 && dcsMultiTrack("WT.si_n", n.val(), "WT.si_z", r.val()), t.submit()
	}
});