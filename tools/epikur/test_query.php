<?

require_once('/var/www/stroik/chack_brack.php');

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

echo '<form method="POST">
<pre>
<!--Текст:           <input type="text" name="text" style="width: 1000px; font-size: 32px;"><br>-->
Ключевой запрос: <input type="text" name="kw" style="width: 1000px; font-size: 32px;">
</pre>
<input type="submit" value="проверить">
</form>';

if ($_POST['kw']!='')
{
	if (check_query($_POST['kw'])==1)
	{
		echo '<div style="color: #00ff00">'.$_POST['kw'].'</div>';
	}
	else
	{
		echo '<div style="color: #ff0000">'.$_POST['kw'].'</div>';
	}
}

$tests=array(
	array('(тест1 && (тест2))','(тест1 && (тест2)'),
	array('(((тест1) && (тест2))|(тест))','(тест1 && (тест2)())'),
	array('"путин && медведев"','путин && "медведев'),
	array('"путин" && "медведев"','"путин && "медведев"'),
	array('"путин" && "медведев"&"123"','"путин" && "медведев"&&""'),
	array('путин && ":)"','"путин ( краб " && медведев)'),
	array('"«путин"','«путин'),
	array('"”путин"','”путин” && "медведев"'),
	array('"путин«"','«путин» && "медведев"'),
	array('"медведев || путин"','медведев | путин ||'),
	array('"медведев && путин"','медведев & & путин'),
	array('что? где? когда?',''),
	array('Алло! && оператор',''),
	array('@лло! && оператор',''),
	array('http://ya.ru && новая версия',''),
	array('@_rcp && rabbitmq',''),
	array('ТКС-банк',''),
	array('newsru.ru/kak_sdelat_svoy_biznes && статья',''),
	array('Ткс банк',''),
	array('wobot | #wobot','!'),
	array('Кето | Кето+','?'),
	array('"P&G" | Proctal and gamble',''),
	array('"привет!@#$%^&*()¡™£¢∞§¶•ªº"',''),
	array('123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789','123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 123456789 '),
	);
echo '<table>';
foreach($tests as $test)
{
	echo '<tr>';
	if (check_query($test[0])==1)
	{
		echo '<td><div style="color: #00aa00">'.$test[0].'</div></td>';
	}
	else
	{
		echo '<td><div style="color: #aa0000">'.$test[0].'</div></td>';
	}
	if (check_query($test[1])==1)
	{
		echo '<td><div style="color: #00aa00">'.$test[1].'</div></td>';
	}
	else
	{
		echo '<td><div style="color: #aa0000">'.$test[1].'</div></td>';
	}
	echo '</tr>';
}
echo '</table>';
// check_query();
// $mquery[]='';

?>