define(function (require, exports, module){
	var $ = require("jq"),
		starOnUrl = "",
		starOffUrl = "";
	function init(e){
		starOnUrl = "/static/img/custom/star-on.png";
		starOffUrl = "/static/img/custom/star-off.png";
		initEvents($("#" + e));
	}
	exports.init = init;

    function initEvents(e){
    	e.find("a").on("mouseover", function(){
    		toggle_stars(e.attr("data-star-set-id"), $(this).index() + 1);
    	});
      $(".stars", ".rating-container").on("mouseleave", function(t){
        var n;
        return n = $(t.currentTarget), reset_stars(n.attr("data-star-set-id"), n.attr("data-rating"))
      }).on("ajax:success", function(e, t, n) {
        return $("#rate_collection").html(t);
      }).find("a").on("click", function() {
        var e = $(this);
        e.parent().attr("data-rating", e.index() + 1);
        $.ajax({
        	type: "post",
    			url: e.attr("href"),
    			dataType: "script",
    			success: function(data){
    				eval(data);
    			}
        });
        return false;
      });
    }
    function toggle_stars(e, t) {
      var n, r, i, s;
      s = [];
      for(r = i = 1; i <= 5; r = ++i) n = "" + e + "_" + r, r > t ? s.push(turn_off_star(n)) : s.push(turn_on_star(n));
      return s
    }
    function turn_on_star(e) {
      return $("#" + e).attr("src", starOnUrl)
    }
    function turn_off_star(e) {
      return $("#" + e).attr("src", starOffUrl)
    }
    function reset_stars(e, t) {
      return toggle_stars(e, t)
    }
});