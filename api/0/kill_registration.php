<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();
//auth();

//echo 'DELETE FROM users WHERE user_email=\'dolby0@gmail.com\'';
$db->query('DELETE FROM users WHERE user_email=\'dolby0@gmail.com\'');

?>