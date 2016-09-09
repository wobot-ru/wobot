<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();
auth();
if (!$loged)
{
	die();
}
//print_r($user);
if (mb_strlen($_POST['order_name'],'UTF-8')<4)
{
	$mas['status']=0;
	//$mas['status']=55;
	echo json_encode($mas);
	die();
}
elseif (strtotime($_POST['order_start'])>strtotime($_POST['order_end']))
{
	$mas['status']=1;
	echo json_encode($mas);	
	die();
}
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
$inf_count_order=$db->query('SELECT count(*) as cnt FROM blog_orders WHERE user_id='.$user['user_id']);
$count_orders=$db->fetch($inf_count_order);
$count_ord=$count_orders['cnt'];
if ($count>$count_ord)
{
	if ($_POST['mnw']!='')
	{
		$mnw=explode(',',$_POST['mnw']);
		foreach ($mnw as $nw)
		{
			if (preg_match('/\"/is',trim($nw)))
			{
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ\ \'\-\.]+\"$/isu',trim($nw))) && (trim($nw)!=''))
				{
					$or=' && ';
					$strnw.=$or.trim($nw);
				}
				else
				{
					$mas['status']=22;
					echo json_encode($mas);	
					die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ\ \'\-\.]+$/isu',trim($nw))) && (trim($nw)!=''))
				{
					$or=' && ';
					$strnw.=$or.trim($nw);
				}
				else
				{
					$mas['status']=22;
					echo json_encode($mas);	
					die();
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
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ\ \'\-\.]+\"$/isu',trim($w))) && (trim($w)!=''))
				{
					$strw.=$and.trim($w);
					$and=' | ';
				}
				else
				{
					//echo $w;
					$mas['status']=21;
					echo json_encode($mas);	
					die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ\ \'\-\.]+$/isu',trim($w))) && (trim($w)!=''))
				{
					$strw.=$and.trim($w);
					$and=' | ';
				}
				else
				{
					//echo $w;
					$mas['status']=21;
					echo json_encode($mas);	
					die();
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
				if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ\ \'\-\.]+\"$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					$mas['status']=23;
					echo json_encode($mas);	
					die();
				}
			}
			else
			{
				if ((preg_match('/^[а-яА-Яa-zA-ZёЁ\ \'\-\.]+$/isu',trim($ew))) && (trim($ew)!=''))
				{
					$ex=' ~~ ';
					$strew.=$ex.trim($ew);
				}
				else
				{
					$mas['status']=23;
					echo json_encode($mas);	
					die();
				}
			}
		}
	}
	$qwry=(($strw!='')?'('.$strw.')':'').$strnw.$strew;
	if ($qwry!='')
	{
		$xml = file_get_contents('http://blogs.yandex.ru/search.rss?text='.urlencode($qwry).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark');
		//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($qwry).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark';
		$r1='/<yablogs\:count>(?<count>.*?)<\/yablogs\:count>/is';
		preg_match_all($r1,$xml,$ot1);
		$count_res=intval($ot1['count'][0]);
		//echo $count_res;
		if ($count_res>10000)
		{
			$mas['status']=3;
			echo json_encode($mas);	
			die();
		}
		//Похуй на интервал
		$_POST['order_start']=mktime(0,0,0,date("n",time())-1,date("j",time()),date("Y",time()));//strtotime($_POST['order_start'])
		$_POST['order_end']=mktime(0,0,0,date("n",time())+1,date("j",time()),date("Y",time()));
		if ($inf_us['tariff_id']==4)
		{
			$mas['status']=5;
			echo json_encode($mas);
			die();
		}
		$qw='INSERT INTO blog_orders (order_name,order_keyword,user_id,order_start,order_end,third_sources,ut_id,ful_com,order_engage) VALUES (\''.addslashes($_POST['order_name']).'\',\''.addslashes($qwry).'\','.$user['user_id'].','.addslashes($_POST['order_start']).','.addslashes($_POST['order_end']).',1,'.$inf_us['ut_id'].',1,1)';
		//echo $qw;
		$db->query($qw);
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
}
else
{
	$mas['status']=4;
	echo json_encode($mas);
	die();
}

?>