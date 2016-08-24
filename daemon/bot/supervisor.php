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
$i=0;
for ($i=1;$i<count($argv);$i++)
{
	if ($i>1) $where.=' or';
	$where.=' order_id='.intval($argv[$i]);
}

//echo 'SELECT * FROM blog_orders where order_id=442';
$ressec=$db->query('SELECT * FROM blog_orders');
echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
$mode='wb';

function detect_troubles($blog)
{
	if ((strlen($blog['order_name'])==0)&&(strlen($blog['order_keyword'])==0)) echo '<font color="red">Ошибка. Запрос у кабинета пуст</font><br>';
	if (intval($blog['user_id'])==0) echo '<font color="red">Ошибка. Пользователь кабинета не назначен</font><br>';
	if (intval($blog['order_start'])==0) echo '<font color="red">Ошибка. Дата начала сбора не указана</font><br>';
	if (intval($blog['order_start'])>intval($blog['order_end'])) echo '<font color="red">Ошибка. Интервал сбора указан не верно</font><br>';
	if (intval($blog['order_start'])==0) echo '<font color="red">Ошибка. Дата начала сбора не указана</font><br>';
	if (intval($blog['order_end'])==0) echo '<font color="red">Ошибка. Дата завершения сбора не указана</font><br>';
	if (intval($blog['order_last'])==0) echo '<font color="red">Ошибка. Кабинет не собран</font><br>';
	//if (intval($blog['order_last_rss'])==0) echo '<font color="red">Ошибка. Кабинет не собран (rss ленты)</font><br>';
	if (intval($blog['ut_id'])==0) echo '<font color="red">Ошибка. Кабинет не относится к тарифу</font><br>';

	//order_last, order_last_rss, graph
	if ((intval($blog['order_end'])<mktime())&&($blog['order_last']<=$blog['order_end'])) echo '<font color="red">Ошибка. Кабинет собран не полностью</font><br>';
	if ($blog['order_end']>=mktime())&&($blog['order_start']<mktime())&&($blog['order_last']<mktime()) echo '<font color="red">Ошибка. Кабинет собран не полностью</font><br>';
	
}

while($blog=$db->fetch($ressec))
{
	echo $blog['order_id'].' '.$blog['order_name'].'<br>';
	//echo '<textarea>';
	//print_r($blog);
	detect_troubles($blog);
	//echo '</textarea><br>
	//';
	echo '<br><br>';
}
?>
