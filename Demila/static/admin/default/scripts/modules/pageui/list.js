define(function (require, exports, module){
	var $ = require("jq"),
		openedlist = null;

	function clickListInit(domf, btnf){
		var $dom = $(domf),
			$b = $dom.find(btnf);
		$b.on("click", function(){
			var that = $(this).parent();
			if(that.hasClass("active")){
				that.removeClass("active");
				openedlist = null;
				$(document).off("click");
			}else{
				if(openedlist){
					openedlist.removeClass("active");
				}
				that.addClass("active");
				openedlist = that;
				$(document).on("click", hideList);
			}
			return false;
		});
		function hideList(e){
			var that = $(e.target);
			if($dom.hasClass("active") && $dom.find(that).length == 0){
				$dom.removeClass("active");
				$(document).off("click");
			}
		}
	}
	exports.clickListInit = clickListInit;

	function leftListInit(opt){
		require.async(["plug/jquery.sly/sly"], function(sly){
			sly($);
			var mainobj = $(opt.leftobjf),
				swbtn = mainobj.find(opt.switchf),
				lists = mainobj.find(opt.menulist),
				tlogo = $(opt.toplogof),
				scrdom = $(opt.scrolldom),
				scrbar = $(opt.scrollbar),
				menuanime = false,
				anima_l = false,
				anima_n = false,
				leftswitch = false;

			swbtn.on("click", function(){
				var mpar = mainobj.parent(),
					tpar = tlogo.parent();
				if(mpar.hasClass("narrow")){
					mpar.removeClass("narrow");
					tpar.removeClass("narrow");
					leftswitch = false;
					scrdom.sly({
						scrollBar: scrbar,
						scrollBy: 100,
						startAt: 0
					});
				}else{
					mpar.addClass("narrow");
					tpar.addClass("narrow");
					leftswitch = true;
					lists.find(".menunav").hide();
					scrdom.sly("destroy").removeAttr("style");
				}
			});

			scrdom.sly(false);
			scrdom.sly({
				scrollBar: scrbar,
				scrollBy: 100,
				startAt: 0
			});
			//scrdom.sly("reload");
			$(window).on("resize", function(){
				scrdom.sly("reload");
			});

			lists.find(".menutip").on("click", function(){
				if(leftswitch) return;
				if(menuanime) return;
				var that = $(this),
					thatPar = that.parent();
				menuanime = true;
				if(thatPar.hasClass("active")){
					anima_l = true;
					anima_n = false;
					that.siblings(".menunav").slideUp(400, "swing", function(){anima_l = false;});
					thatPar.removeClass("active");
				}else{
					anima_l = true;
					anima_n = true;
					if(thatPar.siblings("li.active").find(".menunav").length == 0){
						anima_l = false;
					}
					that.siblings(".menunav").slideDown(400, "swing", function(){anima_n = false;});
					thatPar.addClass("active").siblings("li.active").removeClass("active").find(".menunav").slideUp(400, "swing", function(){anima_l = false;});
				}
				setTimeout(animaEnd, 200);

				function animaEnd(){
					if(!anima_l && !anima_n){
						menuanime = false;
						anima_l = true;
						anima_n = true;
						scrdom.sly("reload");
					}else{
						setTimeout(animaEnd, 200);
					}
				}
			});
			lists.children("li").each(function(){
				var t = $(this);
				t.on("mouseover", function(){
					if(!leftswitch) return;
					var that = $(this);
					that.find(".menunav").show();
				}).on("mouseout", function(){
					if(!leftswitch) return;
					var that = $(this);
					that.find(".menunav").hide();
				});
				if(t.hasClass("active")){
					t.find(".menunav").show();
					scrdom.sly("reload");
				}
			});
		});
	}
	exports.leftListInit = leftListInit;
});