var wasChanged = false;
var chart, chart2;
var minDate, maxDate, graphtype, tickInt;
var urlCom, shift, theme;
var start_interval = false;
var loadingCount = 0;
var allCount = 0;
var themeDataForDiagrams = [];
var filterChanged = false;
var fltr = [];

var mmItems = [
  {id: "mm-words",
    api: "words",
    title: "Со словами",
    event: onSelectMMitem
  }
];

function onSelectMMitem(index, cast) {
  return false;
  $.cookie('theme-compare' + mmItems[index].id, cast);
  filterChangeFlag();
}

function formatDate(unixTimestamp) {
  if (unixTimestamp == null) return null;
  var dt = new Date(parseInt(unixTimestamp));
  var day = dt.getDate();
  var month = dt.getMonth() + 1;
  var year = dt.getFullYear();

  if (day < 10) {
    day = '0' + day;
  }

  if (month < 10) {
    month = '0' + month;
  }

  return day + "." + month + "." + year;
}

/*
 Открывает модельное окно попапа
 */
function loadmodal(href, width, height, type) {
  if (!width) width = 804;
  if (!height) height = 500;
  $.fancybox({
    'href': href,
    'width': width,
    'height': height,
    'scrolling': 'no',
    'titleShow': false,
    'padding': 0,
    'autoScale': false,
    'transitionIn': 'none',
    'transitionOut': 'none',
    type: type,
    onClosed: function () {
      $(".EXTERN").css('display', 'none');
    }
  });
}

function FillExportList() {
  start_interval = true;
  if ($('.export-list').is(":visible")) {
    $.postJSON(postURL_GetExports, {order_id: id}, function (data) {
      $('.export-list div table').html('<tr><td width="30" class="td_border_bottom">№</td><td width="100" class="td_border_bottom">Время экспорта</td><td width="200" class="td_border_bottom" align="center">Период исследования</td><td width="100" class="td_border_bottom">Прогресс</td></tr>');
      $.each(data, function (i, k) {
        $('.export-list div table').append('<tr><td>' + (i + 1) + '.</td><td>' + new Date(parseInt(k.export_time * 1000, 10)).format("dd.mm.yyyy HH:MM:ss") + '</td><td align="center">' + new Date(parseInt(k.start_time * 1000, 10)).format("dd.mm.yyyy") + ' - ' + new Date(parseInt(k.end_time * 1000, 10)).format("dd.mm.yyyy") + '</td><td>' + (k.progress !== undefined ? (k.progress == -1 ? 'ошибка' : k.progress) : '<a href="' + k.dl_link + '"><b>Скачать</b><a>') + '</td></tr>');
      });
    });
  }
  setTimeout('FillExportList();', 5000);
}

function LaunchExport() {
  var request = {};

  request['start'] = $("#dp-begin").datepicker("getDate").format("dd.mm.yyyy");
  request['end'] = $("#dp-end").datepicker("getDate").format("dd.mm.yyyy");
  request['order_id'] = id;
  $('#export_launch').attr('onclick', '');
  $.postJSON(postURL_AddExport, request, function (data) {
    $('#export_launch').attr('onclick', 'LaunchExport(); return false;');
    $("#dialog-message-add").dialog({
      modal: true,
      buttons: {
        "Ок": function () {
          $(this).dialog("close");
        }
      },
      draggable: false,
      resizable: false,
      minWidth: 400,
      maxWidth: 400
    });
    return false;
  });
}

var masterChart, detailChart;
// create the master chart
function createMaster(series) {
  createDetail(masterChart, series)
}

// create the detail chart
function createDetail(masterChart, series) {
  var detailData = series;
  Highcharts.setOptions({
    global: {
      useUTC: false
    }
  });

  detailChart = new Highcharts.Chart({
    chart: {
      marginBottom: 100,
      renderTo: 'detail-container',
      reflow: false,
      marginLeft: 40,
      marginRight: 0,
      style: {
        position: 'absolute'
      },
      spacingLeft: 0,
      height: 281,
      width: 806
    },
    colors: [
      '#7f7bdb',
      '#c0ac97',
      '#b0c78f',
      '#c09797',
      '#97c0c0',
      '#86c4d1',
      '#92A8CD',
      '#A47D7C',
      '#B5CA92'
    ],
    legend: {
        borderWidth: 0
    },
    xAxis: {
      type: 'datetime',
      lineColor: '#000',
      labels: {
        formatter: function () {
          rangestart = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
          var tttext = '';
          if (graphtype == 'hour') {
            tttext = (new Date((this.value - 1) * 3600000 + shift)).format("H:00");
          }
          else if (graphtype == 'day') {
            tttext = (new Date((this.value - 1) * 86400000 + shift)).format("d.mm");
          }
          else if (graphtype == 'week') {
            rngstart = new Date((this.value - 1) * 7 * 86400000 - 86400000 * 6 + shift);
            if (rngstart > rangestart)
            {
              tttext = rngstart.format("d") + '-' + (new Date((this.value - 1) * 7 * 86400000 + shift)).format("d.mm");
            }
            else
            {
              tttext = rangestart.format("d") + '-' + (new Date((this.value - 1) * 7 * 86400000 + shift)).format("d.mm");
            }
          }
          else if (graphtype == 'month') {
            rngstart = new Date((this.value - 1) * 30 * 86400000 - 86400000 * 29 + shift);
            if (rngstart > rangestart)
            {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 30 * 86400000 + shift))).format("m.yy");
            }
            else
            {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 30 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'quarter') {
            rngstart = new Date((this.value - 1) * 90 * 86400000 - 86400000 * 89 + shift);
            if (rngstart > rangestart)
            {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 90 * 86400000 + shift))).format("m.yy");
            }
            else
            {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 90 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'halfyear') {
            rngstart = new Date((this.value - 1) * 180 * 86400000 - 86400000 * 179 + shift);
            if (rngstart > rangestart)
            {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 180 * 86400000 + shift))).format("m.yy");
            }
            else
            {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 180 * 86400000 + shift))).format("m.yy");
            }
          }
          return tttext;
        },
        staggerLines: 2,
        y: 25
      },
      tickWidth: 1,
      tickColor: '#000',
      minPadding: 0,
      tickInterval: tickInt,
      minorTickPosition: 'inside',
      gridLineWidth: 1
    },
    yAxis: {lineWidth: 1, lineColor: '#000', tickWidth: 1, tickColor: '#000', gridLineWidth: 1, minorGridLineWidth: 1, minorGridLineColor: '#F0F0F0', minorTickWidth: 1, minorTickInterval: 'auto', title: {text: ""}, allowDecimals: false, min: 0},
    title: { text: ""},
    plotOptions: {
      series: {
        animation: false,
        shadow: false,
        lineWidth: 3,
        cursor: 'pointer',
        point: {
          events: {
            'click': function () {
              if (graphtype == 'week') {
                rangestart = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
                pstart = new Date((this.category - 1) * 86400000 * 7 + shift - 86400 * 6 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 7 + shift);
                if (rangestart > pstart)
                {
                  pstart = rangestart;
                }
              }
              else if (graphtype == 'month') {
                rangestart = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
                pstart = new Date((this.category - 1) * 86400000 * 30 + shift - 86400 * 29 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 30 + shift);
                if (rangestart > pstart)
                {
                  pstart = rangestart;
                }
              }
              else if (graphtype == "quarter") {
                rangestart = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
                pstart = new Date((this.category - 1) * 86400000 * 90 + shift - 86400 * 89 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 90 + shift);
                if (rangestart > pstart)
                {
                  pstart = rangestart;
                }
              }
              else if (graphtype == "halfyear") {
                rangestart = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
                pstart = new Date((this.category - 1) * 86400000 * 180 + shift - 86400 * 179 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 180 + shift);
                if (rangestart > pstart)
                {
                  start = rangestart;
                }
              }
              else if (graphtype == 'day') {
                pstart = new Date((this.category - 1) * 86400000 + shift);
                pend = new Date((this.category - 1) * 86400000 + shift);
              }
              else if (graphtype == 'hour') {
                pstart = new Date(shift);
                pend = new Date(shift);
              }
              if (graphtype == 'hour') {
                $("#dp-begin").datepicker("setDate", pstart);
                $("#dp-end").datepicker("setDate", pend);
                $("#date #datepicker").val( dateToWords(pstart.format("dd.mm.yyyy"), true) + " - " + dateToWords(pend.format("dd.mm.yyyy"), true));
                $.cookie("theme-compare-fromDate-theme", pstart.getTime());
                $.cookie("theme-compare-toDate-theme", pend.getTime());
                $.cookie("theme-compare-md5", '');
                window.open(urlCom, '_blank');
              }
              else {
                $("#dp-begin").datepicker("setDate", pstart);
                $("#dp-end").datepicker("setDate", pend);
                $("#date #datepicker").val( dateToWords(pstart.format("dd.mm.yyyy"), true) + " - " + dateToWords(pend.format("dd.mm.yyyy"), true));
                $.cookie("theme-compare-fromDate-theme", pstart.getTime());
                $.cookie("theme-compare-toDate-theme", pend.getTime());
                $.cookie("theme-compare-md5", '');
                $("#progressbar").progressbar({value: 20});
                $(".progress").fadeIn();
                loadAllContent(pstart.format("dd.mm.yyyy"), pend.format("dd.mm.yyyy"));
              }
            }
          }
        },
        marker: {
          fillColor: '#FFFFFF',
          lineWidth: 2,
          lineColor: null,
          radius: 3,
          symbol: "circle"
        }
      }
    },
    tooltip: {
      enabled: true,
      formatter: function () {
        if (graphtype == 'hour')
        {
          tttext = (new Date((this.x - 1) * 3600000 + shift)).format("H:00");
        }
        if (graphtype == 'day')
        {
          tttext = (new Date((this.x - 1) * 86400000 + shift)).format("d.mm.yyyy");
        }
        rangestart = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
        if (graphtype == 'week') {
          rngstart = new Date((this.x - 1) * 86400000 * 7 + shift - 86400 * 6 * 1000);
          if (rngstart > rangestart)
          {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 7 + shift)).format("dd.mm.yy");
          }
          else
          {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 7 + shift)).format("dd.mm.yy");
          }
        }
        if (graphtype == 'month') {
          rngstart = new Date((this.x - 1) * 86400000 * 30 + shift - 86400 * 29 * 1000);
          if (rngstart > rangestart)
          {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 30 + shift)).format("dd.mm.yy");
          }
          else tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 30 + shift)).format("dd.mm.yy");
        }
        if (graphtype == 'quarter') {
          rngstart = new Date((this.x - 1) * 86400000 * 90 + shift - 86400 * 89 * 1000);
          if (rngstart > rangestart)
          {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 90 + shift)).format("dd.mm.yy");
          }
          else
          {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 90 + shift)).format("dd.mm.yy");
          }
        }
        if (graphtype == 'halfyear') {
          rngstart = new Date((this.x - 1) * 86400000 * 180 + shift - 86400 * 179 * 1000);
          if (rngstart > rangestart)
          {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 180 + shift)).format("dd.mm.yy");
          }
          else
          {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 180 + shift)).format("dd.mm.yy");
          }
        }
        return '<b>' + this.series.name + '</b><br/>Постов ' + this.y + '<br/>' + tttext;
      }
    },
    series: detailData,
    navigation: {
      buttonOptions: {
        enabled: false
      }
    }
  });
}

Array.prototype.shuffle = function () {
  var len = this.length;
  var i = len;
  while (i--) {
    var p = parseInt(Math.random() * len);
    var t = this[i];
    this[i] = this[p];
    this[p] = t;
  }
};

function loadmodal(href, width, height) {
  if (!width) width = 804;
  if (!height) height = 500;
  $.fancybox({
    'href': href,
    'width': width,
    'height': height,
    'scrolling': 'no',
    'titleShow': false,
    'padding': 0,
    'autoScale': false,
    'transitionIn': 'none',
    'transitionOut': 'none',
    type: "inline",
    onClosed: function () {
      $(".EXTERN").css('display', 'none');
    }
  });
}

function showPopup(idd, title, h1, h2, data, chart, id) {
  if (typeof(data[0]) == 'undefined') {
  }
  else {
    id = typeof id !== 'undefined' ? id : 0;
    var strr = "";

    var inline;

    if ($("#" + idd).length != 0) $("#" + idd).remove();
    inline += '<div id="' + idd + '" class="EXTERN" style="display: block;">' +
      '<div  class="span-7 last" style="margin: 5px; width:780px; height:465px;">' +
      '<table>' +
      '<tr>' +
      '<td> ';

    if (chart == 'map') {
      inline += '<iframe src="/geochart.html#' + id + '" width="485" height="300" align="left" style="scrolling:no;">' +
        'Ваш браузер не поддерживает плавающие фреймы!' +
        '</iframe>';
    }
    else {
      inline += '</div> <div class="pie-export"><a href="#" onclick="chart2.exportChart (); return false;"><img src="img/btn_download_out.gif"></a><a href="#" onclick="chart2.print(); return false;"><img src="img/btn_print_out.gif"></a></div><div class="inline pie-diag" id="diagramm-popup">'; //добавление экспорта
    }

    inline += '	</td> \
	<td> \
    	<h4 class="span-3" style="margin-bottom: -6px; text-align: center; width: 285px;">' + title + '</h4>';


    inline += '<div class="row clear"></div> \
		<div class="text-black"> \
				<div class="row span-6 last text-lightgrey bold " style="border-bottom: 1px solid; width: 290px;"> \
		            <p class="span-2 text-center" style="width: 55px">№</p> \
		            <p class="span-3 text-center" style="width: 85px">' + h1 + '</p>';

    if (data[0].count_posts != undefined) {
      inline += '<p class="span-2 text-center" style="width: 50px">' + h2 + '</p><p class="span-1 text-center" style="width: 45px">Постов</p>';
    }
    else {
      inline += '<p class="span-2 text-right" style="width: 75px">' + h2 + '</p>';
    }
    inline += '	        </div>';
    inline += '      <div class="tableheaderborder clear"></div> \
                <div class="tablecontent span-7 last scroll" style="margin-left: 10px;">';

    var scrollname;
    if (title == 'Города') scrollname = 'scrollbarGor';
    else if (title == 'Ресурсы') scrollname = 'scrollbarRes';
    else if (title == 'Облако тегов') scrollname = 'scrollbarObl';
    else if (title == 'Лидеры мнений') scrollname = 'scrollbarLid';
    else if (title == 'Список слов') scrollname = 'scrollbarWord';
    else if (title == 'Охват') scrollname = 'scrollbarOhv';
    else if (title == 'Вовлеченность') scrollname = 'scrollbarVovl';

    //скроллинг начало
    inline += '<div id=\"' + scrollname + '\" class="scrollbar3"><div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div><div class="viewport"><div class="overview">';
    inline += '<table style="width: 250px;" class="dialog-List2">';

    if (typeof(data[0]) == 'undefined') {
    }
    else if (data[0].count_posts == undefined) {
      $.each(data, function (i, e) {
        var name = e.name;
        var namenl = name;
        if (name == undefined) name = e.nick;
        if (name == undefined) name = e.word;
        if (idd == 'resources-popup') {
          if (name == '.') return true;
          name = '<a href="messages_list.html#' + id + '"  onclick="$.cookie(\'' + id + '-resources-msg\',\'' + name + '\'); window.location.href = \'messages_list.html#' + id + '\'; return false;">' + name + '</a>';
        }
        if (idd == 'cities-popup') {
          if (name == '.') return true;
          name = '<a href="messages_list.html#' + id + '"  onclick="$.cookie(\'' + id + '-cities-msg\',\'' + name + '\'); window.location.href = \'messages_list.html#' + id + '\'; return false;">' + name.substr(0, 20) + '</a>';
        }
        if (idd == 'tags-popup') {
          if (name == '.') return true;
          name = '<a href="#" onclick="$.cookie(\'' + id + '-word_' + encodeURIComponent(name) + '\',\'' + true + '\'); window.location.href = \'messages_list.html#' + id + '\';  return false;">  ' + name + '</a>';
        }
        if (idd == 'tags-popup') {
          namenl = e.word;
        }
        strr += " " + namenl + "\t" + e.count + "\n";
        inline += '\
			<tr bgcolor="#fff"> \
				<td bgcolor="#fff"><div style="width: 35px; overflow: hidden">' + (i + 1) + '</div></td> \
				<td bgcolor="#fff"><div style="width: 120px; overflow: hidden">' + name + '</div></td> \
				<td bgcolor="#fff"><div style="width: 55px; overflow: hidden">' + e.count + '</div></td> \
			</tr>';
      });
    }
    else {
      $.each(data, function (i, e) {
        var name = e.name;
        var prom_id = e.id;
        if (name == undefined) name = e.nick;
        if (name == undefined) name = e.word;
        strr += " " + name + "\t" + e.count + "\t" + e.count_posts + "\n";
        name = '<a href="#" onclick="$.cookie(\'' + id + '-prom_' + prom_id + '\',\'' + true + '\'); window.location.href = \'messages_list.html#' + id + '\';  return false;">  ' + name + '</a>';
        inline += '\
			<tr bgcolor="#fff"> \
				<td bgcolor="#fff"><div style="width: 35px; overflow: hidden">' + (i + 1) + '</div></td> \
				<td bgcolor="#fff"><div style="width: 80px; overflow: hidden">' + name + '</div></td> \
				<td bgcolor="#fff"><div style="width: 55px; overflow: hidden">' + e.count + '</div></td> \
				<td bgcolor="#fff"><div style="width: 40px; overflow: hidden">' + e.count_posts + '</div></td> \
			</tr>';
      });
    }

    inline += '</table>';

    //скроллинг конец
    inline += '</div></div></div>';

    inline += '</div> \
		<div class="row clear"></div> \
		<div class="row clear" style="position:relative;">\
        <a class="span-7 last text-right text-lightgrey" id="' + idd + '-copy-button" style="font-size: 12px;">копировать в буфер</a></div> \
		</td> \
		</tr> \
		</table> \
</div></div>';
    $("body").append($(inline));
    $('#' + scrollname).tinyscrollbar();
    $('#' + scrollname).tinyscrollbar_update();
    //$('#scrollbar3').tinyscrollbar_update();
    loadmodal("#" + idd, 1000, 500);

    if (chart == 'map') {
    }
    else {
      console.log(chart);
      $('#diagramm-popup').html('');
      chart2 = new Highcharts.Chart({
        chart: {
          renderTo: 'diagramm-popup',
          width: 470,
          height: 300,
          plotBorderWidth: null
        },
        colors: [
          "#7f7bdb", "#c0ac97", "#b0c78f", "#c09797", "#97c0c0", "#86c4d1"
        ],
        legend: {
          enabled: false
        },
        credits: {
          enabled: false
        },
        title: {
          text: ''
        },
        tooltip: {
          formatter: function () {
            return '<b>' + this.point.name + '</b>: ' + Math.round(this.percentage) + ' %';
          },
          borderRadius: 3,
          borderWidth: 1,
          style: {
            fontSize: '10px'
          }
        },
        plotOptions: {
          series: {
            cursor: 'pointer',
            events: {
              click: function (event) {
                if (event.point.x < 5) {
                  var on_click = $(".dialog-List2 tbody tr:nth-child(" + (event.point.x + 1) + ") td:nth-child(2) div a").attr("onclick");
                  var res = on_click.replace(" return false;", "");
                  eval(res);
                }
              }
            }
          },
          pie: {
            animation: false,
            shadow: false,
            size: "60%",
            dataLabels: {
              enabled: true,
              color: '#000000',
              connectorColor: '#000000',
              softConnector: true,
              borderRadius: 0,
              connectorWidth: 1,
              overflow: 'justify',
              distance: 30,
              formatter: function () {
                if (Math.round(this.percentage) > 0) {
                  return '<div style="font-size: 10px; width: 20px; border: 1px solid #f00; display: block;">' + this.point.name + ': ' + Math.round(this.percentage) + '%</div>';
                }
              }
            },
            showInLegend: true
          }
        },
        series: [
          {
            type: 'pie',
            data: chart
          }
        ],
        navigation: {
          buttonOptions: {
            enabled: false
          }
        }
      });
    }
    $("#" + idd + "-copy-button").zclip({
      path: "js/ZeroClipboard.swf",
      beforeCopy: function () {
      },
      afterCopy: function () {
        return false;
      },
      copy: function () {
        return strr;
      }
    });
  }
}

function dateToWords(inDate, haveYear) {
  var date = inDate.split(".");

  var month = "";
  switch (parseInt(date[1], 10)) {
    case 1 :
      month = "января";
      break;
    case 2 :
      month = "февраля";
      break;
    case 3 :
      month = "марта";
      break;
    case 4 :
      month = "апреля";
      break;
    case 5 :
      month = "мая";
      break;
    case 6 :
      month = "июня";
      break;
    case 7 :
      month = "июля";
      break;
    case 8 :
      month = "августа";
      break;
    case 9 :
      month = "сентября";
      break;
    case 10 :
      month = "октября";
      break;
    case 11:
      month = "ноября";
      break;
    case 12:
      month = "декабря";
      break;
  }

  var result = parseInt(date[0], 10) + " " + month;
  if (haveYear == true) result = result + " '" + parseInt(date[2], 10) % 100;
  return result;
}

function today() {
  var today = Date.today();
  if (today.getTime() > maxDate.getTime()) today = maxDate;

  var boundaries = getDateBoundaries(minDate, maxDate, today, today);
  today = boundaries[2];

  $("#dp-end").datepicker("option", "minDate", minDate);
  $("#dp-begin").datepicker("option", "maxDate", maxDate);

  $("#dp-begin").datepicker("setDate", today);
  $("#dp-end").datepicker("setDate", today);

  $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
  $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

  $("#date #datepicker").val(
    dateToWords(today.format("dd.mm.yyyy"), true) + " - " +
      dateToWords(today.format("dd.mm.yyyy"), true));
  $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie("theme-compare-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie("theme-compare-md5", '');
  $("#progressbar").progressbar({value: 20});
  $(".progress").fadeIn();
  loadAllContent(today.format("dd.mm.yyyy"), today.format("dd.mm.yyyy"));
}

function yesterday() {
  var a = Date.today().add(-1).days();
  if (a.getTime() > maxDate.getTime()) a = maxDate.add(-1).days();

  var boundaries = getDateBoundaries(minDate, maxDate, a, Date.today());
  a = boundaries[2];

  $("#dp-end").datepicker("option", "minDate", minDate);
  $("#dp-begin").datepicker("option", "maxDate", maxDate);

  $("#dp-begin").datepicker("setDate", a);
  $("#dp-end").datepicker("setDate", a);

  $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
  $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

  $("#date #datepicker").val(
    dateToWords(a.format("dd.mm.yyyy"), true) + " - " +
      dateToWords(a.format("dd.mm.yyyy"), true));

  $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie("theme-compare-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie("theme-compare-md5", '');
  $("#progressbar").progressbar({value: 20});
  $(".progress").fadeIn();
  loadAllContent(a.format("dd.mm.yyyy"), a.format("dd.mm.yyyy"));
}

function week() {
  var o = Date.today();
  if (o.getTime() > maxDate.getTime()) o = maxDate;

  var a = new Date(o.getTime());
  var b = new Date(o.getTime());
  a.setDate(a.getDate() - 6);
  b.setDate(b.getDate());

  var boundaries = getDateBoundaries(minDate, maxDate, a, b);
  a = boundaries[2];
  b = boundaries[1];

  $("#dp-end").datepicker("option", "minDate", minDate);
  $("#dp-begin").datepicker("option", "maxDate", maxDate);

  $("#dp-begin").datepicker("setDate", a);
  $("#dp-end").datepicker("setDate", b);

  $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
  $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

  $("#date #datepicker").val(
    dateToWords(a.format("dd.mm.yyyy"), true) + " - " +
      dateToWords(b.format("dd.mm.yyyy"), true));

  $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie("theme-compare-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie("theme-compare-md5", '');
  $("#progressbar").progressbar({value: 20});
  $(".progress").fadeIn();
  loadAllContent(a.format("dd.mm.yyyy"), b.format("dd.mm.yyyy"));
}

function month() {
  var o = Date.today();
  if (o.getTime() > maxDate.getTime()) o = maxDate;

  var a = new Date(o.getTime());
  var b = new Date(o.getTime());

  a.setDate(a.getDate() - 29);
  b.setDate(b.getDate());

  var boundaries = getDateBoundaries(minDate, maxDate, a, b);
  a = boundaries[2];
  b = boundaries[1];

  $("#dp-end").datepicker("option", "minDate", minDate);
  $("#dp-begin").datepicker("option", "maxDate", maxDate);

  $("#dp-begin").datepicker("setDate", a);
  $("#dp-end").datepicker("setDate", b);

  $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
  $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

  $("#date #datepicker").val(
    dateToWords(a.format("dd.mm.yyyy"), true) + " - " +
      dateToWords(b.format("dd.mm.yyyy"), true));
  $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie("theme-compare-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie("theme-compare-md5", '');
  $("#progressbar").progressbar({value: 20});
  $(".progress").fadeIn();
  loadAllContent(a.format("dd.mm.yyyy"), b.format("dd.mm.yyyy"));
}

function year() {
  var o = Date.today();
  if (o.getTime() > maxDate.getTime()) o = maxDate;

  var a = new Date(o.getTime());
  var b = new Date(o.getTime());

  a.setFullYear(a.getFullYear() - 1);
  b.setFullYear(b.getFullYear());


  var boundaries = getDateBoundaries(minDate, maxDate, a, b);
  a = boundaries[2];
  b = boundaries[1];

  $("#dp-end").datepicker("option", "minDate", minDate);
  $("#dp-begin").datepicker("option", "maxDate", maxDate);

  $("#dp-begin").datepicker("setDate", a);
  $("#dp-end").datepicker("setDate", b);

  $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
  $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

  $("#date #datepicker").val(
    dateToWords(a.format("dd.mm.yyyy"), true) + " - " +
      dateToWords(b.format("dd.mm.yyyy"), true));

  $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie("theme-compare-md5", '');
  $("#progressbar").progressbar({value: 20});
  $(".progress").fadeIn();
  loadAllContent(a.format("dd.mm.yyyy"), b.format("dd.mm.yyyy"));
}

function visibleItem(it, isVisible) {
  if (isVisible == true) {
    $(it).parent().removeClass("nosub");
    $(it).parent().find(".sub").toggle();
  } else {
    $(it).parent().addClass("nosub");
    $(it).parent().find(".sub").hide();
  }
}

function getDateBoundaries(fromBoundary, toBoundary, fromAct, toAct) {
  var a1 = new Date(fromAct.getTime()) , a2 = new Date(fromAct.getTime());
  return [
    new Date(Math.min(new Date(Math.max(a1.add(-1).months().getTime(), fromBoundary.getTime())).getTime(), fromBoundary.getTime())),
    new Date(Math.min(toAct.getTime(), toBoundary.getTime())),
    new Date(Math.max(a2.getTime(), fromBoundary.getTime()))
  ];
}

function loadContent(id, dstart, dend) {

  var params = getThemeParams();
  params.order_id = id;
  params.start = dstart;
  params.end = dend;
  params.md5 = '';

  $.postJSON(ajaxURL_Order, params, function (data) {

    loadingCount--;
    loadingCount = loadingCount < 0 ? 0 : loadingCount;

    showThemeData(id, data);

    if( loadingCount == 0 )
    {
      updateDiagram(themeDataForDiagrams);
      $("#progressbar").progressbar({value: 100});
      $(".progress").fadeOut(1000);
      if( allCount <= 1 )
      {
        addNewThemePopup();
      }
    }
    else
    {
      var loading_index = Math.ceil(100/allCount);
      $("#progressbar").progressbar({value: 100-(loading_index*loadingCount)});
    }
  });
}

$(document).ready(function () {
  createDropDown("export-as", "34px");
  var loc = location.href.split('#');

  $("#date #datepicker").click(function (e) {
    $("#date .dp").toggle();
    $("#datepicker").blur();
    return false;
  });

  $("#date .ddp").click(function (e) {
    $("#date .dp").toggle();
    return false;
  });

  $("#home-url").attr("href", inernalURL_themesList);

  $("#home").attr("href", inernalURL_themesList);

  $("#user_email").text($.cookie("user_email"));

  exp = $.cookie("user_exp");
  if (exp == null) exp = 0;
  if (exp == null || exp < 4 || exp == "0" || exp == "Аккаунт заблокирован") $("#user_exp").addClass("warn");
  $("#user_exp").text(exp);

  $("#exit").attr("href", inernalURL_logout);
  $("#access").attr("href", inernalURL_accessSetup);

  $("#user_tariff").text($.cookie("user_tariff"));
  $("#user_tariff").attr("href", inernalURL_tariff + $.cookie("tarif_id"));
  $("#user_tariff").unbind("click").click(function (e) {
    loadmodal(inernalURL_tariff + $.cookie("tarif_id"), 300, 400, "iframe");
    return false;
  });

  $("#user_money").html($.cookie("user_money") + "&nbsp;<span class=\"rur\">p<span></span></span>");

  $("#billing").attr("href", inernalURL_billing + "?user_id=" + $.cookie("user_id"));
  $("#billing").unbind("click").click(function (e) {
    loadmodal(inernalURL_billing + "?user_id=" + $.cookie("user_id"), "50", "100%", "iframe");
    return false;
  });

  $("#user_consultant").text($.cookie("user_consultant"));
  /* НЕ ОПРЕДЕЛЕНО ПО ТЗ */
  $("#user_consultant").attr("href", "FOO");
  $("#user_consultant").unbind("click").click(function (e) {
    loadmodal("FOO", "75%", "75%", "iframe");
    return false;
  });

  $("#faq").attr("href", inernalURL_faq);

  $("#mail-btn").css("cursor", "pointer").click(function (e) {
    $("#export-form").attr("action", postURL_Email);
    var form = $("#export-form");
    $('[name="order_id"]', form).val(id);
    $('[name="start"]', form).val($("#dp-begin").datepicker("getDate").format("dd.mm.yyyy"));
    $('[name="end"]', form).val($("#dp-end").datepicker("getDate").format("dd.mm.yyyy"));
    $('[name="format"]', form).val($("#tdd-export-as").attr("value"));
    form.submit();
  });

	$(document).bind('click', function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("dd") && !$clicked.parents().hasClass("ui-datepicker-calendar") && !$clicked.parents().hasClass("ui-datepicker-prev") && !$clicked.parents().hasClass("ui-datepicker-next")) {
      $("#date .dp").hide();
      if (wasChanged) {
        wasChanged = false;

        filterChangeFlag();
        $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
        $.cookie("theme-compare-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
        $.cookie("theme-compare-md5", '');
        $.cookie("theme-compare-page-msg", 0);
      }
    }
    $clicked = $(e.target);
    if ((!$clicked.parents().hasClass("dropdown")))
    {
      $(".dropdown dd ul").hide();
    }
  });

  loadAllOrdersForSelect();
  loadAllThemesSettings();

  //change
  var notices = new Array('themeNotice');
  showNotices(notices);

  $('html').click(function (e) {
	  if ($(e.target).parents().hasClass("tree-dd") == false) {
            $('.tree-dd').fadeOut(100);
            $('.filter-type-selector').fadeOut(100);
            if (isChangedTree) {
            }
            isChangedTree = false;
          }

      if (($(e.target).parents().hasClass("export-list-db") == false) && ($(e.target).parents().hasClass("export_list") == false)) {
        if ($('.export-list').is(':visible')) {
          $('.export-list').fadeOut(400);
          $('.export-list_upper').fadeOut(400);
        }
      }
  });

  $.each(['positive', 'negative', 'neutral'], function (i, tone) {
    var isChecked = ($.cookie("theme-compare-" + tone + "-msg") == "true" || $.cookie("theme-compare-" + tone + "-msg") == undefined ? 1 : 0);
    $("#" + tone + " img").css('opacity', isChecked);
    $.cookie("theme-compare-" + tone + "-msg", isChecked == 1);
    $("#" + tone).unbind("click").click(function (e) {
      filterChangeFlag();
      $("img", this).css("opacity", ($("img", this).css("opacity") == 1 ? 0 : 1));
      $.cookie("theme-compare-" + tone + "-msg", $("img", this).css("opacity") == 1);
      return false;
    });
  });

  $("#tone_toggle").unbind("click").click(function (e) {
    if (filterChanged == true) {
      // Очистка груповой выборки
      hideFilters();
      $("#tone_toggle").css("background-color", "#CCC");
      filterChanged = false;
      $(window, document).scrollTop(0);
      var dstart = formatDate($.cookie("theme-compare-fromDate-theme"));
      var dend = formatDate($.cookie("theme-compare-toDate-theme"));
      $("#progressbar").progressbar({value: 20});
      $(".progress").fadeIn();
      loadAllContent(dstart, dend);
      return false;
    }
  });

  $('#clear_filter').unbind('click').click(function(){
    clearFilter();
  });

	$('.filter-type-selector .choice-button').unbind("click").click(function (e) {
	    var _this = $(this);
	    var filter_id = _this.parent().attr('id');
	    $('.choice-button', _this.parent()).removeClass('selected');
	    _this.addClass('selected');
	    $.cookie("theme-compare-" + filter_id, _this.attr('cast'));
	    filterChangeFlag();
	    e.stopPropagation();
	  });

  $('.add-theme-popup .controls .cancel').click(function(e){
    e.stopPropagation();
    $(this).parents('.add-theme-popup').hide();
  });
  $('.add-theme-popup .controls .ok').click(function(e){
    e.stopPropagation();
    var exist_ids = $.cookie('compareThemeList');
    if (exist_ids != null) {
      exist_ids = exist_ids.split(',');
    }
    else {
      exist_ids = []
    }
    var checked_ids = [];
    $('.add-theme-popup input:checked').each(function(){
      checked_ids.push($(this).val());
      if( $.inArray( $(this).val(), exist_ids ) < 0 )
      {
        addNewTheme($(this).val());
      }
    });
    $.each(exist_ids, function (i, id) {
      if( $.inArray( id, checked_ids ) < 0 )
      {
        removeTheme(id);
      }
    });
    $(this).parents('.add-theme-popup').hide();
  });
});

function themeNotice() {
  if ($.cookie("themeNoticeNot") != 1) {
    var mainNoticeId = $.gritter.add({
      title: 'Вы находитесь на странице сравнения тем.',
      text: 'На этой странице представлена обзорная аналитика по выбранной теме в удобной графической ' +
        'форме. Вы можете просмотреть динамику упоминаний на графике, распределение упоминаний' +
        ' по ресурсам и городам, увидеть наиболее активных авторов упоминаний. ' +
        '<br>Вы можете оперативно загрузить эту аналитику в удобном для вас формате или распечатать ее.<br>' +
        'Чтобы изменить интервал отображения данных, выберите нужные вам даты в поле период или' +
        'выделите его на графике под динамикой упоминаний.' +
        '<a href="#" id="themeNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
      sticky: true,
      time: '',
      class_name: 'my-sticky-class',
      after_close: function (e) { }
    });
    $('#themeNoticeNot').click(function () {
      $.cookie("themeNoticeNot", 1);
      hideNotice("themeNotice", 1);
      $.gritter.remove(mainNoticeId);
      return false;
    });
  }
}

function loadAllContent(dstart, dend) {

  var compare_ids = $.cookie('compareThemeList');
  if (compare_ids == null) {
    window.location.href = inernalURL_themesList;
  }
  else {
    compare_ids = compare_ids.split(',');
    loadingCount = compare_ids.length;
    allCount = compare_ids.length;
    $('.theme-info:not(.template)').remove();
    if (loadingCount >= 1) {
      $.each(compare_ids, function (i, id) {
        loadContent(id, dstart, dend);
      });
    }
  }
}

function removeTheme(remove_id) {
  var compare_ids = $.cookie('compareThemeList');
  var new_compare_ids = [];
  if (compare_ids != null) {
    compare_ids = compare_ids.split(',');
    $.each(compare_ids, function (i, val) {
      if (val != remove_id) {
        new_compare_ids.push(val);
      }
    })
  }
  $('#' + remove_id + 'compare_info').remove();
  allCount--;
  allCount = allCount < 0 ? 0 : allCount;
  $.cookie('compareThemeList', new_compare_ids.join(','));
  if (new_compare_ids.length <= 0) {
    $.cookie('compareThemeList', null);
    $.cookie('theme-compare-cities-msg', null);
    $.cookie('theme-compare-cities-msg-group', null);
    $.cookie('theme-compare-filter_cities-type', null);
    $.cookie('theme-compare-resources-msg', null);
    $.cookie('theme-compare-resources-msg-group', null);
    $.cookie('theme-compare-filter_resources-type', null);
    $.cookie('theme-compare-aditional-words', null);
    $.cookie('theme-compare-sort-msg', null);
    $.cookie('theme-compare-positive-msg', null);
    $.cookie('theme-compare-negative-msg', null);
    $.cookie('theme-compare-neutral-msg', null);
    window.location.href = inernalURL_themesList;
  }
  updateDiagram(themeDataForDiagrams);
  return false;
}

function addNewTheme(id) {
  var theme_ids = $.cookie('compareThemeList');
  if (theme_ids != null) {
    theme_ids = theme_ids.split(',');
  }
  else {
    theme_ids = []
  }
  if (theme_ids.length < 10) {
    allCount++ ;
    theme_ids.push(id);
    theme_ids = arrayUnique(theme_ids);
    $.cookie('compareThemeList', theme_ids.join(','));
    dstart = formatDate($.cookie("theme-compare-fromDate-theme"));
    dend = formatDate($.cookie("theme-compare-toDate-theme"));
    loadingCount++ ;
    $("#progressbar").progressbar({ value: 0 });
    $(".progress").fadeIn(1);
    loadContent(id, dstart, dend);
  }
  return false;
}

function addNewThemePopup()
{
  if( $('.add-theme-popup').css('display') != 'block' )
  {
    var compare_ids = $.cookie('compareThemeList');
    $('#add-theme-for-compare .content input[type="checkbox"]').removeAttr('checked');
    if (compare_ids != null) {
      compare_ids = compare_ids.split(',');
      $.each(compare_ids, function (i, id) {
        $('#add-theme-'+id).attr('checked','checked');
      });
    }
    $('.add-theme-popup').show();
  }
}

function loadAllOrdersForSelect()
{
  $.postJSON(ajaxURL_Orders, {'sort': 'default'}, function (data) {
    $.each(data.orders, function (key, order) {
      var html = '<li><input type="checkbox" id="add-theme-'+order.id+'" value="'+order.id+'"><label for="add-theme-'+order.id+'">'+order.keyword+'</label></li>';
      $('#add-theme-for-compare .content ul').append(html);
    });
  });
}

function setDatepickerDates() {
  var d1 = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10));
  var d2 = new Date(parseInt($.cookie("theme-compare-toDate-theme"), 10));

  minDate = new Date(parseInt($.cookie("theme-compare-minDate-theme"), 10));
  maxDate = new Date(parseInt($.cookie("theme-compare-maxDate-theme"), 10));

  // Установка выбора даты для фильтра
  if (!$("#dp-begin").hasClass("hasDatepicker")) {
    $("#date #datepicker").val(dateToWords(d1.format("dd.mm.yyyy"), true) + " - " + dateToWords(d2.format("dd.mm.yyyy"), true));

    $("#dp-begin").datepicker({
      dateFormat: "dd.mm.yy",
      firstDay: 1,
      minDate: minDate,
      maxDate: maxDate,
      onSelect: function (dateText, inst) {
        var str = $("#date #datepicker").val();
        var subs = str.split("-");
        wasChanged = true;
        $("#date #datepicker").val(dateToWords(dateText, true) + " -" + subs[1]);
        $("#dp-end").datepicker("option", "minDate", $(this).datepicker("getDate"));
      }
    }).datepicker("setDate", d1);

    $("#dp-end").datepicker({
      dateFormat: "dd.mm.yy",
      firstDay: 1,
      minDate: minDate,
      maxDate: maxDate,
      onSelect: function (dateText, inst) {
        var str = $("#date #datepicker").val();
        var subs = str.split("-");
        wasChanged = true;
        $("#date #datepicker").val(subs[0] + "- " + dateToWords(dateText, true));
        $("#dp-begin").datepicker("option", "maxDate", $(this).datepicker("getDate"));
      }
    }).datepicker("setDate", d2);
    $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
  }
}

var arrayUnique = function(a) {
  return a.reduce(function(p, c) {
    if (p.indexOf(c) < 0)
    {
      p.push(c);
    }
    return p;
  }, []);
};

function updateDiagram()
{
  var compare_ids = $.cookie('compareThemeList');
  if (compare_ids != null) {
    compare_ids = compare_ids.split(',');
    var series = [];
    $.each(compare_ids, function (i, id) {
      if( (id in themeDataForDiagrams) )
      {
        series.push(themeDataForDiagrams[id]);
      }
    });
    if( compare_ids.length == series.length )
    {
      buildDiagram(series);
    }
  }
}

function buildDiagram(series)
{
  $('#large').empty();
  // make the container smaller and add a second container for the master chart
  var $container = $('#lines-diagramm').css('position', 'relative');

  $('<div id="detail-container">').appendTo($container);

  $('<div id="master-container">').css({ position: 'absolute', top: 218, height: 60, width: '100%' }).appendTo($container);

  createMaster(series);

  $("tspan").each(function (index, element) {
    if ($(element).text() == "Highcharts.com") {
      $(element).text("");
    }
  });
}

function filterChangeFlag() {
  if (!filterChanged)
  {
    filterChanged = true;
    $("#tone_toggle").css("background-color", "#3CABDF");
  }
}

function hideFilters() {
  $(document).click();
  $('html').click();
  $("#filter_cities-tree").hide();
  $("#filter_resources-tree").hide();
}

function loadFiltersData() {
  var start = new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10)).format("dd.mm.yyyy");
  var end = new Date(parseInt($.cookie("theme-compare-toDate-theme"), 10)).format("dd.mm.yyyy");

  var compare_ids = $.cookie('compareThemeList');
  if (compare_ids == null) {
    window.location.href = inernalURL_themesList;
  }
  else {
    compare_ids = compare_ids.split(',');
    $.postJSON(ajaxURL_Filters, {order_id: compare_ids[0], start: start, end: end}, function (responce) {
      $("#filter_cities  *").unbind("click", jsTree_onClick).click(jsTree_onClick);
      $("#filter_cities-tree").jstree("destroy");

      $("#filter_cities-tree").css("display", "none");
      $("#filter_cities-tree").unbind("loaded.jstree")
        .bind("loaded.jstree", function (e, data) {
          var checked = $.cookie("theme-compare-cities-msg");
          var checkedarr = [];
          if (checked !== null && checked !== undefined && checked !== "") {
            checked = checked.split(",");
          }
          else {
            checked = [];
          }
          $.each(checked, function (i, tex) {
            $.each($("a:contains('" + tex + "')", $(e.target)), function (i, node) {

              if ($(node).text().trim() == tex) {
                checkedarr.push(tex);
                $(e.target).jstree("check_node", $(node));
              }
            });
          });
          $.cookie("theme-compare-cities-msg", checkedarr.join(","));

          checked = $.cookie("theme-compare-cities-msg-group");
          checkedarr = [];
          if (checked !== null && checked !== undefined && checked !== "") {
            checked = checked.split(",");
          }
          else {
            checked = [];
          }

          $.each(checked, function (i, tex) {
            $.each($("a:contains('" + tex + "')", $(e.target)), function (i, node) {

              if ($(node).text().trim() == tex) {
                checkedarr.push(tex);
                $(e.target).jstree("check_node", $(node));
              }
            });
          });
          $.cookie("theme-compare-cities-msg-group", checkedarr.join(","));

          checked = $.cookie("theme-compare-filter_cities-type");
          if (checked !== null && checked !== undefined && checked !== "") {
            $('#filter_cities-type .choice-button').removeClass('selected');
            $('#filter_cities-type .choice-button[cast="' + checked + '"]').addClass('selected');
          }

          $("#filter_cities .jstree-checkbox").click(jsTree_onCheck);
        })
        .jstree({
          "json_data": { "data": buildTreeCR(
            (
              (responce !== null && responce.params !== null) ?
                responce.params.city_tree : null
              ), "cities")},
          "plugins": [ "themes", "checkbox", "ui", "json_data" ],
          'core': { 'animation': false },
          'themes': { 'dots': true, 'icons': false }
        });

      $("#filter_resources  *").unbind("click", jsTree_onClick).click(jsTree_onClick);

      $("#filter_resources-tree").jstree("destroy");

      $("#filter_resources-tree").css("display", "none");
      $("#filter_resources-tree").unbind("loaded.jstree")
        .bind("loaded.jstree", function (e, data) {
          var checked = $.cookie("theme-compare-resources-msg");
          var checkedarr = [];
          if (checked !== null && checked !== undefined && checked !== "") {
            checked = checked.split(",");
          }
          else {
            checked = [];
          }
          $.each(checked, function (i, tex) {
            $.each($("a:contains('" + tex + "')", $(e.target)), function (i, node) {
              if ($(node).text().trim() == tex) {
                checkedarr.push(tex);
                $(e.target).jstree("check_node", $(node));
              }
            });
          });
          $.cookie("theme-compare-resources-msg", checkedarr.join(","));

          checked = $.cookie("theme-compare-resources-msg-group");
          checkedarr = [];
          if (checked !== null && checked !== undefined && checked !== "") {
            checked = checked.split(",");
          }
          else {
            checked = [];
          }
          $.each(checked, function (i, tex) {
            $.each($("a:contains('" + tex + "')", $(e.target)), function (i, node) {
              if ($(node).text().trim() == tex) {
                checkedarr.push(tex);
                $(e.target).jstree("check_node", $(node));
              }
            });
          });
          $.cookie("theme-compare-resources-msg-group", checkedarr.join(","));

          checked = $.cookie("theme-compare-filter_resources-type");
          if (checked !== null && checked !== undefined && checked !== "") {
            $('#filter_resources-type .choice-button').removeClass('selected');
            $('#filter_resources-type .choice-button[cast="' + checked + '"]').addClass('selected');
          }


          $("#filter_resources  .jstree-checkbox").click(jsTree_onCheck);
        })
        .jstree({
          "json_data": { "data": buildTreeCR(
            ((responce !== null && responce.params !== null && responce.params.source_tree !== null) ?
              responce.params.source_tree : null
              )
            , "resources") },
          "plugins": [ "themes", "checkbox", "ui", "json_data" ],
          'core': { 'animation': false },
          'themes': { 'dots': true, 'icons': false }
        });

      if (responce !== null && responce.params !== null && responce.params.tags !== null) {
        order_tags = responce.params.tags;
      }

      if (responce !== null && responce.params !== null) {
        fltr = responce.params;
      }
      initSlidePanel();
    });
  }
}

function t() {
  var time = new Date();
  return time.getTime();
}

var hidePadel = false, mm_hide = true;
function initSlidePanel() {
  $("#MM")
    .mouseenter(function (e) {
      mm_hide = false;
      $("#MM").fadeTo(100, 1);
    })
    .mouseleave(function (e) {
      mm_hide = true;
      setTimeout('if (hidePadel && mm_hide) $("#MM").fadeTo(100,0.0001);', 500);
    });

  // Построение
  $("#mm-template").hide();
  $("#mm-words").remove();

  $.each(mmItems, function (i, info) {
    var node = $("#mm-template").clone();
    node.removeClass("mm-itm-open").addClass("mm-itm-closed");
    node.attr("id", info.id).attr("index", i);
    // Заголовок
    $(".h > p > span.flag", node).html("+");
    $(".h > p > span.text", node).html(" &nbsp; " + info.title);
    $(".h", node).css("cursor", "pointer").click(function (e) {
      var parent = $(this).parents(".mm-item"),
        isOpened = parent.hasClass("mm-itm-open");
      if (isOpened) {
        parent.removeClass("mm-itm-open").addClass("mm-itm-closed");
        $(".h > p > span.flag", parent).html("+");
        $(".btns", parent).hide();
        $(".flist", parent).hide();
        $("#new-word", parent).hide();
      } else {
        parent.removeClass("mm-itm-closed").addClass("mm-itm-open");
        $(".h > p > span.flag", parent).html("&#8211;");
        $(".btns", parent).show();
        $(".flist", parent).show();
        $("#new-word", parent).show();
      }
      resizeWindowHeight();
      return false;
    });

    // Списки
    if (info.id == "mm-words") {
      flst = '';
      if ( $.cookie('theme-compare-aditional-words') != null && $.cookie('theme-compare-aditional-words').length > 0 )
      {
        var aditional_words = $.cookie('theme-compare-aditional-words').split(',');
        $.each(aditional_words, function (index, element) {
          wordcheck = '';
          flst += '<p class="fitem"><input type="checkbox" class="inline fcheck" id="word_' + element + '"' + wordcheck + '/><span>&nbsp;' + element + '</span></p>';
        });
      }

      $(".flist", node).html(flst);
    }

    // Кнопки
    $(".flist ", node).hide();
    $(".btns ", node).hide();
    $(".btns-bg > *", node)
      .removeClass("btn-dwn")
      .addClass("btn-up")
      .css("cursor", "pointer")
      .click(function (e) {
        var buttons = $(this).parents(".btns-bg"),
          index = parseInt($(this).parents(".mm-item").attr('index'), 10),
          cast = $(this).attr("cast");
        $("> *", buttons)
          .removeClass("btn-dwn")
          .addClass("btn-up");
        $(this).removeClass("btn-up").addClass("btn-dwn");
        mmItems[index].event(index, cast);

        var checked = $(this).attr('cast');
        if (checked == undefined || checked == null) {
          checked = "selected"; // По умолчанию выбрана вторая кнопка
          $.cookie('theme-compare' + info.id, checked);
        }
        else {
          $.cookie('theme-compare' + info.id, checked);
        }
        $('.btns-bg > [cast="' + checked + '"]', node).removeClass("btn-up").addClass("btn-dwn");
        filterChangeFlag();
        return false;
      });

    // Выставляем нужную кнопку
    var checked = $.cookie('theme-compare' + info.id);
    if (checked == undefined || checked == null) {
      checked = "selected"; // По умолчанию выбрана вторая кнопка
      $.cookie('theme-compare' + info.id, checked);
    }
    else {
      $.cookie('theme-compare' + info.id, checked);
    }

    $('.btns-bg > [cast="' + checked + '"]', node).removeClass("btn-up").addClass("btn-dwn");

    // Добавляем инпут для добавления слов
    if( info.id == "mm-words" )
    {
      $('.btns', node).before('<div id="new-word"><input type="text" value=""><button>Добавить</button></div>');
      $("#new-word", node).hide();
      $("#new-word button", node).click(function(){
        if( $("#new-word input", node).val().length > 0 )
        {
          $(".flist", node).append('<p class="fitem"><input type="checkbox" class="inline fcheck" id="word_' + $("#new-word input", node).val() + '" checked="checked"/><span>&nbsp;' + $("#new-word input", node).val() + '</span></p>');
          var aditional_words = [];
          if( $.cookie('theme-compare-aditional-words') != null && $.cookie('theme-compare-aditional-words').length > 0 )
          {
            aditional_words = $.cookie('theme-compare-aditional-words').split(',');
          }
          aditional_words.push($("#new-word input", node).val());
          $.cookie('theme-compare-aditional-words', aditional_words.join(','));
          $.cookie('theme-compare-word_' + encodeURIComponent($("#new-word input", node).val()), 'true');
          $("#new-word input", node).val('');
          resizeWindowHeight();
          filterChangeFlag();
        }
      });
    }

    $("#mm-items").append(node);
    if (checked != "selected") {
      mm_hide = false;
      hidePanel = false;
      //открываем меню с отмеченым чекбоксом
      $('#' + info.id).removeClass("mm-itm-closed").addClass("mm-itm-open");
      $('#' + info.id).children(".flist").show();
      $('#' + info.id).children(".btns").show();
      $('#' + info.id).children("#new-word").show();
    }
    node.show();
  });

  $.each($(".fcheck"), function (i, elm) {
    if ($.cookie('theme-compare-' + encodeURIComponent(elm.id)) == 'true') {//изменения тут!!!
      $('#' + elm.id).attr('checked', true);
      mm_hide = false;
      hidePanel = false;
      //открываем меню с отмеченым чекбоксом
      $('#' + elm.id).parents(".mm-itm-closed").removeClass("mm-itm-closed").addClass("mm-itm-open");
      $('#' + elm.id).parents(".flist").show();
      $('#' + elm.id).parents(".mm-itm-open").children(".btns").show();
      $('#' + elm.id).parents(".mm-itm-open").children("#new-word").show();
    }
    else {
      $('#' + elm.id).attr('checked', false);
    }
    resizeWindowHeight();
  });

  $(".fcheck").live('click', function () {
    var elm = $(this).attr('id');
    if ($(this).attr('checked')) {
      $.cookie('theme-compare-' + encodeURIComponent(elm), 'true'); //изменения тут!!!
    }
    else {
      $.cookie('theme-compare-' + encodeURIComponent(elm), 'false'); //изменения тут!!!
    }
    filterChangeFlag();
    page = 0;
  });

  var dstart = formatDate($.cookie("theme-compare-fromDate-theme"));
  var dend = formatDate($.cookie("theme-compare-toDate-theme"));
  loadAllContent(dstart, dend);
}

function jsTree_onClick(e) {
  hideFilters();
  var thisTree = $($($(e.target).parents(".jstree-wrapper")[0]).find(".tree-dd")[0]);
  $(".tree-dd").each(function (index, element) {
    if (!$(element).is(thisTree)) {
      $(element).fadeOut(100);
      $('.filter-type-selector', $(element).parent()).fadeOut(100);
    }
  });
  $(e.target).blur();
  thisTree.fadeIn(100);
  $('.filter-type-selector', thisTree.parent()).fadeIn(100);
  thisTree.jstree("focus");
  e.stopPropagation();
  return false;
}

function buildTreeCR(node, cookie) {
  var result = [];

  if (node !== null) {
    $.each(node, function (index, element) {
      if (typeof element !== "object") {
        result.push({"data": index });

      } else {
        result.push({
          "data": index,
          "children": buildTreeCR(element, cookie)
        });
      }
    });
  }
  return result;
}

var isChangedTree = false;
function jsTree_onCheck(e) {
  filterChangeFlag();
  var thisTree = $($($(e.target).parents(".jstree-wrapper")[0]).find(".tree-dd")[0]);
  setTimeout(function () {
    getTreeChecked($("#filter_cities-tree"), "theme-compare-cities-msg");
    getTreeChecked($("#filter_resources-tree"), "theme-compare-resources-msg");
    thisTree.change();
    isChangedTree = true;
  }, 200);
}

function getTreeChecked(tree, cookieName) {
  var groups = [];
  var checked = [];
  var cookieNameGroup = cookieName+'-group';

  $(".jstree-undetermined .jstree-checked.jstree-leaf > a", tree).each(function (i, e) {
    checked.push($(e).text().substr(2));
  });
  $(".jstree-no-icons > .jstree-checked > a", tree).each(function (i, e) {
    groups.push($(e).text().substr(2));
  });

  $.cookie(cookieName, checked.join(","));
  $.cookie(cookieNameGroup, groups.join(","));
  return { items: checked.join(","), groups: groups.join(",") };
}

function getThemeParams() { // используется при экспорте и загрузке контента
  if ($.cookie("theme-compare-md5") == null) {
    $.cookie("theme-compare-md5", "");
    md5 = '';
  }
  var params = {
    stime: new Date(parseInt($.cookie("theme-compare-fromDate-theme"), 10)).format("dd.mm.yyyy"),
    etime: new Date(parseInt($.cookie("theme-compare-toDate-theme"), 10)).format("dd.mm.yyyy"),
    positive: $.cookie("theme-compare-positive-msg"),
    negative: $.cookie("theme-compare-negative-msg"),
    neutral: $.cookie("theme-compare-neutral-msg"),
    post_type: $.cookie("theme-compare-show-msg"),
    md5: $.cookie("theme-compare-md5")
  };
  var list, filter_type, group_list, tree_val;

  // Добавляем фильтры бокового меню (mmItems)
  $.each(mmItems, function (i, filter) {
    params[filter.api] = $.cookie('theme-compare' + filter.id);
  });

  // Добавляем фильтры по лидерам и словам (mmItems)

  $.each($(".fcheck"), function (i, elm) {
    if ($.cookie('theme-compare-' + encodeURIComponent(elm.id)) == 'true') { //измения тут
      params[elm.id] = 'true';
    }
  });

  // Добавляем фильтр по городам
  list = $.cookie("theme-compare-cities-msg");
  if (list == undefined || list == null || list == "")
  {
    tree_val = getTreeChecked($("#filter_cities-tree"), "theme-compare-cities-msg");
    params['location'] = tree_val['items'];
  }
  else {
    params['location'] = list;
  }
  group_list = $.cookie("theme-compare-cities-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
    tree_val = getTreeChecked($("#filter_cities-tree"), "theme-compare-cities-msg");
    params['cou'] = tree_val['groups'];
  }
  else {
    params['cou'] = group_list;
  }

  filter_type = $.cookie("theme-compare-filter_cities-type");
  params['locations'] = 'selected';
  if (filter_type != undefined && filter_type != null && filter_type != "") {
    params['locations'] = filter_type;
  }
  //:~

  // Добавляем фильтр по ресурсам
  list = $.cookie("theme-compare-resources-msg");
  if (list == undefined || list == null || list == "")
  {
    tree_val = getTreeChecked($("#filter_resources-tree"), "theme-compare-resources-msg");
    params['res'] = tree_val['items'];
  }
  else {
    params['res'] = list;
  }
  group_list = $.cookie("theme-compare-resources-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
    tree_val = getTreeChecked($("#filter_resources-tree"), "theme-compare-resources-msg");
    params['shres'] = tree_val['groups'];
  }
  else {
    params['shres'] = group_list;
  }

  filter_type = $.cookie("theme-compare-filter_resources-type");
  params['hosts'] = 'selected';
  if (filter_type != undefined && filter_type != null && filter_type != "") {
    params['hosts'] = filter_type;
  }
  return params;
}

function showThemeData(id, data)
{
	  $("#tdd-filter-show").remove();
	  $('#filter-show option[selected="selected"]').removeAttr('selected');

	  var toCheck = $('#filter-show option[value="' + $.cookie("theme-compare-show-msg") + '"]');
	  if (toCheck.length)
	  {
	    $('#filter-show option[value="' + $.cookie("theme-compare-show-msg") + '"]').attr('selected', 'selected');
	  }
	  else
	  {
	    $($('#filter-show option')[0]).attr('selected', 'selected');
	  }

	  createDropDown("filter-show", 112);
	  $("#tdd-filter-show").unbind("change").change(function (e) {
	    filterChangeFlag();
	    $.cookie("theme-compare-show-msg", $(this).attr("value"));
	  });

	  urlCom = inernalURL_messages + id;
    graphtype = data.graphtype;

    var infoBlock = $('#L4').clone();
    infoBlock.removeClass('template');
    infoBlock.attr('id', id + 'compare_info');

    $('#title-block a', infoBlock).html(data.order_name).attr('href', inernalURL_themePage + id);

    $('#remove-block', infoBlock).click(function () {
      removeTheme(id);
    });

    // Панель с миниграффиками
    var value;
    // Постов
    var post_count = (data.posts > 0 || parseInt(data.posts) > 0 ) ? data.posts : 0;
    $("#posts", infoBlock).text(post_count);
    $("#posts", infoBlock).parent().find(" .sub img").attr("src", themapage_Templates.posts.replace("%order_id%", id));
    value = parseInt(data.posts_dyn, 10);
    $("#posts", infoBlock).parent().find(".sub-txt p")
      .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
      .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
    visibleItem("#posts", false);

    // Уникальных
    $("#uniq", infoBlock).text(data.uniq);
    $("#uniq", infoBlock).parent().find(" .sub img").attr("src", themapage_Templates.uniq.replace("%order_id%", id));
    value = parseInt(data.uniq_dyn, 10);
    $("#uniq", infoBlock).parent().find(".sub-txt p")
      .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
      .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
    visibleItem("#uniq", false);

    // Ресурсы
    $("#src", infoBlock).text(data.src);
    $("#src", infoBlock).parent().find(" .sub img").attr("src", themapage_Templates.src.replace("%order_id%", id));
    value = parseInt(data.src_dyn, 10);
    $("#src", infoBlock).parent().find(".sub-txt p")
      .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
      .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
    visibleItem("#src", false);

    // Аудитория
    $("#value", infoBlock).text(data.value);
    $("#value", infoBlock).parent().find(" .sub img").attr("src", themapage_Templates.aud.replace("%order_id%", id));
    value = parseInt(data.value_dyn, 10);
    $("#value", infoBlock).parent().find(".sub-txt p")
      .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
      .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
    visibleItem("#value", false);

    // Вовлеченность
    visibleItem("#engage", false);

    $(".item", infoBlock).each(function (index, element) {
      $(element).mouseenter(function (e) {
        var elem = $(e.target);
        if ($(elem).hasClass("nosub") == true) {
          $(".sub", elem).show();
          $(elem).removeClass("nosub");
        }
      });
      $(element).mouseleave(function (e) {
        var elem = $(e.target);
        if ($(elem).hasClass("nosub") != true) {
          if ($(elem).hasClass("item") == true) {
            $(".sub", elem).hide();
            $(elem).addClass("nosub");
          } else {
            $(elem).parent(".item").addClass("nosub");
            $(".sub", $(elem).parent(".item")).hide();
          }
        }
      });
    });

    var eng_mdin = [];
    var eng_count = 0;
    $(data.eng_mdin).each(function (index, element) {
      if (eng_mdin.length <= 5)
        eng_mdin[eng_mdin.length] = [element.name, element.count];
      else {
        eng_mdin[5][0] = "Другие";
        eng_mdin[5][1] += element.count;
      }
      eng_count += element.count;
    });

    if (data.engage == '0') {
      $("#engage-block", infoBlock).css("cursor", "default").fadeTo(0, 0.5);
    }
    else {
      $("#engage-block", infoBlock).css("cursor", "pointer").fadeTo(0, 1);
      $("#engage-block", infoBlock).unbind().click(function (e) {
        $(eng_mdin).each(function (index, value) {
          if (value[0] == "\"Мне нравится\" Вконтакте") eng_mdin[index][0] = "vk.com";
          if (value[0] == "Лайки Facebook") eng_mdin[index][0] = "facebook.com";
          if (value[0] == "Ретвиты Twitter") eng_mdin[index][0] = "twitter.com";
          if (value[0] == "Комментарии Livejournal") eng_mdin[index][0] = "livejournal.com";
        });
        showPopup("engage-popup", "Вовлеченность", "Ресурс", "Значение", data.eng_mdin, eng_mdin, id);
        return false;
      });
    }

    var sources = [];
    var sources_count = 0;
    $(data.sources).each(function (index, element) {
      if (sources.length <= 5)
        sources[sources.length] = [element.name, element.count];
      else {
        sources[5][0] = "Другие";
        sources[5][1] += element.count;
      }
      sources_count += element.count;
    });

    if (data.src == '0') {
      $("#src-block", infoBlock).css("cursor", "default").fadeTo(0, 0.5);
    }
    else {
      $("#src-block", infoBlock).css("cursor", "pointer").fadeTo(0, 1);
      $("#src-block", infoBlock).unbind().click(function (e) {
        showPopup("resources-popup", "Ресурсы", "Ресурс", "Постов", data.sources, sources, id);
        return false;
      });
    }

    var value_mdin = [];
    var value_count = 0;
    $(data.value_mdin).each(function (index, element) {
      if (value_mdin.length <= 5)
        value_mdin[value_mdin.length] = [element.name, element.count];
      else {
        value_mdin[5][0] = "Другие";
        value_mdin[5][1] += element.count;
      }
      value_count += element.count;
    });
    if (data.value == '0') {
      $("#value-block", infoBlock).css("cursor", "default").fadeTo(0, 0.5);
    }
    else {
      $("#value-block", infoBlock).css("cursor", "pointer").fadeTo(0, 1);
      $("#value-block", infoBlock).unbind().click(function (e) {
        showPopup("value-popup", "Охват", "Ресурс", "Охват", data.value_mdin, value_mdin, id);
        return false;
      });
    }


    var promotions = [];
    var promotions_count = 0;
    $(data.promotions).each(function (index, element) {
      if (promotions.length <= 5)
        promotions[promotions.length] = [element.nick, parseInt(element.count)];
      else {
        promotions[5][0] = "Другие";
        promotions[5][1] += parseInt(element.count);
      }
      promotions_count += element.count;
    });

    if (data.uniq == '0') {
      $("#uniq-block", infoBlock).css("cursor", "default").fadeTo(0, 0.5);
    }
    else {
      $("#uniq-block", infoBlock).css("cursor", "pointer").fadeTo(0, 1);
      $("#uniq-block", infoBlock).unbind().click(function (e) {
        showPopup("promoters-popup", "Лидеры мнений", "Ник", "Охват", data.promotions, promotions, id);
        return false;
      });
    }

    if (data.posts == '0' || data.posts == null) {
      $("#post-block", infoBlock).css("cursor", "default").fadeTo(0, 0.5);
    }
    else {
      $("#post-block", infoBlock).css("cursor", "default").fadeTo(0, 1);
    }

    $("#engage-block", infoBlock).tipTip({content: 'Вовлеченность – оценка участия аудитории в обсуждении темы и ее распространении.<br/> <a href="http://www.wobot.ru/faq#1_13" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
    $("#value-block", infoBlock).tipTip({content: 'Потенциальный охват аудитории.<br/><a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
    $("#src-block", infoBlock).tipTip({content: 'Количество ресурсов, на которых были найдены упоминания.'});

    $("#uniq-block", infoBlock).tipTip({content: 'Количество уникальных авторов'});
    $("#post-block", infoBlock).tipTip({content: 'Показать упоминания'});
    $(".paperTip").tipTip({content: 'Количество упоминаний', defaultPosition: "top"});
    $(".pieTip").tipTip({content: 'Процентная доля упоминаний', defaultPosition: "top"});
    $(".manTip").tipTip({content: 'Охват автора. <br/> <a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition: "top"});

    $(".checkTip").tipTip({content: 'Вес слова. <br/> <a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition: "top"});
    $("#exportmailTip").tipTip({content: 'Отправить экспорт на почту', defaultPosition: "top"});
    $("#exportTip").tipTip({content: 'Скачать экспорт', defaultPosition: "top"});
    $("#user_email").tipTip({content: 'Настройки пользователя', defaultPosition: "right"});
    $("#user_exp").tipTip({content: 'Для продления кабинета необходима оплата тарифа', defaultPosition: "bottom"});
    $("#diagram_export").tipTip({content: 'Скачать график', defaultPosition: "top"});
    $("#diagram_print").tipTip({content: 'Печать', defaultPosition: "top"});
    $("#newThemeCount").tipTip({content: 'Введите кол-во сообщения или процент от общего числа<br/> для создания новой темы с произвольной выборкой сообщений.<br/> Пример: если у вас в теме 100 сообщений и вы выбрали 50 (50%),<br/> то будет создана новая тема с произвольными 50 сообщениями из этой темы<br/> с сохранением всей разметки (теги, тональность) отобранных сообщений.'});

    var lines_date = [];
    shift = null;
    $.each(data.graph, function (index, element) {
      if( shift < (parseInt(index, 10) * 1000) && shift != null )
      {
        return false;
      }
      shift = parseInt(index, 10) * 1000;
    });

    if (graphtype == 'hour') {
      tickInt = 1;
    }
    else if (graphtype == 'day') {
      tickInt = 1;
    }
    else if (graphtype == 'week') {
      tickInt = 1;
    }
    else if (graphtype == "month" || graphtype == "quarter" || graphtype == "halfyear") {
      tickInt = 1;
    }
    var i = 1;
    $.each(data.graph, function (index, element) {
      lines_date.push([i, element]);
      i++;
    });

    themeDataForDiagrams[id] =
    { name: data.order_name,
      data: lines_date,
      showInLegend: true,
      marker: {
        symbol: "circle"
      }
    };

    $('#add-theme-bg').before(infoBlock);
}

function resizeWindowHeight()
{
  if( $('#mm-items').innerHeight() > 690 )
  {
    $('#MM').css({height: $('#mm-items').innerHeight() + 'px'});
    $('#container').css({height: $('#mm-items').innerHeight() + 250 + 'px'});
    $('#body').css({height: $('#mm-items').innerHeight() + 110 + 'px', minHeight : '0'});
  }
  else
  {
    $('#MM').css({height: '690px'});
    $('#container').css({height: 'auto'});
    $('#body').css({height: '100%', minHeight: '100%'});
  }
}

function loadAllThemesSettings()
{
  $("#progressbar").progressbar({ value: 10 });
  $(".progress").fadeIn(1);
  var compare_ids = $.cookie('compareThemeList');
  if (compare_ids == null) {
    window.location.href = inernalURL_themesList;
  }
  else {
    compare_ids = compare_ids.split(',');
    loadingCount = compare_ids.length;
    allCount = compare_ids.length;
    if (loadingCount >= 1) {
      var theme_maxDate = 0;
      var theme_minDate = 99999999999999999999;
      $.each(compare_ids, function (i, id) {
        $.postJSON(ajaxURL_getThemeSettings, {order_id: id}, function (responce) {

          theme_minDate = parseInt(responce.order_start) < theme_minDate ? responce.order_start : theme_minDate;
          theme_maxDate = parseInt(responce.order_end) > theme_maxDate ? responce.order_end : theme_maxDate;

          loadingCount--;
          loadingCount = loadingCount < 0 ? 0 : loadingCount;
          if( loadingCount == 0 )
          {
            theme_minDate = new Date(parseInt(theme_minDate+'000'));
            theme_maxDate = new Date(parseInt(theme_maxDate+'000'));
            $.cookie("theme-compare-minDate-theme", theme_minDate.getTime());
            $.cookie("theme-compare-maxDate-theme", theme_maxDate.getTime());
            if ($.cookie("theme-compare-fromDate-theme") == null) {
              $.cookie("theme-compare-fromDate-theme", theme_minDate.getTime());
            }
            if ($.cookie("theme-compare-toDate-theme") == null) {
              $.cookie("theme-compare-toDate-theme", theme_maxDate.getTime());
            }
            setDatepickerDates();
            loadFiltersData();
          }
        });
      });
    }
  }
}

function clearFilter()
{
  $.cookie("theme-compare-md5", null);
  $.cookie("theme-compare-page-msg", null);
  $.cookie("theme-compare-aditional-words", null);
  $.cookie("theme-compare-fromDate-theme", null);
  $.cookie("theme-compare-toDate-theme", null);
  $.cookie("theme-compare-sort-msg", null);
  $.cookie("theme-compare-positive-msg", null);
  $.cookie("theme-compare-negative-msg", null);
  $.cookie("theme-compare-neutral-msg", null);
  $.cookie("theme-compare-show-msg", null);
  $.cookie("theme-compare-cities-msg", null);
  $.cookie("theme-compare-cities-msg-group", null);
  $.cookie("theme-compare-filter_cities-type", null);
  $.cookie("theme-compare-resources-msg", null);
  $.cookie("theme-compare-resources-msg-group", null);
  $.cookie("theme-compare-filter_resources-type", null);
  $.cookie("theme-compare-filter_resources-type", null);
  $.each(mmItems, function (i, filter) {
    $.cookie('theme-compare' + filter.id, null);
  });
  $.each($(".fcheck"), function (i, elm) {
    $.cookie('theme-compare-' + encodeURIComponent(elm.id), null);
  });
  window.location.reload();
}