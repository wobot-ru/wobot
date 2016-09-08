<?
session_start();

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$masmon['января']=1;
$masmon['февраля']=2;
$masmon['марта']=3;
$masmon['апреля']=4;
$masmon['мая']=5;
$masmon['июня']=6;
$masmon['июля']=7;
$masmon['августа']=8;
$masmon['сентября']=9;
$masmon['октября']=10;
$masmon['ноября']=11;
$masmon['декабря']=12;

function get_market($vars,$ts,$te)
{
	echo date('r',$ts).' '.date('r',$te)."\n";
	global $masmon,$redis;
	$p=0;
	do
	{
		sleep(1);
		if (($attemp=='')||(intval($attemp)>=10))
		{
			$mproxy=json_decode($redis->get('proxy_list'),true);
			$attemp=0;
		}
		// $mproxy=array('202.170.83.212:3128');
		// $page=4000;
		if ($vars['shop_id']!='')
		{
			do
			{
				$cont=parseURLproxy( 'https://m.market.yandex.ru/shop/'.$vars['shop_id'].'?sort_by=date&page_num='.intval($page),$mproxy[rand(0,399)] );
				// $cont=parseURLproxy( 'http://m.market.yandex.ru/grades-shop.xml?shop_id='.$vars['shop_id'].'&sort_by=date&page_num='.intval($page),$mproxy[rand(0,399)] );
				if ($cont=='') $attemp++;
			}
			while (($cont=='') && ($attemp<30));
			$link='http://market.yandex.ru/shop/'.$vars['shop_id'].'/reviews?sort_by=date&page_num='.floor(intval($page)/10);
			echo 'https://m.market.yandex.ru/shop/'.$vars['shop_id'].'?sort_by=date&page_num='.intval($page)."\n";
		}
		elseif (($vars['modelid']!='') && ($vars['hid']!=''))
		{
			do
			{
				$cont=parseURLproxy( 'http://m.market.yandex.ru/grades-model.xml?modelid='.$vars['modelid'].'&sort_by=date&hid='.$vars['hid'].'&page_num='.intval($page),$mproxy[rand(0,399)] );
				if ($cont=='') $attemp++;
			}
			while (($cont=='') && ($attemp<30));
			$link='http://market.yandex.ru/product/'.$vars['modelid'].'/reviews?sort_by=date&page_num='.floor(intval($page)/10);
			// echo 'http://m.market.yandex.ru/grades-model.xml?modelid='.$vars['modelid'].'&hid='.$vars['hid'].'&page_num='.intval($page);
		}
		if (preg_match('/charset=windows\-1251"/is', $cont)) $cont=iconv('windows-1251','UTF-8',$cont);
		// echo $cont.'***';
		$regex='/<i class="b-stars__star b-stars__star_type_full"><\/i><\/div>(?<time>[\.\d]*?)<\/div><div[^\<]*?>(?<good>.*?)<\/div><div[^\<]*?>(?<bad>.*?)<\/div>/isu';
		preg_match_all($regex, $cont, $out);
		// print_r($out);
		// die();
		// if (trim($out_time['time'][0])=='') 
		if (preg_match('/block\.yandex\.ru/isu', $cont)||($cont=='')||!preg_match('/<title>.*Яндекс\.Маркет.*<\/title>/isu', $cont))
		{
			// echo $cont."\n";
			echo 'CONT!!!!!'."\n";
			$attemp++;
			continue;
		}
		// echo '+++';
		// print_r($out_time);

		foreach ($out['time'] as $key => $item)
		{
			$time=strtotime($item);
			if (($time<=$te)&&($time>=$ts))
			{ 
				$outpost['time'][]=$time;
				$outpost['content'][]=strip_tags($out['good'][$key].' '.$out['bad'][$key]);
				$outpost['link'][]=$link;
				$outpost['eng'][]=0;
			}
		}
		// print_r($outpost);
		// die();
		// echo 'gg';
		$page++;
		if ($page>1000) return $outpost;
		echo '|'.$time.'|'.($ts-86400).'|'.$time.'|'.intval(preg_match('/block\.yandex\.ru/isu', $cont)).'|'."\n";
	}	
	while (($time>($ts-86400)&&!preg_match('/block\.yandex\.ru/isu', $cont))||preg_match('/block\.yandex\.ru/isu', $cont)||$cont==''||!preg_match('/<title>Яндекс\.Маркет<\/title>/isu', $cont));
	// print_r($outpost);
	//echo 'ggg';
	return $outpost;
}

// get_market(json_decode('{"shop_id":"115706"}',true),mktime(0,0,0,4,10,2016),mktime(0,0,0,4,21,2016));
// get_market(json_decode('{"modelid":"8454852","hid":"91491"}',true),mktime(0,0,0,9,1,2012),mktime(0,0,0,9,30,2013));

?>
