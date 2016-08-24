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
  var proccessExport = false;

  var tarif_id = $.cookie("tarif_id");
  var exportUrl = '';
  if( export_format == 'xls' )
  {
    exportUrl = postURL_ExportXlsCompare;
    proccessExport = true;
  }

  if( proccessExport )
  {
    var form = getExportForm(exportUrl, export_format);

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

  $('#xls_export_page_list input[name=xls_export_page]').removeAttr('checked');
  $('#xls_export_page_list input[name=xls_export_page][value="analytics"]').attr('checked', 'checked');
};

var getFilterValue = function() {
  var sidebarFilterItems = [
    {id: "mm-words",
      api: "words",
      title: "Со словами",
      event: onSelectMMitem
    }
  ];

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
  $.each(sidebarFilterItems, function (i, filter) {
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
    tree_val = getFilterTreeValue($("#filter_cities-tree"), "theme-compare-cities-msg");
    params['location'] = tree_val['items'];
  }
  else {
    params['location'] = list;
  }
  group_list = $.cookie("theme-compare-cities-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
    tree_val = getFilterTreeValue($("#filter_cities-tree"), "theme-compare-cities-msg");
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
    tree_val = getFilterTreeValue($("#filter_resources-tree"), "theme-compare-resources-msg");
    params['res'] = tree_val['items'];
  }
  else {
    params['res'] = list;
  }
  group_list = $.cookie("theme-compare-resources-msg-group");
  if (group_list == undefined || group_list == null || group_list == "")
  {
    tree_val = getFilterTreeValue($("#filter_resources-tree"), "theme-compare-resources-msg");
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

var getExportForm = function(exportUrl, export_format) {
  $('body > form#export-form').remove();
  var filterData = getFilterValue();
  var theme_ids = $.cookie('compareThemeList');
  var form =
    '<form id="export-form" target="_blank" method="post" action="' + exportUrl + '">'+
    '<input type="hidden" name="order_ids"  value="' + theme_ids + '" />'+
    '<input type="hidden" name="start"     value="' + $("#dp-begin").datepicker("getDate").format("dd.mm.yyyy") + '" />'+
    '<input type="hidden" name="end"       value="' + $("#dp-end").datepicker("getDate").format("dd.mm.yyyy") + '" />'+
    '<input type="hidden" name="format"    value="' + export_format + '" />';


  $.each(filterData, function (name, value) {
    form += '<input type="hidden" name="' + name + '" value="' + value + '" />';
  });

  form += '</form>';
  form = $(form);
  $('body').append(form);
  return form;
};