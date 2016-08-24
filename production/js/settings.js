var tariff_id;

function updateTips(t, tip) {
  $(tip).text(t);
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

function showNotices(notices) {
  $.postJSON(ajaxURL_GetSettings, {}, function (data) {
    $.cookie("tariff_posts", data.tariff_posts);
    $.cookie("themeNoticeNot", data.themeNotice);
    $.cookie("messagesNoticeNot", data.messagesNotice);
    $.cookie("mainNoticeNot", data.mainNotice);
    $.cookie("newresNoticeNot", data.newresNotice);
    $.cookie("compareNoticeNot", data.compareNotice);
    $.cookie("comparepageNoticeNot", data.comparepageNotice);

    $.each(notices, function (i, v) {
      window[v]();
    });
  });
}

function countSum(tarif_id) {
  var price, k;
  if (tarif_id == 17) price = 30000; //1490
  else if (tarif_id == 15) price = 1490; //Стартовый
  else if (tarif_id == 14) price = 6000; //4983
  else if (tarif_id == 13) price = 16500; //14800
  else if (tarif_id == 12) price = 42000;//36800
  //else if (tarif_id == 5) price = 1400;
  //else if (tarif_id == 6) price = 4000;
  //else if (tarif_id == 7) price = 10000;
  else price = 6000;

  var months = $("#months").val();

  k = 1;
  if (months > 11) k = 0.85;

  var sum = Math.floor(price * months * k);
  return sum;
}

function successDialog() {
  $('body').append('<div id="dialog-paySuccess" title="Состояние оплаты">\
                    <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em;">\
                    <p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>\
                    <strong>Подсказка: </strong>ваш платеж был проведен. Средства поступят в течение 15 минут с момента оплаты.\
                    </p></div></div>');

  $("#dialog-paySuccess").dialog({
    modal: true,
    buttons: {
      "Ок": function () {
        $(this).dialog("close");
        $("#dialog-Settings").dialog("open");
      }
    },
    draggable: false,
    resizable: false,
    minWidth: 400,
    maxWidth: 400,
    autoOpen: false
  });

  $("#dialog-paySuccess").dialog("open");
}

function makeDialog(tarif, id, exp) {
  tariff_id = tarif;
  var tariff = $.cookie("tarif_id");
  if ((tarif == 'Демо' || tariff == 16) && $.cookie("reminder_" + id) != 1) {

    $('body').append('<div id="dialog-demoAlert" title="Состояние аккаунта">\
        <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em;">\
        <p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>\
        Это демо-кабинет, создавать можно только 2 темы, экспорт ограничен 100 сообщениями, ретроспектива по умолчанию выставлена на 1 месяц.\
        <br>Для использования всех функций системы перейдите на платный тариф.\
        </p></div></div>');

    $("#dialog-demoAlert").dialog({
      modal: true,
      buttons: {
        "Оплатить": function () {
          $(this).dialog("close");
          $("#dialog-Settings").dialog("open");
        },
        "Напомнить позже": function () {
          $.cookie("reminder_" + id, 1, { expires: 1 });
          $(this).dialog("close");
        }
      },
      draggable: false,
      resizable: false,
      minWidth: 400,
      maxWidth: 400,
      autoOpen: false
    });

    $("#dialog-demoAlert").dialog("open");
  }
  else if (tarif != 'Демо' && exp <= 3 && exp > 0 && $.cookie("reminder_" + id) != 1 /*&& истекает*/) {

    $('body').append('<div id="dialog-expAlert" title="Состояние аккаунта">\
                <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em;">\
                <p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>\
                <strong>Подсказка: </strong>срок действия вашего аккаунта истекает в течение 3-х дней. Вы можете продлить аккаунт, оплатив нужный вам тариф.\
                </p></div></div>');

    $("#dialog-expAlert").dialog({
      modal: true,
      buttons: {
        "Оплатить": function () {
          $(this).dialog("close");
          $("#dialog-Settings").dialog("open");
        },
        "Напомнить позже": function () {
          $.cookie("reminder_" + id, 1, { expires: 1 });
          $(this).dialog("close");
        }
      },
      draggable: false,
      resizable: false,
      minWidth: 400,
      maxWidth: 400,
      autoOpen: false
    });

    $("#dialog-expAlert").dialog("open");
  }
  else if (tarif != 'Демо' && exp == 'Аккаунт заблокирован' && $.cookie("reminder_blocked_" + id) != 1 /*&& истекает*/) {
    $('body').append('<div id="dialog-expAlert" title="Состояние аккаунта">\
                <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em;">\
                <p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>\
                <strong>Подсказка: </strong>на вашем счету закончились средства. Сбор данных не осуществляется.  Оплатите, пожалуйста, нужный вам тариф для восстановления аккаунта.\
                </p></div></div>');

    $("#dialog-expAlert").dialog({
      modal: true,
      buttons: {
        "Оплатить": function () {
          $(this).dialog("close");
          $("#dialog-Settings").dialog("open");
        },
        "Напомнить позже": function () {
          $.cookie("reminder_blocked_" + id, 1, { expires: 1 });
          $(this).dialog("close");
        }
      },
      draggable: false,
      resizable: false,
      minWidth: 400,
      maxWidth: 400,
      autoOpen: false
    });
    $("#dialog-expAlert").dialog("open");
  }
}

function billingSlider() {
  var select = $("#months");
  var slider = $("<div id='slider' style=\"float:right; width: 48%; margin-top: 5px;\" ></div>").insertAfter(select).slider({
    min: 1,
    max: 12,
    range: "min",
    value: select[ 0 ].selectedIndex + 1,
    slide: function (event, ui) {
      select[ 0 ].selectedIndex = ui.value - 1;
    }
  });

  $("#months").change(function () {
    slider.slider("value", this.selectedIndex + 1);
    $("#billingSum").val($("#billingSum").val() + 1);
  });
}

function getSettings() {
  $.postJSON(ajaxURL_GetSettings, {}, function (data) {
    $("#dialog-Settings-name").val(data.fio);
    $("#dialog-Settings-company").val(data.user_company);
    $("#dialog-Settings-email").val(data.user_mails);
    $("#dialog-Settings-phone").val(data.contact_name);
    var radios = $('input:radio[name=radio]');
    if (radios.is(':checked') === false) {
      radios.filter('[value=' + data.freq_mail + ']').attr('checked', 'checked').button("refresh");
    }
    if( ( data.user_mid_priv == 2 || data.user_mid_priv == 3 || data.user_access == 0 ) && $('#access_setup').is('div') )
    {
      $('#access_setup').parent().remove();
      $('#tarif-limit').css({width: '215px'});
    }
  });
}

function hideNotice(name, value) {
  $.postJSON(ajaxURL_setAdvSettings, {name: name, value: value}, function (data) {
  });
}

function selectTarif(tarif_id) {
  $('#changeTarif').val(tarif_id);
  $("#billingSum").val(countSum(tarif_id));
}

function saveSettings(fio, company, pass, ver_pass, emails, freq, contact) {
  $.postJSON(ajaxURL_SaveSettings, {fio: fio, company: company, pass: pass, ver_pass: ver_pass, emails: emails, freq: freq, contact: contact}, function (data) {
    var status = data.status;
    if (status == 1) {
      $("#dialog-Settings-name").addClass("ui-state-error");
      $("#dialog-Settings-tip").text("ФИО должно быть более 3х символов");
      $("#tip-cont-4").show();
    }
    else if (status == 2) {
      $("#dialog-Settings-company").addClass("ui-state-error");
      $("#dialog-Settings-tip").text("Название компании должно быть более 3х символов");
      $("#tip-cont-4").show();
    }
    else if (status == 3) {
      $("#tip-cont-4").show();
      $("#dialog-Settings-passnew").addClass("ui-state-error");
      $("#dialog-Settings-passcheck").addClass("ui-state-error");
      $("#dialog-Settings-tip").text("Пароль должен состоять из 8 символов. Минимум 1 заглавная буква и 1 цифра.");
    }
    else if (status == 5) {
      $("#tip-cont-4").show();
      $("#dialog-Settings-email").addClass("ui-state-error");
      $("#dialog-Settings-tip").text("Некорректный список e-mail адресов. Проверьте правильнось ввода.");
    }
    else if (status == 6) {
      $("#dialog-Password").dialog("open");
    }
    else {
      $("#tip-cont-4").hide();
      //TODO: добавить для фейдинга
      $(".faded").fadeTo('slow', 0.2, function () {
        $("#savenotice").fadeTo(0, 1, function () {
          setTimeout(function () {
            $("#savenotice").hide();
            $(".faded").fadeTo('slow', 1);
          }, 1000);
        });
      });
    }
  });
}

function getSignature(sum, tariff_id, months, submit) {
  $.postJSON(ajaxURL_GetBilling, {sum: sum, tariff_id: tariff_id, months: months }, function (data) {
    $("#InvId").val(data.bill_id);
    $("#Sign").val(data.sign);
    $("#OutSum").val(sum);
    if (submit) {
      $("#billingForm").submit();
    }
  });
}

function getBilling() {

  $("#billingTable").empty();
  $.postJSON(ajaxURL_GetBilling, {}, function (data) {

    $("#billingTable").append('<col width="70" /><col width="70" align="right"/><col /><col />');
    if (data == null) {
      $("#billingTable").append("<tr><td>-</td><td>-</td><td>-</td><td>-</td></tr>");
      return false;
    }
    else {
      $.each(data.bill, function (key, bill) {
        var billtype;
        if (bill.type == 1) billtype = 'пополнение';
        else if (bill.type == 2) billtype = 'оплата тарифа';

        var billstatus;
        if (bill.status == 1) billstatus = 'успешно';
        else if (bill.status == 0) billstatus = 'ошибка';

        $("#billingTable").append("<tr><td>" + bill.date + "</td><td align='right'><span style='padding-right:18px;'>" + bill.money + "</span></td><td width='107'>" + billtype + "</td><td>" + billstatus + "</td></tr>");
      });
    }
  });
}


function makeSettingsDialog() {
  $('body').append('<div id="dialog-Password" class="mdialog" title="Смена пароля">\
    <div class="ui-widget">\
    <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em;">\
    <p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>\
    <strong>Подсказка:</strong>\
    Письмо о смене пароля было выслано вам на почту. Перейдите по ссылке, указанной в письме, для подтверждения.</p>\
    </div></div></div>\
  \
    <div id="dialog-Settings" class="mdialog" title="Настройки пользователя">\
    <div id="savenotice" style="display:none; width:auto; height:auto; position:absolute; top:180px; left:200px; text-align:center;"><div class="ui-state-highlight ui-corner-all" style="width:300px; height:auto; margin:auto;">    <p style="padding: 20px 0; text-align:left;"><span class="ui-icon ui-icon-info" style="margin-right: 10px; margin-left:40px; float:left;"></span>    <strong>Подсказка:</strong> настройки сохранены.</p></div></div>\
    <div class="faded" style="width: 47%; float: left; padding-right: 3%; margin-right: 3%; border-right: 1px dashed silver;">\
    <p style="font-size: 16px;">Пользователь: <span id="popup-user"></span></p>\
    <p style="font-size: 16px;">Ваш тариф: <span id="popup-tarif"></span></p>\
    <br><label>Контактное лицо (ФИО)</label>\
    <input type="text" id="dialog-Settings-name" class="ui-form "/>\
    <em id=""></em><label>Контактный телефон</label>\
    <input type="text" id="dialog-Settings-phone" class="ui-form "/>\
    <label>Ваша компания</label>\
    <input type="text" id="dialog-Settings-company" class="ui-form "/>\
    <label>Смена пароля</label>\
    <input type="password" id="dialog-Settings-passnew" class="ui-form "/>\
    <label>Повторите пароль</label>\
    <input type="password" id="dialog-Settings-passcheck" class="ui-form "/>\
    <p>Высылать дайжест каждый:</p>\
    <div style="float: left; width: 100%; margin-bottom: 5px;" id="mail-settings">\
    <div id="radio">\
        <input type="radio" id="dialog-Settings-digest-daily"  name="radio" value="1" /> <label for="dialog-Settings-digest-daily" style="float: left;"> день</label>\
        <input type="radio" id="dialog-Settings-digest-weekly"  name="radio"  value="2" /> <label for="dialog-Settings-digest-weekly" style="float: left;"> неделю</label>\
        <input type="radio" id="dialog-Settings-digest-monthly"  name="radio" value="3" /> <label for="dialog-Settings-digest-monthly" style="float: left;">месяц</label>\
        <input type="radio" id="dialog-Settings-digest-disable"  name="radio"  value="0" /> <label for="dialog-Settings-digest-disable" style="float: left;"> не высылать</label>\
    </div></div>\
    <label>Адреса e-mail через запятую</label>\
    <input type="text" id="dialog-Settings-email" class="ui-form "/>\
    <div class="ui-widget" id="tip-cont-4">\
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">\
    <p style="padding: 10px 0;" ><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .7em;"></span>\
    <strong>Ошибка:</strong> <span id="dialog-Settings-tip"></span> </p>\
    </div></div>\
    <em id="dialog-Settings-email-tip"></em></div>\
    <div class="faded" style="width: 46%; float: left;">\
    <h1 style="font-size: 16px;">Ваш баланс: <span id="popup-money"></span> руб.<br> до окончания действия <span id="popup-exp"></span> дней</h1>\
    <div id="scrollbar2">\
    <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>\
    <div class="viewport">\
    <div class="overview">\
    <table style="width:310px;">\
    <col width="70" /><col width="65" align="right" /><col /><col />\
    <tr><td>Дата</td><td>Сумма <span style="font-size:8px;">руб.</span></td><td>Назначение</td><td>Статус</td></tr></table>\
    <table id="billingTable" style="width:310px;" class="dialog-List"></table>\
    </div></div></div>\
    <h1 style="font-size: 16px; padding-bottom: 20px;">Пополнить баланс:</h1>\
    <div style="float: left; width: 143px;">\
    <p style="float: left; margin-right: 10px;">Период:</p>\
    <select id="months" style="float: left; margin-right: 5px; ">\
    <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>\
    <option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option>\
    <option value="10">10</option><option value="11">11</option><option value="12">12</option>\
    </select> <p style="float: left; margin-right: 5px;">мес.</p></div>\
    <div style="float: left; width:162px ;">Тариф:\
    <select  id="changeTarif" style="width:100px;"><option value="14">Базовый</option><option value="13">Профессиональный</option><option value="17">Бизнес</option><option value="12">Корпоративный</option></select>\
    <a href="http://www.wobot.ru/tariffs.html" target="_blank" ><span id="tarifinfo" class="ui-icon ui-icon-info" style="float: right;  "></span></a></div>\
            <div style="float: left; width: 100%; padding-top: 10px;">\
            <p style="float:left; padding-right:15px;">Cумма:</p><input type="text" name="OutSum" value="1400" disabled="true" class="ui-form" style="width:26%; margin-right: 5%; float:left;" id="billingSum">\
            <input type="submit" value="Оплатить" id="robokassaSubmit">\
    <div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em; float:left;">\
    <p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>\
    <strong>Подсказка:</strong> новый тариф вступает в действие после окончания действия предыдущего.</p></div>\
            <form action="https://merchant.roboxchange.com/Index.aspx" method="POST" id="billingForm"><input type="hidden" name="MrchLogin" value="Wobot">\
                <input type="hidden" name="OutSum" id="OutSum">\
                <input type="hidden" name="InvId" id="InvId">\
                <input type="hidden" name="Desc" value="ROBOKASSA Пополнение счета кабинета">\
                <input type="hidden" name="SignatureValue" value="" id="Sign">\
                <input type="hidden" name="Shp_item" value="0" id="billingItem">\
                <input type="hidden" name="IncCurrLabel" value="PCR">\
                <input type="hidden" name="Culture" value="ru">\
                </form></div></div><p id="d_mes"></p></div>');
}

function declOfNum(number, titles)
{
    cases = [2, 0, 1, 1, 1, 2];
    return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
}

$(document).ready(function () {
  makeSettingsDialog();
  $("#popup-tarif").text($.cookie("user_tarif"));
  $("#popup-user").text($.cookie("user_email"));

  $("#popup-money").html($.cookie("user_money"));
  if ($.cookie("user_exp") == "Аккаунт заблокирован") $("#popup-exp").text(0);
  else $("#popup-exp").text($.cookie("user_exp"));

  $("#robokassaSubmit").click(function () {
    getSignature($("#billingSum").val(), $("#changeTarif").val(), $("#months").val(), true);
  });

  $('#scrollbar2').tinyscrollbar();

  var settingsName = $("#dialog-Settings-name"),
    settingsCompany = $("#dialog-Settings-company"),
    settingsPassnew = $("#dialog-Settings-passnew"),
    settingsPasscheck = $("#dialog-Settings-passcheck"),
    settingsEmail = $("#dialog-Settings-email"),
    settingsPhone = $("#dialog-Settings-phone");

  var allSettings = $([]).add(settingsName)
    .add(settingsCompany)
    .add(settingsPassnew)
    .add(settingsPasscheck)
    .add(settingsEmail)
    .add(settingsPhone);

  $("#dialog-Settings").dialog({
    modal: true,
    open: function () {
      $(".ui-dialog-buttonpane button:contains('Применить')").button("disable");
      $('#dialog-Settings-name, #dialog-Settings-phone, #dialog-Settings-company, #dialog-Settings-passnew, #dialog-Settings-passcheck, #dialog-Settings-email, #radio')
        .on('change keypress paste textInput input', function () {
          $(".ui-dialog-buttonpane button:contains('Применить')").button("enable");
        });
      selectTarif($.cookie("tarif_id"));
      $("#billingSum").val(countSum($.cookie("tarif_id")));

      allSettings.removeClass("ui-state-error");
      settingsPassnew.val('');
      settingsPasscheck.val('');
      $("#tip-cont-4").hide();
      getSettings();
      getBilling();
      $('#radio').buttonset();
    },
    buttons: {
      "Применить": function () {
        allSettings.removeClass("ui-state-error");
        $("#tip-cont-4").hide();
        bValid = true;
        bValid = bValid && checkLength(settingsName, "Длина имени должна быть от 4 до 50 символов.", 3, 50, "#dialog-Settings-tip");
        if (settingsPassnew.val() != settingsPasscheck.val()) {
          settingsPasscheck.addClass("ui-state-error");
          updateTips("Пароли не совпадают.", $("#dialog-Settings-tip"));
          bValid = false;
        }

        var mailFreq = $('input[name=radio]:checked').val();

        if (bValid) {
          saveSettings(settingsName.val(),
            settingsCompany.val(),
            settingsPassnew.val(),
            settingsPasscheck.val(),
            settingsEmail.val(),
            mailFreq,
            settingsPhone.val()
          );
        }
        else {
          $("#tip-cont-4").show();
        }

        $(".ui-dialog-buttonpane button:contains('Применить')").button("disable");
      },
      "Закрыть": function () {
        $(this).dialog("close");
      }
    },

    draggable: false,
    resizable: false,
    minWidth: 700,
    maxWidth: 700,
    autoOpen: false
  });

  //for settings
  $("#dialog-Password").dialog({
    modal: true,
    buttons: {
      "Ок": function () {
        $("#pass");
        $(this).dialog("close");
        $("#dialog-Settings-passnew").val('');
        $("#dialog-Settings-passcheck").val('');
      }
    },
    draggable: false,
    resizable: false,
    minWidth: 500,
    maxWidth: 500,
    autoOpen: false
  });

  $('#robokassaSubmit').button();

  getSettings();

  $("#changeTarif").change(function () {
    $("#billingSum").val(countSum($(this).val()));
  });
  $("#months").change(function () {
    $("#billingSum").val(countSum($("#changeTarif").val()));
  });

  $(document).delegate("#tarif17", "click", function () {
    selectTarif(17);
    $("#tiptip_holder").fadeOut();
    return false;
  });
  $(document).delegate("#tarif14", "click", function () {
    selectTarif(14);
    $("#tiptip_holder").fadeOut();
    return false;
  });
  $(document).delegate("#tarif13", "click", function () {
    selectTarif(13);
    $("#tiptip_holder").fadeOut();
    return false;
  });
  $(document).delegate("#tarif12", "click", function () {
    selectTarif(12);
    $("#tiptip_holder").fadeOut();
    return false;
  });

  if ($.cookie("tarif_id") == 5 || $.cookie("tarif_id") == 6 || $.cookie("tarif_id") == 7 || $.cookie("tarif_id") == 15) {
    if ($.cookie("tarif_id") == 5) {
      $('<option selected value="5">Начальный</option>').appendTo("#changeTarif");
    }
    if ($.cookie("tarif_id") == 6) {
      $('<option selected value="6">Базовый_old</option>').appendTo("#changeTarif");
    }
    if ($.cookie("tarif_id") == 7) {
      $('<option selected value="7">Расширенный</option>').appendTo("#changeTarif");
    }
    if ($.cookie("tarif_id") == 15) {
      $('<option selected value="15">Стартовый</option>').appendTo("#changeTarif");
    }
  }

  var success = window.location.search.replace("?", "");
  if (success) successDialog();

  $("#tarifinfo").tipTip({content: '\
            <table class="tipTable" style="margin-bottom: 5px; font-size:10px;"><thead><tr><th class="izzyGridDescription">&nbsp;</th>\
        <th width="100"><h4>Базовый</h4></th><th width="100"><h4>Профессиональный</h4></th><th width="100"><h4>Бизнес</h4></th><th width="100"><h4>Корпоративный</h4></th></tr></thead>\
    <tfoot><tr style="color: white;"><td></td>\
        <td><a href="#" id="tarif14">выбрать</a></td>\
        <td><a href="#" id="tarif13">выбрать</a></td>\
        <td><a href="#" id="tarif17">выбрать</a></td>\
        <td><a href="#" id="tarif12">выбрать</a></td>\
        </tr></tfoot>\
    <tr><td width="200">Срок действия</td><td>1 месяц</td><td>1 месяц</td><td>1 месяц</td><td>1 месяц</td></tr>\
    <tr><td class="izzyGridDescription">Количество тем</td><td >до 5</td><td>до 15</td><td>до 30</td><td>до 60</td></tr>\
    <tr class="odd"><td class="izzyGridDescription">Количество упоминаний</td><td >50 000</td><td>75 000</td><td>100 000</td><td>100 000</td></tr>\
    <tr><td class="izzyGridDescription">Сбор ретроспективных данных</td><td >2 месяц</td><td>3 месяца</td><td>5 месяца</td><td>8 месяцев</td></tr>\
    <tr><td >Стоимость в месяц</td><td class="izzyGridSelected">6 000 руб.</td><td class="izzyGridSelected">16 500 руб.</td>\
    <td class="izzyGridSelected">30 000 руб.</td><td class="izzyGridSelected">42 000 руб.</td></tr></tbody></table>', edgeOffset: -10, keepAlive: true, maxWidth: 500})
});
