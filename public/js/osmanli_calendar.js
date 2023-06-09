//Dev: Abrar Ajaz
//Version: 1v
osmanli_calendar = {
	//CURRENT_DATE:	new Date().toLocaleString('en-Ca',{hour12: false, timeZone: 'Asia/Kuala_Lumpur'})
	CURRENT_DATE:	new Date() ,	// This is  date
	SELECT_DATE:	new Date() ,	// This is  date
	MIN_DATE: 'OFF',
	MAX_DATE: 'OFF',
	DAYS_DISABLE_MAX: 'OFF',
	DAYS_DISABLE_MIN: 'OFF',
	ON_SELECT_FUNC: "OFF",
	content : 'January February March April May June July August September October November December'.split(' '),
	weekDayName : 'SUN MON TUES WED THURS FRI'.split(' '),
	daysOfMonth : [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],

	init: function() {
		console.log("#init")
		this.clearCalendar()
		this.myCalendar()
		this.disable_days()
		this.disable_back()

		// .not(':eq(31)') is SCREWED UP!!
		// The thirty first date is NOT clickable!
		//$('.date_table > tbody > tr > td').not(':eq(31)').click(function(e) {

		$('.date_table > tbody > tr > td').click(function(e) {
			var target = e.target;

			if ($(target).attr('disabled') === "disabled") {return}

			$('.date_table > tbody  > tr > td').removeClass('selected_date');
			$(target).addClass('selected_date');
			let day = $(target).html();
			let month  = $('.month-year > h3').html();

			//alert(day);
			//alert(month);

			$('#startDate').val(day+' '+month);
			osmanli_calendar.SELECT_DATE = new Date(day+'-'+month+'-'+
				osmanli_calendar.CURRENT_DATE.getFullYear())

			console.log(osmanli_calendar.SELECT_DATE)

			if (osmanli_calendar.ON_SELECT_FUNC != "OFF") {
			  osmanli_calendar.ON_SELECT_FUNC(osmanli_calendar.SELECT_DATE)
			}
		});
	},

	disable_back : function(dir) {
		return_ = undefined;

		if (this.MIN_DATE != 'OFF') {
			if (this.MIN_DATE.getMonth() >= this.CURRENT_DATE.getMonth() &&
				this.MIN_DATE.getFullYear() == this.CURRENT_DATE.getFullYear()) {
				$('.prev-month').addClass('disabled');
				$('.prev-month').css("cursor", "not-allowed");
				$('.prev-month').attr('disabled','on');
				if (dir < 0) {
					return_ =  true
				}
			} else {
				$('.prev-month').removeClass('disabled');
				$('.prev-month').removeAttr('disabled');
				$('.prev-month').css("cursor", "pointer");
			}
		}

		if (this.MAX_DATE != 'OFF') {
			if (this.MAX_DATE.getMonth() <= this.CURRENT_DATE.getMonth() &&
				this.MAX_DATE.getFullYear() == this.CURRENT_DATE.getFullYear()) {
				$('.next-month').addClass('disabled');
				$('.next-month').css("cursor", "not-allowed");
				$('.next-month').attr('disabled','on');
				if (dir > 0) {
					return_ =  true
				}
			} else {
				$('.next-month').removeClass('disabled');
				$('.next-month').removeAttr('disabled');
				$('.next-month').css("cursor", "pointer");
			}
		}

		if (return_ == undefined) {
				return false;
		} else {
			return true;
		}
	},

	disable_days: function() {
		if (this.DAYS_DISABLE_MIN == 'OFF' && this.DAYS_DISABLE_MAX == 'OFF') {
			return null;
		}
		tr = $(".date_table > tbody > tr").not(':eq(0)');

		tr.each(function(){
			td = $(this).children('td');

			td.each(function(){
				value = $(this).html()
				//   alert(value)
				if (value == '') {
					$(this).addClass('disabled');
					$(this).attr('disabled', 'on');
					$(this).css("cursor", "none");
				}
				value = parseInt(value.trim())
				if (value === NaN) {
					return
				}

				//min
				if (osmanli_calendar.MIN_DATE != 'OFF') {
					if (
						osmanli_calendar.MIN_DATE.getMonth() == osmanli_calendar.CURRENT_DATE.getMonth() &&
						osmanli_calendar.MIN_DATE.getFullYear() == osmanli_calendar.CURRENT_DATE.getFullYear()
						) {
						if (value < osmanli_calendar.MIN_DATE.getDate()) {
							$(this).addClass('disabled');
							$(this).attr('disabled','on');
							$(this).css("cursor", "not-allowed")
						}
					}
				}

				if (osmanli_calendar.MAX_DATE != "OFF") {
					if (
						osmanli_calendar.MAX_DATE.getMonth() == osmanli_calendar.CURRENT_DATE.getMonth() &&
						osmanli_calendar.MAX_DATE.getFullYear() == osmanli_calendar.CURRENT_DATE.getFullYear()
						) {
						if (value > osmanli_calendar.MAX_DATE.getDate()) {
							$(this).addClass('disabled');
							$(this).attr('disabled','on');
							$(this).css("cursor", "not-allowed")
						}
					}
				}
			})
		})
	},

	renderCalendar: function(startDay, totalDays, currentDate) {
		// console.log("Current Date: ", currentDate);
		var currentRow = 1;
		var currentDay = startDay;
		var $table = $('table.date_table');
		var $week = this.getCalendarRow();
		var $day;
		var i = 1;

		for (; i <= totalDays; i++) {
			$day = $week.find('td').eq(currentDay);
			$day.text(i);

			if (i === currentDate) {
				$day.addClass('selected_date');
			}

			currentDay = ++currentDay % 7;

			if (currentDay == 0 && (i + 1 <= totalDays)) {
				$week = this.getCalendarRow();
				console.log($week)

				currentRow++;
			}
		}
	},

	getCalendarRow	:	function() {
		var $table = $('table.date_table');
		var $tr = $('<tr/>');
		for (var i = 0, len = 7; i < len; i++) {
			$tr.append($('<td/>'));
		}
		$table.append($tr);
		return $tr;
	},

	myCalendar	:	function() {
		var month = this.CURRENT_DATE.getMonth();
		var day = this.CURRENT_DATE.getDay();
		var year = this.CURRENT_DATE.getFullYear();
		var date = this.CURRENT_DATE.getDate();
		var totalDaysOfMonth = this.daysOfMonth[month];
		var counter = 1;
		var $h3 = $('<h3>');
		$h3.text(this.content[month] + ' ' + year);
		$h3.appendTo('.month-year');
		var dateToHighlight = 0;


		if (this.CURRENT_DATE.getMonth() === this.SELECT_DATE.getMonth() &&
			this.CURRENT_DATE.getFullYear() === this.SELECT_DATE.getFullYear() ) {
			dateToHighlight = this.SELECT_DATE.getDate();
		}

		if (month === 1) {
			if ((year % 100 !== 0) && (year % 4 === 0) || (year % 400 === 0)) {
				totalDaysOfMonth = 29;
			}
		}

		this.renderCalendar(this.getCalendarStart(day, date),
			totalDaysOfMonth, dateToHighlight);
	},

	getCalendarStart: function(dayOfWeek, currentDate) {
		var date = currentDate - 1;
		var startOffset = (date % 7) - dayOfWeek;
		if (startOffset > 0) {
			startOffset -= 7;
		}
		return Math.abs(startOffset);
	},

	navigationHandler: function(dir) {
		if ((this.MIN_DATE != 'OFF' && this.disable_back(dir) && dir < 0) ||
			(this.MAX_DATE != 'OFF' && this.disable_back(dir) && dir > 0)) {
			return null;
		}
		this.CURRENT_DATE.setMonth(this.CURRENT_DATE.getMonth() + dir);
		this.clearCalendar();
		this.init();
	},

	clearCalendar: function() {
		var $trs = $('.date_table > tbody > tr').not(':eq(0)');
		$trs.remove();
		$('.month-year').empty();
	},
	next_month: function() {
		this.navigationHandler(1)
	},
	pre_month:function() {
		this.navigationHandler(-1)
	}

}
