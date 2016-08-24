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
//<link href=\'/css/index_lk.css\' rel=\'stylesheet\' type=\'text/css\' />
$params.='
data=\'09.09.2011\';
time_beg=\'09.09.2011\';
';
start_tpl('','','');

$db = new database();
$db->connect();

//auth();
if (!$loged) die();
$html_out .='	<div id=\'table\'>';
/*foreach($_COOKIE as $n => $ck)
{
        if (substr($n,0,5)=='order') 
        {
		unset($_COOKIE[$n]);
        }
}*/

/*$html_out .= '
    <table width="100%" height="100%" align="center" cellpadding="0" cellspacing="0" border="0">
';*/
// login information set
//function cabinet()
//{
	//global $db, $config, /*$loged, $user_email, $user_pass,*/$user;
	//$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' and user_pass=\''.md5($user_pass).'\' LIMIT 1');
	//$row = $db->fetch($res);
	//if (intval($row['user_id'])!=0)
	//{
	//	$user_id=$row['user_id'];
	//	$_SESSION['user_email']=$user_email;
	//	$_SESSION['user_pass']=$user_pass;
	//	$loged=1;
	//	$user=$row;
		//echo $user['user_email'].' вход выполнен<br>';
                $rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($user['user_id']));
                while ($rw = $db->fetch($rs)) {
                        //$html_out .= '<tr><td class="title">Тариф <a href="/tariff/'.$rw['tariff_id'].'"><span class="rl" style="font-size: 16px;">'.$rw['tariff_name'].'</span></a> '.$rw['tariff_desc'].'</tr></td>
//';
		$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($user['user_id']).' and ut_id='.$rw['ut_id']);
		$i=0;
		/*echo '<form action="/new/comment" method="post" id="filternameform" target="_blank">
			<input type="hidden" id="or_id" name="order_id" value="'.$order_id.'">
			<input type="hidden" id="nname" name="all_main" value="true">
			<input type="hidden" id="nname1" name="format" value="pdf">
		</form>';*/
		$html_out.='<div style="padding: 0 50px 0 50px; margin: 0 50px 0 50px;">';
		while ($row = $db->fetch($res)) {
			//echo $row['order_id'];
			//$fn = "/var/www/data/blog/".$row['order_id'].".metrics";
			//$h = fopen($fn, "r");
			//$data = fread($h, filesize($fn));
			$data=$row['order_metrics'];
			$metrics=json_decode($data,true);
			//fclose($h);
			unset($sources);
			unset($data);
			//$fn = "/var/www/data/blog/".$row['order_id'].".src";
			//$h = fopen($fn, "r");
			//$data = fread($h, filesize($fn));
			$data=$row['order_src'];
			$sources=json_decode($data, true);
			$k=0;
			foreach ($sources as $ii => $source)
			{
				$other+=$source;
			}
			//print_r($metrics);
			//$hhandle=fopen("/var/www/data/blog/".$row['order_id'].".src","r");
			//$res_info=fread($hhandle,filesize("/var/www/data/blog/".$row['order_id'].".src"));
			$res_info=$row['order_src'];
			//echo $res_info;
			//fclose($hhandle);
			$mas_res=json_decode($res_info,true);
			$res_count=count($mas_res);
			//print_r($res_count);
			$coll=0;
			foreach ($mas_res as $ind => $item)
			{
				$coll+=$item;
			}
			if (($coll<10) || ($coll>20))
			{
				if (($coll % 10)==1)
				{
					$text_col="запись";
				}
				else
				if ((($coll % 10)==2) || (($coll % 10)==3) || (($coll % 10)==4))
				{
					$text_col="записи";
				}
				else
				{
					$text_col="записей";
				}
			}
			else
			{
				$text_col="записей";
			}
			if (($res_count<10) || ($res_count>20))
			{
				if (($res_count % 10)==1)
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
			$i++;
			if($row['order_end']==0)$row['order_end']=mktime(0,0,0,date('m'),date('d'),date('Y'));
			$html_out .= '<div class=\'record\'>
            <div class=\'top_line\'>
              <span class=\'number\'>'.$i.'.</span>
              <span class=\'report_url\'>
                '.($row['order_last']>$row['order_start']?'<a href="order/'.$row['order_id'].'" style="font-size: 22px;">':'<b style="font-weight: normal; font-size: 22px;">').((mb_strlen($row['order_name'], 'UTF-8')>0)?$row['order_name']:$row['order_keyword']).($row['order_last']>$row['order_start']?'</a>':'</b>').'
              </span>
              <span class=\'images\'>
                '.($row['order_last']>$row['order_start']?'<a href=\'/new/rss?order_id='.$row['order_id'].'\' target="blank" style="text-decoration: none;">
                  <img src=\'/img/001.gif\' />
                </a>':'').'
              </span>
            </div>
            <div class=\'bot_line\'>
              <span class=\'descr\'>
            	<span class=\'report_date\'>
              		'.date('d.m.Y',$row['order_start']).' — '.date('d.m.Y',$row['order_end']).'
            	</span> / 
                <span class=\'records_num\'>
                  <a href=\'#\' class="vtip" title="умоминания">'.$coll.'</a>
                </span>
                '.$text_col.' на
                <span class=\'resources_num\'>
                  <a href=\'#\' class="vtip" title="ресурсы">'.$res_count.'</a>
                </span>
                '.$text_res.' / <a href="#" class="vtip" title="аудитория">'.$metrics['value'].'</a> './*'/ <a href="#" class="vtip" title="цитируемость">'.((intval((1/($coll/250))*100)+intval((1/($metrics['value']/1000))*100))/100).'</a> / <a href="#" class="vtip" title="вовлеченность">'.(intval($coll/$metrics['value']*100)/100).'</a> */'
 / 
     <span class=\'report_status\'>
       '.($row['order_last']>$row['order_start']?'отчет готов <span class="rln">('.date('d.m.Y',$row['order_last']).')</span>':'отчет готовится').'
     </span>
              </span>
            </div>
          </div>
          ';
			/*$html_out .= '<tr><td class="'.($i%2?'list2':'list').'"><input type="checkbox" name="order'.$row['order_id'].'" class="listch">'.$i.'. '.($row['order_last']>$row['order_start']?'<a href="order/'.$row['order_id'].'">':'').'<span class="gl">'.$row['order_keyword'].'</span>'.($row['order_last']>$row['order_start']?'</a>':'').'<br>
<span class="sl">состояние: '.($row['order_last']>$row['order_start']?'отчет готов <span class="rln">('.date('d.m.Y',$row['order_last']).')</span>':'отчет готовится').', интервал времени: <span class="rln">'.date('d.m.Y',$row['order_start']).'-'.date('d.m.Y',$row['order_end']).'</span></span></td></tr>
';*/
		}
		if ($i==0) $html_out .= 'услуги не выбраны';
		}
	//}
//}
if ($user['user_email']=='mail@wobot.ru') $html_out .= '<div id=\'button_new_trend\'>
<br>
</div>
</div>
';
else $html_out .= '<div id=\'button_new_trend\' style="text-align: center;">
  <a href=\'#\' onclick="loadmodal(\'/add\');return false;">
    <img src=\'/img/button_new_trend.gif\' />
  </a>
<table height="30%">
<td>
&nbsp;
</td>
</table>
</div>
</div>';
/*$html_out .= '
<tr><td class="title"><a href="order/add"><span class="gl">Добавить тему</span></a>, или создать <a href="#" onclick="loaditem(\'user/order?order_id=\'+$(\'.listch:checked\').map(function() {return $(this).attr(\'name\');}).get().join(),\'#cntnt\', function() { makemap(); makegraph(); });return false;"><span class="gl">Мульти-отчет</span></a></td></tr>
    <tr><td class="spacer" height="100%" colspan="3">&nbsp;</td></tr>
</table>
';*/

stop_tpl();
?>
