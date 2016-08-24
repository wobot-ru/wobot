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
				$cont=parseURLproxy( 'http://m.market.yandex.ru/grades-shop.xml?shop_id='.$vars['shop_id'].'&sort_by=date&page_num='.intval($page),$mproxy[rand(0,399)] );
				if ($cont=='') $attemp++;
			}
			while (($cont=='') && ($attemp<30));
			$link='http://market.yandex.ru/shop/'.$vars['shop_id'].'/reviews?sort_by=date&page_num='.floor(intval($page)/10);
			echo 'http://m.market.yandex.ru/grades-shop.xml?shop_id='.$vars['shop_id'].'&sort_by=date&page_num='.intval($page)."\n";
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
		$regex='/<p class=\"?b-comment\"?>(?<cont>.*?)<\/p>/isu';
		preg_match_all($regex, $cont, $out_cont);
		$regex='/<span class="b\-plate__verybad">(?<cont>.*?)<\/span>/isu';
		preg_match_all($regex, $cont, $out_cont_bad);
		// print_r($out_cont);
		$regex='/<span class=\"?b-plate__date\"?>(?<time>.*?)<\/span>/isu';
		preg_match_all($regex, $cont, $out_time);
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
		// print_r($out_time);
		if ($out_time['time'][0]=='сегодня') $time=mktime(0,0,0,date('n'),date('j'),date('Y'));
		elseif ($out_time['time'][0]=='вчера') $time=mktime(0,0,0,date('n'),date('j')-1,date('Y'));
		elseif (preg_match('/\d+\s[а-яё]+\s\d+/isu', $out_time['time'][0]))
		{
			// echo '++++';
			$regex='/(?<day>\d+)\s(?<mon>[а-яё]+)\s(?<year>\d+)/isu';
			preg_match_all($regex, $out_time['time'][0], $otime);
			$time=mktime(0,0,0,$masmon[$otime['mon'][0]],$otime['day'][0],$otime['year'][0]);
		}
		elseif ($out_time['time'][0]!='')
		{
			echo '----';
			$regex='/(?<day>\d+)\s(?<mon>[а-яё]+)/isu';
			preg_match_all($regex, $out_time['time'][0], $otime);
			$time=mktime(0,0,0,$masmon[$otime['mon'][0]],$otime['day'][0],date('Y'));
		}

		if (trim(strip_tags(implode(' ', $out_cont['cont'])).' '.implode(' ', $out_cont_bad['cont']))=='') $break++;
		else $break=0;
		if ($break==10) break;

		if (($time<=$te)&&($time>=$ts))
		{ 
			$outpost['time'][]=$time;
			$outpost['content'][]=strip_tags(implode(' ', $out_cont['cont'])).' '.implode(' ', $out_cont_bad['cont']);
			$outpost['link'][]=$link;
			$outpost['eng'][]=0;
			$regex_nick='/<a class=\"?b-user__link\"? href="http\:\/\/(?<user_nick>.*?)\.ya\.ru\/go\-market\.xml\">/isu';
			preg_match_all($regex_nick, $cont, $out_nick);
			/*if (trim($out_nick['user_nick'][0])!='')
			{
				$regex='/<b class=\"?b\-user\"?.*?>\s*(?<nick>.*?)<\/b>/isu';
				preg_match_all($regex, $cont, $out_nick);
				print_r($out_nick);
				$out_nick['user_nick'][0]=strip_tags($out_nick['nick'][0]);
			}*/
			$outpost['author_id'][]=$out_nick['user_nick'][0];
			$outpost['author_name'][]=$out_nick['user_nick'][0];
		}
		print_r($outpost);
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
