<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');
require_once('auth.php');
require_once('/var/www/daemon/fsearch3/ch.php');
require_once('/var/www/com/checker.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

/*$_POST['authors']='test123';
$_POST['authors_type']='only';
$_POST['auto_nastr']=0;
$_POST['from_age']=1;
$_POST['gender']=2;
$_POST['loc']='loc_Украина,loc_зарубежье,loc_Санкт-Петербург';
$_POST['mew']='test2';
//$_POST['mkw 
$_POST['mnw']='tets';
$_POST['mw']='tets1';
$_POST['order_id']=1406;
$_POST['order_name']='TEST';
$_POST['remove_spam']=1;
$_POST['res']='tetsts';
$_POST['res_type']='except';
$_POST['to_age']=20;*/
//$_POST=$_GET;
$av['remove_spam']=1;

//echo $_COOKIE['user_id'];
//error_reporting(-1);
auth();
if (!$loged) die();
//print_r($_POST);
//print_r($user);
//die();
if (($user['user_mid']!=0) && ($user['user_mid_priv']==3)) 
{
	$mas['status']='ok';
	echo json_encode($mas);
	die();	
}

$qus=$db->query('SELECT user_id,ut_id FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
$us=$db->fetch($qus);
if ($user['user_mid']==0)
{
	if ($us['ut_id']!=$user['ut_id'])
	{
		$errors[]=6;
		//$out['status']=6;
		//echo json_encode($out);
		//die();	
	}
}

if (intval($_POST['order_id'])==0)
{
	$errors[]=1;
	//$out['status']=1;
	//echo json_encode($out);	
	//die();
}

$qorder=$db->query('SELECT order_id,order_settings,order_keyword FROM blog_orders WHERE order_id='.$_POST['order_id']);
$order=$db->fetch($qorder);
$settings=json_decode($order['order_settings'],true);

/*foreach ($_POST as $key => $item)
{
	if (preg_match('/loc\_/isu',$key))
	{
		preg_match_all('/loc_(?<loc>[а-я\-ё\s]*)$/isu', $key, $out);
		$locations[]=$out['loc'][0];
	}
	if (preg_match('/loc\_/isu',$key))
	{
		preg_match_all('/res_(?<loc>[а-я\-ё\s]*)$/isu', $key, $out);
		$sources[]=$out['loc'][0];
	}
}*/

$mloc=explode(',', $_POST['loc']);
$mres=explode(',', $_POST['res']);
unset($settings['loc']);
unset($settings['res']);
unset($settings['author']);
foreach ($mloc as $item)
{
	if (trim($item)=='') continue;
	if (in_array($item, $settings['loc'])) continue;
	// $settings['cou'][]=$item;
}
foreach ($mres as $item)
{
	if (in_array($item, $settings['res'])) continue;
	// $settings['res'][]=$item;
}

$mauthors=explode(',', $_POST['authors']);
foreach ($mauthors as $item)
{
	if (trim($item)=='') continue;
	$user1=new users();
	$blog_id=$user1->get_url($item);
	if ($blog_id==0)
	{
		if (!in_array(8,$errors)) $errors[]=8;
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

unset($out);
/*if (count($locations)!=0)
{
	foreach ($locations as $item)
	{
		$settings['loc'][$item]=1;
	}
}
if (count($sources)!=0)
{
	foreach ($sources as $item)
	{
		$settings['res'][$item]=1;
	}
}*/

if (isset($_POST['gender']))
{
	$settings['gender']=intval($_POST['gender']);
}

if (isset($_POST['remove_spam']))
{
	if ($settings['remove_spam']!=intval($_POST['remove_spam'])) file_get_contents('http://localhost/tools/cashjob.php?order_id='.$_POST['order_id']);
	$settings['remove_spam']=intval($_POST['remove_spam']);
}
else
{
	if (!isset($settings['remove_spam'])) $settings['remove_spam']=0;
}

if (($_POST['order_start']!='') && ($_POST['order_end']!=''))
{
	$start=strtotime($_POST['order_start']);
	$end=strtotime($_POST['order_end']);
	if ($start<$end) $query_adv.=',order_start='.$start.',order_end='.$end;
	else $errors[]=9;
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
if (isset($_POST['tone_type']))
{
	$settings['tone_type']=$_POST['tone_type'];
}
if (isset($_POST['tone_object']))
{
	$settings['tone_object']=$_POST['tone_object'];
}

if ($_POST['mko']!='')
{
	$qobject=$db->query('SELECT * FROM blog_object WHERE object_id='.$_POST['mko'].' LIMIT 1');
	$object=$db->fetch($qobject);
	$qwry=$object['object_keyword'];
}
elseif ($_POST['mkw']!='')
{
	$mcount_open=explode('(', $_POST['mkw']);
	$mcount_close=explode(')', $_POST['mkw']);
	if (count($mcount_close)!=count($mcount_open))
	{
		$errors[]=7;
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
					// if (trim($_POST['mw'])!='')	$or=' | ';
					$strnw.=$or.trim($nw);
					$or=' | ';
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
					// if (trim($_POST['mw'])!='')	$or=' | ';
					$strnw.=$or.trim($nw);
					$or=' | ';
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
	// $qwry=(($strw!='')?'('.$strw.')':'').($strw!=''?'&&':'').$strnw.$strew;
	// $qwry=(($strw!='')?'('.$strw.')':'').(trim($_POST['mnw'])!=''?$strnw:'').$strew;
	$qwry=($strw==''?'':'('.$strw.')').($strnw==''?'':($strw==''?'':' & ').'('.$strnw.')').$strew;
	// echo $qwry;
	// die();
	if ($qwry!='')
	{
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
		if (($count_res>10000) && ($user['tariff_id']==16))
		{
			$errors[]=3;
			$out['status']=3;
			echo json_encode($out);	
			die();
		}
		//$db->query('UPDATE blog_orders SET order_keyword=\''.addslashes($qwry).'\' WHERE order_id='.intval($_POST['order_id']));
		//$out['status']='ok';
		//echo json_encode($out);
	}
}

if (check_query($qwry)==0)
{
	$errors[]=3;
	$outmas['status']=6;
	// echo json_encode($outmas);
	// die();
}

if ($qwry!=$order['order_keyword']) $is_modified=1;

if($is_modified==1){
	$nostop = val_not($qwry,'');
	preg_match_all('/#(?<tag>[A-Za-z_0-9А-Яа-я]*)/isu', $nostop['kw'], $outtag);
	$curr_tags = $db->query('SELECT tp_id,gr_id FROM blog_tp WHERE order_id='.$_POST['order_id'].' AND tp_type=\'tag_instagram\'');
	$exist_tags = array();
	$exist_ids = array();
	$new_tags = $outtag['tag'];
	$deleted_tags = array();
	$add_tags = array();



	while ($tag = $db->fetch($curr_tags)) {
		$exist_tags[]=$tag['gr_id'];
		$exist_ids[]=$tag['tp_id'];
	}

	foreach ($new_tags as $key => $value) {
		if(!in_array($value, $exist_tags)){
			$add_tags[]=$value;
		}
	}

	foreach ($exist_tags as $key => $value) {
		if(!in_array($value, $new_tags)){
			$deleted_tags[]=$key;
		}
	}

	if(count($add_tags)>0){
			foreach ($add_tags as $key => $value) {
				$db->query('INSERT INTO blog_tp (order_id,gr_id,tp_type,tp_filter) VALUES ('.$order['order_id'].',\''.addslashes($value).'\',\'tag_instagram\',\'1\')');
			}
		}

	if(count($deleted_tags)>0){
			foreach ($deleted_tags as $key => $value) {
				$db->query('DELETE FROM blog_tp WHERE tp_id='.$exist_ids[$value]);
			}
		}
}

if (trim($_POST['order_name'])!='')
{
	$_POST['order_name']=trim(preg_replace('/[^а-яА-Яa-zA-ZёЁ0-9\!\?\s\(\)]/isu', '', $_POST['order_name']));
	if (mb_strlen($_POST['order_name'],'UTF-8')<3)
	{
		$errors[]=4;
		//$out['status']=4;
		//echo json_encode($out);	
		//die();
	}
}

if (isset($_POST['auto_nastr']) && intval($_POST['auto_nastr'])==0) $auto_nastr=0;
elseif (isset($_POST['auto_nastr']) && intval($_POST['auto_nastr'])==1) $auto_nastr=mktime(0,0,0,date('n'),date('j'),date('Y'));
elseif (isset($_POST['auto_nastr']) && intval($_POST['auto_nastr'])==2) $auto_nastr=1;

if (count($errors)!=0)
{
	$out['errors']=$errors;
	echo json_encode($out);
	die();
}
else
{
	if ($_POST['mko']!='') $qwry='@'.$_POST['mko'].'@';
	if ($user['user_mid']!=0)
	{
		$qmid=$db->query('SELECT * FROM user_tariff WHERE user_id='.$user['user_mid'].' LIMIT 1');
		$mid=$db->fetch($qmid);
		// print_r($mid);
		$user['user_id']=$mid['user_id'];
		$user['ut_id']=$mid['ut_id'];
	}
	//echo 'UPDATE blog_orders SET '.(count($settings)!=0?'order_settings=\''.addslashes(json_encode($settings)).'\'':'').(isset($_POST['auto_nastr'])?',order_nastr='.intval($_POST['auto_nastr']):'').($qwry!=''?',order_keyword=\''.addslashes($qwry).'\'':'').(isset($_POST['order_name'])?',order_name=\''.addslashes($_POST['order_name']).'\'':'').(intval($_POST['disable_theme'])==1?',user_id=0':',user_id='.$user['user_id']).' WHERE order_id='.intval($_POST['order_id']);
	$db->query('UPDATE blog_orders SET '.(count($settings)!=0?'order_settings=\''.addslashes(json_encode($settings)).'\'':'').((isset($_POST['auto_nastr'])&&$user['tariff_id']!=16)?',order_nastr='.$auto_nastr:'').($qwry!=''?',order_keyword=\''.addslashes($qwry).'\'':'').(isset($_POST['order_name'])?',order_name=\''.addslashes($_POST['order_name']).'\'':'').(intval($_POST['disable_theme'])==1?',user_id=0':',user_id='.$user['user_id']).($is_modified==1?',order_last_modified='.time():'').$query_adv.' WHERE order_id='.intval($_POST['order_id']));
	//$out['status']='ok';
	$out['status']='ok';
	echo json_encode($out);
}
?>