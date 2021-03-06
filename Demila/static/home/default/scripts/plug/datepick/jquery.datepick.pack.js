/* http://keith-wood.name/datepick.html
   Date picker for jQuery v4.0.2.
   Written by Keith Wood (kbwood{at}iinet.com.au) February 2010.
   Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and 
   MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses. 
   Please attribute the author if you use it. */
define(function (require, exports, module){
	return function(jquery){

(function($) {
	function Datepicker() {
		this._defaults = {
			pickerClass: '',
			showOnFocus: true,
			showTrigger: null,
			showAnim: 'show',
			showOptions: {},
			showSpeed: 'normal',
			popupContainer: null,
			alignment: 'bottom',
			fixedWeeks: false,
			firstDay: 0,
			calculateWeek: this.iso8601Week,
			monthsToShow: 1,
			monthsOffset: 0,
			monthsToStep: 1,
			monthsToJump: 12,
			changeMonth: true,
			yearRange: 'c-10:c+10',
			shortYearCutoff: '+10',
			showOtherMonths: false,
			selectOtherMonths: false,
			defaultDate: null,
			selectDefaultDate: false,
			minDate: null,
			maxDate: null,
			dateFormat: 'mm/dd/yyyy',
			autoSize: false,
			rangeSelect: false,
			rangeSeparator: ' - ',
			multiSelect: 0,
			multiSeparator: ',',
			onDate: null,
			onShow: null,
			onChangeMonthYear: null,
			onSelect: null,
			onClose: null,
			altField: null,
			altFormat: null,
			constrainInput: true,
			commandsAsDateFormat: false,
			commands: this.commands
		};
		this.regional = {
			'': {
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
				dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
				dateFormat: 'mm/dd/yyyy',
				firstDay: 0,
				renderer: this.defaultRenderer,
				prevText: '&lt;Prev',
				prevStatus: 'Show the previous month',
				prevJumpText: '&lt;&lt;',
				prevJumpStatus: 'Show the previous year',
				nextText: 'Next&gt;',
				nextStatus: 'Show the next month',
				nextJumpText: '&gt;&gt;',
				nextJumpStatus: 'Show the next year',
				currentText: 'Current',
				currentStatus: 'Show the current month',
				todayText: 'Today',
				todayStatus: 'Show today\'s month',
				clearText: 'Clear',
				clearStatus: 'Clear all the dates',
				closeText: 'Close',
				closeStatus: 'Close the datepicker',
				yearStatus: 'Change the year',
				monthStatus: 'Change the month',
				weekText: 'Wk',
				weekStatus: 'Week of the year',
				dayStatus: 'Select DD, M d, yyyy',
				defaultStatus: 'Select a date',
				isRTL: false
			}
		};
		$.extend(this._defaults, this.regional['']);
		this._disabled = []
	}
	$.extend(Datepicker.prototype, {
		dataName: 'datepick',
		markerClass: 'hasDatepick',
		_popupClass: 'datepick-popup',
		_triggerClass: 'datepick-trigger',
		_disableClass: 'datepick-disable',
		_coverClass: 'datepick-cover',
		_monthYearClass: 'datepick-month-year',
		_curMonthClass: 'datepick-month-',
		_anyYearClass: 'datepick-any-year',
		_curDoWClass: 'datepick-dow-',
		commands: {
			prev: {
				text: 'prevText',
				status: 'prevStatus',
				keystroke: {
					keyCode: 33
				},
				enabled: function(a) {
					var b = a.curMinDate();
					return (!b || $.datepick.add($.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), 1 - a.get('monthsToStep') - a.get('monthsOffset'), 'm'), 1), -1, 'd').getTime() >= b.getTime())
				},
				date: function(a) {
					return $.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), -a.get('monthsToStep') - a.get('monthsOffset'), 'm'), 1)
				},
				action: function(a) {
					$.datepick.changeMonth(this, -a.get('monthsToStep'))
				}
			},
			prevJump: {
				text: 'prevJumpText',
				status: 'prevJumpStatus',
				keystroke: {
					keyCode: 33,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.curMinDate();
					return (!b || $.datepick.add($.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), 1 - a.get('monthsToJump') - a.get('monthsOffset'), 'm'), 1), -1, 'd').getTime() >= b.getTime())
				},
				date: function(a) {
					return $.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), -a.get('monthsToJump') - a.get('monthsOffset'), 'm'), 1)
				},
				action: function(a) {
					$.datepick.changeMonth(this, -a.get('monthsToJump'))
				}
			},
			next: {
				text: 'nextText',
				status: 'nextStatus',
				keystroke: {
					keyCode: 34
				},
				enabled: function(a) {
					var b = a.get('maxDate');
					return (!b || $.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), a.get('monthsToStep') - a.get('monthsOffset'), 'm'), 1).getTime() <= b.getTime())
				},
				date: function(a) {
					return $.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), a.get('monthsToStep') - a.get('monthsOffset'), 'm'), 1)
				},
				action: function(a) {
					$.datepick.changeMonth(this, a.get('monthsToStep'))
				}
			},
			nextJump: {
				text: 'nextJumpText',
				status: 'nextJumpStatus',
				keystroke: {
					keyCode: 34,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.get('maxDate');
					return (!b || $.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), a.get('monthsToJump') - a.get('monthsOffset'), 'm'), 1).getTime() <= b.getTime())
				},
				date: function(a) {
					return $.datepick.day($.datepick.add($.datepick.newDate(a.drawDate), a.get('monthsToJump') - a.get('monthsOffset'), 'm'), 1)
				},
				action: function(a) {
					$.datepick.changeMonth(this, a.get('monthsToJump'))
				}
			},
			current: {
				text: 'currentText',
				status: 'currentStatus',
				keystroke: {
					keyCode: 36,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.curMinDate();
					var c = a.get('maxDate');
					var d = a.selectedDates[0] || $.datepick.today();
					return (!b || d.getTime() >= b.getTime()) && (!c || d.getTime() <= c.getTime())
				},
				date: function(a) {
					return a.selectedDates[0] || $.datepick.today()
				},
				action: function(a) {
					var b = a.selectedDates[0] || $.datepick.today();
					$.datepick.showMonth(this, b.getFullYear(), b.getMonth() + 1)
				}
			},
			today: {
				text: 'todayText',
				status: 'todayStatus',
				keystroke: {
					keyCode: 36,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.curMinDate();
					var c = a.get('maxDate');
					return (!b || $.datepick.today().getTime() >= b.getTime()) && (!c || $.datepick.today().getTime() <= c.getTime())
				},
				date: function(a) {
					return $.datepick.today()
				},
				action: function(a) {
					$.datepick.showMonth(this)
				}
			},
			clear: {
				text: 'clearText',
				status: 'clearStatus',
				keystroke: {
					keyCode: 35,
					ctrlKey: true
				},
				enabled: function(a) {
					return true
				},
				date: function(a) {
					return null
				},
				action: function(a) {
					$.datepick.clear(this)
				}
			},
			close: {
				text: 'closeText',
				status: 'closeStatus',
				keystroke: {
					keyCode: 27
				},
				enabled: function(a) {
					return true
				},
				date: function(a) {
					return null
				},
				action: function(a) {
					$.datepick.hide(this)
				}
			},
			prevWeek: {
				text: 'prevWeekText',
				status: 'prevWeekStatus',
				keystroke: {
					keyCode: 38,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.curMinDate();
					return (!b || $.datepick.add($.datepick.newDate(a.drawDate), -7, 'd').getTime() >= b.getTime())
				},
				date: function(a) {
					return $.datepick.add($.datepick.newDate(a.drawDate), -7, 'd')
				},
				action: function(a) {
					$.datepick.changeDay(this, -7)
				}
			},
			prevDay: {
				text: 'prevDayText',
				status: 'prevDayStatus',
				keystroke: {
					keyCode: 37,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.curMinDate();
					return (!b || $.datepick.add($.datepick.newDate(a.drawDate), -1, 'd').getTime() >= b.getTime())
				},
				date: function(a) {
					return $.datepick.add($.datepick.newDate(a.drawDate), -1, 'd')
				},
				action: function(a) {
					$.datepick.changeDay(this, -1)
				}
			},
			nextDay: {
				text: 'nextDayText',
				status: 'nextDayStatus',
				keystroke: {
					keyCode: 39,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.get('maxDate');
					return (!b || $.datepick.add($.datepick.newDate(a.drawDate), 1, 'd').getTime() <= b.getTime())
				},
				date: function(a) {
					return $.datepick.add($.datepick.newDate(a.drawDate), 1, 'd')
				},
				action: function(a) {
					$.datepick.changeDay(this, 1)
				}
			},
			nextWeek: {
				text: 'nextWeekText',
				status: 'nextWeekStatus',
				keystroke: {
					keyCode: 40,
					ctrlKey: true
				},
				enabled: function(a) {
					var b = a.get('maxDate');
					return (!b || $.datepick.add($.datepick.newDate(a.drawDate), 7, 'd').getTime() <= b.getTime())
				},
				date: function(a) {
					return $.datepick.add($.datepick.newDate(a.drawDate), 7, 'd')
				},
				action: function(a) {
					$.datepick.changeDay(this, 7)
				}
			}
		},
		defaultRenderer: {
			picker: '<div class="datepick">' + '<div class="datepick-nav">{link:prev}{link:today}{link:next}</div>{months}' + '{popup:start}<div class="datepick-ctrl">{link:clear}{link:close}</div>{popup:end}' + '<div class="datepick-clear-fix"></div></div>',
			monthRow: '<div class="datepick-month-row">{months}</div>',
			month: '<div class="datepick-month"><div class="datepick-month-header">{monthHeader}</div>' + '<table><thead>{weekHeader}</thead><tbody>{weeks}</tbody></table></div>',
			weekHeader: '<tr>{days}</tr>',
			dayHeader: '<th>{day}</th>',
			week: '<tr>{days}</tr>',
			day: '<td>{day}</td>',
			monthSelector: '.datepick-month',
			daySelector: 'td',
			rtlClass: 'datepick-rtl',
			multiClass: 'datepick-multi',
			defaultClass: '',
			selectedClass: 'datepick-selected',
			highlightedClass: 'datepick-highlight',
			todayClass: 'datepick-today',
			otherMonthClass: 'datepick-other-month',
			weekendClass: 'datepick-weekend',
			commandClass: 'datepick-cmd',
			commandButtonClass: '',
			commandLinkClass: '',
			disabledClass: 'datepick-disabled'
		},
		setDefaults: function(a) {
			$.extend(this._defaults, a || {});
			return this
		},
		_ticksTo1970: (((1970 - 1) * 365 + Math.floor(1970 / 4) - Math.floor(1970 / 100) + Math.floor(1970 / 400)) * 24 * 60 * 60 * 10000000),
		_msPerDay: 24 * 60 * 60 * 1000,
		ATOM: 'yyyy-mm-dd',
		COOKIE: 'D, dd M yyyy',
		FULL: 'DD, MM d, yyyy',
		ISO_8601: 'yyyy-mm-dd',
		JULIAN: 'J',
		RFC_822: 'D, d M yy',
		RFC_850: 'DD, dd-M-yy',
		RFC_1036: 'D, d M yy',
		RFC_1123: 'D, d M yyyy',
		RFC_2822: 'D, d M yyyy',
		RSS: 'D, d M yy',
		TICKS: '!',
		TIMESTAMP: '@',
		W3C: 'yyyy-mm-dd',
		formatDate: function(f, g, h) {
			if (typeof f != 'string') {
				h = g;
				g = f;
				f = ''
			}
			if (!g) {
				return ''
			}
			f = f || this._defaults.dateFormat;
			h = h || {};
			var i = h.dayNamesShort || this._defaults.dayNamesShort;
			var j = h.dayNames || this._defaults.dayNames;
			var k = h.monthNamesShort || this._defaults.monthNamesShort;
			var l = h.monthNames || this._defaults.monthNames;
			var m = h.calculateWeek || this._defaults.calculateWeek;
			var n = function(a, b) {
					var c = 1;
					while (s + c < f.length && f.charAt(s + c) == a) {
						c++
					}
					s += c - 1;
					return Math.floor(c / (b || 1)) > 1
				};
			var o = function(a, b, c, d) {
					var e = '' + b;
					if (n(a, d)) {
						while (e.length < c) {
							e = '0' + e
						}
					}
					return e
				};
			var p = function(a, b, c, d) {
					return (n(a) ? d[b] : c[b])
				};
			var q = '';
			var r = false;
			for (var s = 0; s < f.length; s++) {
				if (r) {
					if (f.charAt(s) == "'" && !n("'")) {
						r = false
					} else {
						q += f.charAt(s)
					}
				} else {
					switch (f.charAt(s)) {
					case 'd':
						q += o('d', g.getDate(), 2);
						break;
					case 'D':
						q += p('D', g.getDay(), i, j);
						break;
					case 'o':
						q += o('o', this.dayOfYear(g), 3);
						break;
					case 'w':
						q += o('w', m(g), 2);
						break;
					case 'm':
						q += o('m', g.getMonth() + 1, 2);
						break;
					case 'M':
						q += p('M', g.getMonth(), k, l);
						break;
					case 'y':
						q += (n('y', 2) ? g.getFullYear() : (g.getFullYear() % 100 < 10 ? '0' : '') + g.getFullYear() % 100);
						break;
					case '@':
						q += Math.floor(g.getTime() / 1000);
						break;
					case '!':
						q += g.getTime() * 10000 + this._ticksTo1970;
						break;
					case "'":
						if (n("'")) {
							q += "'"
						} else {
							r = true
						}
						break;
					default:
						q += f.charAt(s)
					}
				}
			}
			return q
		},
		parseDate: function(f, g, h) {
			if (g == null) {
				throw 'Invalid arguments';
			}
			g = (typeof g == 'object' ? g.toString() : g + '');
			if (g == '') {
				return null
			}
			f = f || this._defaults.dateFormat;
			h = h || {};
			var j = h.shortYearCutoff || this._defaults.shortYearCutoff;
			j = (typeof j != 'string' ? j : this.today().getFullYear() % 100 + parseInt(j, 10));
			var k = h.dayNamesShort || this._defaults.dayNamesShort;
			var l = h.dayNames || this._defaults.dayNames;
			var m = h.monthNamesShort || this._defaults.monthNamesShort;
			var n = h.monthNames || this._defaults.monthNames;
			var o = -1;
			var p = -1;
			var q = -1;
			var r = -1;
			var s = false;
			var t = false;
			var u = function(a, b) {
					var c = 1;
					while (z + c < f.length && f.charAt(z + c) == a) {
						c++
					}
					z += c - 1;
					return Math.floor(c / (b || 1)) > 1
				};
			var v = function(a, b) {
					u(a, b);
					var c = [2, 3, 4, 11, 20]['oy@!'.indexOf(a) + 1];
					var d = new RegExp('^-?\\d{1,' + c + '}');
					var e = g.substring(y).match(d);
					if (!e) {
						throw 'Missing number at position {0}'.replace(/\{0\}/, y);
					}
					y += e[0].length;
					return parseInt(e[0], 10);
				};
			var w = function(a, b, c, d) {
					var e = (u(a, d) ? c : b);
					for (var i = 0; i < e.length; i++) {
						if (g.substr(y, e[i].length) == e[i]) {
							y += e[i].length;
							return i + 1;
						}
					}
					throw 'Unknown name at position {0}'.replace(/\{0\}/, y);
				};
			var x = function() {
					if (g.charAt(y) != f.charAt(z)) {
						throw 'Unexpected literal at position {0}'.replace(/\{0\}/, y);
					}
					y++;
				};
			var y = 0;
			for (var z = 0; z < f.length; z++) {
				if (t) {
					if (f.charAt(z) == "'" && !u("'")) {
						t = false;
					} else {
						x();
					}
				} else {
					switch (f.charAt(z)) {
					case 'd':
						q = v('d');
						break;
					case 'D':
						w('D', k, l);
						break;
					case 'o':
						r = v('o');
						break;
					case 'w':
						v('w');
						break;
					case 'm':
						p = v('m');
						break;
					case 'M':
						p = w('M', m, n);
						break;
					case 'y':
						var A = z;
						s = !u('y', 2);
						z = A;
						o = v('y', 2);
						break;
					case '@':
						var B = this._normaliseDate(new Date(v('@') * 1000));
						o = B.getFullYear();
						p = B.getMonth() + 1;
						q = B.getDate();
						break;
					case '!':
						var B = this._normaliseDate(new Date((v('!') - this._ticksTo1970) / 10000));
						o = B.getFullYear();
						p = B.getMonth() + 1;
						q = B.getDate();
						break;
					case '*':
						y = g.length;
						break;
					case "'":
						if (u("'")) {
							x();
						} else {
							t = true;
						}
						break;
					default:
						x();
					}
				}
			}
			if (y < g.length) {
				throw 'Additional text found at end';
			}
			if (o == -1) {
				o = this.today().getFullYear();
			} else if (o < 100 && s) {
				o += (j == -1 ? 1900 : this.today().getFullYear() - this.today().getFullYear() % 100 - (o <= j ? 0 : 100));
			}
			if (r > -1) {
				p = 1;
				q = r;
				for (var C = this.daysInMonth(o, p); q > C; C = this.daysInMonth(o, p)) {
					p++;
					q -= C;
				}
			}
			var B = this.newDate(o, p, q);
			if (B.getFullYear() != o || B.getMonth() + 1 != p || B.getDate() != q) {
				throw 'Invalid date';
			}
			return B;
		},
		determineDate: function(f, g, h, i, j) {
			if (h && typeof h != 'object') {
				j = i;
				i = h;
				h = null;
			}
			if (typeof i != 'string') {
				j = i;
				i = '';
			}
			var k = function(a) {
					try {
						return $.datepick.parseDate(i, a, j);
					} catch (e) {}
					a = a.toLowerCase();
					var b = (a.match(/^c/) && h ? $.datepick.newDate(h) : null) || $.datepick.today();
					var c = /([+-]?[0-9]+)\s*(d|w|m|y)?/g;
					var d = c.exec(a);
					while (d) {
						b = $.datepick.add(b, parseInt(d[1], 10), d[2] || 'd');
						d = c.exec(a);
					}
					return b;
				};
			g = (g ? $.datepick.newDate(g) : null);
			f = (f == null ? g : (typeof f == 'string' ? k(f) : (typeof f == 'number' ? (isNaN(f) || f == Infinity || f == -Infinity ? g : $.datepick.add($.datepick.today(), f, 'd')) : $.datepick._normaliseDate(f))));
			return f;
		},
		daysInMonth: function(a, b) {
			var c = (a.getFullYear ? a : this.newDate(a, b, 1));
			return 32 - this.newDate(c.getFullYear(), c.getMonth() + 1, 32).getDate();
		},
		dayOfYear: function(a, b, c) {
			var d = (a.getFullYear ? a : this.newDate(a, b, c));
			var e = this.newDate(d.getFullYear(), 1, 1);
			return (d.getTime() - e.getTime()) / this._msPerDay + 1;
		},
		iso8601Week: function(a, b, c) {
			var d = (a.getFullYear ? new Date(a.getTime()) : this.newDate(a, b, c));
			d.setDate(d.getDate() + 4 - (d.getDay() || 7));
			var e = d.getTime();
			d.setMonth(0);
			d.setDate(1);
			return Math.floor(Math.round((e - d) / 86400000) / 7) + 1;
		},
		today: function() {
			return this._normaliseDate(new Date());
		},
		newDate: function(a, b, c) {
			return (!a ? null : this._normaliseDate(a.getFullYear ? new Date(a.getTime()) : new Date(a, b - 1, c)));
		},
		_normaliseDate: function(a) {
			if (!a) {
				return a;
			}
			a.setHours(0);
			a.setMinutes(0);
			a.setSeconds(0);
			a.setMilliseconds(0);
			a.setHours(a.getHours() > 12 ? a.getHours() + 2 : 0);
			return a;
		},
		year: function(a, b) {
			a.setFullYear(b);
			return this._normaliseDate(a);
		},
		month: function(a, b) {
			a.setMonth(b - 1);
			return this._normaliseDate(a);
		},
		day: function(a, b) {
			a.setDate(b);
			return this._normaliseDate(a);
		},
		add: function(a, b, c) {
			if (c == 'd' || c == 'w') {
				a.setDate(a.getDate() + b * (c == 'w' ? 7 : 1));
			} else {
				var d = a.getFullYear() + (c == 'y' ? b : 0);
				var e = a.getMonth() + (c == 'm' ? b : 0);
				a.setTime(this._normaliseDate(new Date(d, e, Math.min(a.getDate(), this.daysInMonth(d, e + 1)))).getTime());
			}
			return a;
		},
		_attachPicker: function(c, d) {
			c = $(c);
			if (c.hasClass(this.markerClass)) {
				return;
			}
			c.addClass(this.markerClass);
			var e = {
				target: c,
				selectedDates: [],
				drawDate: null,
				pickingRange: false,
				inline: ($.inArray(c[0].nodeName.toLowerCase(), ['div', 'span']) > -1),
				get: function(a) {
					var b = this.settings[a] !== undefined ? this.settings[a] : $.datepick._defaults[a];
					if ($.inArray(a, ['defaultDate', 'minDate', 'maxDate']) > -1) {
						b = $.datepick.determineDate(b, null, this.selectedDates[0], this.get('dateFormat'), e.getConfig());
					}
					return b;
				},
				curMinDate: function() {
					return (this.pickingRange ? this.selectedDates[0] : this.get('minDate'));
				},
				getConfig: function() {
					return {
						dayNamesShort: this.get('dayNamesShort'),
						dayNames: this.get('dayNames'),
						monthNamesShort: this.get('monthNamesShort'),
						monthNames: this.get('monthNames'),
						calculateWeek: this.get('calculateWeek'),
						shortYearCutoff: this.get('shortYearCutoff')
					};
				}
			};
			$.data(c[0], this.dataName, e);
			var f = ($.fn.metadata ? c.metadata() : {});
			e.settings = $.extend({}, d || {}, f || {});
			if (e.inline) {
				this._update(c[0]);
			} else {
				this._attachments(c, e);
				c.bind('keydown.' + this.dataName, this._keyDown).bind('keypress.' + this.dataName, this._keyPress).bind('keyup.' + this.dataName, this._keyUp);
				if (c.attr('disabled')) {
					this.disable(c[0]);
				}
			}
		},
		options: function(a, b) {
			var c = $.data(a, this.dataName);
			return (c ? (b ? (b == 'all' ? c.settings : c.settings[b]) : $.datepick._defaults) : {});
		},
		option: function(a, b, c) {
			a = $(a);
			if (!a.hasClass(this.markerClass)) {
				return;
			}
			b = b || {};
			if (typeof b == 'string') {
				var d = b;
				b = {};
				b[d] = c;
			}
			var e = $.data(a[0], this.dataName);
			var f = e.selectedDates;
			extendRemove(e.settings, b);
			this.setDate(a[0], f, null, false, true);
			e.pickingRange = false;
			e.drawDate = $.datepick.newDate(this._checkMinMax((b.defaultDate ? e.get('defaultDate') : e.drawDate) || e.get('defaultDate') || $.datepick.today(), e));
			if (!e.inline) {
				this._attachments(a, e);
			}
			if (e.inline || e.div) {
				this._update(a[0]);
			}
		},
		_attachments: function(a, b) {
			a.unbind('focus.' + this.dataName);
			if (b.get('showOnFocus')) {
				a.bind('focus.' + this.dataName, this.show);
			}
			if (b.trigger) {
				b.trigger.remove();
			}
			var c = b.get('showTrigger');
			b.trigger = (!c ? $([]) : $(c).clone().removeAttr('id').addClass(this._triggerClass)[b.get('isRTL') ? 'insertBefore' : 'insertAfter'](a).click(function() {
				if (!$.datepick.isDisabled(a[0])) {
					$.datepick[$.datepick.curInst == b ? 'hide' : 'show'](a[0]);
				}
			}));
			this._autoSize(a, b);
			if (b.get('selectDefaultDate') && b.get('defaultDate') && b.selectedDates.length == 0) {
				this.setDate(a[0], $.datepick.newDate(b.get('defaultDate') || $.datepick.today()));
			}
		},
		_autoSize: function(d, e) {
			if (e.get('autoSize') && !e.inline) {
				var f = new Date(2009, 10 - 1, 20);
				var g = e.get('dateFormat');
				if (g.match(/[DM]/)) {
					var h = function(a) {
							var b = 0;
							var c = 0;
							for (var i = 0; i < a.length; i++) {
								if (a[i].length > b) {
									b = a[i].length;
									c = i;
								}
							}
							return c;
						};
					f.setMonth(h(e.get(g.match(/MM/) ? 'monthNames' : 'monthNamesShort')));
					f.setDate(h(e.get(g.match(/DD/) ? 'dayNames' : 'dayNamesShort')) + 20 - f.getDay());
				}
				e.target.attr('size', $.datepick.formatDate(g, f, e.getConfig()).length);
			}
		},
		destroy: function(a) {
			a = $(a);
			if (!a.hasClass(this.markerClass)) {
				return;
			}
			var b = $.data(a[0], this.dataName);
			if (b.trigger) {
				b.trigger.remove();
			}
			a.removeClass(this.markerClass).empty().unbind('.' + this.dataName);
			if (b.get('autoSize') && !b.inline) {
				a.removeAttr('size');
			}
			$.removeData(a[0], this.dataName);
		},
		multipleEvents: function(b) {
			var c = arguments;
			return function(a) {
				for (var i = 0; i < c.length; i++) {
					c[i].apply(this, arguments);
				}
			};
		},
		enable: function(b) {
			var c = $(b);
			if (!c.hasClass(this.markerClass)) {
				return;
			}
			var d = $.data(b, this.dataName);
			if (d.inline) c.children('.' + this._disableClass).remove().end().find('button,select').attr('disabled', '').end().find('a').attr('href', 'javascript:void(0)');
			else {
				b.disabled = false;
				d.trigger.filter('button.' + this._triggerClass).attr('disabled', '').end().filter('img.' + this._triggerClass).css({
					opacity: '1.0',
					cursor: ''
				});
			}
			this._disabled = $.map(this._disabled, function(a) {
				return (a == b ? null : a);
			});
		},
		disable: function(b) {
			var c = $(b);
			if (!c.hasClass(this.markerClass)) return;
			var d = $.data(b, this.dataName);
			if (d.inline) {
				var e = c.children(':last');
				var f = e.offset();
				var g = {
					left: 0,
					top: 0
				};
				e.parents().each(function() {
					if ($(this).css('position') == 'relative') {
						g = $(this).offset();
						return false;
					}
				});
				var h = c.css('zIndex');
				h = (h == 'auto' ? 0 : parseInt(h, 10)) + 1;
				c.prepend('<div class="' + this._disableClass + '" style="' + 'width: ' + e.outerWidth() + 'px; height: ' + e.outerHeight() + 'px; left: ' + (f.left - g.left) + 'px; top: ' + (f.top - g.top) + 'px; z-index: ' + h + '"></div>').find('button,select').attr('disabled', 'disabled').end().find('a').removeAttr('href');
			} else {
				b.disabled = true;
				d.trigger.filter('button.' + this._triggerClass).attr('disabled', 'disabled').end().filter('img.' + this._triggerClass).css({
					opacity: '0.5',
					cursor: 'default'
				});
			}
			this._disabled = $.map(this._disabled, function(a) {
				return (a == b ? null : a);
			});
			this._disabled.push(b);
		},
		isDisabled: function(a) {
			return (a && $.inArray(a, this._disabled) > -1);
		},
		show: function(b) {
			b = b.target || b;
			var c = $.data(b, $.datepick.dataName);
			if ($.datepick.curInst == c) {
				return;
			}
			if ($.datepick.curInst) {
				$.datepick.hide($.datepick.curInst, true);
			}
			if (c) {
				c.lastVal = null;
				c.selectedDates = $.datepick._extractDates(c, $(b).val());
				c.pickingRange = false;
				c.drawDate = $.datepick._checkMinMax($.datepick.newDate(c.selectedDates[0] || c.get('defaultDate') || $.datepick.today()), c);
				c.prevDate = $.datepick.newDate(c.drawDate);
				$.datepick.curInst = c;
				$.datepick._update(b, true);
				var d = $.datepick._checkOffset(c);
				c.div.css({
					left: d.left,
					top: d.top
				});
				var e = c.get('showAnim');
				var f = c.get('showSpeed');
				f = (f == 'normal' && $.ui && $.ui.version >= '1.8' ? '_default' : f);
				var g = function() {
						var a = $.datepick._getBorders(c.div);
						c.div.find('.' + $.datepick._coverClass).css({
							left: -a[0],
							top: -a[1],
							width: c.div.outerWidth() + a[0],
							height: c.div.outerHeight() + a[1]
						});
					};
				if ($.effects && $.effects[e]) {
					c.div.show(e, c.get('showOptions'), f, g);
				} else {
					c.div[e || 'show']((e ? f : ''), g);
				}
				if (!e) {
					g();
				}
			}
		},
		_extractDates: function(a, b) {
			if (b == a.lastVal) {
				return;
			}
			a.lastVal = b;
			var c = a.get('dateFormat');
			var d = a.get('multiSelect');
			var f = a.get('rangeSelect');
			b = b.split(d ? a.get('multiSeparator') : (f ? a.get('rangeSeparator') : ''));
			var g = [];
			for (var i = 0; i < b.length; i++) {
				try {
					var h = $.datepick.parseDate(c, b[i], a.getConfig());
					if (h) {
						var k = false;
						for (var j = 0; j < g.length; j++) {
							if (g[j].getTime() == h.getTime()) {
								k = true;
								break;
							}
						}
						if (!k) {
							g.push(h);
						}
					}
				} catch (e) {}
			}
			g.splice(d || (f ? 2 : 1), g.length);
			if (f && g.length == 1) {
				g[1] = g[0];
			}
			return g;
		},
		_update: function(a, b) {
			a = $(a.target || a);
			var c = $.data(a[0], $.datepick.dataName);
			if (c) {
				if (c.inline) {
					a.html(this._generateContent(a[0], c));
				} else if ($.datepick.curInst == c) {
					if (!c.div) {
						c.div = $('<div></div>').addClass(this._popupClass).css({
							display: (b ? 'none' : 'static'),
							position: 'absolute',
							left: a.offset().left,
							top: a.offset().top + a.outerHeight()
						}).appendTo($(c.get('popupContainer') || 'body'));
					}
					c.div.html(this._generateContent(a[0], c));
					a.focus();
				}
				if (c.inline || $.datepick.curInst == c) {
					var d = c.get('onChangeMonthYear');
					if (d && (!c.prevDate || c.prevDate.getFullYear() != c.drawDate.getFullYear() || c.prevDate.getMonth() != c.drawDate.getMonth())) {
						d.apply(a[0], [c.drawDate.getFullYear(), c.drawDate.getMonth() + 1]);
					}
				}
			}
		},
		_updateInput: function(a, b) {
			var c = $.data(a, this.dataName);
			if (c) {
				var d = '';
				var e = '';
				var f = (c.get('multiSelect') ? c.get('multiSeparator') : c.get('rangeSeparator'));
				var g = c.get('dateFormat');
				var h = c.get('altFormat') || g;
				for (var i = 0; i < c.selectedDates.length; i++) {
					d += (b ? '' : (i > 0 ? f : '') + $.datepick.formatDate(g, c.selectedDates[i], c.getConfig()));
					e += (i > 0 ? f : '') + $.datepick.formatDate(h, c.selectedDates[i], c.getConfig());
				}
				if (!c.inline && !b) {
					$(a).val(d);
				}
				$(c.get('altField')).val(e);
				var j = c.get('onSelect');
				if (j && !b && !c.inSelect) {
					c.inSelect = true;
					j.apply(a, [c.selectedDates]);
					c.inSelect = false;
				}
			}
		},
		_getBorders: function(c) {
			var d = function(a) {
					var b = ($.browser.msie ? 1 : 0);
					return {
						thin: 1 + b,
						medium: 3 + b,
						thick: 5 + b
					}[a] || a;
				};
			return [parseFloat(d(c.css('border-left-width'))), parseFloat(d(c.css('border-top-width')))];
		},
		_checkOffset: function(a) {
			var b = (a.target.is(':hidden') && a.trigger ? a.trigger : a.target);
			var c = b.offset();
			var d = false;
			$(a.target).parents().each(function() {
				d |= $(this).css('position') == 'fixed';
				return !d;
			});
			if (d && $.browser.opera) {
				c.left -= document.documentElement.scrollLeft;
				c.top -= document.documentElement.scrollTop;
			}
			var e = (!$.browser.mozilla || document.doctype ? document.documentElement.clientWidth : 0) || document.body.clientWidth;
			var f = (!$.browser.mozilla || document.doctype ? document.documentElement.clientHeight : 0) || document.body.clientHeight;
			if (e == 0) {
				return c;
			}
			var g = a.get('alignment');
			var h = a.get('isRTL');
			var i = document.documentElement.scrollLeft || document.body.scrollLeft;
			var j = document.documentElement.scrollTop || document.body.scrollTop;
			var k = c.top - a.div.outerHeight() - (d && $.browser.opera ? document.documentElement.scrollTop : 0);
			var l = c.top + b.outerHeight();
			var m = c.left;
			var n = c.left + b.outerWidth() - a.div.outerWidth() - (d && $.browser.opera ? document.documentElement.scrollLeft : 0);
			var o = (c.left + a.div.outerWidth() - i) > e;
			var p = (c.top + a.target.outerHeight() + a.div.outerHeight() - j) > f;
			if (g == 'topLeft') {
				c = {
					left: m,
					top: k
				};
			} else if (g == 'topRight') {
				c = {
					left: n,
					top: k
				};
			} else if (g == 'bottomLeft') {
				c = {
					left: m,
					top: l
				};
			} else if (g == 'bottomRight') {
				c = {
					left: n,
					top: l
				};
			} else if (g == 'top') {
				c = {
					left: (h || o ? n : m),
					top: k
				};
			} else {
				c = {
					left: (h || o ? n : m),
					top: (p ? k : l)
				};
			}
			c.left = Math.max((d ? 0 : i), c.left - (d ? i : 0));
			c.top = Math.max((d ? 0 : j), c.top - (d ? j : 0));
			return c;
		},
		_checkExternalClick: function(a) {
			if (!$.datepick.curInst) {
				return;
			}
			var b = $(a.target);
			if (!b.parents().andSelf().hasClass($.datepick._popupClass) && !b.hasClass($.datepick.markerClass) && !b.parents().andSelf().hasClass($.datepick._triggerClass)) {
				$.datepick.hide($.datepick.curInst);
			}
		},
		hide: function(b, c) {
			var d = $.data(b, this.dataName) || b;
			if (d && d == $.datepick.curInst) {
				var e = (c ? '' : d.get('showAnim'));
				var f = d.get('showSpeed');
				f = (f == 'normal' && $.ui && $.ui.version >= '1.8' ? '_default' : f);
				var g = function() {
						d.div.remove();
						d.div = null;
						$.datepick.curInst = null;
						var a = d.get('onClose');
						if (a) {
							a.apply(b, [d.selectedDates]);
						}
					};
				d.div.stop();
				if ($.effects && $.effects[e]) {
					d.div.hide(e, d.get('showOptions'), f, g);
				} else {
					var h = (e == 'slideDown' ? 'slideUp' : (e == 'fadeIn' ? 'fadeOut' : 'hide'));
					d.div[h]((e ? f : ''), g);
				}
				if (!e) {
					g();
				}
			}
		},
		_keyDown: function(a) {
			var b = a.target;
			var c = $.data(b, $.datepick.dataName);
			var d = false;
			if (c.div) {
				if (a.keyCode == 9) {
					$.datepick.hide(b);
				} else if (a.keyCode == 13) {
					$.datepick.selectDate(b, $('a.' + c.get('renderer').highlightedClass, c.div)[0]);
					d = true;
				} else {
					var e = c.get('commands');
					for (var f in e) {
						var g = e[f];
						if (g.keystroke.keyCode == a.keyCode && !! g.keystroke.ctrlKey == !! (a.ctrlKey || a.metaKey) && !! g.keystroke.altKey == a.altKey && !! g.keystroke.shiftKey == a.shiftKey) {
							$.datepick.performAction(b, f);
							d = true;
							break;
						}
					}
				}
			} else {
				var g = c.get('commands').current;
				if (g.keystroke.keyCode == a.keyCode && !! g.keystroke.ctrlKey == !! (a.ctrlKey || a.metaKey) && !! g.keystroke.altKey == a.altKey && !! g.keystroke.shiftKey == a.shiftKey) {
					$.datepick.show(b);
					d = true;
				}
			}
			c.ctrlKey = ((a.keyCode < 48 && a.keyCode != 32) || a.ctrlKey || a.metaKey);
			if (d) {
				a.preventDefault();
				a.stopPropagation();
			}
			return !d;
		},
		_keyPress: function(a) {
			var b = a.target;
			var c = $.data(b, $.datepick.dataName);
			if (c && c.get('constrainInput')) {
				var d = String.fromCharCode(a.keyCode || a.charCode);
				var e = $.datepick._allowedChars(c);
				return (a.metaKey || c.ctrlKey || d < ' ' || !e || e.indexOf(d) > -1);
			}
			return true;
		},
		_allowedChars: function(a) {
			var b = a.get('dateFormat');
			var c = (a.get('multiSelect') ? a.get('multiSeparator') : (a.get('rangeSelect') ? a.get('rangeSeparator') : ''));
			var d = false;
			var e = false;
			for (var i = 0; i < b.length; i++) {
				var f = b.charAt(i);
				if (d) {
					if (f == "'" && b.charAt(i + 1) != "'") {
						d = false;
					} else {
						c += f;
					}
				} else {
					switch (f) {
					case 'd':
					case 'm':
					case 'o':
					case 'w':
						c += (e ? '' : '0123456789');
						e = true;
						break;
					case 'y':
					case '@':
					case '!':
						c += (e ? '' : '0123456789') + '-';
						e = true;
						break;
					case 'J':
						c += (e ? '' : '0123456789') + '-.';
						e = true;
						break;
					case 'D':
					case 'M':
					case 'Y':
						return null;
					case "'":
						if (b.charAt(i + 1) == "'") {
							c += "'";
						} else {
							d = true;
						}
						break;
					default:
						c += f;
					}
				}
			}
			return c;
		},
		_keyUp: function(a) {
			var b = a.target;
			var c = $.data(b, $.datepick.dataName);
			if (c && !c.ctrlKey && c.lastVal != c.target.val()) {
				try {
					var d = $.datepick._extractDates(c, c.target.val());
					if (d.length > 0) {
						$.datepick.setDate(b, d, null, true);
					}
				} catch (a) {}
			}
			return true;
		},
		clear: function(a) {
			var b = $.data(a, this.dataName);
			if (b) {
				b.selectedDates = [];
				this.hide(a);
				if (b.get('selectDefaultDate') && b.get('defaultDate')) {
					this.setDate(a, $.datepick.newDate(b.get('defaultDate') || $.datepick.today()));
				} else {
					this._updateInput(a);
				}
			}
		},
		getDate: function(a) {
			var b = $.data(a, this.dataName);
			return (b ? b.selectedDates : []);
		},
		setDate: function(a, b, c, d, e) {
			var f = $.data(a, this.dataName);
			if (f) {
				if (!$.isArray(b)) {
					b = [b];
					if (c) {
						b.push(c);
					}
				}
				var g = f.get('dateFormat');
				var h = f.get('minDate');
				var k = f.get('maxDate');
				var l = f.selectedDates[0];
				f.selectedDates = [];
				for (var i = 0; i < b.length; i++) {
					var m = $.datepick.determineDate(b[i], null, l, g, f.getConfig());
					if (m) {
						if ((!h || m.getTime() >= h.getTime()) && (!k || m.getTime() <= k.getTime())) {
							var n = false;
							for (var j = 0; j < f.selectedDates.length; j++) {
								if (f.selectedDates[j].getTime() == m.getTime()) {
									n = true;
									break;
								}
							}
							if (!n) {
								f.selectedDates.push(m);
							}
						}
					}
				}
				var o = f.get('rangeSelect');
				f.selectedDates.splice(f.get('multiSelect') || (o ? 2 : 1), f.selectedDates.length);
				if (o) {
					switch (f.selectedDates.length) {
					case 1:
						f.selectedDates[1] = f.selectedDates[0];
						break;
					case 2:
						f.selectedDates[1] = (f.selectedDates[0].getTime() > f.selectedDates[1].getTime() ? f.selectedDates[0] : f.selectedDates[1]);
						break;
					}
					f.pickingRange = false;
				}
				f.prevDate = (f.drawDate ? $.datepick.newDate(f.drawDate) : null);
				f.drawDate = this._checkMinMax($.datepick.newDate(f.selectedDates[0] || f.get('defaultDate') || $.datepick.today()), f);
				if (!e) {
					this._update(a);
					this._updateInput(a, d);
				}
			}
		},
		performAction: function(a, b) {
			var c = $.data(a, this.dataName);
			if (c && !this.isDisabled(a)) {
				var d = c.get('commands');
				if (d[b] && d[b].enabled.apply(a, [c])) {
					d[b].action.apply(a, [c]);
				}
			}
		},
		showMonth: function(a, b, c, d) {
			var e = $.data(a, this.dataName);
			if (e && (d != null || (e.drawDate.getFullYear() != b || e.drawDate.getMonth() + 1 != c))) {
				e.prevDate = $.datepick.newDate(e.drawDate);
				var f = this._checkMinMax((b != null ? $.datepick.newDate(b, c, 1) : $.datepick.today()), e);
				e.drawDate = $.datepick.newDate(f.getFullYear(), f.getMonth() + 1, (d != null ? d : Math.min(e.drawDate.getDate(), $.datepick.daysInMonth(f.getFullYear(), f.getMonth() + 1))));
				this._update(a);
			}
		},
		changeMonth: function(a, b) {
			var c = $.data(a, this.dataName);
			if (c) {
				var d = $.datepick.add($.datepick.newDate(c.drawDate), b, 'm');
				this.showMonth(a, d.getFullYear(), d.getMonth() + 1);
			}
		},
		changeDay: function(a, b) {
			var c = $.data(a, this.dataName);
			if (c) {
				var d = $.datepick.add($.datepick.newDate(c.drawDate), b, 'd');
				this.showMonth(a, d.getFullYear(), d.getMonth() + 1, d.getDate());
			}
		},
		_checkMinMax: function(a, b) {
			var c = b.get('minDate');
			var d = b.get('maxDate');
			a = (c && a.getTime() < c.getTime() ? $.datepick.newDate(c) : a);
			a = (d && a.getTime() > d.getTime() ? $.datepick.newDate(d) : a);
			return a;
		},
		retrieveDate: function(a, b) {
			var c = $.data(a, this.dataName);
			return (!c ? null : this._normaliseDate(new Date(parseInt(b.className.replace(/^.*dp(-?\d+).*$/, '$1'), 10))));
		},
		selectDate: function(a, b) {
			var c = $.data(a, this.dataName);
			if (c && !this.isDisabled(a)) {
				var d = this.retrieveDate(a, b);
				var e = c.get('multiSelect');
				var f = c.get('rangeSelect');
				if (e) {
					var g = false;
					for (var i = 0; i < c.selectedDates.length; i++) {
						if (d.getTime() == c.selectedDates[i].getTime()) {
							c.selectedDates.splice(i, 1);
							g = true;
							break;
						}
					}
					if (!g && c.selectedDates.length < e) {
						c.selectedDates.push(d);
					}
				} else if (f) {
					if (c.pickingRange) {
						c.selectedDates[1] = d;
					} else {
						c.selectedDates = [d, d];
					}
					c.pickingRange = !c.pickingRange;
				} else {
					c.selectedDates = [d];
				}
				c.prevDate = $.datepick.newDate(d);
				this._updateInput(a);
				if (c.inline || c.pickingRange || c.selectedDates.length < (e || (f ? 2 : 1))) {
					this._update(a);
				} else {
					this.hide(a);
				}
			}
		},
		_generateContent: function(h, i) {
			var j = i.get('renderer');
			var k = i.get('monthsToShow');
			k = ($.isArray(k) ? k : [1, k]);
			i.drawDate = this._checkMinMax(i.drawDate || i.get('defaultDate') || $.datepick.today(), i);
			var l = $.datepick.add($.datepick.newDate(i.drawDate), -i.get('monthsOffset'), 'm');
			var m = '';
			for (var n = 0; n < k[0]; n++) {
				var o = '';
				for (var p = 0; p < k[1]; p++) {
					o += this._generateMonth(h, i, l.getFullYear(), l.getMonth() + 1, j, (n == 0 && p == 0));
					$.datepick.add(l, 1, 'm');
				}
				m += this._prepare(j.monthRow, i).replace(/\{months\}/, o);
			}
			var q = this._prepare(j.picker, i).replace(/\{months\}/, m).replace(/\{weekHeader\}/g, this._generateDayHeaders(i, j)) + ($.browser.msie && parseInt($.browser.version, 10) < 7 && !i.inline ? '<iframe src="javascript:void(0);" class="' + this._coverClass + '"></iframe>' : '');
			var r = i.get('commands');
			var s = i.get('commandsAsDateFormat');
			var t = function(a, b, c, d, e) {
					if (q.indexOf('{' + a + ':' + d + '}') == -1) {
						return;
					}
					var f = r[d];
					var g = (s ? f.date.apply(h, [i]) : null);
					q = q.replace(new RegExp('\\{' + a + ':' + d + '\\}', 'g'), '<' + b + (f.status ? ' title="' + i.get(f.status) + '"' : '') + ' class="' + j.commandClass + ' ' + j.commandClass + '-' + d + ' ' + e + (f.enabled(i) ? '' : ' ' + j.disabledClass) + '">' + (g ? $.datepick.formatDate(i.get(f.text), g, i.getConfig()) : i.get(f.text)) + '</' + c + '>');
				};
			for (var u in r) {
				t('button', 'button type="button"', 'button', u, j.commandButtonClass);
				t('link', 'a href="javascript:void(0)"', 'a', u, j.commandLinkClass);
			}
			q = $(q);
			if (k[1] > 1) {
				var v = 0;
				$(j.monthSelector, q).each(function() {
					var a = ++v % k[1];
					$(this).addClass(a == 1 ? 'first' : (a == 0 ? 'last' : ''));
				});
			}
			var w = this;
			q.find(j.daySelector + ' a').hover(function() {
				$(this).addClass(j.highlightedClass);
			}, function() {
				(i.inline ? $(this).parents('.' + w.markerClass) : i.div).find(j.daySelector + ' a').removeClass(j.highlightedClass);
			}).click(function() {
				w.selectDate(h, this);
			}).end().find('select.' + this._monthYearClass + ':not(.' + this._anyYearClass + ')').change(function() {
				var a = $(this).val().split('/');
				w.showMonth(h, parseInt(a[1], 10), parseInt(a[0], 10));
			}).end().find('select.' + this._anyYearClass).click(function() {
				$(this).css('visibility', 'hidden').next('input').css({
					left: this.offsetLeft,
					top: this.offsetTop,
					width: this.offsetWidth,
					height: this.offsetHeight
				}).show().focus();
			}).end().find('input.' + w._monthYearClass).change(function() {
				try {
					var a = parseInt($(this).val(), 10);
					a = (isNaN(a) ? i.drawDate.getFullYear() : a);
					w.showMonth(h, a, i.drawDate.getMonth() + 1, i.drawDate.getDate());
				} catch (e) {
					alert(e);
				}
			}).keydown(function(a) {
				if (a.keyCode == 13) {
					$(a.target).change();
				} else if (a.keyCode == 27) {
					$(a.target).hide().prev('select').css('visibility', 'visible');
					i.target.focus();
				}
			});
			q.find('.' + j.commandClass).click(function() {
				if (!$(this).hasClass(j.disabledClass)) {
					var a = this.className.replace(new RegExp('^.*' + j.commandClass + '-([^ ]+).*$'), '$1');
					$.datepick.performAction(h, a);
				}
			});
			if (i.get('isRTL')) {
				q.addClass(j.rtlClass);
			}
			if (k[0] * k[1] > 1) {
				q.addClass(j.multiClass);
			}
			var x = i.get('pickerClass');
			if (x) {
				q.addClass(x);
			}
			$('body').append(q);
			var y = 0;
			q.find(j.monthSelector).each(function() {
				y += $(this).outerWidth();
			});
			q.width(y / k[0]);
			var z = i.get('onShow');
			if (z) {
				z.apply(h, [q, i]);
			}
			return q;
		},
		_generateMonth: function(a, b, c, d, e, f) {
			var g = $.datepick.daysInMonth(c, d);
			var h = b.get('monthsToShow');
			h = ($.isArray(h) ? h : [1, h]);
			var j = b.get('fixedWeeks') || (h[0] * h[1] > 1);
			var k = b.get('firstDay');
			var l = ($.datepick.newDate(c, d, 1).getDay() - k + 7) % 7;
			var m = (j ? 6 : Math.ceil((l + g) / 7));
			var n = b.get('showOtherMonths');
			var o = b.get('selectOtherMonths') && n;
			var p = b.get('dayStatus');
			var q = (b.pickingRange ? b.selectedDates[0] : b.get('minDate'));
			var r = b.get('maxDate');
			var s = b.get('rangeSelect');
			var t = b.get('onDate');
			var u = e.week.indexOf('{weekOfYear}') > -1;
			var v = b.get('calculateWeek');
			var w = $.datepick.today();
			var x = $.datepick.newDate(c, d, 1);
			$.datepick.add(x, -l - (j && (x.getDay() == k) ? 7 : 0), 'd');
			var y = x.getTime();
			var z = '';
			for (var A = 0; A < m; A++) {
				var B = (!u ? '' : '<span class="dp' + y + '">' + (v ? v(x) : 0) + '</span>');
				var C = '';
				for (var D = 0; D < 7; D++) {
					var E = false;
					if (s && b.selectedDates.length > 0) {
						E = (x.getTime() >= b.selectedDates[0] && x.getTime() <= b.selectedDates[1]);
					} else {
						for (var i = 0; i < b.selectedDates.length; i++) {
							if (b.selectedDates[i].getTime() == x.getTime()) {
								E = true;
								break;
							}
						}
					}
					var F = (!t ? {} : t.apply(a, [x, x.getMonth() + 1 == d]));
					var G = (F.selectable != false) && (o || x.getMonth() + 1 == d) && (!q || x.getTime() >= q.getTime()) && (!r || x.getTime() <= r.getTime());
					C += this._prepare(e.day, b).replace(/\{day\}/g, (G ? '<a href="javascript:void(0)"' : '<span') + ' class="dp' + y + ' ' + (F.dateClass || '') + (E && (o || x.getMonth() + 1 == d) ? ' ' + e.selectedClass : '') + (G ? ' ' + e.defaultClass : '') + ((x.getDay() || 7) < 6 ? '' : ' ' + e.weekendClass) + (x.getMonth() + 1 == d ? '' : ' ' + e.otherMonthClass) + (x.getTime() == w.getTime() && (x.getMonth() + 1) == d ? ' ' + e.todayClass : '') + (x.getTime() == b.drawDate.getTime() && (x.getMonth() + 1) == d ? ' ' + e.highlightedClass : '') + '"' + (F.title || (p && G) ? ' title="' + (F.title || $.datepick.formatDate(p, x, b.getConfig())) + '"' : '') + '>' + (n || (x.getMonth() + 1) == d ? F.content || x.getDate() : '&nbsp;') + (G ? '</a>' : '</span>'));
					$.datepick.add(x, 1, 'd');
					y = x.getTime();
				}
				z += this._prepare(e.week, b).replace(/\{days\}/g, C).replace(/\{weekOfYear\}/g, B);
			}
			var H = this._prepare(e.month, b).match(/\{monthHeader(:[^\}]+)?\}/);
			H = (H[0].length <= 13 ? 'MM yyyy' : H[0].substring(13, H[0].length - 1));
			H = (f ? this._generateMonthSelection(b, c, d, q, r, H, e) : $.datepick.formatDate(H, $.datepick.newDate(c, d, 1), b.getConfig()));
			var I = this._prepare(e.weekHeader, b).replace(/\{days\}/g, this._generateDayHeaders(b, e));
			return this._prepare(e.month, b).replace(/\{monthHeader(:[^\}]+)?\}/g, H).replace(/\{weekHeader\}/g, I).replace(/\{weeks\}/g, z);
		},
		_generateDayHeaders: function(a, b) {
			var c = a.get('firstDay');
			var d = a.get('dayNames');
			var e = a.get('dayNamesMin');
			var f = '';
			for (var g = 0; g < 7; g++) {
				var h = (g + c) % 7;
				f += this._prepare(b.dayHeader, a).replace(/\{day\}/g, '<span class="' + this._curDoWClass + h + '" title="' + d[h] + '">' + e[h] + '</span>');
			}
			return f;
		},
		_generateMonthSelection: function(a, b, c, d, e, f) {
			if (!a.get('changeMonth')) {
				return $.datepick.formatDate(f, $.datepick.newDate(b, c, 1), a.getConfig());
			}
			var g = a.get('monthNames' + (f.match(/mm/i) ? '' : 'Short'));
			var h = f.replace(/m+/i, '\.').replace(/y+/i, '\/');
			var i = '<select class="' + this._monthYearClass + '" title="' + a.get('monthStatus') + '">';
			for (var m = 1; m <= 12; m++) {
				if ((!d || $.datepick.newDate(b, m, $.datepick.daysInMonth(b, m)).getTime() >= d.getTime()) && (!e || $.datepick.newDate(b, m, 1).getTime() <= e.getTime())) {
					i += '<option value="' + m + '/' + b + '"' + (c == m ? ' selected="selected"' : '') + '>' + g[m - 1] + '</option>';
				}
			}
			i += '</select>';
			h = h.replace(/\./, i);
			var j = a.get('yearRange');
			if (j == 'any') {
				i = '<select class="' + this._monthYearClass + ' ' + this._anyYearClass + '" title="' + a.get('yearStatus') + '">' + '<option>' + b + '</option></select>' + '<input class="' + this._monthYearClass + ' ' + this._curMonthClass + c + '" value="' + b + '">';
			} else {
				j = j.split(':');
				var k = $.datepick.today().getFullYear();
				var l = (j[0].match('c[+-].*') ? b + parseInt(j[0].substring(1), 10) : ((j[0].match('[+-].*') ? k : 0) + parseInt(j[0], 10)));
				var n = (j[1].match('c[+-].*') ? b + parseInt(j[1].substring(1), 10) : ((j[1].match('[+-].*') ? k : 0) + parseInt(j[1], 10)));
				i = '<select class="' + this._monthYearClass + '" title="' + a.get('yearStatus') + '">';
				var o = $.datepick.add($.datepick.newDate(l + 1, 1, 1), -1, 'd');
				o = (d && d.getTime() > o.getTime() ? d : o).getFullYear();
				var p = $.datepick.newDate(n, 1, 1);
				p = (e && e.getTime() < p.getTime() ? e : p).getFullYear();
				for (var y = o; y <= p; y++) {
					if (y != 0) {
						i += '<option value="' + c + '/' + y + '"' + (b == y ? ' selected="selected"' : '') + '>' + y + '</option>';
					}
				}
				i += '</select>';
			}
			h = h.replace(/\//, i);
			return h;
		},
		_prepare: function(e, f) {
			var g = function(a, b) {
					while (true) {
						var c = e.indexOf('{' + a + ':start}');
						if (c == -1) {
							return;
						}
						var d = e.substring(c).indexOf('{' + a + ':end}');
						if (d > -1) {
							e = e.substring(0, c) + (b ? e.substr(c + a.length + 8, d - a.length - 8) : '') + e.substring(c + d + a.length + 6);
						}
					}
				};
			g('inline', f.inline);
			g('popup', !f.inline);
			var h = /\{l10n:([^\}]+)\}/;
			var i = null;
			while (i = h.exec(e)) {
				e = e.replace(i[0], f.get(i[1]));
			}
			return e;
		}
	});

	function extendRemove(a, b) {
		$.extend(a, b);
		for (var c in b) if (b[c] == null || b[c] == undefined) a[c] = b[c];
		return a;
	};
	$.fn.datepick = function(a) {
		var b = Array.prototype.slice.call(arguments, 1);
		if ($.inArray(a, ['getDate', 'isDisabled', 'options', 'retrieveDate']) > -1) {
			return $.datepick[a].apply($.datepick, [this[0]].concat(b));
		}
		return this.each(function() {
			if (typeof a == 'string') {
				$.datepick[a].apply($.datepick, [this].concat(b))
			} else {
				$.datepick._attachPicker(this, a || {})
			}
		})
	};
	$.datepick = new Datepicker();
	$(function() {
		$(document).mousedown($.datepick._checkExternalClick).resize(function() {
			$.datepick.hide($.datepick.curInst)
		})
	})

})(jquery);

	}
});