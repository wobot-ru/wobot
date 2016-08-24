<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/api/0/auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();

$data=$db->query('SELECT * FROM user_tariff as a left join blog_tariff as b on a.tariff_id=b.tariff_id WHERE user_id='.intval($user['user_id']));
$inf=$db->fetch($data);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Биллинг WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="http://beta.wobot.ru/css/main.css">
   	<link rel="stylesheet" type="text/css" href="http://beta.wobot.ru/css/msglist.css">
</head>
<body style="background-color: #ECECEC; padding: 10px;">
<h1><? echo $inf['tariff_name'] ?></h1>
<p><? echo $inf['tariff_desc'] ?></p>
	</body>
	</html>
