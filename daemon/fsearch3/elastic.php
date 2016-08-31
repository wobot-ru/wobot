<?
// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');
// require_once('ch.php');

require_once('/var/www/daemon/com/infix.php');

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

/*function getsearch($query,$size,$from,$ts,$te,$global)
{
	$ch = curl_init();
	if (intval($global)==0) curl_setopt($ch, CURLOPT_URL, 'http://146.185.176.196:9200/_all/post/_search?pretty=true');
	else curl_setopt($ch, CURLOPT_URL, 'http://wobotindex1.cloudapp.net:9200/posts/post/_search?pretty=true');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Нужно явно указать, что будет POST запрос
	curl_setopt($ch, CURLOPT_POST, true);
	//Здесь передаются значения переменных
	$query=preg_replace('/[^а-яА-Яa-zA-Z0-9ё\(\)\|\&\?\.\,\[\]\*\~]/isu', ' ', $query);
	$query='{"query":{"bool":{"must":[{"query_string":{"default_field":"post.message","query":"'.$query.'"}},{"range":{"post.post_date":{"from":"'.date('Y-n-j\TH:i:s',$ts).'","to":"'.date('Y-n-j\TH:i:s',$te).'"}}}],"must_not":[],"should":[]}},"from":'.$from.',"size":'.$size.',"sort":[],"facets":{}}';
	// $fp = fopen('/var/www/daemon/logs/elastic_query.log', 'a');
	// fwrite($fp, date('r').' '.$query."\n");
	// fclose($fp);
	// echo $query."\n";
	// curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query": { "filtered" : { "filter" : { "range" : { "post_date" : {	"from": "'.date('Y-n-j\TH:i:s',$ts).'", "to": "'.date('Y-n-j\TH:i:s',$te).'" } } },	"query" : { "text" : { "message" : "'.$query.'" } } } }, "size": "'.$size.'", "from": "'.$from.'" }');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	// $fp = fopen('log_elastic.txt', 'a');
	// fwrite($fp, $query."\n");
	// fclose($fp);
	//	curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query": {"query_string": {"query": "message:'.$query.'"}}, "size":"'.$size.'","from":"'.$from.'"}');
	//echo '{"query": {"query_string": {"query": "message:'.$query.'"}}, "range": {"post_date": {"from": "'.date('Y-n-j\TH:i:s',$ts).'","to": "'.date('Y-n-j\TH:i:s',$te).'"}}, "size":"'.$size.'","from":"'.$from.'"}';
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // таймаут соединения
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // таймаут ответа
	curl_setopt($ch, CURLOPT_USERAGENT, 'FUCK');
	$data = curl_exec($ch);
	curl_close($ch);
	//echo $data;
	return $data;
}

function get_elastic($keyword,$ts,$te,$lan,$global)
{
	// return $outmas;
	$tmp_keyword=$keyword;
	// $keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$keyword);
	// $keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	// $mkeyword=explode('  ',$keyword);
	$keyword=preg_replace('/\|/isu', ' OR ', $keyword);
	$keyword=preg_replace('/\&+/isu', ' AND ', $keyword);
	$keyword=preg_replace('/\~+/isu', ' NOT ', $keyword);
	$keyword=preg_replace('/(\/\([\-\+]\d+\s?[\-\+]\d+\)|\/[\-\+]?\d+)/isu', ' AND ', $keyword);
	// $keyword=preg_replace('/\"+/isu', '', $keyword);
	$keyword=preg_replace('/\s+/isu', ' ', $keyword);
	$stem_word=new Lingua_Stem_Ru();
	$mkeyword=get_simple_word($keyword);
	// echo $keyword;
	// die();
	// foreach ($mkeyword as $frase)
	{
		$i=-1;
		// if (isset($yet_word[$stem_word->stem_word($frase)])) continue;
		// if (mb_strlen(trim($frase),'UTF-8')<=3) continue;
		$frase=preg_replace('/\s+/isu',' AND ',$frase);
		$count_results=0;
		do
		{
			echo '.';
			$i++;
			// echo ($i*100).' ';
			$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\+\(\)\.\#\&\"\|\-\!\*\,\_\'\s]/isu',' ',$keyword);
			// echo $keyword."\n";
			$cont=getsearch(addslashes($keyword),100,($i*100),$ts,$te,$global);
			sleep(1);
			// echo $item_kw;
			$mas=json_decode($cont,true);
			// print_r($mas);
			if ($mas['status']!=0) 
			{
				$headers  = "From: noreply2@wobot.ru\r\n"; 
				$headers .= "Bcc: noreply2@wobot.ru\r\n";
				$headers .= 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
				mail('zmei123@yandex.ru','Кривой запрос для TPF',$tmp_keyword,$headers);
				mail('r@wobot.co','Кривой запрос TPF',$tmp_keyword,$headers);
				die();
			}
			// print_r($mas['hits']['total']);
			foreach ($mas['hits']['hits'] as $key => $item)
			{
				if (check_post($item['_source']['message'],$tmp_keyword)==0) continue;
				if (in_array($item['_source']['message'], $outmas['fulltext'])) continue;
				$outmas['link'][]=$item['_source']['link'];
				$outmas['nick'][]=$item['_source']['user'];
				$outmas['fulltext'][]=$item['_source']['message'];
				$outmas['time'][]=strtotime($item['_source']['post_date']);
				if (trim($item['title'])=='')
				{
					if (mb_strlen($item['_source']['message'],'UTF-8')>=150)
					{
						$regex='/^(?<st>.{0,150})[^а-яa-zё]/isu';
						preg_match_all($regex, $item['_source']['message'], $out);
						$short_text=trim($out['st'][0]).'...';
					}
					else
					{
						$short_text=$item['_source']['message'];	
					}
				}
				$outmas['content'][]=$short_text;
				// if (count($outmas['time'])>100) $outmas=post_slice($outmas);
				//echo $item['_source']['post_date'].' '.$item['_source']['link'].' '.$item['_source']['message']."\n";
			}
			if (intval($count_results)==0) $count_results=$mas['hits']['total'];
			if (($mas['hits']['total']>100000) && ($i>1000)) break;
		}
		while ($i<=ceil($count_results/100));
	}
	// $outmas=post_slice($outmas);
	print_r($outmas);
	return $outmas;
}*/


function getsearch($query,$size,$from,$ts,$te,$month,$year,$scroll_token)
{
	$ch = curl_init();
	echo 'http://91.218.113.136:9200/*y'.$year.'*m'.$month.'/post/_search?scroll=1m'."\n";
	if ($scroll_token=='') curl_setopt($ch, CURLOPT_URL, 'http://91.218.113.136:9200/*y'.$year.'*m'.$month.'/post/_search?scroll=1m');
	else curl_setopt($ch, CURLOPT_URL, 'http://91.218.113.136:9200/_search/scroll?scroll=1m');
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Нужно явно указать, что будет POST запрос
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_USERPWD, "es:Elastic777"); 
	//Здесь передаются значения переменных
	//{"query":{"bool":{"must":[{"query_string":{"default_field":"post_body.ru","query":"\"голодные игры\" OR \"THE HUNGER GAMES\" OR \"HUNGERGAMES\" OR \"сойка пересмешница\""}},{"range":{"post_date":{"gte":"2014-06-01","lt":"2014-07-16"}}}]}}}
	if ($scroll_token=='') $query='{"query":{"bool":{"must":[{"query_string":{"query":"'.$query.'","fields":["post_body.ru"]}}],"filter":{"query":{"range":{"post_date":{"gte":"'.date('Y-m-d\TH:i:00.000\Z',$ts).'","lte":"'.date('Y-m-d\TH:i:00.000\Z',$te).'"}}}}}},"from":'.$from.',"size":'.$size.'}';
	else $query=$scroll_token;
	echo $query;
	// $query=preg_replace('/[^а-яА-Яa-zA-Z0-9ё\(\)\|\&\?\.\,\[\]\*\~]/isu', ' ', $query);
	// $query='{"query":{"bool":{"must":[{"query_string":{"default_field":"post.message","query":"'.$query.'"}},{"range":{"post.post_date":{"from":"'.date('Y-n-j\TH:i:s',$ts).'","to":"'.date('Y-n-j\TH:i:s',$te).'"}}}],"must_not":[],"should":[]}},"from":'.$from.',"size":'.$size.',"sort":[],"facets":{}}';
	// $fp = fopen('/var/www/daemon/logs/elastic_query.log', 'a');
	// fwrite($fp, date('r').' '.$query."\n");
	// fclose($fp);
	// echo $query."\n";
	// curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query": { "filtered" : { "filter" : { "range" : { "post_date" : {	"from": "'.date('Y-n-j\TH:i:s',$ts).'", "to": "'.date('Y-n-j\TH:i:s',$te).'" } } },	"query" : { "text" : { "message" : "'.$query.'" } } } }, "size": "'.$size.'", "from": "'.$from.'" }');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
	// $fp = fopen('log_elastic.txt', 'a');
	// fwrite($fp, $query."\n");
	// fclose($fp);
	//	curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query": {"query_string": {"query": "message:'.$query.'"}}, "size":"'.$size.'","from":"'.$from.'"}');
	//echo '{"query": {"query_string": {"query": "message:'.$query.'"}}, "range": {"post_date": {"from": "'.date('Y-n-j\TH:i:s',$ts).'","to": "'.date('Y-n-j\TH:i:s',$te).'"}}, "size":"'.$size.'","from":"'.$from.'"}';
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // таймаут соединения
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // таймаут ответа
	curl_setopt($ch, CURLOPT_USERAGENT, 'FUCK');
	$data = curl_exec($ch);
	// echo $data."\n";
	// print_r(json_decode($data,true));
	curl_close($ch);
	//echo $data;
	return $data;
}


function get_elastic($keyword,$ts,$te,$lan,$global)
{
	// return $outmas;
	$tmp_keyword=$keyword;
	// $keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$keyword);
	// $keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	// $mkeyword=explode('  ',$keyword);
	$keyword=preg_replace('/\|/isu', ' OR ', $keyword);
	$keyword=preg_replace('/\&+/isu', ' AND ', $keyword);
	$keyword=preg_replace('/\~+/isu', ' NOT ', $keyword);
	$keyword=preg_replace('/(\/\([\-\+]\d+\s?[\-\+]\d+\)|\/[\-\+]?\d+)/isu', ' AND ', $keyword);
	// $keyword=preg_replace('/\"+/isu', '', $keyword);
	$keyword=preg_replace('/\s+/isu', ' ', $keyword);
	$stem_word=new Lingua_Stem_Ru();
	$mkeyword=get_simple_word($keyword);
	// print_r($mkeyword);
	// echo $keyword;
	// die();
	// foreach ($mkeyword as $frase)
	{
		$i=-1;
		// if (isset($yet_word[$stem_word->stem_word($frase)])) continue;
		// if (mb_strlen(trim($frase),'UTF-8')<=3) continue;
		$frase=preg_replace('/\s+/isu',' AND ',$frase);
		$count_results=0;
		for ($t_index=$ts;$t_index<=$te;$t_index=mktime(0,0,0,date('n',$t_index)+1,1,date('Y',$t_index)))
		{
			$scroll_token='';
			$i=0;
			do
			{
				// echo '.';
				$i++;
				echo ($i*100)."\n";
				$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\+\(\)\.\#\&\"\|\-\!\*\,\_\'\s]/isu',' ',$keyword);
				echo $keyword."\n";
				// if ($i>100) break;
				$cont=getsearch(addslashes($keyword),100,($i*100),$ts,$te,date('n',$t_index),date('Y',$t_index),$scroll_token);
				// print_r(json_decode($cont,true));
				// die();
				sleep(1);
				// echo $item_kw;
				$mas=json_decode($cont,true);
				$scroll_token=$mas['_scroll_id'];
				echo "\n".'COUNT = '.$mas['hits']['total'].' HITS = '.count($mas['hits']['hits'])."\n";
				foreach ($mas['hits']['hits'] as $key => $item)
				{
					// echo $tmp_keyword."\n";
					if (check_post($item['_source']['post_body'],$tmp_keyword)==0) continue;
					echo '.';
					// if (in_array($item['_source']['message'], $outmas['fulltext'])) continue;
					if (preg_match('/^https\:\/\/www\.facebook\.com/isu', $item['_source']['post_href'])) $item['_source']['post_href']=preg_replace('/https\:\/\//isu','http://',$item['_source']['post_href']);
					$outmas['link'][]=$item['_source']['post_href'];
					$outmas['nick'][]=$item['_source']['sm_profile_id'];
					$outmas['fulltext'][]=$item['_source']['post_body'];
					$outmas['time'][]=strtotime($item['_source']['post_date']);
					// if (trim($item['title'])=='')
					{
						if (mb_strlen($item['_source']['post_body'],'UTF-8')>=150)
						{
							$regex='/^(?<st>.{0,150})[^а-яa-zё]/isu';
							preg_match_all($regex, $item['_source']['post_body'], $out);
							$short_text=trim($out['st'][0]).'...';
						}
						else
						{
							$short_text=$item['_source']['post_body'];	
						}
					}
					$outmas['content'][]=$short_text;
					// if (count($outmas['time'])>100) $outmas=post_slice($outmas);
					//echo $item['_source']['post_date'].' '.$item['_source']['link'].' '.$item['_source']['message']."\n";
				}
				if (intval($count_results)==0) $count_results=$mas['hits']['total'];
				// if (($mas['hits']['total']>100000) && ($i>1000)) break;
			}
			while ($i<=ceil($count_results/100));
		}
	}
	// $outmas=post_slice($outmas);
	print_r($outmas);
	return $outmas;
}

// get_elastic('Тиньков|Тинькоф|Тинькофф|Тиньковв|Тинков|Тинкоф|Тинкофф|ТКС|Тинькоффф|Tinkov|Tinkof|Tinkoff|"Tin’kov"|"Tin’koff"|TCS Bank|TCS-Bank|tcsbank',mktime(0,0,0,3,20,2013),mktime(0,0,0,3,23,2013),'ru');

//$mas=json_decode(getsearch('путин',100,0,mktime(0,0,0,12,1,2012),mktime(0,0,0,12,10,2012)),true);
//foreach ($mas['hits']['hits'] as $key => $item)
//{
//	echo $item['_source']['post_date'].' '.$item['_source']['link'].' '.$item['_source']['message']."\n";
//}

?>
