<?


//TODO:
// query
// order select
// &, nbsp, mdash -replace
// > < -wrong tags replace


if (intval($_POST['order_id'])==0)
{
echo '
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1>Экспорт в Carrot2</h1>
<form method="post">
Номер темы: <input type="text" name="order_id"/><br/>
Начало интервала: <input type="text" name="start" value="'.date('d.m.Y').'"/><br/>
Конец интервала: <input type="text" name="end" value="'.date('d.m.Y').'"/><br/>
<input type="submit" name="Сгенерировать"/>
</form>
</body>
</html>
';

}
else
{

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$order_id=intval($_POST['order_id']);
$start=strtotime($_POST['start']);
$end=strtotime($_POST['end']);

$db = new database();
$db->connect();

header ("Content-Type:text/xml");
header('Content-Disposition: attachment; filename="'.$order_id.'.xml"');

echo '<?xml version="1.0" encoding="UTF-8"?>
<searchresult>
  <query></query>
';
$i=0;

$qw=$db->query('SELECT p.post_content, p.post_link, b.blog_login FROM blog_post AS p LEFT JOIN robot_blogs2 AS b on p.blog_id=b.blog_id WHERE p.order_id='.$order_id.' and p.post_time>'.intval($start).' and p.post_time<'.intval($end));
while ($row = $db->fetch($qw)) {
		echo'
  <document id="'.$i.'">
    <title></title>
    <url>'.strip_tags(strip_tags(str_replace('&', '', $row['post_link']))).'</url>
    <snippet>
      '.strip_tags(preg_replace("/[^A-Za-z0-9а-яА-Я\-\_ ]/ius", '', $row['post_content'])).'
    </snippet>
  </document>
  ';
  $i++;
}

echo '</searchresult>';

}
?>
