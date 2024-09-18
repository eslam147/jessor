(function(factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if (typeof module === 'object' && typeof module.exports === 'object') {
    factory(require('jquery'));
  } else {
    factory(jQuery);
  }
}(function($) {
  var lastUsedDate = null;

  $.timeago = function(timestamp) {
    if (timestamp instanceof Date) {
      return inWords(timestamp);
    } else if (typeof timestamp === "string") {
      return inWords($.timeago.parse(timestamp));
    } else if (typeof timestamp === "number") {
      return inWords(new Date(timestamp));
    } else {
      return inWords($.timeago.datetime(timestamp));
    }
  };

  var $t = $.timeago;
  function inWords(date) {
    var distanceMillis = distance(date);
    var result = $t.inWords(distanceMillis);
    lastUsedDate = date;
    return result;
  }

  $.extend($.timeago, {
    settings: {
      refreshMillis: 60000,
      allowPast: true,
      allowFuture: false,
      localeTitle: false,
      cutoff: 0,
      autoDispose: true,
      strings: {
        prefixAgo: null,
        prefixFromNow: null,
        suffixAgo: "ago",
        suffixFromNow: "now",
        inPast: "now",
        seconds: "now",
        minute: "a minute",
        minutes: "%d minutes",
        hour: "an hour",
        hours: "%d hours",
        day: "a day",
        days: "%d days",
        week: "a week",
        weeks: "%d weeks",
        month: "a month",
        months: "%d months",
        year: "a year",
        years: "%d years",
        wordSeparator: " ",
        numbers: []
      }
    },

    inWords: function(distanceMillis) {
      if (!this.settings.allowPast && !this.settings.allowFuture) {
        throw 'timeago allowPast and allowFuture settings cannot both be set to false.';
      }
  
      var $l = this.settings.strings;
      var prefix = $l.prefixAgo;
      var suffix = $l.suffixAgo;
      if (this.settings.allowFuture) {
        if (distanceMillis < 0) {
          prefix = $l.prefixFromNow;
          suffix = $l.suffixFromNow;
        }
      }

      if (!this.settings.allowPast && distanceMillis >= 0) {
        return $l.inPast;
      }
      function getDaysInCurrentMonth() {
        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();
        const firstDayOfNextMonth = new Date(year, month + 1, 1);
        const lastDayOfCurrentMonth = new Date(firstDayOfNextMonth - 1);
        const daysInCurrentMonth = lastDayOfCurrentMonth.getDate();
        return daysInCurrentMonth;
      }
      function isLeapYear(year) {
        return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
      }
  
      // دالة للحصول على عدد أيام السنة
      function getDaysInYear(year) {
          return isLeapYear(year) ? 366 : 365;
      }
      var seconds = Math.abs(distanceMillis) / 1000;
      var minutes = seconds / 60;
      var hours = minutes / 60;
      var days = hours / 24;
      var months = days / 30; // Approximate
      var years = days / 365; // Approximate
      var timeUnits = [];
      function getDaysInMonth(month, year) {
        return new Date(year, month + 1, 0).getDate();
      }
      if (Math.floor(years) >= 1) {
        timeUnits.push(substitute(Math.floor(years) === 1 ? $l.year : $l.years, Math.floor(years)));
      }
      if (Math.floor(months) >= 1) {
        var totalDaysInMonth = getDaysInMonth(new Date().getMonth(), new Date().getFullYear());
        var remainingDaysAfterMonths = days - (Math.floor(months) * totalDaysInMonth);
        timeUnits.push(substitute(Math.floor(months) === 1 ? $l.month : $l.months, Math.floor(months)));
      }
      var remainingDaysAfterMonths = days - (Math.floor(months) * 30);
      if (Math.floor(remainingDaysAfterMonths / 7) >= 1) {
        timeUnits.push(substitute(Math.floor(remainingDaysAfterMonths / 7) === 1 ? $l.week : $l.weeks, Math.floor(remainingDaysAfterMonths / 7)));
      }
      if (remainingDaysAfterMonths % 7 >= 1) {
        timeUnits.push(substitute(Math.floor(remainingDaysAfterMonths % 7) === 1 ? $l.day : $l.days, Math.floor(remainingDaysAfterMonths % 7)));
      }
      if(Math.floor(days) < 1)
      {
        if (Math.floor(hours) >= 1) {
          timeUnits.push(substitute(Math.floor(hours) % 24 === 1 ? $l.hour : $l.hours, Math.floor(hours) % 24));
        }
        if(Math.floor(hours) < 1)
        {
          if (Math.floor(minutes) % 60 >= 1) {
            timeUnits.push(substitute(Math.floor(minutes) % 60 === 1 ? $l.minute : $l.minutes, Math.floor(minutes) % 60));
          }
        }
      }
      if (timeUnits.length === 0) {
        return $l.seconds;
      }
      if(timeUnits.length > 1) {
        $.each(timeUnits, function(i, unit) {
          timeUnits[i] = unit.replace(/^(a\s|an\s)/, '');
        });
      }

      // Combine time units into a single string
      var formattedTime = timeUnits.length > 1
        ? timeUnits.slice(0, -1).join(', ') + ' and ' + timeUnits.slice(-1)
        : timeUnits[0];

      var separator = $l.wordSeparator || " ";
      if ($l.wordSeparator === undefined) { separator = " "; }
      var result = $.trim([prefix, formattedTime, suffix].join(separator));

      // Remove redundant "ago"
      return result.replace(/( ago)+$/, ' ago').trim();
    },

    parse: function(iso8601) {
      var s = $.trim(iso8601);
      s = s.replace(/\.\d+/,""); // remove milliseconds
      s = s.replace(/-/,"/").replace(/-/,"/"); // fix date format
      s = s.replace(/T/," ").replace(/Z/," UTC");
      s = s.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"); // -04:00 -> -0400
      s = s.replace(/([\+\-]\d\d)$/," $100"); // +09 -> +0900
      return new Date(s);
    },

    datetime: function(elem) {
      var iso8601 = $t.isTime(elem) ? $(elem).attr("datetime") : $(elem).attr("title");
      return $t.parse(iso8601);
    },

    isTime: function(elem) {
      return $(elem).get(0).tagName.toLowerCase() === "time";
    }
  });

  var functions = {
    init: function() {
      functions.dispose.call(this);
      var refresh_el = $.proxy(refresh, this);
      refresh_el();
      var $s = $t.settings;
      if ($s.refreshMillis > 0) {
        this._timeagoInterval = setInterval(refresh_el, $s.refreshMillis);
      }
    },
    update: function(timestamp) {
      var date = (timestamp instanceof Date) ? timestamp : $t.parse(timestamp);
      $(this).data('timeago', { datetime: date });
      if ($t.settings.localeTitle) {
        $(this).attr("title", date.toLocaleString());
      }
      refresh.apply(this);
    },
    updateFromDOM: function() {
      $(this).data('timeago', { datetime: $t.parse($t.isTime(this) ? $(this).attr("datetime") : $(this).attr("title")) });
      refresh.apply(this);
    },
    dispose: function () {
      if (this._timeagoInterval) {
        window.clearInterval(this._timeagoInterval);
        this._timeagoInterval = null;
      }
    }
  };

  $.fn.timeago = function(action, options) {
    var fn = action ? functions[action] : functions.init;
    if (!fn) {
      throw new Error("Unknown function name '"+ action +"' for timeago");
    }
    this.each(function() {
      fn.call(this, options);
    });
    return this;
  };

  function refresh() {
    var $s = $t.settings;

    if ($s.autoDispose && !$.contains(document.documentElement, this)) {
      $(this).timeago("dispose");
      return this;
    }

    var data = prepareData(this);
    if (!isNaN(data.datetime)) {
      if ($s.cutoff === 0 || Math.abs(distance(data.datetime)) < $s.cutoff) {
        $(this).text(inWords(data.datetime));
      } else {
        if ($(this).attr('title').length > 0) {
          $(this).text($(this).attr('title'));
        }
      }
    }
    return this;
  }

  function prepareData(element) {
    element = $(element);
    if (!element.data("timeago")) {
      element.data("timeago", { datetime: $t.datetime(element) });
      var text = $.trim(element.text());
      if ($t.settings.localeTitle) {
        element.attr("title", element.data('timeago').datetime.toLocaleString());
      } else if (text.length > 0 && !($t.isTime(element) && element.attr("title"))) {
        element.attr("title", text);
      }
    }
    return element.data("timeago");
  }
  function distance(date) {
    return (new Date().getTime() - date.getTime());
  }

  document.createElement("abbr");
  document.createElement("time");
  
  function substitute(string, number) {
    return string.replace(/%d/i, number);
  } 
})
);