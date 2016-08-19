<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

echo '
<form id="sf">
<select size="3" name="hero" onchange="document.getElementById(\'sf\').submit();">
    <option value="1">День</option>
    <option value="7">Неделя</option>
    <option value="30">Месяц</option>
</select>
</form>
';

$users=$db->query('SELECT a.order_id,a.order_date,a.user_id,a.ut_id,b.user_ctime FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON a.ut_id=c.ut_id WHERE c.tariff_id=3 OR c.tariff_id=16');

while ($user=$db->fetch($users))
{
	if ($user['user_ctime']!=0)
	$us_count[mktime(0,0,0,date('n',$user['user_ctime']),date('j',$user['user_ctime']),date('Y',$user['user_ctime']))]++;
}

//print_r($us_count);

$users=$db->query('SELECT b.user_id,b.user_ctime FROM users as b LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE c.tariff_id=3 OR c.tariff_id=16');
while ($user=$db->fetch($users))
{
	if ($user['user_ctime']!=0)
	$us_all_count[mktime(0,0,0,date('n',$user['user_ctime']),date('j',$user['user_ctime']),date('Y',$user['user_ctime']))]++;
}
krsort($us_count);
krsort($us_all_count);
$kk=0;
foreach ($us_all_count as $key => $item)
{
	$arall1[$kk]['count']=$item;
	$arall1[$kk]['date']=$key;
	//echo $arall[$kk]['count'].' '.date('r',$arall[$kk]['date']).'<br>';
	$kk++;
}
$kk=0;
//print_r($us_count);
foreach ($us_count as $key => $item)
{
	$ar[$kk]['count']=$item;
	$ar[$kk]['date']=$key;
	$kk++;
}
//echo $arall1[0]['date'].'|'.$arall1[count($arall1)-1]['date'];
$kk=0;
$i=0;
for($t=$arall1[count($arall1)-1]['date'];$t<=mktime(0,0,0,date('n'),date('j'),date('Y'));$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
{
	if ($t<($arall1[count($arall1)-1]['date']+($i+1)*$_GET['hero']*86400))
	{
		$arall[$kk]['count']+=intval($us_all_count[$t]);
		$arall[$kk]['date']=$arall1[count($arall1)-1]['date']+$i*$_GET['hero']*86400;
	}
	else
	{
		$i++;
		$kk++;
		$arall[$kk]['count']=intval($us_all_count[$t]);
		$arall[$kk]['date']=$arall1[count($arall1)-1]['date']+($i+1)*$_GET['hero']*86400;
	}
}
$kk=0;
$i=0;
for($t=$arall1[count($arall1)-1]['date'];$t<=mktime(0,0,0,date('n'),date('j'),date('Y'));$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
{
	//echo $t.' ';
	if ($t<($arall1[count($arall1)-1]['date']+($i+1)*$_GET['hero']*86400))
	{
		$ar1[$kk]['count']+=intval($us_count[$t]);
		$ar1[$kk]['date']=$arall1[count($arall1)-1]['date']+$i*$_GET['hero']*86400;
	}
	else
	{
		$i++;
		$kk++;
		$ar1[$kk]['count']=intval($us_count[$t]);
		$ar1[$kk]['date']=$arall1[count($arall1)-1]['date']+($i+1)*$_GET['hero']*86400;
	}
}

echo '
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="http://production.wobot.ru/js/jquery.js"></script>
<script type="text/javascript" src="http://production.wobot.ru/js/highcharts.js"></script>
<script>
	var data = [
		';
		foreach ($arall as $key => $item)
		{
			echo $zap.$item['count'];
			$zap=',';
		}
		echo '
	];
	var data12 = [
		';
		$zap='';
		foreach ($ar1 as $key => $item)
		{
			echo $zap.$item['count'];
			$zap=',';
		}
		echo '
	];

var chart;
$(document).ready(function() {
	chart = new Highcharts.Chart({
		chart: {
			renderTo: \'container\',
			type: \'line\',
			marginRight: 130,
			marginBottom: 25
		},
		title: {
			text: \'График регистрации новых пользователей и созданных ими тем\',
			x: -20 //center
		},
		subtitle: {
			text: \'\',
			x: -20
		},
		xAxis: {
			type: \'datetime\',
			showLastTickLabel: true,
			maxZoom: 14 * 24 * 3600000, // fourteen days
			plotBands: [{
				id: \'mask-before\',
				from: Date.UTC('.date('Y',$arall[0]['date']).', '.(date('n',$arall[0]['date'])-1).', '.date('j',$arall[0]['date']).'),
				to: Date.UTC('.date('Y',$arall[count($arall)-1]['date']).', '.(date('n',$arall[count($arall)-1]['date'])-1).', '.date('j',$arall[count($arall)-1]['date']).'),
				color: \'rgba(0, 0, 0, 0)\'
			}],
			title: {
				text: null
			},
		},
		yAxis: {
			title: {
				text: \'\'
			},
			plotLines: [{
				value: 0,
				width: 1,
				color: \'#808080\'
			}]
		},
		tooltip: {
			shared: true,
			crosshairs: {
				width: 3
			}
		},
		legend: {
			layout: \'vertical\',
			align: \'right\',
			verticalAlign: \'top\',
			x: -10,
			y: 100,
			borderWidth: 0
		},
		plotOptions: {
			series: {
				cursor: \'pointer\',
				point: {
					events: {
						click: function() {
							hs.htmlExpand(null, {
								pageOrigin: {
									x: this.pageX,
									y: this.pageY
								},
								headingText: this.series.name,
								maincontentText: Highcharts.dateFormat(\'%A, %b %e, %Y\', this.x) +\':<br/> \'+
									this.y +\' visits\',
								width: 200
							});
						}
					}
				},
				marker: {
					lineWidth: 1
				}
			}
		},
		series: [{
			name: \'Регистрации\',
			pointInterval: '.$_GET['hero'].' * 24 * 3600 * 1000,
			pointStart: Date.UTC('.date('Y',$arall[0]['date']).', '.(date('n',$arall[0]['date'])-1).', '.date('j',$arall[0]['date']).'),
			data: data
		}, {
			name: \'Темы\',
			pointInterval: '.$_GET['hero'].' * 24 * 3600 * 1000,
			pointStart: Date.UTC('.date('Y',$arall[0]['date']).', '.(date('n',$arall[0]['date'])-1).', '.date('j',$arall[0]['date']).'),
			data: data12
		}]
	});
});
</script>
</head>
<body>
<div id="container" style="width: 100%; height: 500px"></div>
</body>
';
?>