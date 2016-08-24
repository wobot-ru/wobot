/*
 Открывает модельное окно попапа
 */
var tarifs = {16: 'Стартовый', 15: 'Базовый', 14: 'Профессиональный', 12: 'Корпоративный', 13: 'Корпоративный'};
var av_order;
var is_addorder;
var exp;
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

/*Ошибки*/
var myMessages = ['error_info', 'error_warning', 'error_error', 'error_success'];

function hideAllMessages() {
  var messagesHeights = new Array(); // this array will store height for each

  for (var i = 0; i < myMessages.length; i++) {
    messagesHeights[i] = $('.' + myMessages[i]).outerHeight(); // fill array
    $('.' + myMessages[i]).css('top', -messagesHeights[i] - 2); //move element outside viewport
  }
}
function showMessage(type) {
  hideAllMessages();
  $('.' + type).animate({top: "0"}, 500);
  setTimeout(function () {
    hideAllMessages();
  }, 8000);
}

function dateToWords(inDate) {
  var date = inDate.split(".");
  var month = "";
  switch (parseInt(date[1], 10)) {
    case 1 :
      month = "янв";
      break;
    case 2 :
      month = "фев";
      break;
      break;
    case 3 :
      month = "мар";
      break;
    case 4 :
      month = "апр";
      break;
    case 5 :
      month = "мая";
      break;
    case 6 :
      month = "июн";
      break;
    case 7 :
      month = "июл";
      break;
    case 8 :
      month = "авг";
      break;
    case 9 :
      month = "сен";
      break;
    case 10 :
      month = "окт";
      break;
    case 11:
      month = "ноя";
      break;
    case 12:
      month = "дек";
      break;
  }
  return parseInt(date[0], 10) + " " + month + " '" + parseInt(date[2], 10) % 100;
}

function addTheme(name, keywordOr, keywordAnd, keywordNot, start, end) {
  var status;
  $.postJSON(ajaxURL_AddTheme, {order_name: name, mw: keywordOr, mnw: keywordAnd, mew: keywordNot, order_start: start, order_end: end}, function (data) {

    $("#newThemeButton").attr("disabled", false);
    status = data.status;
    if (status == 21) {
      $("#tip-cont-2").show();
      $("#newTheme-kwrd-tip").text("Некорректно составлен список, проверьте правильность ввода.");
      $("#dialog-newTheme-keywordOr").addClass("ui-state-error");
    }
    else if (status == 22) {
      $("#tip-cont-2").show();
      $("#newTheme-kwrd-tip").text("Некорректно составлен список, проверьте правильность ввода.");
      $("#dialog-newTheme-keywordAnd").addClass("ui-state-error");
    }
    else if (status == 23) {
      $("#tip-cont-2").show();
      $("#newTheme-kwrd-tip").text("Некорректно составлен список, проверьте правильность ввода.");
      $("#dialog-newTheme-keywordNot").addClass("ui-state-error");
    }
    else if (status == 3) {
      $("#tip-cont-2").show();
      $("#newTheme-kwrd-tip").text("Для введенных ключевых слов превышено ограничение тарифа на количество сообщений.");
      $("#dialog-newTheme-keywordAnd").addClass("ui-state-error");
    }
    else if (status == 4) {
      $("#tip-cont-2").show();
      $("#newTheme-kwrd-tip").text("Невозможно добавить тему. Превышено количество тем для тарифа.");
      $("#dialog-newTheme-keywordAnd").addClass("ui-state-error");
    }
    else {
      $("#tip-cont-2").hide();
      $("#dialog-newTheme").dialog('close');

      var tarif_id = $.cookie("tarif_id");

      if (tarif_id == 3) {
        $("#dialog-message-add strong").text('Тема добавлена. Наш менеджер свяжется с вами для подтверждения поискового запроса.');
      }
      $("#dialog-message-add ").dialog('open');
      loadContent();
    }
  });
}

function getResourceList() {
  $.postJSON(ajaxURL_GetResTable, {}, function (data) {
    $("#resTable").empty();
    $.each(data.src, function (key, source) {
      var statusStr;
      if (source.status == 0) statusStr = 'добавлен';
      else if (source.status == 1) statusStr = 'нельзя добавить';
      else if (source.status == 2) statusStr = 'в процессе обработки';
      $("#resTable").append("<tr><td>" + source.link + "</td><td>" + statusStr + "</td></tr>")
    });
  });
  return false;
}

function addResource(url) {
  $.postJSON(ajaxURL_AddResource, {Url: url}, function (data) {
    if (data.status == 'ok') {
      loadContent();
      $("#dialog-newResource").dialog("close");
    }
    else if (data.status == 'fail') {
      $("#add_src_url").addClass("ui-state-error");
      updateTips("Проверьте правильность адреса", "#newUrlTip");
      $("#tip-cont-3").show();
    }
    else if (data.status == 'fail2') {
      $("#add_src_url").addClass("ui-state-error");
      updateTips("Ваш ресурс уже подключен", "#newUrlTip");
      $("#tip-cont-3").show();
    }
  });
}

function dropDateFilters() {
  var cookie_date = document.cookie.split(";");
  var re_fromdate = new RegExp("([0-9]{3,4}-fromDate-theme.*)=.*");
  for (var i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re_fromdate) != -1) {
      $.cookie(cookie_date[i].match(re_fromdate)[1], null);
    }
  }
  var re_todate = new RegExp("([0-9]{3,4}-toDate-theme.*)=.*");
  for (i = 0; i < cookie_date.length; i++) {
    if (cookie_date[i].search(re_todate) != -1) {
      $.cookie(cookie_date[i].match(re_todate)[1], null);
    }
  }
}

function checkLength(o, n, min, max, tip) {
  if (o.val().trim().length > max || o.val().trim().length < min) {
    o.addClass("ui-state-error");
    updateTips(n, tip);
    return false;
  } else {
    return true;
  }
}


function loadContent() {
  $("#progressbar").progressbar("option", "value", 0);
  $(".progress").fadeIn(1);
  sort = $("#tdd-order-by").attr("value");
  if ((sort == null) || (sort == ''))
  {
    sort = 'default';
  }

  $.postJSON(ajaxURL_Orders, {'sort': sort}, function (data) {
    $("#user_email").text(data.user_email);
    $("#progressbar").progressbar("option", "value", 25);

    $("#dyn2").text(data.orders.length);
    $("#popup-exp").text(data.tarif_exp);
    exp = data.tarif_exp;
    if (exp == null)
    {
      exp = 0;
    }
    if (exp == 0)
    {
      exp = "0";
    }
    if (exp == null || exp < 4 || exp == "0" || exp == "Аккаунт заблокирован")
    {
      $("#user_exp").addClass("warn");
    }
    $("#user_exp").text(exp);
    if (exp == "Аккаунт заблокирован") {
      $("#user_email").tipTip({content: 'Нажмите, чтобы пополнить баланс', keepAlive: true, defaultPosition: 'right', activation: 'focus'});
      $("#user_email").focus();
      $("#popup-exp").text('0');
    }

    av_order = data.av_order;

    is_addorder = data.is_addorder;

    $("#opensettings").click(function () {
      $("#tiptip_holder").css("z-index", "999");
      $("#dialog-Settings").dialog("open");
    });

    if (av_order == 0) {
      $(".btn-theme").css('opacity', '0.2');
      $(".btn-theme a").attr('href', '#');
      $(".btn-theme").tipTip({content: 'Исчерпан лимит тем для Вашего кабинета. <br>Чтобы добавить новую тему, удалите любую<br> тему, либо перейдите на тариф <a href="#" onclick="$(\'#dialog-Settings\').dialog(\'open\');  return false;" id="opensettings">' + tarifs[$.cookie("tarif_id")] + '</a> ', keepAlive: true});
    }
    else {
      $(".btn-theme").tipTip({content: 'Добавить новую тему.<br><a href="http://www.wobot.ru/faq#1_5" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
    }
    $("#user_email").unbind("click").click(function (e) {/* заглушка для popup-а */
      return false;
    });

    $("#exit").attr("href", inernalURL_logout);
    $("#access").attr("href", inernalURL_accessSetup);

    $("#user_tariff").text(data.user_tarif);
    $("#popup-tarif").text(data.user_tarif);
    $("#popup-user").text(data.user_email);
    $("#popup-money").html(data.user_money);

    $("#user_tariff").attr("href", inernalURL_tariff + data.tarif_id);
    $("#user_tariff").unbind("click").click(function (e) {
      loadmodal(inernalURL_tariff + data.tarif_id, 300, 400, "iframe");
      return false;
    });

    $("#user_money").html(data.user_money + "&nbsp;<span class=\"rur\">p<span></span></span>");

    $("#billing").attr("href", inernalURL_billing + "?user_id=" + data.user_id);
    $("#billing").unbind("click").click(function (e) {
      loadmodal(inernalURL_billing + "?user_id=" + data.user_id, "50", "100%", "iframe");
      return false;
    });

    $("#user_consultant").text(data.user_consultant);
    /* НЕ ОПРЕДЕЛЕНО ПО ТЗ */
    $("#user_consultant").attr("href", "http://reformal.ru/widget/58144");
    $("#user_consultant").unbind("click").click(function (e) {
      loadmodal("http://reformal.ru/widget/58144", "75%", "75%", "iframe");
      return false;
    });
    /*:~ */

    $("#faq").attr("href", inernalURL_faq);

    var ready = 0;

    var showAll = true;

    var ids = $.cookie("compare_ids");
    if (ids == undefined || ids == null || ids == "") ids = [];
    else ids = ids.split(",");

    if (data.user_id == null) {
      window.location.replace("/");
    }

    $.cookie("user_id", data.user_id);
    $.cookie("user_email", data.user_email);
    $.cookie("user_exp", data.tarif_exp);
    $.cookie("user_tarif", data.user_tarif);
    $.cookie("tarif_id", data.tarif_id);
    $.cookie("user_money", data.user_money);

    $("#billingItem").val(data.user_id);

    //тем пока нет
    if (data.orders.length == 0) {
      $("#progressbar").progressbar("option", "value", 100);
      $(".progress").fadeOut(1000);
    }

    $(".FL[real='real']").remove();
    $.each(data.orders, function (key, order) {
      // Подготовка
      var theme = null;

      if (order.ready == false)
      {
        theme = $("#theme-notready").clone();
      }
      else if ((order.ready_perc == 30) || (order.ready_perc == 50) || (order.ready_perc == 70)) {
        theme = $("#theme-ready").clone();
        $(".order-image", theme).remove();
        $(".s", theme).remove();
        $(theme).attr("completed", "0");
      }
      else {
        ready++;
        theme = $("#theme-ready").clone();
      }

      $(theme).css("display", "block");

      $("#progressbar").progressbar("option", "value", 50);

      // Заполнение полей

      $(".order-image", theme).attr("src", imgURL_themesGraph.replace('%order_id%', order.id));

      $(".rss", theme).attr("href", imgURL_themesRSS.replace('%order_id%', order.id));
      $(".rss", theme).click(function (e) {
        e.stopPropagation();
      });

      $(".edittheme", theme).attr("href", "theme_edit.html#" + order.id);
      $(".edittheme", theme).click(function (e) {
        e.stopPropagation();
      });

      $(".deletetheme", theme).attr("href", order.id);
      $(".deletetheme", theme).click(function () {
        var currThemeId = $(this).attr("href");
        var currThemeName = $(".order-keyword", theme).text();
        if (confirm('Вы уверены, что хотите безвозвратно удалить тему "' + currThemeName + '" из кабинета?')) {
          $.postJSON(ajaxURL_orderRemove, {order_id: currThemeId}, function (data) {
            if (data.status == "ok") {
              $("#" + currThemeId).remove();
              $(".error_success h3").text("Удаление темы");
              $(".error_success p").text("Ваша тема успешно удалена");
              showMessage(myMessages[3]);
            } else {
              $(".error_error h3").text("Удаление темы");
              $(".error_error p").text("При удалении темы возникла неизвестная ошибка");
              showMessage(myMessages[2]);
            }
          });
          return false;
        } else {
          return false;
        }
      });

      $(theme).attr("id", order.id);
      $(theme).attr("real", "real");

      $(theme).find(".order-keyword").attr("href", inernalURL_themePage + order.id);

      $(".order-href", theme).css("cursor", "pointer").click(function (e) {
        window.location.href = inernalURL_themePage + order.id;
      });

      $(theme).find(".order-keyword").html(order.keyword);

      $(theme).find(".order-dates").html(dateToWords(order.start) + ' &#8211; <br/>' + dateToWords(order.end));
      $(theme).find(".order-dates").attr('title', 'Период мониторинга');

      $(theme).find(".popup").each(function (index, element) {
        $(this).attr("id", index + "-" + order.id);
      });

      $("#progressbar").progressbar("option", "value", 75);	//$(theme).find(".r0").css("height",$(theme).find(".order-keyword").height() + 30);

      var delta = "+ 0";

      if (order.ready != false) {

        $(theme).find(".order-posts").text(order.posts+'/'+data.tariff_posts/1000+'k');
        if( ( data.tariff_posts - order.posts ) <= 500 )
        {
          $(theme).find(".order-posts").css({color: '#de4343'});
        }
        else if( parseInt(order.posts) != order.posts && (data.tariff_posts / 1000 - parseInt(order.posts)) <= 1 )
        {
          $(theme).find(".order-posts").css({color: '#de4343'});
        }
        $(theme).find(".order-posts").tipTip({content: 'По вашей теме доступно '+order.posts+' '+declOfNum(order.posts, ['последнее сообщение','последних сообщения','последних сообщений'])+' из '+data.tariff_posts/1000+'k доступных по лимиту.<br/> Вся статистика по теме будет только по доступным сообщениям!'});

        $(theme).find(".order-posts-delta").removeClass("pls");
        $(theme).find(".order-posts-delta").removeClass("mns");
        if (parseInt(order.din_posts, 10) < 0)
        {
          $(theme).find(".order-posts-delta").addClass("mns");
        }
        else if (parseInt(order.din_posts, 10) >= 0)
        {
          $(theme).find(".order-posts-delta").addClass("pls");
        }
        $(theme).find(".order-posts-delta").text(order.din_posts);

        $(theme).find(".order-src").text(order.src);

        $(theme).find(".order-src-delta").removeClass("pls");
        $(theme).find(".order-src-delta").removeClass("mns");
        if (parseInt(order.din_src, 10) < 0)
        {
          $(theme).find(".order-src-delta").addClass("mns");
        }
        else if (parseInt(order.din_src, 10) >= 0)
        {
          $(theme).find(".order-src-delta").addClass("pls");
        }
        $(theme).find(".order-src-delta").text(order.din_src);
        $(theme).find(".order-value").text(order.value);
        $(theme).find(".order-value-delta").removeClass("pls");
        $(theme).find(".order-value-delta").removeClass("mns");
        if (parseInt(order.div_value, 10) < 0)
        {
          $(theme).find(".order-value-delta").addClass("mns");
        }
        else if (parseInt(order.div_value, 10) >= 0)
        {
          $(theme).find(".order-value-delta").addClass("pls");
        }
        $(theme).find(".order-value-delta").text(order.div_value);
        $(theme).find(".order-ready").text(dateToWords(order.ready));
        //процент сбора темы
        if (order.ready_perc < 100) {
          $(theme).find(".rdy").html("<span style='color: #E4C704 !important;'>Готова: " + order.ready_perc + "%</span>");
        }
        else {
          $(theme).find(".rdy").html("<span>Готова</span>");
        }
        if ($.inArray(order.id + "", ids) == -1) {
          $(theme).find(".checkbox").attr("value", "0");
          $(theme).find(".checkbox .lr2").css("opacity", "0");
          if (ids.length < 2) {
            $(theme).find(".checkbox .lr0").css("display", "none");
          }
          else {
            $(theme).find(".checkbox .lr0").css("display", "none");
          }
        } else {
          $(theme).find(".checkbox").attr("value", "1");
          $(theme).find(".checkbox .lr2").css("opacity", "1");
          if (ids.length > 1) $(theme).find(".checkbox .lr0").css("display", "block");
        }
      } else {
        if (showAll)
          $(theme).css("display", "block");
        else
          $(theme).css("display", "none");
      }
      $("#body").append(theme);

      $(".order-value").tipTip({content: 'Потенциальный охват аудитории. <br><a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
      $(".order-src").tipTip({content: 'Количество ресурсов, на которых были найдены упоминания.'});
//      $(".order-posts").tipTip({content: 'Количество и динамика упоминаний по теме.'});
      $(".order-dates").tipTip();
      $(".rss").tipTip({content: 'RSS - Нажмите, если вы хотите получать краткий отчет по теме на e-mail ежедневно'});
      $(".edittheme").tipTip({content: 'Настройки темы.'});
      $(".deletetheme").tipTip({content: 'Удаление темы.'});
      $(".order-image").tipTip({content: 'Динамика упоминаний за последние 5 дней.'});

      $("#user_email").tipTip({content: 'Настройки пользователя', defaultPosition: "right"});
      $("#user_exp").tipTip({content: 'Для продления кабинета необходима оплата тарифа', defaultPosition: "bottom"});

      $("#progressbar").progressbar("option", "value", 100);
      $(".progress").fadeOut(1000);
    });
    $("#themes-ready").text(ready);

    //for settings
    makeDialog(data.user_tarif, data.user_email, data.tarif_exp);
  });
}

function updateTips(t, tip) {
  $(tip).text(t);
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
  cookie_word = document.cookie.split(";");
  re_word = new RegExp("([0-9]{3,4}-tag_.*)=.*");
  for (i = 0; i < cookie_word.length; i++) {
    if (cookie_word[i].search(re_word) != -1) {
      $.cookie(cookie_word[i].match(re_word)[1], null);
    }
  }
}

$(document).ready(function () {
  hideAllMessages();
  dropDateFilters();
  clearFilters();
  $("#progressbar").progressbar({
    value: 0
  });


  var newThemeName = $("#dialog-newTheme-name"),
    newThemeDate1 = $("#dialog-newTheme-datepicker1"),
    newThemeDate2 = $("#dialog-newTheme-datepicker2"),
    keywordOr = $("#dialog-newTheme-keywordOr"),
    keywordAnd = $("#dialog-newTheme-keywordAnd"),
    keywordNot = $("#dialog-newTheme-keywordNot");

  var allTips = $([]).add($("#newTheme-name-tip"))
    .add($("#newTheme-date-tip"))
    .add($("#newTheme-kwrd-tip"))
    .add($("#newTheme-kwrd2-tip"))
    .add($("#newTheme-kwrd3-tip"));

  var allFields = $([]).add(newThemeName)
    .add(newThemeDate1)
    .add(newThemeDate2)
    .add(keywordOr)
    .add(keywordAnd)
    .add(keywordNot);

  $('#scrollbar1').tinyscrollbar();

  function checkRegexp(o, regexp, n, tip) {
    var test = o.val().match(regexp);
    if (test != null) {
      o.addClass("ui-state-error");
      updateTips(n, tip);
      return false;
    } else {
      return true;
    }
  }

  function isValidDate(controlName, format) {
    var isValid = true;

    try {
      jQuery.datepicker.parseDate(format, jQuery('#' + controlName).val(), null);

    }
    catch (error) {

      isValid = false;
    }

    return isValid;
  }

  function isValidURL(url) {
    return /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/.test(url);
  }

  createDropDown("order-by");

  $('#tdd-order-by').change(function () {
    loadContent();
  });

  $("#logo a").attr("href", inernalURL_themesList);
  $(".btn-all")
    .css("cursor", "pointer")
    .click(function () {
      $(".btn-ready").removeClass("selected");
      $(".btn-all").addClass("selected");
      $(".FL[real='real']").fadeIn(200);
    });


  $(".btn-ready")
    .css("cursor", "pointer")
    .click(function () {
      $(".btn-all").removeClass("selected");
      $(".btn-ready").addClass("selected");
      $(".FL[completed='0']").fadeOut(200);
    });

  $("#yourRes")
    .click(function () {
      $("#scrollbar1").slideToggle('slow');
      $('#scrollbar1').tinyscrollbar_update();

    });


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
    maxWidth: 400,
    autoOpen: false
  });


  $("#dialog-newTheme").dialog({

    modal: true,
    open: function (event, ui) {
      var tarif_id = $.cookie("tarif_id");
      $("#dialog-newTheme-datepicker1").datepicker({dateFormat: "dd.mm.yy", changeYear: true});
      $("#dialog-newTheme-datepicker2").datepicker({dateFormat: "dd.mm.yy", changeYear: true});
      if (tarif_id == 5) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-1m").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 6) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-2m").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 7) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-3m").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 15) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-1m").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 14) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-2m").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 13) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-3m").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 12) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "01.01.2012").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 3) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-2w").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else if (tarif_id == 16) {
        $("#dialog-newTheme-datepicker1").datepicker("setDate", "-2w").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
        $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
      }
      else
      {
        var addThemeFlag = 1;
      }

      $("#tip-cont-1").hide();
      $("#tip-cont-2").hide();

      if (av_order < 1) {
        $("#dialog-newTheme-hide").hide();
        $("#dialog-newTheme-message").show();
        $(":button:contains('Добавить')").attr("disabled", true).addClass("ui-state-disabled");
      }
      else if (is_addorder == 0) {
        $("#dialog-newTheme-message2").show();
        $(":button:contains('Добавить')").attr("disabled", true).addClass("ui-state-disabled");
      }
      else if (addThemeFlag == 1) {
        $("#dialog-newTheme-message3").show();
        $(":button:contains('Добавить')").attr("disabled", true).addClass("ui-state-disabled");
      }
      else {
        $("#dialog-newTheme-message").hide();
        $("#dialog-newTheme-message2").hide();
        $("#dialog-newTheme-message3").hide();
        $("#dialog-newTheme-hide").show();
        $("#dialog-newTheme").dialog("option", "position", "center");
      }
    },
    close: function (event, ui) {
      $(":button:contains('Добавить')").attr("disabled", false).removeClass("ui-state-disabled");
      $("#reset")[0].reset();
      allFields.removeClass("ui-state-error");
      allTips.text('');
    },
    buttons: {
      "Добавить": {
        id: "newThemeButton",
        text: "Добавить",
        click: function () {
          var bValid1 = true;
          var bValid2 = true;
          allFields.removeClass("ui-state-error");
          allTips.text('');
          $("#tip-cont-1").hide();
          $("#tip-cont-2").hide();

          bValid1 = bValid1 && checkLength(newThemeName, "Длина названия должна быть от 3 до 50 символов.", 3, 50, "#newTheme-name-tip");

          if (!isValidDate("dialog-newTheme-datepicker1", "dd.mm.yy")) {
            newThemeDate1.addClass("ui-state-error");
            updateTips("Неверный формат даты.", "#newTheme-name-tip");
            bValid1 = false;
          }
          if (!isValidDate("dialog-newTheme-datepicker2", "dd.mm.yy")) {
            newThemeDate2.addClass("ui-state-error");
            updateTips("Неверный формат даты.", "#newTheme-name-tip");
            bValid1 = false;
          }
          if ($("#dialog-newTheme-datepicker1").datepicker("getDate") > $("#dialog-newTheme-datepicker2").datepicker("getDate")) {
            updateTips("Окончание периода должно быть после или в день начала.", "#newTheme-name-tip");
            newThemeDate1.addClass("ui-state-error");
            newThemeDate2.addClass("ui-state-error");
            bValid1 = false;
          }

          if (bValid1) $("#tip-cont-1").hide(); else $("#tip-cont-1").show();

          bValid2 = bValid2 && checkLength(keywordOr, "Длина слова должна быть от 3 символов.", 3, 1000, "#newTheme-kwrd-tip");
          bValid2 = bValid2 && checkRegexp(keywordOr, /(,{1}|^)([A-Za-zА-Яа-я0-9 ]*"{1}[A-Za-zА-Яа-я0-9-&. ]*(,{1}|$))/, "Некорректно составлен список слов, проверьте правильность ввода.", "#newTheme-kwrd-tip");

          if (keywordAnd.val().length > 0) {
            bValid2 = bValid2 && checkLength(keywordAnd, "Длина слова должна быть от 3 символов.", 3, 1000, "#newTheme-kwrd-tip");
            bValid2 = bValid2 && checkRegexp(keywordAnd, /(,{1}|^)([A-Za-zА-Яа-я0-9 ]*"{1}[A-Za-zА-Яа-я0-9-&. ]*(,{1}|$))/, "Некорректно составлен список обязательных слов, проверьте правильность ввода.", "#newTheme-kwrd-tip");
          }
          if (keywordNot.val().length > 0) {
            bValid2 = bValid2 && checkLength(keywordNot, "Длина слова должна быть от 3 символов.", 3, 1000, "#newTheme-kwrd-tip");
            bValid2 = bValid2 && checkRegexp(keywordNot, /(,{1}|^)([A-Za-zА-Яа-я0-9 ]*"{1}[A-Za-zА-Яа-я0-9-&. ]*(,{1}|$))/, "Некорректно составлен список стоп-слов, проверьте правильность ввода.", "#newTheme-kwrd-tip");
          }
//"
          if (!bValid2) $("#tip-cont-2").show();

          if (bValid1 && bValid2) {
            $("#newThemeButton").attr("disabled", true);
            var status = addTheme(
              $("#dialog-newTheme-name").val(),
              $("#dialog-newTheme-keywordOr").val(),
              $("#dialog-newTheme-keywordAnd").val(),
              $("#dialog-newTheme-keywordNot").val(),
              $("#dialog-newTheme-datepicker1").val(),
              $("#dialog-newTheme-datepicker2").val()
            );
          }
        }},
      "Отменить": function () {
        $("#reset")[0].reset();
        $(this).dialog("close");
      }
    },

    draggable: false,
    resizable: false,
    minWidth: 700,
    maxWidth: 700,
    autoOpen: false
  });


  $("#dialog-newResource").dialog({
    modal: true,
    open: function () {
      getResourceList();
      $('#scrollbar1').tinyscrollbar_update();
      $("#tip-cont-3").hide();
      $('#scrollbar1').show();
      $("#dialog-newResource").dialog("option", "position", "center");
      $('#scrollbar1').hide();
    },
    close: function () {
      $("#add_src_url").val('');
      $("#add_src_url").removeClass("ui-state-error");
      $("#newUrlTip").text('');
    },
    buttons: {
      "Добавить": function () {
        $("#tip-cont-3").hide();
        addResource($("#add_src_url").val());
      },
      "Отменить": function () {
        $(this).dialog("close");
      }
    },
    draggable: false,
    resizable: false,
    minWidth: 500,
    maxWidth: 500,
    autoOpen: false
  });

  $(".sort .btn a").click(function () {
    $(".dropdown dd ul").toggle();
    return false;
  });

  loadContent();

  var notices = new Array('mainNotice', 'newresNotice');
  showNotices(notices);
});

function mainNotice() {
  if ($.cookie("mainNoticeNot") != 1) {
    var mainNoticeId = $.gritter.add({
      title: 'Вы находитесь на главной странице вашего кабинета системы Wobot.',
      text: 'Здесь представлены ваши темы, по которым осуществляется мониторинг, а также основная статистика по ним.<br>\
			<a href="#" id="mainNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
      sticky: true,
      time: '',
      class_name: 'my-sticky-class',
      after_close: function (e) { }
    });
    $('#mainNoticeNot').click(function () {
      $.cookie("mainNoticeNot", 1);
      $.gritter.remove(mainNoticeId);
      hideNotice("mainNotice", 1);
      return false;
    });
  }
}

function newresNotice() {
//  if ($.cookie("newresNoticeNot") != 1) {
//    var newresNoticeId = $.gritter.add({
//      title: 'Добавление новой темы',
//      text: 'Вы можете добавить новую тему для мониторинга или подключить новый ресурс (форум, блог и т.д.), если его нет в нашей базе.<br>' +
//			  'Подробнее о том, что это такое и как добавление ресурсов улучшает качество мониторинга, вы можете прочитать <a href="http://www.wobot.ru/faq#1_9" class="a-dotted">здесь</a>.<br>' +
//			  '<a href="#" id="newresNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
//      sticky: true,
//      time: '',
//      class_name: 'my-sticky-class',
//      after_close: function (e) { }
//    });
//    $('#newresNoticeNot').click(function () {
//      $.cookie("newresNoticeNot", 1);
//      $.gritter.remove(newresNoticeId);
//      hideNotice("newresNotice", 1);
//      return false;
//    });
//  }
}

function compareNotice() {
  if ($.cookie("compareNoticeNot") != 1) {
    var compareNoticeId = $.gritter.add({
      title: 'Сравнение тем',
      text: 'Выбрав несколько тем, вы можете сравнить данные по ним. Для этого отметьте галочкой интересующие вас темы и перейдите по ссылке сравнить.<br>\
			<a href="#" id="compareNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
      sticky: true,
      time: '',
      class_name: 'my-sticky-class',
      after_close: function (e) { }
    });
    $('#compareNoticeNot').click(function () {
      $.cookie("compareNoticeNot", 1);
      $.gritter.remove(compareNoticeId);
      hideNotice("compareNotice", 1);
      return false;
    });
  }
}