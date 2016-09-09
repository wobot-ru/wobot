<?
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/daemon/fsearch3/ch.php');
require_once('auth.php');
require_once('/var/www/new/com/config.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

//echo $_COOKIE['user_id'];
auth();
if (!$loged) die();
//$_POST['order_id']=713;
$access_token='5f04c371670c397e2c5f9115da38e3a2cd3c69e69c66e96af357b4dde670544ea32143a0302413688d82a';
$fb_at='CAAJ68ASJQwQBAHL2ESIoqojdp4edYotgZCP0L5mYQe38rrVZBZBS1hxZAb1HFwe22xJ2dkQowB5IxAptYMEebqeSaa3ZAHaIwpwyaagHRXqIo2SXyIJPU1DQeVoZCZClRRn5yZBRr7fSeyuOV3CIBuZCxTKrvOA401OxGtqokNUjOnySZCAGdFr55KtjbfjdvGHmIZD';
//die();
$memcache_obj = new Memcache;

$memcache_obj->connect('localhost', 11211);
$out1=intval($memcache_obj->get('groups_search_'.$user['user_id']));
if ($out1>5)
{
	$out['status']='fail';
	$errors[]=3;
	$out['errors']=$errors;
	echo json_encode($out);
	die();
}
else
{
	$out1++;
	$memcache_obj->set('groups_search_'.$user['user_id'], $out1,0,30);
}

function search_group($keyword)
{
	global $access_token;
	global $fb_at;
	//echo $keyword;
	$nkeyword=$keyword;
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$keyword);
	$keyword=preg_replace('/\~+([\s]+)?\([а-яА-Яa-zA-Z\-\ \,\.\|]+\)/isu','',$keyword);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	//echo $keyword;
	//die();
	$word=new Lingua_Stem_Ru();
	$mkeyword=explode('  ',$keyword);
	foreach ($mkeyword as $item)
	{
		$mkw[$word->stem_word($item)]=$item;
	}
	//echo check_post('(((((((((((:Группа людей, которые отгрызают наушник, если он сломался:)))))))))))','сломались&наушники|поломались&наушники|повредились&наушники|сломались&уши|сломался&джек|сломался&штекер|наушники&перестали|ухо&перестало');
	//print_r($mkw);
	//die();
	$k=0;
	foreach ($mkw as $item)
	{
		if (mb_strlen($item,'UTF-8')<3) continue;
		$i=0;
		do
		{
			if ($k>9) break;
			//echo 'https://api.vkontakte.ru/method/groups.search?q='.urlencode($item).'&offset='.($i*100).'&access_token='.$access_token.'&count=100';
			$cont=parseUrl('https://api.vkontakte.ru/method/groups.search?q='.urlencode($item).'&offset='.($i*100).'&access_token='.$access_token.'&count=100');
			usleep(100000);
			$mas=json_decode($cont,true);
			foreach ($mas['response'] as $key => $it)
			{
				if ($key==0) continue;
				//echo $it['name'].' '.$nkeyword."<br>";
				if (check_post($it['name'],$nkeyword)==1)
				{
					if ($k>9) continue;
					$outmas[$k]['link']='http://vk.com/'.$it['screen_name'];
					$outmas[$k]['name']=$it['name'];
					$k++;
				}
			}
			$i++;
		}
		while ((($i*100)<$mas['response'][0]) && ($i<1));
	}
	foreach ($mkw as $item)
	{
		if (mb_strlen($item,'UTF-8')<3) continue;
		if ($k>20) break;
		//echo 'https://api.vkontakte.ru/method/groups.search?q='.urlencode($item).'&offset='.($i*100).'&access_token='.$access_token.'&count=100';
		$cont=parseUrl('https://graph.facebook.com/search?q='.urlencode($item).'&type=group&limit=10&access_token='.$fb_at);
		usleep(100000);
		$mas=json_decode($cont,true);
		//$outmas['groups']='https://graph.facebook.com/search?q='.urlencode($item).'&type=group&limit=10&access_token='.$fb_at;
		foreach ($mas['data'] as $key => $it)
		{
			//$outmas['test'].=$it['name']." ";
			if (check_post($it['name'],$nkeyword)==1)
			{
				if ($k>20) continue;
				$outmas[$k]['link']='http://facebook.com/'.$it['id'];
				$outmas[$k]['name']=$it['name'];
				$k++;
			}
		}
	}
	//print_r($outmas);
	return $outmas;
}

if ($_POST['mkw']!='')
{
	$mcount_open=explode('(', $_POST['mkw']);
	$mcount_close=explode(')', $_POST['mkw']);
	if (count($mcount_close)!=count($mcount_open))
	{
		$errors[]=1;
		//$outmas['status']=7;
		//echo json_encode($outmas);
		//die();
	}
	$qwry=$_POST['mkw'];
}
elseif (($_POST['mnw']!='') || ($_POST['mw']!=''))
{
	if ($_POST['mnw']!='')
	{
		$mnw=explode(',',$_POST['mnw']);
		foreach ($mnw as $nw)
		{
			if (trim($nw)=='') continue;
			if (preg_match('/\"/is',trim($nw)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($nw))) && (trim($nw)!=''))
				{
					//if (trim($_POST['mw'])!='')	$or=' && ';
					$strnw.=$or.trim($nw);
					$or=' && ';
				}
				else
				{
					if (!in_array(22, $errors))	$errors[]=22;
					//$out['status']=22;
					//echo json_encode($out);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($nw))) && (trim($nw)!=''))
				{
					//if (trim($_POST['mw'])!='')	$or=' && ';
					$strnw.=$or.trim($nw);
					$or=' && ';
				}
				else
				{
					if (!in_array(22, $errors)) $errors[]=22;
					//$out['status']=22;
					//echo json_encode($out);	
					//die();
				}
			}
		}
	}
	if ($_POST['mw']!='')
	{
		$mw=explode(',',$_POST['mw']);
		foreach ($mw as $w)
		{
			if (trim($w)=='') continue;
			if (preg_match('/\"/is',trim($w)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($w))) && (trim($w)!=''))
				{
					$strw.=$and.trim($w);
					$and=' | ';
				}
				else
				{
					//echo $w;
					if (!in_array(21, $errors)) $errors[]=21;
					//$out['status']=21;
					//echo json_encode($out);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($w))) && (trim($w)!=''))
				{
					$strw.=$and.trim($w);
					$and=' | ';
				}
				else
				{
					//echo $w;
					if (!in_array(21, $errors)) $errors[]=21;
					//$out['status']=21;
					//echo json_encode($out);	
					//die();
				}
			}
		}
	}
	if ($_POST['mew']!='')
	{
		$mew=explode(',',$_POST['mew']);
		foreach ($mew as $ew)
		{
			if (trim($ew)=='') continue;
			if (preg_match('/\"/is',trim($ew)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					if (!in_array(23, $errors)) $errors[]=23;
					//$out['status']=23;
					//echo json_encode($out);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					if (!in_array(23, $errors)) $errors[]=23;
					//$out['status']=23;
					//echo json_encode($out);	
					//die();
				}
			}
		}
	}
	$qwry=(($strw!='')?'('.$strw.')':'').$strnw.$strew;
}

if (count($errors)==0)
{
	//$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.$_POST['order_id']);
	//$order=$db->fetch($qorder);
	//$outmas['groups']=search_group($order['order_keyword']);
	$outmas['groups']=search_group($qwry);
	echo json_encode($outmas);
}
else
{
	$out['status']='fail';
	$out['errors']=$errors;
	echo json_encode($out);
}

?>