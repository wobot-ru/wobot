/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Highcharts.setOptions({
				global: {
					useUTC: false
				}
			});
				
			var chart;
                        var series;
                        //var series, series1;
 function AddData() {
        var x = (new Date()).getTime(), // current time
                y = n;
        series.addPoint([x, y], true, true);
        n=0;
//        n1=0;
//        y = n2;
//        series1.addPoint([x, y], true, true);
//        n2=0;
        setTimeout(AddData, interval*3);
}                       
                        
	$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'graph',
						defaultSeriesType: 'spline',
						marginRight: 10,
						events: {
							load: function() {
				
								// set up the updating of the chart each second
								series = this.series[0];
                                                                series1 = this.series[1];
								setTimeout(AddData, interval*3);
							}
						}
					},
                                        colors: [
                                            '#33ccff', 
                                            '#ff3333', 
                                            '#89A54E', 
                                            '#80699B', 
                                            '#3D96AE', 
                                            '#DB843D', 
                                            '#92A8CD', 
                                            '#A47D7C', 
                                            '#B5CA92'
                                    ],
					title: {
                                            
						text: null
					},
					xAxis: {
                                                
						type: 'datetime',
						tickPixelInterval: 200,
                                                gridLineWidth: 1,
                                                labels: {
                                                    y:30,
                                                    style: { 
                                                            font: '25px Calibri',
                                                            color: '#555'
                                                        }}
					},
					credits: {
						enabled: false
					},
					yAxis: {
                                                allowDecimals: false,
                                                min:0,
                                                labels: {
                                                    y:10,
                                                    style: { 
                                                            font: '25px Calibri',
                                                            color: '#555'
                                                        }
                                                },
						title: {
							text: 'Кол-во постов',
                                                        style: { 
                                                            font: '30px  Calibri bold',
                                                            color: '#555'
                                                        }
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						formatter: function() {
				                return '<b>'+ this.series.name +'</b><br/>'+
								Highcharts.dateFormat('%H:%M:%S', this.x) +'<br/>'+ 
								Highcharts.numberFormat(this.y,0);
						}
					},
                                        plotOptions: {
                                             spline: {
                                                lineWidth: 10,
                                                marker: {

                                                         radius: 10
                                                         //fillColor: 'rgb(150,150,150)'
//                                                         lineColor: 'rgba(59, 89, 151, 1)',
//                                                         lineWidth:3
                                                    }}
                                            },
					legend: {
						enabled: false
					},
					exporting: {
						enabled: false
					},
					series: [{
						name: 'twitter1',
						data: (function() {
							// generate an array of random data
							var data = [],
								time = (new Date()).getTime(),
								i;
							
							for (i = -19; i <= 0; i++) {
								data.push({
									x: time + i * 5000,
									y: null
								});
							}
							return data;
						})()
					}
//                                        ,
//                                    {
//						name: 'twitter2',
//						data: (function() {
//							// generate an array of random data
//							var data = [],
//								time = (new Date()).getTime(),
//								i;
//							
//							for (i = -19; i <= 0; i++) {
//								data.push({
//									x: time + i * 5000,
//									y: null
//								});
//							}
//							return data;
//						})()
//					}
                                    ],
                                    symbols:  'circle'
				});
				
				
			});
