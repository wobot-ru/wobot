<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Проверка наличия источника в базе WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="http://www.wobot.ru/stylesheets/jquery.fancybox-1.3.0.css" rel="stylesheet" type="text/css" />
    <link href="favicon.png" rel="shortcut icon" />
    <script src="http://www.wobot.ru/javascripts/jquery-1.5.min.js" type="text/javascript"></script>
    <script src="http://www.wobot.ru/javascripts/popup.js" type="text/javascript"></script>
    <script src="http://www.wobot.ru/javascripts/jquery.fancybox-1.3.0.pack.js" type="text/javascript"></script>
    <script src="http://www.wobot.ru/javascripts/price_sig.js" type="text/javascript"></script>
  </head>
  <body style="background: #fff;">
	<h1>Проверка наличия источника в базе WOBOT</h1>
	<form method="post">
	Введите домен ресурса: <input type="text" name="domain" value="<?=(mb_strlen($_POST['domain'],'UTF-8')>0)?($_POST['domain']):('twitter.com')?>"/>
	<input type="submit" value="проверить">
	</form>
<?



function check_src($hn)
{
	global $db;
	$outmas['in_base']=0;
	$outmas['in_azure']=0;
	$qw=$db->query('SELECT * FROM blog_post WHERE post_host=\''.$hn.'\' LIMIT 1');
	//echo 'SELECT * FROM blog_post WHERE post_host=\''.$hn.'\' LIMIT 1';
	$count=$db->fetch($qw);
	if ($count>0)
	{
		$outmas['in_base']=1;
	}
	$cont=parseUrl('http://wobotrest.cloudapp.net/contains.aspx?domain='.$hn);
	if ($cont=='yes')
	{
		$outmas['in_azure']=1;
	}
	return $outmas;
}

//echo $_POST['domain'];

if (mb_strlen($_POST['domain'],'UTF-8')>0)
{
	$res=check_src($_POST['domain']);
	//print_r($res);
	if (($res['in_azure']==1)||($res['in_base']==1)) echo '<script>alert(\'Источник ЕСТЬ в базе\');</script>';
	else echo '<script>alert(\'Источника НЕТ в базе\');</script>';
}
//echo $_POST['domain'];

?>
	</body>
</html>