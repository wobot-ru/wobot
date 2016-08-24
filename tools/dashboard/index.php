<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$instances[]='ec2-54-228-217-24.eu-west-1.compute.amazonaws.com';
$instances[]='ec2-54-228-30-178.eu-west-1.compute.amazonaws.com';
$instances[]='ec2-79-125-49-85.eu-west-1.compute.amazonaws.com';
$instances[]='bmstu.wobot.ru';
$instances[]='188.120.239.225';

$count_elastic=$redis->get('count_elastic');
$count_cash=$redis->get('count_cash');

$qprev=$db->query('SELECT id FROM tp_proxys WHERE valid=1');
$count_proxy=$db->num_rows($qprev);
$avcount['stop']=1;
$avcount[20]=1;
$avcount[50]=1;
if ($count_proxy>150) $avcount[100]=1;
if ($count_proxy>250) $avcount[200]=1;

$ass_launch['eng_job']='et';
$ass_launch['multi_ft']='fulljob';
$ass_launch['re_transf']='transf';
$ass_launch['tp_job3']='fs';
$ass_launch['multi_uj']='uj';

$gls=file_get_contents('http://ec2-54-228-217-24.eu-west-1.compute.amazonaws.com/rcrawler/get_count.php');
$mgls=json_decode($gls,true);

if (($_POST['act']=='launch') && (isset($avcount[$_POST['count']])))
{
	file_get_contents('http://188.120.239.225/tools/launch.php?act=launch&count='.$_POST['count'].'&name='.$_POST['name']);
}

$qhorder=$db->query('SELECT order_id,order_name FROM blog_orders WHERE third_sources=2 AND order_end>='.mktime(0,0,0,date('n'),date('j'),date('Y')).' AND user_id!=0 AND user_id!=145');
while ($horder=$db->fetch($qhorder))
{
	$mhorder[]=$horder;
}

foreach ($mhorder as $item)
{
	$torder_id.=$zp.$item['order_id'];
	$zp=',';
	//echo 'tail -n 20 /var/www/daemon/logs/fs'.($item % 100).'.log'."\n";
	$mhcont['order_id'][]=$item['order_id'];
	$mhcont['order_name'][]=$item['order_name'];
	$mhcont['logs'][]=preg_replace('/\n/isu','<br>',shell_exec('tail -n 10 /var/www/daemon/logs/fs'.($item['order_id'] % 100).'.log'));
}

$cache=$redis->get('cacher');
$mcache=json_decode($cache,true);

$dt_din_acc=(mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'))-mktime(date('H')-1,0,0,date('n'),date('j'),date('Y')))/60;
for ($t=mktime(date('H')-1,0,0,date('n'),date('j'),date('Y'));$t<=mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'));$t+=60)
{
	$din_acc[$t]=$mcache['account']['hour'][$t];
	$din_acc_null[$t]=$mcache['account_null']['hour'][$t];
	$din_eng[$t]=$mcache['engage']['hour'][$t];
	$din_ful[$t]=$mcache['fulljob']['hour'][$t];
	$din_procblogs[$t]=$mcache['account_toprocess']['hour'][$t];
	if (intval($mcache['account_toprocess']['hour'][$t])!=0) $lasttt=$mcache['account_toprocess']['hour'][$t];
	if ($mcache['account']['hour'][$t]>$max) $max=$mcache['account']['hour'][$t];
}
$outmas['max']=$max;
$outmas['now']=$mcache['account']['hour'][mktime(date('H'),date('i')-1,0,date('n'),date('j'),date('Y'))];

for ($t=mktime(0,0,0,date('n'),date('j')-5,date('Y'));$t<=mktime(0,0,0,date('n'),date('j'),date('Y'));$t+=86400)
{
	if ($mcache['account']['day'][$t]>$maxd) $maxd=$mcache['account']['day'][$t];
}
$outmasd['now']=$mcache['account']['day'][mktime(0,0,0,date('n'),date('j'),date('Y'))];
$outmasd['max']=$maxd;

$prev=shell_exec('tail -n 30 /var/www/tools/stat.txt');
$mprev=explode("\n",$prev);
foreach ($mprev as $item)
{
	if (trim($item)=='') continue;
	$mitem=explode(' ', $item);
	$prev_data_item=json_decode($mitem[1],true);
	$prev_mas['all'][$mitem[0]]=$prev_data_item['all'];
	$prev_mas['eng'][$mitem[0]]=$prev_data_item['engage'];
	$prev_mas['ful'][$mitem[0]]=$prev_data_item['ful'];
	$prev_mas['prc'][$mitem[0]]=$prev_data_item['process'];
}

$qprev=$db->query('SELECT post_id FROM blog_post_prev');
$count_prev=$db->num_rows($qprev);

$qprev=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_last_update=0');
$count_not_update=$db->num_rows($qprev);

$assoc_daemon['eng_job']='engagement';
$assoc_daemon['multi_ft']='fulljob';
$assoc_daemon['re_transf']='re_transf';
$assoc_daemon['tp_job3']='tp_job3';
$assoc_daemon['multi_uj']='userjob';
$assoc_daemon['efjob']='ef';
$assoc_daemon['elastic']='elastic';
$assoc_daemon['sentiment']='sentiment';
$assoc_daemon['charge']='charge';
$assoc_daemon['cash']='cash';
$cdmns=shell_exec('ps ax | grep php');
$mdmns=explode("\n", $cdmns);
$count_process=count($mdmns);
foreach ($mdmns as $item)
{
	if (preg_match('/elastic_job/isu', $item)) $all_daemons['elastic']++;
	if (preg_match('/wobot\.prodSentiment\.php/isu', $item)) $all_daemons['sentiment']++;
	if (preg_match('/re_charge\.php/isu', $item)) $all_daemons['charge']++;
	if (preg_match('/cashjob/isu', $item)) $all_daemons['cash']++;
	$regex='/php\s(?<name>.*?)\./isu';
	preg_match_all($regex, $item, $out);
	$all_daemons[$out['name'][0]]++;
}

$load_average=shell_exec('uptime');
$regex='/(?<la>\d+\.\d+)/isu';
preg_match_all($regex, $load_average, $out);
$la=$out['la'][0];

echo '<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highcharts Example</title>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
		<script type="text/javascript">
		$(function () {
	
    var chart = new Highcharts.Chart({
	
	    chart: {
	        renderTo: \'container\',
	        type: \'gauge\',
	        plotBackgroundColor: null,
	        plotBackgroundImage: null,
	        plotBorderWidth: 0,
	        plotShadow: false
	    },
	    
	    title: {
	        text: \'Accometer\'
	    },
	 //    subtitle: {
		// 	text: \'Количество аккаунтов обработанных аккаунтов за последниюю минуту, относительно максимального количества анкет, обработанных за последние 50 минут\',
		// 	x: -20
		// },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    pane: {
	        startAngle: -150,
	        endAngle: 150,
	        background: [{
	            backgroundColor: {
	                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	                stops: [
	                    [0, \'#FFF\'],
	                    [1, \'#333\']
	                ]
	            },
	            borderWidth: 0,
	            outerRadius: \'109%\'
	        }, {
	            backgroundColor: {
	                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	                stops: [
	                    [0, \'#333\'],
	                    [1, \'#FFF\']
	                ]
	            },
	            borderWidth: 1,
	            outerRadius: \'107%\'
	        }, {
	            // default background
	        }, {
	            backgroundColor: \'#DDD\',
	            borderWidth: 0,
	            outerRadius: \'105%\',
	            innerRadius: \'103%\'
	        }]
	    },
	       
	    // the value axis
	    yAxis: {
	        min: 0,
	        max: '.$outmas['max'].',
	        
	        minorTickInterval: \'auto\',
	        minorTickWidth: 1,
	        minorTickLength: 10,
	        minorTickPosition: \'inside\',
	        minorTickColor: \'#666\',
	
	        tickPixelInterval: 30,
	        tickWidth: 2,
	        tickPosition: \'inside\',
	        tickLength: 10,
	        tickColor: \'#666\',
	        labels: {
	            step: 2,
	            rotation: \'auto\'
	        },
	        title: {
	            text: \'Acc/min\'
	        },
	        plotBands: [{
	            from: 0,
	            to: '.($outmas['max']/4).',
	            color: \'#DF5353\' // green
	        }, {
	            from: '.($outmas['max']/4).',
	            to: '.(2*$outmas['max']/3).',
	            color: \'#DDDF0D\' // yellow
	        }, {
	            from: '.(2*$outmas['max']/3).',
	            to: '.$outmas['max'].',
	            color: \'#55BF3B\' // red
	        }]        
	    },
	
	    series: [{
	        name: \'Speed\',
	        data: ['.$outmas['now'].'],
	        tooltip: {
	            valueSuffix: \' Acc/min\'
	        }
	    }]
	
	});

    var chartd = new Highcharts.Chart({
	
	    chart: {
	        renderTo: \'containerd\',
	        type: \'gauge\',
	        plotBackgroundColor: null,
	        plotBackgroundImage: null,
	        plotBorderWidth: 0,
	        plotShadow: false
	    },
	    
	    title: {
	        text: \'Accometer\'
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    pane: {
	        startAngle: -150,
	        endAngle: 150,
	        background: [{
	            backgroundColor: {
	                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	                stops: [
	                    [0, \'#FFF\'],
	                    [1, \'#333\']
	                ]
	            },
	            borderWidth: 0,
	            outerRadius: \'109%\'
	        }, {
	            backgroundColor: {
	                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
	                stops: [
	                    [0, \'#333\'],
	                    [1, \'#FFF\']
	                ]
	            },
	            borderWidth: 1,
	            outerRadius: \'107%\'
	        }, {
	            // default background
	        }, {
	            backgroundColor: \'#DDD\',
	            borderWidth: 0,
	            outerRadius: \'105%\',
	            innerRadius: \'103%\'
	        }]
	    },
	       
	    // the value axis
	    yAxis: {
	        min: 0,
	        max: '.$outmasd['max'].',
	        
	        minorTickInterval: \'auto\',
	        minorTickWidth: 1,
	        minorTickLength: 10,
	        minorTickPosition: \'inside\',
	        minorTickColor: \'#666\',
	
	        tickPixelInterval: 50,
	        tickWidth: 2,
	        tickPosition: \'inside\',
	        tickLength: 10,
	        tickColor: \'#666\',
	        labels: {
	            step: 2,
	            rotation: \'auto\'
	        },
	        title: {
	            text: \'Acc/day\'
	        },
	        plotBands: [{
	            from: 0,
	            to: '.($outmasd['max']/4).',
	            color: \'#DF5353\' // green
	        }, {
	            from: '.($outmasd['max']/4).',
	            to: '.(2*$outmasd['max']/3).',
	            color: \'#DDDF0D\' // yellow
	        }, {
	            from: '.(2*$outmasd['max']/3).',
	            to: '.$outmasd['max'].',
	            color: \'#55BF3B\' // red
	        }]        
	    },
	
	    series: [{
	        name: \'Speed\',
	        data: ['.$outmasd['now'].'],
	        tooltip: {
	            valueSuffix: \' Acc/day\'
	        }
	    }]
	
	});

	chartprev = new Highcharts.Chart({
	    chart: {
	        renderTo: \'containerprev\',
	        type: \'line\',
	        marginRight: 130,
	        marginBottom: 25
	    },
	    title: {
	        text: \'Count blog_post_prev\',
	        x: -20 //center
	    },
		xAxis: {
			type: \'datetime\',
			//showLastTickLabel: true,
			//maxZoom: 14 * 24 * 3600000, // fourteen days
			// plotBands: [{
			// 	id: \'mask-before\',
				// from: Date.UTC(2012, 11, 7),
			//	to: Date.UTC(2013, 1, 6),
			// 	color: \'rgba(0, 0, 0, 0)\'
			// }],
			title: {
				text: null
			},
		},
	    yAxis: {
	        title: {
	            text: \'blog_post_prev\'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: \'#808080\'
	        }],
	        min: 0
	    },
	    marker: {
			fillColor: \'#FFFFFF\',
			lineWidth: 2,
			lineColor: null, // inherit from series,
			radius: 3,
			symbol: \'circle\'
		},
	    tooltip: {
	        // formatter: function() {
	        // 		var newdt=new Date(this.x);
	        // 		console.log(newdt.getHours()-3);
	        // 		//alert(newdt.hours);
	        // 		return \'<b>\'+ this.series.name +\'</b><br/>\'+
	        //         (newdt.getHours()-3) +\':\'+ (newdt.getMinutes()) +\': \'+ this.y +\'°C\';
	        // },
	        shared: true,
	        crosshairs: {
				width: 3
			}
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    legend: {
	        layout: \'vertical\',
	        align: \'right\',
	        verticalAlign: \'top\',
	        x: -10,
	        y: 100,
	        borderWidth: 0
	    },
	    series: [{
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'All\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        foreach ($prev_mas['all'] as $key => $item)
	        {
	        	echo $zap.intval($prev_mas['all'][$key]);
	        	$zap=',';
	        }
	        echo ']
	    }, {
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'Engage\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($prev_mas['eng'] as $key => $item)
	        {
	        	echo $zap.intval($prev_mas['eng'][$key]);
	        	$zap=',';
	        }
	        echo ']
	    }, {
    		lineWidth: 1,
            cursor: \'pointer\',
	        name: \'Full\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($prev_mas['ful'] as $key => $item)
	        {
	        	echo $zap.intval($prev_mas['ful'][$key]);
	        	$zap=',';
	        }
	        echo ']
	    }, {
    		lineWidth: 1,
            cursor: \'pointer\',
	        name: \'Proc\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-30,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($prev_mas['prc'] as $key => $item)
	        {
	        	echo $zap.intval($prev_mas['prc'][$key]);
	        	$zap=',';
	        }
	        echo ']
	    }]
	});

	chartdinacc = new Highcharts.Chart({
	    chart: {
	        renderTo: \'container_din_acc\',
	        type: \'line\',
	        marginRight: 130,
	        marginBottom: 25
	    },
	    title: {
	        text: \'Processing robot_blogs2\',
	        x: -20 //center
	    },
		xAxis: {
			type: \'datetime\',
			//showLastTickLabel: true,
			//maxZoom: 14 * 24 * 3600000, // fourteen days
			// plotBands: [{
			// 	id: \'mask-before\',
				// from: Date.UTC(2012, 11, 7),
			//	to: Date.UTC(2013, 1, 6),
			// 	color: \'rgba(0, 0, 0, 0)\'
			// }],
			title: {
				text: null
			},
		},
	    yAxis: {
	        title: {
	            text: \'robot_blogs2\'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: \'#808080\'
	        }],
	        min: 0
	    },
	    marker: {
			fillColor: \'#FFFFFF\',
			lineWidth: 2,
			lineColor: null, // inherit from series,
			radius: 3,
			symbol: "circle"
		},
	    tooltip: {
	        // formatter: function() {
	        // 		var newdt=new Date(this.x);
	        // 		console.log(newdt.getHours()-3);
	        // 		//alert(newdt.hours);
	        // 		return \'<b>\'+ this.series.name +\'</b><br/>\'+
	        //         (newdt.getHours()-3) +\':\'+ (newdt.getMinutes()) +\': \'+ this.y +\'°C\';
	        // },
	        shared: true,
	        crosshairs: {
				width: 3
			}
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    legend: {
	        layout: \'vertical\',
	        align: \'right\',
	        verticalAlign: \'top\',
	        x: -10,
	        y: 100,
	        borderWidth: 0
	    },
	    series: [{
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'All\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($din_acc as $key => $item)
	        {
	        	if ($key==mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'))) continue;
	        	echo $zap.intval($item);
	        	$zap=',';
	        }
	        echo ']
	    }]
	});

	chartdinacc = new Highcharts.Chart({
	    chart: {
	        renderTo: \'container_din_acc_null\',
	        type: \'line\',
	        marginRight: 130,
	        marginBottom: 25
	    },
	    title: {
	        text: \'Not process robot_blogs2\',
	        x: -20 //center
	    },
		xAxis: {
			type: \'datetime\',
			//showLastTickLabel: true,
			//maxZoom: 14 * 24 * 3600000, // fourteen days
			// plotBands: [{
			// 	id: \'mask-before\',
				// from: Date.UTC(2012, 11, 7),
			//	to: Date.UTC(2013, 1, 6),
			// 	color: \'rgba(0, 0, 0, 0)\'
			// }],
			title: {
				text: null
			},
		},
	    yAxis: {
	        title: {
	            text: \'robot_blogs2\'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: \'#808080\'
	        }],
	        min: 0
	    },
	    marker: {
			fillColor: \'#FFFFFF\',
			lineWidth: 2,
			lineColor: null, // inherit from series,
			radius: 3,
			symbol: "circle"
		},
	    tooltip: {
	        // formatter: function() {
	        // 		var newdt=new Date(this.x);
	        // 		console.log(newdt.getHours()-3);
	        // 		//alert(newdt.hours);
	        // 		return \'<b>\'+ this.series.name +\'</b><br/>\'+
	        //         (newdt.getHours()-3) +\':\'+ (newdt.getMinutes()) +\': \'+ this.y +\'°C\';
	        // },
	        shared: true,
	        crosshairs: {
				width: 3
			}
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    legend: {
	        layout: \'vertical\',
	        align: \'right\',
	        verticalAlign: \'top\',
	        x: -10,
	        y: 100,
	        borderWidth: 0
	    },
	    series: [{
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'All\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($din_acc_null as $key => $item)
	        {
	        	if ($key==mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'))) continue;
	        	echo $zap.intval($item);
	        	$zap=',';
	        }
	        echo ']
	    }]
	});

	/*chartdineng = new Highcharts.Chart({
	    chart: {
	        renderTo: \'container_din_eng\',
	        type: \'line\',
	        marginRight: 130,
	        marginBottom: 25
	    },
	    title: {
	        text: \'Process engage per minute\',
	        x: -20 //center
	    },
		xAxis: {
			type: \'datetime\',
			//showLastTickLabel: true,
			//maxZoom: 14 * 24 * 3600000, // fourteen days
			// plotBands: [{
			// 	id: \'mask-before\',
				// from: Date.UTC(2012, 11, 7),
			//	to: Date.UTC(2013, 1, 6),
			// 	color: \'rgba(0, 0, 0, 0)\'
			// }],
			title: {
				text: null
			},
		},
	    yAxis: {
	        title: {
	            text: \'blog_post_prev\'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: \'#808080\'
	        }],
	        min: 0
	    },
	    marker: {
			fillColor: \'#FFFFFF\',
			lineWidth: 2,
			lineColor: null, // inherit from series,
			radius: 3,
			symbol: "circle"
		},
	    tooltip: {
	        // formatter: function() {
	        // 		var newdt=new Date(this.x);
	        // 		console.log(newdt.getHours()-3);
	        // 		//alert(newdt.hours);
	        // 		return \'<b>\'+ this.series.name +\'</b><br/>\'+
	        //         (newdt.getHours()-3) +\':\'+ (newdt.getMinutes()) +\': \'+ this.y +\'°C\';
	        // },
	        shared: true,
	        crosshairs: {
				width: 3
			}
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    legend: {
	        layout: \'vertical\',
	        align: \'right\',
	        verticalAlign: \'top\',
	        x: -10,
	        y: 100,
	        borderWidth: 0
	    },
	    series: [{
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'All\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($din_eng as $key => $item)
	        {
	        	if ($key==mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'))) continue;
	        	echo $zap.intval($item);
	        	$zap=',';
	        }
	        echo ']
	    }]
	});*/

	$(function () {
	        $(\'#container_glas\').highcharts({
	            chart: {
	                type: \'column\'
	            },
	            title: {
	                text: \'RSS stack\'
	            },
	            xAxis: {
	                categories: [';
	                ksort($mgls);
	                $zap='';
	                foreach ($mgls as $key => $item)
	                {
	                	echo $zap.($key/3600);
	                	$zap=',';
	                }
	                echo '],
	                labels:{
            			rotation:-45
            		}
	            },
		        legend: {
		            enabled: false
		        },
		        exporting: {
		            enabled: false
		        },
				credits: {
					enabled: false
				},
	            series: [{
	                name: \'RSS\',
	                data: [';
	                $zap='';
	                foreach ($mgls as $key => $item)
	                {
	                	echo $zap.$item;
	                	$zap=',';
	                }
	                echo ']
	            }]
	        });
	    });

	/*chartdinful = new Highcharts.Chart({
	    chart: {
	        renderTo: \'container_din_ful\',
	        type: \'line\',
	        marginRight: 130,
	        marginBottom: 25
	    },
	    title: {
	        text: \'Process fulltext per minute\',
	        x: -20 //center
	    },
		xAxis: {
			type: \'datetime\',
			//showLastTickLabel: true,
			//maxZoom: 14 * 24 * 3600000, // fourteen days
			// plotBands: [{
			// 	id: \'mask-before\',
				// from: Date.UTC(2012, 11, 7),
			//	to: Date.UTC(2013, 1, 6),
			// 	color: \'rgba(0, 0, 0, 0)\'
			// }],
			title: {
				text: null
			},
		},
	    yAxis: {
	        title: {
	            text: \'blog_post_prev\'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: \'#808080\'
	        }],
	        min: 0
	    },
	    marker: {
			fillColor: \'#FFFFFF\',
			lineWidth: 2,
			lineColor: null, // inherit from series,
			radius: 3,
			symbol: "circle"
		},
	    tooltip: {
	        // formatter: function() {
	        // 		var newdt=new Date(this.x);
	        // 		console.log(newdt.getHours()-3);
	        // 		//alert(newdt.hours);
	        // 		return \'<b>\'+ this.series.name +\'</b><br/>\'+
	        //         (newdt.getHours()-3) +\':\'+ (newdt.getMinutes()) +\': \'+ this.y +\'°C\';
	        // },
	        shared: true,
	        crosshairs: {
				width: 3
			}
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    legend: {
	        layout: \'vertical\',
	        align: \'right\',
	        verticalAlign: \'top\',
	        x: -10,
	        y: 100,
	        borderWidth: 0
	    },
	    series: [{
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'All\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($din_ful as $key => $item)
	        {
	        	if ($key==mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'))) continue;
	        	echo $zap.intval($item);
	        	$zap=',';
	        }
	        echo ']
	    }]
	});*/


	container_din_process_blogs = new Highcharts.Chart({
	    chart: {
	        renderTo: \'container_din_process_blogs\',
	        type: \'line\',
	        marginRight: 130,
	        marginBottom: 25
	    },
	    title: {
	        text: \'Not availible blogs\',
	        x: -20 //center
	    },
		xAxis: {
			type: \'datetime\',
			//showLastTickLabel: true,
			//maxZoom: 14 * 24 * 3600000, // fourteen days
			// plotBands: [{
			// 	id: \'mask-before\',
				// from: Date.UTC(2012, 11, 7),
			//	to: Date.UTC(2013, 1, 6),
			// 	color: \'rgba(0, 0, 0, 0)\'
			// }],
			title: {
				text: null
			},
		},
	    yAxis: {
	        title: {
	            text: \'blog_post_prev\'
	        },
	        plotLines: [{
	            value: 0,
	            width: 1,
	            color: \'#808080\'
	        }],
	        min: 0
	    },
	    marker: {
			fillColor: \'#FFFFFF\',
			lineWidth: 2,
			lineColor: null, // inherit from series,
			radius: 3,
			symbol: "circle"
		},
	    tooltip: {
	        // formatter: function() {
	        // 		var newdt=new Date(this.x);
	        // 		console.log(newdt.getHours()-3);
	        // 		//alert(newdt.hours);
	        // 		return \'<b>\'+ this.series.name +\'</b><br/>\'+
	        //         (newdt.getHours()-3) +\':\'+ (newdt.getMinutes()) +\': \'+ this.y +\'°C\';
	        // },
	        shared: true,
	        crosshairs: {
				width: 3
			}
	    },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
		credits: {
			enabled: false
		},
	    legend: {
	        layout: \'vertical\',
	        align: \'right\',
	        verticalAlign: \'top\',
	        x: -10,
	        y: 100,
	        borderWidth: 0
	    },
	    series: [{
    		lineWidth: 3,
            cursor: \'pointer\',
	        name: \'All\',
   			pointInterval: 60 * 1000,
			pointStart: Date.UTC('.date('Y',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('n',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('j',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('H',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).', '.date('i',mktime(date('H'),date('i')-$dt_din_acc,0,date('n')-1,date('j'),date('Y'))).'),
	        data: [';
	        $zap='';
	        foreach ($din_procblogs as $key => $item)
	        {
	        	if ($key==mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'))) continue;
	        	echo $zap.intval($item);
	        	$zap=',';
	        }
	        echo ']
	    }]
	});

});

		</script>	
	<script type="text/JavaScript">
	<!--
	function timedRefresh(timeoutPeriod) {
		setTimeout("location.reload(true);",timeoutPeriod);
	}
	'.($torder_id==''?'/*':'').'
	function getData() {
		$.ajax({
			type: "POST",
			url: "getdata.php",
			dataType: "json",
			data: { id: 2, order_id: "'.$torder_id.'" }
		}).done(function( msg ) {
			//alert(msg);
			if (msg!=null)
			{
				$.each(msg, function(index,value){
					$(\'#ord_\'+index).html(value);
				});
			}				
		})
	}
	setInterval(\'getData()\', 5000);
	'.($torder_id==''?'*/':'').'
	function refresh(id) {
		$.ajax({
			type: "POST",
			url: "refresh.php",
			dataType: "json",
			data: { order_id: id }
		})
	}
	//   -->
	</script>
	<script>
		$(function() {
			$( "#tabs" ).tabs();
		});
  </script>
	</head>
	<body style="background-color: #000;" onload="JavaScript:timedRefresh(60000);">
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/highcharts-more.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<table>
<tr>
<td>
<table>
<tr>
<td>
<div id="container" style="width: 250px; height: 250px; display: inline;"></div>
</td>
<td>
<div id="containerd" style="width: 250px; height: 250px;"></div>
</td>
<td valign="top"><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">Prevs...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.$count_prev.'</div><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-top: 3px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">NAblogs...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.intval($lasttt).'</div></td>
<td valign="top"><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;" >NPblogs...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.$count_not_update.'</div><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-top: 3px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">Elastic...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.intval($count_elastic).'</div></td>
<td valign="top"><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;" >Processes</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.$count_process.'</div><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-top: 3px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">Cash...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.intval($count_cash).'</div></td>
<td valign="top"><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;" >LA...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.$la.'</div></td>
<td valign="top"><div style="background-color: #FFF; width: 100px; height: 30px; text-align: center; border-radius: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">Proxy...</div><div style="background-color: #FFF; width: 100px; height: 50px; padding-top: 22px; text-align: center; border-radius: 5px; font-size: 25px; font-weight: bold;">'.$count_proxy.'</div></td>
</tr>
</table>
<table>
<tr>
<td><div id="containerprev" style="width: 504px; height: 240px;"></div></td>
<td><div id="container_din_acc" style="width: 504px; height: 240px;"></div></td>
</tr>
<tr>
<td><div id="container_din_acc_null" style="width: 504px; height: 240px;"></div></td>
<td><div id="container_din_process_blogs" style="width: 504px; height: 240px;"></div></td>
</tr>
<!--<tr>
<td></td>
<td><div id="container_din_eng" style="width: 504px; height: 240px;"></div></td>
<td></td>
</tr>-->
</table>
<div id="container_glas" style="width: 1000px; height: 250px; border-radius: 5px; border-radius: 5px; margin-left: 5px; margin-bottom: 3px; font-size: 20px;"></div>
<div style="width: 1000px; height: 240px; background-color: #FFF; border-radius: 5px; padding: 5px; margin: 5px; overflow: scroll;"><iframe src="http://ec2-54-228-217-24.eu-west-1.compute.amazonaws.com/rcrawler/get_stat.php" width="980" height="340" align="left"></iframe></div>
</td>
<td>
<td valign="top">'.(count($mhcont['order_id'])==0?'<!--':'').'
<div id="tabs" style="width: 492px; font-size: 62.5%;">
	<ul>
	';
	for ($i=0;$i<count($mhcont['order_id']);$i++)
	{
		echo '<li><a href="#tabs-'.$mhcont['order_id'][$i].'">'.$mhcont['order_id'][$i].'</a></li>';
	}
	echo '
	</ul>
	';
	for ($i=0;$i<count($mhcont['logs']);$i++)
	{
		echo '<div id="tabs-'.$mhcont['order_id'][$i].'" style="height: 185px; overflow: scroll;">
	<p id="ord_'.$mhcont['order_id'][$i].'">'.$mhcont['logs'][$i].'</p>
	</div>';
	}
	echo '
</div>'.(count($mhcont['order_id'])==0?'-->':'').'
<div style="background-color: #FFF; width: 500px; height: 150px; text-align: center; border-radius: 5px; margin-top: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 15px;">';
foreach ($all_daemons as $key => $item)
{
	if (!isset($assoc_daemon[$key])) continue;
	echo $assoc_daemon[$key].' '.$item.'
	<br>';
}
echo '
</div>
<div style="background-color: #FFF; width: 500px; height: 150px; text-align: center; border-radius: 5px; margin-top: 5px; margin-bottom: 3px; font-size: 20px;">
<table>
<tr><td>
<iframe src="http://188.120.239.225/tools/dashboard/elastic.php" width="250" height="140" align="left" style="display: inline;"></iframe>
</td><td>
<iframe src="http://ec2-54-228-30-178.eu-west-1.compute.amazonaws.com/tpf/statuses.php" width="220" height="60" align="left"></iframe>
</td></tr>
</table>
</div>
<div style="background-color: #FFF; width: 500px; height: 300px; text-align: center; border-radius: 5px; margin-top: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">
CRIT ORDER('.count($mcache['crit_order']).')<br><div style="margin-left: 30px; overflow: scroll; width: 450px; height: 250px;">';
// print_r($mcache['crit_order']);
foreach ($mcache['crit_order'] as $key => $item)
{
	$form='
	<form method="post" action="http://production.wobot.ru/" target="_blank" style="display: inline;">
	<input type="hidden" name="token" value="'.(md5(mb_strtolower($item['user_email'],'UTF-8').':'.$item['user_pass'])).'">
	<input type="hidden" name="user_id" value="'.$item['user_id'].'">
	<input type="submit" value="Войти в production">
	</form>';
	$refr='';
	if ($item['third_sources']!=2)
	$refr='<a href=\'#\' id="refr'.$key.'" onclick="$(\'#refr'.$key.'\').remove(); refresh('.$key.'); return false;"><img src="refresh.png" height="16"></a>';
	$t=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$item=$item['values'];
	echo '<b>'.$key.'</b>|'.$item[$t-86400*5].' '.$item[$t-86400*4].' '.$item[$t-86400*3].' '.$item[$t-86400*2].' '.$item[$t-86400].' '.$item[$t].' '.$form.' '.$refr.'<br>';
}
echo '
</div>
</div>

<div style="background-color: #FFF; width: 500px; height: 400px; text-align: center; border-radius: 5px; margin-top: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">
FREE SPACE<br><div style="margin-left: 30px; overflow: scroll; width: 450px; height: 350px;">';
// print_r($mcache['crit_order']);
foreach ($instances as $key => $item)
{
	echo '<p style="font-size: 14px;">'.$item.'</p><iframe src="http://'.$item.'/dfh.php" width="468" height="60" align="left"></iframe>';
}
echo '
</div>
</div>

</td>

</td>
</tr>
<tr>
<td>
<div style="background-color: #FFF; width: 1000px; height: 300px; text-align: center; border-radius: 5px; margin-top: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">
WARNING ORDER('.count($mcache['warning_order']).')<br><div style="margin-left: 30px; overflow: scroll; width: 900px; height: 250px;">';
// print_r($mcache['crit_order']);
foreach ($mcache['warning_order'] as $key => $item)
{
	$form='
	<form method="post" action="http://production.wobot.ru/" target="_blank" style="display: inline;">
	<input type="hidden" name="token" value="'.(md5(mb_strtolower($item['user_email'],'UTF-8').':'.$item['user_pass'])).'">
	<input type="hidden" name="user_id" value="'.$item['user_id'].'">
	<input type="submit" value="Войти в production">
	</form>';
	$t=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$item=$item['values'];
	echo '<b>'.$key.'</b>|'.$item[$t-86400*5].' '.$item[$t-86400*4].' '.$item[$t-86400*3].' '.$item[$t-86400*2].' '.$item[$t-86400].' '.$item[$t].' '.$form.'<br>';
}
echo '
</div>
</div>
<div style="background-color: #FFF; width: 1000px; height: 300px; text-align: center; border-radius: 5px; margin-top: 5px; margin-bottom: 3px; padding-top: 12px; font-size: 20px;">
NORM ORDER('.count($mcache['norm_order']).')<br><div style="margin-left: 30px; overflow: scroll; width: 900px; height: 250px;">';
// print_r($mcache['crit_order']);
foreach ($mcache['norm_order'] as $key => $item)
{
	$form='
	<form method="post" action="http://production.wobot.ru/" target="_blank" style="display: inline;">
	<input type="hidden" name="token" value="'.(md5(mb_strtolower($item['user_email'],'UTF-8').':'.$item['user_pass'])).'">
	<input type="hidden" name="user_id" value="'.$item['user_id'].'">
	<input type="submit" value="Войти в production">
	</form>';
	$t=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$item=$item['values'];
	echo '<b>'.$key.'</b>|'.$item[$t-86400*5].' '.$item[$t-86400*4].' '.$item[$t-86400*3].' '.$item[$t-86400*2].' '.$item[$t-86400].' '.$item[$t].' '.$form.'<br>';
}
echo '
</div>
</div>

</td>
</tr>
</table>
	</body>
</html>
';
/*
$var=$redis->get('order_'.$row['order_id'].'_'.$t);
			$m_dinams=json_decode($var,true);
			$out['graph'][$t]=intval($m_dinams['count_post']);
*/


?>