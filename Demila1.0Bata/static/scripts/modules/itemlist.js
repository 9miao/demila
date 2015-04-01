define(function (require, exports, module){
	var $ = require("jq"),
    cookie = require("plug/jquery.cookie")($),
		initialised = !1;

	function init(e){
    return e == null && (e = !1), initialised ? reset(e) : ($(document).on("change.sorter", ".sort_select", function() {
      return this.form.submit()
    }), sortDirection(), e && layoutSwitcher(), initialised = !0)
	}
	exports.init = init;


  function reset(e) {
    sortDirection();
    if(e) return setLayout($.cookie("item-layout"))
  }
  function sortDirection() {
    var e, t, n;
    t = $("#sort-direction-form");
    if(t.length) return n = t.data().order, e = $("#sort-direction"), t.hasClass("hidden") && $(".sort-direction-" + n).remove(), e.displayButtonAsLink({
      "class": "sort-control sort-control-tooltip sort-direction-" + n,
      "data-tooltip": e.text()
    })
  }
  function layoutSwitcher() {
    return $(document).on("click.sorter", ".layout-list, .layout-grid", function(e) {
      var t;
      return t = $(e.target), t.hasClass("active") ? !1 : t.hasClass("layout-list") ? ($(".item-grid").removeClass("item-grid").addClass("item-list"), $(".layout-switcher").find(".active").removeClass("active"), t.addClass("active"), $.cookie("item-layout", "list")) : ($(".item-list").removeClass("item-list").addClass("item-grid"), $(".layout-switcher").find(".active").removeClass("active"), t.addClass("active"), $.cookie("item-layout", "grid")), e.preventDefault()
    }), setLayout($.cookie("item-layout"))
  }
  function setLayout(e) {
    return e ? $(".layout-" + e).click() : $(".item-list").length ? $(".layout-list").addClass("active") : $(".layout-grid").addClass("active")
  }
});