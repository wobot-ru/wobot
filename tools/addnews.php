<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

echo '<meta charset="utf-8"><div style="border: 1px solid black;">Пользователь: wobotresearch1@yandex.ru Пароль: wobotresearch</div>';

if ($_GET['del_id']!='')
{
	$db->query('DELETE FROM blog_tp WHERE tp_id='.intval($_GET['del_id']));
}
if ($_POST['addid']!='')
{
	$mid=explode(',', $_POST['addid']);
	foreach ($mid as $id)
	{
		if ($_POST['type']!='')
		{
			$qadd=$db->query('SELECT * FROM blog_tp WHERE order_id='.$_POST['order_id'].' AND gr_id=\''.addslashes($id).'\' LIMIT 1');
			if ($db->num_rows($qadd)==0) $db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$_POST['order_id'].',\''.addslashes($id).'\',\''.$_POST['type'].'\')');
		}
	}
}
if ($_POST['newsgo_order_id']!='')
{
	$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['newsgo_order_id']).' LIMIT 1');
	$order=$db->fetch($qorder);
	if ($_POST['newsgo_type']=='yandex_news') header('Location: http://news.yandex.ru/yandsearch?text='.urlencode($order['order_keyword']).'&rpt=nnews2&grhow=clutop');
	elseif ($_POST['newsgo_type']=='google_news') header('Location: https://news.google.ru/news/search?hl=ru&ned=ru_ru&q='.urlencode($order['order_keyword']));
	elseif ($_POST['newsgo_type']=='novoteka_news') header('Location: http://www.novoteka.ru/search?query='.urlencode(iconv('UTF-8','windows-1251',$order['order_keyword'])));
	die();
}

echo '<form method="GET" id="submform">Кабинет: <select name="user_id" onchange="document.getElementById(\'submform\').submit();">';
echo '<option selected></option>';
$qusers=$db->query('SELECT * FROM users ORDER BY user_id DESC');
while ($user=$db->fetch($qusers))
{
	echo '<option value="'.$user['user_id'].'" '.($_GET['user_id']==$user['user_id']?'selected':'').'>'.$user['user_id'].' '.$user['user_email'].'</option>';
}
echo '</select></form>';

if ($_GET['user_id']!='') 
{
	echo '<form method="POST" id="newsgo" action="?user_id='.$_GET['user_id'].'" target="_blank">
	<input type="hidden" name="newsgo_order_id" id="idnews">
	<input type="hidden" name="newsgo_type" id="typenews">
	</form>';
	echo '<form method="POST" action="?user_id='.$_GET['user_id'].'">';
	echo 'Отчет: <select name="order_id" id="selid">';
	$qorder=$db->query('SELECT * FROM blog_orders WHERE user_id='.$_GET['user_id']);
	while ($order=$db->fetch($qorder))
	{
		echo '<option value="'.$order['order_id'].'">'.$order['order_id'].' '.$order['order_name'].'</option>';
	}
	echo '</select> <a href="#" onclick="document.getElementById(\'typenews\').value=\'yandex_news\'; document.getElementById(\'idnews\').value=document.getElementById(\'selid\').value; document.getElementById(\'newsgo\').submit();">Яндекс новости</a> <a href="#" onclick="document.getElementById(\'typenews\').value=\'google_news\'; document.getElementById(\'idnews\').value=document.getElementById(\'selid\').value; document.getElementById(\'newsgo\').submit();">Google новости</a> <a href="#" onclick="document.getElementById(\'typenews\').value=\'novoteka_news\'; document.getElementById(\'idnews\').value=document.getElementById(\'selid\').value; document.getElementById(\'newsgo\').submit();">Новотека новости</a><br>';
	echo '<textarea name="addid" cols="50" rows="10"></textarea><br><select name="type"><option value="google_news">google новости<option selected value="yandex_news">yandex новости<option value="novoteka_news">новотека новости</select><br><input type="submit" value="Добавить"></form>';
}

if ($_GET['user_id']!='')
{
	$qtp=$db->query('SELECT * FROM blog_tp as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.$_GET['user_id'].' AND (a.tp_type=\'yandex_news\' OR a.tp_type=\'google_news\' OR a.tp_type=\'novoteka_news\')');
	while ($tp=$db->fetch($qtp))
	{
		echo 'id: '.$tp['tp_id'].'; order_id: '.$tp['order_id'].'; название отчета: '.$tp['order_name'].'; тип новости: '.$tp['tp_type'].'; запрос: '.mb_substr($tp['gr_id'],0,50,'UTF-8').'... <a href="?user_id='.$_GET['user_id'].'&del_id='.$tp['tp_id'].'">X</a>'.'<br>';
	}
}

?>