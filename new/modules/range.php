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

$db = new database();
$db->connect();

auth();
if (!$loged) die();

$jqready='
$( "#range" ).slider({
	range: true,
	min: 0,
	max: 500,
	values: [ 75, 300 ],
	slide: function( event, ui ) {
		$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
	}
});

$( ".range" ).slider({
	range: true,
	min: 0,
	max: 500,
	values: [ 75, 300 ],
	slide: function( event, ui ) {
		//alert(ui.values[ 1 ]-ui.values[ 0 ]);
		//$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
		//$( "#range" ).slider( "values", 1, $( "#range" ).slider( "values", 0 )+100 );
		//$( ".range" ).each(
			//function (ui) {
			//		this.slider("values", 1, this.slider("values", 0 )+ui.values[ 1 ]-ui.values[ 0 ]);
			//}
		//);
	}
});

$( ".range" ).bind( "slidechange", function(event, ui) {
	alert(ui.values[ 1 ]-ui.values[ 0 ]);
	//$(".range").each( alert(this) );
	var ranges = document.getElementsByClassName("range");
	//$.each(ranges,function(this){
	//	alert(this);
	//});
});

$( "#amount" ).val( "$" + $( "#range" ).slider( "values", 0 ) +
	" - $" + $( "#range" ).slider( "values", 1 ) );
	
//$( "#range" ).slider( "values", 1, $( "#range" ).slider( "values", 0 )+100 );
';
start_tpl($jqready,'<link href=\'/css/index_lk.css\' rel=\'stylesheet\' type=\'text/css\' />');

$html_out .='	<div id=\'table\'>';
$html_out .= '
	<p>
		<label for="amount">Price range:</label>
		<input type="text" id="amount" style="border:0; color:#f6931f; font-weight:bold;" />
	</p>

	<div class="range" id="range"></div>
	<div class="range"></div>
	<div class="range"></div>
	<div class="range"></div>
	<div class="range"></div>
';

                $rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE user_id='.intval($user['user_id']));
                while ($rw = $db->fetch($rs)) {

		$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($user['user_id']).' and ut_id='.$rw['ut_id']);
		$i=0;
		echo '<form action="/new/comment" method="post" id="filternameform" target="_blank">
			<input type="hidden" id="or_id" name="order_id" value="'.$order_id.'">
			<input type="hidden" id="nname" name="all_main" value="true">
			<input type="hidden" id="nname1" name="format" value="pdf">
		</form>';
		while ($row = $db->fetch($res)) {
			$data=$row['order_metrics'];
			$metrics=json_decode($data,true);
			unset($sources);
			unset($data);
			$data=$row['order_src'];
			$sources=json_decode($data, true);
			$k=0;
			foreach ($sources as $ii => $source)
			{
				$other+=$source;
			}
			$res_info=$row['order_src'];
			$mas_res=json_decode($res_info,true);
			$res_count=count($mas_res);
			$coll=-1;
			foreach ($mas_res as $ind => $item)
			{
				$coll+=$item;
			}
			$i++;
			if($row['order_end']==0)$row['order_end']=mktime(0,0,0,date('m'),date('d'),date('Y'));
			$html_out .= '<div class=\'record\'>
            <div class=\'top_line\'>
              <span class=\'number\'>'.$i.'.</span>
              <span class=\'report_url\'>
                '.($row['order_last']>$row['order_start']?'<a href="order/'.$row['order_id'].'" style="font-size: 22px;">':'<b style="font-weight: normal; font-size: 22px;">').((mb_strlen($row['order_name'], 'UTF-8')>0)?$row['order_name']:$row['order_keyword']).($row['order_last']>$row['order_start']?'</a>':'</b>').'
              </span>
              <span class=\'report_status\'>
                '.($row['order_last']>$row['order_start']?'отчет готов <span class="rln">('.date('d.m.Y',$row['order_last']).')</span>':'отчет готовится').'
              </span>

              <span class=\'report_date\'>
                ('.date('d.m.Y',$row['order_start']).' — '.date('d.m.Y',$row['order_end']).')
              </span>
              <span class=\'images\'>
                <!--<a href=\'#\' style="text-decoration: none;" onclick="document.getElementById(\'or_id\').value=\''.$row['order_id'].'\'; document.getElementById(\'filternameform\').action=\'/new/export\';document.getElementById(\'filternameform\').submit();"">
                  <img src=\'/img/095.gif\' />
                </a>-->
                <!--<a href=\'#\' style="text-decoration: none;">
                  <img src=\'/img/004.gif\' />

                </a>-->
                '.($row['order_last']>$row['order_start']?'<a href=\'/new/rss?order_id='.$row['order_id'].'\' target="blank" style="text-decoration: none;">
                  <img src=\'/img/001.gif\' />
                </a>':'').'
              </span>
            </div>
            <div class=\'bot_line\'>
              <span class=\'descr\'>
                <span class=\'records_num\'>

                  <a href=\'#\'>'.$coll.'</a>
                </span>
                '.$text_col.' на
                <span class=\'resources_num\'>
                  <a href=\'#\'>'.$res_count.'</a>
                </span>
                '.$text_res.' / <a href="#" class="vtip" title="аудитория">'.$metrics['value'].'</a>
              </span>
            </div>
          </div>
          ';
		}
		if ($i==0) $html_out .= 'услуги не выбраны';
		}

if ($user['user_email']=='mail@wobot.ru') $html_out .= '<div id=\'button_new_trend\'>
<br>
</div>
</div>
';
else $html_out .= '<div id=\'button_new_trend\'>
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

stop_tpl();
?>
