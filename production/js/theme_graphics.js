var graph_data = {};
$(document).ready(function () {
  setDefaultDiagramSettings();

  $('#tdd-quality_property').change(function () {
    var _this = $(this);
    if (_this.attr('value') == 'post_time') {
      $('#delimiter').parent().hide();
      $('.diagram-settings label').eq(2).hide();
      $('#tdd-number_property li[value="post_tag"]').show();
    }
    else {
      $('#delimiter').parent().show();
      $('.diagram-settings label').eq(2).show();
      $('#tdd-delimiter li').show();
      $('#tdd-delimiter li[value="'+_this.attr('value')+'"]').hide();
      if( $('#tdd-number_property').attr('value') == 'post_tag' )
      {
        $('#tdd-number_property').attr('value', 'post_id')
        $('#tdd-number_property dt a').html($('#tdd-number_property li[value="post_id"] a').html());
      }
      $('#tdd-number_property li[value="post_tag"]').hide();
    }
  });

  $('#tdd-delimiter').change(function () {
    var _this = $(this);
    $('#tdd-quality_property li').show();
    $('#tdd-quality_property li[value="'+_this.attr('value')+'"]').hide();
  });

  $('.diagram-settings button').click(function () {
    if (checkSettingChange()) {
      $('#number_property option').removeAttr('selected');
      $('#number_property option[value="' + $('#tdd-number_property').attr('value') + '"]').attr('selected', 'selected');
      $('#quality_property option').removeAttr('selected');
      $('#quality_property option[value="' + $('#tdd-quality_property').attr('value') + '"]').attr('selected', 'selected');
      $('#delimiter option').removeAttr('selected');
      $('#delimiter option[value="' + $('#tdd-delimiter').attr('value') + '"]').attr('selected', 'selected');

      $.cookie(id + "-graph-number-property", $('#tdd-number_property').attr('value'));
      $.cookie(id + "-graph-quality-property", $('#tdd-quality_property').attr('value'));
      $.cookie(id + "-graph-delimiter", $('#tdd-delimiter').attr('value'));

      buildCustomDiagram();
    }
  });
});

var setDefaultDiagramSettings = function () {
  if( $.cookie(id + "-graph-number-property") == null )
  {
    $('#number_property').val('post_id');
  }
  else
  {
    $('#number_property').val($.cookie(id + "-graph-number-property"));
  }

  if( $.cookie(id + "-graph-quality-property") == null )
  {
    $('#quality_property').val('post_time');
  }
  else
  {
    $('#quality_property').val($.cookie(id + "-graph-quality-property"));
  }

  if( $.cookie(id + "-graph-delimiter") == null )
  {
    $('#delimiter').val('none');
  }
  else
  {
    $('#delimiter').val($.cookie(id + "-graph-delimiter"));
  }

  if ($('#quality_property').val() == 'post_time') {
    $('#delimiter').parent().hide();
    $('.diagram-settings label').eq(2).hide();
  }
  else {
    $('#delimiter').parent().show();
    $('.diagram-settings label').eq(2).show();
  }

  createDropDown("number_property", 160);
  createDropDown("quality_property", 80);
  createDropDown("delimiter", 80);

  $('#tdd-delimiter li[value="'+$('#tdd-quality_property').attr('value')+'"]').hide();
  $('#tdd-quality_property li[value="'+$('#tdd-delimiter').attr('value')+'"]').hide();
};

var checkSettingChange = function () {
  return $('#number_property option[selected="selected"]').val() != $('#tdd-number_property').attr('value')
    || $('#quality_property option[selected="selected"]').val() != $('#tdd-quality_property').attr('value')
    || $('#delimiter option[selected="selected"]').val() != $('#tdd-delimiter').attr('value');
};

var buildCustomDiagram = function () {
  var data = [];
  var prev_data = {};
  var params = {};
  var data_key = '';
  if ($('#quality_property').val() == 'post_time') {
    data_key = $('#number_property').val() + '_time';
    if (graph_data.hasOwnProperty(data_key)) {
      if( $('#number_property').val() == 'post_tag' )
      {
        createTimeDiagramMulti(graph_data[data_key]);
      }
      else
      {
        createTimeDiagram(graph_data[data_key]);
      }
    }
    else {
      params = getThemeParams();
      params.start = formatDate($.cookie(id + "-fromDate-theme"));
      params.end = formatDate($.cookie(id + "-toDate-theme"));
      params.md5 = '';
      params.xtype = $('#number_property').val();

      $('#graph_data_error').hide();
      $('#graph_data_loader').show();
      $.postJSON(ajaxURL_GraphData, params, function (responce) {
          if( responce.hasOwnProperty('error') )
          {
            $('#graph_data_error').show();
            $('#graph_data_loader').hide();
          }
          else
          {
            var i = 1;
            $.each(responce.data, function (index, element) {
              if (index != 'graph_type') {
                if( $('#number_property').val() == 'post_tag' )
                {
                  $.each(element, function (tag_name, value) {
                    if( !prev_data[tag_name] ){
                      prev_data[tag_name] = [];
                    }
                    prev_data[tag_name].push(value);
                  });
                }
                else
                {
                  data.push([i, element]);
                  i++;
                }
              }
            });

            if( $('#number_property').val() == 'post_tag' )
            {
              $.each(prev_data, function (tag_name, values) {
                data.push({
                  name: tag_name,
                  data: values
                });
              });
            }

            graph_data[data_key] = data;
            $('#graph_data_loader').hide();
            if( $('#number_property').val() == 'post_tag' )
            {
              createTimeDiagramMulti(graph_data[data_key]);
            }
            else
            {
              createTimeDiagram(graph_data[data_key]);
            }
          }
        }
      );
    }
  }
  else {
    if ($('#delimiter').val() == 'none') {
      data_key = $('#number_property').val() + '_' + $('#quality_property').val();
      if (graph_data.hasOwnProperty(data_key)) {
        createColumnDiagram(graph_data[data_key]);
      }
      else
      {
        params = getThemeParams();
        params.start = formatDate($.cookie(id + "-fromDate-theme"));
        params.end = formatDate($.cookie(id + "-toDate-theme"));
        params.md5 = '';
        params.ytype = $('#number_property').val();
        params.xtype = $('#quality_property').val();

        $('#graph_data_error').hide();
        $('#graph_data_loader').show();
        $.postJSON(ajaxURL_GraphData, params, function (responce) {
            if( responce.hasOwnProperty('error') )
            {
              $('#graph_data_error').show();
              $('#graph_data_loader').hide();
            }
            else
            {
              data = {categories: [], data: []};
              $.each(responce, function (index, element) {
                data.data.push([index, element == 0 ? null : element]);
                data.categories.push(index);
              });
              graph_data[data_key] = data;
              $('#graph_data_loader').hide();
              createColumnDiagram(data);
            }
          }
        );
      }
    }
    else {
      data_key = $('#number_property').val() + '_' + $('#quality_property').val() + '_' + $('#delimiter').val();
      if (graph_data.hasOwnProperty(data_key)) {
        createColumnDiagramWithSeparator(graph_data[data_key]);
      }
      else
      {
        params = getThemeParams();
        params.start = formatDate($.cookie(id + "-fromDate-theme"));
        params.end = formatDate($.cookie(id + "-toDate-theme"));
        params.md5 = '';
        params.ytype = $('#number_property').val();
        params.xtype = $('#quality_property').val();
        params.separator = $('#delimiter').val();

        $('#graph_data_error').hide();
        $('#graph_data_loader').show();
        $.postJSON(ajaxURL_GraphData, params, function (responce) {
            if( responce.hasOwnProperty('error') )
            {
              $('#graph_data_error').show();
              $('#graph_data_loader').hide();
            }
            else
            {
              var _data = {};
              data = {categories: [], data: []};
              $.each(responce, function (index, element_ar) {
                data.categories.push(index);
                $.each(element_ar, function (i, element) {
                  if (!_data.hasOwnProperty(i)) {
                    _data[i] = [];
                  }
                  _data[i].push(element == 0 ? null : element);
                });
              });
              $.each(_data, function (index, element) {
                data.data.push({name: index, data: element});
              });
              graph_data[data_key] = data;
              $('#graph_data_loader').hide();
              createColumnDiagramWithSeparator(data);
            }
          }
        );
      }
    }
  }
};

var createTimeDiagram = function (data) {
  Highcharts.setOptions({
    global: {
      useUTC: false
    }
  });

  detailChart = new Highcharts.Chart({
    title: { text: ""},
    chart: {
      marginBottom: 80,
      renderTo: 'detail-container',
      reflow: false,
      marginLeft: 40,
      marginRight: 20,
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
      enable: false
    },
    xAxis: {
      type: 'datetime',
      lineColor: '#000',
      labels: {
        formatter: function () {
          var rangestart = new Date(minDate);
          var tttext = '';

          if ($.cookie(id + "-fromDate-theme") != null) {
            rangestart = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
          }

          var rngstart = new Date((this.value - 1) * 7 * 86400000 - 86400000 * 6 + shift);
          if (graphtype == 'week') {
            if (rngstart > rangestart) {
              tttext = rngstart.format("d") + '-' + (new Date((this.value - 1) * 7 * 86400000 + shift)).format("d.mm");
            }
            else {
              tttext = rangestart.format("d") + '-' + (new Date((this.value - 1) * 7 * 86400000 + shift)).format("d.mm");
            }
          }
          else if (graphtype == 'month') {
            rngstart = new Date((this.value - 1) * 30 * 86400000 - 86400000 * 29 + shift);
            if (rngstart > rangestart) {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 30 * 86400000 + shift))).format("m.yy");
            }
            else {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 30 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'quarter') {
            rngstart = new Date((this.value - 1) * 90 * 86400000 - 86400000 * 89 + shift);
            if (rngstart > rangestart) {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 90 * 86400000 + shift))).format("m.yy");
            }
            else {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 90 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'halfyear') {
            rngstart = new Date((this.value - 1) * 180 * 86400000 - 86400000 * 179 + shift);
            if (rngstart > rangestart) {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 180 * 86400000 + shift))).format("m.yy");
            }
            else {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 180 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'hour') {
            tttext = (new Date((this.value - 1) * 3600000 + shift)).format("H:00");
          }
          else if (graphtype == 'day') {
            tttext = (new Date((this.value - 1) * 86400000 + shift)).format("d.mm");
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
    plotOptions: {
      series: {
        animation: false,
        shadow: false,
        lineWidth: 3,
        cursor: 'pointer',
        point: {
          events: {
            'click': function () {
              var pstart = new Date();
              var pend = new Date();
              var rangestart = new Date(minDate);
              if ($.cookie(id + "-fromDate-theme") != null) {
                rangestart = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
              }

              if (graphtype == 'week') {
                pstart = new Date((this.category - 1) * 86400000 * 7 + shift - 86400 * 6 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 7 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == 'month') {
                pstart = new Date((this.category - 1) * 86400000 * 30 + shift - 86400 * 29 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 30 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == "quarter") {
                pstart = new Date((this.category - 1) * 86400000 * 90 + shift - 86400 * 89 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 90 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == "halfyear") {
                pstart = new Date((this.category - 1) * 86400000 * 180 + shift - 86400 * 179 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 180 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == 'day') {
                pstart = new Date((this.category - 1) * 86400000 + shift);
                pend = new Date((this.category - 1) * 86400000 + shift);
              }
              else if (graphtype == 'hour') {
                pstart = new Date(shift);
                pend = new Date(shift);
              }

              $("#dp-begin").datepicker("setDate", pstart);
              $("#dp-end").datepicker("setDate", pend);
              $("#date #datepicker").val(dateToWords(pstart.format("dd.mm.yyyy"), true) + " - " + dateToWords(pend.format("dd.mm.yyyy"), true));
              $.cookie(id + "-fromDate-theme", pstart.getTime());
              $.cookie(id + "-toDate-theme", pend.getTime());
              $.cookie(id + "-md5", '');
              if (graphtype == 'hour') {
                window.open(inernalURL_messages + id, '_blank');
              }
              else {
                loadContent(id, pstart.format("dd.mm.yyyy"), pend.format("dd.mm.yyyy"));
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
        var tttext = '';
        if (graphtype == 'hour') {
          tttext = (new Date((this.x - 1) * 3600000 + shift)).format("H:00");
        }
        else if (graphtype == 'day') {
          tttext = (new Date((this.x - 1) * 86400000 + shift)).format("d.mm.yyyy");
        }
        var rangestart = new Date(minDate);
        if ($.cookie(id + "-fromDate-theme") != null) {
          rangestart = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
        }

        var rngstart = new Date((this.x - 1) * 86400000 * 7 + shift - 86400 * 6 * 1000);
        if (graphtype == 'week') {
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 7 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 7 + shift)).format("dd.mm.yy");
          }
        }
        else if (graphtype == 'month') {
          rngstart = new Date((this.x - 1) * 86400000 * 30 + shift - 86400 * 29 * 1000);
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 30 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 30 + shift)).format("dd.mm.yy");
          }
        }
        else if (graphtype == 'quarter') {
          rngstart = new Date((this.x - 1) * 86400000 * 90 + shift - 86400 * 89 * 1000);
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 90 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 90 + shift)).format("dd.mm.yy");
          }
        }
        else if (graphtype == 'halfyear') {
          rngstart = new Date((this.x - 1) * 86400000 * 180 + shift - 86400 * 179 * 1000);
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 180 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 180 + shift)).format("dd.mm.yy");
          }
        }
        return '<b>' + this.series.name + '</b><br/>' + this.y + ' (' + tttext + ')';
      }
    },
    series: [
      {
        name: $('#number_property option[value="'+$('#number_property').val()+'"]').text(),
        data: data,
        showInLegend: true,
        marker: {
          symbol: "circle"
        }
      }
    ],
    navigation: {
      buttonOptions: {
        enabled: false
      }
    }
  });
  $("tspan").each(function (index, element) {
    if ($(element).text() == "Highcharts.com") {
      $(element).text("");
    }
  });
};

var createTimeDiagramMulti = function (data) {
  Highcharts.setOptions({
    global: {
      useUTC: false
    }
  });

  detailChart = new Highcharts.Chart({
    title: { text: ""},
    chart: {
      marginBottom: 80,
      renderTo: 'detail-container',
      reflow: false,
      marginLeft: 40,
      marginRight: 20,
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
      enable: false
    },
    xAxis: {
      type: 'datetime',
      lineColor: '#000',
      labels: {
        formatter: function () {
          var rangestart = new Date(minDate);
          var tttext = '';

          if ($.cookie(id + "-fromDate-theme") != null) {
            rangestart = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
          }

          var rngstart = new Date((this.value - 1) * 7 * 86400000 - 86400000 * 6 + shift);
          if (graphtype == 'week') {
            if (rngstart > rangestart) {
              tttext = rngstart.format("d") + '-' + (new Date((this.value - 1) * 7 * 86400000 + shift)).format("d.mm");
            }
            else {
              tttext = rangestart.format("d") + '-' + (new Date((this.value - 1) * 7 * 86400000 + shift)).format("d.mm");
            }
          }
          else if (graphtype == 'month') {
            rngstart = new Date((this.value - 1) * 30 * 86400000 - 86400000 * 29 + shift);
            if (rngstart > rangestart) {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 30 * 86400000 + shift))).format("m.yy");
            }
            else {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 30 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'quarter') {
            rngstart = new Date((this.value - 1) * 90 * 86400000 - 86400000 * 89 + shift);
            if (rngstart > rangestart) {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 90 * 86400000 + shift))).format("m.yy");
            }
            else {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 90 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'halfyear') {
            rngstart = new Date((this.value - 1) * 180 * 86400000 - 86400000 * 179 + shift);
            if (rngstart > rangestart) {
              tttext = rngstart.format("m.yy") + '-' + (new Date(((this.value - 1) * 180 * 86400000 + shift))).format("m.yy");
            }
            else {
              tttext = rangestart.format("m.yy") + '-' + (new Date(((this.value - 1) * 180 * 86400000 + shift))).format("m.yy");
            }
          }
          else if (graphtype == 'hour') {
            tttext = (new Date((this.value - 1) * 3600000 + shift)).format("H:00");
          }
          else if (graphtype == 'day') {
            tttext = (new Date((this.value - 1) * 86400000 + shift)).format("d.mm");
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
    plotOptions: {
      series: {
        animation: false,
        shadow: false,
        lineWidth: 3,
        cursor: 'pointer',
        point: {
          events: {
            'click': function () {
              var pstart = new Date();
              var pend = new Date();
              var rangestart = new Date(minDate);
              if ($.cookie(id + "-fromDate-theme") != null) {
                rangestart = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
              }

              if (graphtype == 'week') {
                pstart = new Date((this.category - 1) * 86400000 * 7 + shift - 86400 * 6 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 7 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == 'month') {
                pstart = new Date((this.category - 1) * 86400000 * 30 + shift - 86400 * 29 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 30 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == "quarter") {
                pstart = new Date((this.category - 1) * 86400000 * 90 + shift - 86400 * 89 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 90 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == "halfyear") {
                pstart = new Date((this.category - 1) * 86400000 * 180 + shift - 86400 * 179 * 1000);
                pend = new Date((this.category - 1) * 86400000 * 180 + shift);
                if (rangestart > pstart) pstart = rangestart;
              }
              else if (graphtype == 'day') {
                pstart = new Date((this.category - 1) * 86400000 + shift);
                pend = new Date((this.category - 1) * 86400000 + shift);
              }
              else if (graphtype == 'hour') {
                pstart = new Date(shift);
                pend = new Date(shift);
              }

              $("#dp-begin").datepicker("setDate", pstart);
              $("#dp-end").datepicker("setDate", pend);
              $("#date #datepicker").val(dateToWords(pstart.format("dd.mm.yyyy"), true) + " - " + dateToWords(pend.format("dd.mm.yyyy"), true));
              $.cookie(id + "-fromDate-theme", pstart.getTime());
              $.cookie(id + "-toDate-theme", pend.getTime());
              $.cookie(id + "-md5", '');
              if (graphtype == 'hour') {
                window.open(inernalURL_messages + id, '_blank');
              }
              else {
                loadContent(id, pstart.format("dd.mm.yyyy"), pend.format("dd.mm.yyyy"));
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
        },
        showInLegend: true
      }
    },
    tooltip: {
      enabled: true,
      formatter: function () {
        var tttext = '';
        if (graphtype == 'hour') {
          tttext = (new Date((this.x - 1) * 3600000 + shift)).format("H:00");
        }
        else if (graphtype == 'day') {
          tttext = (new Date((this.x - 1) * 86400000 + shift)).format("d.mm.yyyy");
        }
        var rangestart = new Date(minDate);
        if ($.cookie(id + "-fromDate-theme") != null) {
          rangestart = new Date(parseInt($.cookie(id + "-fromDate-theme"), 10));
        }

        var rngstart = new Date((this.x - 1) * 86400000 * 7 + shift - 86400 * 6 * 1000);
        if (graphtype == 'week') {
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 7 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 7 + shift)).format("dd.mm.yy");
          }
        }
        else if (graphtype == 'month') {
          rngstart = new Date((this.x - 1) * 86400000 * 30 + shift - 86400 * 29 * 1000);
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 30 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 30 + shift)).format("dd.mm.yy");
          }
        }
        else if (graphtype == 'quarter') {
          rngstart = new Date((this.x - 1) * 86400000 * 90 + shift - 86400 * 89 * 1000);
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 90 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 90 + shift)).format("dd.mm.yy");
          }
        }
        else if (graphtype == 'halfyear') {
          rngstart = new Date((this.x - 1) * 86400000 * 180 + shift - 86400 * 179 * 1000);
          if (rngstart > rangestart) {
            tttext = rngstart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 180 + shift)).format("dd.mm.yy");
          }
          else {
            tttext = rangestart.format("dd.mm.yy") + ' - ' + (new Date((this.x - 1) * 86400000 * 180 + shift)).format("dd.mm.yy");
          }
        }
        return '<b>' + this.series.name + '</b><br/>' + this.y + ' (' + tttext + ')';
      }
    },
    series: data,
    navigation: {
      buttonOptions: {
        enabled: false
      }
    }
  });
  $("tspan").each(function (index, element) {
    if ($(element).text() == "Highcharts.com") {
      $(element).text("");
    }
  });
};

var createColumnDiagram = function (data) {
  Highcharts.setOptions({
    global: {
      useUTC: false
    }
  });

  detailChart = new Highcharts.Chart({
    title: { text: ""},
    chart: {
      marginBottom: 80,
      renderTo: 'detail-container',
      reflow: false,
      marginLeft: 40,
      marginRight: 20,
      style: {
        position: 'absolute'
      },
      spacingLeft: 0,
      height: 281,
      width: 806,
      type: 'column'
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
    xAxis: {
      categories: data.categories,
      labels: {
//        rotation: -45,
        style: {
          fontSize: '10px'
        }
      }
    },
    yAxis: {lineWidth: 1, lineColor: '#000', tickWidth: 1, tickColor: '#000', gridLineWidth: 1, minorGridLineWidth: 1, minorGridLineColor: '#F0F0F0', minorTickWidth: 1, minorTickInterval: 'auto', title: {text: ""}, allowDecimals: false, min: 0},
    tooltip: {
      enabled: true,
      formatter: function () {
        return '<b>' + this.series.name + '</b><br/>' + this.y + ' (' + this.x + ')';
      }
    },
    series: [
      {
        name: $('#number_property option[value="'+$('#number_property').val()+'"]').text(),
        data: data.data,
        dataLabels: {
            enabled: false,
            rotation: -90,
            color: '#333',
            align: 'right',
            x: -8,
            y: 10,
            style: {
                fontSize: '10px'
            }
        },
        showInLegend: true
      }
    ],
    legend: {
      enable: false
    },
    navigation: {
      buttonOptions: {
        enabled: false
      }
    }
  });
  $("tspan").each(function (index, element) {
    if ($(element).text() == "Highcharts.com") {
      $(element).text("");
    }
  });
};

var createColumnDiagramWithSeparator = function (data) {
  Highcharts.setOptions({
    global: {
      useUTC: false
    }
  });

  detailChart = new Highcharts.Chart({
    title: {
      text: ''
    },
    chart: {
      marginBottom: 80,
      renderTo: 'detail-container',
      reflow: false,
      marginLeft: 40,
      marginRight: 20,
      style: {
        position: 'absolute'
      },
      spacingLeft: 0,
      height: 281,
      width: 806,
      type: 'column'
    },
    xAxis: {
      categories: data.categories
    },
    yAxis: {lineWidth: 1, lineColor: '#000', tickWidth: 1, tickColor: '#000', gridLineWidth: 1, minorGridLineWidth: 1, minorGridLineColor: '#F0F0F0', minorTickWidth: 1, minorTickInterval: 'auto', title: {text: ""}, allowDecimals: false, min: 0},
    legend: {
      enable: false
    },
    tooltip: {
      formatter: function () {
        return '<b>' + this.x + '</b><br/>' +
          this.series.name + ': ' + this.y + '<br/>' +
          'Всего: ' + this.point.stackTotal;
      }
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: true,
          color: '#fff'
        },
        showInLegend: true
      }
    },
    series: data.data,
    navigation: {
      buttonOptions: {
        enabled: false
      }
    }
  });

  $("tspan").each(function (index, element) {
    if ($(element).text() == "Highcharts.com") {
      $(element).text("");
    }
  });
};