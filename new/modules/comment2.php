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

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");


$db = new database();
$db->connect();
//print_r($_POST);
auth();
if (!$loged) die();
//print_r(json_decode(urldecode($_POST['tag_links']),true));
//loading metrics
if (intval($_POST['order_id'])==0) die();
$fn = "/var/www/new/data/blog/".$_POST['order_id'].".metrics";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
$metrics=json_decode($data,true);
//print_r($metrics);
fclose($h);


//head text with description of report master form
if ($_POST['showmode']=="notspam")
{
	$mode="без спама";
}
elseif ($_POST['showmode']=="showfav")
{
	$mode="избранные";
}
elseif ($_POST['showmode']=="onlyspam")
{
	$mode="только спам";
}
elseif ($_POST['showmode']=="showall")
{
	$mode="все";
}

if ($_POST['unrep']=='on')
{
	$mode1="без дублей";
}
else
{
	$mode1="с дублями";
}

if ($_POST['markt']=="true")
{
	$type[]="социальные сети";
}
if ($_POST['markt1']=="true")
{
	$type[]="новостные ресурсы";
}
if ($_POST['markt2']=="true")
{
	$type[]="микроблоги";
}
if ($_POST['markt3']=="true")
{
	$type[]="форумы и блоги";
}

$type_text="";
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

if (isset($_POST['hashq']))
{
	$type_text=$_SESSION[$_POST['hashq'].'_ttext'];
	$mode1=$_SESSION[$_POST['hashq'].'_mode1'];
	$mode=$_SESSION[$_POST['hashq'].'_mode'];
}
if ($_POST['snick']!='')
{
	$_POST['ntime']=date('d.m.Y',$_POST['ntime']);
	$_POST['etime']=date('d.m.Y',$_POST['etime']);
}

start_tpl('','<link href=\'/css/list_lk.css\' rel=\'stylesheet\' type=\'text/css\' />');
$html_out .='
     <div id=\'top\'>
	  <div class=\'top_line\'>
	    <span class=\'date\'>
	      Дата: с '.((isset($_POST['hashq']))?$_SESSION[$_POST['hashq'].'_ntime']:date('d.m.Y',strtotime($_POST['ntime']))).' по '.((isset($_POST['hashq']))?$_SESSION[$_POST['hashq'].'_etime']:date('d.m.Y',strtotime($_POST['etime']))).'
	    </span>
	    <ul class=\'controls\'>
	      <li class=\'no_bullet\'>
	        <a href=\'#\'>'.$mode.'</a>
	      </li>
	      <li class=\'no_bullet\'>
	        '.$mode1.'
	      </li>
	      <li class=\'no_bullet\'>
	        '.$type_text.'
	      </li>
	    </ul>
	  </div>
	  <div class=\'bot_line\'>
	    <span>
	      Найдены <div style="display: inline;" id="colup"></div> на <div style="display: inline;" id="colup1"></div>.
	    </span>
	  </div>
	</div>
	<script type="text/javascript">
	hs.graphicsDir = \'/img/graphics/\';
	hs.outlineType = \'rounded-white\';
	hs.wrapperClassName = \'draggable-header\';
	</script>
	<script type="text/javascript">
	function openNewWindow(_this)
	{
		var w = (window.innerWidth / 100) * 80;
		var h = (window.innerHeight / 100) * 80;
		hs.minWidth = 300;/*w;*/
		hs.minHeight = 200;/*h;*/
		hs.lang.creditsText = _this.title;
		hs.creditsHref = "#";

	    var result = hs.htmlExpand(_this, { objectType: \'iframe\' } );
	    return result;
	}
	</script>
	
	<div id=\'table\'>
';

$time_start = microtime(true);
$gn_time = 0;

unset($resorrr);
foreach ($_POST as $inddd => $posttt)
{
	if ((substr($inddd, 0, 4)=='res_'))
	{
		if ($posttt=='true')
		{
			//str_replace("_",".",$inddd);
			$resorrr[]=str_replace("_",".",substr($inddd,4));
			$resorrr1[str_replace("_",".",substr($inddd,4))]=1;
		}
	}
}
//print_r($resorrr);
foreach ($_POST as $indddloc => $postttloc)
{
	if ((substr($indddloc, 0, 4)=='loc_'))
	{
		if ($indddloc!='loc_othr')
		{
			//str_replace("_",".",$inddd);
			$loc[]=str_replace('_',' ',substr($indddloc,4));
		}
	}
}
//print_r($loc);
foreach ($_POST as $inddd1 => $posttt1)
{
	if ((substr($inddd1, 0, 2)=='tg'))
	{
		$tgv[]=$posttt1;
	}
}
//print_r($tgv);

if ($_POST['positive']==''&&$_POST['negative']==''&&$_POST['neutral']=='')
{
	$_POST['positive']='true';
	$_POST['negative']='true';
	$_POST['neutral']='true';
}

$mastexttype=array('','Не важно','Средне','Важно','Очено важно');

if (intval($_POST['order_id'])!=0)
{
			$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1"); // проверка order_id

		if (mysql_num_rows($res)==0) die();
			$order = $db->fetch($res);

$fn = "/var/www/new/data/blog/".intval($order_id).".src";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
//$sources=json_decode($data, true);
$sources=json_decode($order['order_src'], true);
//print_r($sources);
$metrics=json_decode($order['order_metrics'],true);
//print_r($metrics);
$kkey=0;
if ($_POST['res_other']==true)
{
	foreach ($sources as $nname => $inddex)
	{
		$kkey++;
		if ($kkey>10)
		{
			$resorrr[]=$nname;
		}
	}
}
$colres=count($resorrr);


$colcom=0;
$colres=0;


if ($_POST['hashq']=='')
{
	$where=get_isshow2();
	if (($_POST['snick']=='') && ($_POST['sword']==''))
	{
   		$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' ORDER BY p.post_time DESC LIMIT 10';
   		$querycount='SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' GROUP BY post_host ORDER BY p.post_time DESC';
   		$que1='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' ORDER BY p.post_time DESC';
		$_SESSION[md5($que1)]=$que1;
	    $respost=$db->query($query);
	    $respost2=$db->query($querycount);
		while ($pstcount = $db->fetch($respost2))
		{
			if ($mode1!='с дублями')
			{
				$pscount+=1;
			}
			else
			{
				$pscount+=$pstcount['cnt'];
				$mmres_c++;
			}
		}
		//print_r($mmres_c);
		//echo $pscount.'gg';
		//echo $querycount;
  		$_SESSION[md5($que1).'_count']=$pscount;
		$_SESSION[md5($que1).'_ttext']=$type_text;
		$_SESSION[md5($que1).'_mode1']=$mode1;
 		$_SESSION[md5($que1).'_mode']=$mode;
		$_SESSION[md5($que1).'_ntime']=date('d.m.Y',strtotime($_POST['ntime']));
		$_SESSION[md5($que1).'_etime']=date('d.m.Y',strtotime($_POST['etime']));
		//echo '<br><br>'.$query.'<br><br>';
   		//die();	
	}
	elseif ($_POST['sword']!='')
	{
		$_POST['sword']=urldecode($_POST['sword']);
		//$_POST['sword']=preg_replace('/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|ов|й|и|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|ь|я)$/isu','',$_POST['sword']);
		//echo $_POST['sword'];
   		$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" ORDER BY p.post_time DESC';
   		$querycount='SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" ORDER BY p.post_time DESC';
   		$que1='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" ORDER BY p.post_time DESC';
		$_SESSION[md5($que1)]=$que1;
	    $respost=$db->query($query);
	    $respost2=$db->query($querycount);
		$pstcount = $db->fetch($respost2);
		$pscount=$pstcount['cnt'];
		$_SESSION[md5($que1).'_count']=$pscount;
		$_SESSION[md5($que1).'_ttext']=$type_text;
		$_SESSION[md5($que1).'_mode1']=$mode1;
		$_SESSION[md5($que1).'_mode']=$mode;
		$_SESSION[md5($que1).'_ntime']=date('d.m.Y',strtotime($_POST['ntime']));
		$_SESSION[md5($que1).'_etime']=date('d.m.Y',strtotime($_POST['etime']));
		$_SESSION[md5($que1).'_sword']=$_POST['sword'];
		$_SESSION[md5($que1).'_scount']=$_POST['scount'];
	}
	else
	{
		//if (in_array($_POST['snick'],$metrics['speakers']['nick']))
		{
			$_POST['snick']=preg_replace('/\-\-/is','',$_POST['snick']);
			$_POST['snick']=preg_replace('/\s/is','',$_POST['snick']);
   			$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id='.intval($_POST['order_id']).' AND b.blog_login=\''.$_POST['snick'].'\' ORDER BY p.post_time DESC LIMIT 10';
   			$querycount='SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id='.intval($_POST['order_id']).' AND b.blog_login=\''.$_POST['snick'].'\' ORDER BY p.post_time DESC';
   			$que1='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id='.intval($_POST['order_id']).' AND b.blog_login=\''.$_POST['snick'].'\' ORDER BY p.post_time DESC';
			$_SESSION[md5($que1)]=$que1;
	    	$respost=$db->query($query);
	    	$respost2=$db->query($querycount);
			$pstcount = $db->fetch($respost2);
			$pscount=$pstcount['cnt'];
			$_SESSION[md5($que1).'_count']=$pscount;
			$_SESSION[md5($que1).'_ttext']=$type_text;
			$_SESSION[md5($que1).'_mode1']=$mode1;
   			$_SESSION[md5($que1).'_mode']=$mode;
			$_SESSION[md5($que1).'_ntime']=date('d.m.Y',strtotime($_POST['ntime']));
			$_SESSION[md5($que1).'_etime']=date('d.m.Y',strtotime($_POST['etime']));
			//echo '<br><br>'.$query.'<br><br>';
   			//die();
		}
	}
}
else
{
	if (isset($_SESSION[$_POST['hashq'].'_sword']))
	{
		$_POST['sword']=$_SESSION[$_POST['hashq'].'_sword'];
	}
	$pscount=$_SESSION[$_POST['hashq'].'_count'];
	if ($_POST['sword']=='')
	{
		$quer=$_SESSION[$_POST['hashq']].' LIMIT '.(intval($_GET['p']-1)*10).',10';
	}
	else
	{
		$quer=$_SESSION[$_POST['hashq']];
	}
    $respost=$db->query($quer);
	$type_text=$_SESSION[$_POST['hashq'].'_ttext'];
	//echo $quer;
}
//print_r($_SESSION);
//session_destroy();
//echo $_POST['sword'];
//echo $query;
//print_r($_SESSION);
if ($_POST['scount']=='')
{
	$all=intval($pscount/10);
	if (($pscount % 10) != 0)
	{
		$all+=1;
	}
}
else
{
	//print_r($_POST);
	//echo 'gg';
	$all=intval($_POST['scount']/10);
	if (($_POST['scount'] % 10) != 0)
	{
		$all+=1;
	}
}
if (isset($_SESSION[$_POST['hashq'].'_scount']))
{
	$all=intval($_SESSION[$_POST['hashq'].'_scount']/10);
	if (($_SESSION[$_POST['hashq'].'_scount'] % 10) != 0)
	{
		$all+=1;
	}
}
//print_r($_SESSION);
//print_r($metrics['speakers']);
$query1='SELECT * FROM blog_tag WHERE user_id='.$user['user_id'];
   $respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
}


/*
$user['user_id']=$order['user_id'];
$order_id=$order['order_id'];
$res11=$db->query("SELECT * from blog_orders WHERE order_id=".$order_id." and user_id=".intval($user['user_id'])." LIMIT 1");
if (mysql_num_rows($res11)==0) die();
$order11 = $db->fetch($res11);
$order11['order_start']=intval($order11['order_start']);
$order11['order_end']=((intval($order11['order_end'])==0)||(intval($order11['order_end'])>mktime(0,0,0,date('m'),date('d')-1,date('Y'))))?(mktime(0,0,0,date('m'),date('d'),date('Y'))):intval($order11['order_end']+60*60*24);
$res_tag11=$db->query("SELECT * from blog_tag WHERE user_id=".intval($user['user_id']));
while ($order_tag11 = $db->fetch($res_tag11))
{
	$mtags[$order_tag11['tag_tag']]=$order_tag11['tag_name'];
}
$data=$order11['order_metrics'];
$metrics=json_decode($data,true);
$data=$order11['order_src'];
$sources=json_decode($data, true);
$k=0;
$src_count=count($sources);
$other=-1;
foreach ($sources as $i => $source)
{
		$other+=$source;
}
$c_posts=$other-$metrics['location'][''];

$count_cou=0;
$kk=0;
arsort($metrics['location_cou']);
//print_r($metrics['location_cou']);
foreach ($metrics['location_cou'] as $key => $item)
{
	$kk++;
	if ($kk>4)
	{
		$count_cou+=$item;
	}
}
$order_id=$order['order_id'];
//echo $_POST['mtags'];
//echo json_encode($mtags);
//echo $order_id;
//print_r($_GET);
//echo json_decode($_GET,true);
$html_out.= '			<div class=\'filterbox ui-corner-all filterbox-show\' id="masterfrm"><b><u><a href="#" id="masterbtn">Мастер отчетов</a></u></b>
			<div class=\'filterbox ui-corner-all filterbox-show\'>
			<form action="/new/comment" method="post" id="filternameform" target="_blank">
				<input type="hidden" name="order_id" value="'.$order_id.'">
				<input type="hidden" id="nname" name="snick" value="">
				<input type="hidden" id="nword" name="sword" value="">
			</form>
			<form action="/new/comment" method="post" id="filterform">
			<input type="hidden" name="tag_links" value="'.urlencode(json_encode($mtags)).'">
			<input type="hidden" name="order_id" value="'.$order_id.'">
		<table>
			<tr>
				<td width="120"><img src="/img/post.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Возможно выбрать упоминания без спама и рекламы.<br>Для добавления сообщения в избранное нужно перейти в меню просмотра.">Сообщения</font></font></td>
				<td style="font-size: 11px;">
					<table cellspacing="0" cellpadding="0">
						<tr>
						<td><label for="izb" tabindex="1"></label><input id="izb" type="radio" name="showmode" value="showfav"/> избранные&nbsp;</td>
						<td><label for="spm" tabindex="1"></label><input id="spm" type="radio" name="showmode" value="notspam"/> без спама&nbsp;</td>
						<td><label for="ospm" tabindex="1"></label><input id="ospm" type="radio" name="showmode" value="onlyspam"/> только спам&nbsp;</td>
						<td><label for="ol" tabindex="1"></label><input id="ol" type="radio" name="showmode" value="showall" checked="true"/> все&nbsp;</td><br>
						</tr>
					</table>
					<table cellspacing="0" cellpadding="0">
						<tr>
						<td><label for="dub" tabindex="1"></label><input id="dub" type="checkbox" name="unrep" class="rescheck"/> фильтровать дубли&nbsp;</td>
						<td><select width="70px" name="os" class="styled">
							<option value="all" SELECTED>Все категории</option>
							<option value="1">Не важно</option>
							<option value="2">Средне</option>
							<option value="3">Важно</option>
							<option value="4">Очень важно</option>
						</select></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td><img src="/img/time.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите временной период, за который вы хотите исследовать упоминания или используйте стандартные временные периоды.">Время</font></div></td>
				<td style="font-size: 11px;">с <input name="ntime" id="ntime" value="'.date('d.m.Y',$order['order_start']).'" type="text"> по <input name="etime" id="etime" value="'.((intval($order['order_end']>time())||(intval($order['order_end'])==0))?date('d.m.Y',mktime(0,0,0,date('n'),date('j')-1,date('Y'))):date('d.m.Y',$order['order_end']-60*60*24)).'" type="text"> <b>или</b> <a href="#" onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">день</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-691200000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">неделя</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-2678400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">месяц</a></td>
			</tr>
			<tr>
				<td><img src="/img/nastr.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите эмоциональную окраску мнений.">Тональность</font></div></td>
				<td style="font-size: 11px;">
				<table cellspacing="0" cellpadding="0">
					<tr>
					<td><label for="neu" tabindex="1"></label><input id="neu" type="checkbox" checked="checked" name="neutral" class="rescheck"/> нейтральные&nbsp;</td>
					<td><label for="pol" tabindex="1"></label><input id="pol" type="checkbox" checked="checked" name="positive" class="rescheck"/> положительные&nbsp;</td>
					<td><label for="otr" tabindex="1"></label><input id="otr" type="checkbox" checked="checked" name="negative" class="rescheck"/> отрицательные&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>';
			if (count($mtags)==0)
			{
				$deltt='';
			}
			else
			{
				$deltt='<a href="#" class="vtip" title="Удалить тег" onclick="loadmodal1(\'/deletetagsmain?orid='.intval($user['user_id']).'&oorid='.$order_id.'\',604,'.(200+count($mtags)*30).');return false;"><img src="/img/Button-Close-icon.png"></a>';
			}
			if (count($mtags)>8)
			{
				$addtt='';
			}
			else
			{
				$addtt='<a href="#" class="vtip" title="Добавить тег" onclick="loadmodal1(\'/addtagsmain?orid='.$order_id.'&tags='.urlencode(json_encode($mtags)).'\',604,250);return false;"><img src="/img/Button-Add-icon.png"></a>&nbsp;<a href="#" class="vtip" title="Редактировать тег" onclick="loadmodal1(\'/edittagsmain?orid='.intval($user['user_id']).'&tags='.urlencode(json_encode($mtags)).'\',604,'.(200+count($mtags)*30).');return false;"><img src="/img/edit-icon.png"></a>';
			}
			$addtt='';
			$deltt='';
			$html_out.='
				<td><img src="/img/tag-icon.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите теги, которые вы хотели бы отобразить в выдаче.">Теги</font></div>&nbsp;'.$addtt.'&nbsp;'.$deltt.'</td>
				<td style="font-size: 11px;"><table cellspacing="0" cellpadding="0"><tr>';
				foreach ($mtags as $key => $item)
				{
					$ti++;
					$html_out.='<td width="120"><label for="tgp'.$key.'" tabindex="1"></label><input id="tgp'.$key.'" type="checkbox" checked="checked" name="tg'.$key.'" value="'.$key.'"> '.$item.'&nbsp;</td>';
					if ($ti % 4 == 0)
					{
						$html_out.='</tr><tr>';
					}
				}
				$html_out.='<td width="120"><label for="tgpn" tabindex="1"></label><input id="tgpn" type="checkbox" checked="checked" name="tgn" value="na"> Без тегов&nbsp;</td>';
				$html_out.='</tr></table></td>
			</tr>
			<tr>
				<td valign="top"><img src="/img/sources.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите ресурсы, по которым хотите посмотреть выдачу.">Ресурсы</font></div></td>
				<td style="font-size: 11px;">
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<label for="resch11" tabindex="1" onclick="if($(\'#resch11\').is(\':checked\')) {checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.u_ssoc\');} else { uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.u_ssoc\');}">социальные сети&nbsp;</label><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick=" if($(\'#resch11\').is(\':checked\')) { checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.u_ssoc\');} else {uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.u_ssoc\');} " value="социальные сети"/>
							</td>
							<td>
								<label for="resch12" tabindex="1" onclick="if($(\'#resch12\').is(\':checked\')) {uncheckPrettyCb(\'.novres\'); uncheckPrettyCb(\'.u_novres\');} else { checkPrettyCb(\'.novres\'); checkPrettyCb(\'.u_novres\');}">новостные ресурсы&nbsp;</label><input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="uecheckAllPC(\'.novres\'); uacheckPrettyCb(\'.u_novres\'); return false;"/>
							</td>
							<td>
								<label for="resch13" tabindex="1" onclick="if($(\'#resch13\').is(\':checked\')) {uncheckPrettyCb(\'.microb\'); uncheckPrettyCb(\'.u_microb\');} else { checkPrettyCb(\'.microb\'); checkPrettyCb(\'.u_microb\');}">микроблоги&nbsp;</label><input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="uecheckAllPC(\'.microb\'); uacheckPrettyCb(\'.u_microb\'); return false;"/>
							</td>
							<td>
								<label for="resch14" tabindex="1" onclick="if($(\'#resch14\').is(\':checked\')) {uncheckPrettyCb(\'.forabl\'); uncheckPrettyCb(\'.u_forabl\');} else { checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_forabl\');}">форумы и блоги</label><input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="uecheckAllPC(\'.forabl\'); uacheckPrettyCb(\'.u_forabl\'); return false;"/>
							</td>
						</tr>
					</table>
					';

				if ($order_id!=0)
				{
					$sourcesgr=$sources;
					unset($sources);
					//$fn = "/var/www/data/blog/".intval($order_id).".src";
					//$h = fopen($fn, "r");
					//$data = fread($h, filesize($fn));
					$data=$order['order_src'];
					//print_r($sourcesgr);
					$sources=json_decode($data, true);

					//$data=$order['order_src'];
					//$sources=json_decode($data, true);

					//$iii=0;
					arsort($metrics['location_cou']);
					$sources2=array_slice($sources, 10, -1);
					uksort($sources2,"strcoll");
					//print_r($sources2);
					$sources=array_slice($sources, 0, 10);

					$sources=array_merge($sources, $sources2);


					$other=0;
					$k=0;
					$html_out.='<table cellspacing="0" cellpadding="0"><tr>';
					//$sources = array_multisort($sources,);
					//ksort($sources);
					foreach ($sources as $i => $source)
					{
						$k++;
						if ($k<10)
						{
							if (($i=="facebook.com") || ($i=="vkontakte.ru"))
							{
								$html_out.='<td><label for="ssoc'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
							}
							else
							if ($i=="mail.ru")
							{
								$html_out.='<td><label for="novres'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
							}
							else
							if (($i=="twitter.com") || ($i=="rutvit.ru"))
							{
								$html_out.='<td><label for="microb'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
							}
							else
							{
								$html_out.='<td><label for="forabl'.$k.'"></label><input type="checkbox" checked="checked" name="res_'.$i.'" class="forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;</td>';
							}
							if (($k%4)==0) $html_out.= '</tr><tr>';
							$socialall+=$source;
						}
						else
						{
							if ($k==10)
							{
								$html_out1.='<div id="othr" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 360px; left: 400px; background-color: #FFFFFF;"><!--<input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="rescheck1" id="resch1" value="true"/> '.$i.' ('.$source.')&nbsp;--><table><tr>';
								if (($i=="facebook.com") || ($i=="vkontakte.ru"))
								{
									$html_out1.='<td><label for="ssoc'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								else
								if ($i=="mail.ru")
								{
									$html_out1.='<td><label for="novres'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								else
								if (($i=="twitter.com") || ($i=="rutvit.ru"))
								{
									$html_out1.='<td><label for="microb'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								else
								{
									$html_out1.='<td><label for="forabl'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								$socialall+=$source;
							}
							else
							{
								if (($i=="facebook.com") || ($i=="vkontakte.ru"))
								{
									$html_out1.='<tr><td><label for="ssoc'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_ssoc" id="ssoc'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								else
								if ($i=="mail.ru")
								{
									$html_out1.='<tr><td><label for="novres'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_novres" id="novres'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								else
								if (($i=="twitter.com") || ($i=="rutvit.ru"))
								{
									$html_out1.='<tr><td><label for="microb'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_microb" id="microb'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								else
								{
									$html_out1.='<tr><td><label for="forabl'.$k.'"></label><input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="u_forabl" id="forabl'.$k.'" value="true"/> '.$i.' ('.$source.')&nbsp;<br></td></tr>';
								}
								$socialall+=$source;
							}
							$other+=$source;
						}
					}
					if ($k>10) $html_out1.='</table></div>';
					$socialother=$other;//<input type="checkbox" checked="checked" name="res_other" class="rescheck" value="true" />
					$html_out.='<td><label for="op" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}"></label><input type="checkbox" class="rescheck" id="op" value="true" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}" checked="checked"/><a id="lothr" class="vtip" title="Ресурсов: '.(count($sources)-9).'" href="#" onclick="$(\'#othr\').toggle(); return false;">другие</a> ('.$other.')&nbsp;</td>
	';
					$html_out.='</tr></table>';
					$html_out.=$html_out1;
					fclose($h);
				}		
				$html_out.='<div id="lothr1" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 400px; left: 220px; background-color: #FFFFFF;"><table>';		
				$ii=0;
				arsort($metrics['location']);
				foreach ($metrics['location'] as $k => $item)
				{
					if ($k!='')
					{
						$ii++;
						$html_out.='<tr><td><label for="loc'.$ii.'"></label><input type="checkbox" class="cou_'.$wobot['destn3'][$k].'" style="margin: 2px;" name="loc_'.$k.'" checked="checked" id="loc'.$ii.'" value="true"/> '.$k.' ('.$item.')&nbsp;<br></td></tr>';
					}
				}
				$html_out.='<tr><td><label for="locothr"></label><input type="checkbox" style="margin: 2px;" class="cou_na" name="loc_othr" checked="checked" id="locothr"/> Неопределено ('.$metrics['location'][''].')&nbsp;<br></td></tr>';
$html_out.='</table></div><table style="margin-top: 5px;" cellspacing="0" cellpadding="0"><tr>';
				$ii=0;
				$chall.=' checkPrettyCb(\'.cc_cou_na\');';
				$unchall.=' uncheckPrettyCb(\'.cc_cou_na\');';
				foreach ($metrics['location_cou'] as $kk => $itt)
				{
					$chall.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
					$unchall.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
					if ($kk!='')
					{
						$ii++;
						if ($ii<4)
						{
							if (($ii % 4) == 0)
							{
								$html_out.='</tr><tr>';
							}
							$html_out.='<td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td>';
						}
						elseif ($ii==4)
						{
							$html_out.='<td><div id="lothr5" style="display:none; min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 380px; left: 560px; background-color: #FFFFFF;"><table cellspacing="0" cellpadding="0"><tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';		
							$challoth.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
							$unchalloth.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
						}
						elseif ($ii>4)
						{
							$html_out.='<tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';
							$challoth.=' checkPrettyCb(\'.cc_cou_'.$kk.'\'); checkPrettyCb(\'.cou_'.$kk.'\');';
							$unchalloth.=' uncheckPrettyCb(\'.cc_cou_'.$kk.'\'); uncheckPrettyCb(\'.cou_'.$kk.'\');';
						}
					}
				}
				if (count($metrics['location_cou'])>4)
				{
					$html_out.='</table></div></td><td><label for="op1" onclick="if($(\'#op1\').is(\':checked\')) {'.$challoth.'} else { '.$unchalloth.'}"></label><input type="checkbox" class="rescheck" id="op1" value="true" onclick="if($(\'#op1\').is(\':checked\')) {'.$challoth.'} else {'.$unchalloth.'}" checked="checked"/><a href="#" onclick="$(\'#lothr5\').toggle(); return false;">Другие</a>&nbsp;('.$count_cou.')</td>';
				}
				$html_out.='<td><label for="loc_countna" onclick="if(!$(\'#loc_countna\').is(\':checked\')) {checkPrettyCb(\'.cou_na\');} else { uncheckPrettyCb(\'.cou_na\');}"></label><input type="checkbox" style="margin: 2px;" checked="checked" name="loc_othr1" class="cc_cou_na" id="loc_countna"/>Неопределено ('.$metrics['location_cou'][''].')</td></tr></table><a href="#" id="locopen">Города</a><br>';
	$html_out.=				'
<!--<br><br><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'ssoc\') { if ($(\'#resch11\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>социальные сети&nbsp;<input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'novres\') { if ($(\'#resch12\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>новостные ресурсы&nbsp;<input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'microb\') { if ($(\'#resch13\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>микроблоги&nbsp;<input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'forabl\') { if ($(\'#resch14\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>форумы и блоги--><a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/new/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&unch=1&com2=1\',function(){ afterunch(); afterunch1();}); afterunch(); return false;">снять все</a> <a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/new/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&ch=1&com2=1\',function(){ afterunch(); afterunch1();}); checkPrettyCb(\'.rescheck2\'); checkPrettyCb(\'.rescheck\'); checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.novres\'); checkPrettyCb(\'.microb\'); checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\'); '.$chall.'afterunch(); return false;">отметить все</a><a href="#" style="font-size: 12px;" onclick="$(\'input\').each(function(){ if($(this).attr(\'id\')==(\'blogs\')) {$(this).attr(\'checked\',\'checked\');}});"></a><br>Экспорт в формате: <select name="format" class="styled"><option value="excel">Excel</option><option value="word">Word</option><option value="openoffice">OpenOffice</option></select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right"><img src="/img/show.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\''.$config['html_root'].'comment2\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Список мнений по заданным критериям появится в новой вкладке.">Показать</font></a> <img src="/img/pdf.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/export\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Сохранить отчет в формате Word.">Экспорт</font></a></td>
			</tr>
			</table>
			</form>
			</div></div>';*/







$html_out .= '
	<script language="javascript">
	function arterunch1(){
		Custom.init;
	}
	function afterunch(){
		window.onload();
		uncheckPrettyCb = function(caller) {
		$(caller).each(function(){
			if($(this).is(\':checked\')){
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'checked\');
					}else{
						$(this).trigger(\'click\');
					};
			};
		});
		};

		checkPrettyCb = function(caller) {
		$(caller).each(function(){
			if($(this).is(\':checked\')){
			}else{
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'\');
					}else{
						$(this).trigger(\'click\');
					};
			};
			});
		};
		uecheckAllPC = function(caller){
			if($(caller).is(\':checked\')){
				$(caller).each(function(){
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'checked\');
					}else{
						$(this).trigger(\'click\');
					};
				});
			}else{
				$(caller).each(function(){
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'\');
					}else{
						$(this).trigger(\'click\');
					};
				});
			};
		};
		var dates = $( "#ntime, #etime" ).datepicker({
			dateFormat: "dd.mm.yy",
			defaultDate: "'.date("d.m.Y",$order['order_end']).'",
			minDate: "'.date("d.m.Y",$order['order_start']).'",
			maxDate: "'.date("d.m.Y",$order['order_end']).'",
			onSelect: function( selectedDate ) {
				var option = this.id == "ntime" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			} 
		});
		$(\'input[type=checkbox],input[type=radio]\').prettyCheckboxes();
		$( "#masterbtn" ).click(function() {
			$( "#masterfrm" ).toggleClass( "filterbox-show");
			$( ".filterbox-contents").toggleClass( "filterbox-contents-show");
			if ($( ".filterbox-contents").attr("class")=="filterbox-contents filterbox-contents-show")
			{
				$.cookie("masbtn", "1");
			}
			else
			{
				$.cookie("masbtn", "0");
			}
			return false;
		});
		$(\'#lothr\').click(function() {
			/*if (c==0)
			{
				$(\'#othr\').fadeOut(\'normal\');
			    c=1;
			}
			else
			{
			  	$(\'#othr\').fadeIn(\'normal\');
			 	c=0;
			}*/
			$(\'#othr\').toggle(\'fast\', function() {
		    // Animation complete.
		  	});
		    return false;
		});	
		$(\'#locopen\').click(function() {
			/*if (c==0)
			{
				$(\'#othr\').fadeOut(\'normal\');
			    c=1;
			}
			else
			{
			  	$(\'#othr\').fadeIn(\'normal\');
			 	c=0;
			}*/
			$(\'#lothr1\').toggle(\'fast\', function() {
		    // Animation complete.
		  	});
		    return false;
		});
		$(\'#othr\').mouseleave(function() {
		  $(\'#othr\').fadeOut(\'normal\');
		  c=1;
		});
		$(\'#lothr1\').mouseleave(function() {
		  $(\'#lothr1\').fadeOut(\'normal\');
		  c=1;
		});
		$(\'#lothr5\').mouseleave(function() {
		  $(\'#lothr5\').fadeOut(\'normal\');
		  c=1;
		});
		vtip();
		
	}
	</script>
	<div class="tagpop" id="othr2" style="min-width: 200px; min-height: 200px; overflow:auto; display: none; z-index: 999; width: 200px; height: 200px; border: 1px solid black; position: absolute; /*top: 100px;*/ left: 880px; background-color: #FFFFFF;"><iframe src="/new/tag" id="ifr" width="200" height="200" align="left"></iframe></div>
	<div class="vfull" id="othr5" style="/*min-width: 500px; min-height: 100px;*/ overflow:auto; display: none; z-index: 999; /*width: 500px; height: 200px;*/ border: 1px solid black; position: absolute; /*top: 100px;*/ left: 180px; background-color: #FFFFFF;"><iframe src="/new/tag" id="ifrfull" /*width="500" height="200"*/ align="left"></iframe></div>
	<form action="/user/fav" method="post" id="favform">
	<input name="order_id" type="hidden" value="'.$order['order_id'].'">
	<input name="fav_nick" id="fav_nick" type="hidden">
	<input name="fav_content" id="fav_content" type="hidden">
	<input name="fav_time" id="fav_time" type="hidden">
	<input name="fav_link" id="fav_link" type="hidden">
	<input name="fav_img" id="fav_img" type="hidden">
	<input name="fav_hn" id="fav_hn" type="hidden">
	<input name="fav_nastr" id="fav_nastr" type="hidden">
	<input name="fav_i" id="fav_i" type="hidden">
	</form>
	<form action="/new/comment2" method="POST" id="submform">
	<input name="hashq" value="'.(($_POST['hashq']!='')?$_POST['hashq']:md5($que1)).'" type="hidden">
	<input name="order_id" value="'.$order['order_id'].'" type="hidden">
	</form>';
	

	
	
	$html_out.='
	<div class="pagination" id="Pagination" style="padding: 5px 10px; margin: 0px 10px; -moz-border-radius: 5px; border-radius: 5px; width: 750px; height: 28px; align: center;"><span class="current prev">←</span>';
	if ($_GET['p']=='')
	{
		$p=1;
	}
	else
	{
		$p=intval($_GET['p']);
	}
	if ($all<10)
	{
		for ($i=1;$i<=$all;$i++)
		{
			if ($i==$p)
			{
				$html_out.='<span class="current">'.$i.'</span>';
			}
			else
			{
				$html_out.='<a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.$i.'\';document.getElementById(\'submform\').submit(); return false; return false;">'.$i.'</a>';
			}
		}
	}
	else
	{
		if ($p<7)
		{
			for ($i=1;$i<10;$i++)
			{
				if ($i==$p)
				{
					$html_out.='<span class="current">'.$i.'</span>';
				}
				else
				{
					$html_out.='<a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.$i.'\';document.getElementById(\'submform\').submit(); return false; return false;">'.$i.'</a>';
				}
			}
			$html_out.='<span>...</span><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all-2).'\';document.getElementById(\'submform\').submit(); return false;">'.($all-2).'</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all-1).'\';document.getElementById(\'submform\').submit(); return false;">'.($all-1).'</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all).'\';document.getElementById(\'submform\').submit(); return false;">'.($all).'</a>';
		}
		elseif ($p>($all-8))
		{
			$html_out.='<a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=1\';document.getElementById(\'submform\').submit(); return false;">1</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=2\';document.getElementById(\'submform\').submit(); return false;">2</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=3\';document.getElementById(\'submform\').submit(); return false;">3</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=4\';document.getElementById(\'submform\').submit(); return false;">4</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=5\';document.getElementById(\'submform\').submit(); return false;">5</a><span>...</span>';
			for ($i=$all-8;$i<=$all;$i++)
			{
				if ($i==$p)
				{
					$html_out.='<span class="current">'.$i.'</span>';
				}
				else
				{
					$html_out.='<a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.$i.'\';document.getElementById(\'submform\').submit(); return false;">'.$i.'</a>';
				}
			}
		}
		else
		{
			$html_out.='<a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=1\';document.getElementById(\'submform\').submit(); return false;">1</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=2\';document.getElementById(\'submform\').submit(); return false;">2</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=3\';document.getElementById(\'submform\').submit(); return false;">3</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=4\';document.getElementById(\'submform\').submit(); return false;">4</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p=5\';document.getElementById(\'submform\').submit(); return false;">5</a><span>...</span>';
			for ($i=($p-2);$i<=($p+2);$i++)
			{
				if ($i==$p)
				{
					$html_out.='<span class="current">'.$i.'</span>';
				}
				else
				{
					$html_out.='<a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.$i.'\';document.getElementById(\'submform\').submit(); return false;">'.$i.'</a>';
				}
			}
			$html_out.='<span>...</span><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all-4).'\';document.getElementById(\'submform\').submit(); return false;">'.($all-4).'</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all-3).'\';document.getElementById(\'submform\').submit(); return false;">'.($all-3).'</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all-2).'\';document.getElementById(\'submform\').submit(); return false;">'.($all-2).'</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.($all-1).'\';document.getElementById(\'submform\').submit(); return false;">'.($all-1).'</a><a href="#" onclick="document.getElementById(\'submform\').action=\'/new/comment2?p='.$all.'\';document.getElementById(\'submform\').submit(); return false;">'.($all).'</a>';
		}
	}
	//echo $all;
	/*$html_out.='
	<span class="current">1</span><a href="#">2</a><a href="#">3</a><a href="#">4</a><a href="#">5</a><a href="#">6</a><a href="#">7</a><a href="#">8</a><a href="#">9</a><a href="#">10</a><span>...</span><a href="#">24</a><a href="#">25</a>';*/
	$html_out.='<a href="#" class="next">→</a></div>
';
//print_r($tagsall);
/*foreach (json_decode(urldecode($_POST['tag_links'])) as $key => $item)
{
	$tagsall[$key]=$item;
}*/

/*$qr='SELECT * from blog_post WHERE order_id=374 ORDER BY post_id DESC LIMIT 50';
$rsrtest=$db->query($qr);
while($psst = $db->fetch($rsrtest))
{
	//$psst['post_content']=str_replace('','',$psst['post_content']);
	//$psst['post_content']=strip_tags($psst['post_content']);
	
	//$psst['post_content'] = iconv("UTF-8", "UTF-8//IGNORE", $psst['post_content']);
	echo $psst['post_content'];
	//$psst['post_content']=preg_replace(' \.\.\./is', '', $psst['post_content']);
	//print_r($psst);
}*/
//$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' ORDER BY p.post_time DESC';
//echo '<br><br>'.$query.'<br><br>';
//die();
//$respost=$db->query($query);
$ii=0;
if ($_POST['sword']!='')
{
	$_POST['sword']=preg_replace('/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|ов|й|и|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ь|ию|ью|ю|ия|ья|ь|я)$/isu','',$_POST['sword']);
	$_POST['sword']=preg_replace('/(ы)$/isu','',$_POST['sword']);
}
while($pst = $db->fetch($respost))
{
	$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
	$outcash['time'][$ii]=$pst['post_time'];
	$outcash['content'][$ii]=$pst['post_content'];
	//echo $pst['post_content'].'<br>';
	$outcash['isfav'][$ii]=$pst['post_fav'];
	$outcash['nastr'][$ii]=$pst['post_nastr'];
	$outcash['isspam'][$ii]=$pst['post_spam'];
	$outcash['nick'][$ii]=$pst['blog_nick'];
	$outcash['type'][$ii]=$pst['post_type'];
	$outcash['id'][$ii]=$pst['post_id'];
	$outcash['tag'][$ii]=explode(',',$pst['post_tag']);
	$outcash['readers'][$ii]=$pst['blog_readers'];
	$outcash['loc'][$ii]=$pst['blog_location'];
	$outcash['gender'][$ii]=$pst['blog_gender'];
	$outcash['age'][$ii]=$pst['blog_age'];
	$outcash['blogin'][$ii]=$pst['blog_login'];
	//$hn22=parse_url(str_replace("\n","",$pst['post_link']));
	//echo $hn22['host'].' ';
	//echo $outcash['loc'][$ii];
	$outcash['eng'][$ii]=$pst['post_engage'];
	//echo '|'.$pst['blog_location']."|<br>";
	$ii++;
}
//print_r($outcash);
//print_r($outcash);
//print_r($wobot['destn1']);
//echo $wobot['destn1']['-74.0 40.8'];
//print_r($outcash['eng']);
//echo count($outcash['link']);
$pn=intval($_GET['p']);
        //$dom = new DomDocument;
        //$res = @$dom->loadHTML($data);
//$dom->encoding='utf-8';
//$dom->schemaValidateSource='';
//$posts = $dom->getElementsByTagName("post");

//$xml = simplexml_load_file("data/blog/135.xml");
   // print_r($xml);
/*if ($_GET['time']!=0) {
list($_GET['time'],$tmp)=explode(',',$_GET['time'],2);
$_GET['time']/=1000;
$_GET['time']=mktime(0,0,0,date('n',$_GET['time']),date('j',$_GET['time']),date('Y',$_GET['time']));
//echo '<script>alert("'.date('n.j.Y',$_GET['time']).'");</script>';
}*/

//$user['user_pagecount']=50;
$i=0;
//echo '(($i>=50*'.$pn.'))&&($i<50*('.$pn.'+1))<br>';
//Сортировка сообщений
//array_multisort($outcash['time'],SORT_DESC,$outcash['link'],SORT_DESC,$outcash['content'],SORT_DESC,$outcash['nick'],SORT_DESC,$outcash['loc'],SORT_DESC);
unset($mas_rep);
unset($mas_spam);
unset($mas_rep_link);
$wkey=0;
$wwwkey=0;
$iss=0;
//$colres=0;
foreach ($outcash['link'] as $key => $llink)
//foreach ($posts as $post)
{
	unset($tag);
	unset($tags);
	$link=urldecode($llink);
	$time=$outcash['time'][$key];
	$content=$outcash['content'][$key];
	$gn_time_start = microtime(true);
	$isfav=$outcash['isfav'][$key];
	$nastr=$outcash['nastr'][$key];
	$id=$outcash['id'][$key];
	$tag=$outcash['tag'][$key];
	$colre=$outcash['readers'][$key];
	$tags=$outcash['tag'][$key];
	$loc=$outcash['loc'][$key];
	$eng=$outcash['eng'][$key];
	$age=$outcash['age'][$key];
	$gender=$outcash['gender'][$key];
	$blogin=$outcash['blogin'][$key];
	$nick=$outcash['nick'][$key];
	$tagv='';
	$loct='';
	$colread='';
	$gen=0;
	if ($gender==0)
	{
		$gen='<a href=\'#\' onclick=\'return false;\' style="display: inline;" class="vtip" title="Пол"><img src="/img/sex-icon.png" height="15" border="0" style="margin-top: 0px;">'.(($age==0)?'':'('.$age.')').'</a>';
	}
	else
	{
		$gen='<a href=\'#\' onclick=\'return false;\' style="display: inline;" class="vtip" title="Пол"><img src="/img/'.(($gender==1)?'female-icon':'Male-icon').'.png" height="15" border="0" style="margin-top: 0px;">'.(($age==0)?'':'('.$age.')').'</a>';
	}
	if (($age==0) && ($gender==0))
	{
		$gen='';
	}
	if ($tag[0]!='')
	{
		$tagv='<div id="tt'.$id.'" style="display: inline;"><a href=\"#\" onclick=\"return false;\" style=\"display: inline;\" class=\"vtip\" title=\"Теги\"><img src="/img/tag.png" height="15" border="0" style="margin-top: 0px;"></a>';
		foreach ($tag as $key1 => $item1)
		{
			if ($key1>0) $tagv.=',';
			$tagv.=' '.$tagsall[$item1];
		}
		$tagv.='</div>';
	}
	else
	{
		$tagv='<div id="tt'.$id.'" style="display: inline;"></div>';
	}
	if (isset($wobot['destn2'][$loc]))
	{
		$loct=" <a href=\"#\" onclick=\"return false;\" style=\"display: inline;\" class=\"vtip\" title=\"Местоположение\"><img src=\"/img/icon-geo.png\" border=\"0\" height=\"13\" style=\"margin-top: 2px;\"></a> ".$wobot['destn2'][$loc];
	}
	else
	{
		//$loct=" <a href=\"#\" onclick=\"return false;\" style=\"display: inline;\" class=\"vtip\" title=\"Местоположение\"><img src=\"/img/icon-geo.png\" border=\"0\" height=\"13\" style=\"margin-top: 2px;\"></a> Неизвестно"." ".$tagv;
	}
	//$loct.=' '.$tagv;
	//$tagv='';
	//$tags=array("клиент","привет");
		$colread='';
		if (($colre!=NULL)&&($colre!=0))
		{
			$colread="<a href=\"#\" onclick=\"return false;\" style=\"display: inline;\" class=\"vtip\" title=\"Подписчики\"><img src=\"/img/icon-hatom.png\" border=\"0\" height=\"13\" style=\"margin-top: 2px;\"></a> ".$colre;
		}
		$colread.=$loct;
	   $pos_nick=array_search($nick,$metrics['speakers']['nick']);
		$colupom='';
	   if (($pos_nick!=false)&&($metrics['speakers']['posts'][$pos_nick]!=0))
	   {
	       $colupom=' <a href=\"#\" onclick=\"return false;\" style=\"display: inline;\" class=\"vtip\" title=\"Упоминания\"><img src="/img/mail_write.png" height="15" border="0" align="bottom"></a> '.$metrics['speakers']['posts'][$pos_nick];
	   }
	if ($order['order_engage']==1)
	{
		$engtext=' <a href="#" onclick="return false;" style="display: inline;" сlass="vtip" title="Вовлеченность"><img src="/img/eng2.png" height="15" border="0" align="bottom"></a> '.intval($eng);
	}
	/*if ($isfav==1) $isfav='+';
	else $isfav='-';
	$nastr=$outcash['nastr'][$key];
	if ($nastr==1) $nastr='+';
	else $nastr='-';*/
	$isspam=$outcash['isspam'][$key];
	$pis=$outcash['type'][$key];
	/*if ($isspam==1) $isspam='+';
	else $isspam='-';*/
	//$nastr=getnastr($content,$order['order_keyword']);
	/*foreach ($ordernastrs as $t=>$onastr)
	{
		if ($onastr[1]==urlencode($link))
		{	
			$nastr=$onastr[0]*(-1);
			break;
		}
	}*/
	
	$gn_time_end = microtime(true);
	$gn_time += $gn_time_end - $gn_time_start;
	
	if (intval($nastr)==1) 
	{
		$nstr='green';
		$nnstr="positive";
	}
	elseif (intval($nastr)==-1) 
	{
		$nstr='red';
		$nnstr="negative";
	}
	else 
	{
		$nstr='black';
		$nnstr="neutral";
	}
    $hn=parse_url($link);
    $hn=$hn['host'];
    $ahn=explode('.',$hn);
    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];
	//$fpizdec=fopen('/var/www/new/deb.txt','a');
	//fwrite($fpizdec, $key."\n");
	//fclose($fpizdec);
	/*if (!in_array($hn,$resorrr))
	{
		//fwrite($fpizdec, "123\n");
		//echo $hn." ";
		continue;
	}*/
			$mas1=$content;
			$mas1=preg_replace('/[г]+/isu','г',$mas1);
			
			//$mas1 = preg_replace("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/is"," ",$mas1);
			/*$mas1=preg_replace('/\n/isu',' ',$mas1);
			$mas1=preg_replace('/\#/isu',' ',$mas1);
			$mas1=preg_replace('/\@/isu',' ',$mas1);
			$mas1=str_replace('\n',' ',$mas1);
			$mas1=str_replace('
	',' ',$mas1);
			$mas1=preg_replace('/,/isu',' ',$mas1);
			$mas1=preg_replace('/_/isu',' ',$mas1);
			$mas1=preg_replace('/\&lt\;/is',' ',$mas1);
			$mas1=preg_replace('/\&gt\;/is',' ',$mas1);
			$mas1=preg_replace('/\&quot\;/is',' ',$mas1);
			$mas1=preg_replace('/\&amp\;/is',' ',$mas1);
			$mas1=preg_replace('/_/isu',' ',$mas1);
			$mas1=preg_replace('/\./isu',' ',$mas1);
			$mas1=preg_replace('/\:/isu',' ',$mas1);
			$mas1=preg_replace('/\!/isu',' ',$mas1);
			$mas1=preg_replace('/[ ]+/isu',' ',$mas1);*/
			/*$mas22=explode(' ',$mas1);
			foreach ($mas22 as $item)
			{
				$item=preg_replace('/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|ов|й|и|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|ь|я)$/isu','',$item);
			}*/
	//$_POST['sword']=preg_replace('/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|ов|й|и|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ь|ию|ью|ю|ия|ья|ь|я)$/isu','',$_POST['sword']);
	//$_POST['sword']=preg_replace('/(ы)$/isu','',$_POST['sword']);
	//$_POST['sword']='нов';
   if (($_POST['snick']=='') && ($_POST['sword']==''))
   {
       if (!in_array($hn,$resorrr))
       {    
           //fwrite($fpizdec, "123\n");
           //echo $hn." ";
           //continue;
       }
   }
	elseif ($_POST['sword']!='')
	{
		if (mb_strpos(mb_strtolower($mas1,"UTF-8"),$_POST['sword'],0,"UTF-8")===false)
		//if (!in_array($_POST['sword'],$mas22))
		{
			continue;
		}
		$iss++;
		if (($iss<(($p-1)*10)) || ($iss>($p*10)))
		{
			continue;
		}
		//echo $iss.'<'.(($p-1)*10).' || '.$iss.'>'.($p*10).'<br>';
		if (!in_array($hn,$mashn))
		{
			$mashn[]=$hn;
		}
	}
   else
   {
       if ($nick!=$_POST['snick'])
       {
           //continue;
       }
   }	
	$prom='';
	if (($hn=='livejournal.com') && (strpos($link,'thread')!==false))
	{
		if ($ahn[0]!='community')
		{
			$prom=$ahn[0];
		}
		if ($ahn[0]=='community')
		{
			$rrrggg='/community\.livejournal\.com\/(?<nick>.*?)\//is';
			preg_match_all($rrrggg,$link,$oou);
			$prom=$oou['nick'][0];
		}
	}
	//echo $hn;
	$tnick='';
	//echo $blogin.'<br>';
	switch ($hn) {
	    case 'livejournal.com':
	        $tnick='<a href="http://'.$nick.'.livejournal.com/" target="_blank" class="vtip" title="Спикер">'.$nick.'</a>';
	        break;
	    case 'twitter.com':
        	$tnick='<a href="http://twitter.com/'.$blogin.'" target="_blank" class="vtip" title="Спикер">'.$blogin.'</a>';
			$nick=$blogin;
	        break;
	    case 'vkontakte.ru':
    		$tnick='<a href="http://vkontakte.ru/id'.$blogin.'" target="_blank" class="vtip" title="Спикер">'.$nick.'</a>';
	        break;
	    case 'facebook.com':
    		$tnick='<a href="http://facebook.com/'.$blogin.'" target="_blank" class="vtip" title="Спикер">'.$nick.'</a>';
	        break;
		default:
			$nick='other';
			break;
	}
	//echo $tnick."<br>";
	$wwwkey++;
	//echo "++++".$wwwkey."+++++++";
	//$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
	if ((intval(date('H',$time))>0)||(intval(date('i',$time))>0)) $stime=date("H:i:s d.m.Y",$time);
	else $stime=date("d.m.Y",$time);
	$isshow=1;
	$cc=0;
	//$link='';
	//$content='';
	//$content=str_replace('','\\\\',$content);
	//$pattern = '/([\x09\x0A\x0D\x20-\x7E]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})|./x';
	//$content=preg_replace($pattern, '$1', $content);
	//$content=preg_replace('/. \.\.\./is', '', $content);
	$content=strip_tags($content);	
	$content=iconv("UTF-8", "UTF-8//IGNORE", $content);
	$content=str_replace("\n",'',$content);
	//$content=str_replace("\n",'',$content);
	$content=str_replace("\t",'',$content);
	$content=str_replace('','',$content);
	$content=preg_replace('/ +/i', ' ', $content);
	$content=preg_replace('/\s/i', ' ', $content);
	$content=htmlentities($content,ENT_QUOTES,'UTF-8');

	$tagp='<input type="text" id="tag'.($wkey+1).'" value="'.$tag.'"> <a href="#" onclick="var tag=$(\\\'#tag'.($wkey+1).'\\\').val(); $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.intval($_POST['order_id']).'&post_id='.intval($id).'&tag=\\\'+tag, success: function(msg1){ } }); return false;">&lg;</a>';
	if (($key % 11) < 7)
	{
		$tagp2='<div style="float: right; margin-right: 20px;"><a href="#" onclick="$(\'#othr2\').offset({top:'.(($key%10)*55+170).'}); if (c==0) { $(\'#othr2\').show(\'fast\', function() {return false;}); c=1;  } else if (c==1) {  if ($(\'#ifr\').attr(\'src\')==\'/new/tag?tall='.urlencode(json_encode($tagsall)).'&tags='.urlencode(json_encode($tags)).'&id='.$id.'&order_id='.$_POST['order_id'].'\') { $(\'#othr2\').hide(\'fast\', function() {return false;}); c=0;} }  {$(\'#othr2\').offset({top:'.(($key%10)*55+170).'}); } $(\'#ifr\').attr(\'src\',\'/new/tag?tall='.urlencode(json_encode($tagsall)).'&tags='.urlencode(json_encode($tags)).'&id='.$id.'&order_id='.$_POST['order_id'].'\');   return false;">Теги</a></div>';
	}
	else
	{
		$tagp2='<div style="float: right; margin-right: 20px;"><a href="#" onclick="$(\'#othr2\').offset({top:'.(($key%10)*55+120).'}); if (c==0) { $(\'#othr2\').show(\'fast\', function() {return false;}); c=1;  } else if (c==1) {  if ($(\'#ifr\').attr(\'src\')==\'/new/tag?tall='.urlencode(json_encode($tagsall)).'&tags='.urlencode(json_encode($tags)).'&id='.$id.'&order_id='.$_POST['order_id'].'\') { $(\'#othr2\').hide(\'fast\', function() {return false;}); c=0;} }  {$(\'#othr2\').offset({top:'.(($key%10)*55+120).'}); } $(\'#ifr\').attr(\'src\',\'/new/tag?tall='.urlencode(json_encode($tagsall)).'&tags='.urlencode(json_encode($tags)).'&id='.$id.'&order_id='.$_POST['order_id'].'\');   return false;">Теги</a></div>';
	}
	$typep='<div style="float: right; margin: 0 15px 0 0;"><select id="my_select'.($wkey+1).'" onChange="var typ=$(\\\'#my_select'.($wkey+1).' option:selected\\\').val(); $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.intval($_POST['order_id']).'&link='.intval($id).'&typep=\\\'+typ, success: function(msg1){ } });">';

    for ($jj=0;$jj<count($mastexttype);$jj++)
    {
        if ($pis==$jj)
        {
            $typep.='<option value="'.$jj.'" SELECTED>'.$mastexttype[$jj].'</option>';
        }
        else
        {
            $typep.='<option value="'.$jj.'">'.$mastexttype[$jj].'</option>';
        }
    }
    $typep.='</select></div>';

	if ($order['ful_com']==1)
	{
		//$viewf='<a href="'.$config['html_root'].'fult?id='.$id.'&kword='.urlencode($order['order_name']).'&keyword='.urlencode($order['order_keyword']).'&cont='.urlencode($content).'" onclick="return openNewWindow(this)">full</a><a href="#"><div class="viewfull" onclick="$(\\\'#othr5\\\').offset({top:'.(($key%10)*55+200).'}); if (c1==0) { $(\\\'#othr5\\\').show(\\\'fast\\\', function() {return false;}); c1=1;  } else if (c1==1) {  if ($(\\\'#ifrfull\\\').attr(\\\'src\\\')==\\\'/new/fult?id='.$id.'&kword='.urlencode($order['order_name']).'&keyword='.urlencode($order['order_keyword']).'&cont='.urlencode($content).'\\\') { $(\\\'#othr5\\\').hide(\\\'fast\\\', function() {return false;}); c1=0;} }  {$(\\\'#othr5\\\').offset({top:'.(($key%10)*55+200).'}); } $(\\\'#ifrfull\\\').attr(\\\'src\\\',\\\'/new/fult?id='.$id.'&kword='.urlencode($order['order_name']).'&keyword='.urlencode($order['order_keyword']).'&cont='.urlencode($content).'\\\'); return false;"></div></a>';
		$viewf='<a href="'.$config['html_root'].'fult?id='.$id.'&kword='.urlencode($order['order_name']).'&keyword='.urlencode($order['order_keyword']).'&cont='.urlencode($content).'&order_id='.$order['order_id'].'" onclick="return openNewWindow(this)"><div class="viewfull"></div></a>';
	}
	if (intval($isfav)==1) $favor='<a href="#"><div class="fav" onclick="';
	else $favor='<a href="#"><div class="fav2" onclick="';
	if (intval($isspam)==1) $spammor='<a href="#"><div class="spamm2" onclick="';
	else $spammor='<a href="#"><div class="spamm" onclick="';
	$wkey++;
	$nastr2='<a href="#"><div class="plus" onclick="var nastrbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&positive='.intval($id).'\\\', success: function(msg1){ $(\\\'#nstrid'.($wkey-1).'\\\').attr(\\\'style\\\',\\\'color:\\\'+msg1); } }); return false;"></div></a><a href="#"><div class="neutral" onclick="var nastrbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&neutral='.intval($id).'\\\', success: function(msg1){$(\\\'#nstrid'.($wkey-1).'\\\').attr(\\\'style\\\',\\\'color:\\\'+msg1);} }); return false;"></div></a><a href="#"><div class="minus" onclick="var nastrbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&negative='.intval($id).'\\\', success: function(msg1){$(\\\'#nstrid'.($wkey-1).'\\\').attr(\\\'style\\\',\\\'color:\\\'+msg1);} }); return false;"></div></a>';
	//fwrite($fpizdec, $link);
	//fclose($fpizdec);
		/*if ($i>0) $html_out.= ',
';*/
//<b>промоутер:</b> <a href="http://'.$prom.'.livejournal.com/">'.$prom.'</a>
		$html_out.='<div style="border: 1px solid #eee; -moz-border-radius: 5px; padding: 3px; margin: 3px; background: #fff;"><span class="sl rln2">'.(($p-1)*10+$i+1).'. '.$stime.' '.$gen.' '.$colread.' '.$tagv.' <div style="float: right; margin: 0 15px 0 0;"><select id="my_select'.$i.'" onchange="var typ=$(\'#my_select'.$i.' option:selected\').val(); $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order['order_id'].'&link='.$id.'&typep=\'+typ, success: function(msg1){ } });"><option value="0" '.(($pis==0)?'selected':'').'></option><option value="1" '.(($pis==1)?'selected':'').'>Не важно</option><option value="2" '.(($pis==2)?'selected':'').'>Средне</option><option value="3" '.(($pis==3)?'selected':'').'>Важно</option><option value="4" '.(($pis==4)?'selected':'').'>Очено важно</option></select></div> '.$tagp2.' <a href="#"><div class="plus" onclick="var nastrbtn=this; $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order['order_id'].'&positive='.$id.'\', success: function(msg1){ $(\'#nstrid'.$i.'\').attr(\'style\',\'color:\'+msg1); } }); return false;"></div></a><a href="#"><div class="neutral" onclick="var nastrbtn=this; $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order['order_id'].'&neutral='.$id.'\', success: function(msg1){$(\'#nstrid'.$i.'\').attr(\'style\',\'color:\'+msg1);} }); return false;"></div></a><a href="#"><div class="minus" onclick="var nastrbtn=this; $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order['order_id'].'&negative='.$id.'\', success: function(msg1){$(\'#nstrid'.$i.'\').attr(\'style\',\'color:\'+msg1);} }); return false;"></div></a>'.$spammor.' var spambtn=this; $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order['order_id'].'&spam='.$id.'\', success: function(msg1){ $(spambtn).attr(\'class\',msg1); members[1][8]=\'\';}}); return false;"></div></a>'.$favor.' var favbtn=this; $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order['order_id'].'&fav='.$id.'\', success: function(msg){ $(favbtn).attr(\'class\',msg); members[1][8]=\'\';}}); return false;"></div></a></span>'.$viewf.'<br><img src="../img/social/'.(file_exists('../img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'" title="'.$hn.'" alt="'.$hn.'"> <b>'.(($nick=='')?' неизвествен':$tnick).' '.(($prom!='')?'(':'').'<a href="http://'.$prom.'.livejournal.com/" target="_blank" class="vtip" title="Промоутер">'.$prom.'</a>'.(($prom!='')?')':'').'</b> <u><a href="'.$link.'" id="nstrid'.$i.'" target="_blank" style="color: '.$nstr.';"><font id="nstrid'.$id.'">'.$content.'</font></a></u></div>';
		$i++;
	
}
$i=$pscount;
if (isset($_SESSION[$_POST['hashq'].'_scount']))
{
	$i=$_SESSION[$_POST['hashq'].'_scount'];
}
if ($_POST['scount']!='')
{
	$i=$_POST['scount'];
}
if (($i<10) || ($i>20))
{
	if (($i % 10)==1)
	{
		$text_i="упоминание";
	}
	else
	if ((($i % 10)==2) || (($i % 10)==3) || (($i % 10)==4))
	{
		$text_i="упоминания";
	}
	else
	{
		$text_i="упоминаний";
	}
}
else
{
	$text_i="упоминаний";
}
//$colres=count($resorrr);
if (!isset($_POST['hashq']))
{
	//$colres=count($mmres_c);
	$colres=$mmres_c;
}
else
{
	$colres=$_SESSION[$_POST['hashq'].'_colres'];
}
if ($_POST['snick']!='')
{
	$colres=1;
}
if ($_POST['sword']!='')
{
	$colres=count($mashn);
}
if (!isset($_POST['hashq']))
{
	$_SESSION[md5($que1).'_colres']=$colres;
}
if (($colres<10) || ($colres>20))
{
	if (($colres % 10)==1)
	{
		$text_res="ресурсе";
	}
	else
	{
		$text_res="ресурсах";
	}
}
else
{
	$text_res="ресурсах";
}
$html_out.= '
<br>
';
/*if ($_COOKIE['writer']!='')
{
	echo 'По автору "'.$_COOKIE['writer'].'" (<a href="#" onclick="$.cookie(\'writer\', \'\'); loaditem(\'user/comment?order_id='.intval($_POST['order_id']).'\',\'#commentbox\', function() { showcomment(); } );">отменить</a>)';
}
if ($_COOKIE['showfav']=='1')
{
	echo 'Избранные (<a href="#" onclick="$.cookie(\'showfav\', \'\'); loaditem(\'user/comment?order_id='.intval($_POST['order_id']).'\',\'#commentbox\', function() { showcomment(); } );">отменить</a>)';
}*/
$html_out .='
<script>
var c=0;
var c1=0;
    $(document).ready(function(){
		$(\'#colup\').html(\''.$i.' '.$text_i.'\');
		$(\'#colup1\').html(\''.$colres.' '.$text_res.'\');
		$(\'#othr\').mouseleave(function() {
		  $(\'#othr\').fadeOut(\'normal\');
		  c=0;
		});
		$(\'#othr5\').mouseleave(function() {
		  $(\'#othr5\').fadeOut(\'normal\');
		  c1=0;
		});
		window.onload();
		uncheckPrettyCb = function(caller) {
		$(caller).each(function(){
			if($(this).is(\':checked\')){
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'checked\');
					}else{
						$(this).trigger(\'click\');
					};
			};
		});
		};

		checkPrettyCb = function(caller) {
		$(caller).each(function(){
			if($(this).is(\':checked\')){
			}else{
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'\');
					}else{
						$(this).trigger(\'click\');
					};
			};
			});
		};
		uecheckAllPC = function(caller){
			if($(caller).is(\':checked\')){
				$(caller).each(function(){
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'checked\');
					}else{
						$(this).trigger(\'click\');
					};
				});
			}else{
				$(caller).each(function(){
					$(\'label[for="\'+$(this).attr(\'id\')+\'"]\').trigger(\'click\');
					if($.browser.msie){
						$(this).attr(\'checked\',\'\');
					}else{
						$(this).trigger(\'click\');
					};
				});
			};
		};
		$(\'input[type=checkbox],input[type=radio]\').prettyCheckboxes();
		$( "#masterbtn" ).click(function() {
			$( "#masterfrm" ).toggleClass( "filterbox-show");
			$( ".filterbox-contents").toggleClass( "filterbox-contents-show");
			if ($( ".filterbox-contents").attr("class")=="filterbox-contents filterbox-contents-show")
			{
				$.cookie("masbtn", "1");
			}
			else
			{
				$.cookie("masbtn", "0");
			}
			return false;
		});
		$(\'#lothr\').click(function() {
			$(\'#othr\').toggle(\'fast\', function() {
		    // Animation complete.
		  	});
		    return false;
		});	
		$(\'#locopen\').click(function() {
			$(\'#lothr1\').toggle(\'fast\', function() {
		    // Animation complete.
		  	});
		    return false;
		});
		$(\'#othr\').mouseleave(function() {
		  $(\'#othr\').fadeOut(\'normal\');
		  c=0;
		});
		var intervalID;
		$(\'#othr2\').mouseleave(function() {
			 intervalID= setInterval(function(){
			 // $(\'#othr2\').fadeOut(\'normal\');
			  $(\'#othr2\').toggle(\'normal\');
			  c=0;
			clearInterval(intervalID);
			//alert(c);
			}, 1500);
		});		
		$(\'#othr2\').mouseenter(function() {
			clearInterval(intervalID);
		});		
		$(\'#lothr1\').mouseleave(function() {
		  $(\'#lothr1\').fadeOut(\'normal\');
		  c=0;
		});
		$(\'#lothr5\').mouseleave(function() {
		  $(\'#lothr5\').fadeOut(\'normal\');
		  c=0;
		});
		vtip();
		var dates = $( "#ntime, #etime" ).datepicker({
			dateFormat: "dd.mm.yy",
			defaultDate: "'.date("d.m.Y",$order['order_end']).'",
			minDate: "'.date("d.m.Y",$order['order_start']).'",
			maxDate: "'.date("d.m.Y",$order['order_end']).'",
			onSelect: function( selectedDate ) {
				var option = this.id == "ntime" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" );
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			} 
		});
    });
    
</script>
	  <!--<div class=\'pagination\' id=\'Pagination\' style=\'padding: 5px 10px; margin: 0px 10px; -moz-border-radius: 5px; border-radius: 5px; width: 750px; height: 28px; align: center;\'></div>
	  <dl id=\'Searchresult\'>
	    <dt>Результаты выборки</dt>
	  </dl>-->
	</div>
	</div>
';

$time_end = microtime(true);
$time = $time_end - $time_start;

$_COOKIE['writer']='';

stop_tpl();

//echo '<b>delay: '.$time.', getnastr delay: '.$gn_time.'</b>';
/*//
echo '<table width="100%"><td align="center">';
if ($pn>7) echo '←&nbsp;';
for ($i=0;$i<intval($j/(50+1)+1);$i++)
if (($i>$pn-8)&&($i<$pn+8))
echo ($pn==$i?'':'<a href="#" onclick="loaditem(\'user/comment?order_id='.intval($_GET['order_id']).'&time='.intval($_GET['time']).'000&p='.$i.'\',\'#commentbox\');return false;">').($i+1).($pn==$i?' ':'</a> ');
if ($pn<intval($j/(50+1)-7)) echo '→';
echo '</td></table>';
//*/
}
		//$res=$db->query('SELECT * FROM users');
		//echo 'Список пользователей:<br>';
		//$i=0;
		//while ($row = $db->fetch($res)) {
		//	$i++;
		//	echo $row['user_email'].' <a class="openform" href="/user/adduser?user_id='.$row['user_id'].'">редактировать</a> <a href="/user/keywords/'.$row['user_id'].'">услуги</a><br>';
		//}
		//if ($i==0) echo 'пользователи отсутствуют<br>';
		//echo'
//<p class="menuitem"><a href="#" class="userlnk" rel="user/admin">Обновить</a> <a class="openform" href="/user/adduser">Добавить</a></p>
//';
//}
?>
