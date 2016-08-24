<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Утилиты для тестирования WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>
<body>
<h1>Добавление ресурса к выдаче</h1>
<p>Определяет возможность добавления ресурса к выдаче, использует множество методов сбора</p>
<p>Необходимо ввести ссылку на ресурс, например: http://www.volchat.ru/, http://forum.sape.ru/</p>
<p>Если можно добавить ресурс, то скрипт выводит кучу хуйни</p>
<form method="post">
<input type="text" name="url" value="http://forum.sape.ru/">
<input type="submit" value="определить">
</form>
<?

if (mb_strlen($_POST['url'],'UTF-8')==0)
{
	die();
}
else $urll=$_POST['url'];

require_once('kernel.php');
require_once('get_rss.php');

$mas1=array();
$all_rss=array();
$assoc_all_rss=array();
//$nurll='http://www.volchat.ru/';//host
//$urll='http://forum.sape.ru/';//link
$mnurl=parse_url($urll);
$nurll='http://'.$mnurl['host'].'/';
$count_repeat=100;
//echo $nurll;

function getmap($url,$ucan)
{
	//echo 'gg '.$url."\n";
	sleep(1);
	global $mas1,$urll,$all_rss,$assoc_all_rss,$count_repeat;
	$count_repeat--;
	if ($count_repeat<0) break;
	$cont=parseUrl($url);
	$m_rss=get_rss($url,$cont);
	foreach ($m_rss as $item)
	{
		if (!isset($assoc_all_rss[$item]))
		{
			$all_rss[]=$item;
			$assoc_all_rss[$item]=1;
		}
	}
	print_r($all_rss);
	echo $url;
	$cont=mb_convert($cont,'UTF-8');//detect charset and convert to utf-8
	$um=parse_url($url);
	$regex='/<a.*?href=[\'\"](?<link>.*?)[\'\"].*?>/is';//choose all links
	preg_match_all($regex,$cont,$out);
	$regexbase='/<base.*?href=[\'\"](?<basel>.*?)[\'\"].*?>/is';
	preg_match_all($regexbase,$cont,$outbase);
	$baseurl=$outbase['basel'][0];
	foreach ($out['link'] as $item)
	{
		if ((mb_strpos($item,'http://',0,'UTF-8')!==false) || (mb_strpos($item,'https://',0,'UTF-8')!==false))//if not without host
		{
			if (mb_strpos($item,$um['host'],0,'UTF-8')!==false)
			{
				$item=preg_replace('/(.*)(\#.*)/is','$1',$item);//process link
				/*$item=preg_replace('/amp\;/is','',$item);
				$item=preg_replace('/([\?\&]*s=.+)/is','',$item);*/
				if (!in_array($item,$mas1))
				{
					$c=0;
					foreach ($ucan as $item1)
					{
						if (mb_strpos($item,$item1,0,'UTF-8')!==false)
						{
							$c=1;
						}
					}
					if ($c==0)
					{
						$mas1[]=$item;
						//print_r($mas1);
						$m_deep_size=explode('/',$item);
						if (count($m_deep_size)>4)
						{
							continue;
						}
						getmap($url,$mas1);
					}
				}
			}
		}
		else//withiut host
		{
			$item=preg_replace('/(.*)(\#.*)/is','$1',$item);//process link
			$item=preg_replace('/amp\;/is','',$item);
			/*$iturl=preg_replace('/(s=[^&]*)/is','',$iturl);
			$iturl=preg_replace('/(\?$)/is','',$iturl);
			$iturl=preg_replace('/(\?\&)/is','?',$iturl);*/
			//echo $item[0].' ';
			if ($baseurl!='')
			{
				$addbu=$baseurl;
			}
			else
			{
				$addbu='http://'.$um['host'].'/';
			}
			if ($item[0]=='/')
			{
				$addlink=$addbu.mb_substr($item,1,mb_strlen($item,'UTF-8')-1,'UTF-8');
				if (!in_array($addlink,$mas1))
				{
					$c=0;
					foreach ($ucan as $item1)
					{
						if (mb_strpos($addlink,$item1,0,'UTF-8')!==false)
						{
							$c=1;
						}
					}
					if ($c==0)
					{
						$mas1[]=$addlink;
						//print_r($mas1);
						$m_deep_size=explode('/',$addlink);
						if (count($m_deep_size)>4)
						{
							continue;
						}
						getmap($addlink,$ucan);
					}
				}
			}
			elseif ($item[0]=='.')
			{
				if ($item[1]='/')
				{
					$rg='/(?<rl>..\/)/is';
					preg_match_all($rg,$item,$ot);
					$count_rl=count($ot['rl']);
					if ($count_rl==0)
					{
						$addlink=$addbu.preg_replace('/(.*)\/.*$/is','$1',mb_substr($item,1,mb_strlen($item,'UTF-8')-1,'UTF-8'));
						if (!in_array($addlink,$mas1))
						{
							$c=0;
							foreach ($ucan as $item1)
							{
								if (mb_strpos($addlink,$item1,0,'UTF-8')!==false)
								{
									$c=1;
								}
							}
							if ($c==0)
							{
								$mas1[]=$addlink;
								//print_r($mas1);
								$m_deep_size=explode('/',$addlink);
								if (count($m_deep_size)>4)
								{
									continue;
								}
								getmap($addlink,$ucan);
							}
						}
					}
					else
					{
						$flink=mb_substr($item,1,mb_strlen($item,'UTF-8')-1,'UTF-8');
						for ($i=0;$i<$count_rl;$i++)
						{
							$flink=preg_replace('/(.*)\/.*$/is','$1',$flink);
						}
						$addlink=$flink;
						if (!in_array($addlink,$mas1))
						{
							$c=0;
							foreach ($ucan as $item1)
							{
								if (mb_strpos($addlink,$item1,0,'UTF-8')!==false)
								{
									$c=1;
								}
							}
							if ($c==0)
							{
								$mas1[]=$addlink;
								//print_r($mas1);
								$m_deep_size=explode('/',$addlink);
								if (count($m_deep_size)>4)
								{
									continue;
								}
								getmap($addlink,$ucan);
							}
						}
					}
				}
				elseif ($item[1]=='.')
				{
					//if
				}
			}
			else
			{
				if (!in_array($urll.$item,$mas1))
				{
					foreach ($ucan as $item1)
					{
						if (mb_strpos($urll.$item1,$item,0,'UTF-8')!==false)
						{
							$c=1;
						}
					}
					if ($c==0)
					{
						$mas1[]=$urll.$item;
						//print_r($mas1);
						$m_deep_size=explode('/',$urll.$item);
						if (count($m_deep_size)>4)
						{
							continue;
						}
						getmap($urll.$item,$ucan);
					}
				}
			}
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
print_r($mmcan);
getmap($urll,$mmcan);

?>
</body>
</html>