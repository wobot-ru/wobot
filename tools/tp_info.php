<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?
date_default_timezone_set ( 'Europe/Moscow' );
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
$db = new database();
$db->connect();
echo '<table border="1">';
echo '<tr><td>id</td><td width="70">order_name</td><td width="150">Ключевые слова</td><td>Дата окончания</td><td>Дата сбора</td><td width="50">Время сбора(сек)</td><td>Оставшееся время(дней)</td></tr>';
$i=0;
//echo 'SELECT * FROM blog_orders WHERE third_sources!=0 AND order_end>'.mktime(0,0,0,date('n'),date('j'),date('Y')).' ORDER BY third_sources DESC';
$qinfo=$db->query('SELECT order_id,order_name,order_keyword,order_end,third_sources FROM blog_orders WHERE third_sources!=0 AND user_id!=0 AND order_end>'.mktime(0,0,0,date('n'),date('j'),date('Y')).' ORDER BY third_sources DESC');
while ($order=$db->fetch($qinfo))
{
	$out[$i]['id']=$order['order_id'];
	$out[$i]['name']=$order['order_name'];
	$out[$i]['keyword']=$order['order_keyword'];
	$out[$i]['end']=$order['order_end'];
	$out[$i]['ts']=$order['third_sources'];
	if ($i!=0)
	{
		$out[$i-1]['delta']=$out[$i-1]['ts']-$out[$i]['ts'];
	}
	//echo '<tr><td>'.$order['order_id'].'</td><td width="70">'.$order['order_name'].'</td><td width="150">'.$order['order_keyword'].'</td><td>'.($order['order_end']!=1?date('j.n.Y G:i:s',$order['order_end']):'не собран').'</td><td>'.($order['third_sources']!=1?date('j.n.Y G:i:s',$order['third_sources']):'не собран').'</td><td width="50">'.($i!=1?($order['third_sources']==1?'-':($prev-$order['third_sources'])):'-').'</td></tr>';
	//$prev=$order['third_sources'];
	$i++;
}
foreach ($out as $item)
{
	echo '<tr><td>'.$item['id'].'</td><td width="70">'.$item['name'].'</td><td width="150">'.$item['keyword'].'</td><td>'.date('j.n.Y G:i:s',$item['end']).'</td><td>'.date('j.n.Y G:i:s',$item['ts']).'</td><td width="50"><b>'.$item['delta'].'</b></td><td>'.intval((($item['end']-$item['ts'])/86400)+1).'</td></tr>';
}
echo '</table>';
?>