define(function (require, exports, module){
	var $ = require("jq");

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

	function plotInit(filter, opts){
		require.async(["modules/common/browser", "plug/flot/jquery.flot.min"], function(brow, flot){
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

	function idxChartInit(settings){
		require.async(["hchart", "plug/highcharts/themes/gray"], function(hchart, gray){
			hchart();
			gray();
			for(var i in settings){
				var tmp = new Highcharts.Chart(settings[i]);
			}
		});
	}
	exports.idxChartInit = idxChartInit;
});