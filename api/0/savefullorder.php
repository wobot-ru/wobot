<?
/*
структура data
[
    {
        "order_name": "q"
    },
    {
		"order_start": 11.11.2012,
		"order_end": 11.12.2012
    },
    {
        "mkw": [],
        "mw": [],
        "mew": [
            "ergre",
            "erger",
            "erger"
        ]
    },
    {
        "res_type": "only",
        "data": [
            "efge",
            "eget",
            "etghet"
        ]
    },
    {
        "author_type": "except",
        "data": [
            "http://twitter.com/wobot.ru",
            "http://martin.livejournal.com"
        ]
    },
    {
        "random_age": {
            "from_age": "12",
            "to_age": "23"
        }
    },
    {
        "gender": 2
    },
    {
    	"loc_type": "only",
        "location": [
            "Нижний Новгород",
            "Казань",
            "Киев",
            "Новосибирск",
            "Одесса",
            "Севастопль",
            "Краснодар"
        ]
    }
]
*/
//print_r($_POST['data']);

// $_POST['order_start']=date('d.m.Y',time()-86400*14);
// $_POST['order_end']=date('d.m.Y',time());
$_POST['data']=json_decode($_POST['data'],true);
foreach($_POST['data'] as $data)
{
	if (isset($data['order_date']))
	{
		$_POST['order_start']=strtotime($data['order_date']['order_start']);
		$_POST['order_end']=strtotime($data['order_date']['order_end']);
	}
	if (isset($data['order_name']))
	{
		//echo 'fuck';
		$_POST['order_name']=$data['order_name'];
	}
	elseif (isset($data['mnw'])||isset($data['mw'])||isset($data['mew']))
	{
		$_POST['mnw']=implode(",", $data['mnw']);
		$_POST['mw']=implode(",", $data['mw']);
		$_POST['mew']=implode(",", $data['mew']);
	}
	elseif (isset($data['mkw']))
	{
		//$_POST['mkw']=$data['mkw'][0];//implode(",", $data['mkw']);
		$_POST['mkw']=implode(",", $data['mkw']);
	}
	elseif (isset($data['res_type']))
	{
		$_POST['res_type']=$data['res_type'];
		$_POST['res']=implode(",", $data['data']);
		if ($data['data']=='all')
		{
			$_POST['res_type']='all';
			$_POST['res']=null;
		}
	}
	elseif (isset($data['author_type']))
	{
		$_POST['author_type']=$data['author_type'];
		$_POST['authors']=implode(",", $data['data']);
		if ($data['data']=='all')
		{
			$_POST['author_type']='all';
			$_POST['authors']=null;
		}
	}
	elseif (isset($data['random_age']))
	{
		$_POST['random_age']=$data['random_age'];
		if ($data['random_age']!=0)
		{
			$_POST['from_age']=$data['random_age']['from_age'];
			$_POST['to_age']=$data['random_age']['to_age'];
		}
	}
	elseif (isset($data['gender']))
	{
		$_POST['gender']=$data['gender'];
	}
	elseif (isset($data['remove_spam']))
	{
		$_POST['remove_spam']=$data['remove_spam'];
	}
	elseif (isset($data['location']))
	{
		if (isset($data['loc_type'])) $_POST['loc_type']=$data['loc_type'];
		else $_POST['loc_type']='only';
		$_POST['loc']=implode(",", $data['location']);
	}
	elseif (isset($data['auto_nastr']))
	{
		$_POST['auto_nastr']=$data['auto_nastr'];
	}
}
// print_r($_POST);
//echo json_encode($_POST);
// die();
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/daemon/com/users.php');
// require_once('/var/www/daemon/fsearch3/ch.php');
require_once('/var/www/com/checker.php');

error_reporting(0);
$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('addorder',$_POST);

function parseUrlmail($url,$to,$subj,$body,$from)
{
/*$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='to='.$to.'&subject='.$subj.'&body='.$body.'&from='.$from;
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $content;*/

$message=str_replace('\\\'','\'',$body);
$headers = "From: noreply@wobot.ru\r\nReply-To: noreply@wobot.ru\r\n";
$headers .= "Content-Type: text/html; charset=utf-8";
$theme = 'Заявка с сайта '.date("d.m.Y");

$mailst =mail($to,$subj, stripslashes($message),$headers);
}

date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();
auth();

if (!$loged)
{
	die();
}

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

//print_r($user);
if (mb_strlen($_POST['order_name'],'UTF-8')<3)
{
	//$mas['status']=0;
	//echo $_POST['order_name'];
	$errors[]=0;
	//echo 'fuck';
	//$mas['status']=55;
	//echo json_encode($mas);
	//die();
}
//elseif (strtotime($_POST['order_start'])>=strtotime($_POST['order_end']))
//{
//	$errors[]=1;
	//$mas['status']=1;
	//echo json_encode($mas);	
	//die();
//}
//$lock=$db->query('LOCK TABLES blog_orders WRITE');
$us=$db->query('SELECT * FROM user_tariff as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE a.user_id='.$user['user_id']);
//echo 'SELECT * FROM user_tariff as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE a.user_id='.$user_id;
$inf_us=$db->fetch($us);
/*if (((time()-$inf_us['ut_date'])/86400)>30)
{
	$mas['status']=5;
	echo json_encode($mas);	
	die();	
}*/
$count=$inf_us['tariff_quot'];
$count_result_all=$inf_us['tariff_posts'];
if ($inf_us['user_tariff']==3)
{
	$inf_count_order=$db->query('SELECT order_id,order_settings FROM blog_orders WHERE ut_id='.$user['ut_id']);
}
else
{
	$inf_count_order=$db->query('SELECT order_id,order_settings FROM blog_orders WHERE user_id='.$user['user_id']);
}
$count_orders=$db->num_rows($inf_count_order);
$count_ord=$count_orders;
while ($inf_order=$db->fetch($inf_count_order))
{
	if ($inf_order['order_id']==$_POST['order_id'])
	{
		$settings=json_decode($inf_order['order_settings'],true);
	}
}
if (intval($_POST['order_id'])==0)
{
	if ($count<$count_ord)
	{
		$errors[]=4;
	}
}

if ($_POST['mkw']=='')
{
	if (($_POST['mw']=='')&&($_POST['mnw']==''))
	{
		$errors[]=11;
	}
	if ($_POST['mnw']!='')
	{
		$mnw=explode(',',$_POST['mnw']);
		foreach ($mnw as $nw)
		{
			if (preg_match('/\"/is',trim($nw)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($nw))) && (trim($nw)!=''))
				{
					if ($_POST['mw']!='') $or=' && ';
					$strnw.=$or.trim($nw);
					$or=' && ';
				}
				else
				{
					$errors[]=22;
					//$mas['status']=22;
					//echo json_encode($mas);	
					//die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+$/isu',trim($nw))) && (trim($nw)!=''))
				{
					if ($_POST['mw']!='') $or=' && ';
					$strnw.=$or.trim($nw);
					$or=' && ';
				}
				else
				{
					$errors[]=22;
					//$mas['status']=22;
					//echo json_encode($mas);	
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
					$errors[]=21;
					//$mas['status']=21;
					//echo json_encode($mas);	
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
					$errors[]=21;
					//$mas['status']=21;
					//echo json_encode($mas);	
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
			if (preg_match('/\"/is',trim($ew)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.]+\"$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					$errors[]=23;
					//$mas['status']=23;
					//echo json_encode($mas);	
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
					$errors[]=23;
					//$mas['status']=23;
					//echo json_encode($mas);	
					//die();
				}
			}
		}
	}
	// $qwry=(($strw!='')?'('.$strw.')':'').$strnw.$strew;
	$qwry=(($strw!='')?'('.$strw.')':'').(trim($_POST['mnw'])!=''?$strnw:'').$strew;
}
else
{
	$mcount_open=explode('(', $_POST['mkw']);
	$mcount_close=explode(')', $_POST['mkw']);
	if (count($mcount_close)!=count($mcount_open))
	{
		$errors[]=6;
		//$outmas['status']=6;
		//echo json_encode($outmas);
		//die();
	}
	if ((check_query($_POST['mkw'])==0)&&(!in_array(6, $errors)))
	{
		$errors[]=6;
	}
	$qwry=$_POST['mkw'];
}

if ($qwry=='') $count_res=0;
else
{
	// $xml = file_get_contents('http://blogs.yandex.ru/search.rss?text='.urlencode($qwry).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark');
	// //echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($qwry).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark';
	// $r1='/<yablogs\:count>(?<count>.*?)<\/yablogs\:count>/is';
	// preg_match_all($r1,$xml,$ot1);
	// $count_res=intval($ot1['count'][0]);
}
if ($qwry=='') $errors[]=10;
//echo $count_res;
if (($count_res>10000) && ($user['tariff_id']!=3))
{
	// $errors[]=3;
	//$mas['status']=3;
	//echo json_encode($mas);	
	//die();
}
//Похуй на интервал
if (($_POST['order_start']=='')&&($_POST['order_end']==''))
{
	$delay[5]=1;
	$delay[6]=2;
	$delay[7]=3;
	$_POST['order_start']=mktime(0,0,0,date("n",time())-$inf_us['tariff_retro'],date("j",time()),date("Y",time()));//strtotime($_POST['order_start'])
	$_POST['order_end']=$inf_us['ut_date'];
	if ($inf_us['tariff_type']==2) $_POST['order_end']=0;
	if ($inf_us['tariff_id']==4)
	{
		$errors[]=9;
		//$mas['status']=5;
		//echo json_encode($mas);
		//die();
	}

	//Демо-тариф две недели назад, две недели вперед
	if ($inf_us['tariff_id']==3)
	{
		$_POST['order_start']=mktime(0,0,0,date("n",time()),date("j",time())-14,date("Y",time()));//strtotime($_POST['order_start'])
		$_POST['order_end']=mktime(0,0,0,date("n",time()),date("j",time())+14,date("Y",time()));
	}
}
else
{
	if (($_POST['order_start']<mktime(0,0,0,date("n",time())-$inf_us['tariff_retro'],date("j",time()),date("Y",time())))||($_POST['order_end']>mktime(0,0,0,date("n",time())+1,date("j",time()),date("Y",time())))) $errors[]=12; 
	if (($_POST['order_start']>$_POST['order_end'])&&(!in_array(12, $errors))) $errors[]=12;
}
//Для демо-тарифа тема добавляется как заблокированная
if ($inf_us['tariff_id']==3)
{
	$usr_mb=0;
}
else
{
	$usr_mb=$user['user_id'];
}

$mloc=explode(',', $_POST['loc']);
$mres=explode(',', $_POST['res']);
unset($settings['loc']);
unset($settings['res']);
unset($settings['author']);

foreach ($mloc as $item)
{
	if (trim($item)=='') continue;
	if (in_array('loc_'.html_entity_decode($item, ENT_COMPAT, 'UTF-8'), $settings['loc'])) continue;
	$settings['loc'][]='loc_'.html_entity_decode($item, ENT_COMPAT, 'UTF-8');
}

$settings['loc_type']=$_POST['loc_type'];

foreach ($mres as $item)
{
	if (trim($item)=='') continue;
	if (in_array($item, $settings['res'])) continue;
	$settings['res'][]=$item;
}

$mauthors=explode(',', $_POST['authors']);
foreach ($mauthors as $item)
{
	if (trim($item)=='') continue;
	$user1=new users();
	$blog_id=$user1->get_nick($item);
	if ($blog_id==0)
	{
		//Сделать добавление нового пользователя в случае валидной ссылки
		//В случае инвалидной выдавать ошибку
		if (!in_array(7,$errors)) $errors[]=7;
		//$out['status']=8;
		//echo json_encode($out);
		//die();
	}
	if (in_array($item, $settings['author'])) continue;
	$settings['author'][]=$item;
	if (in_array($item, $settings['author_id'])) continue;
	$settings['author_id'][]=$blog_id;
}

if (isset($_POST['author_type']))
{
	$settings['author_type']=$_POST['author_type'];
}
if (isset($_POST['res_type']))
{
	$settings['res_type']=$_POST['res_type'];
}
if (isset($_POST['gender']))
{
	$settings['gender']=intval($_POST['gender']);
}

if (isset($_POST['remove_spam']))
{
	$settings['remove_spam']=intval($_POST['remove_spam']);
}
else
{
	if (!isset($settings['remove_spam'])) $settings['remove_spam']=0;
}
if (intval($_POST['random_age'])==0)
{
	$settings['from_age']=0;
	$settings['to_age']=0;
	$settings['random_age']=intval($_POST['random_age']);
}
elseif (isset($_POST['from_age']) || isset($_POST['to_age']))
{
	$from_age=(isset($_POST['from_age'])?$_POST['from_age']:intval($settings['from_age']));
	$to_age=(isset($_POST['to_age'])?$_POST['to_age']:intval($settings['to_age']));
	if ($from_age>$to_age)
	{
		$errors[]=5;
		//$out['status']=5;
		//echo json_encode($out);	
		//die();		
	}
	$settings['from_age']=$_POST['from_age'];
	$settings['to_age']=$_POST['to_age'];
	$settings['random_age']=1;
}
if (isset($_POST['auto_nastr']))
{
	$settings['auto_nastr']=$_POST['auto_nastr'];
}

if (count($errors)==0)
{
	if (intval($_POST['order_id'])==0)
	{
		//echo 'INSERT INTO blog_orders (order_date,order_name,order_keyword,user_id,order_start,order_end,third_sources,ut_id,ful_com,order_engage,order_lang'.(count($settings)!=0?',order_settings':'').(isset($_POST['auto_nastr'])?',order_nastr':'').') VALUES ('.time().',\''.addslashes($_POST['order_name']).'\',\''.addslashes($qwry).'\','.$usr_mb.','.addslashes($_POST['order_start']).','.addslashes($_POST['order_end']).',1,'.$inf_us['ut_id'].',1,1,2'.(count($settings)!=0?',\''.addslashes(json_encode($settings)).'\'':'').(isset($_POST['auto_nastr'])?',1':'').')';
		$qw='INSERT INTO blog_orders (order_date,order_name,order_keyword,user_id,order_start,order_end,third_sources,ut_id,ful_com,order_engage,order_lang'.(count($settings)!=0?',order_settings':'').(isset($_POST['auto_nastr'])?',order_nastr':'').') VALUES ('.time().',\''.addslashes($_POST['order_name']).'\',\''.addslashes($qwry).'\','.$usr_mb.','.addslashes($_POST['order_start']).','.addslashes($_POST['order_end']).',1,'.$inf_us['ut_id'].',1,1,2'.(count($settings)!=0?',\''.addslashes(json_encode($order_settings)).'\'':'').(isset($_POST['auto_nastr'])?',1':'').')';
		$db->query($qw);
		$mas['order_id']=$db->insert_id();
		$settings['widgets'][0]['name']='Количество сообщений';
		$settings['widgets'][0]['type']='linear';
		$settings['widgets'][0]['data']['themes'][]=$db->insert_id();
		$settings['widgets'][0]['data']['sub_themes']='';
		$settings['widgets'][0]['data']['y']='posts_count';
		$settings['widgets'][0]['data']['step']='day';
		$settings['widgets'][1]['name']='Авторы';
		$settings['widgets'][1]['type']='linear';
		$settings['widgets'][1]['data']['themes'][]=$db->insert_id();
		$settings['widgets'][1]['data']['sub_themes']='';
		$settings['widgets'][1]['data']['y']='author_count';
		$settings['widgets'][1]['data']['step']='day';
		$settings['widgets'][2]['name']='Охват';
		$settings['widgets'][2]['type']='linear';
		$settings['widgets'][2]['data']['themes'][]=$db->insert_id();
		$settings['widgets'][2]['data']['sub_themes']='';
		$settings['widgets'][2]['data']['y']='value';
		$settings['widgets'][2]['data']['step']='day';
		$settings['widgets'][3]['name']='Вовлеченность в разрезе по площадкам';
		$settings['widgets'][3]['type']='stack';
		$settings['widgets'][3]['data']['themes'][]=$db->insert_id();
		$settings['widgets'][3]['data']['sub_themes']='';
		$settings['widgets'][3]['data']['x']='time';
		$settings['widgets'][3]['data']['y']='engage';
		$settings['widgets'][3]['data']['split']='post_host';
		//print_r($settings);
		$qw2='UPDATE blog_orders SET order_settings=\''.addslashes(json_encode($settings)).'\' WHERE order_id='.$db->insert_id();
		//echo $qw2;
		$db->query($qw2);
		//die();
		$headers  = "From: noreply@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply@wobot.ru\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		
		mail('r@wobot.co','Команда Wobot'.(($inf_us['tariff_id']==3)?' // Тема демо-тарифа':''),"Пользователь ".$user['user_email']." добавил тему для исследования. <br>Название темы: ".$_POST['order_name']."<br>Ключевые слова: ".$qwry."<br>Период мониторинга: ".date('d.m.y',$_POST['order_start'])."-".date('d.m.y',$_POST['order_end'])." ",$headers);
		$mas['status']='ok';
		echo json_encode($mas);
		if ($inf_us['tariff_id']!=3)
		{
			parseUrl('http://188.120.239.225/tools/charge.php?order_id='.$db->insert_id());
		}
		die();
	}
	else
	{
		$qisset=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
		if ($db->num_rows($qisset)!=0)
		{
			$db->query('UPDATE blog_orders SET '.(count($settings)!=0?'order_settings=\''.addslashes(json_encode($settings)).'\'':'').(isset($_POST['auto_nastr'])?',order_nastr='.intval($_POST['auto_nastr']):'').($qwry!=''?',order_keyword=\''.addslashes($qwry).'\'':'').(isset($_POST['order_name'])?',order_name=\''.addslashes($_POST['order_name']).'\'':'').($inf_us['tariff_id']==3?',user_id=0':',user_id='.$user['user_id']).' WHERE order_id='.intval($_POST['order_id']));
			$mas['status']='ok';
			echo json_encode($mas);
			die();
		}
		else
		{
			$errors[]=8;
			$outmas['status']='fail';
			$outmas['errors']=$errors;
			echo json_encode($outmas);
			die();
		}
	}
}
else
{
	$outmas['status']='fail';
	$outmas['errors']=$errors;
	echo json_encode($outmas);
	die();
}

?>
