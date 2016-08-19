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
                        
                        
	$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'graph',
						defaultSeriesType: 'spline',
						marginRight: 10,
						events: {
							load: function() {
				
								// set up the updating of the chart each second
								var series = this.series[0];
								setInterval(function() {
									var x = (new Date()).getTime(), // current time
										y = n;
									series.addPoint([x, y], true, true);
                                                                        n=0;
								}, 7000);
							}
						}
					},
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
						name: 'Random data',
						data: (function() {
							// generate an array of random data
							var data = [],
								time = (new Date()).getTime(),
								i;
							
							for (i = -19; i <= 0; i++) {
								data.push({
									x: time + i * 5000,
									y: Math.floor(Math.random()*11)
								});
							}
							return data;
						})()
					}]
				});
				
				
			});
