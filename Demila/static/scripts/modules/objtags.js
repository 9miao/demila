define(function (require, exports, module){
	var $ = require("jq");

	function tagsInit(tagfilter, tagtipfilter, tagaddfilter, tagvals, alltags, readonly){
		var $tags = $(tagfilter),
			$tagtip = $(tagtipfilter),
			$tagadd = $(tagaddfilter),
			tagtxt = $tags.val(),
			tagnums = alltags.length;
		if(readonly){
			$tagtip.find(".tageditbtn").hide();
		}
		makeTagHtml($tags, $tagtip, tagvals, readonly);
		$tagtip.find(".tageditbtn").on("click", function(){
			$tagadd.show();
		});
		$tagtip.on("click", ".tagtipobj .delbtn", function(){
			$(this).parent().remove();
			makeTagvl($tags, $tagtip);
		});
		$tagadd.find(".addtagsbtn").on("click", function(){
			$tagadd.hide();
		});
		$tagadd.find("#addtagipt").on("keyup", function(e){
			if(e.keyCode == 13){
				return false;
			}
			var word = $(this).val(),
				t = makeTaglistHtml(word, alltags, tagnums);
			if(t != ""){
				$tagadd.find("#addtaglist").html(t).show();
			}else{
				$tagadd.find("#addtaglist").html(t).hide();
			}
		}).on("focus", function(){
			var t = makeTaglistHtml($(this).val(), alltags, tagnums);
			if(t != ""){
				$tagadd.find("#addtaglist").html(t).show();
			}else{
				$tagadd.find("#addtaglist").html(t).hide();
			}
		});
		$tagadd.find("#addtaglist").on("mouseover", ".addtagobj", function(){
			$(this).addClass("hover").siblings(".addtagobj").removeClass("hover");
		}).on("click", ".addtagobj", function(){
			var that = $(this),
				val = that.html();
			$tagadd.find("#addtaglist").html("").hide().siblings("#addtagipt").val("");
			if(($tagtip.find(".tagtipobj").length < 6) && !checkSameTag(that.attr("tag-id"), $tags)){
				$tagtip.find(".tageditbtn").before(getTabObjHtml({id: that.attr("tag-id"), name: val}));
				makeTagvl($tags, $tagtip);
			}
		});

	}
	exports.tagsInit = tagsInit;

	function makeTagvl(tag, tip){
		var t = [];
		tip.find(".tagtipobj").each(function(){
			t.push($(this).attr("tag-id"));
		});
		tag.val(t.join(","));
	}
	function makeTaglistHtml(key, all, tagnums){
		//console.log(all);
		if(key == ""){
			//return "";
		}
		var tmp = [],
			thtml = "";
		for(var i = 0, len = all.length; i < len; i++){
			if(all[i].name.indexOf(key) >= 0 && $("#tagstxt").val().indexOf(all[i].id) < 0){
				tmp.push(all[i]);
			}
		}
		if(tmp.length == 0 && key.length <= 3){
			tmp = all;
		}
		for(var i = 0, len = tmp.length; i < len; i++){
			thtml += "<div class='addtagobj' tag-id='" + tmp[i].id + "'>" + tmp[i].name + "</div>";
		}
		return thtml;
	}
	function makeTagHtml(vals, tip, tags, readonly){
		if(tags.length <= 0){
			return;
		}
		tags = delSameTag(tags);
		var tmp = "",
			thtml = "";
		for(var i = 0, len = tags.length; i < len; i++){
			tmp += tags[i].id;
			if(i != len - 1){
				tmp += ",";
			}
			thtml += getTabObjHtml(tags[i], readonly);
		}
		vals.val(tmp);
		tip.prepend(thtml);
	}
	function delSameTag(tags){
		var t = [],
			res = [];
		for(var i = 0, len = tags.length; i < len; i++){
			if(!t.indexOf){
				if(t.join(",").indexOf(tags[i].id) < 0){
					t.push(tags[i].id);
					res.push(tags[i]);
				}
			}else if(t.indexOf(tags[i].id) < 0){
				t.push(tags[i].id);
				res.push(tags[i]);
			}
		}
		return res;
	}
	function checkSameTag(tagid, tags){
		var t = tags.val().split(",");
		if(!t.indexOf){
			return t.join(",").indexOf(tagid) >= 0;
		}
		return t.indexOf(tagid) >= 0;
	}
	function getTabObjHtml(obj, readonly){
		return "<span class='tagtipobj clr' tag-id='" + obj.id + "'><span title='" + obj.name + "' class='tiptxt'>" + obj.name + "</span>" + (readonly ? "": "<span title='删除标签' class='delbtn'></span>") + "</span>";
	}
});