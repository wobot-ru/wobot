<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('hotkeys',$_POST);

$settings=json_decode($user['user_settings'],true);
$hotkeys=json_decode($settings['hotkeys'],true);
if ($hotkeys['43']=='') $hotkeys['43']='positive_tone';
if ($hotkeys['45']=='') $hotkeys['45']='negative_tone';
if ($hotkeys['48']=='') $hotkeys['48']='neutral_tone';
if ($hotkeys['49']=='') $hotkeys['49']='tag_1';
if ($hotkeys['50']=='') $hotkeys['50']='tag_2';
if ($hotkeys['51']=='') $hotkeys['51']='tag_3';
if ($hotkeys['52']=='') $hotkeys['52']='tag_4';
if ($hotkeys['53']=='') $hotkeys['53']='tag_5';
if ($hotkeys['54']=='') $hotkeys['54']='tag_6';
if ($hotkeys['55']=='') $hotkeys['55']='tag_7';
if ($hotkeys['56']=='') $hotkeys['56']='tag_8';
if ($hotkeys['57']=='') $hotkeys['57']='tag_9';
if ($hotkeys['46']=='') $hotkeys['46']='delete';//del
if ($hotkeys['115']=='') $hotkeys['115']='not_delete';//del
if ($hotkeys['108']=='') $hotkeys['108']='favorite';//r
if ($hotkeys['110']=='') $hotkeys['110']='not_favorite';//r
if ($hotkeys['116']=='') $hotkeys['116']='full_text';//f
if ($hotkeys['9']=='') $hotkeys['9']='mark_message';//tab
// if ($hotkeys['81']=='') $hotkeys['81']='mass_positive_tone';//Q
// if ($hotkeys['69']=='') $hotkeys['69']='mass_negative_tone';//E
// if ($hotkeys['87']=='') $hotkeys['87']='mass_neutral_tone';//W
// if ($hotkeys['33']=='') $hotkeys['33']='mass_tag1';//!
// if ($hotkeys['64']=='') $hotkeys['64']='mass_tag2';//@
// if ($hotkeys['35']=='') $hotkeys['35']='mass_tag3';//#
// if ($hotkeys['36']=='') $hotkeys['36']='mass_tag4';//$
// if ($hotkeys['37']=='') $hotkeys['37']='mass_tag5';//%
// if ($hotkeys['94']=='') $hotkeys['94']='mass_tag6';//^
// if ($hotkeys['38']=='') $hotkeys['38']='mass_tag7';//&
// if ($hotkeys['42']=='') $hotkeys['42']='mass_tag8';//*
// if ($hotkeys['40']=='') $hotkeys['40']='mass_tag9';//(
// if ($hotkeys['68']=='') $hotkeys['68']='mass_delete';//D
// if ($hotkeys['67']=='') $hotkeys['67']='mass_not_delete';//C
// if ($hotkeys['83']=='') $hotkeys['83']='mass_favorite';//S
// if ($hotkeys['88']=='') $hotkeys['88']='mass_not_favorite';//X

echo json_encode($hotkeys);

?>