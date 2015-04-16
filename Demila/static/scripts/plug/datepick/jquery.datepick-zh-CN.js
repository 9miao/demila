/* http://keith-wood.name/datepick.html
   English UK localisation for jQuery Datepicker.
   Written by Stuart. */
define(function (require, exports, module){
	return function(jquery){

(function($) {
	$.datepick.regional['en-GB'] = {
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort: ['1月', '2月', '3月', '4月', '5月', '6月',
		'7月', '8月', '9月p', '10月', '11月', '12月'],
		dayNames: ['星期天', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'],
		dayNamesShort: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
		dayNamesMin: ['日','一','二','三','四','五','六'],
		dateFormat: 'yyyy/mm/dd', firstDay: 1,
		renderer: $.datepick.defaultRenderer,
		prevText: '往前', prevStatus: '显示前一个月',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '显示前一年',
		nextText: '往后', nextStatus: '显示下一月',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '显示下一年',
		currentText: '当月', currentStatus: '显示当月',
		todayText: '今天', todayStatus: '显示今天的月份',
		clearText: '清除', clearStatus: '清除当前日期',
		closeText: '完成', closeStatus: '关闭不变更',
		yearStatus: '显示其它年份', monthStatus: '显示其它月份',
		weekText: '周', weekStatus: '一年中的周',
		dayStatus: '选择 DD, M d', defaultStatus: '选择一个日期',
		isRTL: false
	};
	$.datepick.setDefaults($.datepick.regional['en-GB']);
})(jquery);

	}
});