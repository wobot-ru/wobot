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

$order_id=intval($path[0]);

$res=$db->query("SELECT * from blog_orders WHERE order_id=".$order_id." and user_id=".intval($user['user_id'])." LIMIT 1");
if (mysql_num_rows($res)==0) die();
$order = $db->fetch($res);
$order['order_start']=intval($order['order_start']);
$order['order_end']=((intval($order['order_end'])==0)||(intval($order['order_end'])>mktime(0,0,0,date('m'),date('d')-1,date('Y'))))?(mktime(0,0,0,date('m'),date('d'),date('Y'))):intval($order['order_end']+60*60*24);
//mktime(0,0,0,date('m'),date('d')-1,date('Y'));
$res_tag=$db->query("SELECT * from blog_tag WHERE user_id=".intval($user['user_id']));
while ($order_tag = $db->fetch($res_tag))
{
	$mtags[$order_tag['tag_tag']]=$order_tag['tag_name'];
}
$jqready='

if ($.cookie("masbtn")==1)
{
	$( "#masterfrm" ).toggleClass( "filterbox-show");
	$( ".filterbox-contents").toggleClass( "filterbox-contents-show");
}

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

showgraph();
/*
var map = new YMaps.Map(document.getElementById("YMapsID"));
map.setCenter(new YMaps.GeoPoint(37.609218,55.753559), 5);

map.addControl(new YMaps.TypeControl());
map.addControl(new YMaps.ToolBar());
map.addControl(new YMaps.Zoom());
map.addControl(new YMaps.MiniMap());
map.addControl(new YMaps.ScaleLine());

var plmks = [

';
//$fn = "/var/www/data/blog/".intval($order_id).".map";
//$h = fopen($fn, "r");
//$data = fread($h, filesize($fn));
$data=$order['order_map'];
$sources=json_decode($data, true);
foreach ($sources as $co => $item)
{
	$it=explode(' ',$item[0],2);
	$jqready.='['.$it[0].','.$it[1].',\''.$item[1].'\']';
	if ($co<count($sources))
	{
		$jqready.=',';
	}
}
$jqready.='
];

var placemark;
for(var i in plmks)
{
    placemark = new YMaps.Placemark(new YMaps.GeoPoint(plmks[i][0], plmks[i][1]));
	placemark.name = name;
	placemark.description = plmks[i][2];
	map.addOverlay(placemark);
	placemark.openBalloon();
}
*/
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
var menu1 = ['; 
/*{
	unset($sources);
	$fn = "data/blog/".intval($order_id).".src";
	$h = fopen($fn, "r");
	$data = fread($h, filesize($fn));
	$sources=json_decode($data, true);
	$other=0;
	$k=0;
	foreach ($sources as $i => $source)
	{
		$k++;
		if (($k>10) && ($k!=count($sources)))
		{
			$jqready.='{\'<input type="checkbox" name="res_'.$i.'" class="rescheck1" id="resch2" value="true"/>'.$i.' '.$source.'\':function(menuItem,menu) { return false;}},';
		}
		else
		if ($k==count($sources))
		{
			$jqready.='{\'<input type="checkbox" name="res_'.$i.'" checked="checked" class="rescheck1" id="resch1" value="true"/>'.$i.' '.$source.'\':function(menuItem,menu) {return false;}}';
		}
	}
}*/
$jqready.=']; 
$(function() { $(\'.cmenu1\').contextMenu(menu1,{theme:\'vista\'}); });
var c=1;
$(\'#othr\').hide();
$(\'#lothr1\').hide();
$(\'#lothr5\').hide();
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

$(".s1").dropdownchecklist();

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
';
start_tpl($jqready,'<link href=\'/css/details_lk.css\' rel=\'stylesheet\' type=\'text/css\' />
<link href=\'/css/old_details_lk.css\' rel=\'stylesheet\' type=\'text/css\' />
');
$html_out .='	<div id=\'table\'>';


//$fn = "/var/www/data/blog/".intval($order_id).".metrics";
//$h = fopen($fn, "r");
//$data = fread($h, filesize($fn));
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
//print_r($metrics);
//print_r($metrics['location']);
fclose($h);
//print_r($metrics['location']);
unset($sources);
unset($data);
//$fn = "/var/www/data/blog/".intval($order_id).".src";
//$h = fopen($fn, "r");
//$data = fread($h, filesize($fn));
$data=$order['order_src'];
$sources=json_decode($data, true);
if ($order['order_engage']=='1')
{
	$engtext='Вовлеченность';
	$engvalue=$metrics['engagement'];
}
else
{
	$engtext='';
	$engvalue='';
}

$k=0;
$src_count=count($sources);
$other=-1;
foreach ($sources as $i => $source)
{
		$other+=$source;
}
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
/*
=================== new design
*/
$keyword=(mb_strlen($order['order_name'],'UTF-8')>0)?$order['order_name']:$order['order_keyword'];
$keyword=mb_strtoupper($keyword, 'UTF-8');
	$html_out.='
	<script language="javascript">
	function arterunch1(){
		Custom.init;
	}
	function afterunch(){
		window.onload();
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
	}
	</script>
  <div class=\'top_line\'>
		<h1><a href="/">ЛИЧНЫЙ КАБИНЕТ</a> → '.$keyword.'</h1>
  </div>
  <div class=\'top_table\'>
    <div class=\'metrixbox ui-corner-all\'>
      <em class=\'metrixtag vtip\' title=\'Найдено упоминаний по вашему запросу за всё время.\'>Всего упоминаний</em>
      <hr />
      <em class=\'metrix\'>'.($other-1).'</em>

      <em class=\'metrixdyn vtip\' title=\'Изменение количества упоминаний за последний день.\'>'.$text_din_p.'</em>
      <br />
      <font color=\'#3c6087\'>+</font>'.intval($metrics['pos_posts']).' / <font color=\'red\'>-</font>'.intval($metrics['neg_posts']).'
    </div>
    <div class=\'metrixbox ui-corner-all\'>
      <em class=\'metrixtag vtip\' title=\'Ресурсы, на которых есть упоминания по вашему запросу за всё время.\'>Ресурсов</em>
      <hr />
      <em class=\'metrix\'>'.$src_count.'</em>
      <em class=\'metrixdyn\'></em>
    </div>
    <div class=\'metrixbox ui-corner-all\'>

      <em class=\'metrixtag vtip\' title=\'Число людей, просмотревших упоминания по вашему запросу за всё время.\'>Аудитория</em>
      <hr />
      <em class=\'metrix\'>'.$metrics['value'].'</em>
      <em class=\'metrixdyn\'></em>
    </div>
    <div class=\'metrixbox ui-corner-all\'>
      <em class=\'metrixtag vtip\' title=\'Характеризует популярность упоминаний и частоту их цитирования.\'>'.$engtext/*Цитируемость*/.'</em>

      <hr />
      <em class=\'metrix\'>'.$engvalue/*((intval((1/($other/250))*100)+intval((1/($metrics['value']/1000))*100))/100).*/.'</em>
      <em class=\'metrixdyn\'></em>
    </div>
    <div class=\'metrixbox ui-corner-all\'>
      <em class=\'metrixtag vtip\' title=\'Характеризует обсуждаемость вашего запроса в социальных медиа.\'>'./*Вовлеченность*/'</em>
      <hr />

      <em class=\'metrix\'>'./*(intval($other/$metrics['value']*100)/100).*/'</em>
      <em class=\'metrixdyn\'></em>
    </div>
    <div class=\'push\'></div>
  </div>
  <div class=\'left_table\'>
    <div class=\'metrixbox ui-corner-all\' style=\'height: 180px\'>
      <em class=\'metrixtag\'>Ресурсы</em>
      <hr />
      <div id=\'socialgraph\' style=\'width: 160px; height: 160px; margin: 3px -8px -8px -8px; padding: 3px -8px -8px -8px;\'></div>
    </div>
    <div class=\'metrixbox ui-corner-all\' style=\'height: 180px\'>
      <em class=\'metrixtag\'>Города</em>
      <hr />
      <div id=\'geograph\' style=\'width: 160px; height: 160px; margin: 3px -8px -8px -8px; padding: 3px -8px -8px -8px;\'></div>
    </div>
    <div class=\'metrixbox ui-corner-all\' style=\'height: 250px\'>
      <em class=\'metrixtag vtip\' title=\'Люди, которые упоминаниют ваш запрос чаще всех в социальных медиа.\'>Спикеры</em>
      <hr />
		<table style="font-size: 10px;">';
		//print_r($metrics);
		$speakcount=0;
	foreach ($metrics['speakers']['nick'] as $i=>$speaker){
		if ($metrics['speakers']['link'][$i]=="twitter.com")
		{
			$speak_t='http://www.twitter.com/'.$speaker;
		}
		elseif ($metrics['speakers']['link'][$i]=="livejournal.com")
		{
			$speak_t='http://'.$speaker.'.livejournal.com/';
		}
		elseif ($metrics['speakers']['link'][$i]=="vkontakte.ru")
		{
			$speak_t='http://vkontakte.ru/id'.$speaker;
		}
		elseif ($metrics['speakers']['link'][$i]=="facebook.com")
		{
			$speak_t='http://www.facebook.com/profile.php?id='.$speaker;
		}
		if ((mb_strlen($metrics['speakers']['login'][$i],'UTF-8')>0)&&($metrics['speakers']['login'][$i]!='users'))
		{
			$html_out.='<tr><td>'.($speakcount+1).'.</td><td> '.((strlen($metrics['speakers']['login'][$i])>0)?'<a href="'.$speak_t.'" target="_blank">'.$metrics['speakers']['login'][$i].'</a>':'неизвестен').'</td><td>&nbsp;&nbsp;<a href="#" onclick="document.getElementById(\'nword\').value=\'\'; document.getElementById(\'nname\').value=\''.$speaker.'\'; document.getElementById(\'filternameform\').action=\'/comment\';document.getElementById(\'filternameform\').submit();">'.$metrics['speakers']['posts'][$i].'</a></td></tr>';
			$speakcount++;
		}
		if ($speakcount==10) break;
	}
		/*foreach ($metrics['speakers']['nick'] as $i=>$speaker){
			$html_out.='<tr><td>'.($i+1).'.</td><td> <a href="http://www.twitter.com/'.$speaker.'" target="_blank">'.$speaker.'</a></td><td> <a href="#" onclick="document.getElementById(\'nname\').value=\''.$speaker.'\'; document.getElementById(\'filternameform\').action=\'/comment\';document.getElementById(\'filternameform\').submit();">'.$metrics['speakers']['posts'][$i].'</a></td></tr>';
			if ($i==9) break;
		}*/
		$html_out.='</table>
    </div>
    <div class=\'metrixbox ui-corner-all\' style=\'height: 250px\'>
      <em class=\'metrixtag vtip\' title=\'Люди, которые упоминают ваш бренд и имеют большой круг друзей или читателей.\'>Промоутеры</em>
      <hr />
      <hr />
		<table style="font-size: 10px;">';
		$promocount=0;
	foreach ($metrics['promotion']['nick'] as $i=>$speaker){
		if ($metrics['promotion']['link'][$i]=="twitter.com")
		{
			$speak_prom='http://www.twitter.com/'.$speaker;
		}
		elseif ($metrics['promotion']['link'][$i]=="livejournal.com")
		{
			$speak_prom='http://'.$speaker.'.livejournal.com/';
		}
		elseif ($metrics['promotion']['link'][$i]=="vkontakte.ru")
		{
			$speak_prom='http://vkontakte.ru/id'.$speaker;
		}
		elseif ($metrics['promotion']['link'][$i]=="facebook.com")
		{
			$speak_t='http://www.facebook.com/profile.php?id='.$speaker;
		}
		if ((mb_strlen($metrics['promotion']['login'][$i],'UTF-8')>0)&&($metrics['speakers']['login'][$i]!='users'))
		{
			$html_out.='<tr><td>'.($promocount+1).'.</td><td> '.((strlen($metrics['promotion']['login'][$i])>0)?'<a href="'.$speak_prom.'" target="_blank">'.$metrics['promotion']['login'][$i].'</a>':'неизвестен').'</td><td>&nbsp;&nbsp;'.$metrics['promotion']['readers'][$i].'</td></tr>';
			$promocount++;
		}
		if ($promocount==10) break;
	}
		/*foreach ($metrics['promotion']['nick'] as $i=>$speaker){
			$html_out.='<tr><td>'.($i+1).'.</td><td> <a href="http://www.twitter.com/'.$speaker.'" target="_blank">'.$speaker.'</a></td><td> '.$metrics['promotion']['readers'][$i].'</td></tr>';
			if ($i==9) break;
		}*/
			$html_out.='</table>
    </div>
    <div class=\'metrixbox ui-corner-all\' style=\'height: 250px\'>
      <em class=\'metrixtag\'>Облако тегов</em>
      <hr />
      <hr />
		<table style="font-size: 10px;">';
		$promocount=0;
		$ki=0;
		$maskw=explode(' ',preg_replace('/\"/is',' ',mb_strtolower($keyword, 'UTF-8')));
		foreach ($maskw as $it => $mkw)
		{
			$maskw1[]=preg_replace('/(ая|а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|ов|й|и|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|ь|я|ер|еров)$/isu','',$mkw);
		}
		//print_r($maskw1);
		//print_r($maskw);
		//print_r($metrics['topwords']);
	foreach ($metrics['topwords'] as $i=>$word)
	{
		if (($ki<10) && (!in_array($i,$maskw)))
		{
			$cutk=preg_replace('/(ая|а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|ов|й|и|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|ь|я|еров)$/isu','',$i);
			//echo $cutk.' ';
			if ((!in_array($cutk,$maskw)) && (!in_array($cutk,$maskw1)))
			{
				$ki++;
				$html_out.='<tr><td>'.$ki.'.</td><td>'.$i.'</td><td>&nbsp;&nbsp;<a href="#" onclick="document.getElementById(\'nname\').value=\'\'; document.getElementById(\'nword\').value=\''.$i.'\'; document.getElementById(\'filternameform\').action=\'/comment\';document.getElementById(\'filternameform\').submit();">'.$word.'</td></tr>';
			}
		}
	}
		/*foreach ($metrics['promotion']['nick'] as $i=>$speaker){
			$html_out.='<tr><td>'.($i+1).'.</td><td> <a href="http://www.twitter.com/'.$speaker.'" target="_blank">'.$speaker.'</a></td><td> '.$metrics['promotion']['readers'][$i].'</td></tr>';
			if ($i==9) break;
		}*/
			$html_out.='</table>
    </div>
  </div>
  <div class=\'right_table\'>
    <div class=\'filterbox ui-corner-all\' id="masterfrm">
			<b><u><a href="#" id="masterbtn">Мастер отчетов</a></u></b>
			<div class=\'filterbox-contents\'>
			<form action="/comment" method="post" id="filternameform" target="_blank">
				<input type="hidden" name="order_id" value="'.$order_id.'">
				<input type="hidden" id="nname" name="snick" value="">
				<input type="hidden" id="nword" name="sword" value="">
			</form>
			<form action="/comment" method="post" id="filterform" target="_blank">
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
			$html_out.='
				<td><img src="/img/tag-icon.png" style="margin: -2px 3px -2px 5px;"> <div style="border-bottom: 1px dashed black; display: inline;"><font class="vtip" title="Выберите теги, которые вы хотели бы отобразить в выдаче.">Теги</font></div>&nbsp;'.$addtt.'&nbsp;'.$deltt.'</td>
				<td style="font-size: 11px;"><table cellspacing="0" cellpadding="0"><tr>';
				foreach ($mtags as $key => $item)
				{
					$ti++;
					$html_out.='<td width="120"><label for="tgp'.$key.'" tabindex="1"></label><input id="tgp'.$key.'" type="checkbox" checked="checked" name="tg'.$key.'" value="'.$key.'"> '.$item.'&nbsp;</td>';
					if ($ti % 5 == 0)
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
					/*foreach($sources2 as $src2)
					{
						$sources[]=$src2;
					}*/
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
								$html_out1.='<div id="othr" style="min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 500px; left: 520px; background-color: #FFFFFF;"><!--<input type="checkbox" style="margin: 2px;" name="res_'.$i.'" checked="checked" class="rescheck1" id="resch1" value="true"/> '.$i.' ('.$source.')&nbsp;--><table><tr>';
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
					$html_out.='<td><label for="op" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}"></label><input type="checkbox" class="rescheck" id="op" value="true" onclick="if($(\'#op\').is(\':checked\')) {checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\');} else { uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\');}" checked="checked"/><a id="lothr" class="vtip" title="Ресурсов: '.(count($sources)-9).'" href="#">другие</a> ('.$other.')&nbsp;</td>
	';
					$html_out.='</tr></table>';
					$html_out.=$html_out1;
					fclose($h);
				}		
				$html_out.='<div id="lothr1" style="min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 495px; left: 380px; background-color: #FFFFFF;"><table>';		
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
							$html_out.='<td><div id="lothr5" style="min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 550px; left: 670px; background-color: #FFFFFF;"><table cellspacing="0" cellpadding="0"><tr><td><label for="loc_count'.$kk.'" onclick="if(!$(\'#loc_count'.$kk.'\').is(\':checked\')) {checkPrettyCb(\'.cou_'.$kk.'\');} else { uncheckPrettyCb(\'.cou_'.$kk.'\');}">'.$kk.' ('.$itt.')</label><input type="checkbox" style="margin: 2px;" name="locc_othr'.$kk.'" checked="checked" class="cc_cou_'.$kk.'" id="loc_count'.$kk.'" value="true"/></td></tr>';		
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
				$html_out.='<td><label for="loc_countna" onclick="if(!$(\'#loc_countna\').is(\':checked\')) {checkPrettyCb(\'.cou_na\');} else { uncheckPrettyCb(\'.cou_na\');}"></label><input type="checkbox" style="margin: 2px;" name="loc_othr1" class="cc_cou_na" checked="checked" id="loc_countna"/>Неопределено ('.$metrics['location_cou'][''].')</td></tr></table><a href="#" id="locopen">Города</a><br>';
	$html_out.=				'
<!--<br><br><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'ssoc\') { if ($(\'#resch11\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>социальные сети&nbsp;<input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'novres\') { if ($(\'#resch12\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>новостные ресурсы&nbsp;<input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'microb\') { if ($(\'#resch13\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>микроблоги&nbsp;<input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'forabl\') { if ($(\'#resch14\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>форумы и блоги--><a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&unch=1\',function(){ afterunch(); });  /*uncheckPrettyCb(\'.rescheck2\'); uncheckPrettyCb(\'.rescheck\'); uncheckPrettyCb(\'.ssoc\'); uncheckPrettyCb(\'.novres\'); uncheckPrettyCb(\'.microb\'); uncheckPrettyCb(\'.forabl\'); uncheckPrettyCb(\'.u_ssoc\'); uncheckPrettyCb(\'.u_novres\'); uncheckPrettyCb(\'.u_microb\'); uncheckPrettyCb(\'.u_forabl\'); '.$unchall.'*/ return false;">снять все</a> <a href="#" style="font-size: 12px;" onclick="$(\'#masterfrm\').load(\'/ajax?order_id='.intval($order_id).'&post_id='.intval($id).'&user_id='.intval($user['user_id']).'&ch=1\',function(){ afterunch(); }); /*checkPrettyCb(\'.rescheck2\'); checkPrettyCb(\'.rescheck\'); checkPrettyCb(\'.ssoc\'); checkPrettyCb(\'.novres\'); checkPrettyCb(\'.microb\'); checkPrettyCb(\'.forabl\'); checkPrettyCb(\'.u_ssoc\'); checkPrettyCb(\'.u_novres\'); checkPrettyCb(\'.u_microb\'); checkPrettyCb(\'.u_forabl\'); '.$chall.'*/ return false;">отметить все</a><a href="#" style="font-size: 12px;" onclick="$(\'input\').each(function(){ if($(this).attr(\'id\')==(\'blogs\')) {$(this).attr(\'checked\',\'checked\');}});"></a><br>Экспорт в формате: <select name="format" class="styled"><option value="excel">Excel</option><option value="word">Word</option><option value="openoffice">OpenOffice</option></select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right"><img src="/img/show.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/comment\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Список мнений по заданным критериям появится в новой вкладке.">Показать</font></a> <img src="/img/pdf.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/export\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Сохранить отчет в формате Word.">Экспорт</font></a></td>
			</tr>
			</table>
			</form>
			</div>
    </div>
';

/*
======================= EOF new design
*/













	/*$html_out.='
	<div id="topbox">
		<div class="metrixbox ui-corner-all"><em class="metrixtag">ВСЕГО УПОМИНАНИЙ</em><br><em class="metrix">'.$other.'</em><em class="metrixdyn"> +0%</em><br><font color="green">+</font>0/<font color="red">-</font>0</div>
		<div class="metrixbox ui-corner-all"><em class="metrixtag">АУДИТОРИЯ</em><br><em class="metrix">'.$metrics['value'].'</em><em class="metrixdyn"> +0%</em></div>
		<div class="metrixbox ui-corner-all"><em class="metrixtag">ЦИТИРУЕМОСТЬ</em><br><em class="metrix">'.((intval((1/($other/250))*100)+intval((1/($metrics['value']/1000))*100))/100).'</em><em class="metrixdyn"> +0%</em></div>
		<div class="metrixbox ui-corner-all"><em class="metrixtag">ВОВЛЕЧЕННОСТЬ</em><br><em class="metrix">'.(intval($other/$metrics['value']*100)/100).'</em><em class="metrixdyn"> +0%</em></div>
		<div class="metrixbox ui-corner-all"><em class="metrixtag">ДОВЕРИЕ</em><br><em class="metrix">0%</em><em class="metrixdyn"> +0%</em></div>
	</div>
	<div id="leftbox">
		<div class="metrixbox ui-corner-all" style="height: 160px;"><em class="metrixtag">РЕСУРСЫ</em><br><div id="socialgraph" style="width: 160px; height: 160px; margin: 3px -8px -8px -8px; padding: 3px -8px -8px -8px;"></div></div>
		<div class="metrixbox ui-corner-all" style="height: 210px;"><em class="metrixtag">СПИКЕРЫ</em><br><table style="font-size: 10px;">';
		foreach ($metrics['speakers']['nick'] as $i=>$speaker){
			$html_out.='<tr><td>'.($i+1).'.</td><td> <a href="http://www.twitter.com/'.$speaker.'" target="_blank">'.$speaker.'</a></td><td> <a href="#" onclick="document.getElementById(\'nname\').value=\''.$speaker.'\'; document.getElementById(\'filternameform\').action=\'/comment\';document.getElementById(\'filternameform\').submit();">'.$metrics['speakers']['posts'][$i].'</a></td></tr>';
			if ($i==9) break;
		}
		$html_out.='</table></div>
		<div class="metrixbox ui-corner-all" style="height: 210px;"><em class="metrixtag">ПРОМОУТЕРЫ</em><br><table style="font-size: 10px;">';
		foreach ($metrics['promotion']['nick'] as $i=>$speaker){
			$html_out.='<tr><td>'.($i+1).'.</td><td> <a href="http://www.twitter.com/'.$speaker.'" target="_blank">'.$speaker.'</a></td><td> '.$metrics['promotion']['readers'][$i].'</td></tr>';
			if ($i==9) break;
		}
			$html_out.='</table></div>
	</div>
	';*/
/*
$html_out .= '

	<div class="filterbox ui-corner-all">
		<b><u>Мастер отчетов</u></b><br><br>
		<form action="/comment" method="post" id="filternameform" target="_blank">
			<input type="hidden" name="order_id" value="'.$order_id.'">
			<input type="hidden" id="nname" name="snick" value="">
		</form>
		<form action="/comment" method="post" id="filterform" target="_blank">
		<input type="hidden" name="order_id" value="'.$order_id.'">
	<table>
		<tr>
			<td><img src="/img/post.png" style="margin: -2px 3px -2px 5px;"> <font class="vtip" title="Возможно выбрать упоминания без спама и рекламы.<br>Для добавления сообщения в избранное нужно перейти в меню просмотра.">Сообщения</font></td>
			<td style="font-size: 11px;">
					<input type="radio" name="showmode" value="showfav"/> избранные&nbsp;
					<input type="radio" name="showmode" value="notspam"/> без спама&nbsp;
					<input type="radio" name="showmode" value="onlyspam"/> только спам&nbsp;
					<input type="radio" name="showmode" value="showall" checked="true"/> все&nbsp;
					<input type="checkbox" checked="checked" name="unrep" class="rescheck"/> фильтровать дубли&nbsp;
			</td>
		</tr>
		<tr>
			<td><img src="/img/time.png" style="margin: -2px 3px -2px 5px;"> <font class="vtip" title="Выберите временной период, за который вы хотите исследовать упоминания или используйте стандартные временные периоды.">Время</font></td>
			<td style="font-size: 11px;">с <input name="ntime" id="ntime" value="10.10.2010" type="text"> по <input name="etime" id="etime" value="10.11.2010" type="text"> <b>или</b> <a href="#" onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">день</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-691200000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">неделя</a> <a href="#"  onclick="var today = new Date(); $(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-2678400000)); $(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', today-86400000));">месяц</a></td>
		</tr>
		<tr>
			<td><img src="/img/nastr.png" style="margin: -2px 3px -2px 5px;"> <font class="vtip" title="Выберите эмоциональную окраску мнений.">Тональность</font></td>
			<td style="font-size: 11px;">
				<input type="checkbox" checked="checked" name="neutral" class="rescheck"/> нейтральные&nbsp;
				<input type="checkbox" checked="checked" name="positive" class="rescheck"/> положительные&nbsp;
				<input type="checkbox" checked="checked" name="negative" class="rescheck"/> отрицательные&nbsp;
			</td>
		</tr>
		<tr>
			<td valign="top"><img src="/img/sources.png" style="margin: -2px 3px -2px 5px;"><font class="vtip" title="Выберите ресурсы, по которым хотите посмотреть выдачу.">Ресурсы</font></td>
			<td style="font-size: 11px;">


				';
				
			if ($order_id!=0)
			{
				unset($sources);
				$fn = "data/blog/".intval($order_id).".src";
				$h = fopen($fn, "r");
				$data = fread($h, filesize($fn));
				$sources=json_decode($data, true);
				$other=0;
				$k=0;
				foreach ($sources as $i => $source)
				{
					$k++;
					if ($k<11)
					{
						if (($i=="facebook.com") || ($i=="vkontakte.ru"))
						{
							$html_out.='<input type="checkbox" checked="checked" name="res_'.$i.'" class="rescheck" id="ssoc" value="true"/> '.$i.' ('.$source.')&nbsp;';
						}
						else
						if ($i=="mail.ru")
						{
							$html_out.='<input type="checkbox" checked="checked" name="res_'.$i.'" class="rescheck" id="novres" value="true"/> '.$i.' ('.$source.')&nbsp;';
						}
						else
						if ($i=="twitter.com")
						{
							$html_out.='<input type="checkbox" checked="checked" name="res_'.$i.'" class="rescheck" id="microb" value="true"/> '.$i.' ('.$source.')&nbsp;';
						}
						else
						{
							$html_out.='<input type="checkbox" checked="checked" name="res_'.$i.'" class="rescheck" id="forabl" value="true"/> '.$i.' ('.$source.')&nbsp;';
						}
						if (($k%4)==0) $html_out.= '<br>';
						$socialall+=$source;
					}
					else
					{
						if ($k==11)
						{
							$html_out1.='<div id="othr" style="min-width: 200px; min-height: 200px; overflow:auto; z-index: 999; width: 200px; height:200px; border: 1px solid black; position: absolute; top: 390px; left: 520px; background-color: #FFFFFF;"><input type="checkbox" name="res_'.$i.'" checked="checked" class="rescheck1" id="resch1" value="true"/> '.$i.' ('.$source.')&nbsp;<br>';
						}
						else
						{
							if (($i=="facebook.com") || ($i=="vkontakte.ru"))
							{
								$html_out1.='<input type="checkbox" name="res_'.$i.'" checked="checked" class="rescheck1" id="ssoc" value="true"/> '.$i.' ('.$source.')&nbsp;<br>';
							}
							else
							if ($i=="mail.ru")
							{
								$html_out1.='<input type="checkbox" name="res_'.$i.'" checked="checked" class="rescheck1" id="novres" value="true"/> '.$i.' ('.$source.')&nbsp;<br>';
							}
							else
							if ($i=="twitter.com")
							{
								$html_out1.='<input type="checkbox" name="res_'.$i.'" checked="checked" class="rescheck1" id="microb" value="true"/> '.$i.' ('.$source.')&nbsp;<br>';
							}
							else
							{
								$html_out1.='<input type="checkbox" name="res_'.$i.'" checked="checked" class="rescheck1" id="forabl" value="true"/> '.$i.' ('.$source.')&nbsp;<br>';
							}
						}
						$other+=$source;
					}
				}
				$html_out1.='</div>';
				$socialother=$other;//<input type="checkbox" checked="checked" name="res_other" class="rescheck" value="true" />
				$html_out.='<input type="checkbox" class="rescheck" id="op" value="true" onclick="if ($(\'#op\').attr(\'checked\')){ $(\'.rescheck1\').attr(\'checked\',\'checked\');} else { $(\'.rescheck1\').attr(\'checked\',\'\');}" checked="checked"/><a id="lothr" href="#">другие</a> ('.$other.')&nbsp;
';
				$html_out.=$html_out1;
				fclose($h);
			}				*/
			
	/*							
$html_out.=				'
				<br><input type="checkbox" name="markt" checked="checked" class="rescheck2" id="resch11" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'ssoc\') { if ($(\'#resch11\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>социальные сети&nbsp;<input type="checkbox" name="markt1" checked="checked" class="rescheck2" id="resch12" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'novres\') { if ($(\'#resch12\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>новостные ресурсы&nbsp;<input type="checkbox" name="markt2" checked="checked" class="rescheck2" id="resch13" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'microb\') { if ($(\'#resch13\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>микроблоги&nbsp;<input type="checkbox" name="markt3" checked="checked" class="rescheck2" id="resch14" value="true" onclick="$(\'input\').each(function(){ if ($(this).attr(\'id\')==\'forabl\') { if ($(\'#resch14\').attr(\'checked\')==false) {$(this).attr(\'checked\',\'\');} else {$(this).attr(\'checked\',\'checked\');}}});"/>форумы и блоги<br><a href="#" style="font-size: 12px;" onclick="$(\'.rescheck\').removeAttr(\'checked\'); $(\'.rescheck1\').attr(\'checked\',\'\');">снять все</a> <a href="#" style="font-size: 12px;" onclick="$(\'.rescheck\').attr(\'checked\',\'checked\'); $(\'.rescheck1\').attr(\'checked\',\'checked\');">отметить все</a><a href="#" style="font-size: 12px;" onclick="$(\'input\').each(function(){ if($(this).attr(\'id\')==(\'blogs\')) {$(this).attr(\'checked\',\'checked\');}});"></a><br>Экспорт в формате: <select name="format"><option value="excel">Excel</option><option value="pdf">PDF</option><option value="word">Word</option></select>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right"><img src="/img/show.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/comment\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Список мнений по заданным критериям появится в новой вкладке.">Показать</font></a> <img src="/img/pdf.png" border="0" style="margin: -2px 3px -2px 5px;"> <a href="#" onclick="document.getElementById(\'filterform\').action=\'/export\';document.getElementById(\'filterform\').submit();"><font class="vtip" title="Сохранить отчет в формате Word.">Экспорт</font></a></td>
		</tr>
		</table>
		</form>
	</div>

	<div class="ui-corner-all" style="border: 1px solid #eee;padding: 5px;margin: 3px;width: 770px; float: left;">
		<table>
		<tr><td>
';
*/
$indother=1;

$html_out.='
	<script type="text/javascript">


		var data = [
		';
	$zap='';
	//$fn = "/var/www/data/blog/".intval($order_id).".graph";
	//$h = fopen($fn, "r");
	//$graph = fread($h, filesize($fn));
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

	//endof


	/*echo 'from: ';
	print_r(getdate($order['order_start']));
	echo ' to: ';
	print_r(getdate($order['order_end']));
	*/
	/*for($t=$order['order_start'];$t<$order['order_end'];$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t))){
		print_r(getdate($t));
	}
	*/

	//for($t=$order['order_start'];$t<$order['order_end'];$t+=86400)
	for($t=$order['order_start'];$t<$order['order_end'];$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
	//$t=mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t));
	$html_out.= $zap.intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
	$zap=', ';
	}

$html_out.='
		];

		var masterChart,
			detailChart;

		var chart;

		function showgraph()
		{


function createGeo()
{
			   chart1 = new Highcharts.Chart({
			      chart: {
			         renderTo: \'geograph\',
			         plotBackgroundColor: null,
			         plotBorderWidth: null,
					reflow: false,
					margin: -10,
					padding: -10,
			         plotShadow: false
			      },
			      title: {
			         text: \'\'
			      },
			      tooltip: {
			         formatter: function() {
			            return \'<font style="font-size: 10px; font-weight: bold;">\'+ this.point.name +\':</font> <font style="font-size: 10px;">\'+ this.y +\'%</font>\';
			         }
			      },
				  credits: {
					enabled: false
				  },
		      legend: {
		         enabled: false
		      },
		      exporting: {
		         enabled: false
		      },
			      plotOptions: {
			         pie: {
			            allowPointSelect: true,
			            cursor: \'pointer\',
			            dataLabels: {
			               enabled: false,
			               color: \'#000000\',
			               connectorColor: \'#000000\',
			               formatter: function() {
			                  return \'<b>\'+ this.point.name +\'</b>: \'+ this.y +\' %\';
			               }
			            }
			         }
			      },
			       series: [{
			         type: \'pie\',
			         name: \'\',
			         data: [
	';
	foreach ($metrics['location'] as $k => $item)
	{
		if ($k!='')
		{
			$html_out.='[\''.$k.'\','.intval((($item/$c_posts)*100)).'],';
		}
		//else
		{
			//$html_out.='[\'Другие\','.intval($item/$c_posts*100).'],';
		}
	}
	$html_out=substr($html_out,0,strlen($html_out)-1);
	//$html_out.=',[\'другие\', '.(100-$tensocial).']';
	$html_out.='
			         ]
			      }]
			   });
}

			function createSocial() {
		   chart = new Highcharts.Chart({
		      chart: {
		         renderTo: \'socialgraph\',
		         plotBackgroundColor: null,
		         plotBorderWidth: null,
				reflow: false,
				margin: -10,
				padding: -10,
		         plotShadow: false
		      },
		      title: {
		         text: \'\'
		      },
		      tooltip: {
		         formatter: function() {
		            return \'<font style="font-size: 10px; font-weight: bold;">\'+ this.point.name +\':</font> <font style="font-size: 10px;">\'+ this.y +\'%</font>\';
		         }
		      },
			  credits: {
				enabled: false
			  },
	      legend: {
	         enabled: false
	      },
	      exporting: {
	         enabled: false
	      },
		      plotOptions: {
		         pie: {
		            allowPointSelect: true,
		            cursor: \'pointer\',
		            dataLabels: {
		               enabled: false,
		               color: \'#000000\',
		               connectorColor: \'#000000\',
		               formatter: function() {
		                  return \'<b>\'+ this.point.name +\'</b>: \'+ this.y +\' %\';
		               }
		            }
		         }
		      },
		       series: [{
		         type: \'pie\',
		         name: \'\',
		         data: [
';
$k=0;
$tensocial=0;
unset($socialar);
foreach ($sourcesgr as $i => $source)
{
	$k++;
	if ($k<9)
	{
		//if ($k>1) $html_out.=',';
		//$html_out.='[\''.$i.'\', '.(intval($source/$socialall*1000)/10).']';
		$socialar['name'][$k-1]=$i;
		$socialar['count'][$k-1]=intval($source/$socialall*1000)/10;
		$tensocial+=intval($source/$socialall*1000)/10;
	}
}
$socialar['count'][9]=100-$tensocial;
$socialar['name'][9]='другие';
array_multisort($socialar['count'], $socialar['name']);
$tt=0;
for($i=0;$i<9;$i++){
	if ($tt>0) $html_out.=',';
	if ($socialar['count'][8-$i]!=0) { $html_out.='[\''.$socialar['name'][8-$i].'\', '.intval($socialar['count'][8-$i]).']'; $tt++; }
}
//$html_out.=',[\'другие\', '.(100-$tensocial).']';
$html_out.='
		         ]
		      }]
		   });
		
	}


	Highcharts.setOptions({
		lang: {
			months: [\'Янв\', \'Фев\', \'Мар\', \'Апр\', \'Mай\', \'Июн\', 
				\'Июл\', \'Авг\', \'Сен\', \'Окт\', \'Ноя\', \'Дек\'],
			weekdays: [\'Вс\', \'Пн\', \'Вт\', \'Ср\', \'Чт\', \'Пт\', \'Сб\']
		}
	});


			// create the master chart
			function createMaster() {
				masterChart = new Highcharts.Chart({
					chart: {
						renderTo: \'master-container\',
						reflow: false,
						borderWidth: 0,
						backgroundColor: null,
						marginLeft: 50,
						marginRight: 20,
						zoomType: \'x\',
						events: {

							// listen to the selection event on the master chart to update the 
							// extremes of the detail chart
							selection: function(event) {
								var extremesObject = event.xAxis[0],
									min = extremesObject.min,
									max = extremesObject.max,
									detailData = [],
									xAxis = this.xAxis[0];

								$(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', min+86400000));
								$(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', max+86400000));
								// reverse engineer the last part of the data
								jQuery.each(this.series[0].data, function(i, point) {
									if (point.x > min && point.x < max) {
										detailData.push({
											x: point.x,
											y: point.y
										});
									}
								});

								// move the plot bands to reflect the new detail span
								xAxis.removePlotBand(\'mask-before\');
								xAxis.addPlotBand({
									id: \'mask-before\',
									from: Date.UTC('.(date('Y',$order['order_start'])).','.(date('m',$order['order_start'])-1).','.(date('d',$order['order_start'])).'),
									to: min,
									color: \'rgba(0, 0, 0, 0.1)\'
								});

								xAxis.removePlotBand(\'mask-after\');
								xAxis.addPlotBand({
									id: \'mask-after\',
									from: max,
									to: '.$order['order_end'].'000,
									color: \'rgba(0, 0, 0, 0.1)\'
								});

								detailChart.series[0].setData(detailData);

								return false;
							}
						}
					},
					title: {
						text: null
					},
					xAxis: {
						type: \'datetime\',
						showLastTickLabel: true,
						maxZoom: 14 * 24 * 3600000, // fourteen days
						plotBands: [{
							id: \'mask-before\',
							from: Date.UTC('.(date('Y',$order['order_start'])).','.(date('m',$order['order_start'])-1).','.(date('d',$order['order_start'])).'),
							to: '.$order['order_end'].'000,
							color: \'rgba(255, 255, 255, 0.2)\'
						}],
						title: {
							text: null
						}
					},
					yAxis: {
						gridLineWidth: 0,
						labels: {
							enabled: false
						},
						title: {
							text: null
						},
						min: 0,
						showFirstLabel: false
					},
					tooltip: {
						formatter: function() {
							return false;
						}
					},
					legend: {
						enabled: false
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						series: {
							fillColor: {
								linearGradient: [0, 0, 0, 70],
								stops: [
									[0, \'#3c6087\'],
									[1, \'rgba(0,0,0,0)\']
								]
							},
							lineWidth: 1,
							marker: {
								enabled: false
							},
							shadow: false,
							states: {
								hover: {
									lineWidth: 1						
								}
							},
							enableMouseTracking: false
						}
					},

					series: [{
						type: \'area\',
						name: \'Упоминания\',
						pointInterval: 24 * 3600 * 1000,
						pointStart: Date.UTC('.(date('Y',$order['order_start'])).','.(date('m',$order['order_start'])-1).','.(date('d',$order['order_start'])).'),
						data: data
					}],

					exporting: {
						enabled: false
					}

				}, function(masterChart) {
					createDetail(masterChart)
				});
			}

			// create the detail chart
			function createDetail(masterChart) {

				// prepare the detail chart
				var detailData = [],
					detailStart = Date.UTC('.(date('Y',$order['order_start'])).','.(date('m',$order['order_start'])-1).','.(date('d',$order['order_start'])).');

				jQuery.each(masterChart.series[0].data, function(i, point) {
					if (point.x >= detailStart) {
						detailData.push(point.y);
					}
				});

				// create a detail chart referenced by a global variable
				detailChart = new Highcharts.Chart({
					chart: {
						defaultSeriesType: \'spline\',
						marginBottom: 120,
						renderTo: \'detail-container\',
						reflow: false,
						marginLeft: 50,
						marginRight: 20,
						style: {
							position: \'absolute\'
						}
					},
					credits: {
						enabled: false
					},
					title: {
						text: \'График упоминаний\'
					},
					subtitle: {
						text: \'упоминания сгруппированы по дням\'
					},
					xAxis: {
						type: \'datetime\',
						maxZoom: 14 * 24 * 3600000,
						minorTickLength: 0,
						 minorTickInterval: 7 * 24 * 3600 * 1000, // one week
				         minorTickWidth: 1,
				         gridLineWidth: 1
					},
					yAxis: {
						min: 0,
						title: null,
						maxZoom: 0.1,
		         labels: {
		            align: \'left\',
		            formatter: function() {
		               return Highcharts.numberFormat(this.value, 0);
		            }
		         },
					},
					tooltip: {
						formatter: function() {
							var point = this.points[0];
							return \'<b>Упоминания</b><br/>Дата: \'+
								Highcharts.dateFormat(\'%A %B %e %Y\', this.x) + \'<br/>\'+
								\'Кол-во: \'+ Highcharts.numberFormat(point.y, 0) +\'\';
						},
						shared: true
					},
					legend: {
						enabled: false
					},
					plotOptions: {
						spline: {
							linewidth: 4,
							marker: {
								    	fillColor: \'#FFFFFF\',
								        lineWidth: 2,
										radius: 2.5,
								        lineColor: null // inherit from series
							}
						}
					},
					series: [{
						name: \'Упоминания\',
						pointStart: detailStart,
						pointInterval: 24 * 3600 * 1000,
						color: \'#3c6087\',
						data: detailData,
				            cursor: \'pointer\',
				            point: {
				               events: {
				                  click: function() {
									$(\'#ntime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', this.x));
									$(\'#etime\').attr(\'value\',Highcharts.dateFormat(\'%d.%m.%Y\', this.x));
				                  }
				               }
				            },
					}],

					exporting: {
						enabled: true
					}

				});
			}

			// make the container smaller and add a second container for the master chart
			var $container = $(\'#container\')
				.css(\'position\', \'relative\');

			var $detailContainer = $(\'<div id="detail-container">\')
				.appendTo($container);

			var $masterContainer = $(\'<div id="master-container">\')
				.css({ position: \'absolute\', top: 300, height: 80, width: \'100%\' })
				.appendTo($container);

			// create master and in its callback, create the detail chart
			createSocial();
			createMaster();
			createGeo();

		}
		
	</script>
';
//720 400
/*
$html_out.='		
	<div id="container" style="width: 720px; height: 400px; margin: 10px 20px;"></div>
		</td></tr>
		<tr>
		<td>
		<div id="YMapsID" style="width:700px;height:400px; margin: 10px 30px;"></div>
		</td>
		</tr>
		</table>
	</div>
';		<div id="YMapsID" style="width:700px;height:400px; margin: 10px 30px;"></div>
*/

$html_out.='		
<div class=\'mapbox ui-corner-all\'>
'.date("d.m.Y",$order['order_start']).' - '.date("d.m.Y",$order['order_end']).'
	<div id="container" style="width: 650px; height: 400px;"></div>
	    </div>
	  </div>
	  <div class=\'push\'></div>
	</div>
	</div>
';
stop_tpl();

?>
