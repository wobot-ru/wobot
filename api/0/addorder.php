<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/com/checker.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/daemon/fsearch3/ch.php');

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
//print_r($user);
if (mb_strlen($_POST['order_name'],'UTF-8')<3)
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
	$inf_count_order=$db->query('SELECT count(*) as cnt FROM blog_orders WHERE ut_id='.$user['ut_id']);
}
else
{
	$inf_count_order=$db->query('SELECT count(*) as cnt FROM blog_orders WHERE user_id='.$user['user_id'].' AND ut_id!=0');
}
$count_orders=$db->fetch($inf_count_order);
$count_ord=$count_orders['cnt'];
// if ($user['user_id']==634) $count=25;
if ($user['user_id']==2121) $count=10;
if ($count>$count_ord)
{
	if ($_POST['mko']!='')
	{
		$qobject=$db->query('SELECT * FROM blog_object WHERE object_id='.$_POST['mko'].' LIMIT 1');
		$object=$db->fetch($qobject);
		$qwry=$object['object_keyword'];
	}
	elseif ($_POST['mkw']=='')
	{
		if ($_POST['mnw']!='')
		{
			$mnw=explode(',',$_POST['mnw']);
			foreach ($mnw as $nw)
			{
				if (preg_match('/\"/is',trim($nw)))
				{
					if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.\*]+\"$/isu',trim($nw))) && (trim($nw)!=''))
					{
						// if (trim($_POST['mw'])!='')	$or=' && ';
						$strnw.=$or.trim($nw);
						$or=' | ';
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
					if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.\*]+$/isu',trim($nw))) && (trim($nw)!=''))
					{
						// if (trim($_POST['mw'])!='')	$or=' && ';
						$strnw.=$or.trim($nw);
						$or=' | ';
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
					if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.\*]+\"$/isu',trim($w))) && (trim($w)!=''))
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
					if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\.\*]+$/isu',trim($w))) && (trim($w)!=''))
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
					if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ0-9\ \'\-\._\*]+\"$/isu',trim($ew))) && (trim($ew)!=''))
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
					if ((preg_match('/^[а-яА-Яa-zA-ZёЁ0-9\ \'\-\._\*]+$/isu',trim($ew))) && (trim($ew)!=''))
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
		// $qwry=(($strw!='')?'('.$strw.')':'').($strw!=''?'&&':'').$strnw.$strew;
		// $qwry=(($strw!='')?'('.$strw.')':'').(trim($_POST['mnw'])!=''?$strnw:'').$strew;
		$qwry=($strw==''?'':'('.$strw.')').($strnw==''?'':($strw==''?'':' & ').'('.$strnw.')').$strew;
		// echo $qwry;
		// die();
	}
	else
	{
		$mcount_open=explode('(', $_POST['mkw']);
		$mcount_close=explode(')', $_POST['mkw']);
		if (count($mcount_close)!=count($mcount_open))
		{
			$outmas['status']=6;
			echo json_encode($outmas);
			die();
		}
		$qwry=$_POST['mkw'];
	}
	
	if ($qwry!='')
	{
		if (check_query($qwry)==0)
		{
			$outmas['status']=6;
			echo json_encode($outmas);
			die();
		}
		if ($user['tariff_id']==16 && mb_strlen($qwry,'UTF-8')>=300)
		{
			$outmas['status']=13;
			echo json_encode($outmas);
			die();
		}		
		$xml = file_get_contents('http://blogs.yandex.ru/search.rss?text='.urlencode($qwry).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark');
		//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($qwry).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark';
		$r1='/<yablogs\:count>(?<count>.*?)<\/yablogs\:count>/is';
		preg_match_all($r1,$xml,$ot1);
		$count_res=intval($ot1['count'][0]);
		//echo $count_res;
		if (($count_res>5000) && ($user['tariff_id']==16)) //демо 2 - id 16
		{
			$mas['status']=3;
			echo json_encode($mas);	
			die();
		}
		//Похуй на интервал
		$assoc_interval[5]=1;
		$assoc_interval[6]=2;
		$assoc_interval[7]=3;
		$assoc_interval[12]=6;
		$assoc_interval[13]=3;
		$assoc_interval[14]=2;
		$assoc_interval[15]=1;

		// $_POST['order_start']=mktime(0,0,0,date("n",time())-(isset($assoc_interval[$inf_us['tariff_id']])?$assoc_interval[$inf_us['tariff_id']]:1),date("j",time()),date("Y",time()));//strtotime($_POST['order_start'])
		// $_POST['order_end']=mktime(0,0,0,date("n",time())+1,date("j",time()),date("Y",time()));
		if ($inf_us['tariff_id']==4)
		{
			$mas['status']=5;
			echo json_encode($mas);
			die();
		}

		//Демо-тариф две недели назад, две недели вперед
		/*if ($inf_us['tariff_id']==3)
		{
			$_POST['order_start']=mktime(0,0,0,date("n",time()),date("j",time())-14,date("Y",time()));//strtotime($_POST['order_start'])
			$_POST['order_end']=mktime(0,0,0,date("n",time()),date("j",time())+14,date("Y",time()));
		}
		else $_POST['order_end']=$user['ut_date'];*/
		if (strtotime($_POST['order_start'])<mktime(0,0,0,6,1,2014))
		{
			$mas['status']=51;
			echo json_encode($mas);
			die();
		}
		$_POST['order_start']=strtotime($_POST['order_start']);
		$_POST['order_end']=strtotime($_POST['order_end']);
		
		//Для демо-тарифа тема добавляется как заблокированная
		if ($inf_us['tariff_id']==3)
		{
			$usr_mb=0;
		}
		else
		{
			$usr_mb=$user['user_id'];
		}
		// print_r($inf_us);
		if ($inf_us['user_mid']!=0)
		{
			$qmid=$db->query('SELECT * FROM user_tariff WHERE user_id='.$inf_us['user_mid'].' LIMIT 1');
			$mid=$db->fetch($qmid);
			// print_r($mid);
			$usr_mb=$mid['user_id'];
			$inf_us['ut_id']=$mid['ut_id'];
		}
		if ($_POST['mko']!='') $qwry='@'.$_POST['mko'].'@';
		$qw='INSERT INTO blog_orders (order_date,order_name,order_keyword,user_id,order_start,order_end,third_sources,ut_id,ful_com,order_engage,similar_text,order_lang) VALUES ('.time().',\''.addslashes($_POST['order_name']).'\',\''.addslashes($qwry).'\','.$usr_mb.','.addslashes($_POST['order_start']).','.addslashes($_POST['order_end']).',1,'.$inf_us['ut_id'].',1,1,1,2)';
		// echo $qw;
		// die();
		$db->query($qw);
		$mas['order_id']=$db->insert_id();
		$headers  = "From: noreply@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply@wobot.ru\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		mail('account-one@wobot-research.com','Команда Wobot'.(($inf_us['tariff_id']==3)?' // Тема демо-тарифа':''),"Пользователь ".$user['user_email']." добавил тему для исследования. <br>Название темы: ".$_POST['order_name']."<br>Ключевые слова: ".$qwry."<br>Период мониторинга: ".date('d.m.y',$_POST['order_start'])."-".date('d.m.y',$_POST['order_end'])." ",$headers);
		mail('r@wobot.co','Команда Wobot'.(($inf_us['tariff_id']==3)?' // Тема демо-тарифа':''),"Пользователь ".$user['user_email']." добавил тему для исследования. <br>Название темы: ".$_POST['order_name']."<br>Ключевые слова: ".$qwry."<br>Период мониторинга: ".date('d.m.y',$_POST['order_start'])."-".date('d.m.y',$_POST['order_end'])." ",$headers);
		// parseUrl('http://188.120.239.225/tools/watch.php?order_id='.$mas['order_id']);
		$mas['status']='ok';
		
		if (strlen($qwry)<80)
		{
			$novoteka_query=preg_replace('/(\&+)/isu',' $1 ',$qwry);
			$novoteka_query=preg_replace('/(\|)/isu',' $1 ',$novoteka_query);
			$novoteka_query=preg_replace('/(\~+)/isu',' ~ ',$novoteka_query);
			$novoteka_query=preg_replace('/(\/\s*\(?[\+\-]\d+\s*[\+\-]\d+\)?|\/[\-\+]?\d+)/isu', ' & ', $novoteka_query);
			$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$mas['order_id'].',\''.addslashes($novoteka_query).'\',\'novoteka_news\')');
			$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type) VALUES ('.$mas['order_id'].',\''.addslashes($novoteka_query).'\',\'google_news\')');
		}

		//add hashtags
		$nostop = val_not($qwry,'');
		preg_match_all('/#(?<tag>[A-Za-z_0-9А-Яа-я]*)/isu', $nostop['kw'], $outtag);
		if(count($outtag['tag'])>0){
			$arr = $outtag['tag'];
			foreach ($arr as $key => $value) {
				$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$mas['order_id'].',\''.addslashes($value).'\',\'tag_instagram\',\'1\')');
			}
		}
    	//return $out['tag'];
    	echo json_encode($mas);

		if ($inf_us['tariff_id']!=3)
		{
			parseUrl('http://localhost/tools/charge.php?order_id='.$db->insert_id());
		}
		die();
	}
}
else
{
	$mas['status']=4;
	echo json_encode($mas);
	die();
}
//$lock=$db->query('UNLOCK TABLES');

?>