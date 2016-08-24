

var data = [
   23,34,25,46,57,23,45,23,39,17,42,23,34,25,46,57,23,45,23,39,17,42,23,34,25,46,57,23,45,23,39,17,42,23,34,25,46,57,23,45,23,39,17,42
];
var colors= [       '#6DA74E', 
                    '#3B7F6B', 
                    '#A2B856', 
                    '#5D7F4C', 
                    '#396054', 
                    '#7F8B53', 
                    '#386E1B', 
                    '#145441', 
                    '#65791D'];
var masterChart,
   detailChart;
   
$(document).ready(function() {
   
   Highcharts.setOptions({
		lang: {
			months: ['Янв', 'Фев', 'Мар', 'Апр', 'Mай', 'Июн', 
				'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
			weekdays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб']
		},
                colors: colors
//                                            '#448684',
//                                            '#C46482',
//                                            '#92C564', 
//                                            '#DDA070', 
//                                            '#4F8222', 
//                                            '#925626',
//                                            '#175856',
//                                            '#812240'
//                                            ]
//                                            '#77C923', 
//                                            '#DFAE27', 
//                                            '#2D359A', 
//                                            '#C3225B', 
//                                            '#B0EF70', 
//                                            '#F6D373', 
//                                            '#7A81E1', 
//                                            '#ED6F9C', 
//                                            '#3E7208']
	});
   // create the master chart
   function createMaster() {
           

      masterChart = new Highcharts.Chart({
         chart: {
            renderTo: 'master-container',
            reflow: false,
            borderWidth: 0,
            backgroundColor: null,
            marginLeft: 0,
            //marginTop: -20,
            zoomType: 'x',
            events: {
               
               // listen to the selection event on the master chart to update the 
               // extremes of the detail chart
               selection: function(event) {
                     var extremesObject = event.xAxis[0],
                     min = extremesObject.min,
                     max = extremesObject.max,
                     detailData = [],
                     xAxis = this.xAxis[0];
                     
                     //изменение значений даты в datepicker
                     $('#sd').val(Highcharts.dateFormat('%d.%m.%Y', extremesObject.min));
                     $('#ed').val(Highcharts.dateFormat('%d.%m.%Y', extremesObject.max));
                     var sd = datePickerController.getDatePicker("sd");
                     var ed = datePickerController.getDatePicker("ed");
                     var dt = datePickerController.dateFormat($('#sd').val(), sd.format.charAt(0) == "m");
                     ed.setRangeLow( dt );
                     dt = datePickerController.dateFormat($('#ed').val(), ed.format.charAt(0) == "m");
                     sd.setRangeHigh( dt );
                     
                     
                  // reverse engineer the last part of the data
                  jQuery.each(this.series[0].data, function(i, point) {
                     if (point.x > min && point.x < max) {
                        detailData.push({
                           x: point.x,
                           y: point.y
                        });
                     }
                  });
                  
                  // move the plot bands to reflect the new detail span
                  xAxis.removePlotBand('mask-before');
                  xAxis.addPlotBand({
                     id: 'mask-before',
                     from: Date.UTC(StrToDate(time_beg).getFullYear(),StrToDate(time_beg).getMonth(),StrToDate(time_beg).getDate()),
                     to: min,
                     color: 'rgba(0, 0, 0, 0.2)'
                  });
                  
                  xAxis.removePlotBand('mask-after');
                  xAxis.addPlotBand({
                     id: 'mask-after',
                     from: max,
                     to: Date.UTC(StrToDate(time_end).getFullYear(),StrToDate(time_end).getMonth(),StrToDate(time_end).getDate()),
                     color: 'rgba(0, 0, 0, 0.2)'
                  });
                  
                  
                  detailChart.series[0].setData(detailData);
                  
                  return false;
               }
            }
         },
         title: {
            text: 'Выберите интервал мышкой',floating  :true,
            style: {
                    color: '#999',
                    fontSize: '16px'
            }
         },
         xAxis: {
            type: 'datetime',
            showLastTickLabel: true,
            maxZoom: 14 * 24 * 3600000, // fourteen days
//            plotBands: [{
//               id: 'mask-before',
//               from: Date.UTC(StrToDate(time_beg).getFullYear(),StrToDate(time_beg).getMonth(),StrToDate(time_beg).getDate()),
//               to: Date.UTC(StrToDate(time_end).getFullYear(),StrToDate(time_end).getMonth(),StrToDate(time_end).getDate()),
//               color: '#ccc' || 'rgba(0, 0, 0, 0.2)'
//            }],
            title: {
               text: null
            }
         },
         yAxis: {
            gridLineWidth: 0,
            labels: {
               enabled: false
            },
            title: {
               text: null
            },
            
            min: 0,
            showFirstLabel: false
         },
         tooltip: {
            formatter: function() {
               return false;
            }
         },
         legend: {
            enabled: false
         },
         credits: {
            enabled: false
         },
         plotOptions: {
            series: {
               fillColor: {
                  linearGradient: [0, 0, 0, 70],
                  stops: [
                     [0, '#6DA74E'],
                     [1, 'rgba(0,0,0,0)']
                  ]
               },
               lineWidth: 1,
               marker: {
                  enabled: false
               },
               shadow: false,
               states: {
                  hover: {
                     lineWidth: 1                  
                  }
               },
               enableMouseTracking: false
            }
         },
      
         series: [{
            type: 'area',
            name: 'Упоминания',
            pointInterval: 24 * 3600 * 1000,
            pointStart: Date.UTC(StrToDate(time_beg).getFullYear(),StrToDate(time_beg).getMonth(),StrToDate(time_beg).getDate()),
            data: data
         }],
         
         exporting: {
            enabled: false
         }
      
      }, function(masterChart) {
         createDetail(masterChart)
      });
   }
   
   // create the detail chart
   function createDetail(masterChart) {
      
      // prepare the detail chart
      var detailData = [],
         detailStart = Date.UTC(StrToDate(time_beg).getFullYear(),StrToDate(time_beg).getMonth(),StrToDate(time_beg).getDate());
         
      jQuery.each(masterChart.series[0].data, function(i, point) {
         if (point.x >= detailStart) {
            detailData.push(point.y);
         }
      });
      
      // create a detail chart referenced by a global variable
      detailChart = new Highcharts.Chart({
         chart: {
            marginBottom: 120,
            renderTo: 'detail-container',
            reflow: false,
            marginTop: 36,
            style: {
               position: 'absolute'
            }
         },
         credits: {
            enabled: false
         },
         title: {
            text: null
         },
         xAxis: {
            type: 'datetime'
         },
         yAxis: {
            title: null,
            maxZoom: 0.1
            
         },
         tooltip: {
            formatter: function() {
                    var point = this.points[0];
                    return '<b>Упоминания</b><br/>Дата: '+
                            Highcharts.dateFormat('%A %B %e %Y', this.x) + '<br/>'+
                            'Кол-во: '+ Highcharts.numberFormat(point.y, 0) +'';
            },
            shared: true
         },
         legend: {
            enabled: false
         },
         plotOptions: {
            spline: {
							linewidth: 4,
							marker: {
								    	fillColor: '#FFFFFF',
								        lineWidth: 1,
										radius: 2.5,
								        lineColor: null // inherit from series
							}
						}
         },
         series: [{
            name: 'Упоминания',
            pointStart: detailStart,
            pointInterval: 24 * 3600 * 1000,
                color: '#360',
                data: detailData,
            cursor: 'pointer',
            point: {
               events: {
                  click: function() {
//                      alert(Highcharts.dateFormat('%d.%m.%Y', this.x));
//                      $('#sd').val(Highcharts.dateFormat('%d.%m.%Y', this.x));
//                      $('#ed').val(Highcharts.dateFormat('%d.%m.%Y', this.x));
                                        $('#ntime').attr('value',Highcharts.dateFormat('%d.%m.%Y', this.x));
                                        $('#etime').attr('value',Highcharts.dateFormat('%d.%m.%Y', this.x));
                  }
               }
            }
         }],
         
         exporting: {
            enabled: true
         }
      
      });
   }
      
   // make the container smaller and add a second container for the master chart
   var $container = $('#graph')
      .css('position', 'relative');
   
   var $detailContainer = $('<div id="detail-container">')
      .appendTo($container);
   
   var $masterContainer = $('<div id="master-container">')
      .css({ position: 'absolute', top: 300, height: 80, width: '100%' })
      .appendTo($container);
      
   // create master and in its callback, create the detail chart
   createMaster();
   
   
   var reschart = CreatePie("resourcespie",resources);
   var citieschart = CreatePie("citiespie",cities);
   
   });
   
   function CreatePie(element,mas)
{{
    var mydata=new Array();
    for (var i=0;i<mas.length;i++)
        {
            mydata[i]=new Array(mas[i][0],mas[i][1]);
        }
    var reschart = new Highcharts.Chart({
		      chart: {
		         renderTo: element,
		         plotBackgroundColor: null,
		         plotBorderWidth: null,
				reflow: false,
				margin: -10,
				padding: -10,
		         plotShadow: false
		      },
		      title: {
		         text: ''
		      },
		      tooltip: {
		         formatter: function() {
		            return '<font style="font-size: 10px; font-weight: bold;">'+ this.point.name +':</font> <font style="font-size: 10px;">'+ this.y +'%</font>';
		         }
		      },
			  credits: {
				enabled: false
			  },
	      legend: {
	         enabled: false
	      },
	      exporting: {
	         enabled: false
	      },
		      plotOptions: {
		         pie: {
		            allowPointSelect: true,
		            cursor: 'pointer',
		            dataLabels: {
		               enabled: false,
		               color: '#000000',
		               connectorColor: '#000000',
		               formatter: function() {
		                  return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
		               }
		            }
		         }
		      },
		       series: [{
		         type: 'pie',
		         name: '',
		         data: mydata
		      }]
		   });
}}