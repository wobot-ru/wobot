<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');

$db = new database();
$db->connect();

$imail=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['user_id']).' AND user_pass=\''.$_GET['token'].'\'');
$user=$db->fetch($imail);
$mails=explode(',',$user['user_mails']);
foreach ($mails as $mail)
{
	if (md5(trim($mail))!=$_GET['mt'])
	{
		$upd.=$zap.$mail;
		$zap=',';
	}
}
//echo 'UPDATE users SET user_mails=\''.$upd.'\' WHERE user_id='.intval($_GET['user_id']).' AND user_pass=\''.$_GET['token'].'\'';
$imail=$db->query('UPDATE users SET user_mails=\''.$upd.'\' WHERE user_id='.intval($_GET['user_id']).' AND user_pass=\''.$_GET['token'].'\'');
echo '
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
Ваша подписка отменена!';

?>