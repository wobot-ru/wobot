var wasChanged = false;
var defaultDiagramData = [];
var id;
var lines_date = [];
var wnsi = null;
var chart, chart2;
var minDate, maxDate, graphtype, tickInt;
var urlCom, shift, theme;
var start_interval = false;
var filterChanged = false;
var detailChart;
var hidePadel = false, mm_hide = true, isChangedTree = false;
var mmItems = [
  {id: "mm-promouters",
    api: "Promotions",
    title: "Лидеры Мнений",
    event: onSelectMMitem
  },
  {id: "mm-words",
    api: "words",
    title: "Со словами",
    event: onSelectMMitem
  },
  {
    id: "mm-tags",
    api: "tags",
    title: "С тегами",
    event: onSelectMMitem
  }
];

var index_popup_html = '<p><strong>Net Sentiment Index (NSI)</strong> – отражает эмоциональность обсуждения темы в числовом выражении.<br/>' +
  'Показатель NSI рассчитывается исходя из количества позитивных, негативных и нейтральных сообщений.' +
  'При расчете индекса значимость каждого сообщения одинакова.</p>' +
  '<p><strong>Weight Net Sentiment Index (WNSI)</strong> - исипользуется для оценки эмоциональности обсуждения темы,' +
  ' с учетом разной значимости исследуемых площадок.' +
  ' Значимость(вес) площадок задается вручную.</p>' +
  '<ul style="font-size: 12px;">' +
  '<li>Значения индексов всегда находятся в интервале от -1 до 1.</li>' +
  '<li>Индекс=1, когда в теме есть только позитивные и/ или нейтральные сообщения.</li>' +
  '<li>Индекс=-1 когда в теме есть только негативные сообщения</li>' +
  '<li>Индекс=0 когда количество негативных сообщений в теме равно сумме позитивных и нейтральных.</li>' +
  '</ul>';

function onSelectMMitem(index, cast) {
  return false;
  $.cookie(id + mmItems[index].id, cast);
  filterChangeFlag();
}

function updateTips(t, tip) {
  $(tip).text(t);
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

function GetExportList() {
  $('.export-list').toggle(400);
  $('.export-list_upper').toggle(400);

  FillExportList();
  if (start_interval == false) {
    setTimeout('FillExportList();', 5000);
  }
}

function sleep(sleep_ms) {
  sleep_ms += new Date().getTime();
  while (new Date() < sleep_ms) {
  }
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

function clearFilters() {
  var cookie_date = document.cookie.split(";");
  var re = new RegExp("([0-9]{3,4}-cities-msg)=.*");
  for (var i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}-resources-msg)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}-show-msg)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}-sort-msg)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}mm-promouters)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}mm-tags)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}mm-words)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}-positive-msg)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}-negative-msg)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  cookie_date = document.cookie.split(";");
  re = new RegExp("([0-9]{3,4}-neutral-msg)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re) != -1) {
      $.cookie(cookie_date[i].match(re)[1], null);
    }
  }
  var cookie_prod = document.cookie.split(";");
  var re_prod = new RegExp("([0-9]{3,4}-prom_.*)=.*");
  for (i = 0; i < cookie_prod.length; i++) {
    if (cookie_prod[i].search(re_prod) != -1) {
      $.cookie(cookie_prod[i].match(re_prod)[1], null);
    }
  }
  var cookie_word = document.cookie.split(";");
  var re_word = new RegExp("([0-9]{3,4}-word_.*)=.*");
  for (i = 0; i < cookie_word.length; i++) {
    if (cookie_word[i].search(re_word) != -1) {
      $.cookie(cookie_word[i].match(re_word)[1], null);
    }
  }
  var cookie_word = document.cookie.split(";");
  var re_word = new RegExp("([0-9]{3,4}-tag_.*)=.*");
  for (i = 0; i < cookie_word.length; i++) {
    if (cookie_word[i].search(re_word) != -1) {
      $.cookie(cookie_word[i].match(re_word)[1], null);
    }
  }
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

function showPopup(idd, title, h1, h2, data, chart) {
  if (typeof(data[0]) == 'undefined') {
  }
  else {
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
    else if (title == 'Индексы') {
      inline += index_popup_html;
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
		            <p class="span-2 text-center" style="width: 55px">№</p>';

    if (title == 'Индексы') {
      inline += '<p class="span-3 text-center" style="width: 55px">' + h1 + '</p>';
    }
    else {
      inline += '<p class="span-3 text-center" style="width: 85px">' + h1 + '</p>';
    }

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
    else if (title == 'Индексы') scrollname = 'scrollbarIndex';

    //скроллинг начало
    inline += '<div id=\"' + scrollname + '\" class="scrollbar3"><div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div><div class="viewport"><div class="overview">';
    inline += '<table style="width: 250px;" class="dialog-List2">';

    if (typeof(data[0]) == 'undefined') {
    }
    else if (title == 'Индексы') {
      $.each(data, function (i, e) {
        var name = e.name;
        var namenl = name;
        strr += " " + namenl + "\t" + e.count + "\n";
        inline += '<tr bgcolor="#fff"> \
        <td bgcolor="#fff"><div style="width: 35px; overflow: hidden">' + (i + 1) + '</div></td> \
        <td bgcolor="#fff"><div style="width: 55px; overflow: hidden">' + name + '</div></td> \
        <td bgcolor="#fff"><div style="width: 120px; overflow: hidden">' + e.count + '</div></td> \
      </tr>';
      });
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

    if (typeof(chart) != 'undefined') {
      if (chart == 'map' || chart == 'index') {
      }
      else {
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
  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie(id + "-md5", '');
  loadContent(id, today.format("dd.mm.yyyy"), today.format("dd.mm.yyyy"));
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

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie(id + "-md5", '');
  loadContent(id, a.format("dd.mm.yyyy"), a.format("dd.mm.yyyy"));
}

function correctWeekDay(day) {
  if (day == 0) return 7; else return day;
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

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie(id + "-md5", '');
  loadContent(id, a.format("dd.mm.yyyy"), b.format("dd.mm.yyyy"));
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
  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  $.cookie(id + "-md5", '');
  loadContent(id, a.format("dd.mm.yyyy"), b.format("dd.mm.yyyy"));
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

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-md5", '');
  loadContent(id, a.format("dd.mm.yyyy"), b.format("dd.mm.yyyy"));
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

function sortSourse(arr) {
  var new_arr = [];
  var len = arr.length;
  for (var j = 0; j < len; j++) {
    var temp_i = 0;
    var small = 0;
    for (var i = 0; i < arr.length; i++) {
      if (arr[i][1] >= small) {
        small = arr[i][1];
        temp_i = i;
      }
    }
    arr[temp_i][0] = arr[temp_i][0].toString();
    new_arr.push(arr[temp_i]);
//    console.log(arr[temp_i]);
    arr.splice(temp_i, 1);
  }
  return new_arr;
}

function loadContent(id, dstart, dend) {
  $("#progressbar").progressbar({ value: 0 });
  $(".progress").fadeIn(1);

  $.postJSON(ajaxURL_getThemeSettings, {order_id: id}, function (responce) {
    $("#progressbar").progressbar("option", "value", 10);

    minDate = new Date(parseInt(responce.order_start + '000'));
    maxDate = ( parseInt(responce.order_end + '000') < Date.today().getTime() ) ? new Date(parseInt(responce.order_end + '000')) : Date.today();
    var boundaries = getDateBoundaries(minDate, maxDate, Date.today(), Date.today());

    var d1 = $.cookie(id + "-fromDate-theme") == null ? boundaries[0] : new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
    var d2 = $.cookie(id + "-toDate-theme") == null ? boundaries[1] : new Date(parseInt($.cookie(id + "-toDate-theme"), 10));

    // Установка выбора даты для фильтра
    if (!$("#dp-begin").hasClass("hasDatepicker")) {
      $("#date #datepicker").val(
        dateToWords(d1.format("dd.mm.yyyy"), true) + " - " +
          dateToWords(d2.format("dd.mm.yyyy"), true));

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

    if ($.cookie(id + "-fromDate-theme") == null) {
      $.cookie(id + "-fromDate-theme", minDate.getTime());
    }

    if ($.cookie(id + "-toDate-theme") == null) {
      $.cookie(id + "-toDate-theme", maxDate.getTime());
    }
    loadFiltersData();
  });
}

$(document).ready(function () {
//  createDropDown("export-as", "34px");
  var loc = location.href.split('#');
  id = loc[loc.length - 1];

  if ((id == '') || (id == null)) {
    window.location.replace("/");
  }

  $("#themeedit").attr("href", "http://beta.wobot.ru/theme_edit.html#" + id);

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

  $('#newThemeCount').keyup(function () {
    var val = $(this).val();
    $.trim(val);
    var intVal = parseInt(val);
    if (val.length > 0) {
      if (intVal == val) {
        if (intVal > theme.posts) {
          $('.make-copy .error').show().html('Введите значение меньше,<br/> чем количество сообщений в теме (' + theme.posts + ')');
          $('.make-copy .create').attr('disabled', 'disabled');
        }
        else {
          $('.make-copy .error').hide();
          $('.make-copy .create').removeAttr('disabled');
        }
      }
      else if (intVal + '%' == val) {
        if (intVal >= 100) {
          $('.make-copy .error').show().html('Введите значение меньше 100%');
          $('.make-copy .create').attr('disabled', 'disabled');
        }
        else {
          $('.make-copy .error').hide();
          $('.make-copy .create').removeAttr('disabled');
        }
      }
      else {
        $('.make-copy .error').show().html('Введите цифровое или процентное значение');
        $('.make-copy .create').attr('disabled', 'disabled');
      }
    }
    else {
      $('.make-copy .error').hide();
      $('.make-copy .create').attr('disabled', 'disabled');
    }

  });

  $('.make-copy .create').click(function () {
    var val = $('#newThemeCount').val();
    $.trim(val);
    var params = { order_id: id, selection_size: parseInt(val) };
    if (val.indexOf('%') != -1) {
      params = { order_id: id, selection_size_proc: parseInt(val) };
    }
    $("#progressbar").progressbar("option", "value", 0);
    $(".progress").fadeIn();
    $.postJSON(ajaxURL_RandomTheme, params, function (responce) {
      if (responce.status == 'ok') {
        window.location.href = '/themes_list.html';
      }
    });
  });

  $(document).bind('click', function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("dd") && !$clicked.parents().hasClass("ui-datepicker-calendar") && !$clicked.parents().hasClass("ui-datepicker-prev") && !$clicked.parents().hasClass("ui-datepicker-next")) {
      $("#date .dp").hide();
      if (wasChanged) {
        wasChanged = false;
        filterChangeFlag();
        $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
        $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
        $.cookie(id + "-md5", '');
        $.cookie(id + "-page-msg", 0);
      }
    }
    $clicked = $(e.target);
    if ((!$clicked.parents().hasClass("dropdown"))) {
      $(".dropdown dd ul").hide();
    }
  });

  $("#export .btn a").click(function () {
    $(".dropdown dd ul").toggle();
    return false;
  });

  var dstart = formatDate($.cookie(id + "-fromDate-theme"));
  var dend = formatDate($.cookie(id + "-toDate-theme"));

  loadContent(id, dstart, dend);

  //change
  var notices = new Array('themeNotice');
  showNotices(notices);

  //themeNotice();

  $("#compare-block a").click(function () {
    return false;
  });

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
    var isChecked = ($.cookie(id + "-" + tone + "-msg") == "true" || $.cookie(id + "-" + tone + "-msg") == undefined ? 1 : 0);
    $("#" + tone + " img").css('opacity', isChecked);
    $.cookie(id + "-" + tone + "-msg", isChecked == 1);
    $("#" + tone).unbind("click").click(function (e) {
      filterChangeFlag();
      $("img", this).css("opacity", ($("img", this).css("opacity") == 1 ? 0 : 1));
      $.cookie(id + "-" + tone + "-msg", $("img", this).css("opacity") == 1);
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
      reloadContent();
      return false;
    }
  });

  $('#clear_filter').unbind('click').click(function () {
    clearFilter();
  });

  $('.filter-type-selector .choice-button').unbind("click").click(function (e) {
    var _this = $(this);
    var filter_id = _this.parent().attr('id');
    $('.choice-button', _this.parent()).removeClass('selected');
    _this.addClass('selected');
    $.cookie(id + "-" + filter_id, _this.attr('cast'));
    filterChangeFlag();
    e.stopPropagation();
  });

  /*if(location.host=="wobotanalytics.cloudapp.net" || location.host=="beta.wobot.ru" || location.host=="11.11.11.11"){
    $("#tagcloud").show();
    if(location.host=="wobotanalytics.cloudapp.net"){
      $("#tagcloud a").attr("href", "http://"+location.host+"/production/lem/lem.html?order_id="+id+"&ostart="+($.cookie(id + "-fromDate-theme")/1000)+"&oend="+($.cookie(id + "-toDate-theme")/1000));
    } else {
      $("#tagcloud a").attr("href", "http://"+location.host+"/lem/lem.html?order_id="+id+"&ostart="+($.cookie(id + "-fromDate-theme")/1000)+"&oend="+($.cookie(id + "-toDate-theme")/1000));
    }
  }*/

});

function themeNotice() {
  if ($.cookie("themeNoticeNot") != 1) {
    var mainNoticeId = $.gritter.add({
      title: 'Вы находитесь на странице аналитики по выбранной теме.',
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
      after_close: function (e) {
        //newresNotice();
      }
    });
    $('#themeNoticeNot').click(function () {
      $.cookie("themeNoticeNot", 1);
      hideNotice("themeNotice", 1);
      $.gritter.remove(mainNoticeId);
      return false;
    });
  }
}

function addThemeToCompare() {
  var theme_ids = $.cookie('compareThemeList');
  if (theme_ids != null) {
    theme_ids = theme_ids.split(',');
  }
  else {
    theme_ids = []
  }
  if (theme_ids.length < 10) {
    theme_ids.push(id);
  }
  theme_ids = arrayUnique(theme_ids);
  if (theme_ids.length == 1 || $.cookie('theme-compare-fromDate-theme') == null || $.cookie("theme-compare-toDate-theme") == null || $.cookie('theme-compare-minDate-theme') == null || $.cookie("theme-compare-maxDate-theme") == null) {
    $.cookie("theme-compare-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
    $.cookie("theme-compare-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
    $.cookie("theme-compare-minDate-theme", $("#dp-begin").datepicker("getDate").getTime());
    $.cookie("theme-compare-maxDate-theme", $("#dp-end").datepicker("getDate").getTime());
  }
  $.cookie('compareThemeList', theme_ids.join(','));
  window.location.href = inernalURL_themesCompare;
  return false;
}

var arrayUnique = function (a) {
  return a.reduce(function (p, c) {
    if (p.indexOf(c) < 0) {
      p.push(c);
    }
    return p;
  }, []);
};

function filterChangeFlag() {
  if (!filterChanged) {
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

function loadFiltersData(md5) {
  var start = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10)).format("dd.mm.yyyy");
  var end = new Date(parseInt($.cookie(id + "-toDate-theme"), 10)).format("dd.mm.yyyy");

  $.postJSON(ajaxURL_Filters, {order_id: id, start: start, end: end}, function (responce) {
    $("#filter_cities  *").unbind("click", jsTree_onClick).click(jsTree_onClick);
    $("#filter_cities-tree").jstree("destroy");

    $("#filter_cities-tree").css("display", "none");
    $("#filter_cities-tree").unbind("loaded.jstree")
      .bind("loaded.jstree", function (e, data) {
        var checked = $.cookie(id + "-cities-msg");
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
        $.cookie(id + "-cities-msg", checkedarr.join(","));

        checked = $.cookie(id + "-cities-msg-group");
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
        $.cookie(id + "-cities-msg-group", checkedarr.join(","));

        checked = $.cookie(id + "-filter_cities-type");
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
        var checked = $.cookie(id + "-resources-msg");
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
        $.cookie(id + "-resources-msg", checkedarr.join(","));

        checked = $.cookie(id + "-resources-msg-group");
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
        $.cookie(id + "-resources-msg-group", checkedarr.join(","));

        checked = $.cookie(id + "-filter_resources-type");
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

    $("#progressbar").progressbar("option", "value", 40);
  });
}

function t() {
  var time = new Date();
  return time.getTime();
}

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

  // Пресеты
  $("#presets").hide();

  // Загружаем пресеты
  $("#presets .template").hide();
  $.postJSON(ajaxURL_GetPresets, {order_id: id}, function (data) {
    if (data != null) {
      $.each(data, function (i, info) {
        var preset = $("#presets .template").clone();
        preset.attr("id", "preset-" + info.id).attr("pk", info.id).removeClass("template");
        $(".title", preset).text(info.name);
        preset.show();
        preset.insertAfter($("#presets-header"));
      });
    }
    $("#presets > .preset .load-preset")
      .css("cursor", "pointer")
      .click(function (e) {
        var id = parseInt($(this).parents(".preset").attr("pk"), 10);
        //loadPreset(id);
        return false;
      });

    $("#presets > .preset .x-preset")
      .css("cursor", "pointer")
      .click(function (e) {
        var id = parseInt($(this).parents(".preset").attr("pk"), 10);
        deletePreset(id);
        return false;
      });
    reloadContent();
  });
  //:~

  // Построение
  $("#mm-template").hide();
  $("#mm-promouters").remove();
  $("#mm-tags").remove();
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
    if (info.id == "mm-promouters") {
      flst = '';
      var selpromo = $.cookie(id + '-selpromo');
      if (fltr !== null && fltr !== undefined && fltr.promotions !== null) {
        $.each(fltr.promotions, function (index, element) {
          selcheck = '';
          if (selpromo == element.id) {
            selcheck = ' checked="checked"';
            $.cookie(id + '-prom_' + element.id, 'true');
            $.cookie(id + '-selpromo', null);
          }
          flst += '<p class="fitem"><input type="checkbox" class="inline fcheck" id="prom_' + element.id + '"' + selcheck + '/><span>&nbsp;' + element.nick + '</span></p>';
        });
      }
      $(".flist", node).html(flst);
    }
    if (info.id == "mm-words") {

      flst = '';
      var selword = $.cookie(id + '-selword');
      if (fltr !== null && fltr !== undefined && fltr.words !== null) {
        $.each(fltr.words, function (index, element) {
          wordcheck = '';
          if (encodeURIComponent(selword) == encodeURIComponent(element.word)) {  //изменение тут
            wordcheck = ' checked="checked"';
            $.cookie(id + '-word_' + element.word, 'true');
            $.cookie(id + '-selword', null);
          }

          if (selword != element.word && $.cookie(id + '-word_' + element.word) != null && selword != null) {
            wordcheck = '';
            $.cookie(id + '-word_' + element.word, null);
          }
          flst += '<p class="fitem"><input type="checkbox" class="inline fcheck" id="word_' + element.word + '"' + wordcheck + '/><span>&nbsp;' + element.word + '</span></p>';
        });
      }

      if ($.cookie(id + '-aditional-words') != null && $.cookie(id + '-aditional-words').length > 0) {
        var aditional_words = $.cookie(id + '-aditional-words').split(',');
        $.each(aditional_words, function (index, element) {
          wordcheck = '';
          flst += '<p class="fitem"><input type="checkbox" class="inline fcheck" id="word_' + element + '"' + wordcheck + '/><span>&nbsp;' + element + '</span></p>';
        });
      }

      $(".flist", node).html(flst);
    }
    if (info.id == "mm-tags") {
      flst = '';
      var seltag = $.cookie(id + '-seltag');
      if (fltr !== null && fltr !== undefined && fltr.tags !== null) {
        $.each(fltr.tags, function (index, element) {
          tagcheck = '';
          if (seltag == index) {
            tagcheck = ' checked="checked"';
            $.cookie(id + '-tag_' + index, 'true');
            $.cookie(id + '-seltag', null);
          }
          flst += '<p class="fitem"><input type="checkbox" class="inline fcheck" id="tag_' + index + '"' + '' + '/><span>&nbsp;' + element + '</span></p>';
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
          $.cookie(id + info.id, checked);
        }
        else {
          $.cookie(id + info.id, checked);
        }
        $('.btns-bg > [cast="' + checked + '"]', node).removeClass("btn-up").addClass("btn-dwn");
        filterChangeFlag();
        return false;
      });

    // Выставляем нужную кнопку
    var checked = $.cookie(id + info.id);
    if (checked == undefined || checked == null) {
      checked = "selected"; // По умолчанию выбрана вторая кнопка
      $.cookie(id + info.id, checked);
    }
    else {
      $.cookie(id + info.id, checked);
    }

    $('.btns-bg > [cast="' + checked + '"]', node).removeClass("btn-up").addClass("btn-dwn");

    // Добавляем инпут для добавления слов
    if (info.id == "mm-words") {
      $('.btns', node).before('<div id="new-word"><input type="text" value=""><button>Добавить</button></div>');
      $("#new-word", node).hide();
      $("#new-word button", node).click(function () {
        if ($("#new-word input", node).val().length > 0) {
          $(".flist", node).append('<p class="fitem"><input type="checkbox" class="inline fcheck" id="word_' + $("#new-word input", node).val() + '" checked="checked"/><span>&nbsp;' + $("#new-word input", node).val() + '</span></p>');
          var aditional_words = [];
          if ($.cookie(id + '-aditional-words') != null && $.cookie(id + '-aditional-words').length > 0) {
            aditional_words = $.cookie(id + '-aditional-words').split(',');
          }
          aditional_words.push($("#new-word input", node).val());
          $.cookie(id + '-aditional-words', aditional_words.join(','));
          $.cookie(id + '-word_' + encodeURIComponent($("#new-word input", node).val()), 'true');
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
    if ($.cookie(id + '-' + encodeURIComponent(elm.id)) == 'true') {//изменения тут!!!
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
      $.cookie(id + '-' + encodeURIComponent(elm), 'true'); //изменения тут!!!
    }
    else {
      $.cookie(id + '-' + encodeURIComponent(elm), 'false'); //изменения тут!!!
    }
    filterChangeFlag();
    page = 0;
  });
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

function jsTree_onCheck(e) {
  filterChangeFlag();
  var thisTree = $($($(e.target).parents(".jstree-wrapper")[0]).find(".tree-dd")[0]);
  setTimeout(function () {
    getTreeChecked($("#filter_cities-tree"), id + "-cities-msg");
    getTreeChecked($("#filter_resources-tree"), id + "-resources-msg");
    thisTree.change();
    isChangedTree = true;
  }, 200);
}

function reloadContent(md5notclear) {

  $(".progress").fadeIn();
  $("#progressbar").progressbar("option", "value", 50);

  var dstart = formatDate($.cookie(id + "-fromDate-theme"));
  var dend = formatDate($.cookie(id + "-toDate-theme"));

  var params = getThemeParams();
  params.start = dstart;
  params.end = dend;
  params.md5 = '';

  $.postJSON(ajaxURL_Order, params, function (data) {

    showThemeData(data);

    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(1000);
  });

}

function getTreeChecked(tree, cookieName) {
  var groups = [];
  var checked = [];
  var cookieNameGroup = cookieName + '-group';

  $(".jstree-undetermined .jstree-checked.jstree-leaf > a", tree).each(function (i, e) {
    checked.push($(e).text().substr(2));
  });
  $(".jstree-no-icons > .jstree-checked > a", tree).each(function (i, e) {
    groups.push($(e).text().substr(2));
  });

  $.cookie(cookieName, checked.join(","));
  $.cookie(cookieNameGroup, groups.join(","));
  var return_val = {
    items: checked.join(","),
    groups: groups.join(",")
  };
  return return_val;
}

function getThemeParams() { // используется при экспорте и загрузке контента
  if ($.cookie(id + "-md5") == null) {
    $.cookie(id + "-md5", "");
    md5 = '';
  }
  var params = {
    order_id: id,
    page: $.cookie(id + "-page-msg") | 0,
    stime: new Date(parseInt($.cookie(id + "-fromDate-theme"), 10)).format("dd.mm.yyyy"),
    etime: new Date(parseInt($.cookie(id + "-toDate-theme"), 10)).format("dd.mm.yyyy"),
    sort: $.cookie(id + "-sort-msg"),
    positive: $.cookie(id + "-positive-msg"),
    negative: $.cookie(id + "-negative-msg"),
    neutral: $.cookie(id + "-neutral-msg"),
    post_type: $.cookie(id + "-show-msg"),
    md5: $.cookie(id + "-md5"),
    perpage: $.cookie(id + "-perpage-msg")
  };
  var list, tmp, filter_type, group_list, tree_val;

  // Добавляем фильтры бокового меню (mmItems)
  $.each(mmItems, function (i, filter) {
    //alert(filter.api+' - '+id+filter.id+' - '+$.cookie(id+filter.id));
    params[filter.api] = $.cookie(id + filter.id);
  });

  // Добавляем фильтры по лидерам и словам (mmItems)

  $.each($(".fcheck"), function (i, elm) {
    if ($.cookie(id + '-' + encodeURIComponent(elm.id)) == 'true') { //измения тут
      params[elm.id] = 'true';
    }
  });

  // Добавляем фильтр по городам
  list = $.cookie(id + "-cities-msg");
  if (list == undefined || list == null || list == "") {
    tree_val = getTreeChecked($("#filter_cities-tree"), id + "-cities-msg");
    params['location'] = tree_val['items'];
  }
  else {
    params['location'] = list;
  }
  group_list = $.cookie(id + "-cities-msg-group");
  if (group_list == undefined || group_list == null || group_list == "") {
    tree_val = getTreeChecked($("#filter_cities-tree"), id + "-cities-msg");
    params['cou'] = tree_val['groups'];
  }
  else {
    params['cou'] = group_list;
  }

  filter_type = $.cookie(id + "-filter_cities-type");
  params['locations'] = 'selected';
  if (filter_type != undefined && filter_type != null && filter_type != "") {
    params['locations'] = filter_type;
  }
  //:~

  // Добавляем фильтр по ресурсам
  list = $.cookie(id + "-resources-msg");
  if (list == undefined || list == null || list == "") {
    tree_val = getTreeChecked($("#filter_resources-tree"), id + "-resources-msg");
    params['res'] = tree_val['items'];
  }
  else {
    params['res'] = list;
  }
  group_list = $.cookie(id + "-resources-msg-group");
  if (group_list == undefined || group_list == null || group_list == "") {
    tree_val = getTreeChecked($("#filter_resources-tree"), id + "-resources-msg");
    params['shres'] = tree_val['groups'];
  }
  else {
    params['shres'] = group_list;
  }

  filter_type = $.cookie(id + "-filter_resources-type");
  params['hosts'] = 'selected';
  if (filter_type != undefined && filter_type != null && filter_type != "") {
    params['hosts'] = filter_type;
  }

  if(location.host=="wobotanalytics.cloudapp.net" || location.host=="beta.wobot.ru" || location.host=="11.11.11.11"){
    $("#tagcloud").show();
    if(location.host=="wobotanalytics.cloudapp.net"){
      $("#tagcloud a").attr("href", "http://"+location.host+"/production/lem/lem.html?order_id="+id+"&ostart="+($.cookie(id + "-fromDate-theme")/1000)+"&oend="+($.cookie(id + "-toDate-theme")/1000));
    } else {
      $("#tagcloud a").attr("href", "http://"+location.host+"/lem/lem.html?order_id="+id+"&ostart="+($.cookie(id + "-fromDate-theme")/1000)+"&oend="+($.cookie(id + "-toDate-theme")/1000));
    }
  }
  
  return params;
}

function showThemeData(data) {
  urlCom = inernalURL_messages + id;
  theme = data;
  var post_count = theme.posts != null ? theme.posts : 0;

  if (post_count > 0 || parseInt(post_count) > 0) {
    $("#previewref a").attr("href", inernalURL_messages + id).unbind('click');
  }
  else {
    $("#previewref a").attr("href", '').unbind('click').click(function () {
      return false;
    });
  }

  $("#tdd-filter-show").remove();
  $('#filter-show option[selected="selected"]').removeAttr('selected');

  var toCheck = $('#filter-show option[value="' + $.cookie(id + "-show-msg") + '"]');
  if (toCheck.length) {
    $('#filter-show option[value="' + $.cookie(id + "-show-msg") + '"]').attr('selected', 'selected');
  }
  else {
    $($('#filter-show option')[0]).attr('selected', 'selected');
  }

  createDropDown("filter-show", 112);
  $("#tdd-filter-show").unbind("change").change(function (e) {
    filterChangeFlag();
    $.cookie(id + "-show-msg", $(this).attr("value"));
  });


  graphtype = data.graphtype;
  if ($('#tarif-limit').is('div') && $.cookie("tariff_posts") != null) {
    var tariff_posts = $.cookie("tariff_posts");
    $('#tarif-limit').text(post_count + '/' + tariff_posts / 1000 + 'k');
    if (( tariff_posts - post_count ) <= 500) {
      $('#tarif-limit').css({color: '#de4343'});
    }
    else if (parseInt(post_count) != post_count && (tariff_posts / 1000 - parseInt(post_count)) <= 1) {
      $('#tarif-limit').css({color: '#de4343'});
    }
    $('#tarif-limit').tipTip({content: 'По вашей теме доступно ' + post_count + ' ' + declOfNum(post_count, ['последнее сообщение', 'последних сообщения', 'последних сообщений']) + ' из ' + tariff_posts / 1000 + 'k доступных по лимиту.<br/> Вся статистика по теме будет только по доступным сообщениям!'});
  }

  $("#dyn").html('' + data.order_name + '<div class="hide-long-text-list"></div>');

  $("#progressbar").progressbar("option", "value", 60);
  // Панель с миниграффиками

  var value;
  // Постов
  $("#posts").text(post_count);
  $("#posts").parent().find(" .sub img").attr("src", themapage_Templates.posts.replace("%order_id%", id));
  value = parseInt(data.posts_dyn, 10);
  $("#posts").parent().find(".sub-txt p")
    .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
    .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
  visibleItem("#posts", false);

  // Уникальных
  $("#uniq").text(data.uniq);
  $("#uniq").parent().find(" .sub img").attr("src", themapage_Templates.uniq.replace("%order_id%", id));
  value = parseInt(data.uniq_dyn, 10);
  $("#uniq").parent().find(".sub-txt p")
    .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
    .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
  visibleItem("#uniq", false);

  // Ресурсы
  $("#src").text(data.src);
  $("#src").parent().find(" .sub img").attr("src", themapage_Templates.src.replace("%order_id%", id));
  value = parseInt(data.src_dyn, 10);
  $("#src").parent().find(".sub-txt p")
    .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
    .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
  visibleItem("#src", false);

  // Аудитория
  $("#value").text(data.value);
  $("#value").parent().find(" .sub img").attr("src", themapage_Templates.aud.replace("%order_id%", id));
  value = parseInt(data.value_dyn, 10);
  $("#value").parent().find(".sub-txt p")
    .addClass((value >= 0) ? "dyn_plus" : ((value < 0) ? "dyn_minus" : ""))
    .text((value > 0) ? "+" + value : ((value < 0) ? value : ""));
  visibleItem("#value", false);

  // Вовлеченность
  visibleItem("#engage", false);

  $("#L4 .item").each(function (index, element) {
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

  $("#resources-diagramm").html("");
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

  $("#promoters-diagramm").html("");
  var promotions = [];
  var promotions_count = 0;
  $(data.promotions).each(function (index, element) {
    if (promotions.length <= 5)
      promotions[promotions.length] = [element.nick, element.count];
    else {
      promotions[5][0] = "Другие";
      promotions[5][1] += element.count;
    }
    promotions_count += element.count;
  });

  $("#value-diagramm").html("");
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

  $("#engage-diagramm").html("");
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

  $("#cities-diagramm").html("");
  $("#speakers-diagramm").html("");
  $("#tags-diagramm").html("");
  sources_tpl = '<table><col width="126px"/><col width="36px"/><col width="26px"/>\
          <tr><th></th><th class="txt-algn-right"><img src="img/pie.png" class="pieTip"></th><th class="txt-algn-right"><img src="img/paper.png" class="paperTip"></th></tr>';

  src2draw = (($(sources).size() < 5) ? $(sources).size() : 5);
  for (index = 0; index < src2draw; index++) {
    sources_tpl += '<tr class="row-' + index + '">' +
      '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
  }
  if ($(sources).size() > 5) {
    sources_tpl += '<tr class="row-5">' +
      '<td><a href="#" class="a-dotted">Другие</a></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
  }
  sources_tpl += '</table>';
  $("#resources-table").html(sources_tpl);

  $(sources).each(function (index, element) {
    sources[index][2] = Math.round(sources[index][1] / sources_count * 100);
    if (index < 5) {
      $($("#resources-table .row-" + index + " td")[0]).html('<a href="messages_list.html#' + id + '"  onclick="clearFilters();$.cookie(\'' + id + '-resources-msg\',\'' + sources[index][0] + '\'); window.location.href = \'messages_list.html#' + id + '\'; return false;">' + sources[index][0] + '</a>');
    }
    else $("a", $($("#resources-table .row-" + index + " td")[0])).unbind().click(function (e) {
      sources = sortSourse(sources);
      showPopup("resources-popup", "Ресурсы", "Ресурс", "Постов", theme.sources, sources);
      return false;
    });
    $($("#resources-table .row-" + index + " td")[1]).text(sources[index][2] + "%").addClass('txt-algn-right');
    $($("#resources-table .row-" + index + " td")[2]).html("" + sources[index][1] + "").addClass('txt-algn-right');
  });

  if (data.cash_update.slice(0, 16) == '01.01.1970 03:00') {
    $(".renewed span").html("не доступно ");
  }
  else {
    var cache_date = new Date(parseInt(data.cash_update + '000'));
    var cache_day = cache_date.getDate();
    cache_day = cache_day >= 10 ? cache_day : '0' + cache_day;
    var cache_month = cache_date.getMonth() + 1;
    cache_month = cache_month >= 10 ? cache_month : '0' + cache_month;
    $(".renewed span").html(cache_day + '.' + cache_month + '.' + cache_date.getFullYear() + ' ' + cache_date.getHours() + ':' + cache_date.getMinutes());
  }

  //data.engage
  if (data.engage == '0') $("#engage-block").css("cursor", "default").fadeTo(0, 0.5);
  else {
    $("#engage-block").css("cursor", "pointer").fadeTo(0, 1);
    $("#engage-block").unbind().click(function (e) {
      $(eng_mdin).each(function (index, value) {
        if (value[0] == "\"Мне нравится\" Вконтакте") eng_mdin[index][0] = "vk.com";
        if (value[0] == "Лайки Facebook") eng_mdin[index][0] = "facebook.com";
        if (value[0] == "Ретвиты Twitter") eng_mdin[index][0] = "twitter.com";
        if (value[0] == "Комментарии Livejournal") eng_mdin[index][0] = "livejournal.com";
      });
      showPopup("engage-popup", "Вовлеченность", "Ресурс", "Значение", theme.eng_mdin, eng_mdin);
      return false;
    });
  }

  //data.index
  if (data.nsi == '0') {
    $("#index-block").css("cursor", "default").fadeTo(0, 0.5);
  }
  else {
    var dstart = formatDate($.cookie(id + "-fromDate-theme"));
    var dend = formatDate($.cookie(id + "-toDate-theme"));

    $("#index-block").css("cursor", "pointer").fadeTo(0, 1);
    $("#index-block").unbind().click(function (e) {
      if (wnsi == null) {
        var params = getThemeParams();
        params.start = dstart;
        params.end = dend;
        params.md5 = '';
        $("#progressbar").progressbar("option", "value", 80);
        $(".progress").fadeIn();
        $.postJSON(ajaxURL_GetWnsi, params, function (response) {
          wnsi = response.wnsi;
          var index_data = [
            {name: 'nsi', count: data.nsi},
            {name: 'wnsi', count: response.wnsi}
          ];
          $("#progressbar").progressbar("option", "value", 100);
          $(".progress").fadeOut();
          showPopup("index-popup", "Индексы", "Индекс", "Значение", index_data, 'index');
        });
      }
      else {
        var index_data = [
          {name: 'nsi', count: data.nsi},
          {name: 'wnsi', count: wnsi}
        ];
        showPopup("index-popup", "Индексы", "Индекс", "Значение", index_data, 'index');
      }
      return false;
    });
  }

  $(".quot").click(function (e) {
    e.stopPropagation();
  });

  if (data.src == '0') $("#src-block").css("cursor", "default").fadeTo(0, 0.5);
  else {
    $("#src-block").css("cursor", "pointer").fadeTo(0, 1);
    $("#src-block").unbind().click(function (e) {
      showPopup("resources-popup", "Ресурсы", "Ресурс", "Постов", theme.sources, sources);
      return false;
    });
  }

  if (data.value == '0') $("#value-block").css("cursor", "default").fadeTo(0, 0.5);
  else {
    $("#value-block").css("cursor", "pointer").fadeTo(0, 1);
    $("#value-block").unbind().click(function (e) {
      showPopup("value-popup", "Охват", "Ресурс", "Охват", theme.value_mdin, value_mdin);
      return false;
    });
  }

  if (data.uniq == '0')
    $("#uniq-block").css("cursor", "default").fadeTo(0, 0.5);
  else {
    $("#uniq-block").css("cursor", "pointer").fadeTo(0, 1);
    $("#uniq-block").unbind().click(function (e) {
      showPopup("promoters-popup", "Лидеры мнений", "Ник", "Охват", theme.promotions, promotions);
      return false;
    });
  }

  if (data.posts == '0' || data.posts == null)
    $("#post-block").css("cursor", "default").fadeTo(0, 0.5).unbind();
  else {
    $("#post-block").css("cursor", "pointer").fadeTo(0, 1);
    $("#post-block").unbind().click(function (e) {
      document.location.href = inernalURL_messages + id;
      return false;
    });
  }

  $("#progressbar").progressbar("option", "value", 70);
  var points = [];
  var cities = [];
  var cities_count = 0;
  $(data.city).each(function (index, element) {
    if (cities.length <= 5)
      cities[cities.length] = [element.name, element.count];
    else {
      cities[5][0] = "Другие";
      cities[5][1] += element.count;
    }
    cities_count += element.count;
  });

  $("#cities td").text('');

  $(cities).each(function (index, element) {
    cities[index][2] = Math.round(cities[index][1] / cities_count * 100);
    if (index < 5) {
      $($("#cities-table .row-" + index + " td")[0]).html('<a href="messages_list.html#' + id + '"  onclick="clearFilters();$.cookie(\'' + id + '-cities-msg\',\'' + cities[index][0] + '\'); window.location.href = \'messages_list.html#' + id + '\'; return false;">' + cities[index][0] + '</a>');
    }
    $($("#cities-table .row-" + index + " td")[1]).text(cities[index][2] + "%");
    $($("#cities-table .row-" + index + " td")[2]).html("<b>" + cities[index][1] + "</b>");
  });

  if ($(cities).length > 5) {
    $($("#cities-table .row-" + 5 + " td")[0]).html('<a href="#">Другие</a>').unbind().click(function (e) {
      showPopup("cities-popup", "Города", "Город", "Постов", theme.city, 'map');
      return false;
    });
  }

  var width = 176;
  var height = 97;

  function v2d_length(v) {
    return Math.sqrt((v.x * v.x) + (v.y * v.y));
  }

  function v2d_1d_devide(v, d) {
    return {x: v.x / d, y: v.y / d};
  }

  function v2d_normalize(v) {
    return v2d_1d_devide(v, v2d_length(v));
  }

  function v2d_1d_multiplex(v, d) {
    return { x: v.x * d, y: v.y * d};
  }

  function gradTorad(grad) {
    return grad * (Math.PI / 180.0);
  }

  function v2d_rotate(v, grad) {
    return {x: v.x * Math.cos(gradTorad(grad)) - v.y * Math.sin(gradTorad(grad)),
      y: v.x * Math.sin(gradTorad(grad)) + v.y * Math.cos(gradTorad(grad))};
  }

  function v2d_add(v1, v2) {
    return {x: v1.x + v2.x, y: v1.y + v2.y};
  }

  function normalizeMouseEvent(e, container) {
    var ePos,
      chartPosLeft,
      chartPosTop,
      chartX,
      chartY;

    // common IE normalizing
    e = e || win.event;
    if (!e.target) {
      e.target = e.srcElement;
    }

    // jQuery only copies over some properties. IE needs e.x and iOS needs touches.
    if (e.originalEvent) {
      e = e.originalEvent;
    }

    if (e.event) {
      e = e.event;
    }

    // iOS
    ePos = e.touches ? e.touches.item(0) : e;

    chartPosition = $(container).offset();
    chartPosLeft = chartPosition.left;
    chartPosTop = chartPosition.top;

    var isIE = /msie/i.test(navigator.userAgent) && !win.opera;
    if (isIE) { // IE including IE9 that has pageX but in a different meaning
      chartX = e.x;
      chartY = e.y;
    } else {
      chartX = ePos.pageX - chartPosLeft;
      chartY = ePos.pageY - chartPosTop;
    }
    return {x: Math.round(chartX), y: Math.round(chartY)};
  }

  var cities = data.city;
  var colors = ["#7f7bdb", "#c0ac97", "#b0c78f", "#c09797", "#97c0c0", "#86c4d1"];
  if (cities[0] != undefined && cities[0] != null && cities[0] != "") //Список городов пуст
  {
    var min = cities[0].count, max = cities[0].count;
    $.each(cities, function (i, e) {
      if (e.count < min) min = e.count;
      if (e.count > max) max = e.count;
    });
    var size;
    for (var i = 0; i < 6; i++) {
      if (cities[i] == undefined) break;
      size = (cities[i].count - min) / (max - min) * 7 + 1;
      if (min == max) {
        size = 8;
      }
    }
  }

  $(data.speakers).each(function (index, element) {
    if (index < 7) {
      $($("#speakers .row-" + index + " td")[0]).text(element.nick);
      $($("#speakers .row-" + index + " td")[1]).text(element.count);
    }
  });

  $("#speakers .seemore a").unbind().click(function (e) {
    showPopup("speakers-popup", "Спикеры", "Ник", "Постов", theme.promotions, promotions);
    return false;
  });

  $("#promouters td").text('');
  $("#promouters .seemore a").text('');
  $(data.promotions).each(function (index, element) {
    if (index < 6) {
      $($("#promouters .row-" + index + " td")[0]).html('<a href="messages_list.html#' + id + '"  onclick="clearFilters();$.cookie(\'' + id + '-selpromo\',\'' + element.id + '\'); window.location.href = \'messages_list.html#' + id + '\'; return false;">' + element.nick + '</a>');
      $($("#promouters .row-" + index + " td")[1]).text(element.count);
      $($("#promouters .row-" + index + " td")[2]).text(element.count_posts);
    }
    else {
      $("#promouters .seemore a").text('Другие').unbind().click(function (e) {
        promotions = sortSourse(promotions);
        showPopup("promoters-popup", "Лидеры мнений", "Ник", "Охват", theme.promotions, promotions);
        return false;
      });
    }
  });

  $("#engage-block").tipTip({content: 'Вовлеченность – оценка участия аудитории в обсуждении темы и ее распространении.<br/> <a href="http://www.wobot.ru/faq#1_13" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
  $("#index-block").tipTip({content: 'Индексы - эмоциональность обсуждения темы в числовом выражении.<br/> <a href="http://www.wobot.ru/faq#1_13" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
  $("#value-block").tipTip({content: 'Потенциальный охват аудитории.<br/><a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
  $("#src-block").tipTip({content: 'Количество ресурсов, на которых были найдены упоминания.'});
  $("#resources .h").tipTip({content: 'Распределение упоминаний по площадкам', defaultPosition: "top"}).css("cursor", "help");
  $("#cities .h").tipTip({content: 'Города – распределение упоминаний по городам, указанным в анкетах пользователей.<br/> <a href="http://www.wobot.ru/faq#1_34" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition: "top"}).css("cursor", "help");
  $("#promouters .h").tipTip({content: 'Лидеры мнений – пользователи с наибольшим числом аудитории и наиболее часто упоминающие тему мониторинга.<br/> <a href="http://www.wobot.ru/faq#1_21">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition: "top"}).css("cursor", "help");
  $("#words .h").tipTip({content: 'Список слов – наиболее часто встречающие слова в одном упоминании по теме мониторинга. <br/> <a href="http://www.wobot.ru/faq#1_32">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition: "top"}).css("cursor", "help");
  $("#uniq-block").tipTip({content: 'Количество уникальных авторов'});
  $("#post-block").tipTip({content: 'Показать упоминания'});
  $("#referenceButton").tipTip({content: 'Посмотреть все упоминания по вашей теме'});
  //$(".paperTip").tipTip({content: 'Посмотреть все упоминания по вашей теме'});
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

  var minWordCount = 1000000000;
  var maxWordCount = 0;
  var words = "";

  var wordss = [];
  var wordss_count = 0;
  $(data.words).each(function (index, element) {
    if (wordss.length <= 5)
      wordss[wordss.length] = [element.word, element.count];
    else {
      wordss[5][0] = "Другие";
      wordss[5][1] += element.count;
    }
    wordss_count += element.count;
    maxWordCount = (element.count > maxWordCount) ? element.count : maxWordCount;
    minWordCount = (element.count < minWordCount) ? element.count : minWordCount;
  });

  for (i = 0; i < 5; i++) {
    $($("#words-table .row-" + i + " td")[0]).html('');
    $($("#words-table .row-" + i + " td")[1]).html('');
  }
  $("a", $($("#words-table .row-5 td")[0])).text('').unbind();
  $($("#words-table .row-5 td")[1]).html('');

  $(wordss).each(function (index, element) {
    if (index < 5) {
      $($("#words-table .row-" + index + " td")[0]).html('<a href="#" onclick="clearFilters();$.cookie(\'' + id + '-word_' + encodeURIComponent(element[0]) + '\',\'' + true + '\'); window.location.href = \'messages_list.html#' + id + '\';  return false;">  ' + wordss[index][0] + "</a>");
    }
    else {
      $("a", $($("#words-table .row-" + index + " td")[0])).text(wordss[index][0]).unbind().click(function (e) {
        wordss = sortSourse(wordss);
        showPopup("tags-popup", "Список слов", "Тег", "Вес", theme.words, wordss);
        return false;
      });
    }
    $($("#words-table .row-" + index + " td")[1]).text(wordss[index][1]).addClass('txt-algn-right');
  });

  $("#progressbar").progressbar("option", "value", 80);

  lines_date = [];
  $.each(data.graph, function (index, element) {
    shift = parseInt(index, 10) * 1000;
    return false;
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
  i = 1;
  $.each(data.graph, function (index, element) {
    lines_date.push([i, element]);
    i++;
  });

  $('#large').empty();
  var $container = $('#lines-diagramm');

  $('<div id="detail-container">').appendTo($container);
  $('<div id="graph_data_loader">').appendTo($container);
  $('<div id="graph_data_error">').appendTo($container);
  $('#graph_data_error').html('Нет данных для построения графика');

  defaultDiagramData = lines_date;
  graph_data = {};
  graph_data['post_id_time'] = lines_date;
  buildCustomDiagram();

  $("tspan").each(function (index, element) {
    if ($(element).text() == "Highcharts.com")$(element).text("");
  });
}

function resizeWindowHeight() {
  if ($('#mm-items').innerHeight() > 690) {
    $('#MM').css({height: $('#mm-items').innerHeight() + 'px'});
    $('#container').css({height: $('#mm-items').innerHeight() + 250 + 'px'});
    $('#body').css({height: $('#mm-items').innerHeight() + 110 + 'px', minHeight: '0'});
  }
  else {
    $('#MM').css({height: '690px'});
    $('#container').css({height: 'auto'});
    $('#body').css({height: '100%', minHeight: '100%'});
  }
}

function clearFilter() {
  $.cookie(id + "-md5", null);
  $.cookie(id + "-page-msg", null);
  $.cookie(id + "-aditional-words", null);
  $.cookie(id + "-fromDate-theme", null);
  $.cookie(id + "-toDate-theme", null);
  $.cookie(id + "-sort-msg", null);
  $.cookie(id + "-positive-msg", null);
  $.cookie(id + "-negative-msg", null);
  $.cookie(id + "-neutral-msg", null);
  $.cookie(id + "-show-msg", null);
  $.cookie(id + "-cities-msg", null);
  $.cookie(id + "-cities-msg-group", null);
  $.cookie(id + "-filter_cities-type", null);
  $.cookie(id + "-resources-msg", null);
  $.cookie(id + "-resources-msg-group", null);
  $.cookie(id + "-filter_resources-type", null);
  $.cookie(id + "-filter_resources-type", null);
  $.each(mmItems, function (i, filter) {
    $.cookie(id + filter.id, null);
  });
  $.each($(".fcheck"), function (i, elm) {
    $.cookie(id + '-' + encodeURIComponent(elm.id), null);
  });
  window.location.reload();
}