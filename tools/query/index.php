<?

if (strlen($_POST['code'])>2)
{
	require_once('/var/www/com/config.php');
	require_once('/var/www/com/func.php');
	require_once('/var/www/com/db.php');
	require_once('/var/www/bot/kernel.php');

	error_reporting(E_ERROR | E_PARSE);
	ignore_user_abort(true);
	set_time_limit(0);
	ini_set('max_execution_time',0);
	ini_set('default_charset','utf-8');
	ob_implicit_flush();

	$db = new database();
	$db->connect();
	
		$query=$_POST['code'];
		
	$ressec=$db->query('SELECT * FROM queries_history WHERE query_text="'.urlencode($query).'"');
	if (mysql_num_rows($ressec)>0)
	{
		$blog=$db->fetch($ressec);
		$count=$blog['query_count'];
		echo '<i>Данный результат получен из кэша от '.date('r',$blog['query_date']).'</i><br>';
	}
	else
	{
	
		$xml = file_get_contents('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&ft=all&from_day='.date('d',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&date=on&from_month='.date('m',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&from_year='.date('Y',mktime(0,0,0,date('m')-1,date('d')-1,date('Y'))).'&to_day='.date('d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_month='.date('m',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&to_year='.date('Y',mktime(0,0,0,date('m'),date('d')-1,date('Y'))).'&holdres=mark');

		$r1='/<yablogs\:count>(?<count>.*?)<\/yablogs\:count>/is';
		preg_match_all($r1,$xml,$ot1);
		$count=intval($ot1['count'][0]);
		$ressec=$db->query('INSERT INTO queries_history (query_text, query_date, query_count) values ("'.urlencode($query).'",'.time().','.urlencode($count).')');
		/*if ($count==0)
		{	
			$r2='/<yablogs\:more>(?<more>.*?)<\/yablogs\:more>/is';
			preg_match_all($r2,$xml,$ot2);
			$more=$ot2['more'][0];

			echo $more."\n";

			$r3='/<item>(?<item>.*?)<\/item>/is';
			preg_match_all($r3,$xml,$ot3);
			print_r($ot3['item']);
			$count+=count($ot3['item']);
			echo "count: ".$count."\n";
			foreach ($ot1['ll'] as $item)
			{
				$outmas[]=$ittem;
			}
		}*/
	}
		echo "<script>alert('Кол-во постов в месяц примерно ".intval($count)."');</script>";
} elseif ((strlen($_POST['code'])==1)||(strlen($_POST['code'])==2))
{
	echo "<script>alert('Сдурел?! Слишком короткий!');</script>";
}

?>

<!doctype html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Wobot: Оценка размера выдачи</title>
    <link rel="stylesheet" href="codemirror/lib/codemirror.css">
    <script src="codemirror/lib/codemirror.js"></script>
    <script src="codemirror/mode/clojure/clojure.js"></script>
    <style>.CodeMirror {background: #f8f8f8;}</style>
    <link rel="stylesheet" href="codemirror/doc/docs.css">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>
  <body>
    <h1>Wobot: Оценка размера выдачи</h1>
<p>Введите текст запроса:</p>
    <form method="post"><textarea id="code" name="code">
<?=((strlen($_POST['code'])>0)?$_POST['code']:'("путин" | "медведев")');?>
</textarea>
<p>Ребят, особо не усердствуем, нажимаем не более 20 раз в день.</p>
<input type="submit" value="Оценить количество постов">
</form>
    <script>
      var editor = CodeMirror.fromTextArea(document.getElementById("code"), {});
    </script>

<?
if (strlen($_POST['code'])>2)
{
	$resc=$db->query('SELECT * FROM queries_history ORDER BY query_id DESC LIMIT 10');
	while($blog=$db->fetch($resc))
	{
		echo '<p style="font-size: 6px;">'.urldecode($blog['query_text']).' - <b>'.$blog['query_count'].'</b></p>';
	}
}
?>

  </body>
</html>
