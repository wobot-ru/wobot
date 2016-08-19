<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>WOBOT &copy; Панель администратора (&beta;-version)</title>
<meta name="description" content="" />
<meta name="keywords" content="Wobot реклама анализ раскрутка баннер" />
<meta name="author" content="Wobot media" />
<meta name="robots" content="all" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script type="text/javascript" src="ckeditor.js"></script>
<script src="sample.js" type="text/javascript"></script>
<link href="sample.css" rel="stylesheet" type="text/css" />

</head>
';
//print_r($_POST);
if ($_POST['id']!='')
{
	$db->query('UPDATE msg_tpl SET name=\''.addslashes($_POST['title']).'\',gl=\''.addslashes($_POST['gl']).'\', message=\''.addslashes($_POST['text']).'\' WHERE id='.intval($_POST['id']));
	//echo 'UPDATE msg_tpl SET name=\''.addslashes($_POST['title']).'\',gl=\''.addslashes($_POST['gl']).'\', message=\''.addslashes($_POST['text']).'\' WHERE id='.intval($_POST['id']);
}
if (($_POST['add']==1) && ($_POST['title']!='') && ($_POST['text']!='') && ($_POST['gl']!=''))
{
	$db->query('INSERT INTO msg_tpl (name,gl,message) VALUES (\''.addslashes($_POST['title']).'\',\''.addslashes($_POST['gl']).'\',\''.addslashes($_POST['text']).'\')');
}
if ($_POST['act']=='del')
{
	$db->query('DELETE FROM msg_tpl WHERE id='.intval($_POST['id']));
}
$im=$db->query('SELECT * FROM msg_tpl');
while ($msg=$db->fetch($im))
{
	echo '<form action="http://bmstu.wobot.ru/tools/editmsg/editmsg.php" method="POST"><input type="hidden" value="'.$msg['id'].'" name="id">id: '.$msg['id'].' <br>Назначение сообщения:<br><input style="width: 400px;" name="gl" value="'.$msg['gl'].'"><br>Заголовок сообщения:<br><input style="width: 400px;" name="title" value="'.$msg['name'].'"><br>Текст сообщения:<br><textarea id="editor'.$msg['id'].'" rows="5" cols="45" name="text">'.$msg['message'].'</textarea><input type="submit" value="сохранить"></form>';
	if ($msg['id']>6)
	{
		echo '<form action="http://bmstu.wobot.ru/tools/editmsg/editmsg.php" method="POST"><input type="submit" value="удалить"><input type="hidden" value="del" name="act"><input type="hidden" name="id" value="'.$msg['id'].'"></form>';
	}
	echo'<br>
	<script type="text/javascript">
	//<![CDATA[

		CKEDITOR.replace( \'editor'.$msg['id'].'\',
			{
				skin : \'kama\'
			});

	//]]>
	</script>
	
	';
}
echo '<form action="http://bmstu.wobot.ru/tools/editmsg/editmsg.php" method="POST"><input type="hidden" value="1" name="add">Назначение сообщения:<br><input style="width: 400px;" name="gl" value=""><br>Заголовок сообщения:<br><input style="width: 400px;" name="title" value=""><br>Текст сообщения:<br><textarea id="editor'.$msg['id'].'" rows="5" cols="45" name="text"></textarea><input type="submit" value="добавить"></form><br>
<script type="text/javascript">
//<![CDATA[

	CKEDITOR.replace( \'editor'.$msg['id'].'\',
		{
			skin : \'kama\'
		});

//]]>
</script>

';
?>