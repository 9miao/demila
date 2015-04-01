define(function (require, exports, module){
	var $ = require("jq");
	//var imageObj = new Image();
		//imgs = ["img/toggle.gif", "img/nyro/ajaxLoader.gif", "img/nyro/prev.gif", "img/nyro/next.gif"];
	//for (i = 0; i < imgs.length; i++) imageObj.src = imgs[i];

	// scrollToTop() - scroll window to the top
	function scrollToTop(e) {
	    $(e).hide().removeAttr("href");
	    if ($(window).scrollTop() != "0") {
	        $(e).fadeIn("slow")
	    }
	    var scrollDiv = $(e);
	    $(window).scroll(function () {
	        if ($(window).scrollTop() == "0") {
	            $(scrollDiv).fadeOut("slow")
	        } else {
	            $(scrollDiv).fadeIn("slow")
	        }
	    });
	    $(e).click(function () {
	        $("html, body").animate({
	            scrollTop: 0
	        }, "slow")
	    })
	}
	exports.scrollToTop = scrollToTop;

	// setup() - Administry init and setup
	function setup() {
	    // Open an external link in a new window
	    $('a[href^="http://"]').filter(function () {
	        return this.hostname && this.hostname !== location.hostname;
	    }).attr('target', '_blank');
		
	    // build animated dropdown navigation
		$('#menu ul').supersubs({ 
			minWidth:    12,   // minimum width of sub-menus in em units 
			maxWidth:    27,   // maximum width of sub-menus in em units 
			extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
							   // due to slight rounding differences and font-family 
		}).superfish(); 
		
	    // build an animated footer
	    $('#animated').each(function () {
	        $(this).hover(function () {
	            $(this).stop().animate({
	                opacity: 0.9
	            }, 400);
	        }, function () {
	            $(this).stop().animate({
	                opacity: 0.0
	            }, 200);
	        });
	    });

	    // scroll to top on request
	    if ($("a#totop").length) Administry.scrollToTop("a#totop");

	    // setup content boxes
	    if ($(".content-box").length) {
	        $(".content-box header").css({
	            "cursor": "s-resize"
	        });
	        // Give the header in content-box a different cursor	
	        $(".content-box header").click(
	        function () {
	            $(this).parent().find('section').toggle(); // Toggle the content
	            $(this).parent().toggleClass("content-box-closed"); // Toggle the class "content-box-closed" on the content
	        });
	    }
		
		// setup nyro popup window
		$.nyroModalSettings({
			debug: false,
			processHandler: function(settings) {
				var url = settings.url;
				if (url && url.indexOf('http://www.youtube.com/watch?v=') == 0) {
					$.nyroModalSettings({
						type: 'swf',
						height: 355,
						width: 425,
						url: url.replace(new RegExp("watch\\?v=", "i"), 'v/')
					});
				}
			}
		});
	    
		// custom tooltips to replace the default browser tooltips for <a title=""> <div title=""> and <span title="">
	    $("a[title], div[title], span[title]").tipTip();
		
		// find closeable boxes and add a "close" action
		$('.closeable').each(function(index){
			$(this).prepend( 
				$('<a></a>')
					.attr({href: '#', title: 'Close'})
					.addClass('close')
					.text('x')
					.click(function() {
						$(this).parent().fadeOut();
						return false;
					})
			);
		});
	}
	exports.setup = setup;

	// progress() - animate a progress bar "el" to the value "val"
	function progress(el, val, max) {
	    var duration = 400;
	    var span = $(el).find("span");
	    var b = $(el).find("b");
	    var w = Math.round((val / max) * 100);
	    $(b).fadeOut('fast');
	    $(span).animate({
	        width: w + '%'
	    }, duration, function () {
	        $(el).attr("value", val);
	        $(b).text(w + '%').fadeIn('fast');
	    });
	}
	exports.progress = progress;

	// videoSupport() - <video> tag support for older browsers through flash player embedding
	function videoSupport(wrapper, videoURL, width, height) {
	    var v = document.createElement("video"); // Are we dealing with a browser that supports <video> tag? 
	    if (!v.play) { // If no, use Flash.
	        var vobj = $('#' + wrapper).find('video');
	        var poster = $(vobj).attr("poster");
	        var params = {
	            allowfullscreen: "true",
	            allowscriptaccess: "always"
	        };
	        var flashvars = {
	            file: videoURL,
	            image: poster
	        };
	        swfobject.embedSWF("player.swf", wrapper, width, height, "9.0.0", "expressInstall.swf", flashvars, params);
	    }
	}
	exports.videoSupport = videoSupport;

	// dateInput() - <input type="date"> support with fallback
	function dateInput(e) {
		require.async(["plug/datepicker/datepicker"], function(datepick){
			datepick($);
		    var i = document.createElement("input"); 
			i.setAttribute("type", "date");
			if (i.type == "text") {
				// No native date picker support :(
				// We shall use jQuery datepick
				//$(e).datepick({dateFormat: 'yyyy-mm-dd'}); 
				$(e).each(function(){
					var $that = $(this);
					$that.DatePicker({
						format:'Y-m-d',
						date: $that.val(),
						current: $that.val(),
						starts: 1,
						onBeforeShow: function(){
							//$that.DatePickerSetDate($that.val(), true);
						},
						onChange: function(formated, dates){
							$that.val(formated);
							$that.DatePickerHide();
						}
				    });
				});
			}
		}); 
	}
	exports.dateInput = dateInput;

	// expandableRows() - expandable table rows
	function expandableRows() {
	    var titles_total = $('td.title').length;
	    if (titles_total) { /* setting z-index for IE7 */
	        $('td.title').each(function (i, e) {
	            $(e).children('div').css('z-index', String(titles_total - i));
	        });

	        $('td.title').find('a').click(function () {
	            // hide previously opened containers
	            $('.opened').hide();
	            // remove highlighted class from rows
	            $('td.highlighted').removeClass('highlighted');

	            // locate the row we clicked onto
	            var tr = $(this).parents("tr");
	            var div = $(this).parent().find('.listingDetails');

	            if (!$(div).hasClass('opened')) {
	                $(div).addClass('opened').width($(tr).width() - 2).show();
	                $(tr).find('td').addClass('highlighted');
	            } else {
	                $(div).removeClass('opened');
	                $(tr).find('td').removeClass('highlighted');
	            }
	            return false;
	        });
	    }
	}
	exports.expandableRows = expandableRows;

	function showFields(action) {
		$("#item_action").val(action);
		$("#approve_item, #unapprove_item").slideUp();
		$("#area3").val('');
		if(action == 'approve') {
			$("#approve_item").slideDown();
		}
		else {
			$("#unapprove_item").slideDown();
		}
		if($("#submit_form").css('display') == 'none') {
			$("#submit_form").css('display', 'block');
		}
	}
	exports.showFields = showFields;


	function cheange_status(filter, purl){
	   	$(filter).on("click", function(){
	   		var that = $(this),
		   		item = that.attr('item'),
		    	status = that.attr('status'),
		    	id = that.attr('id');
		    $('#' + id).hide();
		    $('.' + id).show();
			$.ajax({
				type: "post",
				url: purl,
				data: 'action=ajax_edit&status='+status+'&item='+item,
				dataType: "json",
				success: function(data){
					if(data){
						$('.'+id).hide();
						that.attr('status',data.status);
						that.attr('src',data.pic);
						$('#'+id).fadeIn();

					}else{
						$('.'+id).hide();
						$('#'+id).show();
					}
				}
		    });
	   	});
	}
	exports.cheange_status = cheange_status;

	function plotInit(filter, opts){
		require.async(["modules/browser", "plug/flot/jquery.flot.min"], function(brow, flot){
			brow($);
			flot($);
			var previousPoint = null;
			$.plot($(filter), opts.datasets, opts.options);
			$(filter).bind("plothover", function (event, pos, item) {
				$("#x").text(pos.x.toFixed(2));
				$("#y").text(pos.y.toFixed(2));
				if(item) {
					if(previousPoint != item.datapoint) {
						previousPoint = item.datapoint;
						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(2),
						y = item.datapoint[1].toFixed(2);
						showTooltip(item.pageX, item.pageY, item.series.label + " (" + opts.contents + " " + y + ")");
					}
				}else{
					$("#tooltip").remove();
					previousPoint = null;
				}
			});

			var i = 0;
			$.each(opts.datasets, function(key, val){
				val.color = i;
				++i;
			});
			var choiceContainer = $("#choices");
			choiceContainer.on("click", "input", plotAccordingToChoices);
			$.each(opts.datasets, function(key, val) {
				choiceContainer.append('<br/><input type="checkbox" name="' + key +
				'" checked="checked" id="id' + key + '">' +
				'<label for="id' + key + '">'
				+ val.label + '</label>');
			});
			function plotAccordingToChoices(){
				var data = [];
				choiceContainer.find("input:checked").each(function () {
					var key = $(this).attr("name");
				console.log(key);
					if (key && opts.datasets[key]){
						data.push(opts.datasets[key]);
					}
				});
				if (data.length >= 0){
					$.plot($(filter), data, opts.options);
				}
			}
			plotAccordingToChoices();
		});
	}
	exports.plotInit = plotInit;

	function showTooltip(x, y, contents) {
		$('<div id="tooltip">' + contents + '</div>').css({
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5,
			border: '1px solid #fdd',
			padding: '2px',
			'background-color': '#fee',
			opacity: 0.80
		}).appendTo("body").fadeIn(200);
	}
	
});