<?
//print_r($_SERVER['argv']);
if (mb_strlen($_SERVER['argv'][1],'UTF-8')==0)
{
	die();
}
else $urll=urldecode($_SERVER['argv'][1]);

$fp = fopen('data.txt', 'a');
fwrite($fp, '!!!');
fclose($fp);

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
//require_once('/var/www/bot/kernel.php');
require_once('kernel.php');
require_once('get_rss.php');

$db = new database();
$db->connect();

$mas1=array();
$all_rss=array();
$assoc_all_rss=array();
//$nurll='http://www.volchat.ru/';//host
//$urll='http://forum.sape.ru/';//link
$mnurl=parse_url($urll);
if (preg_match('/[а-яА-ЯёЁ]/isu',$urll))
{
	$nurll='http://'.$mnurl['path'].'/';
}
else
{
	$nurll='http://'.$mnurl['host'].'/';
}
$count_repeat=20;	
//echo $nurll;
error_reporting(0);
//print_r($mnurl);
function getmap($url,$ucan)
{
	//echo $url;
	//echo 'gg '.$url."\n";
	sleep(1);
	global $mas1,$urll,$all_rss,$assoc_all_rss,$count_repeat,$_SERVER,$db,$nurll;
	$count_repeat--;
	echo $count_repeat.'----------'."\n";
	if ($count_repeat<0) 
	{
		$fp = fopen('data.txt', 'a');
		fwrite($fp, json_encode($all_rss));
		
		// echo 'gg________'; 
		//print_r($all_rss);
		foreach ($all_rss as $item)
		{
			echo $item."\n";
			// $db->query('INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')');
			//echo 'INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')'."\n";
			//fwrite($fp, 'INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')');
		}
		$mmm=get_yarss($nurll);
		foreach ($mmm as $item)
		{
			if ($item!='')
			{
				echo $item."\n";
				// $db->query('INSERT INTO azure_rss (rss_link,rss_type) VALUES (\''.addslashes($item).'\',\'ru\')');
			}
		}
		{
			$mhn=parse_url($_SERVER['argv'][1]);
		    $mhn=$mhn['host'];
		    $mahn=explode('.',$mhn);
		    $mhn = $mahn[count($mahn)-2].'.'.$mahn[count($mahn)-1];
			$mhh = $mahn[count($mahn)-2];
			// $db->query('UPDATE user_src SET `count`='.intval(count($all_rss)).',`update`=1 WHERE `hn`=\''.$mhn.'\'');
			//echo 'UPDATE user_src SET `count`='.intval(count($all_rss)).',`update`=1 WHERE `hn`=\''.$mhn.'\''."\n";
			//fwrite($fp, 'UPDATE user_src SET `count`='.intval(count($all_rss)).',`update`=1 WHERE `hn`=\''.$mhn.'\'');
		}
		fclose($fp);
		break;
		echo '123';
	}
	$cont=parseUrl($url);
	//echo $url.'!!!!';
	$m_rss=get_rss($url,$cont);
	if (preg_match('/<rss.*?version.*/is',$cont))
	{
		$all_rss[]=$url;
		$assoc_all_rss[$url]=1;
	}
	//echo $url.'!!!!!';
	//print_r($m_rss);
	foreach ($m_rss as $item)
	{
		if (!isset($assoc_all_rss[$item]))
		{
			$all_rss[]=$item;
			$assoc_all_rss[$item]=1;
		}
	}
	//print_r($all_rss);
	$mhn=parse_url($url);
    $mhn=$mhn['host'];
    $mahn=explode('.',$mhn);
    $mhn = $mahn[count($mahn)-2].'.'.$mahn[count($mahn)-1];
	$mhh = $mahn[count($mahn)-2];
	//print_r($all_rss);
	//echo $url;
	$cont=mb_convert($cont,'UTF-8');//detect charset and convert to utf-8
	$um=parse_url($url);
	$regex='/<a.*?href=[\'\"](?<link>.*?)[\'\"].*?>/is';//choose all links
	preg_match_all($regex,$cont,$out);
	$regexbase='/<base.*?href=[\'\"](?<basel>.*?)[\'\"].*?>/is';
	preg_match_all($regexbase,$cont,$outbase);
	$baseurl=$outbase['basel'][0];
	//echo '|'.count($out['link']);
	foreach ($out['link'] as $item)
	{
		$item=preg_replace('/#.*/is','',$item);
		$item=preg_replace('/amp\;/is','',$item);
		$hn=parse_url($item);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		if (preg_match('/https?/is',$item))
		{
			if (!isset($mas1[$item]) && ($hn==$mhn))
			{
				$mas1[$item]++;
				//echo $item."\n";
				getmap($item,$ucan);
			}
		}
		else
		{
			if ($item[0]=='/')
			{
				// echo '1';
				if ($baseurl!='')
				{
					$addurl=$baseurl;
					if (!isset($mas1[$addurl.substr($item,1)]))
					{
						// echo $addurl.substr($item,1)."\n";
						$mas1[$addurl.substr($item,1)]++;
						getmap($addurl.substr($item,1),$ucan);
					}
				}
				else
				{
					$addurl=$url;
					$hn=parse_url($addurl);
					if ($hn['scheme']=='')
					{
						$hn['scheme']='http';
					}
					if (($hn['host']=='') && (preg_match('/[а-яА-ЯёЁ]/isu',$hn['path'])))
					{
						if (!isset($mas1[$hn['scheme'].'://'.$hn['path'].$item]))
						{
							// echo $hn['scheme'].'://'.$hn['path'].$item."\n";
							$mas1[$hn['scheme'].'://'.$hn['path'].$item]++;
							getmap($hn['scheme'].'://'.$hn['path'].$item,$ucan);
						}
					}
					else
					{
						if (!isset($mas1[$hn['scheme'].'://'.$hn['host'].$item]))
						{
							// echo $hn['scheme'].'://'.$hn['host'].$item."\n";
							$mas1[$hn['scheme'].'://'.$hn['host'].$item]++;
							getmap($hn['scheme'].'://'.$hn['host'].$item,$ucan);
						}
					}
				}
			}
			else
			if (($item[0]!='/') && ($item[0]!='.'))
			{
				// echo '2';
				if ($baseurl!='')
				{
					if (!isset($mas1[$baseurl.$item]))
					{
						// echo '3';
						// echo $baseurl.$item."\n";
						$mas1[$baseurl.$item]++;
						getmap($baseurl.$item,$ucan);
					}
				}
				else
				{
					$addurl=$url;
					$hn=parse_url($addurl);
					if (!isset($mas1[$hn['scheme'].'://'.$hn['host'].'/'.$item]))
					{
						// echo '4';
						// echo $hn['scheme'].'://'.$hn['host'].'/'.$item."\n";
						$mas1[$hn['scheme'].'://'.$hn['host'].'/'.$item]++;
						getmap($hn['scheme'].'://'.$hn['host'].'/'.$item,$ucan);
					}
				}
			}
			//echo 'NO'.$item."\n";
		}
	}
}

$canc=parseUrl($nurll.'robots.txt');//generate array of dissalowed link
if ($canc!='')
{
	$canc=preg_replace('/\n/','|',$canc);
	$mascan=explode('|',$canc);
	foreach ($mascan as $item)
	{
		$rg='/(Disallow)\: (?<data>.*)/is';
		preg_match_all($rg,$item,$out);
		if ($out['data'][0]!='')
		{
			$mmcan[]=$out['data'][0];
		}
	}
}
//print_r($mmcan);
getmap($urll,$mmcan);
//print_r($all_rss);

?>
