<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

// echo 'SELECT SUM(post_engage) FROM blog_post WHERE post_host=\'twitter.com\' post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y'));
$qeng=$db->query('SELECT SUM(post_engage) as cnt,post_host FROM blog_post WHERE post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' GROUP BY post_host ORDER BY cnt DESC');
while ($eng=$db->fetch($qeng))
{
	if ($eng['cnt']==0) continue;
	$t.=$eng['cnt'].' '.$eng['post_host'].'<br>';
}

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
mail('zmei123@yandex.ru','Тест engage',$t,$headers);
mail('r@wobot.co','Тест engage',$t,$headers);

?>