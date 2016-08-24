<?php
/*

Wobot inc. 2010

Отображение постов
Разработчики: Рыбаков Владимир, Юдин Роман
Запускается: при нажатии на кнопку Показать мастера отчетов

*/
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/new/com/auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/new/com/porter.php');
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();
//print_r($_POST);
auth();
if (!$loged) die();
//print_r(json_decode(urldecode($_POST['tag_links']),true));
//loading metrics
if (intval($_POST['order_id'])==0) die();
$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$order=$db->fetch($res);
$src=$order['order_src'];
$src=json_decode($src,true);
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
//print_r($metrics['topwords']);
//print_r($metrics['location']);
//print_r($order);
$type_text="";
$word=new Lingua_Stem_Ru();

foreach ($_POST as $inddd => $posttt)
{
	if ((substr($inddd, 0, 4)=='res_'))
	{
		if ($posttt=='on')
		{
			if (mb_strlen(substr($inddd, 4),'UTF-8')!=1)
			{
				$resorrr[]=str_replace("_",".",substr($inddd,4));
				//$asocres[str_replace("_",".",substr($inddd,4))]++;
				$asocres[urldecode(preg_replace('/(.*?)\_([^\_]*)$/is','$1.$2',substr($inddd,4)))]++;
			}
		}
	}
	if ((substr($inddd, 0, 4)=='tags'))
	{
		if (substr($inddd, 4)!='')
		{
			$tgv[]=substr($inddd, 4);
		}
		else
		{
			$tgv[]='no';
		}
	}
	if ((substr($inddd, 0, 7)=='cities_'))
	{
		if (isset($wobot['destn2'][str_replace('_',' ',substr($inddd, 7))]))
		{
			$loc[]=str_replace('_',' ',substr($inddd,7));
			$asocloc[str_replace('_',' ',substr($inddd,7))]++;
		}
		if (substr($inddd, 7)=='не_определено')
		{
			$loc[]='na';
			$asocloc['na']++;
		}
	}
	if ((substr($inddd, 0, 16)=='promouters_popup'))
	{
		$prom[]=$posttt;
		$ascprom[$posttt]=1;
	}
	if ((substr($inddd, 0, 14)=='speakers_popup'))
	{
		$speak[]=$posttt;
		$ascspeak[$posttt]=1;
	}
	if ((substr($inddd, 0, 6)=='words_'))
	{
		$kww[]=substr($inddd, 6);
		$asckww[substr($inddd, 6)]=1;
	}
}
//print_r($resorrr);
//echo '<br><br>';
//print_r($ascspeak);
//echo '<br><br>';
//echo count($resorrr).'|||<br><br>';
//print_r($loc);
//print_r($src);

$query1='SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']);
   $respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
	$tagsallrev[$tgl1['tag_name']]=$tgl1['tag_tag'];
}

/*foreach ($_POST as $inddd1 => $posttt1)
{
	if ((substr($inddd1, 0, 4)=='tags'))
	{
		$tgv[]=substr($inddd1, 4);
	}
}*/
//print_r($_SESSION);
//echo $_POST['hashq'];
//if (!isset($_SESSION[$_POST['hashq']]))
//print_r($_POST);
{
	if ($_POST['ntime']!='')
	{
		if ($_POST['str']=='')
		{
			//echo 'gg';
			if ($_POST['nword']!='')
			{
				//$msg=$word->stem_word(preg_replace('/\s*/isu','',mb_strtoupper($_POST['nword'],'UTF-8')));
				//echo $msg;
				$qwery='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND UPPER(p.post_content) LIKE \'%'.mb_strtoupper($word->stem_word(preg_replace('/\s*/isu','',$_POST['nword'])),'UTF-8').'%\' ORDER BY p.post_time DESC LIMIT 10';
				$sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND UPPER(p.post_content) LIKE \'%'.mb_strtoupper($word->stem_word(preg_replace('/\s*/isu','',$_POST['nword'])),'UTF-8').'%\' ORDER BY p.post_time DESC LIMIT ';
				$qposts=$db->query($qwery);
				//echo $qwery.' '.($order['order_end']+86400);
				$_POST['hashq']=md5($sqw);
				$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND UPPER(p.post_content) LIKE \'%'.mb_strtoupper($word->stem_word(preg_replace('/\s*/isu','',$_POST['nword'])),'UTF-8').'%\' GROUP BY post_host');
			}
			else
			if ($_POST['nnick']!='')
			{
				$qwery='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND b.blog_login=\''.$_POST['nnick'].'\' ORDER BY p.post_time DESC LIMIT 10';
				$sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND b.blog_login=\''.$_POST['nnick'].'\' ORDER BY p.post_time DESC LIMIT ';
				$qposts=$db->query($qwery);
				//echo $qwery.' '.($order['order_end']+86400);
				$_POST['hashq']=md5($sqw);
				$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND b.blog_login=\''.$_POST['nnick'].'\' GROUP BY post_host');
			}
			else
			{
				$qwery='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.((count($_POST)<10)?' AND p.post_time>='.$order['order_start'].' AND p.post_time<='.(($order['order_end']==0)?($order['order_last']+86400):($order['order_end']+86400)):getisshow3()).' ORDER BY p.post_time DESC LIMIT 10';
				$sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.((count($_POST)<10)?' AND p.post_time>='.$order['order_start'].' AND p.post_time<='.(($order['order_end']==0)?($order['order_last']+86400):($order['order_end']+86400)):getisshow3()).' ORDER BY p.post_time DESC LIMIT ';
				$qposts=$db->query($qwery);
				//echo $qwery.' '.($order['order_end']+86400);
				$_POST['hashq']=md5($sqw);
				$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.((count($_POST)<10)?' AND p.post_time>='.$order['order_start'].' AND p.post_time<'.(($order['order_end']==0)?($order['order_last']+86400):($order['order_end']+86400)):getisshow3()).' GROUP BY post_host');
			}
			//echo 'SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.((count($_POST)<10)?' AND p.post_time>='.$order['order_start'].' AND p.post_time<='.(($order['order_end']==0)?$order['order_last']:$order['order_end']):getisshow3()).' GROUP BY post_host';
			while ($count=$db->fetch($countqposts))
			{
				$cnt+=$count['cnt'];
				$cnt_host++;
			}
			//echo $cnt.' '.$cnt_host;
			$_SESSION[$_POST['hashq']]=$sqw;
			$_SESSION['count_'.$_POST['hashq']]=$cnt;
			$_SESSION['counth_'.$_POST['hashq']]=$cnt_host;
			if ($_POST['ton']=='n') {$ton1='true'; $t_ton.='нейтральные, ';}
			if ($_POST['ton1']=='p') {$ton2='true'; $t_ton.='положительные, ';}
			if ($_POST['ton2']=='m') {$ton3='true'; $t_ton.='отрицательные, ';}
			if ($_POST['ton3']=='no') {$ton4='true'; $t_ton.='не определено, ';}
			$t_ton=substr($t_ton,0,strlen($t_ton)-2);
			if ($t_ton=='') 
			{
				$t_ton='нейтральные, положительные, отрицательные, не определено'; 
				$ton1='true';
				$ton2='true';
				$ton3='true';
				$ton4='true';
			}
		}
		else
		{
			if (isset($_SESSION[$_POST['hashq']]))
			{
				//echo 'tt';
				//$qposts=$db->query($_SESSION[$_POST['hashq']].($_POST['str']*10).',10');
				$qwery=$_SESSION[$_POST['hashq']].($_POST['str']*10-10).',10';
				$qposts=$db->query($qwery);
				//echo '<br>'.$_SESSION[$_POST['hashq']].($_POST['str']*10).',10';
				//print_r($_POST);
				$ton1='false';
				$ton2='false';
				$ton3='false';
				$ton4='false';
				if ($_POST['ton']=='n') {$ton1='true'; $t_ton.='нейтральные, ';}
				if ($_POST['ton1']=='p') {$ton2='true'; $t_ton.='положительные, ';}
				if ($_POST['ton2']=='m') {$ton3='true'; $t_ton.='отрицательные, ';}
				if ($_POST['ton3']=='no') {$ton4='true'; $t_ton.='не определено, ';}
				$t_ton=substr($t_ton,0,strlen($t_ton)-1);
			}
			else
			{
				//echo 'jj';
				$qwery='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" ORDER BY p.post_time DESC LIMIT 10';
				$sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" ORDER BY p.post_time DESC LIMIT ';
				$qposts=$db->query($qwery);
				//echo $qwery;
				$_POST['hashq']=md5($sqw);
				$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p WHERE p.order_id='.intval($_POST['order_id']).' AND p.post_time>='.$order['order_start'].' AND p.post_time<='.(($order['order_end']==0)?$order['order_last']:$order['order_end']));
				$count=$db->fetch($countqposts);
				$cnt=$count['cnt'];
				$_SESSION[$_POST['hashq']]=$sqw;
				$_SESSION['count_'.$_POST['hashq']]=$cnt;
			}
		}
	}
}
//else
{
	//echo 'tt';
	//$qposts=$db->query($_SESSION[$_POST['hashq']].($_POST['str']*10).',10');
	//echo '<br>'.$_SESSION[$_POST['hashq']].','.($_POST['str']*10);
}


$params.='var chtags=new Array();';
while($pst = $db->fetch($qposts))
{
	$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
	$outcash['time'][$ii]=$pst['post_time'];
	$outcash['content'][$ii]=$pst['post_content'];
	$outcash['isfav'][$ii]=$pst['post_fav'];
	$outcash['nastr'][$ii]=$pst['post_nastr'];
	$outcash['isspam'][$ii]=$pst['post_spam'];
	$outcash['nick'][$ii]=$pst['blog_nick'];
	$outcash['type'][$ii]=$pst['post_type'];
	$outcash['id'][$ii]=$pst['post_id'];
	$outcash['tag'][$ii]=explode(',',$pst['post_tag']);
	//print_r($outcash['tag'][$ii]);
	$params.='chtags['.$outcash['id'][$ii].']=Array(';
	$zap='';
	foreach ($tagsall as $item => $key)
	{
		if (in_array($item,$outcash['tag'][$ii]))
		{
			$params.=$zap.'{\'name\':\''.$key.'\',\'value\':\'true\'}';
			$zap=', ';
		}
		else
		{
			$params.=$zap.'{\'name\':\''.$key.'\',\'value\':\'false\'}';
			$zap=', ';
		}
	}
	$params.=');';
	$outcash['readers'][$ii]=$pst['blog_readers'];
	$outcash['loc'][$ii]=$pst['blog_location'];
	$outcash['gender'][$ii]=$pst['blog_gender'];
	$outcash['age'][$ii]=$pst['blog_age'];
	$outcash['blogid'][$ii]=$pst['blog_id'];
	$outcash['blogin'][$ii]=$pst['blog_login'];
	$outcash['eng'][$ii]=$pst['post_engage'];
	$ii++;
}

foreach ($type as $ind => $items)
{
	if (count($type)!=1)
	{
		if ($ind!=(count($type)-1))
		{
			$type_text.=$items.", ";	
		}
		else
		{
			$type_text.=$items;
		}
	}	
	else
	{
		$type_text=$items;
	}
	
}

//print_r($src);
$params.='
var esp=0;
var chesp=\'\';
var author=\'\'; 
var hhn=\'\';
var ppid=0;
	function onspam(idt){
		var t=$(\'#spamthis\').attr(\'checked\')+\'|\'+$(\'#spamauth\').attr(\'checked\')+\'|\'+$(\'#spamres\').attr(\'checked\'); 
		$.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&idt=\'+idt+\'&hn=\'+hhn+\'&atr=\'+author+\'&ch=\'+chesp+\'&post_id=\'+esp+\'&spam=\'+t, success: function(msg1){ if(msg1==\'reload\'){ document.getElementById(\'filterform\').submit(); } } });
	}
	var time_beg=\''.(($_POST['sd']!='')?$_POST['sd']:$_POST['ntime']).'\';
	var time_end=\''.(($_POST['ed']!='')?$_POST['ed']:$_POST['etime']).'\';
	var time_start1=\''.$_POST['ntime'].'\';
	var time_end1=\''.$_POST['etime'].'\';
	var time_b=\''.$_POST['sd'].'\';
	var time_e=\''.$_POST['ed'].'\';
	var speakers = Array(
	';
	$zap='';
	foreach ($metrics['speakers']['link'] as $key => $item)
	{
		if ($item=='twitter.com')
		{
			$text_link='http://twitter.com/'.$metrics['speakers']['nick'][$key];
			$text_nick=$metrics['speakers']['nick'][$key];
		}
		else
		if ($item=='livejournal.com')
		{
			$text_link='http://'.$metrics['speakers']['nick'][$key].'.livejournal.com/';
			$text_nick=$metrics['speakers']['nick'][$key];
		}
		else
		if ($item=='vkontakte.ru')
		{
			$text_link='http://vkontakte.ru/'.$metrics['speakers']['nick'][$key];
			$text_nick=$metrics['speakers']['rnick'][$key];
		}
		else
		if ($item=='facebook.com')
		{
			$text_link='http://facebook.com/'.$metrics['speakers']['nick'][$key];
			$text_nick=$metrics['speakers']['rnick'][$key];
		}
		$rnick=$text_nick;
		if (mb_strpos($text_nick,' ')!==false)
		{
			$text_nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$text_nick);
		}
		if (mb_strlen($text_nick,'UTF-8')>13)
		{
			//$text_nick=mb_substr($text_nick,0,11,'UTF-8').'...';
		}
		if ($_POST['nnick']=='')
		{
			$params.=$zap.'{\'name\': \''.$text_nick.'\',\'nick\': \''.$metrics['speakers']['nick'][$key].'\', \'num\': '.intval($metrics['speakers']['posts'][$key]).', \'link\':\''.$text_link.'\',\'checked\':\''.(isset($ascspeak[$metrics['speakers']['nick'][$key]])?true:false).'\'}';
		}
		else
		{
			if ($_POST['nnick']==$metrics['speakers']['nick'][$key])
			{
				$params.=$zap.'{\'name\': \''.$text_nick.'\',\'nick\': \''.$metrics['speakers']['nick'][$key].'\', \'num\': '.intval($metrics['speakers']['posts'][$key]).', \'link\':\''.$text_link.'\',\'checked\':\'true\'}';
			}
			else
			{
				$params.=$zap.'{\'name\': \''.$text_nick.'\',\'nick\': \''.$metrics['speakers']['nick'][$key].'\', \'num\': '.intval($metrics['speakers']['posts'][$key]).', \'link\':\''.$text_link.'\',\'checked\':\'\'}';
			}
		}
		$zap=',';
	}
$params.='
	);
	var promouters = Array(
	';
	$zap='';
	foreach ($metrics['promotion']['link'] as $key => $item)
	{
		if ($item=='twitter.com')
		{
			$text_link='http://twitter.com/'.$metrics['promotion']['nick'][$key];
			$text_nick=$metrics['promotion']['nick'][$key];
		}
		else
		if ($item=='livejournal.com')
		{
			$text_link='http://'.$metrics['promotion']['nick'][$key].'.livejournal.com/';
			$text_nick=$metrics['promotion']['nick'][$key];
		}
		$rnick=$text_nick;
		if (mb_strpos($text_nick,' ')!==false)
		{
			$text_nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$text_nick);
		}
		if (mb_strlen($text_nick,'UTF-8')>13)
		{
			//$text_nick=mb_substr($text_nick,0,11,'UTF-8').'...';
		}
		if ($_POST['nnick']=='')
		{
			$params.=$zap.'{\'name\': \''.$text_nick.'\',\'nick\': \''.$text_nick.'\' , \'num\': '.intval($metrics['promotion']['readers'][$key]).', \'link\':\''.$text_link.'\', \'checked\':\''.(isset($ascprom[$rnick])?true:false).'\'}';
		}
		else
		{
			//echo $_POST['nnick'].' '.$metrics['promotion']['nick'][$key].'<br>';
			if ($_POST['nnick']==$metrics['promotion']['nick'][$key])
			{
				$params.=$zap.'{\'name\': \''.$text_nick.'\',\'nick\': \''.$text_nick.'\', \'num\': '.intval($metrics['promotion']['readers'][$key]).', \'link\':\''.$text_link.'\', \'checked\':\'true\'}';
			}
			else
			{
				$params.=$zap.'{\'name\': \''.$text_nick.'\',\'nick\': \''.$text_nick.'\', \'num\': '.intval($metrics['promotion']['readers'][$key]).', \'link\':\''.$text_link.'\', \'checked\':\'\'}';
			}
		}
		$zap=',';
	}
$params.='
	);
	var tags=Array(
	';
	$zap=', ';
	$qtags=$db->query("SELECT * from blog_tag WHERE user_id=".intval($user['user_id']));
	if (count($_POST)>10)
	{
		if ($_POST['tagsбез_тегов']=='1')
		{
			$params.='{\'name\':\'без тегов\',\'value\':\'true\'} ';
		}
		else
		{
			$params.='{\'name\':\'без тегов\',\'value\':\'false\'} ';
		}
		while ($tags=$db->fetch($qtags))
		{
			$mtags[$tags['tag_name']]['name']=$tags['tag_name'];
			$mtags[$tags['tag_name']]['id']=$tags['tag_tag'];
			if (isset($_POST['tags'.preg_replace('/\s/is','_',$tags['tag_name'])]))
			{
		 		$params.=$zap.'{\'name\':\''.$tags['tag_name'].'\',\'value\':\'true\',\'idt\':'.$tags['tag_tag'].'}';
			}
			else
			{
		 		$params.=$zap.'{\'name\':\''.$tags['tag_name'].'\',\'value\':\'false\',\'idt\':'.$tags['tag_tag'].'}';
			}
			$zap=',';
		}
	}
	else
	{
		$params.='{\'name\':\'без тегов\',\'value\':\'true\'} ';
		while ($tags=$db->fetch($qtags))
		{
			$mtags[$tags['tag_name']]['name']=$tags['tag_name'];
			$mtags[$tags['tag_name']]['id']=$tags['tag_tag'];
	 		$params.=$zap.'{\'name\':\''.$tags['tag_name'].'\',\'value\':\'true\'}';
			//$zap=',';
		}
	}
	$params.='
	);
var words=Array(';
$zap='';
$ii=0;
//print_r($asckww);
$c=0;
foreach ($metrics['topwords'] as $key => $item)
{
	$ii++;
	//if ($ii<10)
	{
		if ($_POST['nword']=='')
		{
			$params.=$zap.'{\'name\':\''.$key.'\',\'checked\':\''.(isset($asckww[$key])?true:false).'\'}';
			$zap=',';
		}
		else
		{
			if (preg_replace('/\s*/is','',mb_strtolower($_POST['nword'],'UTF-8'))==$key)
			{
				$params.=$zap.'{\'name\':\''.$key.'\',\'checked\':\'1\'}';
				$zap=',';
				$c=1;
			}
			else
			{
				$params.=$zap.'{\'name\':\''.$key.'\',\'checked\':\'\'}';
				$zap=',';
			}
		}
	}
}
if ($c==0)
{
	$params.=$zap.'{\'name\':\''.preg_replace('/\s*/is','',mb_strtolower($_POST['nword'],'UTF-8')).'\',\'checked\':\'1\'}';
	$zap=',';
}
foreach ($asckww as $key => $item)
{
	if (($key!='rb') && (!isset($metrics['topwords'][$key])))
	{
		$params.=$zap.'{\'name\':\''.$key.'\',\'checked\':\'1\'}';
	}
}
$params.=');

/*var words=Array(\'слово1\',\'слово2\',\'слово3\',\'слово4\',\'слово5\');*/
	';
	//print_r($m);
	//print_r($asocloc);
	if (count($_POST)>10)
	{
		foreach ($metrics['location'] as $key => $item)
		{
			$i++;
			//if ($i<10)
			{
				if ($key=='')
				{
					if (isset($asocloc['na']))
					{
						$mas['не определено']=true;
						$mas['не определено']['syscheck']=0;
						$not['не определено']=1;
					}
					else
					{
						$mas['не определено']=false;
						$mas['не определено']['syscheck']=0;
						//$not['не определено']=1;
					}
				}
				else
				{
					if ($wobot['destn3'][$key]=='Россия')
					{
						//if (isset($asocloc[str_replace(' ','_',$key)]))
						if (isset($asocloc[$key]))
						{
							$mas[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])][str_replace(' ','_',$key)]=true;
							$mas[$wobot['destn3'][$key]]['syscheck']=0;
							$not[$wobot['destn3'][$key]]=1;
							$mas[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])]['syscheck']=0;
							$mnot[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])]=1;
						}
						else
						{
							$mas[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])][str_replace(' ','_',$key)]=false;
							$mas[$wobot['destn3'][$key]]['syscheck']=0;
							$mas[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])]['syscheck']=0;
						}
					}
					else
					{
						//if (isset($asocloc[str_replace(' ','_',$key)]))
						if (isset($asocloc[$key]))
						{
							$mas[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)][str_replace(' ','_',$key)]=true;
							$mas[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)]['syscheck']=0;
							$not[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)]=1;
						}
						else
						{
							$mas[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)][str_replace(' ','_',$key)]=false;
							$mas[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)]['syscheck']=0;
						}
					}
				}
			}
		}
	}
	else
	{
		foreach ($metrics['location'] as $key => $item)
		{
			$i++;
			{
				if ($key=='')
				{
					$mas['не определено']=null;
				}
				else
				{
					if ($wobot['destn3'][$key]=='Россия')
					{
						$mas[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])][str_replace(' ','_',$key)]=true;
						$mas[$wobot['destn3'][$key]]['syscheck']=1;
						$mas[$wobot['destn3'][$key]][(isset($m[$key])?$m[$key]:$wobot['destn3'][$key])]['syscheck']=1;
					}
					else
					{
						$mas[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)][str_replace(' ','_',$key)]=true;
						$mas[(isset($wobot['destn3'][$key])?$wobot['destn3'][$key]:$key)]['syscheck']=1;
					}
				}
			}
		}
	}
	foreach ($not as $key => $item)
	{
		$mas[$key]['syscheck']=1;
	}
	foreach ($mnot as $key1 => $item)
	{
		foreach ($item as $key2 => $it)
		{
			$mas[$key1][$key2]['syscheck']=1;
		}
	}
	//print_r($mas);
	//print_r($mas);
	//unset($mas);
	//$mas['авыаыв']=null;
	//$mas['авыа']['ффыф']=null;
	//$mas['авыа']['ыыыы']=null;
	//$mas=array("авыаыва"=>null,"ыыыы"=>array("цццц"=>null,"йййй"=>null));
	$params.='var cities = '.json_encode($mas).';';
	$params.='/*
var cities = {"не определено":null,"Россия":{"Центр":{"Московская обл-ть":{"Москова":null,"Королёв":null,"Люберцы":null,"Одинцово":null},"Тверская обл-ть":null,"Владимирская обл-ть":null,"Санкт-Петербург":null,"Кострома":null},"Север":{"Московская обл-ть":null,"Тверская обл-ть":null,"Владимирская обл-ть":null,"Восток":null,"Запад":null},"Юг":null,"Восток":null,"Запад":null},
    "Белоруссия":{"Центр":null,"Север":null,"Юг":null,"Восток":null,"Запад":null}};*/
	';
	//print_r($asocres);
	unset($not);
	if (count($_POST)>10)
	{
		foreach ($src as $key => $item)
		{
			$key=urldecode($key);
			//echo $key.' ';
			if (isset($asocres[$key]))
			{
				$mas1[mb_strtoupper(mb_substr($key,0,1,"UTF-8"),'UTF-8')][$key]=true;
				$not[mb_substr($key,0,1,"UTF-8")]=1;
				//echo mb_substr($key,0,1,"UTF-8").'<br>';
				$mas1[mb_strtoupper(mb_substr($key,0,1,"UTF-8"),'UTF-8')]['syscheck']=0;
			}
			else
			{
				$mas1[mb_strtoupper(mb_substr($key,0,1,"UTF-8"),'UTF-8')][$key]=false;
				$must[$key[0]]=1;
				$mas1[mb_strtoupper(mb_substr($key,0,1,"UTF-8"),'UTF-8')]['syscheck']=0;
			}
		}
	}
	else
	{
		foreach ($src as $key => $item)
		{
			$key=urldecode($key);
			$mas1[mb_strtoupper(mb_substr($key,0,1,"UTF-8"),'UTF-8')][$key]=true;
			$mas1[mb_strtoupper(mb_substr($key,0,1,"UTF-8"),'UTF-8')]['syscheck']=1;
		}
	}
	foreach ($not as $key => $item)
	{
		$mas1[mb_strtoupper($key,'UTF-8')]['syscheck']=1;
	}
	//print_r($not);
	ksort($mas1);
	$params.='var resources = '.json_encode($mas1).';';
	$params.='
/*var resources = {"не определено":null,"Россия":{"Центр":{"Московская обл-ть":{"Москова":null,"Королёв":null,"Люберцы":null,"Одинцово":null},"Тверская обл-ть":null,"Владимирская обл-ть":null,"Санкт-Петербург":null,"Кострома":null},"Север":{"Московская обл-ть":null,"Тверская обл-ть":null,"Владимирская обл-ть":null,"Восток":null,"Запад":null},"Юг":null,"Восток":null,"Запад":null},
    "Белоруссия":{"Центр":null,"Север":null,"Юг":null,"Восток":null,"Запад":null}};*/

function ontag(html){
	$.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&tagsall='.urlencode(json_encode($mtags)).'&post_id=\'+ppid+\'&taghtml=\'+html, success: function(msg1){  } });
}
function maddtag(neww)
{
	$.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&user_id='.intval($user['user_id']).'&nameaddtag=\'+neww, success: function(msg1){ document.getElementById(\'filterform\').submit(); } });
}
function deltagc(deltag)
{
	$.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&tagsall='.urlencode(json_encode($mtags)).'&user_id='.intval($user['user_id']).'&namedeltag=\'+deltag, success: function(msg1){ document.getElementById(\'filterform\').submit(); } });
}
function edittags(edittag)
{
	$.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&tagsall='.urlencode(json_encode($mtags)).'&user_id='.intval($user['user_id']).'&nameedittag=\'+edittag, success: function(msg1){ document.getElementById(\'filterform\').submit(); } });
}
';

switch ($_POST['ref']) {
    case 'all':
    	$t_up='все';
        break;
    case 1:
    	$t_up='избранные';
        break;
    case 2:
    	$t_up='без спама';
        break;
    case 3:
    	$t_up='только спам';
        break;
    default;
        $t_up='все';
    break;
}

start_tpl('','',$params);

$html_out .='
		<form action="'.$config['html_root'].'comment" method="POST" id="filterform">
		<input type="hidden" name="order_id" value="'.$_POST['order_id'].'">
		<input type="hidden" name="hashq" value="'.$_POST['hashq'].'">
		<input type="hidden" name="str" value="" id="str">
		<input type="hidden" name="ntime" value="'.$_POST['ntime'].'">
		<input type="hidden" name="etime" value="'.$_POST['etime'].'">
	          <div class="span-5 border" id="filters">  
	              <div id="time_full" class="filter filter_color bottomborder hide">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/time.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите временной период, за который вы хотите исследовать упоминания или используйте стандартные временные периоды.">Время</h3>
	                  </div>
	                      <div class="date clear span-5 last">
	                          <p class="span-1 text-right">c</p>
	                          <input type="text" class="format-d-m-y divider-dot highlight-days-67 range-high-today span-3" name="sd" id="sd" value="'.$_POST['sd'].'"/>
	                      </div>
	                      <div class="date clear span-5 last">
	                          <p class="span-1 text-right">по</p>
	                          <input type="text" class="format-d-m-y divider-dot highlight-days-67 range-high-today span-3" name="ed" id="ed" value="'.$_POST['ed'].'"/>
	                      </div>
	                  <div>
	                       <div class="clear"><label for="time_all">весь период</label><input id="time_all" type="radio" name="time" value="all" class="styled" /></div>
	                       <div class="clear"><label for="time_today">день</label><input id="time_today" type="radio" name="time" value="day" class="styled" /></div>
	                       <div class="clear"><label for="time_week">неделя</label><input id="time_week" type="radio" name="time" value="week" class="styled" /></div>
	                       <div class="clear"><label for="time_month">месяц</label><input id="time_month" type="radio" name="time" value="month" class="styled" /></div>
	                       <div class="clear"><label for="time_diffrent">другое</label><input id="time_diffrent" type="radio" name="time" value="different" class="styled" checked=\'true\'/></div>
	                  </div>
	                  <div class="row clear"></div>
	<!--                  <hr/>-->
	              </div>
	              <div id="time_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/time.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите временной период, за который вы хотите исследовать упоминания или используйте стандартные временные периоды.">Время</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">с&emsp;<span>03/09/2011</span></p>
	                      <p class="span-5 last">по&emsp;<span>03/10/2011</span></p>
	                  </div>
	                  <div class="row clear"></div>
	<!--                      <div class="clear span-5 last">
	                          <p class="span-1 text-right">c</p>
	                          <input type="text" class="span-3 input" name="1" value="03/09/2011" readonly/>
	                      </div>
	                      <div class="clear span-5 last">
	                          <p class="span-1 text-right">по</p>
	                          <input type="text" class="span-3 input" name="fname" value="03/10/2011" readonly/>
	                      </div>-->
	<!--                  <hr/>-->
	              </div>

	              <div id="ton_full" class="filter bottomborder filter_color hide">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/smile.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите эмоциональную окраску мнений.">Тональность</h3>
	                  </div>
	                  <div>
	                       <div class="clear"><label for="ton_neu">нейтральные</label><input id="ton_neu" type="checkbox" name="ton" value="n" class="styled" '.(($ton1=='true')?'checked="true"':'').' /></div>
	                       <div class="clear"><label for="ton_good">положительные</label><input id="ton_good" type="checkbox" name="ton1" value="p" class="styled" '.(($ton2=='true')?'checked="true"':'').' /></div>
	                       <div class="clear"><label for="ton_bad">отрицательные</label><input id="ton_bad" type="checkbox" name="ton2" value="m" class="styled" '.(($ton3=='true')?'checked="true"':'').' /></div>
	                       <div class="clear"><label for="ton_no">не определено</label><input id="ton_no" type="checkbox" name="ton3" value="no" class="styled" '.(($ton4=='true')?'checked="true"':'').' /></div>
	                  </div>
	                  <div class="row clear"></div>
	              </div>
	              <div id="ton_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/smile.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите эмоциональную окраску мнений.">Тональность</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">'.$t_ton.'</p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>

	              <div id="deltags_popup" class="span-7 last popupfilter hide tag_popup">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6 vtip" title="Выберите теги, которые вы хотели бы отразить в выдаче.">Удалить теги</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>

	                    <div class="popupmain span-7 border_top border_bottom">
	                        <div class="row clear"></div>
	                         <div class="span-7 list"></div>
	                         <div class="row clear"></div>
	                    </div>
	                   <div class="span-7  last popupbottom">
	                       <a class=\'dottedgrey text-grey selectall\'>отметить все</a>
	                       <a class=\'dottedgrey text-grey deselectall\'>снять все</a>
	                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn del">Удалить</a>
	                            <a class="span-2 smallbtn cancel">Отменить</a>
	                       </div>
	                   </div>
	              </div>

	              <div id="addtags_popup" class="span-7 last popupfilter hide tag_popup">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Добавить тег</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>

	                    <div class="popupmain span-7 border_top border_bottom">
	                        <div class="row clear"></div>
	                         <input type="text" class="span-6">
	                         <div class="row clear"></div>
	                    </div>
	                   <div class="span-7  last popupbottom rows-2">
	                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn add">Добавить</a>
	                            <a class="span-2 smallbtn cancel">Отменить</a>
	                       </div>
	                   </div>
	              </div>

	              <div id="edittags_popup" class="span-7 last popupfilter hide tag_popup">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Изменить теги</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>

	                    <div class="popupmain span-7 border_top border_bottom">
	                        <div class="row clear"></div>
	                         <div class="span-7 list"></div>
	                    </div>
	                   <div class="span-7  last popupbottom rows-2">
	                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn save">Сохранить</a>
	                            <a class="span-2 smallbtn cancel">Отменить</a>
	                       </div>
	                   </div>
	              </div>

	              <div id="tag_full" class="filter bottomborder filter_color overflow hide">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/tags.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите теги, которые вы хотели бы отразить в выдаче.">Теги</h3>
	                  </div>
	                  <div id="tags_list">
	                       <!--<div class="clear"><label for="tagno">без тегов</label><input id="tagno" type="checkbox" name="tags" value="no" class="styled" checked="true" /></div>-->
	                  </div>
	                  <div class="row clear"></div>
	                  <div class="prepend-1 span-4 last overflow">
	                      <a id="edittag_btn" class="span-1 btntag"><img src="/img/images/filters/edit.png"/></a>
	                      <a id="addtag_btn" class="span-1 btntag"><img src="/img/images/filters/add.png"/></a>
	                      <a id="deltag_btn" class="span-1 btntag"><img src="/img/images/filters/del.png"/></a>
	                  </div>
	              </div>

	              <div id="tag_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/tags.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите теги, которые вы хотели бы отразить в выдаче.">Теги</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">тег1, тег2, тег3, тег4, тег5, без тегов</p>
	                  </div>
	                  <div class="row clear"></div>

	              </div>

	              <div id="ref_full" class="filter bottomborder filter_color overflow hide">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/reference.png"/>
	                  <h3 class="span-4 last vtip" title="Возможно выбрать упоминания без спама и рекламы.">Упоминания</h3>
	                  </div>
	                  <div>
	                      <div class="clear"><label for="refall">все</label><input id="refall" type="radio" name="ref" value="all" class="styled" '.(($_POST['ref']=='all')?'checked="true"':'').' '.((count($_POST<10))?'checked="true"':'').' /></div>
	                       <div class="clear"><label for="refselected">избранные</label><input id="refselected" type="radio" name="ref" value="1" class="styled" '.(($_POST['ref']=='1')?'checked="true"':'').' /></div>
	                       <div class="clear"><label for="refnospam">без спама</label><input id="refnospam" type="radio" name="ref" value="2" class="styled" '.(($_POST['ref']=='2')?'checked="true"':'').' /></div>
	                       <div class="clear"><label for="refonlyspam">только спам</label><input id="refonlyspam" type="radio" name="ref" value="3" class="styled" '.(($_POST['ref']=='3')?'checked="true"':'').' /></div>
	                  </div>
	                  <div class="row clear"></div>
	              </div>
	              <div id="ref_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/reference.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите регионы, из которых вы хотели бы увидеть упоминания.">Упоминания</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">'.$t_up.'</p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>

	              <div id="res_popup" class="span-7 last popupfilter hide">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Выбор ресурсов</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>
	                  <div class="expcoll clear">
	                       <a class=\'dottedgrey text-grey treeexpandall\'>развернуть всё</a>
	                       <a class=\'dottedgrey text-grey treecollapseall\'>свернуть всё</a>
	                   </div>   
	                         <ul id="res_tree" class="popuptree ">
	                            </ul>

	                   <div class="span-7 rows-2 last popupbottom">
	                       <div>
	                       <a class=\'dottedgrey text-grey treeselectall\'>отметить всё</a>
	                       <a class=\'dottedgrey text-grey treedeselectall\'>снять всё</a>
	                       </div>

	                       <div class="row clear"></div>
	                   </div>
	              </div>

	              <div id="res_full" class="filter bottomborder filter_color overflow hide">
	                  <div class="rows-2">
	                      <img class="span-1" src="/img/images/filters/res.png"/>
	                      <h3 class="span-4 last vtip" title="Выберите ресурсы, по которым хотите просмотреть выдачу.">Ресурсы</h3>
	                  </div>
	                  <div class="choose">
	                      <div class="span-3 last prepend-2">
	                      <a class="span-2 last smallbtn">Выбрать</a>
	                      </div>
	                      <p class="span-5 last bold">Вы выбрали:</p>
	                      <div class="span-4 last prepend-1">
	                      </div>
	                  </div>
	                  <div class="row clear"></div>
	                  </div>

	              <div id="res_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/res.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите ресурсы, по которым хотите просмотреть выдачу.">Ресурсы</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">
	                      </p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>

	              <div id="cities_popup" class="span-7 last popupfilter hide">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Выбор городов</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>
	                  <div class="expcoll clear">
	                       <a class=\'dottedgrey text-grey treeexpandall\'>развернуть всё</a>
	                       <a class=\'dottedgrey text-grey treecollapseall\'>свернуть всё</a>
	<!--                       <div class="row clear"></div>-->
	                   </div>   
	                         <ul id="cities_tree" class="popuptree ">
	                            </ul>

	                   <div class="span-7 rows-2 last popupbottom">
	                       <div>
	                       <a class=\'dottedgrey text-grey treeselectall\'>отметить всё</a>
	                       <a class=\'dottedgrey text-grey treedeselectall\'>снять всё</a>
	                       </div>

	                       <div class="row clear"></div>
	<!--                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn hide">Выбрать</a>
	                            <a class="span-2 smallbtn hide">Отменить</a>
	                       </div>-->
	                   </div>
	              </div>

	              <div id="cities_full" class="filter bottomborder filter_color overflow hide">
	                  <div class="rows-2">
	                      <img class="span-1" src="/img/images/filters/cities.png"/>
	                      <h3 class="span-4 last vtip" title="Выберите регионы, из которых вы хотели бы увидеть упоминания.">Города</h3>
	                  </div>
	                  <div class="choose">
	                      <div class="span-3 last prepend-2">
	                      <a class="span-2 last smallbtn">Выбрать</a>
	                      </div>

	                      <p class="span-5 last bold">Вы выбрали:</p>
	                      <div class="span-4 last prepend-1">
	                      </div>
	                  </div>
	                  <div class="row clear"></div>
	              </div>
	              <div id="cities_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/cities.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите регионы, из которых вы хотели бы увидеть упоминания.">Города</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">
	                          не определено, Белоруссия, Украина, Уральский регион, Сибирь, Москва
	                      </p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>

	               <div id="speakers_popup" class="span-7 last popupfilter hide">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Выбор спикеров</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>

	                    <div class="popupmain span-7 border_top border_bottom">
	                      <div class="span-3">
	                        <p  class="top bottom vert-center rows-2">
	                          Сортировать по:
	                        </p>
	                      </div>
	                      <div class="span-4 last customselect">
	                          <select class="styled smallselect sort" name="speakers"/>
	                            <option>рейтингу</option>
	                            <option>алфавиту</option>
	                         </select>
	                      </div>
	                        <div id=\'popup_speakers_list\' class="scroll span-7"></div>

	                    </div>
	                   <div class="span-7  last popupbottom rows-2">
	                       <a class=\'dottedgrey text-grey selectall\'>отметить все</a>
	                       <a class=\'dottedgrey text-grey deselectall\'>снять все</a>
	<!--                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn hide">Выбрать</a>
	                            <a class="span-2 smallbtn hide">Отменить</a>
	                       </div>-->
	                   </div>
	              </div>

	              <div id="speakers_full" class="filter bottomborder filter_color overflow hide">
	                  <div class="rows-2">
	                      <img class="span-1" src="/img/images/filters/speakers.png"/>
	                      <h3 class="span-4 last vtip" title="Выберите людей, чаще всего упомянувших ваш запрос.">Спикеры</h3>
	                  </div>
	                  <div>
	                       <div class="clear"><label for="speakersall">все</label><input id="speakersall" type="radio" name="speakers" value="all" class="styled" '.(($_POST['speakers']=='all')?'checked="true"':'').(count($_POST<10)?'checked="true"':'').'/></div>
	                       <div class="clear"><label for="speakersselected">только выбранные</label><input id="speakersselected" type="radio" name="speakers" value="selected" class="styled" '.(($_POST['speakers']=='selected')?'checked="true"':'').'/></div>
	                       <div class="clear"><label for="speakersexcept">все, кроме выбранных</label><input id="speakersexcept" type="radio" name="speakers" value="except" class="styled" '.(($_POST['speakers']=='except')?'checked="true"':'').'/></div>
	                  </div>
	                  <div class="row clear"></div>
	<!--                  <div class="span-3 last prepend-2">
	                  <a class="span-2 last smallbtn disabled">Выбрать</a>
	                  </div>-->
	<!--                  <div id=\'speakers_choose\' class=\'hide\'>
	                      <p class="span-5 last bold">Вы выбрали:</p>
	                      <div class="span-4 last prepend-1">
	                          <p>спикер1 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>спикер2 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>спикер3 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>спикер4 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>спикер5 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>спикер6 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                      </div>
	                  </div>-->
	<!--                  <div class="row clear"></div>-->
	              </div>



	              <div id="speakers_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/speakers.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите людей, чаще всего упомянувших ваш запрос.">Спикеры</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">
						                          ';
						if ($_POST['nnick']=='')
						{
							if (count($_POST)>10)
							{
								//echo 'gggggg';
								//print_r($speak);
								if ($_POST['speakers']!='all')
								{
									if ($_POST['speakers']=='selected')
									{
										$zap='';
										foreach ($speak as $key => $item)
										{
											$posss=array_search($item,$metrics['speakers']['nick']);
											if ($metrics['speakers']['rnick'][$posss]=='')
											{
												$html_out.=$zap.$item;
												$zap=', ';					
											}
											else
											{
												$html_out.=$zap.$metrics['speakers']['rnick'][$posss];
												$zap=', ';					
											}
										}
									}
									else
									if ($_POST['speakers']=='except')
									{
										$html_out.='<span class="bold">все, кроме: </span>';
										$zap='';
										foreach ($speak as $key => $item)
										{
											$html_out.=$zap.$item;
											$zap=', ';					
										}
									}
								}
							}
							else
							{
								$html_out.='все';
							}
						}
						else
						{
							if (in_array($_POST['nnick'],$metrics['speakers']['nick']))
							{
								$posss=array_search($_POST['nnick'],$metrics['speakers']['nick']);
								if ($metrics['speakers']['rnick'][$posss]=='')
								{
									$html_out.=$_POST['nnick'];
								}
								else
								{
									$html_out.=$metrics['speakers']['rnick'][$posss];
								}
							}
						}
						$html_out.='
	                      </p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>

	              <div id="promouters_popup" class="span-7 last popupfilter hide">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Выбор промоутеров</h4>
	                   	   <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>

	                    <div class="popupmain span-7 border_top border_bottom">
	                      <div class="span-3">
	                        <p  class="top bottom vert-center rows-2">
	                          Сортировать по:
	                        </p>
	                      </div>
	                      <div class="span-4 last customselect">
	                          <select class="styled smallselect sort" name="promouters"/>
	                            <option>рейтингу</option>
	                            <option>алфавиту</option>
	                         </select>
	                      </div>
	                        <div id=\'popup_promouters_list\' class="scroll span-7"></div>

	                    </div>
	                   <div class="span-7  last popupbottom rows-2">
	                       <a class=\'dottedgrey text-grey selectall\'>отметить все</a>
	                       <a class=\'dottedgrey text-grey deselectall\'>снять все</a>
	<!--                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn hide">Выбрать</a>
	                            <a class="span-2 smallbtn hide">Отменить</a>
	                       </div>-->
	                   </div>
	              </div>

	              <div id="promouters_full" class="filter bottomborder filter_color overflow hide">
	                  <div class="rows-2">
	                      <img class="span-1" src="/img/images/filters/promouters.png"/>
	                      <h3 class="span-4 last vtip" title="Выберите людей, упомянувших ваш бренд и имеющих больше всего друзей.">Промоутеры</h3>
	                  </div>
	                  <div>
	                       <div class="clear"><label for="promoutersall">все</label><input id="promoutersall" type="radio" name="promouters" value="all" class="styled" '.(($_POST['promouters']=='all')?'checked="true"':'').(count($_POST<10)?'checked="true"':'').'/></div>
	                       <div class="clear"><label for="promoutersselected">только выбранные</label><input id="promoutersselected" type="radio" name="promouters" value="selected" class="styled" '.(($_POST['promouters']=='selected')?'checked="true"':'').'/></div>
	                       <div class="clear"><label for="promoutersexcept">все, кроме выбранных</label><input id="promoutersexcept" type="radio" name="promouters" value="except" class="styled" '.(($_POST['promouters']=='except')?'checked="true"':'').'/></div>
	                  </div>
	                  <div class="row clear"></div>
	<!--                  <div class="span-3 last prepend-2">
	                  <a class="span-2 last smallbtn disabled">Выбрать</a>
	                  </div>-->
	<!--                  <div id=\'promouters_choose\' class=\'hide\'>
	                      <p class="span-5 last bold">Вы выбрали:</p>
	                      <div class="span-4 last prepend-1">
	                          <p>промоутер1 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>промоутер2 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>промоутер3 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                          <p>промоутер4 <a><img class="cross_del" src="/img/images/filters/cross.png"/></a></p>
	                      </div>
	                  </div>-->
	<!--                  <div class="row clear"></div>-->
	              </div>
	              <div id="promouters_wrapped" class="filter bottomborder wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/promouters.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите людей, упомянувших ваш бренд и имеющих больше всего друзей.">Промоутеры</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">
	                          ';
	if ($_POST['nnick']=='')
	{
		if (count($_POST)>10)
		{
			//echo 'gggggg';
			//print_r($speak);
			if ($_POST['promouters']!='all')
			{
				if ($_POST['promouters']=='selected')
				{
					$zap='';
					foreach ($prom as $key => $item)
					{
						$html_out.=$zap.$item;
						$zap=', ';					
					}
				}
				else
				if ($_POST['promouters']=='except')
				{
					$html_out.='<span class="bold">все, кроме: </span>';
					$zap='';
					foreach ($prom as $key => $item)
					{
						$html_out.=$zap.$item;
						$zap=', ';					
					}
				}
			}
		}
		else
		{
			$html_out.='все';
		}
	}
	else
	{
		if (in_array($_POST['nnick'],$metrics['promotion']['nick']))
		{
			$html_out.=$_POST['nnick'];
		}
	}
	$html_out.='
	                      </p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>


	              <div id="words_popup" class="span-7 last popupfilter hide">
	                   <div class="rows-2 span-7 last">
	                       <h4 class="popupname span-6">Добавить слово</h4>
	                       <a><img class="right close" src="/img/images/filters/close.png"/></a>
	                   </div>

	                    <div class="popupmain span-7 border_top border_bottom">
	                        <div class="row clear"></div>
	                         <input type="text" class="span-6">
	                         <div class="row clear"></div>
	                    </div>
	                   <div class="span-7  last popupbottom rows-2">
	                       <div class=\'rows-2 vert-center prepend-3\'>
	                            <a class="span-2 smallbtn add">Добавить</a>
	                            <a class="span-2 smallbtn cancel">Отменить</a>
	                       </div>
	                   </div>
	              </div>

	              <div id="words_full" class="filter filter_color overflow hide">
	                  <div class="rows-2">
	                      <img class="span-1" src="/img/images/filters/words.png"/>
	                      <h3 class="span-4 last vtip" title="Выберите слова, которые вы хотели бы увидеть в выдаче.">Со словами</h3>
	                  </div>
	                  <div>
	                       <div class="clear"><label for="wordsall">все</label><input id="wordsall" type="radio" name="words_rb" value="all" '.(($_POST['words_rb']=='all')?'checked="true"':'').(count($_POST<10)?'checked="true"':'').' class="styled"/></div>
	                       <div class="clear"><label for="wordsselected">только выбранные</label><input id="wordsselected" type="radio" name="words_rb" value="selected"  class="styled" '.(($_POST['words_rb']=='selected')?'checked="true"':'').'/></div>
	                       <div class="clear"><label for="wordsexcept">все, кроме выбранных</label><input id="wordsexcept" type="radio" name="words_rb" value="except" class="styled" '.(($_POST['words_rb']=='except')?'checked="true"':'').'/></div>
	                  </div>
	                  <div class="row clear"></div>
	                  <div id="words_choose" class="hide">
	                      <div id="words_list"></div>
	                       <div class="row clear"></div>
	                  <div class="span-4 last prepend-1">
	                  <a id="add_word_btn" class="span-3 last smallbtn">Добвить слово</a>
	                  <div class="row clear"></div>
	                  </div>
	                  </div>
	              </div>
	              <div id="words_wrapped" class="filter wrappedfilter">
	                  <div class="rows-2">
	                  <img class="span-1" src="/img/images/filters/words.png"/>
	                  <h3 class="span-4 last vtip" title="Выберите слова, которые вы хотели бы увидеть в выдаче.">Со словами</h3>
	                  </div>
	                  <div class="comment text-lightgrey">
	                      <p class="span-5 last">
						                          ';
						if ($_POST['nword']=='')
						{
							if (count($_POST)>10)
							{
								//echo 'gggggg';
								//print_r($speak);
								if ($_POST['words_rb']!='all')
								{
									if ($_POST['words_rb']=='selected')
									{
										$zap='';
										foreach ($kww as $key => $item)
										{
											if ($item!='rb')
											{
												$html_out.=$zap.$item;
												$zap=', ';					
											}
										}
									}
									else
									if ($_POST['words_rb']=='except')
									{
										$html_out.='<span class="bold">все, кроме: </span>';
										$zap='';
										foreach ($kww as $key => $item)
										{
											if ($item!='rb')
											{
												$html_out.=$zap.$item;
												$zap=', ';					
											}
										}
									}
								}
							}
							else
							{
								$html_out.='все';
							}
						}
						else
						{
							$html_out.=preg_replace('/\s*/is','',mb_strtolower($_POST['nword'],'UTF-8'));
						}
						$html_out.='
	                      </p>
	                  </div>
	                  <div class="row clear"></div>
	              </div>
	          </div>





	          <div class="span-19 last" id="content">
	              <div class="row clear"></div>
	              <div class="rows-2 clear">
	                  <div class="span-4 ">
	                          <a class="greenbtn span-4 last" onclick="document.getElementById(\'filterform\').submit();">Показать</a>
	                  </div>
	                  <div class="span-2 prepend-4">
	                      <p  class="top bottom text-18 vert-center rows-2">
	                          Формат:
	                        </p>
	                  </div>
	                  <div class="span-4 customselect">
	                      <select class="styled" name="format"/>
	                        <option>Excel</option>
	                        <option>Word</option>
	                        <option>OpenOffice</option>
	                     </select>
	                  </div>
	                  <div class="span-5 last">
	                          <a href="#" class="greybtn span-5 last" onclick="document.getElementById(\'filterform\').target=\'_blank\'; document.getElementById(\'filterform\').action=\'/new/newexport\'; document.getElementById(\'filterform\').submit(); document.getElementById(\'filterform\').target=\'\'; document.getElementById(\'filterform\').action=\'/new/comment\';">Сформировать отчёт</a>
	                  </div>
	              </div>
	              <div class="row clear"></div>
	              <p class="top bottom text-18">
	                  Найдено <span class="bold">'.(($cnt=='')?$_SESSION['count_'.$_POST['hashq']]:$cnt).'</span> упоминаний на <span class="bold">'.$_SESSION['counth_'.$_POST['hashq']].'</span> ресурсах
	              </p>

				</form>

	<!--              листатель страниц-->
	              <div class="pages">
	                  <div class="span-1 arrows">
	                         <a><img src="/img/images/arrowleft.png"/></a> 
	                  </div>
	                  <div class="span-17 nodecoration text-black text-center">
	';
	$count_page=((isset($_SESSION['count_'.$_POST['hashq']]))?intval($_SESSION['count_'.$_POST['hashq']]/10)+1:intval($cnt/10)+1);
	//echo "<br>".$count_page;
	//echo $cnt;
	//echo ($_SESSION['count_'.$_POST['hashq']] % 10);
	if (isset($_SESSION['count_'.$_POST['hashq']]))
	{
		if (($_SESSION['count_'.$_POST['hashq']] % 10)==0)
		{
			$count_page-=1;
		}
	}
	else
	{
		if (($cnt % 10)==0)
		{
			$count_page-=1;
		}
	}
	if ($count_page<8)
	{
		//echo 'ZAOP';
		if ($_POST['str']!='')
		{
			for ($i=0;$i<intval($count_page);$i++)
			{
				if (($i+1)==$_POST['str'])
				{
					$html_out.='<a class="span-1 highlightpage">'.($i+1).'</a>
						';
				}
				else
				{
					$html_out.='<a class="span-1" onclick="document.getElementById(\'str\').value='.($i+1).'; document.getElementById(\'filterform\').submit()">'.($i+1).'</a>
						';
				}
			}
		}
		else
		{
			$html_out.='<a class="span-1 highlightpage">1</a>
				';
				//echo $count_page/10;
			for ($i=1;$i<intval($count_page);$i++)
			{
				$html_out.='<a class="span-1" onclick="document.getElementById(\'str\').value='.($i+1).'; document.getElementById(\'filterform\').submit()">'.($i+1).'</a>
						';
			}
		}
	}
	else
	{
		if ($_POST['str']!='')
		{
			if ($_POST['str']<6)
			{
				$html_out.='<a class="span-1 '.(($_POST['str']==1)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
							<a class="span-1 '.(($_POST['str']==2)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
							<a class="span-1 '.(($_POST['str']==3)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
							<a class="span-1 '.(($_POST['str']==4)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
							<a class="span-1 '.(($_POST['str']==5)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=5; document.getElementById(\'filterform\').submit()">5</a>
							<a class="span-1 '.(($_POST['str']==6)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=6; document.getElementById(\'filterform\').submit()">6</a>
	                      	<div class="span-6 dotted"></div>
							<a class="span-1" '.(($_POST['str']==($count_page-4))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-4).'; document.getElementById(\'filterform\').submit()">'.($count_page-4).'</a>
							<a class="span-1" '.(($_POST['str']==($count_page-3))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
							<a class="span-1" '.(($_POST['str']==($count_page-2))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
							<a class="span-1" '.(($_POST['str']==($count_page-1))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
							<a class="span-1 last '.(($_POST['str']==($count_page))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
					';
			}
			else
			if (($_POST['str']>=6) && ($_POST['str']<=($count_page-4)))
			{
				$html_out.='
				<a class="span-1" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
				<a class="span-1" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
                <div class="span-2 dotted"></div>
				';
				for ($i=$_POST['str']-2;$i<=($_POST['str']+2);$i++)
				{
					$html_out.='<a class="span-1 '.(($_POST['str']==$i)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.$i.'; document.getElementById(\'filterform\').submit()">'.$i.'</a>
	                ';
				}
				$html_out.='
                <div class="span-2 dotted"></div>
                <a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
                <a class="span-1 last" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
                ';
			}
			else
			{
				//echo $_POST['str'].' '.($count_page-1).' '.(($_POST['str']==($count_page-2))?'highlightpage':'');
				$html_out.='<a class="span-1 '.(($_POST['str']==1)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
							<a class="span-1 '.(($_POST['str']==2)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
							<a class="span-1 '.(($_POST['str']==3)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
							<a class="span-1 '.(($_POST['str']==4)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
							<a class="span-1 '.(($_POST['str']==5)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=5; document.getElementById(\'filterform\').submit()">5</a>
							<a class="span-1 '.(($_POST['str']==6)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=6; document.getElementById(\'filterform\').submit()">6</a>
	                      	<div class="span-6 dotted"></div>
							<a class="span-1 '.(($_POST['str']==($count_page-4))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-4).'; document.getElementById(\'filterform\').submit()">'.($count_page-4).'</a>
							<a class="span-1 '.(($_POST['str']==($count_page-3))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
							<a class="span-1 '.(($_POST['str']==($count_page-2))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
							<a class="span-1 '.(($_POST['str']==($count_page-1))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
							<a class="span-1 last '.(($_POST['str']==($count_page))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
					';
			}
		}
		else
		{
			$html_out.='<a class="span-1 highlightpage" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=5; document.getElementById(\'filterform\').submit()">5</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=6; document.getElementById(\'filterform\').submit()">6</a>
                      	<div class="span-6 dotted"></div>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-4).'; document.getElementById(\'filterform\').submit()">'.($count_page-4).'</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
						<a class="span-1 last" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
				';
		}
	}
	$html_out.='
	                      <!--<a class="span-1 highlightpage">1</a>
	                      <a class="span-1">2</a>
	                      <a class="span-1">3</a>
	                      <a class="span-1">4</a>
	                      <a class="span-1">5</a>
	                      <a class="span-1">6</a>
	                      <a class="span-1">7</a>
	                      <a class="span-1">8</a>
	                      <div class="span-6 dotted"></div>
	                      <a class="span-1">146</a>
	                      <a class="span-1">147</a>
	                      <a class="span-1 last">148</a>-->
	                  </div>
	                  <div class="span-1 last arrows">
	                      <a><img src="/img/images/arrowright.png"/></a> 
	                  </div>
	              </div>

				';
				//print_r($outcash);
				foreach($outcash['link'] as $key => $item)
				{
					$id=$outcash['id'][$key];
					$blogin=$outcash['blogin'][$key];
					$nick=$outcash['nick'][$key];
					//echo $nick.' ';
					$content=$outcash['content'][$key];
					//$content=explode("\n",$outcash['content'][$key]);
					$blogid=$outcash['blogid'][$key];
					$hn=parse_url($item);
				    $hn=$hn['host'];
				    $ahn=explode('.',$hn);
				    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
					$hh = $ahn[count($ahn)-2];
					$time=$outcash['time'][$key];
					if ((intval(date('H',$time))>0)||(intval(date('i',$time))>0)) $stime=date("H:i:s d.m.Y",$time);
					else $stime=date("d.m.Y",$time);
					$text_age='';
					if ($outcash['age'][$key]!=0)
					{
						if ((($outcash['age'][$key]>=10) && ($outcash['age'][$key]<=20)) || ((($outcash['age'][$key] % 10)>=5) && (($outcash['age'][$key] % 10)<=9)))
						{
							$text_age='('.$outcash['age'][$key].' лет)';
						}
						else
						if ((($outcash['age'][$key] % 10)>1) && (($outcash['age'][$key] % 10)<5))
						{
							$text_age='('.$outcash['age'][$key].' года)';
						}
						else
						{
							$text_age='('.$outcash['age'][$key].' год)';
						}
					}
					if ($outcash['gender'][$key]!=0)
					{
						if ($outcash['gender'][$key]==1)
						{
							$text_gen='/img/images/post_icons/female-icon.jpg';
						}
						else
						{
							$text_gen='/img/images/post_icons/Male-icon.jpg';
						}
					}
					else
					//if ($outcash['age'][$key]!=0)
					{
						$text_gen='/img/images/post_icons/sex-icon.jpg';
					}
					$tagv='';
					foreach ($outcash['tag'][$key] as $key1 => $item1)
					{
						if ($key1>0) $tagv.=',';
						$tagv.=' '.$tagsall[$item1];
					}
					$tnick='';
					switch ($hn) {
					    case 'livejournal.com':
					        $tnick='<a href="http://'.$nick.'.livejournal.com/" target="_blank" class="bold vtip" title="Спикер">'.$nick.'</a>';
					        break;
					    case 'twitter.com':
				        	$tnick='<a href="http://twitter.com/'.$blogin.'" target="_blank" class="bold vtip" title="Спикер">'.$blogin.'</a>';
							$nick=$blogin;
					        break;
					    case 'vkontakte.ru':
							$nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$nick);
				    		$tnick='<a href="http://vkontakte.ru/id'.$blogin.'" target="_blank" class="bold vtip" title="Спикер">'.$nick.'</a>';
					        break;
					    case 'vk.com':
							$nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$nick);
				    		$tnick='<a href="http://vk.com/id'.$blogin.'" target="_blank" class="bold vtip" title="Спикер">'.$nick.'</a>';
					        break;
					    case 'facebook.com':
				    		$tnick='<a href="http://facebook.com/'.$blogin.'" target="_blank" class="bold vtip" title="Спикер">'.$nick.'</a>';
					        break;
						default:
							$nick='other';
							break;
					}
					//echo $tnick.'<br>';
					$html_out.='<!--              ПОСТ!!!-->
					<div class="post">
					                  <div class="span-1">
					                      <p class="top bottom text-18 text-right">'.(intval((($_POST['str']=='')?0:$_POST['str']-1)*10)+$key+1).'</p>
					                  </div>
					                  <div class="span-18 last">
					                      <div class="top-post">
					                          <div class="span-15">
					                              <div class="span-1 last">
					                                  <img class="resicon" src="../img/social/'.(file_exists('../img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'"/>
					                              </div>
					                              <div class="span-14 last">
					                                  <a class="top bottom text-18" href="'.preg_replace('/\s/is','',$item).'" id="post_'.$id.'" target="_blank">
					                                      '.((preg_replace('/\s+/is',' ',strip_tags($content))=='')?preg_replace('/\s/is','',$item):preg_replace('/\s+/is',' ',strip_tags($content))).'
					                                  </a>
					                              </div>
					                              <div class="span-15 last">
					                                  <p class="top bottom" id="ful_post_'.$outcash['id'][$key].'">
					                                      <!--'.preg_replace('/\s+/is',' ',strip_tags($content)).'-->
														</span>
					                                  </p>
					                                  <div class="span-15 last">
					                                      '.(($order['ful_com']==1)?'<div class="span-3">
					                                          <a class="top bottom comment text-lightgrey dottedlightgrey" onclick="$.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&keyword='.urlencode(($order['order_keyword']!='')?$order['order_keyword']:$order['order_name']).'&post_id='.intval($outcash['id'][$key]).'&ful_post=1\', success: function(msg1){ /*alert(msg1);*/ if (msg1!=\'\') { $(\'#ful_post_'.$outcash['id'][$key].'\').html(msg1); } else { $(\'#ful_post_'.$outcash['id'][$key].'\').html($(\'#post_'.$id.'\').text()); } } });">
					                                          Подробнее
					                                          </a>
					                                      </div>':'').'

					                                      <p class="top bottom comment text-grey text-right">
					                                          '.$stime.'
					                                      </p>
					                                  </div> 
					                              </div>
					                          </div>



					    <!--                      правая часть-->
					                          <div class="span-3 last">
					                              <div class="span-1">
					                                  <img class="abouticon center" src="/img/images/post_icons/comment.png"/>
					                                  <p class="top bottom bold text-darkgrey text-center vtip" title="Характеризует популярность упоминания и частоту его цитирования.">'.(($order['order_engage']==1)?$outcash['eng'][$key]:'-').'</p>
					                              </div>
					                              <div class="span-1">
					                                  <img class="abouticon center" src="/img/images/post_icons/like.png"/>
					                                  <p class="top bottom bold text-darkgrey text-center vtip" title="Характеризует популярность упоминания и частоту его цитирования.">'.(($order['order_engage']==1)?$outcash['eng'][$key]:'-').'</p>
					                              </div>
					                              <div class="span-1 last">
					                                  <img class="abouticon center" src="/img/images/post_icons/audience.png"/>
					                                  <p class="top bottom bold text-darkgrey text-center">'.formatint($outcash['readers'][$key]).'</p>
					                              </div>
					                              <div class="span-3 last">

					                                      <div class="tonality left">
					                                          <a id="ton_pos'.$outcash['id'][$key].'" onclick="var tag=$(\'#po_'.$outcash['id'][$key].'\').attr(\'class\'); $.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&post_id='.intval($outcash['id'][$key]).'&tag=\'+tag, success: function(msg1){  } }); return false;"><img id="po_'.$outcash['id'][$key].'" class="tonalicon goodton '.(($outcash['nastr'][$key]==1)?'bright':'').'" src="/img/images/post_icons/good'.(($outcash['nastr'][$key]==1)?'':'_bw').'.jpg"/></a>
					                                          <a id="ton_neu'.$outcash['id'][$key].'" onclick="var tag=$(\'#ng_'.$outcash['id'][$key].'\').attr(\'class\'); $.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&post_id='.intval($outcash['id'][$key]).'&tag=\'+tag, success: function(msg1){  } }); return false;"><img id="ng_'.$outcash['id'][$key].'" class="tonalicon neuton '.(($outcash['nastr'][$key]==0)?'bright':'').'" src="/img/images/post_icons/nutral'.(($outcash['nastr'][$key]==0)?'':'_bw').'.png"/></a>
					                                          <a id="ton_neg'.$outcash['id'][$key].'" onclick="var tag=$(\'#ne_'.$outcash['id'][$key].'\').attr(\'class\'); $.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&post_id='.intval($outcash['id'][$key]).'&tag=\'+tag, success: function(msg1){  } }); return false;"><img id="ne_'.$outcash['id'][$key].'" class="tonalicon badton '.(($outcash['nastr'][$key]==-1)?'bright':'').'" src="/img/images/post_icons/bad'.(($outcash['nastr'][$key]==-1)?'':'_bw').'.jpg"/></a>
					                                      </div>

					                                  <a onclick="var tag=$(\'#fav_'.$outcash['id'][$key].'\').attr(\'class\'); $.ajax({ type: \'POST\', url: \'/new/ajax2\', data: \'order_id='.intval($_POST['order_id']).'&post_id='.intval($outcash['id'][$key]).'&ton=\'+tag, success: function(msg1){ } }); return false;"><img id="fav_'.$outcash['id'][$key].'" class="selectedicon '.(($outcash['isfav'][$key]==1)?'bright':'').'" src="/img/images/post_icons/star'.(($outcash['isfav'][$key]==1)?'':'_bw').'.png"/></a>
					                                  <a onclick="esp='.intval($outcash['id'][$key]).'; author=\''.$blogid.'\'; hhn=\''.$hn.'\'; chesp=$(\'#spamm_'.intval($outcash['id'][$key]).'\').attr(\'class\');"><img id="spamm_'.intval($outcash['id'][$key]).'" class="spam '.(($outcash['isspam'][$key]==1)?'bright':'').'" src="/img/images/post_icons/bin'.(($outcash['isspam'][$key]==1)?'':'_bw').'.png"/></a>
					                              </div>
					                          </div>
					                      </div>
					                      <div class="sign_post text-grey">
					                          <div class="span-5">
					                              <img class="gendericon" src="'.$text_gen.'"/>
					                              '.$tnick.'  '.$text_age.'
					                          </div>
					                          <div class="span-4">
					                              '.((isset($wobot['destn1'][$outcash['loc'][$key]]))?$wobot['destn3'][$wobot['destn1'][$outcash['loc'][$key]]].': '.$wobot['destn1'][$outcash['loc'][$key]]:'Неопределено').'
					                          </div>
					                          <div class="span-6 comment text-right assigned_tags">
					                              '.(($tagv==' ')?'не выбраны':$tagv).'
					                          </div>
					                          <div class="span-3 last">
					                              <a class="top bottom comment dottedgrey assigntags" onclick="ppid='.intval($outcash['id'][$key]).'">
					                                  Назначить теги
					                              </a>
					                          </div>
					                      </div>
					                  </div>
					              </div>';
				}
				
				$html_out.='



	<!--            листатель страниц-->
	           <div class="pages">
	                  <div class="span-1 arrows">
	                         <a><img src="/img/images/arrowleft.png"/></a> 
	                  </div>
	                  <div class="span-17 nodecoration text-black text-center">
	';
	if ($count_page<8)
	{
		//echo 'ZAOP';
		if ($_POST['str']!='')
		{
			for ($i=0;$i<intval($count_page);$i++)
			{
				if (($i+1)==$_POST['str'])
				{
					$html_out.='<a class="span-1 highlightpage">'.($i+1).'</a>
						';
				}
				else
				{
					$html_out.='<a class="span-1" onclick="document.getElementById(\'str\').value='.($i+1).'; document.getElementById(\'filterform\').submit()">'.($i+1).'</a>
						';
				}
			}
		}
		else
		{
			$html_out.='<a class="span-1 highlightpage">1</a>
				';
				//echo $count_page/10;
			for ($i=1;$i<intval($count_page);$i++)
			{
				$html_out.='<a class="span-1" onclick="document.getElementById(\'str\').value='.($i+1).'; document.getElementById(\'filterform\').submit()">'.($i+1).'</a>
						';
			}
		}
	}
	else
	{
		if ($_POST['str']!='')
		{
			if ($_POST['str']<6)
			{
				$html_out.='<a class="span-1 '.(($_POST['str']==1)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
							<a class="span-1 '.(($_POST['str']==2)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
							<a class="span-1 '.(($_POST['str']==3)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
							<a class="span-1 '.(($_POST['str']==4)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
							<a class="span-1 '.(($_POST['str']==5)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=5; document.getElementById(\'filterform\').submit()">5</a>
							<a class="span-1 '.(($_POST['str']==6)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=6; document.getElementById(\'filterform\').submit()">6</a>
	                      	<div class="span-6 dotted"></div>
							<a class="span-1" '.(($_POST['str']==($count_page-4))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-4).'; document.getElementById(\'filterform\').submit()">'.($count_page-4).'</a>
							<a class="span-1" '.(($_POST['str']==($count_page-3))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
							<a class="span-1" '.(($_POST['str']==($count_page-2))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
							<a class="span-1" '.(($_POST['str']==($count_page-1))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
							<a class="span-1 last '.(($_POST['str']==($count_page))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
					';
			}
			else
			if (($_POST['str']>=6) && ($_POST['str']<=($count_page-4)))
			{
				$html_out.='
				<a class="span-1" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
				<a class="span-1" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
                <div class="span-2 dotted"></div>
				';
				for ($i=$_POST['str']-2;$i<=($_POST['str']+2);$i++)
				{
					$html_out.='<a class="span-1 '.(($_POST['str']==$i)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.$i.'; document.getElementById(\'filterform\').submit()">'.$i.'</a>
	                ';
				}
				$html_out.='
                <div class="span-2 dotted"></div>
                <a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
                <a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
                <a class="span-1 last" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
                ';
			}
			else
			{
				//echo $_POST['str'].' '.($count_page-1).' '.(($_POST['str']==($count_page-2))?'highlightpage':'');
				$html_out.='<a class="span-1 '.(($_POST['str']==1)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
							<a class="span-1 '.(($_POST['str']==2)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
							<a class="span-1 '.(($_POST['str']==3)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
							<a class="span-1 '.(($_POST['str']==4)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
							<a class="span-1 '.(($_POST['str']==5)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=5; document.getElementById(\'filterform\').submit()">5</a>
							<a class="span-1 '.(($_POST['str']==6)?'highlightpage':'').'" onclick="document.getElementById(\'str\').value=6; document.getElementById(\'filterform\').submit()">6</a>
	                      	<div class="span-6 dotted"></div>
							<a class="span-1 '.(($_POST['str']==($count_page-4))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-4).'; document.getElementById(\'filterform\').submit()">'.($count_page-4).'</a>
							<a class="span-1 '.(($_POST['str']==($count_page-3))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
							<a class="span-1 '.(($_POST['str']==($count_page-2))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
							<a class="span-1 '.(($_POST['str']==($count_page-1))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
							<a class="span-1 last '.(($_POST['str']==($count_page))?'highlightpage':'').'" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
					';
			}
		}
		else
		{
			$html_out.='<a class="span-1 highlightpage" onclick="document.getElementById(\'str\').value=1; document.getElementById(\'filterform\').submit()">1</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=2; document.getElementById(\'filterform\').submit()">2</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=3; document.getElementById(\'filterform\').submit()">3</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=4; document.getElementById(\'filterform\').submit()">4</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=5; document.getElementById(\'filterform\').submit()">5</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value=6; document.getElementById(\'filterform\').submit()">6</a>
                      	<div class="span-6 dotted"></div>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-4).'; document.getElementById(\'filterform\').submit()">'.($count_page-4).'</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-3).'; document.getElementById(\'filterform\').submit()">'.($count_page-3).'</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-2).'; document.getElementById(\'filterform\').submit()">'.($count_page-2).'</a>
						<a class="span-1" onclick="document.getElementById(\'str\').value='.($count_page-1).'; document.getElementById(\'filterform\').submit()">'.($count_page-1).'</a>
						<a class="span-1 last" onclick="document.getElementById(\'str\').value='.($count_page).'; document.getElementById(\'filterform\').submit()">'.($count_page).'</a>
				';
		}
	}
	$html_out.='
	                      <!--<a class="span-1">1</a>
	                      <a class="span-1">2</a>
	                      <a class="span-1">3</a>
	                      <a class="span-1 highlightpage">4</a>
	                      <a class="span-1">5</a>
	                      <a class="span-1">6</a>
	                      <a class="span-1">7</a>
	                      <a class="span-1">8</a>
	                      <div class="span-6 dotted"></div>
	                      <a class="span-1">146</a>
	                      <a class="span-1">147</a>
	                      <a class="span-1 last">148</a>-->
	                  </div>
	                  <div class="span-1 last arrows">
	                      <a><img src="/img/images/arrowright.png"/></a> 
	                  </div>
	              </div> 


	          </div>

';


stop_tpl();


?>
