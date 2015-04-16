define(function (require, exports, module){
	var $ = require("jq"),
		resend = null,
	    nums = 60,
	    iscounting = false,
	    rule = {
	    	mail: /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/
	    }

	function btninit(opts){
		resend = opts.resetbtn;
		var setmail = opts.setmailbtn,
			mailipt = opts.mailipt,
			maillink = opts.maillink;
		resend.on("click", function(){
			if(resend.hasClass("disable") || iscounting){
				return;
			}
			var m = mailipt.val(),
				ds = {"res_send": "yes", "user_id": opts.uid};
			if(m != ""){
				if(!rule.mail.test(m)){
					mailipt.parent().siblings(".errtxt").html("邮箱格式不正确");
					resend.addClass("disable");
					return;
				}else{
					ds = {"res_mail": "yes", "user_id": opts.uid, "email": m};
				}
			}
			resend.addClass("disable").html("发送中");
			$.ajax({
				type: "post",
				url: opts.purl,
				data: ds,
				dataType: "json",
				success: function(data){
					if(data.status=='success'){
						if(data.mail){
							maillink.attr("href", data.mail);
						}
						nums = 60;
						iscounting = true;
						alert('发送成功，请注意查收');
						setTimeout(countdown, 1000);
					}else{
						alert("发送失败，请稍后重试");
						resend.removeClass("disable").html("重新发送");
					}
				}
			});
		});
		mailipt.on("keyup", function(){
			var that = $(this);
			if(that.val() != "" && !rule.mail.test(that.val())){
				that.parent().siblings(".errtxt").html("邮箱格式不正确");
				resend.addClass("disable");
			}else{
				that.parent().siblings(".errtxt").html("");
				if(!iscounting){
					resend.removeClass("disable");
				}
			}
		});
		setmail.on("click", function(){
			mailipt.parent().show();
			mailipt.focus();
		});
	}
	exports.btninit = btninit;

	function countdown(){
		nums--;
		resend.html("还有" + nums + "重新发送");
		if(nums == 0){
			iscounting = false;
			resend.removeClass("disable").html("重新发送");
		}else{
			setTimeout(countdown, 1000);
		}
	}
});