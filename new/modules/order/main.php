<?
/*
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
//require_once ('tpl/main.php');
require_once('tpl/main.php');
*/

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
date_default_timezone_set('Europe/Moscow');

setlocale(LC_COLLATE, 'UTF8');

/*
=====================================================================================================================================================

	WOBOT 2010 (с) http://www.wobot.ru
	
	MODULE START
	Developer:	Yudin Roman
	Description:
	Right autostart module.
	
	СТАРТОВЫЙ МОДУЛЬ
	Разработка:	Юдин Роман
	Описание:
	Начальный стартовый модуль.
	
=====================================================================================================================================================
*/

$db = new database();
$db->connect();

//auth();
if (!$loged) die();

/*foreach($_COOKIE as $n => $ck)
{
        if (substr($n,0,5)=='order') 
        {
		unset($_COOKIE[$n]);
        }
}*/


//------resources------
$res_micr=array('mail.ru','twitter.com','mblogi.qip.ru','rutvit.ru','friendfeed.com','godudu.com','juick.com','jujuju.ru','sports.ru','mylife.ru','chikchirik.ru','chch.ru','f5.ru','zizl.ru','smsnik.com');
$soc_netw=array('1c-club.com','cjclub.ru','diary.ru','facebook.com','myspace.com','orkut.com','vkontakte.ru','stranamam.ru','dietadiary.com','ya.ru');
$nov_res=array('km.ru','regnum.ru','akm.ru','arms-tass.su','annews.ru','itar-tass.com','interfax.ru','interfax-russia.ru','oreanda.ru','1prime.ru','rbc.ru','rbc.ru','ria.ru','rosbalt.ru','tasstelecom.ru','finmarket.ru','expert.ru','newtimes.ru','akzia.ru','aif.ru','argumenti.ru','bg.ru','vedomosti.ru','izvestia.ru','itogi.ru','kommersant.ru','kommersant.ru','kp.ru','mospravda.ru','mn.ru','mk.ru','ng.ru','novayagazeta.ru','newizv.ru','kommersant.ru','politjournal.ru','profile.ru','rbcdaily.ru','gosrf.ru','rodgaz.ru','rg.ru','russianews.ru','senat.org','sobesednik.ru','tribuna.ru','trud.ru','newstube.ru','vesti.ru','mir24.tv','ntv.ru','1tv.ru','rutv.ru','tvkultura.ru','tvc.ru','tvzvezda.ru','5-tv.ru','ren-tv.com','radiovesti.ru','govoritmoskva.ru','ruvr.ru','kommersant.ru','cultradio.ru','radiomayak.ru','radiorus.ru','rusnovosti.ru','msk.ru','infox.ru','lenta.ru','lentacom.ru','newsru.com','temadnya.ru','newsinfo.ru','rb.ru','utronews.ru','moscow-post.ru','apn.ru','argumenti.ru','wek.ru','vz.ru','gazeta.ru','grani.ru','dni.ru','evrazia.org','ej.ru','izbrannoe.ru','inopressa.ru','inosmi.ru','inforos.ru','kommersant.ru','kreml.org','polit.ru','pravda.ru','rabkor.ru','russ.ru','smi.ru','svpressa.ru','segodnia.ru','stoletie.ru','strana.ru','utro.ru','fedpress.ru','lifenews.ru','belrus.ru','pfrf.ru','rosculture.ru','kremlin.ru','gov.ru','rosnedra.com');


$order_id=intval($path[0]);

$res=$db->query("SELECT * from blog_orders WHERE order_id=".$order_id." and user_id=".intval($user['user_id'])." LIMIT 1");
if (mysql_num_rows($res)==0) die();
$order = $db->fetch($res);
$order['order_start']=intval($order['order_start']);
$order['order_end']=((intval($order['order_end'])==0)||(intval($order['order_end'])>mktime(0,0,0,date('m'),date('d')-1,date('Y'))))?(mktime(0,0,0,date('m'),date('d'),date('Y'))):intval($order['order_end']+60*60*24);
//mktime(0,0,0,date('m'),date('d')-1,date('Y'));
$res_tag=$db->query("SELECT * from blog_tag WHERE user_id=".intval($user['user_id']));
//print_r($order['order_metrics']);
//$mmm=json_decode($order['order_metrics'],true);
//print_r($mmm['speakers']);
while ($order_tag = $db->fetch($res_tag))
{
	$mtags[$order_tag['tag_tag']]=$order_tag['tag_name'];
}
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
unset($sources);
unset($data);
$data=$order['order_src'];
$sources=json_decode($data, true);
foreach ($sources as $i => $source)
{
		$other+=$source;
}
$c_posts=$other-$metrics['location'][''];
$params='
	var refr_graph=0;
	var time_beg=\''.date('d.m.Y',$order['order_start']).'\';
	var time_end=\''.date('d.m.Y',$order['order_end']).'\';
	var speakers = Array(
	';
	foreach ($metrics['speakers']['link'] as $key => $item)
	{
		if ($item=='twitter.com')
		{
			$text_link='http://twitter.com/'.$metrics['speakers']['nick'][$key];
			$text_nick=$metrics['speakers']['nick'][$key];
			$mpspeak.=' '.$metrics['speakers']['nick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
		}
		else
		if ($item=='livejournal.com')
		{
			$text_link='http://'.$metrics['speakers']['nick'][$key].'.livejournal.com/';
			$text_nick=$metrics['speakers']['nick'][$key];
			$mpspeak.=' '.$metrics['speakers']['nick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
		}
		else
		if ($item=='vkontakte.ru')
		{
			$text_link='http://vkontakte.ru/id'.$metrics['speakers']['nick'][$key];
			$text_nick=$metrics['speakers']['rnick'][$key];
			$mpspeak.=' '.$metrics['speakers']['rnick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
		}
		else
		if ($item=='facebook.com')
		{
			$text_link='http://facebook.com/'.$metrics['speakers']['nick'][$key];
			$text_nick=$metrics['speakers']['rnick'][$key];
			$mpspeak.=' '.$metrics['speakers']['rnick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
		}
		if (mb_strpos($text_nick,' ')!==false)
		{
			$text_nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$text_nick);
		}
		if (mb_strlen($text_nick,'UTF-8')>13)
		{
			$text_nick=mb_substr($text_nick,0,11,'UTF-8').'...';
		}
		$params.=$zap.'{\'name\': \''.$text_nick.'\', \'nick\':\''.$metrics['speakers']['nick'][$key].'\', \'num\': '.intval($metrics['speakers']['posts'][$key]).', \'link\':\''.$text_link.'\'}';
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
			$mpprom.=' '.$metrics['promotion']['nick'][$key].' '.intval($metrics['promotion']['readers'][$key]);
		}
		else
		if ($item=='livejournal.com')
		{
			$text_link='http://'.$metrics['promotion']['nick'][$key].'.livejournal.com/';
			$text_nick=$metrics['promotion']['nick'][$key];
			$mpprom.=' '.$metrics['promotion']['nick'][$key].' '.intval($metrics['promotion']['readers'][$key]);
		}
		if (mb_strpos($text_nick,' ')!==false)
		{
			$text_nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$text_nick);
		}
		if (mb_strlen($text_nick,'UTF-8')>13)
		{
			$text_nick=mb_substr($text_nick,0,11,'UTF-8').'...';
		}
		$params.=$zap.'{\'name\': \''.$text_nick.'\', \'nick\':\''.$metrics['promotion']['nick'][$key].'\', \'num\': '.intval($metrics['promotion']['readers'][$key]).', \'link\':\''.$text_link.'\'}';
		$zap=',';
	}
$params.='
	);
	';
	$params1.='
	$(document).ready(function(){
	var mspeak = \''.$mpspeak.'\';
	var mprom=\''.$mpprom.'\';

});';
$params.='
	var resources=[
	';
	$sourcesgr=$sources;
	//print_r($sourcesgr);
	foreach ($sourcesgr as $i => $source)
	{
		$k++;
		if ($k<9)
		{
			//if ($k>1) $html_out.=',';
			//$html_out.='[\''.$i.'\', '.(intval($source/$socialall*1000)/10).']';
			$socialar['name'][$k-1]=$i;
			$socialar['all'][$k-1]=$source;
			$socialar['count'][$k-1]=intval($source/$other*1000)/10;
			$tensocial+=intval($source/$other*1000)/10;
			$witho_s+=$source;
		}
	}
	$socialar['count'][9]=100-$tensocial;
	$socialar['name'][9]='другие';
	$socialar['all'][9]=$other-$witho_s;
	array_multisort($socialar['count'], $socialar['name'], $socialar['all']);
	$tt=0;
	for($i=0;$i<9;$i++){
		if ($tt>0) $params.=',';
		if ($socialar['count'][8-$i]!=0) { $params.='[\''.$socialar['name'][8-$i].'\', '.intval($socialar['count'][8-$i]).', '.intval($socialar['all'][8-$i]).']'; $tt++; }
	}
	$params.='
	];
	var order_id='.$order['order_id'].';

	var cities=[
	';
	$i=0;
	foreach ($metrics['location'] as $k => $item)
	{
		if ($i<8)
		{		
			if (($k!='') && (intval($item/$c_posts*100)>=1))
			{
				$i++;
				//$params.='[\''.$k.'\','.intval((($item/$c_posts)*100)).', '.$item.'],';
				$masg1['value'][]=$item;
				$masg1['proc'][]=intval((($item/$c_posts)*100));
				$masg1['name'][]=$k;
				$masg['value'][]=$item;
				$masg['proc'][]=intval((($item/$c_posts)*100));
				$masg['name'][]=$k;
				$pc_geo+=intval((($item/$c_posts)*100));
				$cc_or+=$item;
			}
			elseif ($k!='')
			{
				$cc_oth+=$item;
			}
		}
	}
	//echo $c_posts.' '.intval(($other-$c_posts)/$other*100).' <a href="#" onclick="copyToClipboard(\'xczc\');">ss</a>';
	$masg['proc'][]=100-$pc_geo;
	$masg['value'][]=intval($c_posts-$cc_or);
	$masg['name'][]='другие';
	//print_r($masg);
	array_multisort($masg['proc'],SORT_DESC,
    $masg['value'],SORT_DESC,$masg['name'],SORT_DESC);
	array_multisort($masg1['proc'],SORT_DESC,
	$masg1['value'],SORT_DESC,$masg1['name'],SORT_DESC);
	//print_r($masg);
	foreach ($masg1['proc'] as $key => $item)
	{
		//if ($masg['name'][$key]!='другие')
		$params.='[\''.$masg1['name'][$key].'\','.$item.','.$masg1['value'][$key].'],';
	}
	$params.='[\'другие\','.intval(($other-$c_posts)/$other*100).', '.intval($other-$c_posts).']';
	$params.='
	];
	var cit1=[
	';
	foreach ($masg['proc'] as $key => $item)
	{
		//if ($masg['name'][$key]!='другие')
		$params.='[\''.$masg['name'][$key].'\','.$item.','.$masg['value'][$key].'],';
	}
	$params.='
	];
	var data = [
	';
	$indother=1;
	$graph=$order['order_graph'];
	//fclose($h);
	$mmtime=json_decode($graph,true);
	$mtime=$mmtime['all'];
	//print_r($mtime);
	foreach ($mtime as $hn=>$gtime){
	if (in_array($hn,$av_host)||(($indother==1)&&(!in_array($hn,$all_host)))) {
	//$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;
		foreach($gtime as $year=>$years) {
			foreach($years as $month=>$months){
				foreach($months as $day=>$days){
						$timet[$year][$month][$day]+=$days;
				}
			}
		}
	}
	}
	//print_r($timet);
	$kned=0;
	$zap='';
	for($t=$order['order_start'];$t<$order['order_end'];$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		$params.= $zap.intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
		$zap=', ';
	}
	
	$params.='
	];
	var words=Array(
	';
	$zap='';
	foreach ($metrics['topwords'] as $word => $cco)
	{
		$k1++;
		if ($k1<2)
		{
			$params.=$zap.'{\'name\': \''.$word.'\', \'num\': 30}';
			$zap=',';
		}
		else
		if ($k1<5)
		{
			$params.=$zap.'{\'name\': \''.$word.'\', \'num\': 15}';
			$zap=',';
		}
		if ($k1<10)
		{
			$params.=$zap.'{\'name\': \''.$word.'\', \'num\': 12}';
			$zap=',';
		}
		if ($k1<30)
		{
			$params.=$zap.'{\'name\': \''.$word.'\', \'num\': 8}';
			$zap=',';
		}
		else
		{
			$params.=$zap.'{\'name\': \''.$word.'\', \'num\': 7}';
			$zap=',';
		}
	}
	$params.='
	);

';

start_tpl($jqready,$params1,$params);
//$html_out .='	<div id=\'table\'>';


//$fn = "/var/www/data/blog/".intval($order_id).".metrics";
//$h = fopen($fn, "r");
//$data = fread($h, filesize($fn));
//print_r($metrics);
//print_r($metrics['location']);
//fclose($h);
if ($order['order_engage']=='1')
{
	$engtext='Engage';
	$engvalue=$metrics['engagement'];
}
else
{
	$engtext='';
	$engvalue='';
}

$k=0;
$src_count=count($sources);
//print_r($sources);

$c_posts=$other-$metrics['location'][''];


if ($metrics['din_posts']=='NA')
{
	$text_din_p='';
}
else
{
	if ($metrics['din_posts']>0)
	{
		$text_din_p='+'.intval($metrics['din_posts']).'%';
	}
	else
	{
		$text_din_p=intval($metrics['din_posts']).'%';
	}
}
$count_cou=0;
$kk=0;
foreach ($metrics['location_cou'] as $key => $item)
{
	$kk++;
	if ($kk>4)
	{
		$count_cou+=$item;
	}
}
foreach ($sources as $i => $source)
{
		//$other+=$source;
}
if ($metrics['din_posts']=='NA')
{
	$text_din_p='';
}
else
{
	if ($metrics['din_posts']>0)
	{
		$text_din_p='+'.intval($metrics['din_posts']).'%';
	}
	else
	{
		$text_din_p=intval($metrics['din_posts']).'%';
	}
}
$engvalue=$metrics['engagement'];
//sprint_r($sources);
//$src_count=count($sources);

//echo $metrics['speakers']['uniq'];
/*
=================== new design
*/

	$html_out.='
	<form id="submform" action="'.$config['html_root'].'comment" method="POST">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<input type="hidden" name="ntime" value="'.date('d.m.Y',$order['order_start']).'">
		<input type="hidden" name="etime" value="'.date('d.m.Y',$order['order_end']).'">
		<input type="hidden" id="nnick" name="nnick" value="">
		<input type="hidden" id="nword" name="nword" value="">
	</form>
	<div class="span-24 last" id="content">
        <div class="row clear"></div>
        <div id="metrics" class="span-24 text-center text-black" >
            <div class="span-4">
                <p class="text-48">'.$other.'<p>
                <a class="dottedblack vtip" title="Найдено упоминаний по вашему запросу.">упоминаний</a>
            </div>
            <div class="span-4 prepend-1">
                <p class="text-48">'.intval($metrics['speakers']['uniq']).'<p>
                <p class="dottedblack vtip" title="Количество уникальных авторов по вашему запросу.">уникальных авторов</p>
            </div>
            <div class="span-4 prepend-1">
                <p class="text-48">'.$src_count.'<p>
                <a class="dottedblack vtip" title="Ресурсы, на которых есть упоминания по вашему запросу за все время.">ресурсов</a>
            </div>
            <div class="span-4 prepend-1">
                <p class="text-48">'.$metrics['value'].'<p>
                <a class="dottedblack vtip" title="Число людей, просмотревших упоминания по вашему запросу за все время.">аудитория</a>
            </div>
            '.(($order['order_engage']!=0)?'<div class="span-4 prepend-1 last">
                <p class="text-48">'.intval($engvalue).'<p>
                <a class="dottedblack vtip" title="Характеризует популярность упоминаний и частоту их цитирования.">вовлечённость</a>
            </div>':'').'
        </div>
        
        
        <div class="row clear"></div><hr/>
        
        
        <div class="rows-2 clear">
            <div class="span-7 ">
                    <a class="greenbtn span-7 last vtip" title="Просмотреть список упоминаний." href="#" onclick="document.getElementById(\'submform\').submit();">Просмотр упоминаний</a>
            </div>
            <div class="span-2 prepend-6">
                <p  class="top bottom text-18 vert-center rows-2">
                    Формат:
                  </p>
            </div>
			<form action="/new/export" target="_blank" id="filterform" method="POST">
            <div class="span-4 customselect">
                <select class="styled" name="format"/>
                  <option>Excel</option>
                  <option>Word</option>
                  <option>OpenOffice</option>
               </select>
            </div>
            <div class="span-5 last">
					<input type="hidden" name="order_id" value="'.$order['order_id'].'">
					<input type="hidden" name="ntime" value="'.date('d.m.Y',$order['order_start']).'">
					<input type="hidden" name="etime" value="'.date('d.m.Y',$order['order_end']).'">
                    <a class="greybtn span-5 last vtip" onclick="document.getElementById(\'filterform\').target=\'_blank\'; document.getElementById(\'filterform\').action=\'/new/export\'; document.getElementById(\'filterform\').submit(); document.getElementById(\'filterform\').target=\'\'; document.getElementById(\'filterform\').action=\'/new/comment\';" title="Экспортировать данные в выбранный вами формат.">Сформировать отчёт</a>
            </div>
			</form>
        </div>
        <div class="row clear"></div><div class="rows-2 clear"></div>
        
        
        <div id="maininfo" class="span-24">
            <h2>Распределение упоминаний</h2>
            <div class="row clear"></div>
            <div class="clear overflow">
            <div class="span-12" id="res_distr">
                <p class="text-24">по ресурсам</p>
                <div id="resourcespie" class="pie span-5"></div>
                
                <div class="span-7 last">
                    <div class="row clear"></div>
                <div class="row span-6 last text-lightgrey bold ">
                <p class="prepend-4 span-1">%</p>
                <p class="span-1 last">всего</p>
                <hr/>
                </div>
                    
                <div class="tablecontent span-7 last"> </div>
                            </div>
            </div>
            <div class="span-12 last" id="city_distr">
                <p class="text-24">по городам</p>
                <div id="citiespie" class="pie span-5"></div>
                 <div class="span-7 last">
                    <div class="row clear"></div>
                <div class="row span-6 last text-lightgrey bold ">
                <p class="prepend-4 span-1">%</p>
                <p class="span-1 last">всего</p>
                <hr/>
                </div>
                    
                <div class="tablecontent span-7 last"> </div>
                            </div>
            </div>
                </div>
            <!--<a class="greybtn span-5 right last">Мастер инфографики</a>-->
            <div class="rows-2 clear"></div>
            
            
            
            <div class="span-24" id="days_distr">
                <p class="text-24">по дням</p>
                <div class="row clear"></div>
                <div class="row clear">
                    <div class="date span-5 last">
                        <p class="span-1 last">c</p>
                        <input type="text" class="format-d-m-y divider-dot highlight-days-67 range-high-today span-3" name="sd" id="sd" value=""/>
                    </div>
                    <div class="date span-5 last">
                        <p class="span-1 last">по</p>
                        <input type="text" class="format-d-m-y divider-dot highlight-days-67 range-high-today span-3" name="ed" id="ed" value=""/>
                    </div>
                </div>
                <div id="graph" class="graph"></div>
            </div>
            <div class="row clear"></div>
            <!--<a class="greybtn span-5 right last">Мастер инфографики</a>-->
            <div class="rows-2 clear"></div>
            
            
            
            <div class="span-12" id="people_distr">
                <p class="text-24">по людям</p>
                <div class="span-12 last row clear"></div>
                <div class="span-6" id="speakers">
                    <h4 class="vtip" title="Люди, которые упоминают ваш запрос чаще всех остальных в социальных медиа.">Спикеры</h4>
                    <div class="text-black">
                    <div class="row span-6 last text-lightgrey bold">
                    <p class="span-1 text-right">№</p>
                    <p class="span-3">ник</p>
                    <p class="span-1 last">постов</p>
                    </div>
                        <div class="tableheaderborder clear"></div>
                    <div class="tablecontent"> </div>
                        <div class="prepend-1 span-3  text-lightgrey clear fancypopup">
                      '.((count($metrics['speakers']['nick'])>10)?'<!--<a class="showall" href="#speakersshowall">--><a href="#" onclick="loadmodal(\'/new/othrr?res=speakers&order_id='.$order['order_id'].'\',290,470); return false;">показать всех</a>':'').'
                    </div>
                    </div>
                </div>
                <div class="span-6 last" id="promouters">
                    <h4 class="vtip" title="Люди, которые упоминают ваш бренд и имеют большой круг друзей или читателей.">Промоутеры</h4>
                    <div class="text-black">
                    <div class="row span-6 last text-lightgrey bold ">
                    <p class="span-1 text-right">№</p>
                    <p class="span-3">ник</p>
                    <p class="span-1 last">читателей</p>
                    
                    </div>
                    <div class="tableheaderborder clear"></div>
                    <div class="tablecontent"> </div>
                    <div class="prepend-1 span-3  text-lightgrey clear fancypopup">
					  '.((count($metrics['promotion']['nick'])>10)?'<!--<a class="showall" href="#promoutersshowall">--><a href="#" onclick="loadmodal(\'/new/othrr?res=promotion&order_id='.$order['order_id'].'\',290,470); return false;">показать всех</a>':'').'
                    </div>
                    
                    </div>
                </div>
            </div>
            
            
            
            <div class="prepend-1 span-11 last " id="words_distr">
                <p class="text-24 vtip" title="Самые, больше всего используемые слова.">по словам</p>
                <div class="row clear"></div>
                <!--<img src="/img/images/tagcloud.jpg"/>-->
				<div id="tagcloud" class="tagcloud">';
				//print_r($metrics['topwords']);
				$i=0;
				foreach ($metrics['topwords'] as $word => $cco)
				{
					$i++;
					if ($i<50)
					{
						if ($cco>100)
						{
							for ($j=0;$j<intval($cco/100)+1;$j++)
							{
								$html_out.=$word.$point;
								$point=' . ';
							}
						}
						else
						{
							for ($j=0;$j<intval($cco/10)+1;$j++)
							{
								$html_out.=$word.$point;
								$point=' . ';
							}
						}
					}
				}
				$html_out.='
				            </div>
                <div class="fancypopup"><!--<a class="text-lightgrey right" href="#wordsshowall">--><a href="#" style="color: #999;" onclick="loadmodal(\'/new/othrr?res=words&order_id='.$order['order_id'].'\',290,470); return false;">посмотреть всё</a></div>
                
            </div>
            
            
            
            
            
            
            <div class=\'hide\'>
                         <div id="promoutersshowall" class="span-7 last">
                             <h4>Промоутеры</h4>
                             <div class="row clear"></div>
                            <div class="text-black">
                            <div class="row span-6 last text-lightgrey bold ">
                            <p class="span-1 text-right">№</p>
                            <p class="span-3">ник</p>
                            <p class="span-1 last">читателей</p>

                            </div>
                            <div class="tableheaderborder clear"></div>
                            <div class="tablecontent span-7 last scroll"> </div>
                         </div>
                             <div class="row clear"></div>
                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" id="copy-button2">копировать в буфер</a></div>
                      </div>
             </div>
            
            <div class=\'hide\'>
                         <div id="speakersshowall" class="span-7 last">
							<script type="text/javascript">

							$("#copy-button2").zclip({
							    path: "http://bmstu.wobot.ru/new_js/js/ZeroClipboard.swf",
								afterCopy: function(){ return false; },
							    copy: function(){
								return mprom;
								}
							});
							function al()
							{
								alert(\'123\');
								$("#copy-button1").zclip({
								    path: "http://bmstu.wobot.ru/new_js/js/ZeroClipboard.swf",
									afterCopy: function(){ return false; },
								    copy: function(){
									return mspeak;
									}
								});
							}
							</script>
                             <h4 class="span-3">Спикеры</h4>
                             <div class="row clear"></div>
                            <div class="text-black">
                            <div class="row span-6 last text-lightgrey bold ">
                            <p class="span-1 text-right">№</p>
                            <p class="span-3">ник</p>
                            <p class="span-1 last">постов</p>
                            </div>
                            <div class="tableheaderborder clear"></div>
                            
                            <div class="tablecontent span-7 last scroll"> </div>
                              </div>
                             <div class="row clear"></div>

                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" id="copy-button1" onclick="al();">копировать в буфер</a></div>
                      </div>
             </div>
            
            <div class=\'hide\'>
                         <div id="wordsshowall" class="span-7 last">
                             <h4>Слова</h4>
                             <div class="row clear"></div>
                            <div class="text-black">
                            <div class="row span-6 last text-lightgrey bold ">
                            <p class="span-1 text-right">№</p>
                            <p class="span-3">слово</p>
                            <p class="span-2 last">кол-во</p>

                            </div>
                            
                            <div class="tableheaderborder clear"></div>
                            <div class="tablecontent span-7 last scroll"> </div>
                         </div>
                             <div class="row clear"></div>
                             <div class="row clear"><a class="span-7 last text-right text-lightgrey">копировать в буфер</a></div>
                      </div>
             </div>
            
        </div>
    </div>
      <a href="#" id="gg" style="display: none;">gg</a>
	
      
    
';
stop_tpl();

?>
