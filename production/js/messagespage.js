var wasChanged = false;
var id; // идентификатор отчета
var minDate, maxDate; //абсолютные временные границы отчета
var readyVector = new Object();
var page = 0;
var toggle;
var toggle_export;
var order_tags, tags = new Object();
var start_interval = false;
var user_id;
var fltr;
var tp1, tpgP, tpOD, tpFD, tpCD, tp10;
var filtersReady = 0;
var filterChanged = false;
var post_count = 0;
var page_post_count = 0;
var post_count_has_tag = 0;
var post_count_no_tag = 0;
var group_action_ids = [];
var group_action_except_ids = [];
var selected_count = 0;
var has_dup = 0;
var tags_per_block = 25;
var user_reaction = 0;


function t() {
  var time = new Date();
  return time.getTime();
}

function hideFilters() {
  $(document).click();
  $('html').click();
  $("#filter_cities-tree").hide();
  $("#filter_resources-tree").hide();
}

function setAdvSettings(name, value) {

  $.postJSON(ajaxURL_setAdvSettings, {name: name, value: value}, function (data) {
    if (name == 'perpage') {
      page_post_count = value;
    }
    page = 0;
    reloadContent();
  });
}

function getPages() {
  tp1 = t();
  $.postJSON(ajaxURL_GetSettings, {}, function (data) {
    $.cookie(id + "-perpage-msg", data.perpage);
    if(data.tcs_sync != null){
      $.cookie(id + "-sync", data.tcs_sync); //sync with CRM
    }
    page_post_count = data.perpage;
    user_reaction = data.user_reaction;
    if( $.cookie('user_id') == 4200 )
    {
      user_reaction = data.user_reaction;
    }
    var toCheck = $('#perpage option[value="' + $.cookie(id + "-perpage-msg") + '"]');

    if (toCheck.length) $('#perpage option[value="' + $.cookie(id + "-perpage-msg") + '"]').attr('selected', 'selected');
    else $($('#perpage option')[0]).attr('selected', 'selected');
    createDropDown("perpage", 35);
    $("#tdd-perpage").unbind("change").change(function (e) {

      $.cookie(id + "-perpage-msg", $(this).attr("value"));
      $.cookie(id + "-page-msg", 0);

      setAdvSettings("perpage", $(this).attr("value"));
    });
  });
  tpgP = t();
}

function updateTips(t, tip) {
  $(tip).text(t);
}

/*
 Открывает модельное окно попапа
 */
function loadmodal(href, width, height, type) {
  if (!width) width = 604;
  if (!height) height = 400;
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

/*
 Сливаем два объекта в один
 */
function mergeOptions(obj1, obj2) {
  var obj3 = {};
  for (var attrname in obj1) {
    obj3[attrname] = obj1[attrname];
  }
  for (var attrname in obj2) {
    obj3[attrname] = obj2[attrname];
  }
  return obj3;
}

/*
 Строим JSON объект по данным для jsTree
 */
function buildTreeCR(node, cookie) { /*jstree-checked*/
  var result = [];

  if (node !== null) {
    $.each(node, function (index, element) {
      if (typeof element !== "object") {
        result.push({"data": index /*{ "title": index }*/});

      } else {
        result.push({
          "data": /*{ "title": index }*/ index,
          "children": buildTreeCR(element, cookie)
        });
      }
    });
  }
  return result;
}

/*
 Создание Cookie строки по отмеченным элементам в jsTree
 и возвращает строку запроса.
 */
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
  var return_val = {
    items: checked.join(","),
    groups: groups.join(",")
  };
  return return_val;
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


  $("#date #datepicker").val( dateToWords(today.format("dd.mm.yyyy"), true) + " - " + dateToWords(today.format("dd.mm.yyyy"), true));

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  reloadContent();
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

  $("#date #datepicker").val( dateToWords(a.format("dd.mm.yyyy"), true) + " - " + dateToWords(a.format("dd.mm.yyyy"), true));

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  reloadContent();
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


  $("#date #datepicker").val( dateToWords(a.format("dd.mm.yyyy"), true) + " - " + dateToWords(b.format("dd.mm.yyyy"), true));

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());

  reloadContent();
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

  $("#date #datepicker").val( dateToWords(a.format("dd.mm.yyyy"), true) + " - " + dateToWords(b.format("dd.mm.yyyy"), true));

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  reloadContent();
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

  $("#date #datepicker").val( dateToWords(a.format("dd.mm.yyyy"), true) + " - " + dateToWords(b.format("dd.mm.yyyy"), true));

  $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
  $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
  reloadContent();
}

/*
 Задает правила для нормализации временных границ
 Входные параметры:
 fromBoundary,toBoundary: абсолтные граници временного интервала
 fromAct, toAct: выбранные граници интервала нуждающиеся в нормализации
 Выходные параметры:
 0: Значение для инициализации datepicker-а слева
 1: Значение для правой граници
 2: Значение для левой граници
 */
function getDateBoundaries(fromBoundary, toBoundary, fromAct, toAct) {
  var a1 = new Date(fromAct.getTime()) , a2 = new Date(fromAct.getTime());
  return [
    new Date(Math.min(new Date(Math.max(a1.add(-1).months().getTime(), fromBoundary.getTime())).getTime(), fromBoundary.getTime()))
    ,
    new Date(Math.min(toAct.getTime(), toBoundary.getTime())),
    new Date(Math.max(a2.getTime(), fromBoundary.getTime()))
  ];
}

/* Обработчик для компонентов jsTree (ресурсы и города) */
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

/* Обработчик для компонентов jsTree (ресурсы и города) */
var isChangedTree = false;
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

/*
 Загрузка данных по фильтрам (города и ресурсы)
 API метод: filters
 Входные параметры API: order_id = id(global)

 Запускается не более 1 раза
 */
function loadFiltersData(md5) {
  var start = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10)).format("dd.mm.yyyy");
  var end = new Date(parseInt($.cookie(id + "-toDate-theme"), 10)).format("dd.mm.yyyy");

  readyVector['loadFiltersData'] = false;
  $.postJSON(ajaxURL_Filters, {order_id: id, start: start, end: end}, function (responce) {

    has_dup = responce.dup;
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
        filtersReady++;
        loadCommentsData(md5);
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
        filtersReady++;
        loadCommentsData(md5);
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
    initSort();
    initGroupTags();

    readyVector['loadFiltersData'] = true;
    $("#progressbar").progressbar("option", "value", 50);
    tpFD = t();
  });
}

/*
 Отправляем запрос на смену тональности
 API метод: nastr
 Входные параметры API:
 1. order_id = id(global) идентификатор отчета
 2. id      = msgID      идентификатор поста
 3. value   = tone       тональность (-1, 0 ,1)
 */
function changeTone(msgID, tone) {

  var message = $('.message[pk="' + msgID + '"]');
  $(".order_tone-changer", message).hide();
  $(".order_tone", message).hide();
  $(".tonloader", message).show();
  $(".order_no", message).show();
  window.setTimeout(function () {
    $.postJSON(ajaxURL_ToneChange, {order_id: id, id: msgID, value: tone}, function (data) {

      $(".tonloader", message).hide();
      //$(".order_tone-changer", message).hide();
      //$(".order_no", message).show();
      $(".order_tone", message)
        .show()
        .removeClass('cb1').removeClass('cb2').removeClass('cb3')
        .addClass('cb' + (-tone + 2) // Смещаем диапазон и переварачиваем  -1 ... 1 => 3 ... 1
        );

      $(".order_tone-changer .cb", message).removeClass("selected");
      if (tone == 1) $($(".order_tone-changer .positive", message).parent()).addClass("selected");
      else if (tone == 0) $($(".order_tone-changer .neutral", message).parent()).addClass("selected");
      else if (tone == -1) $($(".order_tone-changer .negative", message).parent()).addClass("selected");
    });
    //set timeout end
  }, 200);
  setNastrForDups(msgID, tone);
}

/*
 Загрузка данных по отчету
 API метод: order
 Входные параметры API: order_id = id(global)

 Запускается не более 1 раза
 */
function loadOrderData() {
  readyVector['loadOrderData'] = false;
  $.postJSON(ajaxURL_Order, {order_id: id}, function (responce) {
    var theme = responce;
    minDate = Date.parseExact(responce.start, "d.M.yyyy");
    maxDate = Date.parseExact(responce.end, "d.M.yyyy");

    var boundaries = getDateBoundaries(minDate, maxDate, Date.today(), Date.today());
    var fromDate, toDate;

    if ($.cookie(id + "-fromDate-theme") == null) {
      fromDate = boundaries[0];
    }
    else {
      fromDate = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
    }

    if ($.cookie(id + "-toDate-theme") == null) {
      toDate = boundaries[1];
    }
    else {
      toDate = new Date(parseInt($.cookie(id + "-toDate-theme"), 10));
    }

    if ($('#tarif-limit').is('div') && $.cookie("tariff_posts") != null) {
      var tariff_posts = $.cookie("tariff_posts");
      $('#tarif-limit').text(theme.posts + '/' + tariff_posts / 1000 + 'k');
      if (( tariff_posts - theme.posts ) <= 500) {
        $('#tarif-limit').css({color: '#de4343'});
      }
      else if( parseInt(theme.posts) != theme.posts && (tariff_posts / 1000 - parseInt(theme.posts)) <= 1 )
      {
        $('#tarif-limit').css({color: '#de4343'});
      }
      $('#tarif-limit').tipTip({content: 'По вашей теме доступно ' + theme.posts + ' ' + declOfNum(theme.posts, ['последнее сообщение', 'последних сообщения', 'последних сообщений']) + ' из ' + tariff_posts / 1000 + 'k доступных по лимиту.<br/> Вся статистика по теме будет только по доступным сообщениям!'});
    }

// Установка выбора новой даты для фильтра
    if (!$("#dp-begin").hasClass("hasDatepicker")) {
      $("#date #datepicker").val(
        dateToWords(fromDate.format("dd.mm.yyyy"), true) + " - " +
          dateToWords(toDate.format("dd.mm.yyyy"), true));


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
          filterChangeFlag();
        }
      }).datepicker("setDate", fromDate);

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
          filterChangeFlag();
        }
      }).datepicker("setDate", toDate);
    }
//конец новой
  
  //TCS sync
  if($.cookie(id + "-sync") == 1){
    $("#CRMsync-plug").hide();
    $("#CRMsync").show();
    $("#CRMsync").click(function(){
      syncDataWithCRM(id);
    });
  }

    $.cookie(id + "-fromDate-theme", fromDate.getTime());
    $.cookie(id + "-toDate-theme", toDate.getTime());

    $("#order_name").text(responce.order_name);

    readyVector['loadOrderData'] = true;
    $("#progressbar").progressbar("option", "value", 40);
    tpOD = t();
    $.postJSON(ajaxURL_getThemeSettings, {order_id: id}, function (responce) {
      if (responce.remove_spam == 1) {
        $.cookie(id + "-show-msg", 'nospam');
      }
      loadFiltersData();
    });
  });
}

function setTag(mid, tid, value) {
  //пусть сначала галочка поставится а потом все остальное
  var tag = $('.message[pk="' + mid + '"] .tag[pk="' + tid + '"]');
  if (value == "0") {
    $(".e1 img", tag).attr("src", "img/cb_ck.png");
  }
  else {
    $(".e1 img", tag).attr("src", "img/cb.png");
  }
  tag.attr('ins', (value == "1") ? "0" : "1");

  $.postJSON(ajaxURL_SetTag, {order_id: id, tag_id: tid, id: mid, mas_tags: tags[mid], mas_post_tags: order_tags, tag_value: (value == "1" ? "false" : "true") }, function (data) {
    if (data.status == "ok") {
      rebuildGroupSelectionForMessage(mid);
    } else {
      alert("Ошибка с присвоением тега.");
      /* FOO */
    }
  });
  setTagForDups(mid, tid, value);
}

function editTag(tid, tname) {
  $.postJSON(ajaxURL_EditTag, {order_id: id, tag_id: tid, tag_name: tname}, function (data) {
    if (data.status == 'fail') alert("Ошибка при изменении тега");
    else if (data.status == 'ok') {
      $('.tag[pk="' + tid + '"] .e2').text(tname);
      $('.tag[pk="' + tid + '"] .e2_2 input').val(tname);
      $("#mm-tags .flist .fitem input[id=tag_" + tid + "]").siblings("span").text("\xa0" + tname);
      // меняем тег в групповых
      $('#group-actions .group-choices.tags ul a[href="' + tid + '"]').html(tname);
    }
  });
}

function delTag(mid, tid) {
  if (confirm("Вы уверены, что хотите удалить тег?")) {

    var tag_container = $('.tag[pk="' + tid + '"]').parent();
    $('.tag[pk="' + tid + '"]').remove();
    if( $.trim(tag_container.html()).length == 0 && ( tag_container.next().is('.message-tag-container') || tag_container.prev().is('.message-tag-container') ) )
    {
      tag_container.parent().css({left: '640px', width: '200px'});
      tag_container.next().css({border: 'none'});
      tag_container.remove();
    }

    // Удаляем тег в груповых тегах
    tag_container = $('#group-actions .group-choices.tags ul a[href="' + tid + '"]').parent().parent();
    $('#group-actions .group-choices.tags ul a[href="' + tid + '"]').parent().remove();
    if( $.trim(tag_container.html()).length == 0 && ( tag_container.next().is('ul') || tag_container.prev().is('ul') ) )
    {
      tag_container.parent().css({width: '165px'});
      tag_container.remove();
    }

//    var order_tags2 = new Array();
//    $.each(order_tags, function (i, e) {
//      if (i != tid)
//      {
//        order_tags2[i] = e;
//      }
//    });

//    order_tags = order_tags2;
    delete order_tags[tid];
    $.postJSON(ajaxURL_DelTag, {order_id: id, tag_id: tid, user_id: user_id}, function (data) {
      if (data.status == "ok") {
        if ($.cookie(id + '-tag_' + tid)) {
          filterChangeFlag();
        }
        $.cookie(id + '-tag_' + tid, null);
        $("#mm-tags .flist .fitem input[id=tag_" + tid + "]").parent("p").remove();
      }
      else {
        alert('Ошибка при удалении тега.');
      }
    });
  }
}

function addTag(name, message_id) {
  $.postJSON(ajaxURL_AddTag, {order_id: id, name_tag: name, user_id: user_id}, function (data) {
    if (data.status == "ok") {
      if( Object.keys(order_tags).length == tags_per_block )
      {
        $('#group-actions .group-choices.tags ul').last().after('<ul></ul>');
        $('#group-actions .group-choices.tags .variants').css({width: '325px'});
      }
      $.each($('.message'), function (foo, message) {
        message = $(message);
        if( Object.keys(order_tags).length == tags_per_block )
        {
          $(".message-tag-container", message).after('<div class="message-tag-container" style="border-left: 1px solid #CECECE;"></div>');
          $(".add-tag", message).css({left: '540px', width: '400px'});
        }
        var tag = $(".add-tag .template", message).clone();
        tag.addClass('tag').removeClass("template").attr("pk", data.id).attr('ins', "0");
        $(".e1 img", tag).attr("src", "img/cb.png");
        $(".e1 a", tag).click(function (e) {
          var parent = $(this).parents('.tag'),
            message = $(this).parents('.message');
          setTag(message.attr('pk'), parent.attr('pk'), parent.attr('ins'));
          return false;
        });
        $(".e2", tag).text(name);
        $(".e2_2 input", tag).val(name);
        $(".e2", tag).click(function (e) {
          var alltags = $(this).closest(".add-tag");
          $(".e2_2", alltags).addClass("hidden");
          $(".e2", alltags).removeClass("hidden");
          $(this).closest(".line").children(".e2_2").removeClass("hidden").children("input").focus().select();
          $(this).addClass("hidden");
        });
        $("input", tag).focusout(function (e) {
          $(".e2_2", tag).addClass("hidden");
          $(".e2", tag).removeClass("hidden");
          $(".e2", tag).text($(".e2_2 input", tag).val());
        });
        $("input", tag).keypress(function (e) {
          if (e.which == 13) {
            $(".e2_2", tag).addClass("hidden");
            $(".e2", tag).removeClass("hidden");
            $(".e2", tag).text($(".e2_2 input", tag).val());
            editTag(tag.attr('pk'), $(".e2_2 input", tag).val());
            //TODO: add "change tag" function
          }
        });

        $(".e3 a", tag).click(function (e) {
          var parent = $(this).parents('.tag'),
            message = $(this).parents('.message');
          delTag(message.attr('pk'), parent.attr('pk'));
          return false;
        });
        tag.css("display", "block");
        $('.message-tag-container', message).last().append(tag);
        //чекаем тег в посте где его добавили
        if ($(message).attr("pk") == message_id) {
          setTag(message_id, data.id, 0);
          $("#mm-tags .flist").append("<p class=\"fitem\"><input type=\"checkbox\" class=\"inline fcheck\" id=\"tag_" + data.id + "\"><span>&nbsp;" + name + "</span></p>");
          if ($("#mm-tags").hasClass("mm-itm-closed")) {
            $("#mm-tags").removeClass("mm-itm-closed").addClass("mm-itm-open");
            $("#mm-tags .flist").show();
            $("#mm-tags .btns").show();
          }
          $("#mm-tags .flist input:last").click(function () {
            if ($(this).attr('checked')) {
              $.cookie(id + '-tag_' + data.id, 'true');
            }
            else {
              $.cookie(id + '-tag_' + data.id, 'false');
            }
            filterChangeFlag();
          });
        }

      });
      // добавляем новый тег в групповые действия
      $('#group-actions .group-choices.tags ul:first-child li').last().remove();
      $('#group-actions .group-choices.tags ul').last().append('<li><input type="checkbox" href="' + data.id + '"><a href="' + data.id + '">' + name + '</a></li>');
      $('#group-actions .group-choices.tags ul').eq(0).append('<li><input type="text" id="group-action-new-tag"><a href="javascript:void(0);" onclick="addGroupTag();" id="group-action-add-tag">+</a></li>');

      order_tags[data.id] = name;
    } else { /* FOO */
    }
  });
}

function FillExportList() {
  if ($('.export-list').is(":visible")) {
    $.postJSON(postURL_GetExports, {order_id: id}, function (data) {
      $('.export-list div table').html('<tr><td width="30" class="td_border_bottom">№</td><td width="100" class="td_border_bottom">Время экспорта</td><td width="200" class="td_border_bottom" align="center">Период исследования</td><td width="100" class="td_border_bottom">Прогресс</td></tr>');
      $.each(data, function (i, k) {
        $('.export-list div table').append('<tr><td>' + (i + 1) + '.</td><td>' + new Date(parseInt(k.export_time * 1000, 10)).format("dd.mm.yyyy HH:MM:ss") + '</td><td align="center">' + new Date(parseInt(k.start_time * 1000, 10)).format("dd.mm.yyyy") + ' - ' + new Date(parseInt(k.end_time * 1000, 10)).format("dd.mm.yyyy") + '</td><td>' + (k.progress !== undefined ? (k.progress == -1 ? 'ошибка' : k.progress + '%') : '<a href="' + k.dl_link + '"><b>Скачать</b><a>') + '</td></tr>');
      });
    });
  }
}

function GetExportList() {
  $('.export-list').toggle(400);
  $('.export-list_upper').toggle(400);
  FillExportList();
  if (start_interval == false) setInterval('FillExportList();', 5000);
}

function sleep(sleep_ms) {
  sleep_ms += new Date().getTime();
  while (new Date() < sleep_ms) {
  }
}

function LaunchExport() {
  var request = getCommentsParams();
  request.start = request.stime;
  request.end = request.etime;
  $('.elem7 > a').attr('onclick', '');
  $.postJSON(postURL_AddExport, request, function (data) {
    $('.elem7 > a').attr('onclick', 'LaunchExport(); return false;');
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

function arrowFix(arrow, id, num_posts, per_page, curr_page) {
  last_page = (num_posts - num_posts % per_page) / per_page;
  if (arrow == "first" && curr_page != 0) {
    page = 0;
    $.cookie(id + "-page-msg", "0");
    reloadContent(true);
    return false;
  }
  if (arrow == "last" && curr_page != last_page) {
    page = last_page;
    $.cookie(id + "-page-msg", parseInt(page));
    reloadContent(true);
    return false;
  }
  if (arrow == "next" && curr_page != last_page) {
    page = parseInt(page) + 1;
    $.cookie(id + "-page-msg", parseInt(page));
    reloadContent(true);
    return false;
  }
}
/*
 Загрузка постов
 API метод: comments
 Входные параметры API:
 used   1.  Order_id        – id запроса(142)
 2. Order_md5       – md5 запроса поискового(null если фильтры изменились или страница только загружена) Что нужно кодировать? Как кодировать? #deprecated
 used   3.  Page            – номер страницы(2)
 4. N_nick          – выбранный ник(если выбрали отсортировать по нику из главной страницы)
 5. N_nick_link     – ресурс выбранного автора(если выбрали отсортировать по нику из главной страницы)
 6. N_word          – выбранное слово(если выбрали отсортировать по слову из главной страницы)
 used   7.  Stime           – начало периода(12.02.2011)
 used   8.  Etime           – конец периода(27.02.2011)
 used   9.  Positive        – вывод позитивных упоминаний(true/false)
 used   10. Negative        – вывод отрицательных упоминаний(true/false)
 used   11. Neutral         – вывод нейтральных упоминаний(true/false)
 used   12. res_Название ресурса (список выбранных ресурсов)(res_twitter_com=true,res_livejournal_com=false…)
 used   13. loc_Название города  (Список выбранных городов)(res_Москва=true,res_Нижний_Новгород=false…)
 14.    Speakers        – режим отбора спикеров (all – все, selected – выбранные, except – все кроме выбранных)
 15.    Speak_Логин cпикера (Список выбранных спикеров)(speak_lalala=true,speak_leha=false)
 16.    Promotions      – режим выбора промоутеров (all – все, selected – выбранные, except – все кроме выбранных)
 17.    Prom_Логин_промоутера (Список выбранных промоутеров)(speak_lalala=true,speak_leha=false)
 18.    Prom_link_Логин_промоутера - Ресурсы выбранных промоутеров (speak_link_Ник_автора = livejournal.com)
 19.    Tags_Название тега (Список выбранных тегов)(tags_5=true,tags_7=false)
 20.    Words           – режим выбора слов (all – все, selected – выбранные, except – все кроме выбранных)
 21.    Word_Слово (Список выбранных слов)(word_привет=true)
 22.    Gender          – выбранный пол(all – все, m – мужчины, w - женщины)
 23.    Age_min         – минимальный выбранный возраст(null – если не выбрано)
 24.    Age_max         – максимальный выбранный возраст(null – если не выбрано)
 used   35. post_type       - (Все упоминания  | Избранные | Без спама | Только спам) = (all | fav | notspam | spam)
 */
function getCommentsParams() { // используется при экспорте и загрузке контента
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
  if (list == undefined || list == null || list == "")
  {
    tree_val = getTreeChecked($("#filter_cities-tree"), id + "-cities-msg");
    params['location'] = tree_val['items'];
  }
  else {
    params['location'] = list;
  }
  group_list = $.cookie(id + "-cities-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
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
  if (list == undefined || list == null || list == "")
  {
    tree_val = getTreeChecked($("#filter_resources-tree"), id + "-resources-msg");
    params['res'] = tree_val['items'];
  }
  else {
    params['res'] = list;
  }
  group_list = $.cookie(id + "-resources-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
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
  return params;
}

function loadCommentsData(md5notclear) {

  $(document).scrollTop(0);
  if (filtersReady < 2) return false;
  if (md5notclear == true) { }
  else {
    page = 0;
    $.cookie(id + "-md5", '');
    md5 = '';
    $.cookie(id + "-page-msg", 0);
  }
  tp_1 = t();
  filtersReady = 0;

  readyVector['loadCommentsData'] = false;

  var params = getCommentsParams();

  var api_method = ajaxURL_Comments;
  if ($.cookie(id + 'mm_dub') == 1) {
    params.byparent = 0;
    api_method = ajaxURL_CommentDup;
  }

  $.postJSON(api_method, params, function (responce) {

    $("#tdd-filter-show").remove();
    $('#filter-show option[selected="selected"]').removeAttr('selected');

    var toCheck = $('#filter-show option[value="' + $.cookie(id + "-show-msg") + '"]');
    if (toCheck.length)
      $('#filter-show option[value="' + $.cookie(id + "-show-msg") + '"]').attr('selected', 'selected');
    else if (responce.remove_spam == 1) $($('#filter-show option')[1]).attr('selected', 'selected');
    else $($('#filter-show option')[0]).attr('selected', 'selected');

    createDropDown("filter-show", 112);
    $("#tdd-filter-show").unbind("change").change(function (e) {
      filterChangeFlag();
      $.cookie(id + "-show-msg", $(this).attr("value"));
    });

    page = responce.page;
    post_count = responce.md5_count_post;
    // Установка количества постов с тегами и без тегов
    var tag_params = getParamsForGroupSelectionByTags(true);
    setMessageCountByFilterParams(tag_params);

    $('#ML .message[id!="template"]').remove();
    $("#template").hide();

    if (responce.md5_count_post == null) responce.md5_count_post = 0;
    $("#order_posts").html(responce.md5_count_post + " &#8211;&nbsp");

    if (responce.md5 == null) responce.md5 = '';
    $.cookie(id + "-md5", responce.md5);

    // Pagination
    $("#pagination").attr("ready", "not");
    if ($("#pagination").attr("ready") != "ready") {

      var perpage = $.cookie(id + "-perpage-msg");

      var optInit = {callback: pageselectCallback, num_edge_entries: 3, items_per_page: perpage, num_display_entries: 4, prev_text: "", next_text: "", current_page: page};
      $("#pagination").pagination(responce.md5_count_post, optInit);

      $(".gotopage", message).click(function (e) {
        page = parseInt($("#ML .gotopage-value").val(), 10) - 1;
        if (page < 0) page = 0;
        $.cookie(id + "-page-msg", page);
        reloadContent(true);
        return false;
      });

      $("#ML .gotopage-value").keypress(function (e) {
        if (e.which == 13)  $(".gotopage").click();
      });

      //first last arrow fix
      $("#pagination li:first-child a").removeAttr("href").unbind("click").click(function () {
        arrowFix("first", id, responce.md5_count_post, perpage, page)
      });
      $("#pagination li:last-child a").removeAttr("href").unbind("click").click(function () {
        arrowFix("last", id, responce.md5_count_post, perpage, page)
      });
      $("#pagination li:nth-child(" + ($("#pagination li").length - 1) + ") a").removeAttr("href").unbind("click").click(function () {
        arrowFix("next", id, responce.md5_count_post, perpage, page)
      });

      $("#pagination").attr("ready", "ready");
    }

    $("#progressbar").progressbar("option", "value", 60);

    var message, index = 1;
    $.each(responce, function (i, data) {
      //if (index != parseInt(i,perpage)) return;
      if (index != parseInt(i, 10)) return;
      message = $("#template").clone();
      message.attr("pk", data.id);
      message.attr("fav", data.fav);
      message.attr("id", "msg-" + data.id);
      $(".group-action-item", message).val(data.id);

      if (data.countdup == undefined) {
        $('.msg-block', message).css({border: 'none'});
        $('.dup-count', message).remove();
        $('.dup-link', message).remove();
      }
      else if (data.countdup == 0) {
        $('.dup-count', message).remove();
        $('.dup-link a', message).remove();
      }
      else {
        $('.dup-count span', message).html(data.countdup);
        $('.dup-link a', message).attr('href', data.id);
      }

      if (!$.isEmptyObject(data.tags)) {
        $(".group-action-item", message).addClass('has-tag')
      }
      else {
        $(".group-action-item", message).addClass('no-tag')
      }
      if ($('#group-checkbox').val() == 'all') {
        if ($.inArray(data.id, group_action_except_ids) < 0) {
          $(".group-action-item", message).attr('checked', 'checked');
        }
      }
      else if ($('#group-checkbox').val() == 'on_page' || $('#group-checkbox').val() == 'none') {
        if ($.inArray(data.id, group_action_ids) >= 0) {
          $(".group-action-item", message).attr('checked', 'checked');
        }
      }
      else if ($('#group-checkbox').val() == 'has_tag' || $('#group-checkbox').val() == 'no_tag') {
        if ($('#group-checkbox').val() == 'has_tag') {
          if ($(".group-action-item", message).hasClass('has-tag') && $.inArray(data.id, group_action_except_ids) < 0) {
            $(".group-action-item", message).attr('checked', 'checked');
          }
        }
        else {
          if ($(".group-action-item", message).hasClass('no-tag') && $.inArray(data.id, group_action_except_ids) < 0) {
            $(".group-action-item", message).attr('checked', 'checked');
          }
        }
      }
      $(".order_no", message).text(page * perpage + index);

      $(".order_tone", message)
        .css("cursor", "pointer")
        .removeClass('cb1').removeClass('cb2').removeClass('cb3')
        .addClass('cb' + (-data.nastr + 2))// Смещаем диапазон и переварачиваем  -1 ... 1 => 3 ... 1
        .click(function (e) {
          $(this).hide();
          $(this).parents(".msg-line").find(".order_tone-changer").show();
          $(this).parents(".msg-line").find(".order_no").hide();
        });

      // Тональность
      $(".order_tone-changer .cb", message).removeClass("selected").css("cursor", "pointer");
      if (data.nastr == 1) $($(".order_tone-changer .positive", message).parent()).addClass("selected");
      else if (data.nastr == 0) $($(".order_tone-changer .neutral", message).parent()).addClass("selected");
      else if (data.nastr == -1) $($(".order_tone-changer .negative", message).parent()).addClass("selected");

      $(".order_tone-changer .cb1 ", message).click(function (e) {
        changeTone($($(this).parents(".message")).attr("pk"), 1);
        e.stopPropagation();
      });
      $(".order_tone-changer .cb2 ", message).click(function (e) {
        changeTone($($(this).parents(".message")).attr("pk"), 0);
        e.stopPropagation();
      });
      $(".order_tone-changer .cb3 ", message).click(function (e) {
        changeTone($($(this).parents(".message")).attr("pk"), -1);
        e.stopPropagation();
      });
      if (data.spam == "1") {
        $(message).fadeTo('fast', 0.5);
        $(message).attr("spam", "true");
      }
      else {
        $(message).attr("spam", "false");
      }

      $(".service_ico", message).attr("src", data.img_url);
      $($(".service_ico", message).parent()).attr("href", data.url);

      activateAnswerLink(data, message);


      $(".post_title", message).html('<a href="' + data.url + '" target="_blank">' + data.post + '</a>');
      $(".post_date", message).text(data.time);

      // Элементы управления
      if (data.fav == 1) $($(".order_fav", message).children(':first-child')).attr("src", "img/btn_star.png");
      else if (data.fav == 0) $($(".order_fav", message).children(':first-child')).attr("src", "img/btn_star_0.png");
      $(".order_fav", message).click(function (e) {
        var fav = $($(this).parents(".message")).attr("fav");
        if (fav == 1) fav = 0;
        else fav = 1;
        $.postJSON(ajaxURL_SetFavourite, {id: $($(this).parents(".message")).attr("pk"), order_id: id, value: fav});
        if (fav == 1) {
          $($(this).children()).attr("src", "img/btn_star.png");
        }
        else if (fav == 0) {
          $($(this).children()).attr("src", "img/btn_star_0.png");
        }
        $($(this).parents(".message")).attr("fav", fav);
        setFavForDups($($(this).parents(".message")).attr("pk"), fav);
        return false;
      });

      $(".order_spam", message).click(function (e) {

        $.postJSON(ajaxURL_SetSpam, {id: $($(this).parents(".message")).attr("pk"), type: "post", order_id: id, value: $($(this).parents(".message")).attr("spam")});
        setSpamForDups($($(this).parents(".message")).attr("pk"), $($(this).parents(".message")).attr("spam"));
        if ($($(this).parents(".message")).attr("spam") == "true") {
          $($(this).parents(".message")).attr("spam", "false");
          $($(this).parents(".message")).fadeTo('fast', 1.0);
        }
        else {
          $($(this).parents(".message")).attr("spam", "true");
          $($(this).parents(".message")).fadeTo('fast', 0.5);
        }
        return false;
      });

      $(".full_text", message).hide().attr("loaded", "false");
      $(".order_open", message).click(function (e) {
        var message = $($(this).parents(".message"));
        if ($(".full_text", message).attr("loaded") == "false") {
          $(".full_text", message).toggle();
          $(".full_text", message).html('<img src="/img/tonloader.gif" />');
          $.postJSON(ajaxURL_GetFullText, { id: $($(this).parents(".message")).attr("pk"), order_id: id }, function (data) {
            $(".full_text", message).html(data.full_content);
            if(data.reaction){
              var react_text = '';
              for(var z=0; z<data.reaction.length; z++){
              react_text += '<div class="answer" style="display: block;"><img class="avatar" src="'+data.reaction[z].reaction_blog_ico+'"/><div class="name"> '+data.reaction[z].reaction_blog_nick+'</div><div class="text">'+data.reaction[z].reaction_content+'</div></div>';

                //react_text += '<div class="answer"><img class="avatar" src="'+data.reaction[z].+'"/><div class="name">'+data.reaction.+'</div><div class="text">'+data.reaction_content+'</div></div>';
              }
            }
            react_text +="";
            $(".full_text", message).after(react_text);
            $(".full_text", message).after();
            $(".full_text", message).attr("loaded", "true");

          });
        } else if ($(".full_text", message).attr("loaded") == "true") {
          $(".full_text", message).toggle();
        }
        $(".answer", message).toggle();
        return false;
      });

      showReaction(data.reaction, message);

      // Подвал
      if (data.gender == "1")
      {
        $(".gender", message).attr("src", "img/ico_person2.png");
      }// Женский пол
      else if (data.gender == "2")
      {
        $(".gender", message).attr("src", "img/ico_person.png");
      }// Мужской пол
      else
      {
        $(".gender", message).attr("src", "img/ico_person3.png");
      }

      $(".nick", message).attr("href", data.auth_url);

      if ((data.nick != null) && (data.nick != '')) {
        //$(".msg-l1-elm8",message).html('<a href="#" class="nick">'+data.nick+'</a><a href="#" class="order_spam_author"><img src="img/btn_x.png"/></a>');
        $(".nick", message).text(data.nick.substr(0, 12)).attr("target", "_blank");
        $(".order_spam_author", message).html('<img src="img/btn_x.png"/>');
      }
      else {
        $(".nick", message).text('');
        $(".order_spam_author", message).html('');
      }

      $(".order_spam_author", message).click(function (e) {
        $.postJSON(ajaxURL_SetSpam, {id: $($(this).parents(".message")).attr("pk"), type: "author", order_id: id, value: $($(this).parents(".message")).attr("spam")});

        var nickname = data.nick.substr(0, 12);

        $.each($('.nick'), function (e) {
          if ($(this).text() == nickname) {
            if ($($(this).parents(".message")).attr("spam") == "true") {
              $($(this).parents(".message")).attr("spam", "false");
              $($(this).parents(".message")).fadeTo('fast', 1.0);
            }
            else {
              $($(this).parents(".message")).attr("spam", "true");
              $($(this).parents(".message")).fadeTo('fast', 0.5);
            }
          }
        });
        return false;
      });

      // Количество сообщений пользователя
      if (data.count_user != undefined && parseInt(data.count_user, 10) > 0) {
        $(".count_user img", message).css("opacity", 1);
        $(".count_user span", message).removeClass("empty").text(data.count_user);
      } else {
        $(".count_user img", message).css({opacity: 0.3, zIndex: 1});
        $(".count_user span", message).addClass("empty").html("&#8212;");
      }

      // Количество лайков
      if (data.likes != undefined && parseInt(data.likes, 10) > 0) {
        $(".likes img", message).css("opacity", 1);
        $(".likes span", message).removeClass("empty").text(data.likes);
      } else {
        $(".likes img", message).css({opacity: 0.3, zIndex: 1});
        $(".likes span", message).addClass("empty").html("&#8212;");
      }

      // Количество друзей
      if (data.foll != undefined && parseInt(data.foll, 10) > 0) {
        $(".foll img", message).css("opacity", 1);
        $(".foll span", message).removeClass("empty").text(data.foll);
      } else {
        $(".foll img", message).css({opacity: 0.3, zIndex: 1});
        $(".foll span", message).addClass("empty").html("&#8212;");
      }

      // Вовлеченность
      if (data.eng != undefined && parseInt(data.eng, 10) > 0) {
        $(".eng img", message).css("opacity", 1);
        $(".eng span", message).removeClass("empty").text(data.eng);
      } else {
        $(".eng img", message).css({opacity: 0.3, zIndex: 1});
        $(".eng span", message).addClass("empty").html("&#8212;");
      }

      // Город
      if (data.geo != undefined) $(".geo", message).removeClass("empty").text(data.geo);
      else $(".geo", message).addClass("empty").html("&#8212;");
      //:~

      // Теги
      $(".tag_add-label", message).hide();
      $(".tag_add-db", message).hide();

      if (data.tags[0] === undefined) {
        $(".add-tag .template").hide();
      }

      $([$(".tag_add-db", message).get(0), $(".tag_add-label", message).get(0)]).mouseenter(function (e) {
        toggle = false;
        return false;
      });

      $([$(".tag_add-db", message).get(0), $(".tag_add-label", message).get(0)]).mouseleave(function (e) {
        var message = $(this).parents(".message");
        toggle = true;
        setTimeout('if (toggle) $(".tag_add-label").click();', 400);
        return false;
      });

      $(".tag_add a", message).click(function (e) {
        var message = $(this).parents(".message");
        $(".tag_add", message).hide();


        $(".e2_2", message).addClass("hidden");
        $(".e2", message).removeClass("hidden");

        $(".tag_add-label", message).show();
        $(".tag_add-db", message).show();
        $(".tag-panel input", message).val("Новый тег").css("color", "silver").css("font-style", "italic");

        return false;
      });

      $(".tag-panel input", message).click(function (e) {

        if ($(this).val() == "Новый тег") $(this).val("").css("font-style", "normal").css("color", "#414141");
        return false;
      });

      $(".tag-panel input", message).keypress(function (e) {
        if (e.which == 13) {
          if ($(this).val() != '') {
            var message = $(this).parents('.message');
            var message_id = $(this).parents('.message').attr("pk");
            addTag($(".tag-panel input", message).val(), message_id);
            $(this).val('');
          }
        }
      });

      $(".tag_add-label", message).click(function (e) {
        var message = $(this).parents(".message");
        $(".tag_add", message).show();
        $(".tag_add-label", message).hide();
        $(".tag_add-db", message).fadeOut(100);
        return false;
      });

      // Дропбокс
      tags[data.id] = data.tags;

      $('.add-tag .tag', message).remove();

      if (order_tags !== null && order_tags !== undefined) {
        var tag_count = 0;
        $.each(order_tags, function (id, name) {
          if( tag_count == tags_per_block )
          {
            $(".add-tag", message).css({left: '540px', width: '400px'});
            $(".message-tag-container", message).after('<div class="message-tag-container" style="border-left: 1px solid #CECECE;"></div>');
          }
          tag_count++;
          $(".add-tag template", message).hide();
          var tag = $(".add-tag .template", message).clone();
          var checked = false;
          $(".add-tag .template", message).hide();
          $.each(data.tags, function (sid, foo) {
            if( sid == id && !checked )
            {
              checked = true;
            }
          });
          if (checked)
          {
            $(".e1 img", tag).attr("src", "img/cb_ck.png");
          }
          else
          {
            $(".e1 img", tag).attr("src", "img/cb.png");
          }
          tag.addClass('tag').removeClass("template").attr("pk", id).attr('ins', (checked ? "1" : "0"));

          $(".e1 a", tag).click(function (e) {
            var parent = $(this).parents('.tag'),
              message = $(this).parents('.message');
            setTag(message.attr('pk'), parent.attr('pk'), parent.attr('ins'));
            return false;
          });

          $(".e2", tag).text(name);
          $(".e2_2 input", tag).val(name);

          $(".e2", tag).click(function (e) {
            var alltags = $(this).closest(".add-tag");
            $(".e2_2", alltags).addClass("hidden");
            $(".e2", alltags).removeClass("hidden");
            $(this).closest(".line").children(".e2_2").removeClass("hidden").children("input").focus().select();
            $(this).addClass("hidden");
          });

          $("input", tag).focusout(function (e) {
            $(".e2_2", tag).addClass("hidden");
            $(".e2", tag).removeClass("hidden");
            $(".e2", tag).text($(".e2_2 input", tag).val());
          });

          $("input", tag).keypress(function (e) {
            if (e.which == 13) {
              $(".e2_2", tag).addClass("hidden");
              $(".e2", tag).removeClass("hidden");
              $(".e2", tag).text($(".e2_2 input", tag).val());
              //TODO: add "change tag" function
              editTag(tag.attr('pk'), $(".e2_2 input", tag).val());
            }
          });

          $(".e3 a", tag).click(function (e) {
            var parent = $(this).parents('.tag'),
              message = $(this).parents('.message');
            //,parent.attr('ins')
            delTag(message.attr('pk'), parent.attr('pk'));
            return false;
          });

          tag.css("display", "block");
          $('.message-tag-container', message).last().append(tag);
        });
      }

      $(".tag-panel .btn a", message).click(function (e) { //добавление тега!
        var message = $(this).parents('.message');
        var message_id = $(this).parents('.message').attr("pk");
        addTag($(".tag-panel input", message).val(), message_id);
        $(".tag-panel input", message).val("");
        return false;
      });

      message.insertBefore("#ML .ml-footer");
      message.show();

      index++;

      $(".service_ico").attr("title", 'Ресурс');
      $(".order_tone").attr('title', 'Простановка тональности.');
      $(".order_spam_author").attr('title', 'Удалить автора из выдачи.');
      $(".foll").attr('title', 'Охват упоминания.');
      $(".eng").attr('title', 'Вовлеченность.');
      $(".post_date").attr('title', 'Дата и время упоминания.');
      $(".gender").attr('title', 'Пол автора.');
      $(".nick").attr('title', 'Ник автора.');
      $(".nick").attr('title', 'Ник автора.');
      $(".order_fav").attr('title', 'Добавить в избранное.');
      $(".order_open").attr('title', 'Показать полный текст упоминания.');
      $(".order_spam").attr('title', 'Удалить упоминание.');
    });

    $("#MM").height($("#ML").height());
    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(1000);
    readyVector['loadCommentsData'] = true;
    tpCD = t();
    tegFix();
  });
}

//фикс для последних 5ти меню тегов, чтобы не вылезали за footer
function tegFix() {
  var len = $(".tag_add-db").length;
  if (len < 5) var count = len; else var count = 5;
  var total = $(document).height();
  for (var i = len; i > 0; i--) {
    $(".tag_add-db:eq(" + (len - i) + ")").show();
    var topToElm = $(".tag_add:eq(" + (len - i) + ")").offset().top;
    var curr = $(".add-tag:eq(" + (len - i) + ")");
    var h = $(curr).height();
    if (total < topToElm + h + 60) {
      $(curr).css("top", -h - 56 + "px");
    }
    $(".tag_add-db:eq(" + (len - i) + ")").hide()
  }
}

/* Функция перезагрузки содержимого страници */
function reloadContent(md5notclear) {

  $("#progressbar").progressbar("option", "value", 60);
  $(".progress").fadeIn(1);
  if (md5notclear == true) {

  }
  else {
    page = 0;
    $.cookie(id + "-md5", '');
    md5 = '';
    $.cookie(id + "-page-msg", 0);
  }
  loadFiltersData(md5notclear);
}

/**
 * Callback function that displays the content.
 *
 * Gets called every time the user clicks on a pagination link.
 *
 * @param {int}page_index New Page index
 * @param {jQuery} jq the container with the pagination links as a jQuery object
 */
function pageselectCallback(page_index, jq) {
  if ($("#pagination").attr("ready") == "ready") {
    page = page_index;
    $.cookie(id + "-page-msg", page_index);
    reloadContent(true);
  }
  return false;
}

/*
 Обработчики пресетов
 API: load_preset, del_preset, save_preset
 */

var haveToSavePreset = true;
function savePreset() {
  if (!haveToSavePreset) return;
  var snapshot = {
    'order_id': id,
    'name': $("#preset-dialog-add .name").val(),
    'params': {
      'mm-promouters': $.cookie(id + 'mm-promouters'),
      'mm-resources': $.cookie(id + 'mm-resources'),
      'mm-speakers': $.cookie(id + 'mm-speakers'),
      'mm-words': $.cookie(id + 'mm-words'),

      '-page-msg': $.cookie(id + '-page-msg'),

      '-positive-msg': $.cookie(id + '-positive-msg'),
      '-negative-msg': $.cookie(id + '-negative-msg'),
      '-neutral-msg': $.cookie(id + '-neutral-msg'),

      '-fromDate-theme': $.cookie(id + '-fromDate-theme'),
      '-toDate-theme': $.cookie(id + '-toDate-theme'),

      '-cities-msg': $.cookie(id + '-cities-msg'),
      '-cities-msg-group': $.cookie(id + '-cities-msg-group'),
      '-resources-msg': $.cookie(id + '-resources-msg'),
      '-resources-msg-group': $.cookie(id + '-resources-msg-group'),

      '-show-msg': $.cookie(id + '-show-msg'),
      '-sort-msg': $.cookie(id + '-sort-msg')
    }
  };

  $.postJSON(ajaxURL_AddPreset, snapshot, function (data) {
    if (data.status == "fail") return;
    var preset = $("#presets .template").clone();
    preset.attr("id", "preset-" + data.preset_id).attr("pk", data.preset_id);
    $(".title", preset).text(data.preset_name);
    $(".load-preset", preset)
      .css("cursor", "pointer")
      .click(function (e) {
        var id = parseInt($(this).parents(".preset").attr("pk"), 10);
        loadPreset(id);
        return false;
      });
    $(".x-preset", preset)
      .css("cursor", "pointer")
      .click(function (e) {
        var id = parseInt($(this).parents(".preset").attr("pk"), 10);
        deletePreset(id);
        return false;
      });
    preset.show();
    preset.insertAfter($("#presets-header"));
  });
}

function loadPreset(pid) { }

/*
 Инициализация боковой панели
 */

function onSelectMMitem(index, cast) {
  return false;
  $.cookie(id + mmItems[index].id, cast);
  filterChangeFlag();
}

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
  });
  //:~

  // Построение
  $("#mm-template").hide();
  $("#mm-promouters").remove();
  $("#mm-tags").remove();
  $("#mm-words").remove();
  $(".dup-checkbox").remove();

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

      if ( $.cookie(id + '-aditional-words') != null && $.cookie(id + '-aditional-words').length > 0 )
      {
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
    if( info.id == "mm-words" )
    {
      $('.btns', node).before('<div id="new-word"><input type="text" value=""><button>Добавить</button></div>');
      $("#new-word", node).hide();
      $("#new-word button", node).click(function(){
        if( $("#new-word input", node).val().length > 0 )
        {
          $(".flist", node).append('<p class="fitem"><input type="checkbox" class="inline fcheck" id="word_' + $("#new-word input", node).val() + '" checked="checked"/><span>&nbsp;' + $("#new-word input", node).val() + '</span></p>');
          var aditional_words = [];
          if( $.cookie(id + '-aditional-words') != null && $.cookie(id + '-aditional-words').length > 0 )
          {
            aditional_words = $.cookie(id + '-aditional-words').split(',');
          }
          aditional_words.push($("#new-word input", node).val());
          $.cookie(id + '-aditional-words', aditional_words.join(','));
          $.cookie(id + '-word_' + encodeURIComponent($("#new-word input", node).val()), 'true');
          $("#new-word input", node).val('');
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

  // Флаг для перепечаток
  var checked = $.cookie(id + 'mm_dub');
  if (checked == undefined || checked == null) {
    checked = 0;
    //убрана галка перепечаток по умолчанию
    /*if (has_dup == 1) {
      checked = 1;
    }*/
    $.cookie(id + 'mm_dub', checked);
  }
  var node = '<div class="dup-checkbox"><input type="checkbox" value="without_dup" id="dup-check" class="inline" onchange="changeDubFilter();">Без перепечаток</div>';
  if (checked == 1) {
    node = '<div class="dup-checkbox"><input type="checkbox" value="without_dup" id="dup-check" class="inline" onchange="changeDubFilter();" checked="checked">Без перепечаток</div>';
  }
  if (has_dup == 0) {
    node = '<div class="dup-checkbox"><input type="checkbox" disabled="disabled" value="without_dup" id="dup-check" class="inline" onchange="return false;">Без перепечаток</div>';
  }
  $("#mm-items").append(node);


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
  });

  $(".fcheck").click(function () {
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

function filterChangeFlag() {
  if (!filterChanged) {
    filterChanged = true;
    $("#tone_toggle").css("background-color", "#3CABDF");
  }
}

// Инициализация
$(document).ready(function () {
  function checkLength(o, n, min, max, tip) {
    if (o.val().trim().length > max || o.val().trim().length < min) {
      o.addClass("ui-state-error");
      updateTips(n, tip);
      return false;
    } else {
      return true;
    }
  }

  $(".tag_add-label").hide();
  $(".tag_add-db").hide();
  $("#progressbar").progressbar({
    value: 0
  });

  id = document.location.href.split("#");
  id = id[id.length - 1];

  user_id = $.cookie("user_id");
  page = ($.cookie(id + "-page-msg") | 0);

  // Шапка
  $("#home").attr("href", inernalURL_themesList);
  $("#order_home").attr("href", inernalURL_themePage + id);

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
  /*:~ */

  //новый datepicker
  $("#date").click(function (e) {
    hideFilters();
    $("#date .dp").toggle();
    $("#datepicker").blur();
    return false;
  });

  $("#date .ddp").click(function (e) {
    filterChangeFlag();
    hideFilters();
    $("#date .dp").toggle();
    return false;
  });

  $(document).bind('click', function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("dd") && !$clicked.parents().hasClass("ui-datepicker-calendar") && !$clicked.parents().hasClass("ui-datepicker-prev") && !$clicked.parents().hasClass("ui-datepicker-next")) {
      $("#date .dp").hide();
      if (wasChanged) {
        wasChanged = false;
        $.cookie(id + "-fromDate-theme", $("#dp-begin").datepicker("getDate").getTime());
        $.cookie(id + "-toDate-theme", $("#dp-end").datepicker("getDate").getTime());
      }
    }
    if ((!$clicked.parents().hasClass("dropdown")))
    {
      $(".dropdown dd ul").hide();
    }
    if ((!$clicked.parents().hasClass("answer-block")))
    {
      $(".answer-block").hide();
    }
  });
  //конец новый

  $("#faq").attr("href", inernalURL_faq);
  $("#progressbar").progressbar("option", "value", 10);
  // Фильтры
  var toCheck;
  // Показать:

  //TODO: тут запилить то что надо, удалить и потом заново создать.

  getPages();
//  createDropDown("export", 58);
  $("#progressbar").progressbar("option", "value", 20);
  //Тональности
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
      group_action_ids = [];
      group_action_except_ids = [];
      selected_count = 0;
      $('#group-actions #group-checkbox').removeAttr('checked').val('none');
      $('#group-actions .group-choices').addClass('hidden');
      $('#group-actions .group-choices.check').removeClass('hidden');
      $('.group-action-item').removeAttr('checked');
      $('#group-actions .group-selected-count span').html(selected_count);
      $('#group-actions .tags .variants input[type="checkbox"]').removeAttr('checked');

      hideFilters();
      $("#tone_toggle").css("background-color", "#CCC");
      filterChanged = false;
      $(window, document).scrollTop(0);
      reloadContent();

      return false;
    }
  });

  $('#clear_filter').unbind('click').click(function(){
    clearFilter();
  });

  // :~

  $("#preset-dialog-add").dialog({
    autoOpen: false,
    resizable: false,
    height: 150,
    width: 268,
    modal: true,
    title: "Добавление пресета",
    buttons: {
      "Добавить": function () {
        haveToSavePreset = true;
        $(this).dialog("close");
      },
      "Отмена": function () {
        haveToSavePreset = false;
        $(this).dialog("close");
      }
    },
    close: savePreset
  });
  $("#progressbar").progressbar("option", "value", 30);
  loadOrderData();

  // Прочее (глобальное)
  $('html').click(function (e) {
    if ($(e.target).parents().hasClass("tree-dd") == false) {
      $('.tree-dd').fadeOut(100);
      $('.filter-type-selector').fadeOut(100);
      if (isChangedTree) {
      }
      isChangedTree = false;
    }
    if ($(e.target).parents().hasClass("tag_add-db") == false) {
      $(".tag_add").show();
      $(".tag_add-label").hide();
      $(".tag_add-db").fadeOut(100);

    }
    if (($(e.target).parents().hasClass("export-list-db") == false) && ($(e.target).parents().hasClass("inline elem8") == false)) {
      if ($('.export-list').is(':visible')) {
        $('.export-list').fadeOut(400);
        $('.export-list_upper').fadeOut(400);
      }
    }
  });
  $("#MM").height($("#ML").height());

  $("#order_posts").tipTip({content: 'Количество упоминаний'});
  $("#home").tipTip({content: 'Список тем'});
  $("#order_name").tipTip({content: 'Страница темы'});
  $("#user_email").tipTip({content: 'Настройки пользователя', defaultPosition: "right"});
  $("#user_exp").tipTip({content: 'Для продления кабинета необходима оплата тарифа', defaultPosition: "bottom"});

  var notices = new Array('messagesNotice');
  showNotices(notices);

  $(window).scroll(function () {
    windowScrollSubscriber();
  });

  $('#top-button').click(function(){
    $("html, body").animate({ scrollTop: 0 });
    return false;
  });

  // Групповые действия
  $('#group-actions .group-choices').hover(function () {
    $('.variants', $(this)).removeClass('hidden');
  }, function () {
    $('.variants', $(this)).addClass('hidden');
  });
  // Отметка/снятие отметки отдельных сообщений
  $('.group-action-item').live('change', function () {
    var _this = $(this);
    if (_this.is(':checked')) {
      selected_count++;
      addSelectedIds(_this.val());
      removeIgnoreIds(_this.val());
    }
    else {
      selected_count--;
      removeSelectedIds(_this.val());
      addIgnoreIds(_this.val());
    }
    if (selected_count == 0) {
      group_action_ids = [];
      group_action_except_ids = [];
      $('#group-actions #group-checkbox').val('none');
      $('#group-actions #group-checkbox').removeAttr('checked');
      $('#group-actions .group-choices').addClass('hidden');
      $('#group-actions .group-choices.check').removeClass('hidden');
    }
    else {
      $('#group-actions #group-checkbox').attr('checked', 'checked');
      $('#group-actions .group-choices').removeClass('hidden');
    }

    $('#group-actions .group-selected-count span').html(selected_count);
    $('#group-actions .tags .variants input[type="checkbox"]').removeAttr('checked');
  });
  // Варианты груповой выборки
  $('#group-actions .check .variants a').click(function () {
    $('#group-actions #group-checkbox').val($(this).attr('href'));
    group_action_ids = [];
    group_action_except_ids = [];
    if ($(this).attr('href') == 'none') {
      $('.group-action-item').removeAttr('checked');
      selected_count = 0;
    }
    else if ($(this).attr('href') == 'on_page' || $(this).attr('href') == 'all') {
      $('.group-action-item').attr('checked', 'checked');
      $('#template .group-action-item').removeAttr('checked');
      if ($(this).attr('href') == 'on_page') {
        selected_count = Math.min(page_post_count, post_count);
        $('.group-action-item').each(function () {
          if ($(this).val() != 2) {
            addSelectedIds($(this).val());
          }
        });
      } else if ($(this).attr('href') == 'all') {
        selected_count = post_count;
      }
    }
    else if ($(this).attr('href') == 'has_tag' || $(this).attr('href') == 'no_tag') {
      $('.group-action-item').removeAttr('checked');
      if ($(this).attr('href') == 'has_tag') {
        $('.group-action-item.has-tag').attr('checked', 'checked');
        selected_count = post_count_has_tag;
      }
      else {
        $('.group-action-item.no-tag').attr('checked', 'checked');
        selected_count = post_count_no_tag;
      }
    }
    if (selected_count > 0) {
      $('#group-actions #group-checkbox').attr('checked', 'checked');
      $('#group-actions .group-choices').removeClass('hidden');
    }
    else {
      $('#group-actions #group-checkbox').removeAttr('checked');
      $('#group-actions .group-choices').addClass('hidden');
      $('#group-actions .group-choices.check').removeClass('hidden');
    }
    $('#group-actions .group-selected-count span').html(selected_count);
    $('#group-actions .check .variants').addClass('hidden');
    $('#group-actions .tags .variants input[type="checkbox"]').removeAttr('checked');
    return false;
  });
  $('#group-checkbox').change(function () {
    var _this = $(this);
    group_action_ids = [];
    group_action_except_ids = [];
    if (_this.is(':checked')) {
      _this.val('on_page');
      selected_count = Math.min(page_post_count, post_count);
      $('.group-action-item').attr('checked', 'checked');
      $('#template .group-action-item').removeAttr('checked');
    }
    else {
      _this.val('none');
      selected_count = 0;
      $('.group-action-item').removeAttr('checked');
    }
    if (selected_count > 0) {
      $('#group-actions .group-choices').removeClass('hidden');
    }
    else {
      $('#group-actions .group-choices').addClass('hidden');
      $('#group-actions .group-choices.check').removeClass('hidden');
    }
    $('#group-actions .group-selected-count span').html(selected_count);
    $('#group-actions .tags .variants input[type="checkbox"]').removeAttr('checked');
    return false;
  });
  // Выбор действия над отмеченными сообщениями
  $('#group-actions .actions .variants a').click(function () {
    var _this = $(this);
    var action = _this.attr('href');
    makeGroupAction(action);
    return false;
  });

  $('#group-actions .tags .variants a').live('click', function () {
    var _this = $(this);
    if (_this.attr('id') != 'group-action-add-tag') {
      var tag_id = _this.attr('href');
      groupTag(tag_id, _this);
    }
    return false;
  });
  $('#group-actions .tags .variants input[type="checkbox"]').live('change', function () {
    var _this = $(this);
    if (_this.is(':checked')) {
      _this.removeAttr('checked');
    }
    else {
      _this.attr('checked', 'checked');
    }
    if (_this.attr('id') != 'group-action-add-tag') {
      var tag_id = _this.attr('href');
      groupTag(tag_id, _this);
    }
    return false;
  });

  $('.filter-type-selector .choice-button').click(function (e) {
    var _this = $(this);
    var filter_id = _this.parent().attr('id');
    $('.choice-button', _this.parent()).removeClass('selected');
    _this.addClass('selected');
    $.cookie(id + "-" + filter_id, _this.attr('cast'));
    filterChangeFlag();
    e.stopPropagation();
  });

  $('.answer_message a').live('click', function(){
    $('.answer-block').hide();
    if( !$(this).hasClass('disabled') && user_reaction == 1 )
    {
      var block = $(this).parents('.answer_message');
      updateSocialAccounts(block);
      $('.answer-block', block).toggle();
    }
    return false;
  });
});

function messagesNotice() {
  if ($.cookie("messagesNoticeNot") != 1) {
    var newresNoticeId = $.gritter.add({
      title: 'Просмотр упоминаний',
      text: 'На этой странице вы можете вручную обработать выдачу мониторинга. ' +
        'Вы можете проставить тональность, добавить теги, а также исключить нерелевантные сообщения из выдачи.<br><br>' +
        'Под каждым упоминанием содержится информация об авторе сообщения (пол, возраст, география) и вовлеченности его' +
        ' аудитории в обсуждение темы. <br><br>Вы можете отфильтровать выдачу сообщений по следующим критериям:' +
        ' по авторам,тональности, ресурсам и по тегам. После фильтрации упоминаний, выдачу можно сохранить в удобном' +
        ' виде или распечатать.<br><br><a href="#" id="messagesNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
      sticky: true,
      time: '',
      class_name: 'my-sticky-class',
      after_close: function (e) {
        //compareNotice();
      },
    });
    $('#messagesNoticeNot').click(function () {
      $.cookie("messagesNoticeNot", 1);
      hideNotice("messagesNotice", 1);
      $.gritter.remove(newresNoticeId);
      return false;
    });
  }
}

function makeGroupAction(action) {
  var with_dups = false;
  if (has_dup == 1 && $.cookie(id + 'mm_dub') == 1) {
    with_dups = true;
  }
  var check_val = $('#group-checkbox').val();
  var ids = getCheckedOnPage();
  var except_ids = getUncheckedOnPage();
  var ids_arr = ids.split(',');
  if (group_action_ids.length > 0) {
    ids += ',' + group_action_ids.join(',');
  }
  if (group_action_except_ids.length > 0) {
    except_ids += ',' + group_action_ids.join(',');
  }
  if (action == 'favorite' || action == 'not_favorite') {
    var value = 1;
    if (action == 'not_favorite') {
      value = 0;
    }

    var params = {order_id: id, id: ids, value: value};
    if (with_dups) {
      params.parent_id = ids;
    }
    var function_name = ajaxURL_SetFavourite;

    if (check_val == 'all') {
      params = getCommentsParams();
      params.order_id = id;
      params.value = value;
      params.adv_query = 1;
      params.except_id = except_ids;
      function_name = ajaxURL_SetFavouriteAdv;
      if (with_dups) {
        params.except_byparent = except_ids;
      }
    }
    else if (check_val == 'has_tag' || check_val == 'no_tag') {
      params = getCommentsParamsForTagSelection();
      params.order_id = id;
      params.value = value;
      params.adv_query = 1;
      params.except_id = except_ids;
      function_name = ajaxURL_SetFavouriteAdv;
      if (with_dups) {
        params.except_byparent = except_ids;
      }
    }
    groupFavorite(function_name, params, ids_arr);
  }

  if (action == 'spam' || action == 'not_spam') {
    var value = true;
    if (action == 'spam') {
      value = false;
    }

    params = {order_id: id, id: ids, value: value, type: 'post'};
    if (with_dups) {
      params.type = 'parent';
      params.parent_id = ids;
    }
    function_name = ajaxURL_SetSpam3;

    if (check_val == 'all') {
      params = getCommentsParams();
      params.order_id = id;
      params.value = value;
      params.adv_query = 1;
      params.except_id = except_ids;
      function_name = ajaxURL_SetSpamAdv;
      if (with_dups) {
        params.except_byparent = except_ids;
      }
    }
    else if (check_val == 'has_tag' || check_val == 'no_tag') {
      params = getCommentsParamsForTagSelection();
      params.order_id = id;
      params.value = value;
      params.adv_query = 1;
      params.except_id = except_ids;
      function_name = ajaxURL_SetSpamAdv;
      if (with_dups) {
        params.except_byparent = except_ids;
      }
    }
    groupSpam(function_name, params, ids_arr);
  }

  if (action == 'positive' || action == 'neutral' || action == 'negative') {
    var value = 1;
    if (action == 'neutral') {
      value = 0;
    }
    if (action == 'negative') {
      value = -1
    }

    params = {order_id: id, id: ids, value: value};
    if (with_dups) {
      params = {order_id: id, parent_id: ids, value: value};
    }
    function_name = ajaxURL_ToneChange;

    if (check_val == 'all') {
      params = getCommentsParams();
      params.order_id = id;
      params.value = value;
      params.adv_query = 1;
      params.except_id = except_ids;
      function_name = ajaxURL_ToneChangeAdv;
      if (with_dups) {
        params.except_byparent = except_ids;
      }
    }
    else if (check_val == 'has_tag' || check_val == 'no_tag') {
      params = getCommentsParamsForTagSelection();
      params.order_id = id;
      params.value = value;
      params.adv_query = 1;
      params.except_id = except_ids;
      function_name = ajaxURL_ToneChangeAdv;
      if (with_dups) {
        params.except_byparent = except_ids;
      }
    }
    groupNastr(function_name, params, ids_arr);
  }
}

function getCheckedOnPage() {
  var ids = [];
  $('.group-action-item:checked').each(function () {
    if ($(this).val() != 2) {
      ids.push($(this).val());
    }
  });
  return ids.join(',');
}

function getUncheckedOnPage() {
  var ids = [];
  $('.group-action-item:not(:checked)').each(function () {
    if ($(this).val() != 2) {
      ids.push($(this).val());
    }
  });
  return ids.join(',');
}

function groupFavorite(method, params, ids_arr) {
  $('#group-actions .actions .variants').addClass('hidden');
  $("#progressbar").progressbar("option", "value", 0);
  $(".progress").fadeIn();
  $.postJSON(method, params, function (responce) {
    if (responce.status == 'ok') {
      $.each(ids_arr, function (i, val) {
        if (params.value == 1) {
          $('#msg-' + val).attr('fav', 1);
          $('#msg-' + val + ' a.order_fav img').attr('src', 'img/btn_star.png');

          $('#msg-' + val + 'dups .message').attr('fav', 1);
          $('#msg-' + val + 'dups .message a.order_fav img').attr('src', 'img/btn_star.png');
        }
        else {
          $('#msg-' + val).attr('fav', 0);
          $('#msg-' + val + ' a.order_fav img').attr('src', 'img/btn_star_0.png');

          $('#msg-' + val + 'dups .message').attr('fav', 0);
          $('#msg-' + val + 'dups .message a.order_fav img').attr('src', 'img/btn_star_0.png');
        }
      });
    }
    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(1000);
  });
}

function groupSpam(method, params, ids_arr) {
  $('#group-actions .actions .variants').addClass('hidden');
  $("#progressbar").progressbar("option", "value", 0);
  $(".progress").fadeIn();
  $.postJSON(method, params, function (responce) {
    if (responce.status == 'ok') {
      $.each(ids_arr, function (i, val) {
        if (params.value) {
          $('#msg-' + val).attr("spam", "false").fadeTo('fast', 1.0);
          $('#msg-' + val + 'dups .message').attr("spam", "false").fadeTo('fast', 1.0);
        }
        else {
          $('#msg-' + val).attr("spam", "true").fadeTo('fast', 0.5);
          $('#msg-' + val + 'dups .message').attr("spam", "true").fadeTo('fast', 0.5);
        }
      });
    }
    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(1000);
  });
}

function groupNastr(method, params, ids_arr) {
  $('#group-actions .actions .variants').addClass('hidden');
  $("#progressbar").progressbar("option", "value", 0);
  $(".progress").fadeIn();
  $.postJSON(method, params, function (responce) {
    if (responce.status == 'ok') {
      $.each(ids_arr, function (i, val) {
        $(".order_tone", $('#msg-' + val)).show().removeClass('cb1').removeClass('cb2').removeClass('cb3').addClass('cb' + (-params.value + 2));
        $(".order_tone", $('#msg-' + val + 'dups')).show().removeClass('cb1').removeClass('cb2').removeClass('cb3').addClass('cb' + (-params.value + 2));
        $(".order_tone-changer .cb", $('#msg-' + val)).removeClass("selected");
        $(".order_tone-changer .cb", $('#msg-' + val + 'dups')).removeClass("selected");
        if (params.value == 1) {
          $(".order_tone-changer .positive", $('#msg-' + val)).parent().addClass("selected");
          $(".order_tone-changer .positive", $('#msg-' + val + 'dups')).parent().addClass("selected");
        }
        else if (params.value == 0) {
          $(".order_tone-changer .neutral", $('#msg-' + val)).parent().addClass("selected");
          $(".order_tone-changer .neutral", $('#msg-' + val + 'dups')).parent().addClass("selected");
        }
        else if (params.value == -1) {
          $(".order_tone-changer .negative", $('#msg-' + val)).parent().addClass("selected");
          $(".order_tone-changer .negative", $('#msg-' + val + 'dups')).parent().addClass("selected");
        }
      });
    }
    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(1000);
  });
}

function groupTag(tag_id, _this) {
  var with_dups = false;
  if (has_dup == 1 && $.cookie(id + 'mm_dub') == 1) {
    with_dups = true;
  }
  var check_val = $('#group-checkbox').val();
  var ids = getCheckedOnPage();
  var except_ids = getUncheckedOnPage();
  var ids_arr = ids.split(',');
  if (group_action_ids.length > 0) {
    ids += ',' + group_action_ids.join(',');
  }
  if (group_action_except_ids.length > 0) {
    except_ids += ',' + group_action_ids.join(',');
  }
  var params = {order_id: id, id: ids, tag_value: true, tag_id: tag_id};
  if (with_dups) {
    params.parent_id = ids;
    params.type = 'parent';
  }
  var api_method = ajaxURL_SetTag;
  var checkbox = $('input[type="checkbox"]', _this.parent());

  if (check_val == 'all') {
    params = getCommentsParams();
    params.order_id = id;
    params.tag_id = tag_id;
    params.tag_value = true;
    params.adv_query = 1;
    params.except_id = except_ids;
    api_method = ajaxURL_SetTagAdv;
    if (with_dups) {
      params.except_byparent = except_ids;
    }
  }
  else if (check_val == 'has_tag' || check_val == 'no_tag') {
    params = getCommentsParamsForTagSelection();
    params.order_id = id;
    params.tag_id = tag_id;
    params.tag_value = true;
    params.adv_query = 1;
    params.except_id = except_ids;
    api_method = ajaxURL_SetTagAdv;
    if (with_dups) {
      params.except_byparent = except_ids;
    }
  }
  $('#group-actions .tags .variants').addClass('hidden');
  $("#progressbar").progressbar("option", "value", 0);
  $(".progress").fadeIn();
  if (checkbox.is(':checked')) {
    checkbox.removeAttr('checked');
    params.tag_value = false;
  }
  else {
    checkbox.attr('checked', 'checked');
  }

  $.postJSON(api_method, params, function (responce) {
    if (responce.status == 'ok') {
      $.each(ids_arr, function (i, val) {
        var tag = $('.message[pk="' + val + '"] .tag[pk="' + tag_id + '"]');
        var tag_dup = $('#msg-' + val + 'dups .message .tag[pk="' + tag_id + '"]');
        if (params.tag_value) {
          $(".e1 img", tag).attr("src", "img/cb_ck.png");
          $(".e1 img", tag_dup).attr("src", "img/cb_ck.png");
          tag.attr('ins', "1");
          tag_dup.attr('ins', "1");
        }
        else {
          $(".e1 img", tag).attr("src", "img/cb.png");
          $(".e1 img", tag_dup).attr("src", "img/cb.png");
          tag.attr('ins', "0");
          tag_dup.attr('ins', "0");
        }
      });
    }
    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(200);
    $('#group-actions .tags .variants').removeClass('hidden');
  });
}

function initGroupTags() {
  $('#group-actions .group-choices.tags ul').eq(1).remove();
  $('#group-actions .group-choices.tags ul').html('');
  var tag_count = 0;
  $.each(order_tags, function (i, e) {
    if( tag_count == tags_per_block )
    {
      $('#group-actions .group-choices.tags ul').last().after('<ul></ul>');
      $('#group-actions .group-choices.tags .variants').css({width: '325px'});
    }
    tag_count++;
    $('#group-actions .group-choices.tags ul').last().append('<li><input type="checkbox" href="' + i + '"/><a href="' + i + '">' + e + '</a></li>');
  });
  $('#group-actions .group-choices.tags ul').eq(0).append('<li><input type="text" id="group-action-new-tag"><a href="javascript:void(0);" onclick="addGroupTag();" id="group-action-add-tag">+</a></li>');
}

function addGroupTag() {
  var tag_val = $('#group-action-new-tag').val();
  if (tag_val.length > 0) {
    addTag(tag_val, -1);
  }
}

function addSelectedIds(id) {
  group_action_ids.push(id);
}

function removeSelectedIds(id) {
  var new_group_action_ids = [];
  $.each(group_action_ids, function (i, v) {
    if (id != v) {
      new_group_action_ids.push(v);
    }
  });
  group_action_ids = new_group_action_ids;
}

function removeIgnoreIds(id) {
  var new_group_action_except_ids = [];
  $.each(group_action_except_ids, function (i, v) {
    if (id != v) {
      new_group_action_except_ids.push(v);
    }
  });
  group_action_except_ids = new_group_action_except_ids;
}

function addIgnoreIds(id) {
  group_action_except_ids.push(id);
}

function rebuildGroupSelectionForMessage(mid) {
  if ($('#group-checkbox').val() == 'has_tag' || $('#group-checkbox').val() == 'no_tag') {
    var has_tag = false;
    var checkbox = $('.group-action-item', $('#msg-' + mid));
    $('.m.tag', $('#msg-' + mid)).each(function () {
      if ($(this).attr('ins') == 1) {
        has_tag = true;
      }
    });
    if (has_tag) {
      checkbox.removeClass('no-tag');
      checkbox.addClass('has-tag');
      if ($('#group-checkbox').val() == 'has_tag' && !checkbox.is(':checked')) {
        checkbox.attr('checked', 'checked');
        checkbox.trigger('change');
      }
      else if ($('#group-checkbox').val() == 'no_tag' && checkbox.is(':checked')) {
        checkbox.removeAttr('checked');
        checkbox.trigger('change');
      }
    }
    else {
      checkbox.removeClass('has-tag');
      checkbox.addClass('no-tag');
      if ($('#group-checkbox').val() == 'has_tag' && checkbox.is(':checked')) {
        checkbox.removeAttr('checked');
        checkbox.trigger('change');
      }
      else if ($('#group-checkbox').val() == 'no_tag' && !checkbox.is(':checked')) {
        checkbox.attr('checked', 'checked');
        checkbox.trigger('change');
      }
    }
  }
}

function getCommentsParamsForTagSelection() {
  if ($('#group-checkbox').val() == 'has_tag') {
    return getParamsForGroupSelectionByTags(true);
  }
  else {
    return getParamsForGroupSelectionByTags(false);
  }
}

function getParamsForGroupSelectionByTags(has_tag) {
  var params_t = getCommentsParams();
  params_t.md5 = '';
  if (has_tag) {
    params_t.tags = 'selected';
  }
  else {
    params_t.tags = 'except';
  }
  $.each(order_tags, function (i, e) {
    params_t['tag_' + i] = true;
  });
  return params_t;
}

function setMessageCountByFilterParams(patams_t) {
  $.postJSON(ajaxURL_CommentCount, patams_t, function (responce) {
    post_count_has_tag = responce.count;
    post_count_no_tag = post_count - responce.count;
  });
}

function changeDubFilter() {
  var checked = 0;
  if ($('#dup-check').is(':checked')) {
    checked = 1;
  }
  $.cookie(id + 'mm_dub', checked);
  filterChangeFlag();
}

function showBup(_this) {
  var dup_block = $("#msg-" + _this.attr('href') + 'dups');
  if (dup_block.length > 0) {
    if (_this.html() == '-') {
      _this.html('+');
      dup_block.hide();
    }
    else {
      _this.html('-');
      dup_block.show();
    }
    $("#MM").height($("#ML").height());
  }
  else {
    loadDups(_this.attr('href'));
    _this.html('-');
  }
  return false;
}

function setFavForDups(mid, value) {
  if ($('#msg-' + mid + ' .dup-link a').attr('href') > 0) {
    var callback = function (ids) {
      $.postJSON(ajaxURL_SetFavourite, {id: ids.join(','), type: "post", order_id: id, value: value});
      if (value == 1) {
        $('.message .msg-l1-elm6 .order_fav img', $('#msg-' + mid + 'dups')).attr("src", "img/btn_star.png");
      }
      else if (value == 0) {
        $('.message .msg-l1-elm6 .order_fav img', $('#msg-' + mid + 'dups')).attr("src", "img/btn_star_0.png");
      }
      $('.message', $('#msg-' + mid + 'dups')).attr("fav", value);
    };

    if ($('#msg-' + mid + 'dups').length > 0) {
      var ids = [];
      $('.message', $('#msg-' + mid + 'dups')).each(function (index) {
        ids.push($(this).attr('pk'));
      });
      callback(ids);
    }
    else {
      loadDups(mid, callback);
    }
  }
  return false;
}

function setSpamForDups(mid, value) {
  if ($('#msg-' + mid + ' .dup-link a').attr('href') > 0) {
    var callback = function (ids) {
      $.postJSON(ajaxURL_SetSpam3, {id: ids.join(','), type: "post", order_id: id, value: value});
      if (value == "true") {
        $('.message', $('#msg-' + mid + 'dups')).attr("spam", "false").fadeTo('fast', 1.0);
      }
      else {
        $('.message', $('#msg-' + mid + 'dups')).attr("spam", "true").fadeTo('fast', 0.5);
      }
    };

    if ($('#msg-' + mid + 'dups').length > 0) {
      var ids = [];
      $('.message', $('#msg-' + mid + 'dups')).each(function (index) {
        ids.push($(this).attr('pk'));
      });
      callback(ids);
    }
    else {
      loadDups(mid, callback);
    }
  }
  return false;
}

function setNastrForDups(mid, tone) {
  if ($('#msg-' + mid + ' .dup-link a').attr('href') > 0) {
    var callback = function (ids) {
      $(".message .order_tone-changer", $('#msg-' + mid + 'dups')).hide();
      $(".message .order_tone", $('#msg-' + mid + 'dups')).hide();
      $(".message .tonloader", $('#msg-' + mid + 'dups')).show();
      $(".message .order_no", $('#msg-' + mid + 'dups')).show();
      $.postJSON(ajaxURL_ToneChange, {order_id: id, id: ids.join(','), value: tone}, function (data) {

        $(".message .tonloader", $('#msg-' + mid + 'dups')).hide();
        $(".message .order_tone", $('#msg-' + mid + 'dups'))
          .show()
          .removeClass('cb1').removeClass('cb2').removeClass('cb3')
          .addClass('cb' + (-tone + 2) // Смещаем диапазон и переварачиваем  -1 ... 1 => 3 ... 1
          );

        $(".message .order_tone-changer .cb", $('#msg-' + mid + 'dups')).removeClass("selected");
        if (tone == 1) {
          $($(".message .order_tone-changer .positive", $('#msg-' + mid + 'dups')).parent()).addClass("selected");
        }
        else if (tone == 0) {
          $($(".message .order_tone-changer .neutral", $('#msg-' + mid + 'dups')).parent()).addClass("selected");
        }
        else if (tone == -1) {
          $($(".message .order_tone-changer .negative", $('#msg-' + mid + 'dups')).parent()).addClass("selected");
        }
      });
    };

    if ($('#msg-' + mid + 'dups').length > 0) {
      var ids = [];
      $('.message', $('#msg-' + mid + 'dups')).each(function (index) {
        ids.push($(this).attr('pk'));
      });
      callback(ids);
    }
    else {
      loadDups(mid, callback);
    }
  }
  return false;
}

function setTagForDups(mid, tid, value) {
  if ($('#msg-' + mid + ' .dup-link a').attr('href') > 0) {
    var callback = function (ids) {
      var tag = $('.message .tag[pk="' + tid + '"]', $('#msg-' + mid + 'dups'));
      $.postJSON(ajaxURL_SetTag, {order_id: id, tag_id: tid, id: ids.join(','), mas_tags: tags[mid], mas_post_tags: order_tags, tag_value: (value == "1" ? "false" : "true") }, function (data) {
        if (data.status == "ok") {
          if (value == "0") {
            $(".e1 img", tag).attr("src", "img/cb_ck.png");
          }
          else {
            $(".e1 img", tag).attr("src", "img/cb.png");
          }
          tag.attr('ins', (value == "1") ? "0" : "1");
        }
      });
    };

    if ($('#msg-' + mid + 'dups').length > 0) {
      var ids = [];
      $('.message', $('#msg-' + mid + 'dups')).each(function (index) {
        ids.push($(this).attr('pk'));
      });
      callback(ids);
    }
    else {
      loadDups(mid, callback);
    }
  }
  return false;
}

function loadDups(mid, callback) {
  $("#msg-" + mid).after('<div id="msg-' + mid + 'dups"></div>');

  var dup_block = $("#msg-" + mid + 'dups');
  var dup_ids = [];
  var params = getCommentsParams();
  var api_method = ajaxURL_CommentDup;

  params.byparent = mid;
  params.similar_text = 1;
  params.md5 = '';

  $("#progressbar").progressbar("option", "value", 0);
  $(".progress").fadeIn();

  $.postJSON(api_method, params, function (responce) {
    var message, index = 1;
    $.each(responce, function (i, data) {
      if (index != parseInt(i, 10)) return;
      dup_ids.push(data.id);
      message = $("#template").clone();
      message.attr("pk", data.id);
      message.attr("fav", data.fav);
      message.attr("id", "msg-" + data.id);

      $(".order_no", message).text(index);

      $('.dup-link', message).remove();
      $('.dup-count', message).remove();
      $('.msg-l1-elm0', message).remove();
      $('.msg-block', message).addClass('short');

      $(".order_tone", message)
        .css("cursor", "pointer")
        .removeClass('cb1').removeClass('cb2').removeClass('cb3')
        .addClass('cb' + (-data.nastr + 2))
        .click(function (e) {
          $(this).hide();
          $(this).parents(".msg-line").find(".order_tone-changer").show();
          $(this).parents(".msg-line").find(".order_no").hide();
        });

      // Тональность
      $(".order_tone-changer .cb", message).removeClass("selected").css("cursor", "pointer");
      if (data.nastr == 1) $($(".order_tone-changer .positive", message).parent()).addClass("selected");
      else if (data.nastr == 0) $($(".order_tone-changer .neutral", message).parent()).addClass("selected");
      else if (data.nastr == -1) $($(".order_tone-changer .negative", message).parent()).addClass("selected");

      $(".order_tone-changer .cb1 ", message).click(function (e) {
        changeTone($($(this).parents(".message")).attr("pk"), 1);
        e.stopPropagation();
      });
      $(".order_tone-changer .cb2 ", message).click(function (e) {
        changeTone($($(this).parents(".message")).attr("pk"), 0);
        e.stopPropagation();
      });
      $(".order_tone-changer .cb3 ", message).click(function (e) {
        changeTone($($(this).parents(".message")).attr("pk"), -1);
        e.stopPropagation();
      });
      //:~
      if (data.spam == "1") {
        $(message).fadeTo('fast', 0.5);
        $(message).attr("spam", "true");
      }
      else {
        $(message).attr("spam", "false");
      }

      $(".service_ico", message).attr("src", data.img_url);
      $($(".service_ico", message).parent()).attr("href", data.url);

      activateAnswerLink(data, message);

      $(".post_title", message).html('<a href="' + data.url + '" target="_blank">' + data.post + '</a>');
      $(".post_date", message).text(data.time);

      // Элементы управления
      if (data.fav == 1) $($(".order_fav", message).children(':first-child')).attr("src", "img/btn_star.png");
      else if (data.fav == 0) $($(".order_fav", message).children(':first-child')).attr("src", "img/btn_star_0.png");
      $(".order_fav", message).click(function (e) {
        var fav = $($(this).parents(".message")).attr("fav");
        if (fav == 1) fav = 0;
        else fav = 1;
        $.postJSON(ajaxURL_SetFavourite, {id: $($(this).parents(".message")).attr("pk"), order_id: id, value: fav});
        if (fav == 1) {
          $($(this).children()).attr("src", "img/btn_star.png");
        }
        else if (fav == 0) {
          $($(this).children()).attr("src", "img/btn_star_0.png");
        }
        $($(this).parents(".message")).attr("fav", fav);
        return false;
      });

      $(".order_spam", message).click(function (e) {

        $.postJSON(ajaxURL_SetSpam, {id: $($(this).parents(".message")).attr("pk"), type: "post", order_id: id, value: $($(this).parents(".message")).attr("spam")});
        if ($($(this).parents(".message")).attr("spam") == "true") {
          $($(this).parents(".message")).attr("spam", "false");
          $($(this).parents(".message")).fadeTo('fast', 1.0);
        }
        else {
          $($(this).parents(".message")).attr("spam", "true");
          $($(this).parents(".message")).fadeTo('fast', 0.5);
        }
        return false;
      });

      $(".full_text", message).hide().attr("loaded", "false");
      $(".order_open", message).click(function (e) {
        var message = $($(this).parents(".message"));
        if ($(".full_text", message).attr("loaded") == "false") {
          $(".full_text", message).toggle();
          $(".full_text", message).html('<img src="/img/tonloader.gif" />');
          $.postJSON(ajaxURL_GetFullText, { id: $($(this).parents(".message")).attr("pk"), order_id: id }, function (data) {
            $(".full_text", message).html(data.full_content);
            if(data.reaction){
              var react_text = '';
              for(var z=0; z<data.reaction.length; z++){
              react_text += '<div class="answer" style="display: block;"><img class="avatar" src="'+data.reaction[z].reaction_blog_ico+'"/><div class="name"> '+data.reaction[z].reaction_blog_nick+'</div><div class="text">'+data.reaction[z].reaction_content+'</div></div>';

                //react_text += '<div class="answer"><img class="avatar" src="'+data.reaction[z].+'"/><div class="name">'+data.reaction.+'</div><div class="text">'+data.reaction_content+'</div></div>';
              }
            }
            react_text +="";
            $(".full_text", message).after(react_text);
            $(".full_text", message).after();
            $(".full_text", message).attr("loaded", "true");

          });
        } else if ($(".full_text", message).attr("loaded") == "true") {
          $(".full_text", message).toggle();
        }
        $(".answer", message).toggle();
        return false;
      });

      showReaction(data.reaction, message);

      // Подвал
      if (data.gender == "1")     $(".gender", message).attr("src", "img/ico_person2.png");// Женский пол
      else if (data.gender == "2")     $(".gender", message).attr("src", "img/ico_person.png");    // Мужской пол
      else                             $(".gender", message).attr("src", "img/ico_person3.png");

      $(".nick", message).attr("href", data.auth_url);

      if ((data.nick != null) && (data.nick != '')) {
        $(".nick", message).text(data.nick.substr(0, 12)).attr("target", "_blank");
        $(".order_spam_author", message).html('<img src="img/btn_x.png"/>');
      }
      else {
        $(".nick", message).text('');
        $(".order_spam_author", message).html('');
      }

      $(".order_spam_author", message).click(function (e) {
        $.postJSON(ajaxURL_SetSpam, {id: $($(this).parents(".message")).attr("pk"), type: "author", order_id: id, value: $($(this).parents(".message")).attr("spam")});

        var nickname = data.nick.substr(0, 12);
        $.each($('.nick'), function (e) {
          if ($(this).text() == nickname) {
            //alert('equal');
            if ($($(this).parents(".message")).attr("spam") == "true") {
              $($(this).parents(".message")).attr("spam", "false");
              $($(this).parents(".message")).fadeTo('fast', 1.0);
            }
            else {
              $($(this).parents(".message")).attr("spam", "true");
              $($(this).parents(".message")).fadeTo('fast', 0.5);
            }
          }
        });
        return false;
      });

      // Количество сообщений пользователя
      if (data.count_user != undefined && parseInt(data.count_user, 10) > 0) {
        $(".count_user img", message).css("opacity", 1);
        $(".count_user span", message).removeClass("empty").text(data.count_user);
      } else {
        $(".count_user img", message).css({opacity: 0.3, zIndex: 1});
        $(".count_user span", message).addClass("empty").html("&#8212;");
      }

      // Количество лайков
      if (data.likes != undefined && parseInt(data.likes, 10) > 0) {
        $(".likes img", message).css("opacity", 1);
        $(".likes span", message).removeClass("empty").text(data.likes);
      } else {
        $(".likes img", message).css({opacity: 0.3, zIndex: 1});
        $(".likes span", message).addClass("empty").html("&#8212;");
      }

      // Количество друзей
      if (data.foll != undefined && parseInt(data.foll, 10) > 0) {
        $(".foll img", message).css("opacity", 1);
        $(".foll span", message).removeClass("empty").text(data.foll);
      } else {
        $(".foll img", message).css({opacity: 0.3, zIndex: 1});
        $(".foll span", message).addClass("empty").html("&#8212;");
      }

      // Вовлеченность
      if (data.eng != undefined && parseInt(data.eng, 10) > 0) {
        $(".eng img", message).css("opacity", 1);
        $(".eng span", message).removeClass("empty").text(data.eng);
      } else {
        $(".eng img", message).css({opacity: 0.3, zIndex: 1});
        $(".eng span", message).addClass("empty").html("&#8212;");
      }

      // Город
      if (data.geo != undefined) $(".geo", message).removeClass("empty").text(data.geo);
      else $(".geo", message).addClass("empty").html("&#8212;");

      // Теги
      $(".tag_add-label", message).hide();
      $(".tag_add-db", message).hide();

      if (data.tags[0] === undefined) {
        $(".add-tag .template").hide();
      }

      $([$(".tag_add-db", message).get(0), $(".tag_add-label", message).get(0)]).mouseenter(function (e) {
        toggle = false;
        return false;
      });

      $([$(".tag_add-db", message).get(0), $(".tag_add-label", message).get(0)]).mouseleave(function (e) {
        var message = $(this).parents(".message");
        toggle = true;
        setTimeout('if (toggle) $(".tag_add-label").click();', 400);
        return false;
      });

      $(".tag_add a", message).click(function (e) {
        var message = $(this).parents(".message");
        $(".tag_add", message).hide();
        $(".e2_2", message).addClass("hidden");
        $(".e2", message).removeClass("hidden");
        $(".tag_add-label", message).show();
        $(".tag_add-db", message).show();
        $(".tag-panel input", message).val("Новый тег").css("color", "silver").css("font-style", "italic");
        return false;
      });

      $(".tag-panel input", message).click(function (e) {
        if ($(this).val() == "Новый тег") $(this).val("").css("font-style", "normal").css("color", "#414141");
        return false;
      });

      $(".tag-panel input", message).keypress(function (e) {
        if (e.which == 13) {
          if ($(this).val() != '') {
            var message = $(this).parents('.message');
            var message_id = $(this).parents('.message').attr("pk");
            addTag($(".tag-panel input", message).val(), message_id);
            $(this).val('');
          }
        }
      });

      $(".tag_add-label", message).click(function (e) {
        var message = $(this).parents(".message");
        $(".tag_add", message).show();
        $(".tag_add-label", message).hide();
        $(".tag_add-db", message).fadeOut(100);
        return false;
      });

      // Дропбокс
      tags[data.id] = data.tags;

      $('.add-tag .tag', message).remove();

      if (order_tags !== null && order_tags !== undefined) {
        var tag_count = 0;
        $.each(order_tags, function (id, name) {
          if( tag_count == tags_per_block )
          {
            $(".add-tag", message).css({left: '540px', width: '400px'});
            $(".message-tag-container", message).after('<div class="message-tag-container" style="border-left: 1px solid #CECECE;"></div>');
          }
          tag_count++;
          $(".add-tag template", message).hide();
          var tag = $(".add-tag .template", message).clone(),
            checked = false;
          $(".add-tag .template", message).hide();
          $.each(data.tags, function (sid, foo) {
            if (sid == id) checked = true;
          });
          if (checked) {
            $(".e1 img", tag).attr("src", "img/cb_ck.png");
          }
          else {
            $(".e1 img", tag).attr("src", "img/cb.png");
          }
          tag.addClass('tag').removeClass("template").attr("pk", id).attr('ins', (checked ? "1" : "0"));

          $(".e1 a", tag).click(function (e) {
            var parent = $(this).parents('.tag'),
              message = $(this).parents('.message');
            setTag(message.attr('pk'), parent.attr('pk'), parent.attr('ins'));
            return false;
          });

          $(".e2", tag).text(name);
          $(".e2_2 input", tag).val(name);

          $(".e2", tag).click(function (e) {
            var alltags = $(this).closest(".add-tag");
            $(".e2_2", alltags).addClass("hidden");
            $(".e2", alltags).removeClass("hidden");
            $(this).closest(".line").children(".e2_2").removeClass("hidden").children("input").focus().select();
            $(this).addClass("hidden");
          });

          $("input", tag).focusout(function (e) {
            $(".e2_2", tag).addClass("hidden");
            $(".e2", tag).removeClass("hidden");
            $(".e2", tag).text($(".e2_2 input", tag).val());
          });

          $("input", tag).keypress(function (e) {
            if (e.which == 13) {
              $(".e2_2", tag).addClass("hidden");
              $(".e2", tag).removeClass("hidden");
              $(".e2", tag).text($(".e2_2 input", tag).val());
              editTag(tag.attr('pk'), $(".e2_2 input", tag).val());
            }
          });

          $(".e3 a", tag).click(function (e) {
            var parent = $(this).parents('.tag'),
              message = $(this).parents('.message');
            delTag(message.attr('pk'), parent.attr('pk'));
            return false;
          });

          tag.css("display", "block");
          message.find(".message-tag-container").last().append(tag);
        });
      }

      $(".tag-panel .btn a", message).click(function (e) { //добавление тега!
        var message = $(this).parents('.message');
        var message_id = $(this).parents('.message').attr("pk");
        addTag($(".tag-panel input", message).val(), message_id);
        $(".tag-panel input", message).val("");
        return false;
      });

      dup_block.append(message);
      message.show();

      index++;

      $(".service_ico").attr("title", 'Ресурс');
      $(".order_tone").attr('title', 'Простановка тональности.');
      $(".order_spam_author").attr('title', 'Удалить автора из выдачи.');
      $(".foll").attr('title', 'Охват упоминания.');
      $(".eng").attr('title', 'Вовлеченность.');
      $(".post_date").attr('title', 'Дата и время упоминания.');
      $(".gender").attr('title', 'Пол автора.');
      $(".nick").attr('title', 'Ник автора.');
      $(".order_fav").attr('title', 'Добавить в избранное.');
      $(".order_open").attr('title', 'Показать полный текст упоминания.');
      $(".order_spam").attr('title', 'Удалить упоминание.');
    });

    $("#progressbar").progressbar("option", "value", 100);
    $(".progress").fadeOut(200);
    if (callback !== undefined) {
      callback(dup_ids);
      $("#msg-" + mid + 'dups').hide();
    }
    else {
      $("#MM").height($("#ML").height());
    }
  });
}

function initSort() {
  $('#filter-sort option[value="dup"]').remove();
  if ($.cookie(id + 'mm_dub') == 1 || $.cookie(id + 'mm_dub') == undefined || $.cookie(id + 'mm_dub') == null) {
    $('#filter-sort').append('<option value="dup">по перепечаткам</option>');
  }
  $('#filter-sort option').removeAttr('selected');
  var toCheck = $('#filter-sort option[value="' + $.cookie(id + "-sort-msg") + '"]');
  if (toCheck.length) {
    toCheck.attr('selected', 'selected');
  }
  else {
    $('#filter-sort option').eq(0).attr('selected', 'selected');
  }
  $('#tdd-filter-sort').remove();
  createDropDown("filter-sort", 74);
  $("#tdd-filter-sort").unbind("change").change(function (e) {
    filterChangeFlag();
    $.cookie(id + "-sort-msg", $(this).attr("value"));
  });
}

function windowScrollSubscriber()
{
  //движение верхней панели
  var pos_x = 80 - $(document).scrollTop();
  var pos_y = $(document).scrollLeft();
  if (pos_x >= 0) {
    $("#M1").css("top", pos_x + "px");
  }
  else {
    $("#M1").css("top", "0");
  }
  $("#M1").css("left", (0 - pos_y) + "px");

  //движение панели групповых действий
  pos_x = 129 - $(document).scrollTop();
  if (pos_x >= 0) {
    $('#group-actions').css({top: pos_x + 100 + "px"});
  }
  else {
    $('#group-actions').css({top: '100px'});
  }

  //движение кнопки возврата вверх
  var aditional_height = 0;
  $('#mm-items .flist:visible').each(function(){
    aditional_height += $(this).innerHeight() + 40;
  });

  pos_x = 129 - $(document).scrollTop() + aditional_height;
  if (pos_x >= 0) {
    $('#top-button').fadeOut();
  }
  else {
    $('#top-button').fadeIn();
  }
}

function clearFilter()
{
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

var updateSocialAccounts = function(block)
{
  if( block.hasClass('twitter-message') )
  {
    updateTwitterAccounts();
  }
  if( block.hasClass('facebook-message') )
  {
    updateFacebookAccounts();
  }
  if( block.hasClass('vk-message') )
  {
    updateVkAccounts();
  }
};

var activateAnswerLink = function(data, message)
{
  if( data.host == 'twitter' )
  {
    $('.answer_message', message).addClass('twitter-message');
    $('.answer_message .add-account', message).addClass('addTwitterAccount');
    $('.answer_message .logout-account', message).addClass('logoutTwitterAccount');
    $('.answer_message .answer-button', message).addClass('answerTwitter').data('message_id', data.id);
    $('.answer_message input[name="message_link"]', message).val(data.url);
    $('.answer_message .group-checkbox', message).remove();
  }
  else if( data.host == 'facebook' )
  {
    $('.answer_message', message).addClass('facebook-message');
    $('.answer_message .add-account', message).addClass('addFacebookAccount');
    $('.answer_message .logout-account', message).addClass('logoutFacebookAccount');
    $('.answer_message .answer-button', message).addClass('answerFacebook').data('message_id', data.id);
    $('.answer_message input[name="message_link"]', message).val(data.url);
    $('.answer_message .group-checkbox', message).remove();
//    $('.answer_message .group-checkbox input', message).attr('id', 'from_group_'+data.id);
//    $('.answer_message .group-checkbox label', message).attr('for', 'from_group_'+data.id);
  }
  else if( data.host == 'vk' )
  {
    $('.answer_message', message).addClass('vk-message');
    $('.answer_message .add-account', message).addClass('loginVk');
    $('.answer_message .logout-account', message).addClass('logoutVkAccount');
    $('.answer_message .answer-button', message).addClass('answerVk').data('message_id', data.id);
    $('.answer_message input[name="message_link"]', message).val(data.url);
    $('.answer_message .group-checkbox input', message).attr('id', 'from_group_'+data.id);
    $('.answer_message .group-checkbox label', message).attr('for', 'from_group_'+data.id);
  }
  else
  {
    $(".answer_message", message).remove();
  }
};

var showReaction = function(reaction, message)
{
  if( user_reaction == 1 )
  {
    if( reaction.reaction_content != null )
    {
      $('.answer .avatar', message).attr('src', reaction.reaction_blog_info.reaction_blog_ico);
      $('.answer .name', message).html(reaction.reaction_blog_info.reaction_blog_nick+'<span>('+reaction.reaction_time+')</span>');
      $('.answer .text', message).html(reaction.reaction_content);
      //$('.answer_message a', message).addClass('disabled');
    }
    else
    {
      $('.answer', message).remove();
    }
  }
  else if( user_reaction == 2 )
  {
    $('.answer_message', message).remove();
  }
  else
  {
    $('.answer_message a', message).addClass('disabled');
  }
};

function syncDataWithCRM(order_id){
  //console.log(order_id);
  $.postJSON(ajaxURL_sync, {order_id: order_id}, function (data) {
      if(data.status=="ok"){
        $("#resultOfSync").text("Синхронизация запущена.");
      } else {
        $("#resultOfSync").text("В процессе синхронизации произошла ошибка.");
      }
      $("#dialog-message-sync").dialog({
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
  });
}
