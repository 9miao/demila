define(function (require, exports, module){
	var $ = require("jq"),
		ybro = require("modules/hbrowser"),
		theDef = require("default");

	function idxTopLbinit(opts){
		var $lbdom = opts.lbdom,
			$lbtn = opts.lbtn
		    $rbtn = opts.rbtn,
		    $lbimg = opts.lbimg,
		    $lblink = opts.lblink,
		    lbobjs = opts.lbobjs,
		    lbleng = lbobjs.length,
		    lbimgs = [],
		    lbidx = -1,
		    toplb = null,
		    imgloaded = 0;
		for(var i in lbobjs){
			var img = new Image();
			lbimgs.push(img);
			img.src = lbobjs[i].img;
			img.onload = function(){
				lbinit();
			};
		}
		function lbinit(){
			imgloaded++;
			//console.log(imgloaded);
			if(imgloaded != lbobjs.length){
				return;
			}
			$lbimg.html("");
			lbmake(true);
			toplb = setInterval(function(){lbmake(true)}, 6000);
			$lbdom.mouseover(function(){
				if(toplb){
			      clearInterval(toplb);
			      toplb = null;
			    }
			}).mouseout(function(){
				if(!toplb){
			      toplb = setInterval(function(){lbmake(true)}, 6000);
			    }
			}).parent().mouseover(function(){
			    $lbtn.show();
			    $rbtn.show();
			}).mouseout(function(){
			    $lbtn.hide();
			    $rbtn.hide();
			});
			$lbtn.click(function(){
				lbmake(false);
			});
			$rbtn.click(function(){
				lbmake(true);
			});
		}
		
		function lbmake(moves){
			if(moves){
				lbidx++;
				if(lbidx >= lbleng){
			      lbidx = 0;
			    }
			}else{
				lbidx--;
				if(lbidx < 0){
			      lbidx = lbleng - 1;
			    }
			}
			var tmp = lbobjs[lbidx],
				img = lbimgs[lbidx];
			$lbimg.fadeOut(function(){
				$lbimg.css({
					"background": "url(" + img.src + ") no-repeat center top"
				});
				$lblink.attr({
					href: tmp.url,
					title: tmp.txt
				}).html(tmp.txt);
				$lbimg.fadeIn();
			});
		}
	}
	exports.idxTopLbinit = idxTopLbinit;

	function idxFreeLbInit($doms){
		var $items = getItems(),
		    freefun;
		if($items.length > 10){
		  freefun = setInterval(lbfun, 5000);
		  $doms.mouseover(function(){
		    if(freefun){
		      clearInterval(freefun);
		      freefun = null;
		    }
		  }).mouseout(function(){
		    if(!freefun){
		      freefun = setInterval(lbfun, 5000);
		    }
		  });
		}
		function lbfun(){
		  $doms.animate({left: "-110px"}, function(){
		    var tmp = $items.first();
		    $items.first().remove();
		    $doms.append(tmp).css({left: "0px"});
		    $items = getItems();
		  });
		}
		function getItems(){
		  return $doms.find("li");
		}
	}
	exports.idxFreeLbInit = idxFreeLbInit;

	function formIptHolder($doms){
		for(var i in $doms){
			var $t = $doms[i];
			if($t.length == 0) return;
			$t.find("input[type='text']").each(function(){
				var $that = $(this),
					txt = $that.attr("place-holder");
				if(txt !== ""){
					$that.val(txt);
				}else{
					return;
				}
				$that.bind("focus", function(){
					if($(this).val() === txt){
						$(this).val("").addClass("isfocus");
					}
				}).bind("blur", function(){
					if($(this).val() === ""){
						$(this).val(txt).removeClass("isfocus");
					}
				});
			});
		}
	}
	exports.formIptHolder = formIptHolder;

	function navmenu($doms){
		for(var i in $doms){
			var $t = $doms[i];
			if(!$t || ($t && $t.length === 0)){
				return;
			}
			$t.find(".navlink").bind("mouseover", function(){
				if($(this).siblings("ol").length == 0){
					$(this).addClass("active2").siblings("ol").show();
				}else{
					$(this).addClass("active").siblings("ol").show();
				}
			}).bind("mouseout", function(){
				$(this).removeClass("active").removeClass("active2").siblings("ol").hide();
			});
			$t.find("ol").bind("mouseover", function(){
				$(this).show().siblings(".navlink").addClass("active").removeClass("active2");
			}).bind("mouseout", function(){
				$(this).hide().siblings(".navlink").removeClass("active").removeClass("active2");
			});
		}
	}
	exports.navmenu = navmenu;

	function ownFormSubmit($form, submitclass, checkform){
		var $subbtn = $form.find(submitclass);
		$subbtn.bind("click", function(){
			if(!checkform()){
				return;
			}
			$form.submit();
		});
	}
	exports.ownFormSubmit = ownFormSubmit;

	/*function pageMsgInit($doms, filter, $msg){//预览图片放大镜事件绑定
		for(var i in $doms){
			var $t = $doms[i];
			$t.find(filter).each(function(){
				var $that = $(this);
				$that.bind("mouseover", function(){
					var t = getElementTop($that.get(0)),
						l = getElementLeft($that.get(0));
					//console.log(l);
					msgshow($msg, $that, t - IMGMSG.mh + IMGMSG.pi.w, l + IMGMSG.pi.w);
					$msg.show();
				}).bind("mouseout", function(){
					//msgshow($msg, -1);
					$msg.hide();
				});
			});
		}
	}
	exports.pageMsgInit = pageMsgInit;*/

	function selectInit($dom, items, onchange){//下拉列表元素事件初始化
		if(items.length !== 0){
			var tmp = "";
			for(var i in items){
				var j = items[i];
				tmp += "<option value='" + j.id +  "'>" + j.txt + "</option>";
			}
			$dom.html(tmp);
		}
		if(onchange && typeof(onchange) === "function"){
			$dom.bind("change", function(){
				onchange($(this).val());
			});
		}
	}
	exports.selectInit = selectInit;

	function checkboxInit($dom, filter){//自定义复选框初始化
		var $checks = $dom.find(filter);
		$checks.each(function(){
			var $that = $(this);
			$that.bind("click", function(){
				$that.toggleClass("active").find("input").val($that.hasClass("active") ? 1: 0);
			});
		});
	}
	exports.checkboxInit = checkboxInit;

	function radioboxInit($dom, filter, radioclass){//自定义单选框初始化
		var $radioes = $dom.find(filter);
		$radioes.each(function(){
			var $that = $(this);
			$that.find(radioclass).bind("click", function(){
				$(this).find(".radioimg").addClass("active");
				$(this).siblings(radioclass).find(".radioimg").removeClass("active");
				$(this).siblings("input").val($(this).find(".radiotxt").html());
			});
		});
	}
	exports.radioboxInit = radioboxInit;

	function authorTestInit($form, $testbtn){
		$testbtn.bind("click", function(){
			var t = true;;
			$form.find("input").each(function(){
				if($(this).val() === "-1"){
					alert("请填写完整问题");
					t = false;
					return false;
				}
			});
			if(t){
				$form.submit();
			}
			return false;
		});
	}
	exports.authorTestInit = authorTestInit;

	function navSearchHide(){
		$("#search").parent().hide();
	}
	exports.navSearchHide = navSearchHide;

	function imgMagnifier(filter){
		if(checkMobile()){
			return;
		}
		$(filter).each(function(){
			var that = $(this);
				imgs = that.find("img");
			that.on("mouseenter", function() {
				showMagnifier($(this).find("img"));
			}).on("mouseleave", function() {
				hideMagnifier($(this).find("img"));
			});
			if(!theDef.checkImage(imgs.attr("data-preview-url"))){
				that.append("<span class='controls'></span>");
			}
		});
	}
	exports.imgMagnifier = imgMagnifier;


	function idxRecomLbInit($doms){
		var $itemobj = $doms.find("#recomitems"),
			$items = $itemobj.find("li"),
			$lbtn = $doms.find("#recomlbtn"),
			$rbtn = $doms.find("#recomrbtn"),
			nums = $items.length,
			m = 0;
		if(nums <= 10){
			$lbtn.remove();
			$rbtn.remove();
			return;
		}else if(nums % 10 != 0){
			var tmp = nums % 10,
				t = "";
			for(var i = 1, len = 10 - tmp; i <= len; i++){
				t += emptyObj();
			}
			$itemobj.append(t);
			$items = $itemobj.find("li");
			nums = $items.length;
		}
		$itemobj.css("width", nums * 110 + "px");
		$lbtn.bind("click", function(){
			if($itemobj.attr("animating") == "1"){
				return;
			}
			if($lbtn.hasClass("disable")){
				return;
			}
			m -= 10;
			$itemobj.attr("animating", "1").animate({left: m * 110 * -1}, 500, function(){
				$itemobj.removeAttr("animating");
			});
			if(m == nums - 10){
				$rbtn.addClass("disable");
			}else{
				$rbtn.removeClass("disable");
			}
			if(m == 0){
				$lbtn.addClass("disable");
			}else{
				$lbtn.removeClass("disable");
			}

		});
		$rbtn.bind("click", function(){
			if($itemobj.attr("animating") == "1"){
				return;
			}
			if($rbtn.hasClass("disable")){
				return;
			}
			m += 10;
			$itemobj.attr("animating", "1").animate({left: m * 110 * -1}, 500, function(){
				$itemobj.removeAttr("animating");
			});
			if(m == nums - 10){
				$rbtn.addClass("disable");
			}else{
				$rbtn.removeClass("disable");
			}
			if(m == 0){
				$lbtn.addClass("disable");
			}else{
				$lbtn.removeClass("disable");
			}
		});
		function emptyObj(){
			return "<li class='thumbnail'></li>";
		}
	}
	exports.idxRecomLbInit = idxRecomLbInit;

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

	function ajaxupload_init_old(pts, $cleanbtn, is_extend){
		require.async(["ajaxup", "ajaxupque", "ajaxupfile", "ajaxuphand"], function(ajman, ajque, ajfile, ajhand){
			ajman();
			ajque();
			ajfile();
			$.extend(pts, {
				file_queued_handler : ajhand.fileQueued,
				file_queue_error_handler : ajhand.fileQueueError,
				file_dialog_complete_handler : ajhand.fileDialogComplete,
				upload_start_handler : ajhand.uploadStart,
				upload_progress_handler : ajhand.uploadProgress,
				upload_error_handler : ajhand.uploadError,
				upload_success_handler : ajhand.uploadSuccess,
				upload_complete_handler : ajhand.uploadComplete,
				queue_complete_handler : ajhand.queueComplete
			})
			ajhand.setExtend(is_extend);
			ajhand.checkSelect();
			//ajhand.selectClickInit();
			var swfu = new SWFUpload(pts);
			$cleanbtn.on("click", function(){
				swfu.cancelQueue();
			});

		});
	}
	exports.ajaxupload_init_old = ajaxupload_init_old;

	function itemlistAjax($btns, $list, $loading){
		var itemlistcach = {};
		$btns.each(function(){
			var v = $(this).attr("cate-id");
			$(this).on("click", function(){
				if($list.attr("ajax-ing") == "1"){
					return false;
				}
				var that = $(this);
				if(that.hasClass("active")){
					return false;
				}
				that.addClass("active").parent().siblings("li").find("a").removeClass("active");
				if(itemlistcach[v] && itemlistcach[v] != ""){
					$list.html(makeItemHtml(itemlistcach[v]));
				}else{
					$list.attr("ajax-ing", "1");
					$list.css("opacity", "0.6");
					$loading.show();
					$.post("/apps/categories/ajax/categories.php?times=" + ~(-new Date()/36e5), {categoryID: v}, function(data){
						$list.removeAttr("ajax-ing");
						itemlistcach[v] = data.data;
						$loading.hide();
						$list.css("opacity", "1");
						$list.html(makeItemHtml(data.data));
						imgMagnifier($list.find("li.landscape-image-magnifier"));
					}, "json");
				}
				return false;
			});
		});
	}
	exports.itemlistAjax = itemlistAjax;

	function uploadInit(url, sessid, funtype){
		var domid = [],
			filetype = ["thumbnail", "main_file", "first_preview", "theme_preview"],
			nums = ["single", "single", "single", "multiple"],
			showtype = ["image", "zip", "image", "prev"],
			issubmit = false;
		if(funtype === "edit"){
			window.onbeforeunload = function(){
				if(!issubmit){
			    	return ("您的修改尚未保存，确定离开此页面？");
				}
			}
			$("form").on("submit", function(){
				issubmit = true;
			});
			domid = ["#thumbnail_edit", "#main_file_edit", "#first_preview_edit", "#theme_preview_edit"];
		}else{
			domid = ["#thumbnail", "#main_file", "#first_preview", "#theme_preview"];
		}
		if(window.FormData === undefined){
			require.async(["modules/ajaxupload_old"], function(upload){
				upload.init(domid, filetype, nums, showtype, url, sessid, funtype);
			});
		}else{
			require.async(["modules/ajaxupload"], function(upload){
				upload.init(domid, filetype, nums, showtype, url, sessid, funtype);
			});
		}
	}
	exports.uploadInit = uploadInit;


	//私有属性
	var IMGMSG = {mw: 494, mh: 339, pi: {w: 90, h: 90}, pw: 1080},
		price_prefix = "";
	function checkMobile(){
		return ybro.versions.mobile || ybro.versions.iPad;
	}
	function makeItemHtml(data){
		var tmp = "";
		for(var i = 0, len = data.length; i < len; i++){
			var j = data[i];
			tmp += "<li class='thumbnail landscape-image-magnifier'>\
				<a href='/items/" + j.id + "'>\
				<img alt='" + j.name + "' border='0' class='preload no_preview' data-item-author='作者 " + (j.user_info)["item-author"] + "' data-item-category='" + getCateStr(j.item_categories) + "' data-item-cost='" + j.price + "' data-item-name='" + j.name + "' data-preview-height='' data-preview-url='/uploads/items/" + j.id + "/preview.jpg' data-preview-width='' src='/uploads/items/" + j.id + "/" + j.thumbnail + "' title='" + j.name + "' />\
				</a>\
				</li>";
		}
		return tmp;
	}
	function getCateStr(cates){
		var tmp = "";
		for(var i = 0, len = cates.length; i < len; i++){
			tmp += cates[i];
			if(i != len - 1){
				tmp += " \\ ";
			}
		}
		return tmp;
	}
	function showMagnifier(e) {
		$(e).attr("data-tooltip") === undefined && ($(e).attr("data-tooltip", $(e).attr("title")), $(e).attr("title", ""), $("img", e).attr("title", "")), populateMagnifierFrom(e), positionMagnifierNextTo(e), magnifierDiv().css({
			display: "inline"
		});
	}
	function hideMagnifier() {
		magnifierDiv().hide()
	}
	function magnifierDiv() {
		return $("div#landscape-image-magnifier");
	}
	function positionMagnifierNextTo(e) {
		var t, n, r;
		t = magnifierDiv();
		n = $(e).offset().top + $(e).outerHeight() - t.outerHeight(), n < $(window).scrollTop() && (n = $(window).scrollTop()), $(e).offset().left + $(e).outerWidth() / 2 >= $(window).width() / 2 ? r = $(e).offset().left - t.outerWidth() : r = $(e).offset().left + $(e).outerWidth(), t.css({
			top: n,
			left: r
		});
	}
	function populateMagnifierFrom(e) {
		bindMetaData(e);
		var t,
			n = magnifierDiv(),
			r = n.find("div.size-limiter"),
			tit = n.find("strong"),
			i = $(e),
			path = i.attr("data-preview-url"),
			free = i.attr("item-type-free");
		if(free == "1"){
			n.find(".price").addClass("freepri");
		}else{
			n.find(".price").removeClass("freepri");
		}
		if(theDef.checkImage(path)){
			t = new Image;
			$(t).attr("src", path);
			r.show().empty().append(t);
			tit.removeClass("autow");
		}else{
			r.hide();
			tit.addClass("autow");
		}
    }
	function bindMetaData(e) {
		var t = $(e),
			n = magnifierDiv(),
			r,
			i,
			s = n.find("strong").empty(),
			o = n.find(".author").empty(),
			u = n.find(".category").empty(),
			a = n.find(".cost").empty(),
			f = n.find(".info");
		i = t.attr("data-item-cost");
		r = typeof $(e).attr("data-item-cost") != "undefined";
		s.html(t.attr("data-item-name"));
		o.html(t.attr("data-item-author"));
		u.html(t.attr("data-item-category"));
		a.html(r ? price_prefix + i : i);
	}
	/*function msgshow($msg, $img, t, l){//放大镜内容显示及位置控制
		if($img === -1){
			$msg.find(".msgimg").attr("src", "").end().find(".msgtit").html("").end().find("msgauthor").html("").end().find("msgtag").html("").end().find(".costnum").html("");
		}else{
			$msg.find(".msgimg").attr("src", $img.attr("msg-img")).end()
				.find(".msgtit").html($img.attr("msg-tit")).end()
				.find(".msgauthor").html($img.attr("msg-author")).end()
				.find(".msgtag").html($img.attr("msg-tag")).end()
				.find(".costnum").html($img.attr("msg-cost"));
			var st = $(document).scrollTop(),
				ph = $(window).height(),
				pw = $(window).width();
			if(t < st){
				t = st;
			}else if((t + IMGMSG.mh) > (st + ph)){
				t = st + ph - IMGMSG.mh;
			}
			if((l + IMGMSG.mw) > ((pw - IMGMSG.pw) / 2 + IMGMSG.pw)){
				l = l - IMGMSG.pi.w - IMGMSG.mw;
			}
			$msg.css({top: t + "px", left: l + "px"});
		}
	}*/
	function getElementLeft(element){
		var actualLeft = element.offsetLeft;
		var current = element.offsetParent;
		while (current !== null){
			actualLeft += current.offsetLeft;
			current = current.offsetParent;
		}
		return actualLeft;
	}
	function getElementTop(element){
		var actualTop = element.offsetTop;
		var current = element.offsetParent;
		while (current !== null){
			actualTop += current.offsetTop;
			current = current.offsetParent;
		}
		return actualTop;
	}
});