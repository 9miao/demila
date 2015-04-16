define(function (require, exports, module){
	var $ = require("jq");
	function idxChartInit(settings){
		require.async(["hchart", "plug/highcharts/themes/gray"], function(hchart, gray){
			hchart();gray();
			for(var i in settings){
				var tmp = new Highcharts.Chart(settings[i]);
			}
		});
	}
	exports.idxChartInit = idxChartInit;
});