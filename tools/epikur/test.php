<?

require_once('/var/www/tools/epikur/infix.php');

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

echo '<form method="POST">
<pre>
Текст:           <input type="text" name="text" style="width: 1000px; font-size: 32px;"><br>
Ключевой запрос: <input type="text" name="kw" style="width: 1000px; font-size: 32px;">
</pre>
<input type="submit" value="проверить">
</form>';

if (($_POST['text']!='') && ($_POST['kw']!=''))
{
	if (check_post($_POST['text'],$_POST['kw'])==1)
	{
		echo '<div style="color: #00ff00">'.$_POST['kw'].'|||'.$_POST['text'].'</div>';
	}
	else
	{
		echo '<div style="color: #ff0000">'.$_POST['kw'].'|||'.$_POST['text'].'</div>';
	}
}

?>