$(document).ready(function(){

  $(document).bind('click', function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().is("#export") && !$clicked.is("#export"))
    {
      $('#export-variant').hide();
    }
  });

  $("#export").click(function(){
    $('#export-variant').show();
  });

  $("#submit_export_form").click(function (e) {
    submitExportForm();
  });

  $('input[name=export_format]').change(function(){
    $('#export_page_list>div').hide();
    $('#export_page_list #'+$(this).val()+'_export_page_list').show();
    $('#submit_export_form').removeAttr('disabled');
    if( $(this).val() == 'xls' )
    {
      changeButtonStatus();
    }
  });

  $('#xls_export_page_list input[name=export_page]').change(function(){
    changeButtonStatus();
  });

  setDefaultExportSettings()
});

var changeButtonStatus = function()
{
  $('#submit_export_form').attr('disabled', 'disabled');
  $('#xls_export_page_list input[name=xls_export_page]:checked').each(function(){
    $('#submit_export_form').removeAttr('disabled');
  });
};

var submitExportForm = function()
{
  var export_format = $('input[name=export_format]:checked').val();
  var export_lang = $('input[name=export_lang]:checked').val();
  var export_page = {
    analytics: 0,
    mentions: 0,
    authors: 0
  };
  var proccessExport = false;

  var tarif_id = $.cookie("tarif_id");
  var exportUrl = '';
  if( export_format == 'xls' )
  {
    exportUrl = postURL_ExportXls;
    $('#xls_export_page_list input[name=xls_export_page]:checked').each(function(){
      export_page[$(this).val()] = 1;
      proccessExport = true;
    });
  }
  else if( export_format == 'doc' )
  {
    $('#doc_export_page_list input[name=doc_export_page]:checked').each(function(){
      export_page[$(this).val()] = 1;
      if( $(this).val() == 'analytics' )
      {
        exportUrl = postURL_ExportDocx;
        proccessExport = true;
      }else if( $(this).val() == 'mentions' )
      {
        exportUrl = postURL_ExportDoc;
        proccessExport = true;
      }
    });
  }
  else if( export_format == 'csv' )
  {
    $('#csv_export_page_list input[name=csv_export_page]:checked').each(function(){
      export_page[$(this).val()] = 1;
      if( $(this).val() == 'mentions' )
      {
        exportUrl = postURL_ExportMentions;
        proccessExport = true;
      }else if( $(this).val() == 'authors' )
      {
        exportUrl = postURL_ExportAuthors;
        proccessExport = true;
      }
    });
  }

  if( proccessExport )
  {
    var form = getExportForm(exportUrl, export_format, export_page, export_lang);

    if (tarif_id == 16) {
      $('body').append('<div id="dialog-demoExportAlert" title="Экспорт темы">' +
        '<div class="ui-state-highlight ui-corner-all" style="margin-top: 10px; margin-bottom: 10px; padding: 0 .7em;">' +
        '<p style="padding: 10px 0;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .7em;"></span>' +
        'Выгрузка экспорта в Демо-кабинете ограничена 100 последними сообщениями.' +
        '</p></div></div>'
      );

      $("#dialog-demoExportAlert").dialog({
        modal: true,
        buttons: {
          "Экспортировать": function () {
            $(this).dialog("close");
            form.submit();
          }
        },
        draggable: false,
        resizable: false,
        minWidth: 400,
        maxWidth: 400,
        autoOpen: false
      });

      $("#dialog-demoExportAlert").dialog("open");
    }
    else {
      form.submit();
    }
  }
};

var setDefaultExportSettings = function(){
  $('input[name=export_format]').removeAttr('checked');
  $('input[name=export_format][value="xls"]').attr('checked', 'checked');

  $('input[name=export_lang]').removeAttr('checked');
  $('input[name=export_lang][value="2"]').attr('checked', 'checked');

  $('#xls_export_page_list input[name=xls_export_page]').removeAttr('checked');
  $('#xls_export_page_list input[name=xls_export_page][value="analytics"]').attr('checked', 'checked');

  $('#doc_export_page_list input[name=doc_export_page]').removeAttr('checked');
  $('#doc_export_page_list input[name=doc_export_page][value="analytics"]').attr('checked', 'checked');

  $('#csv_export_page_list input[name=csv_export_page]').removeAttr('checked');
  $('#csv_export_page_list input[name=csv_export_page][value="mentions"]').attr('checked', 'checked');
};

var getFilterValue = function() {
  var sidebarFilterItems = [
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
  var list, filter_type, group_list, tree_val;

  // Добавляем фильтры бокового меню (mmItems)
  $.each(sidebarFilterItems, function (i, filter) {
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
    tree_val = getFilterTreeValue($("#filter_cities-tree"), id + "-cities-msg");
    params['location'] = tree_val['items'];
  }
  else {
    params['location'] = list;
  }
  group_list = $.cookie(id + "-cities-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
    tree_val = getFilterTreeValue($("#filter_cities-tree"), id + "-cities-msg");
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
    tree_val = getFilterTreeValue($("#filter_resources-tree"), id + "-resources-msg");
    params['res'] = tree_val['items'];
  }
  else {
    params['res'] = list;
  }
  group_list = $.cookie(id + "-resources-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
    tree_val = getFilterTreeValue($("#filter_resources-tree"), id + "-resources-msg");
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
};

var getFilterTreeValue = function(tree, cookieName) {
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
  return return_val = { items: checked.join(","), groups: groups.join(",") };
};

var getExportForm = function(exportUrl, export_format, export_page, export_lang) {
  $('body > form#export-form').remove();
  var filterData = getFilterValue();
  var form =
    '<form id="export-form" target="_blank" method="post" action="' + exportUrl + '">'+
    '<input type="hidden" name="order_id"  value="' + id + '" />'+
    '<input type="hidden" name="start"     value="' + $("#dp-begin").datepicker("getDate").format("dd.mm.yyyy") + '" />'+
    '<input type="hidden" name="end"       value="' + $("#dp-end").datepicker("getDate").format("dd.mm.yyyy") + '" />'+
    '<input type="hidden" name="format"    value="' + export_format + '" />'+
    '<input type="hidden" name="analytics" value="' + export_page.analytics + '" />'+
    '<input type="hidden" name="mentions"  value="' + export_page.mentions + '" />'+
    '<input type="hidden" name="authors"   value="' + export_page.authors + '" />'+
    '<input type="hidden" name="lang"      value="' + export_lang + '" />';

  $.each(filterData, function (name, value) {
    form += '<input type="hidden" name="' + name + '" value="' + value + '" />';
  });

  form += '</form>';
  form = $(form);
  $('body').append(form);
  return form;
};