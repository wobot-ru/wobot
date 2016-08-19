<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Утилиты для тестирования WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>
<body>
<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();
/*$i=0;
for ($i=1;$i<count($argv);$i++)
{
	if ($i>1) $where.=' or';
	$where.=' order_id='.intval($argv[$i]);
}*/

//echo 'SELECT * FROM blog_orders where order_id=442';

function detect_troubles($blog)
{
	$errors=0;
	$warns=0;
	$html_out = '';
	if ((strlen($blog['order_name'])==0)&&(strlen($blog['order_keyword'])==0)) { $html_out .= '<font color="red">Ошибка. Запрос у кабинета пуст</font><br>'; $errors++; }
	if (intval($blog['user_id'])==0) { $html_out .= '<font color="red">Ошибка. Пользователь кабинета не назначен</font><br>'; $errors++; }
	if (intval($blog['order_start'])==0) { $html_out .= '<font color="red">Ошибка. Дата начала сбора не указана</font><br>'; $errors++; }
	if ((intval($blog['order_start'])>intval($blog['order_end']))&&($blog['order_end']>0)) { $html_out .= '<font color="red">Ошибка. Интервал сбора указан не верно</font><br>'; $errors++; }
	if (intval($blog['order_start'])==0) { $html_out .= '<font color="red">Ошибка. Дата начала сбора не указана</font><br>'; $errors++; }
	if (intval($blog['order_last'])==0) { $html_out .= '<font color="red">Ошибка. Кабинет не собран</font><br>'; $errors++; }
	//if (intval($blog['order_last_rss'])==0) { $html_out .= '<font color="red">Ошибка. Кабинет не собран (rss ленты)</font><br>'; $errors++; }
	if (intval($blog['ut_id'])==0) { $html_out .= '<font color="red">Ошибка. Кабинет не относится к тарифу</font><br>'; $errors++; }

	if ((intval($blog['order_end'])<mktime(0, 0, 0, date("m"),date("d"),date("Y")))&&($blog['order_last']<$blog['order_end'])) { $html_out .= '<font color="red">Ошибка. Кабинет собран не полностью</font><br>'; $errors++; }
	if (($blog['order_end']>=mktime(0, 0, 0, date("m"),date("d"),date("Y")))&&($blog['order_start']<mktime(0, 0, 0, date("m"),date("d"),date("Y")))&&($blog['order_last']<mktime(0, 0, 0, date("m"),date("d"),date("Y")))) { $html_out .= '<font color="red">Ошибка. Кабинет реал-тайм собран не полностью</font><br>'; $errors++; }
	if (($blog['order_end']==0)&&($blog['order_start']<mktime(0, 0, 0, date("m"),date("d"),date("Y")))&&($blog['order_last']<mktime(0, 0, 0, date("m"),date("d"),date("Y")))) { $html_out .= '<font color="red">Ошибка. Кабинет с открытым интервалом собран не полностью</font><br>'; $errors++; }
	
	//ful_com, order_engage, order_graph
	
	//order_src
	$sources=json_decode($blog['order_src'], true);

	foreach ($sources as $i => $source)
	{
			$other+=$source;
	}
	if (intval($other)==0) { $html_out .= '<font color="red">Ошибка. В кабинете нет сообщений</font><br>'; $errors++; }
	
	//order_metrics
	$metrics=json_decode($blog['order_metrics'],true);

	if (intval($metrics['speakers']['uniq'])==0) { $html_out .= '<font color="#cc0">Предупреждение. Кол-во уникальных авторов равно нулю.</font><br>'; $warns++; }

	if (count($metrics['speakers']['posts'])==0) { $html_out .= '<font color="red">Ошибка. Спикеры не собраны</font><br>'; $errors++; }
	$nicks=0;
	foreach ($metrics['speakers']['nick'] as $nick) if ($nick=='') $nicks++;
	if ($nicks>0) { $html_out .= '<font color="red">Ошибка. Спикеры пустые ники</font><br>'; $errors++; }
	$nicks=0;
	foreach ($metrics['speakers']['login'] as $nick) if ($nick=='') $nicks++;
	if ($nicks>0) { $html_out .= '<font color="red">Ошибка. Спикеры пустые логины</font><br>'; $errors++; }
	
	if (($blog['order_engage']==1)&&(intval($metrics['engagement'])==0)) { $html_out .= '<font color="#cc0">Предупреждение. Нулевой engagement</font><br>'; $warns++; }
	if (intval($metrics['value'])==0)  { $html_out .= '<font color="#cc0">Предупреждение. Нулевая аудитория</font><br>'; $warns++; }
	
	if (count($metrics['promotion']['readers'])==0) { $html_out .= '<font color="red">Ошибка. Промоутеры не собраны</font><br>'; $errors++; }
	$nicks=0;
	foreach ($metrics['promotion']['nick'] as $nick) if ($nick=='') $nicks++;
	if ($nicks>0) { $html_out .= '<font color="red">Ошибка. Промоутеры пустые ники</font><br>'; $errors++; }
	$nicks=0;
	foreach ($metrics['promotion']['login'] as $nick) if ($nick=='') $nicks++;
	if ($nicks>0) { $html_out .= '<font color="red">Ошибка. Промоутеры пустые логины</font><br>'; $errors++; }	
	
	if (count($metrics['topwords'])==0) { $html_out .= '<font color="red">Ошибка. Облако тегов не собрано</font><br>'; $errors++; }
	if (count($metrics['location'])==0) { $html_out .= '<font color="red">Ошибка. Гео-информация не собрана</font><br>'; $errors++; }
	if (count($metrics['location_cou'])==0) { $html_out .= '<font color="red">Ошибка. Гео-информация собрана не верно (нет стран)</font><br>'; $errors++; }
	
	return array('html'=>$html_out,'errors'=>$errors,'warns'=>$warns);
}

$total_errors=0;
$total_warns=0;

echo '<a href="?order=date">важные</a> / <a href="?order=errors">кол-во ошибок</a><br><br>';

$ressec=$db->query('SELECT * FROM blog_orders AS b LEFT JOIN users AS u ON b.user_id=u.user_id'.($_GET['order']=='errors'?'':' ORDER BY b.order_end DESC'));
echo 'orders to check: '.mysql_num_rows($ressec)."<br>";
$mode='wb';

while($blog=$db->fetch($ressec))
{
	$result=detect_troubles($blog);
	if (($result['errors']>0)||($result['warns']>0))
	{
		//echo $blog['user_email'].' '.$blog['order_id'].' <b>'.(strlen($blog['order_name'])>0?$blog['order_name']:$blog['order_keyword']).'</b> <a href="/tools/cashjob.php?order_id='.$blog['order_id'].'" target="_blank">обновить кэш</a><br>'.$result['html'].'<br><br>';
		$emails[]=$blog['user_email'];
		$orders[]=$blog['order_id'];
		$names[]=(strlen($blog['order_name'])>0?$blog['order_name']:$blog['order_keyword']);
		$htmls[]=$result['html'];
		$errors[]=$result['errors'];
		$warns[]=$result['warns'];
		
		$total_errors+=$result['errors'];
		$total_warns+=$result['warns'];
	}
}

if ($_GET['order']=='errors') array_multisort($errors, SORT_DESC, $warns, $emails, $orders, $names, $htmls);

echo 'Ошибок: '.$total_errors.', Предупреждений: '.$total_warns.'<br><br>';

foreach ($errors as $key=>$errors)
{
	echo $emails[$key].' '.$orders[$key].' <b>'.$names[$key].'</b> <a href="/tools/cashjob.php?order_id='.$orders[$key].'" target="_blank">обновить кэш</a><br>'.$htmls[$key].'<br><br>';
}
?>
</body>
</html>
