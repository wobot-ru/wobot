<?
/*require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');*/

function get_rss($link,$cc_cont)// получение rss-с конкретной ссылки
{
	$h=parse_url($link);
	$mh[$h['host']]++;
	$cont=$cc_cont;//parseUrl($link);
	// serach for all 'RSS Feed' declarations
	//echo $item."\n";
	if (preg_match_all('/<link[^>]+type=\s*(?:"|)application\/rss\+xml[^>]*>/is', $cont, $rawMatches)) 
	{
		// extract url from each declaration
		foreach ($rawMatches[0] as $rawMatch) 
		{
			if (preg_match('/href=\s*(?:"|)([^"\s>]+)/i', $rawMatch, $rawUrl)) 
			{
				$h1=parse_url($rawUrl[1]);
				if ($h1['host']=='')
				{
					if ($h['host'][0]=='/')
					{
						$outmas[]=preg_replace('/amp;/is','','http://'.$h['host'].$rawUrl[1]);
					}
					else
					{
						$outmas[]=preg_replace('/amp;/is','','http://'.$h['host'].'/'.$rawUrl[1]);
					}
					//echo 'http://'.$h['host'].$rawUrl[1]."\n";
				}
				else
				{
					$outmas[]=preg_replace('/amp;/is','',$rawUrl[1]);
					//echo $rawUrl[1]."\n";
				}
				$jj++;
				//print_r($rawUrl);
			}
		} 
	}
	return $outmas;
}

function get_yarss($link)// получение yarss-ссылок форума с любой ссылки
{
	$h=parse_url($link);
	$rlink=$h['scheme'].'://'.$h['host'].'/yarss.php';
	$cont=parseUrl($rlink);
	$regex='/<a href=[\'\"]?(?<link>.*?)[\'\"]?>/is';
	preg_match_all($regex,$cont,$out);
	foreach ($out['link'] as $key => $item)
	{
		if ((mb_strpos($item,'opml')===false) && (mb_strpos($item,'add.xml')===false))
		{
			$h1=parse_url($item);
			if ($h1['host']=='')
			{
				if ($h1['host'][0]=='/')
				{
					$outmas[]=preg_replace('/amp;/is','','http://'.$h['host'].$item);
				}
				else
				{
					$outmas[]=preg_replace('/amp;/is','','http://'.$h['host'].'/'.$item);
				}
			}
			else
			{
				$outmas[]=$item;
			}
		}
	}
	return $outmas;
}

function is_rss($cont)
{
	return intval(preg_match('/\<\?xml[^<]*?version\=.*?\>\<rss/is',$cont));
}

//print_r(get_yarss('http://www.securitylab.ru/forum/'));
//echo is_rss(parseUrl('http://dynamic.feedsportal.com/pf/591249/http://feeds.kommersant.ru/RSS_Export/RU/Nauka.xml'));
//print_r($mh);
//print_r(get_rss('http://forum.future-me.ru/yarss.php'));
//print_r(get_rss('http://forum.igromania.ru/'));
//echo count($mh);

?>