var wasChanged = false;
var id;
var chart, chart2;
var minDate, maxDate, graphtype, tickInt;
var urlCom, shift/*,xmax*/;
var REG_TYPE = "simple";
var first_load = 0;
var adding_group = 0;
var dialog_win;
var focuses = [0, 0, 0, 0];
var theme;
var groupsLoaded = 0;
var groups_unchecked = 0;
var is_theme_create = 0;
var adv_keys_changed = 0;
var tags = {};
var search_object_list = {};
var advance_key_words_length = 700;

/*Ошибки*/
var myMessages = ['error_info', 'error_warning', 'error_error', 'error_success'];

function redirectAfterSave() {
  setTimeout($(location).attr('href', 'themes_list.html'), 2000);
}

function hideAllMessages() {
  var messagesHeights = new Array(); // this array will store height for each

  for (i = 0; i < myMessages.length; i++) {
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

function showTagError(tag, text, field) {
  $(".ui-widget", tag).show();
  $("span.errormsg", tag).text(text);
  $("." + field + " input", tag).addClass("ui-state-error");
}

function showAddError(field, text) {
  $("#" + field).addClass("ui-state-error");
  $("#adderror").show();
  $("#adderror span").text(text).show();
}

function clearAddErrors() {
  $("#adderror").hide();
  $(".ui-form input").removeClass("ui-state-error");
}

function clearErrors(tag) {
  $(".name input", tag).removeClass("ui-state-error");
  $(".kw input", tag).removeClass("ui-state-error");
  $("#tagerror", tag).hide();
}

function deleteTags(i) {
  var tagstmp = {}, k = 0;
  delete tags[i];
  $.each(tags, function (i, e) {
    tagstmp[k] = e;
    k++;
  });
  tags = tagstmp;
}

var tagssave = 0;

function saveTag(id, e) {
  if (tagssave == Object.keys(tags).length) {
    redirectAfterSave();
    return false;
  }

  $.postJSON(ajaxURL_AddTagFull, {order_id: id, tag_name: e.name, user_id: user_id, auto: e.auto, tag_kw: e.keywords, tag_sw: e.stopwords}, function (responce) {

    /* status
     0 - нет имени
     1 - нет ключевых слов
     21 - неправильные ключевые слова
     22 - неправильные стоп слова
     fail - другие ошибки
     */
    //TODO: сделать чтобы если не автоматом то всеравно добавлялось без ключевых слов
    if (responce.status == 0) showAddError("addname", "введите имя тега.");
    else if (responce.status == 1 && auto == 1) showAddError("kwrd", "введите ключевые слова.");
    else if (responce.status == 21) showAddError("kwrd", "проверьте правильность ввода ключевых слов.");
    else if (responce.status == 22) showAddError("swrd", "проверьте правильность ввода стоп-слов.");
    else {
      $("#addtagform").slideUp('fast');
      $("#savetag").hide();
      $("#canceltag").hide();
      $("#addtag").show();
      loadContent(id);
      $("#addname").val('');
      $("#kwrd").val('');
      $("#swrd").val('');
      $("#akw").val('');
      tagssave++;
      saveTag(id, tags[tagssave]);
    }
  });
}

function renderTags(tags) {
  $.each(tags, function (i, e) {
    var tag = $(".tagstable .template").clone();
    $(".tag" + i).fadeOut('fast').remove();
    tag.removeClass("template").addClass("tag" + i).show();
    tag.attr("pk", i);
    $(".name", tag).html('<span>' + e.name + '</span><input type="text" value="' + e.name + '" style="display:none;" />');
    $(".auto div", tag).attr("id", "radiotag" + i);
    $(".auto div", tag).children('input').eq(0).attr("id", "auto" + i).attr("name", "radio" + i);
    $(".auto div", tag).children('input').eq(1).attr("id", "manual" + i).attr("name", "radio" + i);
    $("label#manual", tag).first().attr("for", "manual" + i);
    $("label#auto", tag).first().attr("for", "auto" + i);
    $(".auto_type", tag).find('input').eq(0).attr("id", "autotag_type_simple" + i).attr("name", "autotag_type" + i);
    $(".auto_type", tag).find('input').eq(1).attr("id", "autotag_type_query" + i).attr("name", "autotag_type" + i);
    $(".auto_type", tag).find('label').eq(0).attr("for", "autotag_type_simple" + i);
    $(".auto_type", tag).find('label').eq(1).attr("for", "autotag_type_query" + i);

    if( e.advanced_keywords != undefined )
    {
      $(".tag_akw input", tag).val(e.advanced_keywords);
    }
    else
    {
      $(".kw input", tag).val(e.keywords);
      $(".sw input", tag).val(e.stopwords);
    }

    //buttonset
    var radios = $('input:radio', tag);
    if (e.auto == 0) {
      var auto = 0;
      $(".edithidden input", tag).attr('disabled', 'disabled').addClass("ui-state-disabled");
    }
    else {
      auto = 1;
      $(".edithidden input", tag).attr('disabled', false).removeClass("ui-state-disabled");
    }
    radios.filter('[value=' + auto + ']').attr('checked', 'checked');
    $('#radiotag' + i, tag).buttonset();
    $('#radiotag' + i, tag).buttonset({disabled: true});
    $(".edit button", tag).click(function () {
      $("#editcancel").click();
      $('#radiotag' + i, tag).buttonset({disabled: false});
      if( !$(".auto_type", tag).is(':hidden') )
      {
        $(".edithidden", tag).hide();
      }
      else
      {
        $(".edithidden", tag).show();
        showTagEditFormByQueryType(tag);
      }
      $(".name span", tag).toggle();
      $(".name input", tag).toggle();
    });

    user_id = $.cookie("user_id");
    $(".delete button", tag).click(function () {
      dialog_win = $('<div id="sharerdialog">Вы действительно хотите удалить тег?</div>')
        .dialog({
          autoOpen: false,
          modal: true,
          resizable: false,
          title: '',
          position: ['center', 150],
          width: 450,
          buttons: {
            "Отмена": function () {
              dialog_win.dialog('close');
            },
            "Удалить": function () {
              dialog_win.dialog('close');
              tag.empty();
              deleteTags(i);
              renderTags(tags);
            }
          }
        });
      dialog_win.dialog('open');
    });

    $("#editsave", tag).button({
      icons: {primary: "ui-icon-check"},
      text: "Ок"
    }).click(function () {
        clearErrors(tag);
        var tag_kw = $(".kw input", tag).val();
        var tag_sw = $(".sw input", tag).val();
        var tag_name = $(".name input", tag).val();
        var auto = $("input[name=radio" + i + "]:checked", tag).val();

        //TODO: захуярить сохранение
        tags[i].name = tag_name;
        tags[i].keywords = tag_kw;
        tags[i].stopwords = tag_sw;
        tags[i].auto = auto;

        if( !$(".auto_type", tag).is(':hidden') )
        {
          $(".edithidden", tag).hide();
        }
        else
        {
          $(".edithidden", tag).show();
          showTagEditFormByQueryType(tag);
        }
        $(".name span", tag).text($(".name input", tag).val());
        $(".name span", tag).toggle();
        $(".name input", tag).toggle();
        $('#radiotag' + i, tag).buttonset({disabled: true});
      });

    //TODO: дальше использовать

    $("label span", tag).css("font-size", "10px");

    $('input:radio[name=radio' + i + ']', tag).change(
      function () {
        if ($(this).val() == 0) {
          $(".edithidden input", tag).attr('disabled', 'disabled').addClass("ui-state-disabled");
          $(".editcancel", tag).click();
        }
        else if ($(this).val() == 1) {
          $(".edithidden input", tag).attr('disabled', false).removeClass("ui-state-disabled");
          if (!$(".kw input", tag).val()) {
            $(".kw input", tag).focus();
            $(".kw input", tag).addClass("ui-state-error");
            $(".edithidden", tag).show();
            $(".name span", tag).hide();
            $(".name input", tag).show();
          }
        }
      }
    );

    $("#editcancel", tag).button({
      icons: {primary: "ui-icon-arrowreturnthick-1-w"},
      text: "Отменить"
    }).click(function () {
      if (!$(".kw input", tag).val()) {
        $("input[name=radio" + i + "][value=" + 0 + "]").attr('checked', 'checked');
        $('#radiotag' + i, tag).buttonset("refresh");
      }
      clearErrors(tag);
      if( !$(".auto_type", tag).is(':hidden') )
      {
        $(".edithidden", tag).hide();
      }
      else
      {
        $(".edithidden", tag).show();
        showTagEditFormByQueryType(tag);
      }
      $(".name span", tag).toggle();
      $(".name input", tag).toggle();
      $('#radiotag' + i, tag).buttonset({disabled: true});
      renderTags(tags);
    });

    $(".edit button", tag).button({
      icons: {
        primary: "ui-icon-pencil"
      },
      text: false
    }).css("width", "24").css("height", "24");

    $(".delete button", tag).button({
      icons: {
        primary: "ui-icon-trash"
      },
      text: false
    }).css("width", "24").css("height", "24");

    $(".edithidden span", tag).css("font-size", "10px");
    $("#editcancel span", tag).css("font-size", "10px");

    $(".tagstable").append(tag);
  });
}

function loadContent(id) {
  $.postJSON(ajaxURL_GetTags, {order_id: id}, function (responce) {
    if (responce != null && responce.status != "fail") {
      var tags = responce.tags;
      if( Object.keys(tags).length > 0 )
      {
        $('#copy-tags').attr('disabled', 'disabled');
      }
      else
      {
        $('#copy-tags').removeAttr('disabled');
      }
      $.each(tags, function (i, e) {
        var tag = $(".tagstable .template").clone();
        $(".tag" + i).fadeOut('fast').remove();
        tag.removeClass("template").addClass("tag" + i).show();
        tag.attr("pk", i);
        $(".name", tag).html('<span>' + e.name + '</span><input type="text" value="' + e.name + '" style="display:none;" />');
        $(".auto div", tag).attr("id", "radiotag" + i);
        $(".auto div", tag).children('input').eq(0).attr("id", "auto" + i).attr("name", "radio" + i);
        $(".auto div", tag).children('input').eq(1).attr("id", "manual" + i).attr("name", "radio" + i);
        $("label#manual", tag).first().attr("for", "manual" + i);
        $("label#auto", tag).first().attr("for", "auto" + i);
        $(".auto_type", tag).find('input').eq(0).attr("id", "autotag_type_simple" + i).attr("name", "autotag_type" + i);
        $(".auto_type", tag).find('input').eq(1).attr("id", "autotag_type_query" + i).attr("name", "autotag_type" + i);
        $(".auto_type", tag).find('label').eq(0).attr("for", "autotag_type_simple" + i);
        $(".auto_type", tag).find('label').eq(1).attr("for", "autotag_type_query" + i);

        var radios = $('input:radio', tag);
        if (e.auto == 0) {
          var auto = 0;
          $(".edithidden input", tag).attr('disabled', 'disabled').addClass("ui-state-disabled");
        }
        else {
          auto = 1;
          $(".edithidden input", tag).attr('disabled', false).removeClass("ui-state-disabled");
        }
        radios.filter('[value=' + auto + ']').attr('checked', 'checked');

        if( e.advanced_keywords != undefined )
        {
          $(".tag_akw input", tag).val(e.advanced_keywords);
          radios.filter('[value="query"]').attr('checked', 'checked');
        }
        else
        {
          $(".kw input", tag).val(e.keywords);
          $(".sw input", tag).val(e.stopwords);
          radios.filter('[value="simple"]').attr('checked', 'checked');
        }

        $('.auto_type input[type="radio"]', tag).change(function(){
          showTagEditFormByQueryType(tag);
        });
        $('#radiotag' + i, tag).buttonset();
        $('#radiotag' + i, tag).buttonset({disabled: true});

        $(".edit button", tag).click(function () {
          $("#editcancel").click();
          $('#radiotag' + i, tag).buttonset({disabled: false});

          if( !$(".auto_type", tag).is(':hidden') )
          {
            $(".edithidden", tag).hide();
          }
          else
          {
            $(".edithidden", tag).show();
            showTagEditFormByQueryType(tag);
          }
          $(".name span", tag).toggle();
          $(".name input", tag).toggle();
        });

        user_id = $.cookie("user_id");
        $(".delete button", tag).click(function () {
          dialog_win = $('<div id="sharerdialog">Вы действительно хотите удалить тег?</div>')
            .dialog({
              autoOpen: false,
              modal: true,
              resizable: false,
              title: '',
              position: ['center', 150],
              width: 450,
              buttons: {
                "Отмена": function () {
                  dialog_win.dialog('close');
                },
                "Удалить": function () {
                  dialog_win.dialog('close');
                  $.postJSON(ajaxURL_DelTag, {order_id: id, tag_id: i, user_id: user_id}, function (data) {
                    tag.remove();
                    loadContent(id);
                  });
                }
              }
            });
          dialog_win.dialog('open');
        });

        $("#editsave", tag).button({
          icons: {primary: "ui-icon-check"},
          text: "Ок"
        }).click(function () {
            clearErrors(tag);
            var tag_kw = $(".kw input", tag).val();
            var tag_sw = $(".sw input", tag).val();
            var tag_akw = $(".tag_akw input", tag).val();
            var tag_name = $(".name input", tag).val();
            var auto = $("input[name=radio" + i + "]:checked", tag).val();

            var tagData = {};
            if( $('input[name="autotag_type' + i + '"]:checked', tag).val() == 'simple' )
            {
              tagData = {
                order_id: id,
                tag_id: i,
                tag_name: tag_name,
                tag_kw: tag_kw,
                tag_sw: tag_sw,
                tag_auto: auto
              };
            }
            else
            {
              tagData = {
                order_id: id,
                tag_id: i,
                tag_name: tag_name,
                tag_akw: tag_akw,
                tag_auto: auto
              };
            }

            //TODO: захуярить сохранение
            $.postJSON(ajaxURL_EditTagFull, tagData, function (data) {
                if (data.status == 0) showTagError(tag, "введите имя тега.", "name");
                else if (data.status == 1 && auto == 1) showTagError(tag, "введите ключевые слова.", "kw");
                else if (data.status == 21) showTagError(tag, "проверьте правильность ввода ключевых слов.", "kw");
                else if (data.status == 22) showTagError(tag, "проверьте правильность ввода стоп-слов.", "sw");
                else {
                  if( !$(".auto_type", tag).is(':hidden') )
                  {
                    $(".edithidden", tag).hide();
                  }
                  else
                  {
                    $(".edithidden", tag).show();
                    showTagEditFormByQueryType(tag);
                  }
                  $(".name span", tag).text($(".name input", tag).val());
                  $(".name span", tag).toggle();
                  $(".name input", tag).toggle();
                }
              });
            $('#radiotag' + i, tag).buttonset({disabled: true});
          });

        //TODO: дальше использовать

        $("label span", tag).css("font-size", "10px");

        $('input:radio[name=radio' + i + ']', tag).change(function () {
          if ($(this).val() == 0) {
            $(".edithidden input", tag).attr('disabled', 'disabled').addClass("ui-state-disabled");
            $(".editcancel", tag).click();
          }
          else if ($(this).val() == 1) {
            $(".edithidden input", tag).attr('disabled', false).removeClass("ui-state-disabled");
            if (!$(".kw input", tag).val()) {
              $(".kw input", tag).focus();
              $(".kw input", tag).addClass("ui-state-error");
              $(".edithidden", tag).show();
              $(".name span", tag).hide();
              $(".name input", tag).show();
            }
          }
          showTagEditFormByQueryType(tag);
        });

        $("#editcancel", tag).button({
          icons: {primary: "ui-icon-arrowreturnthick-1-w"},
          text: "Отменить"
        }).click(function () {
            if (!$(".kw input", tag).val()) {
              $("input[name=radio" + i + "][value=" + 0 + "]").attr('checked', 'checked');
              $('#radiotag' + i, tag).buttonset("refresh");
              $.postJSON(ajaxURL_EditTagFull, {order_id: id, tag_id: i, change_auto: 1, tag_auto: 0}, function (data) { });
            }
            clearErrors(tag);
            if( !$(".auto_type", tag).is(':hidden') )
            {
              $(".edithidden", tag).hide();
            }
            else
            {
              $(".edithidden", tag).show();
              showTagEditFormByQueryType(tag);
            }
            $(".name span", tag).toggle();
            $(".name input", tag).toggle();
            $('#radiotag' + i, tag).buttonset({disabled: true});
            loadContent(id);
          });

        $(".edit button", tag).button({
          icons: {
            primary: "ui-icon-pencil"
          },
          text: false
        }).css("width", "24").css("height", "24");

        $(".delete button", tag).button({
          icons: {
            primary: "ui-icon-trash"
          },
          text: false
        }).css("width", "24").css("height", "24");
        $(".edithidden span", tag).css("font-size", "10px");
        $("#editcancel span", tag).css("font-size", "10px");
        $(".tagstable").append(tag);
      });
      $(".tagstable .template").hide();
      var value;
    }
    else {
      $(".tagstable .template").hide();
      $('#copy-tags').removeAttr('disabled');
    }
  });
}

function loadThemeSettings(id) {
  loadContent(id);
  $.postJSON(ajaxURL_getThemeSettings, {order_id: id}, function (responce) {
    if (responce.status === undefined) {
      if (responce.order_name !== undefined) {
        $("#theme_name input").val(responce.order_name);
        $(".layer > h3 > a").remove();
        $(".layer > h3 > span").after('<a class="back_to_list" href="/themes_list.html">(вернуться к списку тем)</a>').after('<a href="/theme_page.html#' + id + '"> ' + responce.order_name + '</a>');
        $("#theme_name").keyup();
      }
      if (responce.order_start !== undefined && responce.order_end != undefined) {
        var order_start = new Date(responce.order_start * 1000);
        var order_end = new Date(responce.order_end * 1000);
        console.log(order_start);
        console.log(order_end);
        $("#order_start").text(order_start.format("dd.mm.yy"));
        $("#order_end").text(order_end.format("dd.mm.yy"));
        if (!$("#dp-begin").hasClass("hasDatepicker")) {
        $("#date #datepicker").val( dateToWords(order_start.format("dd.mm.yyyy"), true) + " - " + dateToWords(order_end.format("dd.mm.yyyy"), true));

        $("#dp-begin").datepicker({
          dateFormat: "dd.mm.yy",
          firstDay: 1,
          minDate: order_start,
          maxDate: order_end,
          onSelect: function (dateText, inst) {
            var str = $("#date #datepicker").val();
            var subs = str.split("-");
            wasChanged = true;
            $("#date #datepicker").val(dateToWords(dateText, true) + " -" + subs[1]);
            $("#dp-end").datepicker("option", "minDate", $(this).datepicker("getDate"));
          }
        }).datepicker("setDate", order_start);

        $("#dp-end").datepicker({
          dateFormat: "dd.mm.yy",
          firstDay: 1,
          minDate: order_start,
          maxDate: order_end,
          onSelect: function (dateText, inst) {
            var str = $("#date #datepicker").val();
            var subs = str.split("-");
            wasChanged = true;
            $("#date #datepicker").val(subs[0] + "- " + dateToWords(dateText, true));
            $("#dp-begin").datepicker("option", "maxDate", $(this).datepicker("getDate"));
          }
        }).datepicker("setDate", order_end);

        $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
      }
      }
      if (responce.disable_theme !== undefined) {
        $("#active input").removeAttr("checked");
        switch (responce.disable_theme) {
          case 0:
          {
            $("#active input:eq(0)").attr("checked", "checked");
          }
            break;
          case 1:
          {
            $("#active input:eq(1)").attr("checked", "checked");
          }
            break;
          default:
          {
            $("#active input:eq(0)").attr("checked", "checked");
          }
            break;
        }
      }
      if (responce.order_keyword.mkw !== undefined) {
        $("#request_type input:eq(0)").removeAttr("checked");
        $("#request_type input:eq(1)").attr("checked", "checked");
        $("#advance_key_words").show();
        $("#advance_key_words textarea").val(responce.order_keyword.mkw);
        $("#key_words").hide();
        $("#additional_key_words").hide();
        $("#stop_words").hide();
        REG_TYPE = "complex";
        var counter = $('#advance_key_words .left-symbols-count');
        counter.html('Осталось символов: '+(advance_key_words_length-$("#advance_key_words textarea").val().length)+'/'+advance_key_words_length);
        if( $("#advance_key_words textarea").val().length > 700 )
        {
          counter.html('Осталось символов: 0/'+advance_key_words_length);
          $("#advance_key_words textarea").val($("#advance_key_words textarea").val().substr(0, advance_key_words_length));
        }
        $("#advance_key_words").keyup();
      }
      else if( responce.order_keyword.mko !== undefined )
      {
        search_object_list[responce.order_keyword.mko.object_id] = responce.order_keyword.mko;
        $("#request_type input:eq(0)").removeAttr("checked");
        $("#request_type input:eq(1)").attr("checked", "checked");
        $("#advance_key_words").show();
        $("#key_words").hide();
        $("#additional_key_words").hide();
        $("#stop_words").hide();
        $("#advance_key_words input").val(responce.order_keyword.mko.object_id);
        $('#advance_key_words .selected-search-object').html(
          '<div rel="'+responce.order_keyword.mko.object_id+'">'+responce.order_keyword.mko.object_name+'<div class="close">x</div></div>'
        ).show();
        $('#advance_key_words .left-symbols-count').html('Осталось символов: '+(advance_key_words_length-responce.order_keyword.mko.length)+'/'+advance_key_words_length);
        REG_TYPE = "complex";
      }
      else {
        if (responce.order_keyword.mw !== undefined) {
          $("#key_words textarea").val(responce.order_keyword.mw);
          $("#key_words").keyup();
        }
        if (responce.order_keyword.mnw !== undefined) {
          $("#additional_key_words textarea").val(responce.order_keyword.mnw);
        }
        if (responce.order_keyword.mew !== undefined) {
          $("#stop_words textarea").val(responce.order_keyword.mew);
        }
      }
      if (responce.res_type !== undefined) {
        $("#monintoring input").removeAttr("checked");
        switch (responce.res_type.type) {
          case "all":
          {
            $("#monintoring input:eq(0)").attr("checked", "checked");
            $("#space_list").hide();
          }
            break;
          case "except":
          {
            $("#monintoring input:eq(1)").attr("checked", "checked");
            $("#space_list").show();
          }
            break;
          case "only":
          {
            $("#monintoring input:eq(2)").attr("checked", "checked");
            $("#space_list").show();
          }
            break;
        }
      }
      if (responce.res !== undefined) {
        var res_str = responce.res.join("\n");
        $("#space_list textarea").val(res_str);
      }
      if (responce.author_type !== undefined) {
        $("#authors input").removeAttr("checked");
        switch (responce.author_type) {
          case "all":
          {
            $("#authors input:eq(0)").attr("checked", "checked");
            $("#authors_list").hide();
          }
            break;
          case "except":
          {
            $("#authors input:eq(1)").attr("checked", "checked");
            $("#authors_list").hide();
          }
            break;
          case "only":
          {
            $("#authors input:eq(2)").attr("checked", "checked");
            $("#authors_list").hide();
          }
            break;
          default:
          {
            $("#authors input:eq(0)").attr("checked", "checked");
            $("#authors_list").hide();
          }
            break;
        }
      }

      if (responce.author !== undefined) {
        var authors_str = responce.author.join("\n");
        $("#authors_list textarea").val(authors_str);
      }
      if (responce.random_age !== undefined) {
        $("#age_diap input").removeAttr("checked");
        switch (responce.random_age) {
          case 0:
          {
            $("#age_diap input:eq(0)").attr("checked", "checked");
            $("#authors_age").hide();
          }
            break;
          case 1:
          {
            $("#age_diap input:eq(1)").attr("checked", "checked");
          }
            break;
          default:
          {
            $("#age_diap input:eq(0)").attr("checked", "checked");
            $("#authors_age").hide();
          }
            break;
        }
      }
      else {
        $("#authors_age").hide();
      }

      if (responce.from_age !== undefined && responce.to_age !== undefined) {
        $("#authors_age input:eq(0)").val(responce.from_age);
        $("#authors_age input:eq(1)").val(responce.to_age);
      }
      if (responce.gender !== undefined) {
        $("#authors_sex input").removeAttr("checked");

        switch (responce.gender) {
          case 0:
          {
            $("#authors_sex input:eq(0)").attr("checked", "checked");
          }
            break;
          case 1:
          {
            $("#authors_sex input:eq(1)").attr("checked", "checked");
          }
            break;
          case 2:
          {
            $("#authors_sex input:eq(2)").attr("checked", "checked");
          }
            break;
          default:
          {
            $("#authors_sex input:eq(0)").attr("checked", "checked");
          }
            break;
        }
      }
      if (responce.cou !== undefined) {
        var arr = responce.cou;
        if (first_load == 0) {
          var def = ["loc_Россия", "loc_зарубежье"];
          first_load = 1;
        }
        else {
          var def = new Array();
          $(".loc_span").each(function () {
            def.push("loc_" + $(this).text());
          });
        }
        $("#countries_cities input").removeAttr("checked");
        for (var i = 0; i < arr.length; i++) {
          var found = 0;
          for (var j = 0; j < def.length; j++) {
            if (arr[i] == def[j]) {
              $("#countries_cities input:eq(" + j + ")").attr("checked", "checked");
              found = 1;
            }
          }
          if (found == 0) {
            var place = arr[i].substr(4);
            $("#add_country_city").before("<div class=\"check-block\"><input type=\"checkbox\" name=\"countries_cities\" checked=\"checked\"> <span class=\"loc_span\">" + place + "</span><button class=\"delete_loc\"></button></div>");
            $(".delete_loc").button({
              icons: {
                primary: "ui-icon-trash"
              },
              text: false,
              disabled: false
            }).css("top", "5px").css("height", "24px").css("margin-left", "10px").click(function () {
                $(this).parent().remove();
              });
          }
        }
      }

      if (responce.remove_spam !== undefined) {
        $("#spam input").removeAttr("checked");
        switch (responce.remove_spam) {
          case 0:
          {
            $("#spam input:eq(0)").attr("checked", "checked");
          }
            break;
          case 1:
          {
            $("#spam input:eq(1)").attr("checked", "checked");
          }
            break;
          case 2:
          {
            $("#spam input:eq(2)").attr("checked", "checked");
          }
            break;
          default:
          {
            $("#spam input:eq(0)").attr("checked", "checked");
          }
            break;
        }
      }
      if (responce.auto_nastr !== undefined) {
        $("#autotone input").removeAttr("checked");
        switch (parseInt(responce.auto_nastr)) {
          case 0:
          {
            $("#autotone input:eq(0)").attr("checked", "checked");
          }
            break;
          case 1:
          {
            $("#autotone input:eq(1)").attr("checked", "checked");
            $("#tone_object").show();
            $("#tone_type").show();
            if(responce.tone_type !== undefined){
              $("#tone_type input:eq("+responce.tone_type+")").attr("checked", "checked");
            }
            if(responce.tone_object !== undefined){
              var prepare = responce.tone_object.split("|").join(",");
              $("#tone_object textarea").val(prepare);
              $("#tone_object textarea").keyup();
            }
          }
            break;
          case 2:
          {
            $("#autotone input:eq(2)").attr("checked", "checked");
            $("#tone_object").show();
            $("#tone_type").show();
            if(responce.tone_type !== undefined){
              $("#tone_type input:eq("+responce.tone_type+")").attr("checked", "checked");
            }
            if(responce.tone_object !== undefined){
              var prepare = responce.tone_object.split("|").join(",");
              $("#tone_object textarea").val(prepare);
              $("#tone_object textarea").keyup();
            }
          }
            break;
          default:
          {
            $("#autotone input:eq(0)").attr("checked", "checked");
          }
            break;
        }
      }
      if (responce.include_groups !== undefined) {
        switch (responce.include_groups) {
          case 0:
          {
            $("#autogroups input").removeAttr("checked");
            groups_unchecked = 1;
          }
            break;
          case 1:
          {
            $("#autogroups input").attr("checked", "checked");
          }
            break;
          default:
          {
            $("#autogroups input").attr("checked", "checked");
          }
            break;
        }
      }
    }
    else {
      var save_errors = responce.status;
      var error_str = "";
      if (save_errors == 1) {
        error_str += "не передан id пользователя, "
      }
      if (save_errors == 2) {
        error_str += "отчет не принадлежит пользователю, "
      }
      var first_leter = error_str.slice(0, 1).toUpperCase();
      error_str = first_leter + error_str.slice(1).slice(0, -2) + ".";
      $(".error_error h3").text("Ошибка при получении данных!");
      $(".error_error p").text(error_str);
      showMessage(myMessages[2]);
    }
  });
}

function sendThemeSettings() {
  if (checkThemeData() == "") {
    var data = collectFormData(id, REG_TYPE);
    if ($.cookie("tarif_id") == 3) {
      data.disable_theme = 1;
    }
    saveThemeSettings(data);
  }
  else {
    console.log("ошибка ввода!");
  }
}

function saveThemeSettings(data, xxx_load) {
  $.postJSON(ajaxURL_saveThemeSettings, data, function (responce) {
    if (id !== undefined && xxx_load != 1) {
      loadThemeSettings(id);
      $(window).scrollTop("80px");
    }
    if (responce.errors === undefined && xxx_load != 1) {
      $(".error_success h3").text("Сохранено");
      $(".error_success p").text("Настройки темы успешно сохранены!");
      showMessage(myMessages[3]);
    }
    else {
      var save_errors = responce.errors;
      var error_str = "";
      for (var i = 0; i < save_errors.length; i++) {
        if (save_errors[i] == 1) {
          error_str += "не передан order_id, "
        }
        if (save_errors[i] == 21) {
          error_str += "не правильно заданы возможные слова, "
        }
        if (save_errors[i] == 22) {
          error_str += "не правильно заданы обязательные слова, "
        }
        if (save_errors[i] == 22) {
          error_str += "не правильно заданы стоп слова, "
        }
        if (save_errors[i] == 3) {
          error_str += "превышено максимальное число постов в выдаче, "
        }
        if (save_errors[i] == 4) {
          error_str += "название темы меньше 3х символов, "
        }
        if (save_errors[i] == 5) {
          error_str += "не правильный возрастной интервал, "
        }
        if (save_errors[i] == 6) {
          error_str += "отчет не принадлежит пользователю, "
        }
        if (save_errors[i] == 7) {
          error_str += "не корректный запрос, "
        }
        if (save_errors[i] == 8) {
          error_str += "не корректные ссылки на пользователей, "
        }
      }
      var first_leter = error_str.slice(0, 1).toUpperCase();
      error_str = first_leter + error_str.slice(1).slice(0, -2) + ".";
      $(".error_error h3").text("Ошибка при сохранении данных!");
      $(".error_error p").text(error_str);

      $(document.body).animate({
        'scrollTop': $('#body').offset().top
      }, 1000);
      showMessage(myMessages[2]);
    }
  });
}

function checkThemeData() {
  var error = "";
  $("input, textarea").removeAttr("style");
  $(".error").hide();
  if ($("#theme_name input").val().length < 3) {
    $("#theme_name input").css("border", "1px solid red");
    $("#name_error").show();
    error += "1,";
  }
  if (
      ($("#advance_key_words input").val() == "" && $("#advance_key_words textarea").val() == "")
      && ($("#key_words textarea").val() == "" && $("#additional_key_words textarea").val() == "")
    )
  {
    $("#key_words textarea").css("border", "1px solid red");
    $("#advance_key_words textarea").css("border", "1px solid red");
    $("#additional_key_words textarea").css("border", "1px solid red");
    $("#words_error").show();
    $("#adv_words_error").show();
    $("#add_words_error").show();
    error += "2,";
  }
  var from = $("#authors_age input:eq(0)").val();
  var to = $("#authors_age input:eq(1)").val();
  var random_age = $("#age_diap input:eq(1)").attr("checked");
  if ((from.search(/^[0-9]+$/) || to.search(/^[0-9]+$/)) && random_age == "checked") {
    $("#authors_age input:eq(0)").css("border", "1px solid red");
    $("#authors_age input:eq(1)").css("border", "1px solid red");
    $("#age_num_error").show();
    error += "3,";
  }
  if (parseInt(from) > parseInt(to)) {
    $("#authors_age input:eq(0)").css("border", "1px solid red");
    $("#authors_age input:eq(1)").css("border", "1px solid red");
    $("#age_val_error").show();
    error += "4,";
  }
  var tone_checked = ($("#autotone input:eq(1)").is(":checked") || $("#autotone input:eq(2)").is(":checked")) ? 1 : 0;
  if(tone_checked){
    var obj = $("#tone_object textarea").val();
    if(obj == ""){
      error += "5,";
      $("#tone_obj_error").text("На введены объекты тональности");
      $("#tone_obj_error").show();
      return error;
    }
    var obj_arr = obj.split(",");
    if(obj_arr.length>10){
      $("#tone_obj_error").text("Количествео объектов больше десяти");
      $("#tone_obj_error").show();
      error += "6,";
      return error;
    }
    var re = new RegExp('[а-яА-Яa-zA-Z_ \-\"#@№\$%ё]+','i');
    for (var i = 0; i < obj_arr.length; i++) {

      if(trim(obj_arr[i])=="" || !re.test(obj_arr[i])){
        $("#tone_obj_error").text("Ошибка при вводе объектов тональности");
        $("#tone_obj_error").show();
        error +="7,";
        break;
      }
    }
  }

  return error;
}

function trim( str, charlist ) {  
  charlist = !charlist ? ' \s\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
  var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
  return str.replace(re, '');
}


function collectFormData(id, req_type) {
  var radios= {'monintoring': 0, 'authors' : 0, 'authors_sex' : 0, 'spam' : 0, 'autotone' : 0, 'active' : 0, 'age_diap' : 0, 'tone_type': 0};
  var locations = [];
  var cases = ["all", "except", "only"];

  $.each(radios, function(name, value){
    for (var i = 0; i < $('#' + name + " input").length; i++) {
      if ($('#' + name + " input:eq(" + i + ")").attr("checked") == "checked") {
        radios[name] = i;
      }
    }
  });

  $("#countries_cities .check-block").each(function(){
    if( $('input', $(this)).is(':checked') )
    {
      locations.push('loc_' + $('span', $(this)).text());
    }
  });

  var temp_mkw = $("#advance_key_words textarea").val();
  var temp_mko = $("#advance_key_words input").val();
  var temp_mw = "";
  var temp_mnw = "";
  var temp_mew = "";
  if (req_type == "simple") {
    temp_mkw = ""; //сложный запрос
    temp_mko = ""; //сложный запрос
    temp_mw = $("#key_words textarea").val(); //список необходимых слов
    temp_mnw = $("#additional_key_words textarea").val(); //список возможных слов
    temp_mew = $("#stop_words textarea").val(); //список стоп слов
  }

  var include_groups = $("#autogroups input").is(":checked") ? 1 : 0;
  if(radios['autotone']==1 || radios['autotone']==2){
    var temp_tone_object = $("#tone_object textarea").val();
    var temp_tone_type = radios['tone_type'];
    var parts = temp_tone_object.split(",");
    for(var i=0; i<parts.length; i++){
      parts[i] = trim(parts[i]);
    }
    temp_tone_object = parts.join("|");
  }
  return {
    order_id: id,
    remove_spam: radios['spam'], //1 - да, 0 - нет
    from_age: $("#authors_age input:eq(0)").val(),
    to_age: $("#authors_age input:eq(1)").val(),
    auto_nastr: radios['autotone'],
    mkw: temp_mkw,
    mko: temp_mko,
    mnw: temp_mnw,
    mw: temp_mw,
    mew: temp_mew,
    order_name: $("#theme_name input").val(),
    loc: locations.join(","),
    res: $("#space_list textarea").val().split("\n").join(","),
    res_type: cases[radios['monintoring']],
    authors: $("#authors_list textarea").val().split("\n").join(","),
    author_type: cases[radios['authors']],
    gender: radios['authors_sex'], //1- ж, 2- м
    disable_theme: radios['active'],
    random_age: radios['age_diap'],
    include_groups: include_groups,
    tone_object: temp_tone_object,
    tone_type: temp_tone_type
  };
}

function createTheme() {
  if (checkThemeData() == "") {
    var start = $("#dialog-newTheme-datepicker1").val();
    var end = $("#dialog-newTheme-datepicker2").val();
    var theme = $("#theme_name input").val();
    var got_id;
    if (REG_TYPE == "simple") {
      var temp_mkw = ""; //сложный запрос
      var temp_mko = ""; //сложный запрос
      var temp_mw = $("#key_words textarea").val(); //список необходимых слов
      var temp_mnw = $("#additional_key_words textarea").val(); //список возможных слов
      var temp_mew = $("#stop_words textarea").val(); //список стоп слов
    }
    else {
      var temp_mkw = $("#advance_key_words textarea").val();
      var temp_mko = $("#advance_key_words input").val();
      var temp_mw = "";
      var temp_mnw = "";
      var temp_mew = "";
    }

    $("#saveloader").show();

    $.postJSON(ajaxURL_AddTheme, {order_name: theme, mw: temp_mw, mnw: temp_mnw, mew: temp_mew, mkw: temp_mkw, mko: temp_mko, order_start: start, order_end: end}, function (answer) {
      if (answer.status == "ok") {
        got_id = answer.order_id;
        var data = collectFormData(got_id, REG_TYPE);
        if ($.cookie("tarif_id") == 3) {
          data.disable_theme = 1;
        }
        saveThemeSettings(data, 1);
        saveTag(got_id, tags[tagssave]);
      }
      else {
        var create_error = answer.status;
        var error_str = "";
        if (create_error == 0) {
          error_str += "длина названия меньше 3х символов, "
        }
        if (create_error == 1) {
          error_str += "начало периода больше конца, "
        }
        if (create_error == 21) {
          error_str += "неправильно заданы слова, которые могут встретится, "
        }
        if (create_error == 22) {
          error_str += "неправильно заданы необходимые слова, "
        }
        if (create_error == 23) {
          error_str += "превышено максимальное количество постов, измените поисковый запрос. "
        }
        if (create_error == 3) {
          error_str += "превышено максимальное количество постов, измените поисковый запрос. "
        }
        if (create_error == 4) {
          error_str += "превышено максимальное количество тем для тарифа, "
        }
        if (create_error == 5) {
          error_str += "тариф мониторинг, "
        }
        if (create_error == 6) {
          error_str += "неправильный сложный запрос, "
        }
        var first_leter = error_str.slice(0, 1).toUpperCase();
        error_str = first_leter + error_str.slice(1).slice(0, -2) + ".";
        $(".error_error h3").text("Ошибка при сохранении данных!");
        $(".error_error p").text(error_str);
        showMessage(myMessages[2]);
        $(document.body).animate({
          'scrollTop': $('#header').offset().top
        }, 2000);
        $("#saveloader").hide();
      }
    });
  }
  else {
    console.log("Ошибка ввода!");
  }
}

function getGroupsData(id) {
  $.postJSON(ajaxURL_groupsGet, {order_id: id}, function (responce) {
    if (responce !== null) {
      if (responce.status != 1 && responce.status != 2) {
        $("#groups").html("");
        for (var key in responce) {
          $("#groups").append("<div class=\"groups_row\"><span class=\"group_id\">" + key + "</span><div class=\"group_name\"><a target=\"_blank\" href=\"" + responce[key].link + "\">" + responce[key].name + "</a></div><div class=\"group_val\">" + responce[key].link + "</div><button class=\"group_edit\">Редактировать</button><button class=\"group_del\">Удалить</button><div class=\"clear\"></div></div>");
        }
        $(".group_edit").button({
          icons: {primary: "ui-icon-pencil"},
          text: false
        }).css("height", "24px");
        $(".group_del").button({
          icons: {primary: "ui-icon-trash"},
          text: false
        }).css("height", "24px");
        $("#add_group").button({
          icons: {primary: "ui-icon-circle-plus"},
          text: "Добавить группу"
        });
        setGroupsEvents();
      }
      else {
        $("#add_group").button({
          icons: {primary: "ui-icon-circle-plus"},
          text: "Добавить группу"
        });
        setGroupsEventsNew();
      }
    }
    else {
      $("#groups").html("");
      $("#add_group").button({
        icons: {primary: "ui-icon-circle-plus"},
        text: "Добавить группу"
      });
      setGroupsEvents();
    }
  });
}

function setGroupsEvents() {
  $(".group_save, .group_edit, .group_undo, .group_del, #add_group").unbind("click");
  $(".group_edit").click(function () {
    var gr = $(this).siblings(".group_val");
    var gr_id = $(this).siblings(".group_id").text();
    var gr_val = gr.text();
    $(this).siblings(".group_del").hide();
    $(this).hide();
    $(gr).replaceWith("<input class=\"group_input\" type=\"text>\" value=\"" + gr_val + "\"><button class=\"group_undo\">Отменить</button><button class=\"group_submit\">Применить</button>");
    $(".group_undo").button({icons: {primary: "ui-icon-arrowreturnthick-1-w"}, text: false}).css("height", "24px");
    $(".group_submit").button({icons: {primary: "ui-icon-circle-plus"}, text: false}).css("height", "24px");
    $(this).siblings(".group_undo").click(function () {
      $(this).siblings(".group_input").replaceWith("<div class=\"group_val\">" + gr_val + "</div>");
      $(this).siblings(".group_undo, .group_submit").remove();
      $(this).siblings(".group_del").show();
      $(this).siblings(".group_edit").show();
      $(this).remove();
    });
    $(this).siblings(".group_submit").click(function () {
      var edit_gr_val = $(this).siblings(".group_input").val();

      $.postJSON(ajaxURL_groupsEdit, {order_id: id, id: gr_id, name_group: edit_gr_val}, function (responce) {
        if (responce.status == "ok") {
          getGroupsData(id);
        }
        else {
          switch (responce.status) {
            case 1:
            {
              var error_str = "Не передан идентификатор пользователя.";
            }
              break;
            case 2:
            {
              var error_str = "Не передано идентификатор группы.";
            }
              break;
            case 3:
            {
              var error_str = "Не передана ссылка на группу.";
            }
              break;
            case 4:
            {
              var error_str = "Отчет не принадлежит пользователю.";
            }
              break;
            case 5:
            {
              var error_str = "Не правильно задано название группы.";
            }
              break;
            default:
            {
              var error_str = "Неизвестная ошибка.";
            }
              break;
          }
          $(".error_error h3").text("Ошибка при редактирования группы!");
          $(".error_error p").text(error_str);
          showMessage(myMessages[2]);
        }
      });
    });
  });
  $(".group_del").click(function () {
    var gr = $(this).siblings(".group_val");
    var gr_id = $(this).siblings(".group_id").text();
    dialog_win = $('<div id="sharerdialog">Вы действительно хотите удалить группу?</div>')
      .dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        title: '',
        position: ['center', 150],
        width: 450,
        buttons: {
          "Отмена": function () {
            dialog_win.dialog('close');
          },
          "Удалить": function () {
            dialog_win.dialog('close');
            $.postJSON(ajaxURL_groupsDel, {order_id: id, id: gr_id}, function (responce) {
              if (responce.status == "ok") {
                getGroupsData(id);
              }
              else {
                switch (responce.status) {
                  case 1:
                  {
                    var error_str = "Не передан идентификатор пользователя.";
                  }
                    break;
                  case 2:
                  {
                    var error_str = "Отчет не принадлежит пользователю.";
                  }
                    break;
                  default:
                  {
                    var error_str = "Неизвестная ошибка.";
                  }
                    break;
                }
                $(".error_error h3").text("Ошибка при удалении группы!");
                $(".error_error p").text(error_str);
                showMessage(myMessages[2]);
              }
            });
          }
        }
      });
    dialog_win.dialog('open');

  });
  $("#add_group").click(function () {
    if (adding_group == 1) {
      return false;
    }
    $("#groups").append("<div><input class=\"group_input\" type=\"text>\"><button class=\"group_undo\">Отменить</button><button class=\"group_save\">Сохранить</button></div>");
    $(".group_undo:last").button({icons: {primary: "ui-icon-arrowreturnthick-1-w"}, text: false}).css("height", "24px");
    $(".group_save").button({icons: {primary: "ui-icon-check"}, text: false}).css("height", "24px");
    $(".group_undo:last").click(function () {
      $(this).parent("div").remove();
      adding_group = 0;
    });
    adding_group = 1;
    $(".group_save:last").click(function () {
      var new_gr = $(this).siblings(".group_input").val();
      if (new_gr != "") {
        $.postJSON(ajaxURL_groupsAdd, {order_id: id, groups: new_gr}, function (responce) {
          if (responce.status == "ok") {
            getGroupsData(id);
            adding_group = 0;
          }
          else {
            switch (responce.status) {
              case 1:
              {
                var error_str = "Не передан идентификатор пользователя.";
              }
                break;
              case 2:
              {
                var error_str = "Не передано называние группы.";
              }
                break;
              case 3:
              {
                var error_str = "Отчет не принадлежит пользователю.";
              }
                break;
              case 4:
              {
                var error_str = "Не правильно задано название группы.";
              }
                break;
              default:
              {
                var error_str = "Неизвестная ошибка.";
              }
                break;
            }
            $(".error_error h3").text("Ошибка при добавлении группы!");
            $(".error_error p").text(error_str);
            showMessage(myMessages[2]);
          }
        });
      }
    });
  });
}

function setGroupsEventsNew() {
  $(".group_save, .group_edit, .group_undo, .group_del, #add_group").unbind("click");
  $(".group_edit").click(function () {
    var gr = $(this).siblings(".group_val");
    var gr_val = gr.text();
    $(this).siblings(".group_del").hide();
    $(this).hide();
    $(gr).replaceWith("<input class=\"group_input\" type=\"text>\" value=\"" + gr_val + "\"><button class=\"group_undo\">Отменить</button><button class=\"group_submit\">Применить</button>");
    $(".group_undo").button({icons: {primary: "ui-icon-arrowreturnthick-1-w"}, text: false}).css("height", "24px");
    $(".group_submit").button({icons: {primary: "ui-icon-circle-plus"}, text: false}).css("height", "24px");
    $(this).siblings(".group_undo").click(function () {
      $(this).siblings(".group_input").replaceWith("<div class=\"group_val\">" + gr_val + "</div>");
      $(this).siblings(".group_undo, .group_submit").remove();
      $(this).siblings(".group_del").show();
      $(this).siblings(".group_edit").show();
      $(this).remove();
    });
    $(this).siblings(".group_submit").click(function () {
      var edit_gr_val = $(this).siblings(".group_input").val();
      var sub = this;
      $.postJSON(ajaxURL_groupsAdd, {groups: edit_gr_val}, function (responce) {
        if (responce.status == "ok") {
          $(sub).siblings(".group_input").replaceWith("<div class=\"group_val\">" + edit_gr_val + "</div>");
          $(sub).siblings(".group_name").replaceWith("<div class=\"group_name\"><a target=\"_blank\" href=\"" + edit_gr_val + "\">" + responce.name + "</a></div>");
          $(sub).siblings(".group_undo, .group_submit").remove();
          $(sub).siblings(".group_del").show();
          $(sub).siblings(".group_edit").show();
          $(sub).remove();
        }
        else {
          switch (responce.status) {
            case 1:
            {
              var error_str = "Не передан идентификатор пользователя.";
            }
              break;
            case 2:
            {
              var error_str = "Не передано идентификатор группы.";
            }
              break;
            case 3:
            {
              var error_str = "Не передана ссылка на группу.";
            }
              break;
            case 4:
            {
              var error_str = "Отчет не принадлежит пользователю.";
            }
              break;
            case 5:
            {
              var error_str = "Не правильно задано название группы.";
            }
              break;
            case "invalid":
            {
              var error_str = "Не правильно задано название группы.";
            }
              break;
            default:
            {
              var error_str = "Неизвестная ошибка.";
            }
              break;
          }
          $(".error_error h3").text("Ошибка при редактирования группы!");
          $(".error_error p").text(error_str);
          showMessage(myMessages[2]);
        }
      });
    });
  });
  $(".group_del").click(function () {
    var del_obj = this;
    dialog_win = $('<div id="sharerdialog">Вы действительно хотите удалить группу?</div>')
      .dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        title: '',
        position: ['center', 150],
        width: 450,
        buttons: {
          "Отмена": function () {
            dialog_win.dialog('close');
          },
          "Удалить": function () {
            dialog_win.dialog('close');
            if ($(del_obj).siblings(".groups_auto")) {
              $("#showGroups").show();
            }
            $(del_obj).parent("div").remove();
            if ($(".groups_auto").length == 0) {
              groupsLoaded = 0;
            }
          }
        }
      });
    dialog_win.dialog('open');
  });
  $("#add_group").click(function () {
    $("#groupsError").hide();
    if (adding_group == 1) {
      return false;
    }
    $("#groups").append("<div><input class=\"group_input\" type=\"text>\"><button class=\"group_undo\">Отменить</button><button class=\"group_save\">Сохранить</button></div>");
    $(".group_undo:last").button({icons: {primary: "ui-icon-arrowreturnthick-1-w"}, text: false}).css("height", "24px");
    $(".group_save").button({icons: {primary: "ui-icon-check"}, text: false}).css("height", "24px");
    $(".group_undo:last").click(function () {
      $(this).parent("div").remove();
      adding_group = 0;
    });
    adding_group = 1;
    $(".group_save:last").click(function () {
      var new_gr = $(this).siblings(".group_input").val();

      if (new_gr != "") {
        $.postJSON(ajaxURL_groupsAdd, {groups: new_gr}, function (responce) {
          if (responce.status == "ok") {
            $(".group_save:last").siblings(".group_undo").remove();
            $(".group_save:last").siblings(".group_input").replaceWith('<div class=\"group_name\"><a target=\"_blank\" href=\"' + new_gr + '\">' + responce.groups[0].name + '</a></div><div class="group_val">' + new_gr + '</div><button class="group_edit"></button><button class="group_del"></button>');
            $(".group_save:last").remove();
            $(".group_edit").button({
              icons: {primary: "ui-icon-pencil"},
              text: false
            }).css("height", "24px");
            $(".group_del").button({
              icons: {primary: "ui-icon-trash"},
              text: false
            }).css("height", "24px");
            adding_group = 0;
            setGroupsEventsNew();
          }
          else {
            switch (responce.status) {
              case 1:
              {
                var error_str = "Не передан идентификатор пользователя.";
              }
                break;
              case 2:
              {
                var error_str = "Не передано называние группы.";
              }
                break;
              case 3:
              {
                var error_str = "Отчет не принадлежит пользователю.";
              }
                break;
              case 4:
              {
                var error_str = "Не правильно задано название группы.";
              }
                break;
              case "invalid":
              {
                var error_str = "Не правильно задано название группы.";
              }
                break;
              default:
              {
                var error_str = "Неизвестная ошибка.";
              }
                break;
            }
            $(".error_error h3").text("Ошибка при добавлении группы!");
            $(".error_error p").text(error_str);
            showMessage(myMessages[2]);
          }
        });
      }
    });
  });
}

function getQueryGroups() {
  var temp_mw = $("#key_words textarea").val();
  var temp_mnw = $("#additional_key_words textarea").val()
  var temp_mew = $("#stop_words textarea").val();
  var temp_mkw = $("#advance_key_words textarea").val();
  if (temp_mnw != "" && temp_mkw == "" && temp_mw != "") {
    var mnw = temp_mnw;
    var mkw = "";
    if (temp_mw != "") {
      var mw = temp_mw;
    }
    else {
      var mw = "";
    }
    if (temp_mew != "") {
      var mew = temp_mew;
    }
    else {
      var mew = "";
    }
    $.postJSON(ajaxURL_groupsSearch, {mew: mew, mw: mw, mnw: mnw, mkw: mkw}, function (responce) {
      var data_arr = new Array();
      if (responce.groups !== null) {
        for (var key in responce.groups) {
          data_arr.push(responce.groups[key].link);
        }
        $.postJSON(ajaxURL_groupsAdd, {order_id: id, groups: data_arr.join(",")}, function (responce) {
          if (responce.status == "ok") {
            getGroupsData(id);
          }
          else {
            switch (responce.status) {
              case 1:
              {
                var error_str = "Не передан идентификатор пользователя.";
              }
                break;
              case 2:
              {
                var error_str = "Не передано называние группы.";
              }
                break;
              case 3:
              {
                var error_str = "Отчет не принадлежит пользователю.";
              }
                break;
              case 4:
              {
                var error_str = "Не правильно задано название группы.";
              }
                break;
              default:
              {
                var error_str = "Неизвестная ошибка.";
              }
                break;
            }
            $(".error_error h3").text("Ошибка при добавлении группы!");
            $(".error_error p").text(error_str);
            showMessage(myMessages[2]);
          }
        });
      }
    });
  }
  else if (temp_mkw != "") {
    var mkw = temp_mkw;
    var mnw = "", mw = "", mew = "";
    $.postJSON(ajaxURL_groupsSearch, {mew: mew, mw: mw, mnw: mnw, mkw: mkw}, function (responce) {
      var data_arr = new Array();
      if (responce.groups !== null) {
        for (var key in responce.groups) {
          data_arr.push(responce.groups[key].link);
        }
        $.postJSON(ajaxURL_groupsAdd, {order_id: id, groups: data_arr.join(",")}, function (responce) {
          if (responce.status == "ok") {
            getGroupsData(id);
          }
          else {
            switch (responce.status) {
              case 1:
              {
                var error_str = "Не передан идентификатор пользователя.";
              }
                break;
              case 2:
              {
                var error_str = "Не передано называние группы.";
              }
                break;
              case 3:
              {
                var error_str = "Отчет не принадлежит пользователю.";
              }
                break;
              case 4:
              {
                var error_str = "Не правильно задано название группы.";
              }
                break;
              default:
              {
                var error_str = "Неизвестная ошибка.";
              }
                break;
            }
            $(".error_error h3").text("Ошибка при добавлении группы!");
            $(".error_error p").text(error_str);
            showMessage(myMessages[2]);
          }
        });
      }
    });
  }
  else {
    console.log("No data");
  }
}

function saveQueryGroups(mnw, mw, mew, mkw, got_id) {
  $.postJSON(ajaxURL_groupsSearch, {mew: mew, mw: mw, mnw: mnw, mkw: mkw}, function (responce) {
    var data_arr = new Array();
    if (responce.groups !== null) {
      for (var key in responce.groups) {
        data_arr.push(responce.groups[key].link);
      }
      $.postJSON(ajaxURL_groupsAdd, {order_id: got_id, groups: data_arr.join(",")}, function (responce) {
        if (responce.status == "ok") {
          window.location.reload("theme_edit.html#" + got_id);
        }
        else {
          switch (responce.status) {
            case 1:
            {
              var error_str = "Не передан идентификатор пользователя.";
            }
              break;
            case 2:
            {
              var error_str = "Не передано называние группы.";
            }
              break;
            case 3:
            {
              var error_str = "Отчет не принадлежит пользователю.";
            }
              break;
            case 4:
            {
              var error_str = "Не правильно задано название группы.";
            }
              break;
            default:
            {
              var error_str = "Неизвестная ошибка.";
            }
              break;
          }
          $(".error_error h3").text("Ошибка при добавлении группы!");
          $(".error_error p").text(error_str);
          showMessage(myMessages[2]);
        }
      });
    }
    else {
      window.location.reload("theme_edit.html#" + got_id);
    }
  });
}

function getQueryGroupsNew() {
  var temp_mw = $("#key_words textarea").val();
  var temp_mnw = $("#additional_key_words textarea").val()
  var temp_mew = $("#stop_words textarea").val();
  var temp_mkw = $("#advance_key_words textarea").val();
  if (temp_mnw != "" || temp_mw != "") {
    var mnw = temp_mnw;
    var mkw = "";
    if (temp_mw != "") {
      var mw = temp_mw;
    }
    else {
      var mw = "";
    }
    if (temp_mew != "") {
      var mew = temp_mew;
    }
    else {
      var mew = "";
    }
    $.postJSON(ajaxURL_groupsSearch, {mew: mew, mw: mw, mnw: mnw, mkw: mkw}, function (responce) {
      if (responce.groups !== null) {
        $("#showGroups").hide();
        groupsLoaded = 1;
        for (var key in responce.groups) {
          $("#groups").append("<div class=\"groups_row\"><div class=\"groups_auto\"></div><span class=\"group_id\">" + key + "</span><div class=\"group_name\"><a target=\"_blank\" href=\"" + responce.groups[key].link + "\">" + responce.groups[key].name + "</a></div><div class=\"group_val\">" + responce.groups[key].link + "</div><button class=\"group_edit\">Редактировать</button><button class=\"group_del\">Удалить</button><div class=\"clear\"></div></div>");
        }
        $(".group_edit").button({
          icons: {primary: "ui-icon-pencil"},
          text: false
        }).css("height", "24px");
        $(".group_del").button({
          icons: {primary: "ui-icon-trash"},
          text: false
        }).css("height", "24px");
        $("#add_group").button({
          icons: {primary: "ui-icon-circle-plus"},
          text: "Добавить группу"
        });
        setGroupsEventsNew();
        $("#ajax-loader").hide();
      }
      else {
        dialog_win = $('<div id="sharerdialog"></div>')
          .html('<p>Группы, соответствующие ключевому запросу, не были найдены.</p>')
          .dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            title: 'Группы не найдены',
            position: ['center', 150],
            width: 450,
            buttons: {
              "Ок": function () {
                dialog_win.dialog('close');
              }
            }
          });
        dialog_win.dialog('open');
        $("#ajax-loader").hide();
      }
    });
  }
  else if (temp_mkw != "") {
    var mkw = temp_mkw;
    var mnw = "", mw = "", mew = "";
    $.postJSON("/api/0/groups_search", {mew: mew, mw: mw, mnw: mnw, mkw: mkw}, function (responce) {
      if (responce.groups !== null) {
        $("#showGroups").hide();
        groupsLoaded = 1;
        for (var key in responce.groups) {
          $("#groups").append("<div class=\"groups_row\"><div class=\"groups_auto\"></div><span class=\"group_id\">" + key + "</span><div class=\"group_name\"><a target=\"_blank\" href=\"" + responce.groups[key].link + "\">" + responce.groups[key].name + "</a></div><div class=\"group_val\">" + responce.groups[key].link + "</div><button class=\"group_edit\">Редактировать</button><button class=\"group_del\">Удалить</button><div class=\"clear\"></div></div>");
        }
        $(".group_edit").button({
          icons: {primary: "ui-icon-pencil"},
          text: false
        }).css("height", "24px");
        $(".group_del").button({
          icons: {primary: "ui-icon-trash"},
          text: false
        }).css("height", "24px");
        $("#add_group").button({
          icons: {primary: "ui-icon-circle-plus"},
          text: "Добавить группу"
        });
        setGroupsEventsNew();
        $("#ajax-loader").hide();
      }
      else {
        dialog_win = $('<div id="sharerdialog"></div>')
          .html('<p>Группы, соответствующие ключевому запросу, не были найдены.</p>')
          .dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            title: 'Группы не найдены',
            position: ['center', 150],
            width: 450,
            buttons: {
              "Ок": function () {
                dialog_win.dialog('close');
              }
            }
          });
        dialog_win.dialog('open');
        $("#ajax-loader").hide();
      }
    });
  }
  else {
    console.log("No data");
    $("#groupsError").show();
    $("#ajax-loader").hide();
  }
}

function loadGroups() {
  $("#ajax-loader").show();
  if (groupsLoaded == 0) {
    getQueryGroupsNew();

  }
  else {
    $(".groups_auto").parent("div").remove();
    getQueryGroupsNew();
  }
}

function convertToAdvancedQuery() {
  var kwd = $("#key_words textarea").val();
  var akwd = $("#additional_key_words textarea").val();
  var skwd = $("#stop_words textarea").val();
  var query = '';


  var kwdarr = kwd.split(',');
  query = kwdarr.join('|');
  query = '(' + query + ')'

  if ($.trim(akwd) != '') {
    kwdarr = akwd.split(',');
    query = query + " && " + kwdarr.join(' && ');
  }
  if ($.trim(skwd) != '') {
    kwdarr = skwd.split(',');
    query = query + " ~~ (" + kwdarr.join('|') + ")";
  }
  return query;
}

$(document).ready(function () {
  if( $.cookie("tarif_id") == 16 )
  {
    advance_key_words_length = 300;
  }
  objectSearch($('#advance_key_words textarea'), advance_key_words_length);
  calculateLeftSymbols($('#advance_key_words textarea'), advance_key_words_length);

  //Ошибки
  hideAllMessages();
  $('.error_message').click(function () {
    $(this).animate({top: -$(this).outerHeight() - 6}, 500);
  });

  //ОФОРМЛЕНИЕ
  $(".error").hide();
  $("#theme_name").keyup(function () {
    var val = $("input", this).val();
    if (val.length < 3) {
      $("#theme_name .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px -18px transparent");
    }
    else {
      $("#theme_name .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
    }
  });

  $("#tone_object").keyup(function(){
    //var tone_checked = ($("#autotone input:eq(1)").is(":checked") || $("#autotone input:eq(2)").is(":checked")) ? 1 : 0;
      var obj = $("#tone_object textarea").val();
      if(obj == ""){
        $("#tone_object .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px -18px transparent");
        return 0;
      } else {
        $("#tone_object .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
      }
      var obj_arr = obj.split(",");
      if(obj_arr.length>10){
        $("#tone_object .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px -18px transparent");
        return 0;
      } else {
        $("#tone_object .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
      }
      for (var i = 0; i < obj_arr.length; i++) {
        if(trim(obj_arr[i])==""){
          $("#tone_object .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px -18px transparent");
          return 0;
        } else {
           $("#tone_object .marker").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
        }
      }
  });

  $('#autotone input:eq(0)').click(function() {
    console.log(0);
    $("#tone_object").hide();
    $("#tone_type").hide();
  });
  $('#autotone input:eq(1)').click(function() {
    $("#tone_object").show();
    $("#tone_type").show();
    console.log(1);
  });
  $('#autotone input:eq(2)').click(function() {
    $("#tone_object").show();
    $("#tone_type").show();
    console.log(1);
  });

  $("#key_words").keyup(function () {
    var val = $("textarea", this).val();
    if (val != "") {
      $("#query_correct").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
      $("#query_correct_val").text(1);
    }
    else {
      checkEmptyQuery();
    }
  });

  $("#additional_key_words").keyup(function () {
    var val = $("textarea", this).val();
    if (val != "") {
      $("#query_correct").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
      $("#query_correct_val").text(1);
    }
    else {
      checkEmptyQuery();
    }
  });


  $("#advance_key_words").keyup(function () {
    adv_keys_changed = 1;
    var val = $("textarea", this).val();
    if (val != "") {
      $("#query_correct").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px 2px transparent");
      $("#query_correct_val").text(1);
    }
    else {
      checkEmptyQuery();
    }
  });

  function checkEmptyQuery() {
    var val1 = $("#key_words textarea").val();
    var val2 = $("#additional_key_words textarea").val();
    var val3 = $("#advance_key_words textarea").val();
    if (val1 == "" && val2 == "" && val3 == "") {
      $("#query_correct").css("background", "url(\"../img/markers_sprite.png\") no-repeat scroll 2px -18px transparent");
      $("#query_correct_val").text(0);
    }
  }

  /*TODO: алгоритм
   если key_words textarea. empty?
   {ниче не делаем}
   иначе если ey_words textarea. not empty?
   {функция преобразования (взять у вовы)
   */


  /*TODO: алгоритм
   если flag_advance_changed && not empty
   {предупреждение что запрос будет потерян, вернуться и все стереть }
   иначе если flag_advance_not_changed
   {вернуться в режим}
   */

  //TODO: сделать при сохранении темы, чтобы если оба запроса заполнены, только advanced брался


  $("#request_type input:eq(0)").click(function () {
    if ($(this).attr("checked") == "checked") {
      if (adv_keys_changed == 1 && $.trim($("#advance_key_words textarea").val()) != '') {
        dialog_losekw = $('<div id="sharerdialog">Ключевой запрос будет потерян. Продолжить?</div>')
          .dialog({
            autoOpen: false,
            modal: true,
            resizable: false,
            title: '',
            position: ['center', 150],
            width: 450,
            buttons: {
              "Продолжить": function () {
                $("#advance_key_words").hide();
                $("#advance_key_words textarea").val('');
                $("#key_words textarea").val('');
                $("#key_words").show();
                $("#additional_key_words textarea").val('');
                $("#additional_key_words").show();
                $("#stop_words textarea").val('');
                $("#stop_words").show();
                adv_keys_changed = 0;
                dialog_losekw.dialog('close');
              },
              "Отменить": function () {
                dialog_losekw.dialog('close');
              }}});
        dialog_losekw.dialog('open');
      }
      else {
        $("#advance_key_words").hide();
        $("#key_words").show();
        $("#additional_key_words").show();
        $("#stop_words").show();
        REG_TYPE = "simple";
      }
    }
  });

  $("#request_type input:eq(1)").click(function () {
    if ($(this).attr("checked") == "checked") {

      if ($.trim($("#key_words textarea").val()) != '') {
        $("#advance_key_words textarea").val(convertToAdvancedQuery());
      }
      $("#advance_key_words").show();
      $("#key_words").hide();
      $("#additional_key_words").hide();
      $("#stop_words").hide();
      REG_TYPE = "complex";
    }
  });

  $("#age_diap input:eq(0)").click(function () {
    if ($(this).attr("checked") == "checked") {
      $("#authors_age").hide();
    }
  });

  $("#age_diap input:eq(1)").click(function () {
    if ($(this).attr("checked") == "checked") {
      $("#authors_age").show();
    }
  });

  $("#monintoring input").click(function () {
    var curr = $(this).index("#monintoring input");
    switch (curr) {
      case 0:
      {
        $("#space_list").hide();
      }
        break;
      case 1:
      {
        $("#space_list").show();
      }
        break;
      case 2:
      {
        $("#space_list").show();
      }
        break;
      default:
      {
        $("#space_list").hide();
      }
        break;
    }
  });

  $("#authors input").click(function () {
    var curr = $(this).index("#authors input");
    switch (curr) {
      case 0:
      {
        $("#authors_list").hide();
      }
        break;
      case 1:
      {
        $("#authors_list").hide();
      }
        break;
      case 2:
      {
        $("#authors_list").hide();
      }
        break;
      default:
      {
        $("#authors_list").hide();
      }
        break;
    }
  });

  $(".quest_marker").attr("href", "http://wobot.ru/faq/#1_5");

  $("#addtagform").hide();

  $("#addautotag").change(function () {
    $(".hidden").toggle();
  });
  $("#addradio1").change(function () {
    $(".hidden").show();
    $('#addtagform input[name="autotag_type"]').trigger('change');
  });

  $('input[name="autotag_type"]').change(function(){
    var val = $('input[name="autotag_type"]:checked').val();
    if( val == 'simple' )
    {
      $('#akw').parent().hide();
      $('#kwrd').parent().show();
      $('#swrd').parent().show();
    }
    else
    {
      $('#akw').parent().show();
      var new_val = convertQueryToAdvanced($('#kwrd').val(), $('#swrd').val());
      if( $.trim(new_val) != '' )
      {
        $('#akw').val(new_val);
      }
      $('#kwrd').parent().hide();
      $('#swrd').parent().hide();
    }
  });
  $("#addradio2").change(function () {
    $(".hidden").hide();
  });
  $("#canceltag").button({
    icons: {primary: "ui-icon-arrowreturnthick-1-w"},
    text: "Отменить"
  }).click(function () {
      clearAddErrors();
      $("#addtagform").slideUp('fast');
      $("#addname").val('');
      if ($("#addautotag").is(":checked"))
      {
        $("#addautotag").click();
      }
      $("#kwrd").val('');
      $("#swrd").val('');
      $("#akw").val('');
      $("#savetag").hide();
      $("#canceltag").hide();
      $("#addtag").show();
    });

  $("#addCC").button({
    icons: {primary: "ui-icon-circle-plus"},
    disabled: false,
    text: "Добавить"
  }).click(function () {
      var loc = $("#add_country_city input").val();
      if (loc != "") {
        $("#add_country_city").before("<div class=\"check-block\"><input type=\"checkbox\" name=\"countries_cities\" checked=\"checked\"> <span class=\"loc_span\">" + loc + "</span><button class=\"delete_loc\"></button></div>");
        $(".delete_loc").button({
          icons: {
            primary: "ui-icon-trash"
          },
          text: false
        }).css("top", "5px").css("height", "24px").css("margin-left", "10px").click(function () {
            $(this).parent().remove();
          });
      }
    });

  $("#undo").button({
    icons: {primary: "ui-icon-arrowreturnthick-1-w"},
    text: "Отменить"
  }).click(function () {
      window.location.href = "themes_list.html";
    });
  $("#addtag").button({
    icons: {primary: "ui-icon-circle-plus"},
    text: "Добавить"
  }).click(function () {
      $("#savetag").show();
      $("#canceltag").show();
      $("#addtag").hide();
      $("#addtagform").slideDown('fast');
    });

  var loc = location.href.split('#');
  id = loc[loc.length - 1];

  if (loc[1] !== undefined) {

    //редактирование темы
    $("#showGroups").remove();
    //блокирование запроса и демографического фильтра
    $("#active_onoff").hide();
    $("#active").hide();
    //
    $(".layer > h3 > span").text("Редактирование темы /");
    var pr = $("#period").index(".form-block");
    $(".form-block:eq(" + (pr - 1) + ")").remove();
    $("#period").remove();

    if ($.cookie("tarif_id") == 3) {
      var pr = $("#active").index(".form-block");
      $(".form-block:eq(" + (pr - 1) + ")").remove();
      $("#active").remove();
    }

    $("#autogroups input").change(function () {
      if ($("#autogroups input").is(":checked")) {
        $("#ajax-loader").show();
        $.postJSON("/api/0/groups_search", {mew: "", mw: "", mnw: "", mkw: $("#advance_key_words textarea").val()}, function (responce) {
          var data_arr = new Array();
          if (responce.groups !== null) {
            for (var key in responce.groups) {
              data_arr.push(responce.groups[key].link);
            }
            $.postJSON(ajaxURL_groupsAdd, {order_id: id, groups: data_arr.join(",")}, function (responce) {
              if (responce.status == "ok") {

              }
              else {
                switch (responce.status) {
                  case 1:
                  {
                    var error_str = "Не передан идентификатор пользователя.";
                  }
                    break;
                  case 2:
                  {
                    var error_str = "Не передано называние группы.";
                  }
                    break;
                  case 3:
                  {
                    var error_str = "Отчет не принадлежит пользователю.";
                  }
                    break;
                  case 4:
                  {
                    var error_str = "Не правильно задано название группы.";
                  }
                    break;
                  default:
                  {
                    var error_str = "Неизвестная ошибка.";
                  }
                    break;
                }
                $(".error_error h3").text("Ошибка при добавлении группы!");
                $(".error_error p").text(error_str);
                showMessage(myMessages[2]);
              }
              getGroupsData(id);
              groups_unchecked = 0;
              $("#ajax-loader").hide();
            });
          }
          else {
            dialog_win = $('<div id="sharerdialog"></div>')
              .html('<p>Группы, соответствующие ключевому запросу, не были найдены.</p>')
              .dialog({
                autoOpen: false,
                modal: true,
                resizable: false,
                title: 'Группы не найдены',
                position: ['center', 150],
                width: 450,
                buttons: {
                  "Ок": function () {
                    dialog_win.dialog('close');
                  }
                }
              });
            dialog_win.dialog('open');
            getGroupsData(id);
            groups_unchecked = 0;
            $("#ajax-loader").hide();
          }
        });
      }
    });

    $("#save").button({
      icons: {primary: "ui-icon-check"},
      text: "Сохранить изменения"
    }).click(function () {
        sendThemeSettings();
      });

    loadContent(id);
    //загрузка данных
    loadThemeSettings(id);

    loadThemesListForSelect(id);

    $(".error_warning h3").text("Обратите внимание");
    $(".error_warning p").text('Изменения настроек темы будут влиять только на новые сообщения.');
    setTimeout(function () {
      showMessage(myMessages[1]);
    }, 1000);
  }
  else {
    is_theme_create = 1;

    $("#show_period").remove();
    $("#sharetag").remove();
    $("#show_period_head").remove();

    $(".tagstable .template").hide();
    $("#L1, #L2").hide();

    $(".layer > h3 > span").text("Создание новой темы");
    $("#page_intro").text("Пожалуйста, внесите необходимую информацию в форму ниже. Сбор упоминаний начнется сразу после сохранения настроек темы. ");
    $(".layer > h3 > a").remove();
    $(".layer > h3 > span").after('<a class="back_to_list" href="themes_list.html">(вернуться к списку тем)</a>')

    if ($.cookie("tarif_id") == 3) {
      var pr = $("#active").index(".form-block");
      $(".form-block:eq(" + (pr - 1) + ")").remove();
      $("#active").remove();
    }

    $("#save").button({
      icons: {primary: "ui-icon-check"},
      text: "Сохранить изменения"
    }).click(function () {
        createTheme();
      });

    if ($("#age_diap input:eq(0)").attr("checked") == "checked") {
      $("#authors_age").hide();
    }

    $("#showGroups").click(function () {
      $(".group_undo").trigger("click");
      $("#groupsError").hide();
      loadGroups();
      return false;
    });

    var tarif_id = $.cookie("tarif_id");
    var exp = $.cookie("user_exp");

//    var tarifs_retro = {1: 0, 2: 0, 3: 0, 4: 0, 5: 1, 6: 2, 7: 3, 8: 0, 9: 0, 10: 1, 11: 0, 12: 6, 13: 3, 14: 2, 15: 1, 16: 1};

    $.postJSON(ajaxURL_GetSettings, {}, function (data) {
      exp =  data.tarif_exp;
      var tarif_retro =  data.tariff_retro;

      var today = new Date();

      var tariffEndDate = new Date();
      tariffEndDate.setDate(tariffEndDate.getDate()+parseInt(exp));

      var tariffStartDate = new Date();
      tariffStartDate.setMonth(tariffStartDate.getMonth()-parseInt(tarif_retro));

      $("#dialog-newTheme-datepicker1").datepicker({
        dateFormat: "dd.mm.yy",
        minDate: tariffStartDate,
        maxDate: tariffEndDate,
        onSelect: function () {
          $("#dialog-newTheme-datepicker2").datepicker("option", "minDate", $(this).datepicker("getDate"));
        }
      }).datepicker("setDate", '-30d').attr("disabled", true).addClass("ui-state-disabled");

      $("#dialog-newTheme-datepicker2").datepicker({
        dateFormat: "dd.mm.yy",
        minDate: $("#dialog-newTheme-datepicker1").datepicker('getDate'),
        maxDate: tariffEndDate,
        onSelect: function () {
          $("#dialog-newTheme-datepicker1").datepicker("option", "maxDate", $(this).datepicker("getDate"));
        }
      }).datepicker("setDate", tariffEndDate).attr("disabled", true).addClass("ui-state-disabled");

      $('input[name="period_type"]').change(function(){
        if( $(this).val() == 'month' )
        {
          $("#dialog-newTheme-datepicker1").datepicker("setDate", '-30d').attr("disabled", true).addClass("ui-state-disabled");
          $("#dialog-newTheme-datepicker2").datepicker("setDate", tariffEndDate).attr("disabled", true).addClass("ui-state-disabled");
        }
        else if( $(this).val() == 'week' )
        {
          $("#dialog-newTheme-datepicker1").datepicker("setDate", '-7d').attr("disabled", true).addClass("ui-state-disabled");
          $("#dialog-newTheme-datepicker2").datepicker("setDate", tariffEndDate).attr("disabled", true).addClass("ui-state-disabled");
        }
        else if( $(this).val() == 'custom' )
        {
          $("#dialog-newTheme-datepicker1").datepicker({
            minDate: tariffStartDate,
            maxDate: tariffEndDate
          }).datepicker("setDate", '-30d').removeAttr("disabled", true).removeClass("ui-state-disabled");
          $("#dialog-newTheme-datepicker2").datepicker({
            minDate: $("#dialog-newTheme-datepicker1").datepicker('getDate'),
            maxDate: tariffEndDate
          }).datepicker("setDate", tariffEndDate).removeAttr("disabled", true).removeClass("ui-state-disabled");
        }
      });

//      $("#dialog-newTheme-datepicker1").datepicker({dateFormat: "dd.mm.yy"});
//      $("#dialog-newTheme-datepicker2").datepicker({dateFormat: "dd.mm.yy"});
  //    $("#dialog-newTheme-datepicker1").datepicker("setDate", "-" + tarifs_retro[tarif_id] + "m").css("color", "black").css("font-weight", "bold");
  //    $("#dialog-newTheme-datepicker2").datepicker("setDate", "+" + exp + "d").attr("disabled", true).addClass("ui-state-disabled").css("color", "black").css("font-weight", "bold");
    });


  }

  getGroupsData(id);

  $("#savetag").button({
    icons: {primary: "ui-icon-check"},
    text: "Ok"
  }).click(function () {
      clearAddErrors();
      user_id = $.cookie("user_id");
      var akw = $("#akw").val();
      var kwrd = $("#kwrd").val();
      var swrd = $("#swrd").val();
      var name = $("#addname").val();
      var auto = $("#addradio1").is(":checked") ? 1 : 0;
      var tagData = {};
      if( $('input[name="autotag_type"]:checked').val() == 'simple' )
      {
        tagData = {
          order_id: id,
          tag_name: name,
          user_id: user_id,
          auto: auto,
          tag_kw: kwrd,
          tag_sw: swrd
        };
      }
      else
      {
        tagData = {
          order_id: id,
          tag_name: name,
          user_id: user_id,
          auto: auto,
          tag_akw: akw
        };
      }

      if (!is_theme_create) {
        $.postJSON(ajaxURL_AddTagFull, tagData, function (responce) {
          /* status
           0 - нет имени
           1 - нет ключевых слов
           21 - неправильные ключевые слова
           22 - неправильные стоп слова
           fail - другие ошибки
           */
          //TODO: сделать чтобы если не автоматом то всеравно добавлялось без ключевых слов
          if (responce.status == 0) showAddError("addname", "введите имя тега.");
          else if (responce.status == 1 && auto == 1) showAddError("kwrd", "введите ключевые слова.");
          else if (responce.status == 21) showAddError("kwrd", "проверьте правильность ввода ключевых слов.");
          else if (responce.status == 22) showAddError("swrd", "проверьте правильность ввода стоп-слов.");
          else {
            $("#addtagform").slideUp('fast');
            $("#savetag").hide();
            $("#canceltag").hide();
            $("#addtag").show();
            loadContent(id);
            $("#addname").val('');
            $("#kwrd").val('');
            $("#swrd").val('');
            $("#akw").val('');
          }
        });
      }
      else {
        if( $('input[name="autotag_type"]:checked').val() == 'simple' )
        {
          tagData = {
            name: name,
            auto: auto,
            keywords: kwrd,
            stopwords: swrd
          };
        }
        else
        {
          tagData = {
            name: name,
            auto: auto,
            advanced_keywords: akw
          };
        }
        tags[Object.keys(tags).length] = tagData;
        $("#addtagform").slideUp('fast');
        $("#savetag").hide();
        $("#canceltag").hide();
        $("#addtag").show();
        $("#addname").val('');
        $("#kwrd").val('');
        $("#swrd").val('');
        $("#akw").val('');
        renderTags(tags);
      }
    });
  $("#savetag span, #canceltag span").css('font-size', '10px');
  $("#addradio").buttonset();
  $('#addradio span').css('font-size', '10px');

  $("#savetag").hide();
  $("#canceltag").hide();

  $('#radiotag1 span').css('font-size', '10px');

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
  $("#user_consultant").attr("href", "FOO");
  $("#user_consultant").unbind("click").click(function (e) {
    loadmodal("FOO", "75%", "75%", "iframe");
    return false;
  });

  $("#faq").attr("href", inernalURL_faq);

  $(document).bind('click', function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("ui-datepicker-calendar") && !$clicked.parents().hasClass("ui-datepicker-prev") && !$clicked.parents().hasClass("ui-datepicker-next")) {
      $("#date .dp").hide();
    }
  });

  $('#delete-button').click(function(){
    var start_delete = $("#dp-begin").datepicker("getDate").format("dd.mm.yyyy");
    var end_delete = $("#dp-end").datepicker("getDate").format("dd.mm.yyyy");

    var popup_html = '<div id="sharerdialog" class="delete-messages-popup">' +
      'Вы действительно хотите удалить все сообщения для этой темы за период с <strong>'+start_delete+'</strong> по <strong>'+end_delete+'</strong>?' +
      '<div class="radios">' +
      '<input id="delete_messages_all" type="radio" name="delete_type" value="all">'+
      '<label for="delete_messages_all">Все</label> '+
      '<input id="delete_messages_spam" type="radio" name="delete_type" value="spam" checked="checked">'+
      '<label for="delete_messages_spam">Спам</label> '+
      '</div>'+
      '</div>';
    var dialog_delete = $(popup_html)
      .dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        title: 'Удаление сообщений',
        position: ['center', 150],
        width: 450,
        buttons: {
          "Отмена": function () {
            dialog_delete.dialog('close');
          },
          "Удалить": function () {
            var post_data = {
              order_id: id,
              start: start_delete,
              end: end_delete,
              remove_spam: $('input[name="delete_type"]:checked', $('.delete-messages-popup')).val() == 'spam' ? 1 : 0
            };
            $.postJSON(ajaxURL_RemovePosts, post_data, function(responce){
              var rezult_html = '';
              dialog_delete.dialog('close');
              if( responce.status == 'ok' )
              {
                rezult_html = '<div id="sharerdialog">Сообщения  успешно удалены, сбор в теме возобновится в течении дня.</div>';
              }
              else if( responce.status == 3 )
              {
                rezult_html = '<div id="sharerdialog">Сообщения успешно удалены, однако количество сообщений в теме превышает  лимит для вашего тарифа, для продолжения сбора вам необходимо дополнительно удалить сообщения.</div>';
              }
              var dialog_rez = $(rezult_html).dialog({
                autoOpen: false,
                modal: true,
                resizable: false,
                title: 'Удаление сообщений',
                position: ['center', 150],
                width: 450,
                buttons: {
                  "Ok": function () {
                    dialog_rez.dialog('close');
                  }
                }
              });
              if( responce.status == 'ok' || responce.status == 3 )
              {
                dialog_rez.dialog('open');
              }
            });
          }
        }
      });
    dialog_delete.dialog('open');
    return false;
  });

  $("label[for='period_type_week']").tipTip({content: 'Тема будет показывать сообщения за последние 7 дней плюс сегодня.', keepAlive: true});
  $("label[for='period_type_month']").tipTip({content: 'Тема будет показывать сообщения за последние 30 дней плюс сегодня', keepAlive: true});

});

function themeNotice() {
  if ($.cookie("themeNoticeNot") != 1) {
    var mainNoticeId = $.gritter.add({
      title: 'Вы находитесь на странице аналитики по выбранной теме.',
      text: 'На этой странице представлена обзорная аналитика по выбранной теме в удобной графической ' +
        'форме. Вы можете просмотреть динамику упоминаний на графике, распределение упоминаний' +
        'по ресурсам и городам, увидеть наиболее активных авторов упоминаний.<br/>' +
        'Вы можете оперативно загрузить эту аналитику в удобном для вас формате или распечатать ее.<br/>' +
        'Чтобы изменить интервал отображения данных, выберите нужные вам даты в поле период или' +
        'выделите его на графике под динамикой упоминаний.' +
        '<a href="#" id="themeNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
      sticky: true,
      time: '',
      class_name: 'my-sticky-class',
      after_close: function (e) {
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

function convertQueryToAdvanced(word, stop_words) {
  var query = '';

  if ($.trim(word) != '') {
    var kwdarr = word.split(',');
    query = '(' + kwdarr.join('|') + ')';
  }

  if ($.trim(stop_words) != '') {
    kwdarr = stop_words.split(',');
    query = query + " ~~ (" + kwdarr.join('|') + ")";
  }
  return query;
}

function showTagEditFormByQueryType(tag_container){
  var val = $('.auto_type input[type="radio"]:checked', tag_container).val();
  if( val == 'simple' )
  {
    $('.tag_akw', tag_container).hide();
    $('.kw', tag_container).show();
    $('.sw', tag_container).show();
  }
  else
  {
    $('.tag_akw', tag_container).show();
    $('.kw', tag_container).hide();
    $('.sw', tag_container).hide();
    var new_val = convertQueryToAdvanced($('.kw input', tag_container).val(), $('.sw input', tag_container).val());
    if( $.trim(new_val) != '' )
    {
      $('.tag_akw input', tag_container).val(new_val);
    }
  }
}

function loadThemesListForSelect(id)
{
  $.postJSON(ajaxURL_Orders, {}, function (data) {
    $.each(data.orders, function (key, order) {
      if( id != order.id )
      {
        $('#theme-list').append('<option value="'+order.id+'">'+order.keyword+'</option>');
      }
    });

    createDropDown('theme-list');

    $("#sharetag .btn a").click(function () {
      $(".dropdown dd ul").toggle();
      return false;
    });

    $('#tdd-theme-list').change(function () {
//      console.log($('#theme-list').val());
      return false;
    });
  });

  $('#copy-tags').click(function(){
    if( $('#theme-list').val() > 0 )
    {
      dialog_win = $('<div id="sharerdialog">Вы действительно хотите скопировать теги?</div>')
        .dialog({
          autoOpen: false,
          modal: true,
          resizable: false,
          title: '',
          position: ['center', 150],
          width: 450,
          buttons: {
            "Отмена": function () {
              dialog_win.dialog('close');
            },
            "Копировать": function () {
              dialog_win.dialog('close');
              $.postJSON(ajaxURL_ShareTag, {old_order_id : $('#theme-list').val(), new_order_id : id}, function(responce){
                if( responce.status == 'ok' )
                {
                  loadContent(id);
                }
              });
            }
          }
        });
      dialog_win.dialog('open');
    }
    return false;
  });
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

function objectSearch(textarea, max_symbols)
{
  var parent = textarea.parents('.form-holder');
  var input = $('input', parent);
  var object_list = $('.search-object', parent);
  var selected_object = $('.selected-search-object', parent);
  var text_length = 0;
  var counter = $('.left-symbols-count', parent);
  textarea.keypress(function(){
    text_length = textarea.val().length;
  });
  textarea.keyup(function(){
    if( text_length != textarea.val().length )
    {
      text_length = textarea.val().length;
      if(text_length > 2 && text_length < 11)
      {
        setTimeout(function(){
          $.postJSON(ajaxURL_objectSearch, {query: textarea.val()}, function (data) {
            $('div', object_list).remove();
            if( data != null )
            {
              $.each(data, function(i, val){
                object_list.append('<div rel="'+val.object_id+'">'+val.object_name+'</div>');
                search_object_list[val.object_id] = val;
              });
            }
          });
        }, 100);

      }
      else
      {
        $('div', object_list).remove();
      }
    }
  });

  $(object_list).delegate('div', 'click', function(){
    var object_id = $(this).attr('rel');
    selected_object.html('<div rel="'+object_id+'">'+$(this).html()+'<div class="close">x</div></div>').show();
    input.val(object_id);
    textarea.val('');

    counter.html('Осталось символов: '+(max_symbols-parseInt(search_object_list[object_id]['length']))+'/'+max_symbols);
    if( parseInt(search_object_list[object_id]['length']) > max_symbols )
    {
      counter.html('Осталось символов: 0/'+max_symbols);
    }
  });

  $(selected_object).delegate('div', 'click', function(e){
    var object_id = $(this).attr('rel');
    if(!$(e.target).hasClass('close'))
    {
      textarea.val($(this).attr('rel'));
      textarea.val(search_object_list[object_id]['object_keyword']);
    }
    else
    {
      counter.html('Осталось символов: '+max_symbols+'/'+max_symbols);
    }
    input.val('');
    selected_object.html('').hide();
    textarea.focus();
  });
}

function calculateLeftSymbols(input, max_symbols)
{
  var parent = input.parents('.form-holder');
  var counter = $('.left-symbols-count', parent);
  counter.html('Осталось символов: '+(max_symbols-input.val().length)+'/'+max_symbols);
  if( input.val().length > max_symbols )
  {
    counter.html('Осталось символов: 0/'+max_symbols);
    input.val(input.val().substr(0, max_symbols));
  }
  input.keyup(function(){
    counter.html('Осталось символов: '+(max_symbols-input.val().length)+'/'+max_symbols);
    if( input.val().length > max_symbols )
    {
      counter.html('Осталось символов: 0/'+max_symbols);
      input.val(input.val().substr(0, max_symbols));
    }
  });

}