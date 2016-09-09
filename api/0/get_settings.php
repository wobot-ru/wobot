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
// $user['user_id']=4200;

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('get_settings',$_POST);

//if (!$loged) die();

//$inf=$db->query('SELECT * FROM users WHERE user_id='.intval($user['user_id']));
//$us=$db->fetch($inf);
$qkeys=$db->query('SELECT * FROM tp_keys WHERE user_id='.$user['user_id']);
while ($key=$db->fetch($qkeys))
{
	if ($key['type']=='tw')
	{
		$ktw=json_decode($key['key'],true);
		$outmas['tokens'][$key['type']][]=$ktw[0];	
	}
	else
	{
		$outmas['tokens'][$key['type']][]=$key['key'];
	}
}
$us=$user;
$rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($us['user_id']));
$tar=$db->fetch($rs);
$outmas['user_mid_priv']=$tar['user_mid_priv'];
$outmas['fio']=$us['user_name'];
$outmas['user_money']=intval($us['user_money']);
$outmas['tariff_posts']=intval($tar['tariff_posts']);
$outmas['tariff_retro']=intval($tar['tariff_retro']);
$outmas['user_company']=$us['user_company'];
$outmas['contact_name']=$us['user_contact'];
$outmas['freq_mail']=intval($us['user_freq']);
$outmas['user_position']=$us['user_position'];
$outmas['tarif_id']=$us['tariff_id'];
$outmas['tarif_exp']=(intval(($us['ut_date']-mktime(0,0,0,date('n'),date('j'),date('Y')))/86400)<0?0:intval(($us['ut_date']-mktime(0,0,0,date('n'),date('j'),date('Y')))/86400));
$outmas['user_tarif']=$tar['tariff_id'];
$outmas['user_mails']=($us['user_mails']=='')?$us['user_email']:$us['user_mails'];
if (($us['tarif_id']==10) || ($us['tarif_id']==11))
{
	$outmas['tariff_type']='messages';
}
else
{
	$outmas['tariff_type']='range';	
}
$outmas['mainNotice']=0;
$outmas['newresNotice']=0;
$outmas['compareNotice']=0;
$outmas['themeNotice']=0;
$outmas['messagesNotice']=0;
$outmas['comparepageNotice']=0;
$settings=json_decode($us['user_settings']);
foreach ($settings as $key => $item)
{
	$outmas[$key]=$item;
}
// print_r($user);
$settings_real=json_decode($user['user_settings'],true);
if (($tar['tariff_id']==12) || ($tar['tariff_id']==13) || ($tar['tariff_id']==14)) $outmas['user_access']=1;
else $outmas['user_access']=0;
if (!isset($settings_real['user_reaction']))
{
	if (($tar['tariff_id']==12) || ($tar['tariff_id']==13)) $outmas['user_reaction']=1;
	else $outmas['user_reaction']=0;
}
else $outmas['user_reaction']=1;//intval($settings_real['user_reaction']);
//$outmas['user_phone']=$us['']
if (intval($outmas['perpage'])==0)
{
	$outmas['perpage']=10;
}
echo json_encode($outmas);
?>