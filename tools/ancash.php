<?
error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		    <!--[if !IE 7]>
		      <link href='/css/ie_footer.css' rel='stylesheet' type='text/css' />
		    <![endif]-->
			<script type="text/javascript" src="/js/jquery.js"></script>
			<script type="text/javascript" src="/js/jquery.highlight-3.js"></script>
			<!--<script type="text/javascript" src="/js/jquery.flot.js"></script>-->
			<script type="text/javascript" src="/js/jquery.cookie.js"></script>

			<!--<script type="text/javascript" src="/js/jquery.flot.selection.js"></script>-->
			<script type="text/javascript" src="/js/jquery.fancybox-1.3.0.pack.js"></script>
			<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-1.3.0.css" media="screen" />
			

		    <!--<link rel="stylesheet" type="text/css" href="/css/jquery-ui-1.8.4.custom.css">-->
		    <!--<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>-->
		    <!--<script type="text/javascript" src="/js/jquery-ui-1.8.4.custom.min.js"></script>-->


			<link type="text/css" href="/css/smoothness/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
			<script type="text/javascript" src="/js/jquery-ui-1.8.9.custom.min.js"></script>
				<script type="text/javascript" src="/js/jquery.blockUI.js"></script>

			<script type="text/javascript" src="/js/jquery.pagination.js"></script>
			<script src="/js/jquery.tipsy.js" type="text/javascript"></script>
			<script src="/js/jquery.contextmenu.js" type="text/javascript"></script>
			<link rel="stylesheet" type="text/css" href="/css/jquery.contextmenu.css" />

			<script type="text/javascript" src="/js/vtip.js"></script>
			<link rel="stylesheet" type="text/css" href="/css/vtip.css" />

			<script type="text/javascript" src="/js/custom-form-elements.js"></script>
			<link rel="stylesheet" href="/css/m_form.css" media="screen" />
			
			
			<script type="text/javascript" src="/js/highslid.js"></script>
			<link rel="stylesheet" type="text/css" href="/css/highslid.css" />
			
			
			
				
			<script src="/js/prettyCheckboxes.js" type="text/javascript" charset="utf-8"></script>
			<link rel="stylesheet" href="/css/prettyCheckboxes.css" type="text/css" media="screen" charset="utf-8" />

			<script type="text/javascript" src="/js/highcharts.js"></script>
			<script type="text/javascript" src="/js/modules/exporting.js"></script>
			<script type="text/javascript" src="/js/jquery.sparkline.min.js"></script>

    		<script src="/js/popup.js" type="text/javascript"></script>
			<script type="text/javascript" src="/js/jquery.ui.datepicker-ru.js"></script>
			<!-- drop_menu -->
		    <link rel="stylesheet" type="text/css" href="/css/ui.dropdownchecklist.themeroller.css">
		    <script type="text/javascript" src="/js/ui.dropdownchecklist.js"></script>
			<script type="text/javascript" src="/js/jqcloud-0.2.4.js"></script>
			<link rel="stylesheet" type="text/css" href="/css/jqcloud.css" />
<script type="text/javascript">


	

</script>
</head>
<body>
<form action="http://bmstu.wobot.ru/tools/ancash.php" method="GET">
Выберите отчет снизу: <input type="text" name="id" id="order_id" value="<?=$_GET['id']?>"> Начало: <input type="text" name="st" id="ntime" value="<?=$_GET['st']?>"> Конец: <input type="text" name="et" id="etime" value="<?=$_GET['et']?>">
<input type="submit" value="Посчитать"> <a href="http://bmstu.wobot.ru/tools/ancash.php">&lg;&lg; вернуться к выбору отчетов</a>
</form>
<?
date_default_timezone_set ( 'Europe/Moscow' );
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
ini_set("memory_limit", "2048M");
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();
if ($_GET['id']!='')
{
	$mas=getcash($_GET['id'],$_GET['st'],$_GET['et']);
	print_r($mas);
	echo '<script type="text/javascript">
	$(document).ready(function(){



		var dates = $( "#ntime, #etime" ).datepicker({
			dateFormat: "dd.mm.yy",
			defaultDate: "01.11.2011",
			minDate: "30.05.2000",
			maxDate: "01.11.2021",
			onSelect: function( selectedDate ) {
				var option = this.id == "ntime" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			} 
		});
	var word_list = [';
	foreach ($mas['top'] as $key => $item)
	{
		if (($key!='арбидол')&&($key!='арбидолы')&&($key!='кама')&&($key!='kama')&&($key!='viatti'))
		{
			echo '{text: "'.$key.'", weight: '.$item.', url: "продам"},';
		}
	}
	echo '];
	$("#my_favorite_latin_words").jQCloud(word_list); 
	var $container = $(\'#container\')
		.css(\'position\', \'relative\');

	var $detailContainer = $(\'<div id="detail-container">\')
		.appendTo($container);

	var $masterContainer = $(\'<div id="master-container">\')
		.css({ position: \'absolute\', top: 300, height: 80, width: \'100%\' })
		.appendTo($container);
		var data = [

		';
		$mtime=$mas['time'];
		//print_r($mtime);
		foreach ($mtime as $hn=>$gtime)
		{
			foreach($gtime as $year=>$years) 
			{
				foreach($years as $month=>$months)
				{
					foreach($months as $day=>$days)
					{
						$timet[$year][$month][$day]+=$days;
					}
				}
			}
		}
		for($t=strtotime($_GET['st']);$t<=strtotime($_GET['et']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			$html_out.= $zap.intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
			$zap=', ';
		}
		echo $html_out;
		echo '
		];
	chart = new Highcharts.Chart({
				      chart: {
				         renderTo: \'socialgraph\',
				         plotBackgroundColor: null,
				         plotBorderWidth: null,
						reflow: false,
						/*margin: -10,
						padding: -10,*/
				         plotShadow: false
				      },
				      title: {
				         text: \'\'
				      },
				      tooltip: {
				         formatter: function() {
				            return \'<font style="font-size: 10px; font-weight: bold;">\'+ this.point.name +\':</font> <font style="font-size: 10px;">\'+ this.y +\'%</font>\';
				         }
				      },
					  credits: {
						enabled: false
					  },
			      legend: {
			         enabled: true
			      },
			      exporting: {
			         enabled: true
			      },
				     /* plotOptions: {
				         pie: {
				            allowPointSelect: true,
				            cursor: \'pointer\',
				            dataLabels: {
				               enabled: true,
				               color: \'#fff\',
				               connectorColor: \'#000000\',
				               formatter: function() {
				                  if (this.percentage >= 5) return \'<p style="font-size: 9px;">\'+this.y +\'%</p>\';
				               }
				            }
				         }
				      },*/
				      plotOptions: {
				         pie: {
				            allowPointSelect: true,
				            cursor: \'pointer\',
				            dataLabels: {
				               enabled: true,
				               color: \'#fff\',
				               connectorColor: \'#000000\',
				               formatter: function() {
			                   if (this.percentage >= 5) return \'<p style="font-size: 9px;">\'+this.y +\'%</p>\';
			                   }				
			            	},
				            showInLegend: true,

				         }
				      },
				       series: [{
				         type: \'pie\',
				         name: \'\',
				    	dataLabels: {
			            	distance: -20
			        		},
				         data: [';
				$zap='';
				foreach ($mas['soc'] as $key => $it)
				{
					if ($key!='')
					{
						$count+=$it;
					}
				}
				foreach ($mas['soc'] as $key => $item)
				{
					if ($key!='')
					{
						echo $zap.'[\''.$key.'\','.(intval($item/$count*100)+1).']';
						$zap=',';
					}
				}
				echo '

				         ]
				      }]
				   });
		var masterChart,
			detailChart;

		var chart;
	Highcharts.setOptions({
		lang: {
			months: [\'Янв\', \'Фев\', \'Мар\', \'Апр\', \'Mай\', \'Июн\', 
				\'Июл\', \'Авг\', \'Сен\', \'Окт\', \'Ноя\', \'Дек\'],
			weekdays: [\'Вс\', \'Пн\', \'Вт\', \'Ср\', \'Чт\', \'Пт\', \'Сб\']
		}
	});

			// create the master chart
			function createMaster() {
				masterChart = new Highcharts.Chart({
					chart: {
						renderTo: \'master-container\',
						reflow: false,
						borderWidth: 0,
						backgroundColor: null,
						marginLeft: 50,
						marginRight: 20,
						zoomType: \'x\',
						events: {

							// listen to the selection event on the master chart to update the 
							// extremes of the detail chart
							selection: function(event) {
								var extremesObject = event.xAxis[0],
									min = extremesObject.min,
									max = extremesObject.max,
									detailData = [],
									xAxis = this.xAxis[0];
								$(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', min+86400000));
								$(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', max+3*86400000));
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
								xAxis.removePlotBand(\'mask-before\');
								xAxis.addPlotBand({
									id: \'mask-before\',
									from: Date.UTC('.date('Y,n,j',strtotime($_GET['st'])).'),
									to: min,
									color: \'rgba(0, 0, 0, 0.1)\'
								});

								xAxis.removePlotBand(\'mask-after\');
								xAxis.addPlotBand({
									id: \'mask-after\',
									from: max,
									to: 1320699600000,
									color: \'rgba(0, 0, 0, 0.1)\'
								});

								detailChart.series[0].setData(detailData);

								return false;
							}
						}
					},
					title: {
						text: null
					},
					xAxis: {
						type: \'datetime\',
						showLastTickLabel: true,
						maxZoom: 14 * 24 * 3600000, // fourteen days
						plotBands: [{
							id: \'mask-before\',
							from: Date.UTC(2010,7,17),
							to: 1320699600000,
							color: \'rgba(255, 255, 255, 0.2)\'
						}],
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
									[0, \'#3c6087\'],
									[1, \'rgba(0,0,0,0)\']
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
						type: \'area\',
						name: \'Упоминания\',
						pointInterval: 24 * 3600 * 1000,
						pointStart: Date.UTC('.date('Y,n,j',strtotime($_GET['st'])).'),
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
					detailStart = Date.UTC('.date('Y,n,j',strtotime($_GET['st'])).');

				jQuery.each(masterChart.series[0].data, function(i, point) {
					if (point.x >= detailStart) {
						detailData.push(point.y);
					}
				});

				// create a detail chart referenced by a global variable
				detailChart = new Highcharts.Chart({
					chart: {
						defaultSeriesType: \'spline\',
						marginBottom: 120,
						renderTo: \'detail-container\',
						reflow: false,
						marginLeft: 50,
						marginRight: 20,
						style: {
							position: \'absolute\'
						}
					},
					credits: {
						enabled: false
					},
					title: {
						text: \'График упоминаний\'
					},
					subtitle: {
						text: \'упоминания сгруппированы по дням\'
					},
					xAxis: {
						type: \'datetime\',
						maxZoom: 14 * 24 * 3600000,
						minorTickLength: 0,
						 minorTickInterval: 24 * 3600 * 1000, // one week
				         minorTickWidth: 1,
				         gridLineWidth: 1
					},
					yAxis: {
						min: 0,
						title: null,
						maxZoom: 0.1
					},
					tooltip: {
						formatter: function() {
							var point = this.points[0];
							return \'<b>Упоминания</b><br/>Дата: \'+
								Highcharts.dateFormat(\'%A %B %e %Y\', this.x) +\'<br/>\'+
								\'Кол-во: \'+ Highcharts.numberFormat(point.y, 0) +\'\';
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
							    	fillColor: \'#FFFFFF\',
							        lineWidth: 2,
									radius: 2.5,
							        lineColor: null // inherit from series
						}
					}
				},
				/*plotOptions: {
					spline: {
						linewidth: 4,
						marker: {
							enabled: false,
							lineColor: \'#3c6087\',
							states: {
								hover: {
									enabled: true,
									radius: 4
								}
							}
						}
					}
				},*/
						series: [{
						name: \'Упоминания\',
						pointStart: detailStart,
						pointInterval: 24 * 3600 * 1000,
						color: \'#3c6087\',
						data: detailData,
				            cursor: \'pointer\',
				            point: {
				               events: {
				                  click: function() {
									$(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', this.x));
									$(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', this.x));
				                  }
				               }
				            },
					}],

					exporting: {
						enabled: true
					}

				});
			}
			// create master and in its callback, create the detail chart
			createMaster();

			chart1 = new Highcharts.Chart({
						      chart: {
						         renderTo: \'geograph\',
						         plotBackgroundColor: null,
						         plotBorderWidth: null,
								reflow: false,
								/*margin: -10,
								padding: -10,*/
						         plotShadow: false
						      },
						      title: {
						         text: \'\'
						      },
						      tooltip: {
						         formatter: function() {
						            return \'<font style="font-size: 10px; font-weight: bold;">\'+ this.point.name +\':</font> <font style="font-size: 10px;">\'+ this.y +\'%</font>\';
						         }
						      },
							  credits: {
								enabled: false
							  },
					      legend: {
					         enabled: true
					      },
					      exporting: {
					         enabled: true
					      },
						     /* plotOptions: {
						         pie: {
						            allowPointSelect: true,
						            cursor: \'pointer\',
						            dataLabels: {
						               enabled: true,
						               color: \'#fff\',
						               connectorColor: \'#000000\',
						               formatter: function() {
						                  if (this.percentage >= 5) return \'<p style="font-size: 9px;">\'+this.y +\'%</p>\';
						               }
						            }
						         }
						      },*/
						      plotOptions: {
						         pie: {
						            allowPointSelect: true,
						            cursor: \'pointer\',
						            dataLabels: {
						               enabled: true,
						               color: \'#fff\',
						               connectorColor: \'#000000\',
						               formatter: function() {
					                   if (this.percentage >= 5) return \'<p style="font-size: 9px;">\'+this.y +\'%</p>\';
					                }				
					            	},
						            showInLegend: true,

						         }
						      },
						       series: [{
						         type: \'pie\',
						         name: \'\',
						    	dataLabels: {
					            	distance: -20
					        		},
						         data: [';
						$zap='';
						foreach ($mas['loc'] as $key => $it)
						{
							if ($key!='')
							{
								$count+=$it;
							}
						}
						foreach ($mas['loc'] as $key => $item)
						{
							if ($key!='')
							{
								echo $zap.'[\''.$key.'\','.(intval($item/$count*100)+1).']';
								$zap=',';
							}
						}
						echo '

						         ]
						      }]
						   });

	});

	</script>';
	echo '<div id=\'my_favorite_latin_words\' style="width: 650px; height: 350px; margin-left: 5px; border: 0px solid #ccc;"></div><div id="container" style="width: 680px; height: 350px;"></div><div id="geograph" style="width: 400px; margin-top: 300px;"></div><div id="socialgraph" style="width: 400px; margin-top: 300px;"></div><br><br><br>';
	$ccc=parseUrl('http://bmstu.wobot.ru/tools/carrot2/carrot/examples/php5/example.php?order_id='.$_GET['id']);
	echo 'http://bmstu.wobot.ru/tools/carrot2/carrot/examples/php5/example.php?order_id='.$_GET['id'];
}
else
{
		$res=$db->query('SELECT * FROM users ORDER BY user_email');
		$i=0;
		$fletter='';
		while ($row = $db->fetch($res)) {
			$i++;
			if (mb_strtoupper(mb_substr($row['user_email'], 0, 1, 'UTF-8'), 'UTF-8')!=$fletter) 
			{
				if ($fletter!='') echo '</div>';
				$fletter = mb_strtoupper(mb_substr($row['user_email'], 0, 1, 'UTF-8'), 'UTF-8');
				echo '<div style="float: left; width: 500px;">
				<h1 style="line-height:20%;">'.$fletter.'</h1>';
			}
			echo '<p style="font-size: 12px; line-height:20%;"><b>'.$row['user_email'].'</b> ('.$row['user_contact'].')</p>';
					$res1=$db->query('SELECT order_id, order_name, order_keyword, order_start, order_end FROM blog_orders WHERE user_id='.$row['user_id']);
					echo '<ul>';
					while ($row1 = $db->fetch($res1)) {
						echo '<li style="font-size: 12px;"><a href="#" onclick="$(\'#order_id\').attr(\'value\',\''.$row1['order_id'].'\'); $(\'#ntime\').attr(\'value\',\''.date("d.m.Y",$row1['order_start']).'\'); $(\'#etime\').attr(\'value\',\''.(($row1['order_end']!=0)?date("d.m.Y",$row1['order_end']):date("d.m.Y",time())).'\'); return false;" title="'.$row1['order_keyword'].'">'.(strlen($row1['order_name'])>0?$row1['order_name']:$row1['order_keyword']).'</a> '.$row1['order_id'].' <a href="/tools/cashjob.php?order_id='.$row1['order_id'].'" target="_blank">обновить кэш</a></li>';
					}
					echo '</ul>';
		}
		if ($i==0) echo 'пользователи отсутствуют<br>';
		echo'
		</div>
';
}
?>
	</body>
	</html>
