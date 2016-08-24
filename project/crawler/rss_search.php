<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('com/config_bmstu.php');
require_once('com/db_bmstu.php');
//require_once('/var/www/bot/kernel.php');
require_once('kernel.php');
require_once('get_rss.php');
require_once('idna_convert.class.php');

$db = new database();
$db->connect();

//$db_bmstu = new database_bmstu();
error_reporting(0);
//print_r($_SERVER['argv']);
if (mb_strlen($_SERVER['argv'][1],'UTF-8')==0)
{
	die();
}
else $url=($_SERVER['argv'][1]);
$id_glob=$url;
$udb=$db->query('SELECT * FROM user_src WHERE id='.intval($url));
$uu=$db->fetch($udb);
$url=($uu['fhn']!=''?$uu['fhn']:'http://'.$uu['hn'].'/');
//echo '/\\'.$url.'|';
//die();
//echo mb_detect_encoding($url, 'auto');
//$url=iconv('UTF-8','windows-1251',$url);
//echo mb_detect_encoding($url, 'auto');
//echo parseUrl();
$hn=parse_url($url);
//print_r($hn);
$url=$hn['scheme'].'://'.$hn['host'];
//echo $url;
//echo '|'.parseURL($url).'|';
//die();
$fp = fopen('data.txt', 'a');
fwrite($fp, '!!!');
fclose($fp);
//echo preg_replace('/[а-я]/isu','',$url);
//echo intval(preg_match('/[а-яА-ЯёЁ]/isu',$url)).'!!!';
//die();
if (preg_match('/[а-яА-ЯёЁ]/isu',$url))
{
	$idn_version = 2008;
	$IDN = new idna_convert(array('idn_version' => $idn_version));
	$url = $IDN->encode($url);
}

//echo $url;
//die();
function rss_search($link)
{
	global $db,$id_glob,$db_bmstu;
	$hn=parse_url($link);
	$yet_link[]=$link;//уже прошли
	$all_link[]=$link;//нужно пройти
	$limit=500;
	do
	{
		$limit--;
		echo $limit.' '.$all_link[0].' '.$current_encoding."\n";
		$cont=parseUrl(($all_link[0]));
		//echo '|'.$cont.'|';
		$cont=mb_convert($cont,'UTF-8');
		$mrss=get_rss($all_link[0],$cont);
		//print_r($mrss);
		foreach ($mrss as $item)
		{
			if (!in_array($item,$all_rss))
			{
				echo '1!!!';
				$all_rss[]=$item;
			}
		}
		if (is_rss($cont))
		{
			if (!in_array($all_link[0],$all_rss))
			{
				echo '2!!!';
				$all_rss[]=$all_link[0];
			}
		}
		else
		{
			//echo $cont;
			$regex='/<a.*?href=[\'\"](?<link>.*?)[\'\"].*?>/isu';
			preg_match_all($regex,$cont,$out);
			//print_r($out['link']);
			foreach ($out['link'] as $item)
			{
				if (preg_match('/^(javascript)|(\#)/isu',$item))
				{
					continue;
				}
				if (preg_match('/http.*/isu',$item))
				{
					//echo 'http';
					$hn_item=parse_url($item);
					if ((!in_array($item,$yet_link)) && ($hn['host']==$hn_item['host']))
					{
						$yet_link[]=$item;
						$all_link[]=$item;
					}
				}
				else
				{
					if ($item[0]=='/')
					{
						//echo '/'."\n";
						if (!in_array($link.$item,$yet_link))
						{
							$yet_link[]=$link.$item;
							$all_link[]=$link.$item;
						}
					}
					elseif (preg_match('/[a-zA-Zа-яА-Я]/isu',$item[0]))
					{
						$pr_link=preg_replace('/^(.*\/)/isu','$1',$all_link[0]);
						//echo $pr_link."\n";
						if (!in_array($pr_link.'/'.$item,$yet_link))
						{
							$yet_link[]=$pr_link.'/'.$item;
							$all_link[]=$pr_link.'/'.$item;
						}
					}
				}
			}
		}
		
		//unset($all_link[0]);
		array_shift($all_link);
		print_r($all_rss);
		//print_r($yet_link);
		sleep(1);
	}
	while ((count($all_link)!=0) && ($limit>0));
	$m_rss=get_yarss($link);
	foreach ($m_rss as $key => $item)
	{
		if (!in_array($item,$all_rss))
		{
			$item=preg_replace('/[\/]+/is','/',$item);
			$item=preg_replace('/http:\//is','http://',$item);
			$cont=parseUrl($item);
			sleep(1);
			if ((is_rss($cont)) && (!in_array($item,$all_rss)))
			{
				$all_rss[]=$item;
			}
		}
	}
	foreach ($all_rss as $key => $item)
	{
		$item=preg_replace('/[\/]+/is','/',$item);
		$all_rss[$key]=preg_replace('/http:\//is','http://',$item);	
	}
	print_r($all_rss);
	//$db_bmstu->connect();
	foreach ($all_rss as $item)
	{
		//$db_bmstu->query('INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')');
		$db->query('INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')');
		echo 'INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')';
		file_get_contents('http://146.185.183.12/add_rss.php?url='.base64_encode($item));
	}
	$mhn=parse_url($link);
    $mhn=$mhn['host'];
    $mahn=explode('.',$mhn);
    $mhn = $mahn[count($mahn)-2].'.'.$mahn[count($mahn)-1];
	$mhh = $mahn[count($mahn)-2];
	$db->query('UPDATE `user_src` SET `count`='.intval(count($all_rss)).',`update`=1 WHERE `id`='.$id_glob);
	echo 'UPDATE user_src SET count='.intval(count($all_rss)).',update=1 WHERE id='.$id_glob;
}

rss_search($url);
?>
