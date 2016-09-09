<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

$access_token='2af49b2681d87de5f2bae8949d4979d3957c281fb44098ae7ee2c085d815c30925d851be02b0270d4e2a7';
$fb_at='AAACQNvcNtEgBABIlzfBUuhmNQHFZArVxW90QJa5vMfe2VmtkWZCPNqEojO9NL70adSpZA7rtGoHa6S6N7RCLqWchPqTmuIXEFGHPVWVNQZDZD';

//$_POST=$_GET;

function get_id_group($link)
{
	global $access_token,$fb_at;
	$hn=parse_url($link);
    $hn=$hn['host'];
    $ahn=explode('.',$hn);
    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];
	if (($hn=='vk.com') || ($hn=='vkontakte.ru'))
	{
		$out['id'][0]='';
		if (preg_match('/vk\.com\/club\/?/isu',$link))
		{
			//echo 1;
			$regex='/\/club(?<id>\d+)\/?$/isu';
			preg_match_all($regex,trim($link),$out);
			//echo $out['id'][0].' ';
		}
		elseif (preg_match('/vk\.com\/public\/?/isu',$link))
		{
			//echo 2;
			$regex='/\/public(?<id>\d+)\/?$/isu';
			preg_match_all($regex,trim($link),$out);
			//echo $out['id'][0].' ';
		}
		if ($out['id'][0]!='')
		{
			$outmas['id']=$out['id'][0];
			$outmas['type']='vk';
			return $outmas;
		}
		else
		{
			$out['id'][0]='';
			if (preg_match('/vk\.com\/id\/?/isu',$link))
			{
				//echo 1;
				$regex='/\/id(?<id>\d+)\/?$/isu';
				preg_match_all($regex,trim($link),$out);
				//echo $out['id'][0].' ';
			}
			if ($out['id'][0]!='')
			{
				$outmas['id']=$out['id'][0];
				$outmas['type']='vk_acc';				
				return $outmas;
			}
			else
			{
				$regex='/vk\.com\/(?<id>[\da-zA-Zа-яА-ЯёЁ\.\_]+)\/?$/isu';
				preg_match_all($regex,trim($link),$out);
				$cont=parseUrl('https://api.vkontakte.ru/method/groups.getById?gid='.$out['id'][0].'&access_token='.$access_token);
				$mas=json_decode($cont,true);
				if (count($mas['error'])==0)
				{
					$outmas['id']=$out['id'][0];
					$outmas['name']=$mas['response'][0]['name'];
					$outmas['type']='vk';
					return $outmas;
				}
				else
				{
					$cont=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.$out['id'][0].'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
					$mas=json_decode($cont,true);
					if (count($mas['error'])==0)
					{
						$outmas['id']=$out['id'][0];
						$outmas['name']=$mas['response'][0]['first_name'].' '.$mas['response'][0]['last_name'];
						$outmas['type']='vk_acc';
						return $outmas;
					}
				}
			}
		}
	}
	elseif ($hn=='facebook.com')
	{
		$out['id'][0]='';
		if (preg_match('/\/pages\/?/isu',$link))
		{
			//echo 1;
			$regex='/\/(?<id>\d+)\/?$/isu';
			preg_match_all($regex,trim($link),$out);
			//echo $out['id'][0].' ';
		}
		elseif (preg_match('/\/groups\/?/isu',$link))
		{
			//echo 2;
			$regex='/\/(?<id>\d+)\/?$/isu';
			preg_match_all($regex,trim($link),$out);
			//echo $out['id'][0].' ';
		}
		else
		{
			//echo 3;
			$regex='/\/(?<id>[a-zA-Zа-яА-Я0-9\-ёЁ\.]+)$\/?$/isu';
			preg_match_all($regex,trim($link),$out);
			//echo $out['id'][0].' ';
		}
		if ($out['id'][0]!='')
		{
			$cont=parseUrl('https://graph.facebook.com/'.$out['id'][0].'?access_token='.$fb_at);
			$mas=json_decode($cont,true);
			if ($mas['error']['code']=='')
			{
				$outmas['id']=$out['id'][0];
				$outmas['name']=$mas['name'];
				$outmas['type']='fb';
				return $outmas;
			}
			else
			{
				return 0;
			}
		}
	}
	elseif ($hn=='banki.ru')
	{
		$regex='/\?PAGE_NAME=[a-z]+\&FID=(?<fid>\d+)\&TID=(?<tid>\d+)/isu';
		preg_match_all($regex, trim($link), $out);
		if (($out['tid'][0]!='') && ($out['fid'][0]!=''))
		{
			$outmas['id']=$out['tid'][0].'_'.$out['fid'][0];
			$outmas['type']='banki_forum';
			$cont=parseUrl($link);
			$cont=iconv('windows-1251', 'utf-8', $cont);
			$regex='/<span class="b\-breadCrumbs__header">(?<name>.*?)<\/span>/isu';
			preg_match_all($regex, $cont, $out);
			$outmas['name']=$out['name'][0];
			return $outmas;
		}
		elseif (preg_match('/\/friends\/group\/[^\/]*?\/forum\/\d+\//isu', trim($link)))
		{
			$regex='/\/friends\/group\/(?<fid>[^\/]*?)\/forum\/(?<tid>\d+)\//isu';
			preg_match_all($regex, trim($link), $out);
			$outmas['id']=$out['fid'][0].'/'.$out['tid'][0];
			$outmas['type']='banki_friends';
			$cont=parseUrl($link);
			$cont=iconv('windows-1251', 'utf-8', $cont);
			$regex='/<h1 class="b\-el\-article__title">(?<name>.*?)<\/h1>/isu';
			preg_match_all($regex, $cont, $out);
			$outmas['name']=$out['name'][0];
			return $outmas;
		}
		elseif (preg_match('/\/services\/questions\-answers\/\?id\=\d+/isu', trim($link)))
		{
			$regex='/\/services\/questions\-answers\/\?id\=(?<tid>\d+)/isu';
			preg_match_all($regex, trim($link), $out);
			$outmas['id']=$out['tid'][0];
			$outmas['type']='banki_question';
			$cont=parseUrl($link);
			$cont=iconv('windows-1251', 'utf-8', $cont);
			$regex='/<h1 class="b\-el\-h1">(?<name>.*?)<\/h1>/isu';
			preg_match_all($regex, $cont, $out);
			$outmas['name']=$out['name'][0];
			return $outmas;
		}
		else
		{
			return 0;
		}
	}
	return 0;
}

//echo $_COOKIE['user_id'];
auth();
if (!$loged) die();

//-------Права на проставления спама после шаринга------
$memcache = memcache_connect('localhost', 11211);
$priv=$memcache->get('blog_sharing');
$mpriv=json_decode($priv,true);
if ($priv=='')
{
	$qshare=$db->query('SELECT * FROM blog_sharing');
	while ($share=$db->fetch($qshare))
	{
		$mpriv[$share['order_id']][$share['user_id']]=$share['sharing_priv'];
	}
}
if ($mpriv[$_POST['order_id']][$user['user_id']]==1)
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('groups_edit',$_POST);

//print_r($_POST);
if (intval($_POST['order_id'])==0)
{
	$out['status']=1;
	echo json_encode($out);	
	die();
}

if ($_POST['id']=='')
{
	$out['status']=2;
	echo json_encode($out);	
	die();
}

if ($_POST['name_group']=='')
{
	$out['status']=3;
	echo json_encode($out);	
	die();
}

$qus=$db->query('SELECT user_id,ut_id FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
$us=$db->fetch($qus);
if ($us['user_id']!=$user['user_id'])
{
	$out['status']=4;
	echo json_encode($out);
	die();	
}

$mb_new_group=get_id_group(trim($_POST['name_group']));
if ($mb_new_group==0)
{
	$out['status']=5;
	echo json_encode($out);
	die();
}
else
{
	$qisset=$db->query('SELECT * FROM blog_tp WHERE gr_id=\''.$mb_new_group['id'].'\' LIMIT 1');
	if ($db->num_rows($qisset)!=0) 
	{
		$out['status']=6;
		echo json_encode($out);
		die();
	}
	$db->query('UPDATE blog_tp SET gr_id=\''.$mb_new_group['id'].'\',tp_type=\''.$mb_new_group['type'].'\',tp_name=\''.$mb_new_group['name'].'\' WHERE order_id='.intval($_POST['order_id']).' AND tp_id='.intval($_POST['id']));
	$out['status']='ok';
	echo json_encode($out);
	die();
}

?>