<?

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');

$db = new database();
$db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

function get_mail_ru($grid,$ts,$te)
{

	$cont=parseUrl('http://my.mail.ru/community/'.$grid.'/ajax_history');
	$mcont=json_decode($cont,true);
	$regexp='/<div.*?data-id="(?<data_id>[0-9A-Z]{16}:[0-9]{10})".*?time="([0-9]{10})".*?<a class="historylayer-link" type="historylayer" href="(?<link>[^\"]*?)".*?>(?<time_old>.*?)<\/a>.*?<div.*?class="b-history-event__event-text2  historylayer-link ".*?>(?<cont>.*?)<\/div>/isu';

	preg_match_all($regexp,$mcont[2],$out);

	//получения остальных постов
	//первичный сбор

	do{
		unset($temp);
		$next_cont=parseUrl('http://my.mail.ru/community/'.$grid.'/ajax_history?ajax_call=1&func_name=history.get&mna=&mnb=&arg_limit=50&arg_from_page=community&arg_filter=groups_history&arg_start='.($out['data_id'][count($out['data_id'])-1]));
		$tcont=json_decode($next_cont,true);
		preg_match_all($regexp,$tcont[2],$temp);
		$out['cont'] = array_merge($out['cont'], $temp['cont']);
		$out['link'] = array_merge($out['link'], $temp['link']);
		$out['data_id'] = array_merge($out['data_id'], $temp['data_id']);
		$out[2] = array_merge($out[2], $temp[2]);
		
		//print_r($out['time']);
	} while (count($temp['link'])!=0);

	//ретросбор

	foreach ($out['link'] as $key => $item)
	{
		//if ($out['time'][$key]<$ts || $out['time'][$key]>($te+86400)) continue;
		if ($out[2][$key]<$ts || $out[2][$key]>($te+86400)) continue;
		$outmas['link'][]=$out['link'][$key];
		$out['cont'][$key]=preg_replace('/<[^\<]*?>/isu', ' ', $out['cont'][$key]);
		$out['cont'][$key]=preg_replace('/\s+/isu', ' ', $out['cont'][$key]);
		$outmas['content'][]=$out['cont'][$key];
		$outmas['time'][]=$out[2][$key];
		//$outmas['data_id'][]=$out['data_id'][$key];
		//$outmas['time'][]=convertToTimestamp($out['time'][$key]);
		$outmas['engage'][]=0;
		$outmas['adv_engage'][]='';
		$outmas['author_id'][]='';
	}
	 //print_r($outmas);
	 //die();
	return $outmas;
}


//$ts = 1393632000;
///$te = 1409529600;

//get_mail_ru('tinkoff_ins',$ts,$te)

?>
