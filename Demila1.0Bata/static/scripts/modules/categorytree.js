define(function (require, exports, module){
	var $ = require("jq");
	function init(){
		var e = new CategoryTree
		e.setupCategoryTree("#first");
		$("#first ul:first li").on("click", function(){
			return $("#second").empty(), e.open_next($("a", this).first(), "#second"), $("#third").empty().addClass("empty"), $("#fourth").empty().addClass("empty")
		});
		$(".container").on("click", "#second li", function(){
			return e.open_next($("a", this).first(), "#third"), $("#fourth").empty().addClass("empty");
		});
		$(".container").on("click", "#third li", function(){
			return e.open_next($("a", this).first(), "#fourth");
		});
		$("li.expandable > a").on("click", function(){
			return $(this).parent().trigger("click"), !1
		});
		$(".container").on("click", "li.expandable > a", function(){
			return $(this).parent().trigger("click"), !1
		});
	}
	exports.init = init;

    var CategoryTree = function(){
		var e = $(this);
		return {
			setupCategoryTree: function(e){
				$("a", e).each(function() {
					if($("li", $("> ul", $(this).parent())).length){
						var e = $(this).parent();
						e.addClass("expandable");
					}
				})
			},
			open_next: function(e, t){
				var n = $(e),
					r = $(t),
					i = $("ul", r),
					s = $("> ul", n.parent()).children().clone();
				if(n.hasClass("all-category") || $("li", $("> ul", n.parent())).length === 0) return;
				n.parent().parent().find(".active").removeClass("active"), n.parent().addClass("active"), r.removeClass("empty"), i.empty(), $("ul", r).length || (i = $("<ul></ul>"), r.append(i)), i.append('<li><a href="' + e.get(0) + '" class="all-category">全部 ' + $(e.get(0)).html() + "</a></li>"), i.append(s), i.is(":empty") && (i.remove(), r.addClass("empty"));
			}
		}
	};
});