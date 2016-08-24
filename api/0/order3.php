<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

//auth();
//if (!$loged) die();
$k=0;
$kk=0;
$kkk=0;
$kkkk=0;
//$user['user_id']=61;
auth();
//echo $loged;
if (!$loged) die();
if (!isset($_GET['order_id']))
{
	$_GET=$_POST;
}
//print_r($_GET);
$c=0;
$out['order_prev']="";
$out['order_next']="";
if ($user['tariff_id']!=3)
{
	$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($user['user_id']));
}
else
{
	$res=$db->query('SELECT * FROM blog_orders WHERE user_id=61');
	$user['user_id']=61;
}
while ($row = $db->fetch($res))
{
	$orders[]=$row['order_id'];
	if ($row['order_id']==$_GET['order_id'])
	{
		//$out['order_next']=$db->fetch($res);
		$out['order_prev']=$orders[count($orders)-2];
		$c=1;
	}
	if ($c==2)
	{
		$out['order_next']=$orders[count($orders)-1];
		break;
	}
	if ($c==1)
	{
		$c++;
	}
} 


if ($out['order_prev']=="")
{
	$out['order_prev']=intval($_GET['order_id']);
}
if ($out['order_next']=="")
{
	$out['order_next']=intval($_GET['order_id']);
}

$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']));
while ($row = $db->fetch($res)) 
{
	$out['promotions']=array();
	$out['speakers']=array();
	$out['city']=array();
	//$out['city']=array();
	//$out['city'][0]['count']=0;
	if ($row['order_end']>mktime(0,0,0,date('n'),date('j'),date('Y')))
	{
		$row['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
	}
	/*if ($user['user_id']==189)
	{
		$m_dinams=json_decode(stripslashes($row['order_beta']),true);
	}
	else
	{
		$m_dinams=json_decode($row['order_beta'],true);
	}*/
	$m_dinams=json_decode($row['order_beta'],true);
	$out['order_name']=($row['order_name']=='')?$row['order_keyword']:$row['order_name'];
	$metrics=json_decode(($row['order_metrics']),true);
	$mas_res=json_decode(($row['order_src']),true);
	$res_count=count($mas_res);
	$coll=0;
	$ll=0;
	arsort($metrics['value_din']);
	foreach ($metrics['value_din'] as $key => $item)
	{
		if (($key!='') && ($item!=0))
		{
			//$out['value_mdin'][$key]=$item;
			$out['value_mdin'][$ll]['name']=$key;
			$out['value_mdin'][$ll]['count']=$item;
			$ll++;
		}
	}
	$ll=0;
	arsort($metrics['eng_din']);
	foreach ($metrics['eng_din'] as $key => $item)
	{
		if (($key!='') && ($item!=0))
		{
			if ($key=='livejournal.com') $key='Комментарии Livejournal';
			if (($key=='vkontakte.ru') || ($key=='vk.com')) $key='"Мне нравится" Вконтакте';
			//if () $key='Нравится Вконтакте';
			if ($key=='facebook.com') $key='Лайки Facebook';
			if ($key=='twitter.com') $key='Ретвиты Twitter';
			$out['eng_mdin'][$ll]['name']=$key;
			$out['eng_mdin'][$ll]['count']=$item;
			$ll++;
			//$out['eng_mdin'][$key]=$item;
		}
	}
	foreach ($mas_res as $ind => $item)
	{
		$out['sources'][$k]['name']=$ind;
		$out['sources'][$k]['count']=$item;
		$k++;
		$coll+=$item;
	}
	arsort($metrics['location']);
	//print_r($metrics['location']);
	foreach ($metrics['location'] as $key => $item)
	{
		if ($key!='')
		{
			$out['city'][$kk]['name']=(($key=='')?'Не определено':$key);
			$out['city'][$kk]['count']=$item;
			$cd=$wobot['destn2'][$key];
			$mcd=explode(' ',$cd);
			$out['city'][$kk]['x']=intval($mcd[0]*100)/100;
			$out['city'][$kk]['y']=intval($mcd[1]*100)/100;
			$kk++;
		}
	}
	foreach ($metrics['speakers']['nick'] as $key => $item)
	{
		//echo $metrics['speakers']['site'][$key].' |';
		if (($metrics['speakers']['site'][$key]!='vkontakte.ru') && ($metrics['speakers']['site'][$key]!='facebook.com') && ($metrics['speakers']['site'][$key]!='vk.com'))
		{
			$out['speakers'][$kkk]['nick']=$metrics['speakers']['nick'][$key];
			$out['speakers'][$kkk]['count']=$metrics['speakers']['posts'][$key];		
		}
		else
		{
			preg_replace('/\s/is',' ',$metrics['speakers']['rnick'][$key]);
			if (mb_strlen($metrics['speakers']['rnick'][$key],'UTF-8')>20)
			{
				$metrics['speakers']['rnick'][$key]=mb_substr($metrics['speakers']['rnick'][$key],0,20,'UTF-8').'...';
			}
			$out['speakers'][$kkk]['nick']=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$metrics['speakers']['rnick'][$key]);
			$out['speakers'][$kkk]['count']=$metrics['speakers']['posts'][$key];		
		}
		$kkk++;
	}
	//print_r($metrics['prom_posts']);
	foreach ($metrics['promotion']['nick'] as $key => $item)
	{
		//echo $metrics['promotion']['link'][$key].' |';
		if (($metrics['promotion']['site'][$key]!='vkontakte.ru') && ($metrics['promotion']['site'][$key]!='facebook.com') && ($metrics['promotion']['site'][$key]!='vk.com'))
		{
			$out['promotions'][$kkkk]['nick']=$metrics['promotion']['nick'][$key];
			$out['promotions'][$kkkk]['count']=intval($metrics['promotion']['readers'][$key]);		
			$out['promotions'][$kkkk]['id']=intval($metrics['promotion']['id'][$key]);		
			$out['promotions'][$kkkk]['count_posts']=intval($metrics['prom_posts'][$metrics['promotion']['nick'][$key].':'.$metrics['promotion']['site'][$key]]);		
		}
		else
		{
			$out['promotions'][$kkkk]['count_posts']=intval($metrics['prom_posts'][$metrics['promotion']['rnick'][$key].':'.$metrics['promotion']['site'][$key]]);		
			preg_replace('/\s/is',' ',$metrics['promotion']['rnick'][$key]);
			if (mb_strlen($metrics['promotion']['rnick'][$key],'UTF-8')>20)
			{
				$metrics['promotion']['rnick'][$key]=mb_substr($metrics['promotion']['rnick'][$key],0,20,'UTF-8').'...';
			}
			$out['promotions'][$kkkk]['nick']=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$metrics['promotion']['rnick'][$key]);
			$out['promotions'][$kkkk]['count']=intval($metrics['promotion']['readers'][$key]);		
			$out['promotions'][$kkkk]['id']=intval($metrics['promotion']['id'][$key]);		
			//echo $metrics['promotion']['nick'][$key].'<br>';
		}
		$kkkk++;
	}
	$graph=$row['order_graph'];
	$mmtime=json_decode($graph,true);
	$mtime=$mmtime['all'];
	//print_r($mtime);
	foreach ($mtime as $hn=>$gtime){
	//if (in_array($hn,$av_host)||(($indother==1)&&(!in_array($hn,$all_host)))) {
	//$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;
		foreach($gtime as $year=>$years) {
			foreach($years as $month=>$months){
				foreach($months as $day=>$days){
						$timet[$year][$month][$day]+=$days;
				}
			}
		}
	//}
	}
	//print_r($timet);
	$kned=0;
	$zap='';
	//print_r($m_dinams);
	$kkey=0;
	$k=0;
	arsort($metrics['topwords']);
	foreach ($metrics['topwords'] as $key => $item)
	{
		$out['words'][$k]['word']=$key;
		$out['words'][$k]['count']=$item;
		$k++;
	}
	if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
	{
		unset($out['speakers']);
		unset($out['promotions']);
		unset($out['sources']);
		unset($out['city']);
		unset($out['value_mdin']);
		unset($out['eng_mdin']);
		unset($out['words']);
		for($t=strtotime($_GET['start']);$t<=strtotime($_GET['end']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			$out['graph'][$t]=intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
			$count_post_per+=intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
			foreach ($m_dinams['src'][$t] as $key => $item)
			{
				//echo $t.' ';
				$mas_cou_src[$key]+=$item;
				if (!isset($yet_src[$key]))
				{
					$count_src_per++;
					$yet_src[$key]=1;
				}
			}
			foreach ($m_dinams['sp_pr'][$t] as $key => $item)
			{
				foreach ($item as $kkk => $iii)
				{
					//echo $kkk.':'.$iii['login'].':'.$key.'<br>';
					if (!isset($y_prms[$iii['login'].':'.$kkk]))
					{
						$m_prms['value'][]=$iii['readers'];
						$m_prms['nick'][]=(($kkk=='vkontakte.ru') || ($kkk=='vk.com'))?$key:$iii['login'];
						$m_prms['srcc'][]=$kkk;
					}
					//echo $key.' '.$iii['login'].':'.$kkk.':'.$m_dinams['sp_pr'][$t][$iii['login']][$kkk]['count']."<br>";
					$y_prms[$iii['login'].':'.$kkk]+=$m_dinams['sp_pr'][$t][$iii['login']][$kkk]['count'];//$iii['count'];
					if ($key!=' ')
					{
						$m_spks[$key]+=$iii['count'];
						$m_spks1[$key.':'.$kkk]+=$iii['count'];
					}
				}
			}
			foreach ($m_dinams['uniq'][$t] as $key => $item)
			{
				foreach ($item as $kkk => $iii)
				{
					//echo $key.' ';
					if ((!isset($yet_uniq[$key.':'.$kkk])) && ($key!=''))
					{
						$count_value_per+=$iii;
						$count_uniq_per++;
					}
					$yet_uniq[$key.':'.$kkk]++;
				}
			}
			foreach ($m_dinams['geo'][$t] as $key => $item)
			{
				$mas_geo_per[$key]+=$item;	
			}
			foreach ($m_dinams['value'][$t] as $key => $item)
			{
				if ($key!='')
				{
					$outper['value_mdint'][$key]+=$item;
				}
			}
			foreach ($m_dinams['eng_time'][$t] as $key => $item)
			{
				if ($key!='')
				{
					$outper['eng_mdint'][$key]+=$item;
				}
			}
			foreach ($m_dinams['words'][$t] as $key => $item)
			{
				if ((trim($key)!='') && (mb_strlen($key,'UTF-8')>3))
				$m_wrds[$item['reword']]+=$item['count'];
			}
			$count_eng_per+=$m_dinams['eng'][$t];
		}
		//print_r($m_prms['srcc']);
		//print_r($m_prms['nick']);
		//print_r($m_dinams['sp_pr']);
		array_multisort($m_prms['value'],SORT_DESC,$m_prms['nick'],SORT_DESC,$m_prms['srcc'],SORT_DESC);
		arsort($m_spks);
		$kl=0;
		$indks=0;
		$k_wrds=0;
		arsort($m_wrds);
		foreach ($m_wrds as $key => $item)
		{
			//$out['words'][$k_wrds]['words']
			$out['words'][$k_wrds]['word']=$key;
			$out['words'][$k_wrds]['count']=$item;
			$k_wrds++;
		}
		arsort($outper['value_mdint']);
		foreach ($outper['value_mdint'] as $key => $item)
		{
			if ($item!=0)
			{
				$out['value_mdin'][$indks]['name']=$key;
				$out['value_mdin'][$indks]['count']=$item;
				$indks++;
			}
		}
		$indks=0;
		arsort($outper['eng_mdint']);
		foreach ($outper['eng_mdint'] as $key => $item)
		{
			if ($item!=0)
			{
				$out['eng_mdin'][$indks]['name']=$key;
				$out['eng_mdin'][$indks]['count']=$item;
				$indks++;
			}
		}
		foreach ($m_prms['value'] as $key => $item)
		{
			//echo $key.':'.$m_prms['nick'][$key].':'.$m_prms['srcc'][$key].'|||'.$y_prms[$m_prms['nick'][$key].':'.$m_prms['srcc'][$key]].'<br>';
			$out['promotions'][$key]['count_posts']=intval($m_spks1[$m_prms['nick'][$key].':'.$m_prms['srcc'][$key]]);//intval($y_prms[$m_prms['nick'][$key].':'.$m_prms['srcc'][$key]]);
			if (mb_strlen($m_prms['nick'][$key],'UTF-8')>20)
			{
				$m_prms['nick'][$key]=mb_substr($m_prms['nick'][$key],0,20,'UTF-8');
			}
			$out['promotions'][$key]['nick']=$m_prms['nick'][$key];
			$out['promotions'][$key]['count']=intval($item);
		}
		$kl=0;
		foreach ($m_spks as $key1 => $item1)
		{
			if ($key1!='')
			{
				$out['speakers'][$kl]['nick']=(mb_strlen($key1,'UTF-8')>15)?preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$key1):$key1;
				$out['speakers'][$kl]['count']=intval($item1);
				$kl++;
			}
		}
		$kl=0;
		arsort($mas_cou_src);
		foreach ($mas_cou_src as $key => $item)
		{
			$out['sources'][$kl]['name']=(mb_strlen($key,'UTF-8')>15)?preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$key):$key;
			$out['sources'][$kl]['count']=$item;
			$kl++;
		}
		arsort($mas_geo_per);
		foreach ($mas_geo_per as $key => $item)
		{
			if ($key!='')
			{
				$out['city'][$kkey]['name']=(($key=='')?'Не определено':$key);
				$out['city'][$kkey]['count']=$item;
				$cd=$wobot['destn2'][$key];
				$mcd=explode(' ',$cd);
				$out['city'][$kkey]['x']=intval($mcd[0]*100)/100;
				$out['city'][$kkey]['y']=intval($mcd[1]*100)/100;
				$kkey++;
			}
		}
	}
	else
	{
		for($t=$row['order_start'];$t<=(($row['order_end']==0)?$row['order_last']:$row['order_end']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			$out['graph'][$t]=intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
		}
	}
	if ((($row['order_end']-$row['order_start'])>86400*30*2) && ($_GET['start']=='null') && ($_GET['end']=='null'))
	{
		//echo 123;
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$mgr=get_mas_week($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
	}
	if ((($row['order_end']-$row['order_start'])>86400*30*24) && ($_GET['start']=='null') && ($_GET['end']=='null'))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$mgr=get_mas_mon($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}		
	}
	if ((((strtotime($_GET['end'])-strtotime($_GET['start']))>86400*30*2) && (strtotime($_GET['start'])!=0)))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$mgr=get_mas_week($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
	}
	if ((((strtotime($_GET['end'])-strtotime($_GET['start']))>86400*30*24) && (strtotime($_GET['start'])!=0)))
	{
		//echo $_GET['start'].' '.$_GET['end'];
		unset($out['graph']);
		$mgr=get_mas_mon($_GET['order_id'],(strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start'])),(strtotime($_GET['end'])==0?$row['order_end']:strtotime($_GET['end'])));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][$key]=$item;
		}
	}
	if (((strtotime($_GET['start'])==strtotime($_GET['end'])) && (strtotime($_GET['start'])!=0)) || ($row['order_start']==$row['order_end']))
	{
		//echo 'gg';
		unset($out['graph']);
		$mgr=get_mas($_GET['order_id'],strtotime($_GET['start'])==0?$row['order_start']:strtotime($_GET['start']));
		foreach ($mgr['time'] as $key => $item)
		{
			$out['graph'][strtotime($_GET['start'])+$key*3600+4*3600]=$item;
		}
	}

	//$out['graph']='img/graph/'.$row['order_id'].'.png';
	if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
	{
		$out['posts']=formatint($count_post_per);
		$out['src']=formatint(intval($count_src_per));
		$out['value']=formatint(intval($count_value_per));
		$out['uniq']=formatint(intval($count_uniq_per));
		$out['engage']=formatint(intval($count_eng_per));		
	}
	else
	{
		$out['posts']=formatint($coll);
		$out['src']=formatint(intval($res_count));
		$out['value']=formatint(intval($metrics['value']));
		$out['uniq']=formatint(intval($metrics['speakers']['uniq']));
		$out['engage']=formatint(intval($metrics['engagement']));
	}
	$out['start']=date('d.m.Y',$row['order_start']);
	$out['end']=date('d.m.Y',($row['order_end']==0)?$row['order_last']:$row['order_end']);
	$out['posts_dyn']=intval($metrics['d_post']>0?formatint(abs($metrics['d_post'])):'-'.formatint(abs($metrics['d_post'])));
	$out['src_dyn']=intval($metrics['d_src']>0?formatint(abs($metrics['d_src'])):'-'.formatint(abs($metrics['d_src'])));//$metrics['d_src'];
	$out['value_dyn']=intval($metrics['d_aud']>0?formatint(abs($metrics['d_aud'])):'-'.formatint(abs($metrics['d_aud'])));//$metrics['d_aud'];
	$out['engage_dyn']=intval($metrics['d_eng']>0?formatint(abs($metrics['d_eng'])):'-'.formatint(abs($metrics['d_eng'])));//$metrics['d_eng'];
	$out['uniq_dyn']=intval($metrics['d_uniq']>0?formatint(abs($metrics['d_uniq'])):'-'.formatint(abs($metrics['d_uniq'])));//$metrics['d_uniq'];
	$i++;
}
if (!isset($out['speakers']))
{
	$out['speakers']=array();
}
if (!isset($out['promotions']))
{
	$out['promotions']=array();
}
if (!isset($out['sources']))
{
	$out['sources']=array();
}
if (!isset($out['city']))
{
	$out['city']=array();
}
if (!isset($out['value_mdin']))
{
	$out['value_mdin']=array();
}
if (!isset($out['eng_mdin']))
{
	$out['eng_mdin']=array();
}
if (!isset($out['words']))
{
	$out['words']=array();
}
//unset($out['eng_mdin']);
//print_r($yet_uniq);
//print_r($metrics);
/*$out['order_id']=$_GET['order_id'];
$out['posts']=5098;
$out['uniq']=562;
$out['src']=282;
$out['value']=277181;
$out['engage']=7812;
$out['start']='15.08.2010';
$out['end']='15.09.2010';

$out['posts_dyn']=10;
$out['uniq_dyn']=32;
$out['src_dyn']=15;
$out['value_dyn']=-22;
$out['engage_dyn']=40;

$out['sources'][0]['name']='twitter.com'; $out['sources'][0]['count']=1872;
$out['sources'][1]['name']='livejournal.com'; $out['sources'][1]['count']=532;
$out['sources'][2]['name']='hpc.ru'; $out['sources'][2]['count']=290;
$out['sources'][3]['name']='google.com'; $out['sources'][3]['count']=136;
$out['sources'][4]['name']='ya.ru'; $out['sources'][4]['count']=130;
$out['sources'][5]['name']='4pda.ru'; $out['sources'][5]['count']=125;
$out['sources'][6]['name']='alfa.kz'; $out['sources'][6]['count']=86;
$out['sources'][7]['name']='4htc.ru'; $out['sources'][7]['count']=81;
$out['sources'][8]['name']='android-no.info'; $out['sources'][8]['count']=79;
$out['sources'][9]['name']='molotok.ru'; $out['sources'][9]['count']=68;
$out['sources'][10]['name']='com.ua'; $out['sources'][10]['count']=63;
$out['sources'][11]['name']='youhtc.ru'; $out['sources'][11]['count']=62;
$out['sources'][12]['name']='aukro.ua'; $out['sources'][12]['count']=60;
$out['sources'][13]['name']='ixbt.com'; $out['sources'][13]['count']=39;
$out['sources'][14]['name']='androidsecretsmagazine.com'; $out['sources'][14]['count']=38;
$out['sources'][15]['name']='asusmobile.ru'; $out['sources'][15]['count']=31;
$out['sources'][16]['name']='juick.com'; $out['sources'][16]['count']=30;
$out['sources'][17]['name']='blogspot.com'; $out['sources'][17]['count']=29;
$out['sources'][18]['name']='ukrgo.com'; $out['sources'][18]['count']=27;
$out['sources'][19]['name']='mail.ru'; $out['sources'][19]['count']=25;
$out['sources'][20]['name']='kharkovforum.com'; $out['sources'][20]['count']=25;
$out['sources'][21]['name']='habrahabr.ru'; $out['sources'][21]['count']=24;
$out['sources'][22]['name']='google.ru'; $out['sources'][22]['count']=23;
$out['sources'][23]['name']='net.ua'; $out['sources'][23]['count']=23;

$out['city'][0]['name']='Москва'; $out['city'][0]['count']=238;
$out['city'][1]['name']='Россия'; $out['city'][1]['count']=91;
$out['city'][2]['name']='Санкт-петербург'; $out['city'][2]['count']=78;
$out['city'][3]['name']='Украина'; $out['city'][3]['count']=70;
$out['city'][4]['name']='Тольятти'; $out['city'][4]['count']=42;
$out['city'][5]['name']='Нью-Йорк'; $out['city'][5]['count']=28;
$out['city'][6]['name']='Минск'; $out['city'][6]['count']=24;
$out['city'][7]['name']='Киев'; $out['city'][7]['count']=23;
$out['city'][8]['name']='Челябинск'; $out['city'][8]['count']=20;
$out['city'][9]['name']='Дубна'; $out['city'][9]['count']=18;
$out['city'][10]['name']='Рига'; $out['city'][10]['count']=15;
$out['city'][11]['name']='Тюмень'; $out['city'][11]['count']=14;
$out['city'][12]['name']='Волгоград'; $out['city'][12]['count']=14;
$out['city'][13]['name']='Архангельск'; $out['city'][13]['count']=10;
$out['city'][14]['name']='Ростов-на-Дону'; $out['city'][14]['count']=10;
$out['city'][15]['name']='Пенза'; $out['city'][15]['count']=10;
$out['city'][16]['name']='Харьков'; $out['city'][16]['count']=10;
$out['city'][17]['name']='Омск'; $out['city'][17]['count']=10;
$out['city'][18]['name']='Саранск'; $out['city'][18]['count']=9;
$out['city'][19]['name']='Смоленск'; $out['city'][19]['count']=9;

$out['promotions'][0]['nick']='Marfitsin'; $out['promotions'][0]['count']=42;
$out['promotions'][1]['nick']='isegals'; $out['promotions'][1]['count']=36;
$out['promotions'][2]['nick']='technewsru'; $out['promotions'][2]['count']=27;
$out['promotions'][3]['nick']='mrozentsvayg'; $out['promotions'][3]['count']=27;
$out['promotions'][4]['nick']='artranceit'; $out['promotions'][4]['count']=27;
$out['promotions'][5]['nick']='Alexandrlaw'; $out['promotions'][5]['count']=27;
$out['promotions'][6]['nick']='FOR_HTC'; $out['promotions'][6]['count']=20;
$out['promotions'][7]['nick']='bigarando'; $out['promotions'][7]['count']=19;
$out['promotions'][8]['nick']='android_ua'; $out['promotions'][8]['count']=19;
$out['promotions'][9]['nick']='velolive'; $out['promotions'][9]['count']=18;
$out['promotions'][10]['nick']='andrewbabkin'; $out['promotions'][10]['count']=18;
$out['promotions'][11]['nick']='YouHTC'; $out['promotions'][11]['count']=18;
$out['promotions'][12]['nick']='Xill47'; $out['promotions'][12]['count']=18;
$out['promotions'][13]['nick']='Marrkyboy'; $out['promotions'][13]['count']=18;
$out['promotions'][14]['nick']='HTC_Ru'; $out['promotions'][14]['count']=12;
$out['promotions'][15]['nick']='thieplh'; $out['promotions'][15]['count']=11;
$out['promotions'][16]['nick']='novosteycom'; $out['promotions'][16]['count']=11;
$out['promotions'][17]['nick']='VirusPanin'; $out['promotions'][17]['count']=11;
$out['promotions'][18]['nick']='zhenyek'; $out['promotions'][18]['count']=10;
$out['promotions'][19]['nick']='pravdzivyj'; $out['promotions'][19]['count']=10;
$out['promotions'][20]['nick']='onton_a'; $out['promotions'][20]['count']=10;
$out['promotions'][21]['nick']='itrus'; $out['promotions'][21]['count']=10;
$out['promotions'][22]['nick']='drlyvsy'; $out['promotions'][22]['count']=10;
$out['promotions'][23]['nick']='dimka48'; $out['promotions'][23]['count']=10;
$out['promotions'][24]['nick']='chibisanton'; $out['promotions'][24]['count']=10;
$out['promotions'][25]['nick']='borutskiy'; $out['promotions'][25]['count']=10;
$out['promotions'][26]['nick']='andrewf'; $out['promotions'][26]['count']=10;
$out['promotions'][27]['nick']='SRG222'; $out['promotions'][27]['count']=10;
$out['promotions'][28]['nick']='Miliaev'; $out['promotions'][28]['count']=10;

$out['speakers'][0]['nick']='Marfitsin'; $out['speakers'][0]['count']=42;
$out['speakers'][1]['nick']='isegals'; $out['speakers'][1]['count']=36;
$out['speakers'][2]['nick']='technewsru'; $out['speakers'][2]['count']=27;
$out['speakers'][3]['nick']='mrozentsvayg'; $out['speakers'][3]['count']=27;
$out['speakers'][4]['nick']='artranceit'; $out['speakers'][4]['count']=27;
$out['speakers'][5]['nick']='Alexandrlaw'; $out['speakers'][5]['count']=27;
$out['speakers'][6]['nick']='FOR_HTC'; $out['speakers'][6]['count']=20;
$out['speakers'][7]['nick']='bigarando'; $out['speakers'][7]['count']=19;
$out['speakers'][8]['nick']='android_ua'; $out['speakers'][8]['count']=19;
$out['speakers'][9]['nick']='velolive'; $out['speakers'][9]['count']=18;
$out['speakers'][10]['nick']='andrewbabkin'; $out['speakers'][10]['count']=18;
$out['speakers'][11]['nick']='YouHTC'; $out['speakers'][11]['count']=18;
$out['speakers'][12]['nick']='Xill47'; $out['speakers'][12]['count']=18;
$out['speakers'][13]['nick']='Marrkyboy'; $out['speakers'][13]['count']=18;
$out['speakers'][14]['nick']='HTC_Ru'; $out['speakers'][14]['count']=12;
$out['speakers'][15]['nick']='thieplh'; $out['speakers'][15]['count']=11;
$out['speakers'][16]['nick']='novosteycom'; $out['speakers'][16]['count']=11;
$out['speakers'][17]['nick']='VirusPanin'; $out['speakers'][17]['count']=11;
$out['speakers'][18]['nick']='zhenyek'; $out['speakers'][18]['count']=10;
$out['speakers'][19]['nick']='pravdzivyj'; $out['speakers'][19]['count']=10;
$out['speakers'][20]['nick']='onton_a'; $out['speakers'][20]['count']=10;
$out['speakers'][21]['nick']='itrus'; $out['speakers'][21]['count']=10;
$out['speakers'][22]['nick']='drlyvsy'; $out['speakers'][22]['count']=10;
$out['speakers'][23]['nick']='dimka48'; $out['speakers'][23]['count']=10;
$out['speakers'][24]['nick']='chibisanton'; $out['speakers'][24]['count']=10;
$out['speakers'][25]['nick']='borutskiy'; $out['speakers'][25]['count']=10;
$out['speakers'][26]['nick']='andrewf'; $out['speakers'][26]['count']=10;
$out['speakers'][27]['nick']='SRG222'; $out['speakers'][27]['count']=10;
$out['speakers'][28]['nick']='Miliaev'; $out['speakers'][28]['count']=10;

$out['words'][0]['word']='desire'; $out['words'][0]['count']=705;
$out['words'][1]['word']='новый'; $out['words'][1]['count']=553;
$out['words'][2]['word']='продам'; $out['words'][2]['count']=552;
$out['words'][3]['word']='продаю'; $out['words'][3]['count']=522;
$out['words'][4]['word']='android'; $out['words'][4]['count']=394;
$out['words'][5]['word']='куплю'; $out['words'][5]['count']=321;
$out['words'][6]['word']='touch'; $out['words'][6]['count']=318;
$out['words'][7]['word']='меня'; $out['words'][7]['count']=295;
$out['words'][8]['word']='hero'; $out['words'][8]['count']=242;
$out['words'][9]['word']='есть'; $out['words'][9]['count']=235;
$out['words'][10]['word']='google'; $out['words'][10]['count']=223;
$out['words'][11]['word']='коммуникаторы'; $out['words'][11]['count']=202;
$out['words'][12]['word']='wildfire'; $out['words'][12]['count']=171;
$out['words'][13]['word']='планшет'; $out['words'][13]['count']=164;
$out['words'][14]['word']='москва'; $out['words'][14]['count']=152;
$out['words'][15]['word']='diamond'; $out['words'][15]['count']=148;
$out['words'][16]['word']='смартфон'; $out['words'][16]['count']=146;
$out['words'][17]['word']='телефон'; $out['words'][17]['count']=143;
$out['words'][18]['word']='сегодня'; $out['words'][18]['count']=135;
$out['words'][19]['word']='samsung'; $out['words'][19]['count']=128;
$out['words'][20]['word']='windows'; $out['words'][20]['count']=126;
$out['words'][21]['word']='санкт'; $out['words'][21]['count']=112;
$out['words'][22]['word']='если'; $out['words'][22]['count']=112;
$out['words'][23]['word']='объявление'; $out['words'][23]['count']=110;
$out['words'][24]['word']='iphone'; $out['words'][24]['count']=108;
$out['words'][25]['word']='legend'; $out['words'][25]['count']=106;
$out['words'][26]['word']='мобильный'; $out['words'][26]['count']=104;
$out['words'][27]['word']='видео'; $out['words'][27]['count']=99;

$out['graph']['1343678400']=28;
$out['graph']['1344542400']=22;
$out['graph']['1344628800']=15;
$out['graph']['1344715200']=37;
$out['graph']['1344801600']=54;
$out['graph']['1344888000']=21;
$out['graph']['1344974400']=9;
$out['graph']['1345060800']=89;
$out['graph']['1345147200']=67;
$out['graph']['1345233600']=38;
$out['graph']['1345320000']=60;
$out['graph']['1345406400']=23;
$out['graph']['1345492800']=123;
$out['graph']['1345579200']=29;
$out['graph']['1345665600']=76;
$out['graph']['1345752000']=67;
$out['graph']['1345838400']=58;
$out['graph']['1345924800']=40;
$out['graph']['1346011200']=35;
$out['graph']['1346097600']=31;

*/
//$out['value_dyn']=0;
//$out['engage_dyn']=0;
//$out['value_dyn']=0;
//$out['src_dyn']=0;
//$out['uniq_dyn']=0;
//$out['posts_dyn']=0;
//print_r(get_mas(736,1334865600));
echo json_encode($out);
?>