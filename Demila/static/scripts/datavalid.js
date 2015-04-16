define(function (require, exports, module){
	var $ = require("jq"),
		postURL = "/",
		ajaxUrls = {
			nickname: ""
		},
		rules = {
			"uName": /^[A-Za-z0-9]*$/,
			"uNick": /^[A-Za-z0-9\u4E00-\u9FFF]*$/,
			"email": /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,
			"photo": /^[1][3|5|8]\d{9}$/
		};

	function setAjaxUrl(urls){
		$.extend(ajaxUrls, urls);
	}
	exports.setAjaxUrl = setAjaxUrl;

	function getunameInit($form){
		var email = $form.find("#email"),
			formbtn = $form.find("#getunamebtn");

		bindEmail(email);
		bindAsClick(formbtn, function(){
			if(cEmail(email)){
				return false;
			}
			formbtn.addClass("clicked").attr("form-clicked", "true");
			$form.submit();
			return false;
		}, $form);
	}
	exports.getunameInit = getunameInit;

	function resetpwdInit($form){
		var uname = $form.find("#username"),
			email = $form.find("#email"),
			formbtn = $form.find("#resetpwdbtn");

		bindEmpty(uname);
		bindEmpty(email);
		bindAsClick(formbtn, function(){
			if(cEmpty(uname) || cEmpty(email)){
				return false;
			}
			formbtn.addClass("clicked").attr("form-clicked", "true");
			$form.submit();
			return false;
		}, $form);
	}
	exports.resetpwdInit = resetpwdInit;

	function placeholder($form){
		$form.find("input").each(function(e){
			var that = $(this),
				holder = getPlaceHolderHtml(),
				htxt = that.attr("place-holder");
			if(!!htxt && htxt != ""){
				if(that.val() == ""){
					that.after(holder.html(htxt));
				}
				that.bind("focus", function(){
					holder.remove();
				}).bind("blur", function(){
					if(that.val() == ""){
						that.after(holder.html(htxt));
					}
				});
			}
		});
	}
	exports.placeholder = placeholder;

	function registerInit($form){
		var uname = $form.find("#username"),
			unick = $form.find("#nickname"),
			umail = $form.find("#usermail"),
			upwd = $form.find("#userpwd"),
			upwd2 = $form.find("#userpwd2"),
			yzm = $form.find("#yzm"),
			//xys = [$form.find("#radioval1"), $form.find("#radioval2")],
			xys = [$form.find("#radioval1")],
			formbtn = $form.find("#regbtn");

		bindUname(uname, formBtnAble);
		bindUnick(unick, formBtnAble);
		bindEmail(umail, formBtnAble);
		bindPwd(upwd, formBtnAble);
		bindRPwd(upwd2, upwd, formBtnAble);
		bindYZM(yzm, formBtnAble);
		bindAsClick(formbtn, function(){
			if(formbtn.hasClass("disable")){
				return false;
			}
			if(checkForm()){
				return false;
			}
			formbtn.addClass("clicked").attr("form-clicked", "true");
			$form.submit();
			return false;
		}, $form);
		function checkForm(){
			return cXy(xys) || cUname(uname) || cUnick(unick) || cEmail(umail) || cPwd(upwd) || cRPwd(upwd2, upwd) || cEmpty(yzm);
		}
		function formBtnAble(pass){
			if(!pass || checkForm()){
				formbtn.addClass("disable");
			}else{
				formbtn.removeClass("disable");
			}
		}
	}
	exports.registerInit = registerInit;

	function loginInit($form){
		//console.log($form);
		var uname = $form.find("#username"),
			upwd = $form.find("#password"),
			yzm = $form.find("#yzm"),
			formbtn = $form.find("#loginbtn");

		//bindPhone(uname);
		//bindPwd(upwd);
		bindEmpty(uname);
		bindEmpty(upwd);
		bindEmpty(yzm);
		bindAsClick(formbtn, function(){
			if(cEmpty(uname) || cEmpty(upwd) || cEmpty(yzm)){
				return false;
			}
			formbtn.addClass("clicked").attr("form-clicked", "true");
			$form.submit();
			return false;
		}, $form);
	}
	exports.loginInit = loginInit;

	function supportInit($form){
		var uname = $form.find("#username"),
			umail = $form.find("#usermail"),
			yzm = $form.find("#yzm"),
			supporttype = $form.find("#supporttype"),
			supportdetail = $form.find("#supportdetail"),
			formbtn = $form.find("#supportbtn");

		bindPhone(uname);
		bindEmail(umail);
		bindYZM(yzm);
		bindEmpty(supportdetail);

		bindAsClick(formbtn, function(){
			if(cPhone(uname) || cEmail(umail) || cEmpty(supportdetail) || cSelval(supporttype) || cEmpty(yzm)){
				return false;
			}
			formbtn.addClass("clicked").attr("form-clicked", "true");
			$form.submit();
			return false;
		});
	}
	exports.supportInit = supportInit;

	function yzminit($yzmimg, $btns){
		$($btns).each(function(){
			$(this).on("click", function(){
				var imgpath = $yzmimg.attr("src");
				$yzmimg.attr("src", imgpath + '?random=' + Math.random());
			});
		});
	}
	exports.yzminit = yzminit;

	function getPlaceHolderHtml(){
		return $("<span class='placeholder'></span>");
	}
	function bindUname($obj, callback){//为个性账号input绑定验证事件，判断是否为空，格式是否正确，并使用ajax验证
		//console.log($obj.on);
		var s = this;
		$obj.bind("blur", function(){
			var value = $(this).val();
			if(checkEmpty(value)){
				errorShow($(this), "账号不能为空");
			}else if(!checkUname(value)){
				errorShow($(this), "账号格式不正确");
			}else if(!checkStrLen(value, 15, true)){
				errorShow($(this), "账号过长，不能超过15个字符");
			}else if(!checkStrLen(value, 4, false)){
				errorShow($(this), "账号过短，不能少于4个字符");
			}else{
				loadingShow($obj);
				$obj.attr("ajax-loading", "1");
				if(callback){
					callback(false);
				}
				$.post(postURL + ajaxUrls.username, {username: value, action: "check"}, function(data){
					$obj.removeAttr("ajax-loading");
					loadingHide($obj);
					if(data == "1"){
						errorShow($obj, "账号已经被占用");
						if(callback){
							callback(false);
						}
					}else if(data == "0"){
						passShow($obj);
						if(callback){
							callback(true);
						}
					}else{

					}
				});
			}
		});
	}
	function bindUnick($obj, callback){
		var s = this;
		$obj.bind("blur", function(){
			var value = $(this).val();
			if(checkEmpty(value)){
				errorShow($(this), "昵称不能为空");
			}else if(!checkUnick(value)){
				errorShow($(this), "昵称格式不正确");
			}else if(!checkStrLen(value, 15, true)){
				errorShow($(this), "昵称过长，不能超过15个字符");
			}else if(!checkStrLen(value, 2, false)){
				errorShow($(this), "昵称过短，不能少于2个字符");
			}else{
				passShow($obj);
			}
		});
	}
	function bindEmail($obj, callback){//为邮箱账号input绑定验证事件，验证是否为空，格式是否正确，以及ajax验证
		if($obj.length == 0){
			return;
		}
		var s = this;
		$obj.bind("blur", function(){
			var value = $(this).val();
			if(checkEmpty(value)){
				errorShow($(this), "邮箱地址不能为空");
			}else if(!checkEmail(value)){
				errorShow($(this), "邮箱格式不正确");
			}else{
				passShow($obj);
				if(callback){
					callback(true);
				}
			}
		});
	}
	function bindPhone($obj, callback){
		$obj.bind("blur", function(){
			var value = $(this).val();
			if(checkEmpty(value)){
				errorShow($(this), "手机号码不能为空");
			}else if(!checkPhone(value)){
				errorShow($(this), "手机号码格式不正确");
			}else{
				passShow($obj);
				if(callback){
					callback(true);
				}
			}
		});
	}
	function bindPwd($obj, callback){//为密码input绑定验证事件，验证是否为空，是否合法，验证格式参照正则
		$obj.bind("blur", function(){
			if(checkEmpty($(this).val())){
				errorShow($(this), "密码不能为空");
			}else if(!checkStrLen($(this).val(), 20, true)){
				errorShow($(this), "密码过长，不能超过20个字符");
			}else if(!checkStrLen($(this).val(), 5, false)){
				errorShow($(this), "密码过短，不能少于5个字符");
			}else{
				passShow($(this));
				if(callback){
					callback(true);
				}
			}
		});
	}
	function bindRPwd($obj, $robj, callback){//为重复密码input绑定验证事件，验证是否为空，是否两次输入相同
		$obj.bind("blur", function(){
			if(checkEmpty($(this).val())){
				errorShow($(this), "密码不能为空");
			}else if(!checkRPwd($(this).val(), $robj.val())){
				errorShow($(this), "两次密码输入不一致");
			}else{
				passShow($(this));
				if(callback){
					callback(true);
				}
			}
		});
	}
	function bindYZM($obj, callback){//为验证码input绑定验证事件，包括cookie验证
		$obj.bind("blur", function(){
			if(checkEmpty($(this).val())){
				errorShow($(this), "验证码不能为空");
			}else{
				passShow($(this));
				if(callback){
					callback(true);
				}
			}
		});
		$obj.bind("keydown", function(){
			if(callback){
				callback(true);
			}
		});
	}
	function bindAsClick($obj, clickFun, form, keydown){
		$obj.bind("click", function(){
			if($(this).attr("form-clicked") == "true"){
				return false;
			}
			clickFun();
			return false;
		});
		if(form){
			form.find("input").bind("keydown", function(e){
				if(e.keyCode === 13){
					$obj.trigger("click");
				}
			});
		}
	}
	function bindEmpty($obj){
		$obj.bind("blur", function(){
			if(checkEmpty($(this).val())){
				errorShow($(this), $(this).attr("data-type") + "不能为空");
			}else{
				passShow($(this));
			}
		});
	}
	function cUname($obj){//判断input输入的值是否满足个性账号的要求，包括非空及格式
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, "账号不能为空");
		}else if(!checkUname($obj.val())){
			t = true;
			errorShow($obj, "账号格式不正确");
		}else if(!checkStrLen($obj.val(), 15, true)){
			t = true;
			errorShow($obj, "账号过长，不能超过15个字符");
		}else if(!checkStrLen($obj.val(), 4, false)){
			t = true;
			errorShow($obj, "账号过短，不能少于4个字符");
		}else{
			passShow($obj);
		}
		return t
	}
	function cUnick($obj){
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, "昵称不能为空");
		}else if(!checkUnick($obj.val())){
			t = true;
			errorShow($obj, "昵称格式不正确");
		}else if(!checkStrLen($obj.val(), 15, true)){
			t = true;
			errorShow($obj, "昵称过长，不能超过15个字符");
		}else if(!checkStrLen($obj.val(), 2, false)){
			t = true;
			errorShow($obj, "昵称过短，不能少于2个字符");
		}else{
			passShow($obj);
		}
		return t
	}
	function cEmail($obj){//判断input输入的值是否非空，且是否满足邮箱的格式
		if($obj.length == 0){
			return false;
		}
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, "邮箱地址不能为空");
		}else if(!checkEmail($obj.val())){
			t = true;
			errorShow($obj, "邮箱格式不正确");
		}else{
			passShow($obj);
		}
		return t;
	}
	function cPhone($obj){
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, "手机号码不能为空");
		}else if(!checkPhone($obj.val())){
			t = true;
			errorShow($obj, "手机号码格式不正确");
		}else{
			passShow($obj);
		}
		return t;
	}
	function cPwd($obj){//判断input输入的值是否非空，且是否满足密码格式要求
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, "密码不能为空");
		}else if(!checkStrLen($obj.val(), 20, true)){
			t = true;
			errorShow($obj, "密码过长，不能超过20个字符");
		}else if(!checkStrLen($obj.val(), 5, false)){
			t = true;
			errorShow($obj, "密码过短，不能少于5个字符");
		}else{
			passShow($obj);
		}
		return t;
	}
	function cRPwd($obj, $robj){//判断input输入的值是否非空，且是否满足两次输入密码一致
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, "密码不能为空");
		}else if(!checkRPwd($obj.val(), $robj.val())){
			t = true;
			errorShow($obj, "两次密码输入不一致");
		}else{
			passShow($obj);
		}
		return t;
	}
	function cEmpty($obj){//判断input输入的值是否非空
		var t = checkEmpty($obj.val());
		if(t){
			errorShow($obj, $obj.attr("data-type") + "不能为空！");
		}else{
			passShow($obj);
		}
		return t;
	}
	function cSelval($obj){
		//var t = ($obj.siblings(".selhidval").val() == "0");
		var t = ($obj.val() == "0");
		if(t){
			errorShow($obj, "请选择问题分类");
		}else{
			passShow($obj);
		}
		return t;
	}
	function cXy(objs){//协议自定义复选框判断
		var t = true;
		for(var i in objs){
			var $obj = objs[i];
			if($obj.val() == "0"){
				t = false;
				errorShow($obj, "请同意协议");
			}
			if(t){
				allTipHide($obj);
			}
		}
		return !t;
	}
	function allTipHide($obj){
		$obj.parent().siblings(".suctxt").hide();
		$obj.parent().siblings(".errtxt").hide();
	}
	function isChinese(str){
		var lst = /[u00-uFF]/;
		return !lst.test(str);
	}
	function checkStrLen($val, len, checkfun){
		var strlength = 0;
		for(var i = 0; i < $val.length; ++i){
			if(isChinese($val.charAt(i)) === true){
				strlength += 1;//中文计算为字符
			}else{
				strlength += 1;
			}
		}
		return checkfun? (strlength <= len) : (strlength >= len);
	}
	function checkEmail($val){
		return rules.email.test($val);
	}
	function checkPhone($val){
		return rules.photo.test($val);
	}
	function checkUname($val){
		return rules.uName.test($val);
	}
	function checkUnick($val){
		return rules.uNick.test($val);
	}
	function checkEmpty($val){
		return $val == "";
	}
	function checkPwd($val){
		var pl = $val.length;
		return pl >= 5 && pl <= 20;
	}
	function checkRPwd($val, $rval){
		return $val == $rval;
	}
	function errorShow($obj, txt){
		$obj.parent().siblings(".suctxt").hide().siblings(".errtxt").show().html(txt);
	}
	function passShow($obj){
		$obj.parent().siblings(".errtxt").hide().siblings(".suctxt").show();
	}
	function loadingShow($obj){
		$obj.parent().siblings(".suctxt").hide().siblings(".errtxt").hide().siblings(".loading").show();
	}
	function loadingHide($obj){
		$obj.parent().siblings(".loading").hide();
	}
});