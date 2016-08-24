<?
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');


$db=new database();
$db->connect();

$order_delta = $_SERVER['argv'][1];
$debug_mode = $_SERVER['argv'][2];
$fp = fopen('/var/www/pids/alert' . $order_delta . '.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

error_reporting(0);

$user_email_arr = array('axel-bogdanov@yandex.ru','research@wobot-research.com',"artem.stepanov-wr11522@ru.michelin.com","tychinskiy@mediastars.ru","budanov@mediastars.ru");

/*$order_id = 6930;
$user_id=4200;*/
$period = 4; //hours
$average = 3; //days
while (1)
{
	$res = $db->query('SELECT user_id, user_settings FROM users WHERE user_settings LIKE \'%themeAlert%\'');
	$i=0;
	while ($temp=$db->fetch($res))
		    {
		    	$user_settings = json_decode($temp['user_settings'],true);
		    	if($user_settings['themeAlert']==1){
		    		$users[$i]['user_id']=$temp['user_id'];
		    		$i++;
		    	}
		    }
	unset($temp);
	foreach ($users as $key => $value) {
		//echo 'SELECT order_id FROM blog_orders WHERE user_id='.$value['user_id']."\n";
		$order_ids = $db->query('SELECT * FROM blog_orders WHERE user_id='.$value['user_id']);
		//var_dump($order_ids);
		while ($temp = $db->fetch($order_ids))
		    {
		    	if($temp['order_id']==191){
		    		$day_start=mktime(date('H'),date('i'),date('s'),date('n'),date('j')-$average,date('Y'));
					$day_end=mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y'));

					//var_dump($period_end);

					$day_posts = $db->query('SELECT post_time FROM blog_post WHERE order_id='.$temp['order_id'].' AND post_time > '.$day_start.' AND post_time < '.$day_end);
					$i=0;
					//var_dump($day_posts);
					while ($collection = $db->fetch($day_posts)){
						$posts[$i] = $collection['post_time'];
						$i++;
					}
					sort($posts);
					$len = count($posts);
					$begin = $posts[0];
					$end = $posts[$len-1];
					$c=0;
					$nfunc=array();
					$last_index = false;
					$night = 0;
					echo "order_id: ".$temp['order_id']."\n";
					for($j=$day_start; $j<$day_end; $j+=$period*3600){
						$nfunc[$c] = 0;
						if($j<=$end){
							for($k=($last_index)?$last_index:0; $k<$len; $k++){
								if($posts[$k]>=$j && $posts[$k]<=$j+$period*3600) {
									if(intval(date("G", $posts[$k]))>2 && intval(date("G", $posts[$k]))<6){
										$night++;
									}
									$nfunc[$c]++;
									$last_index = $k;
								}
							}
						}
						//echo "Count: ".$nfunc[$c]." hour: ".(date("G", $posts[$last_index]))." night: ".$night."\n";
						if($night>$nfunc[$c]*0.5){
							$nfunc[$c]=0;
						} else {
							$c++;
						}
						$night=0;
					}
					$math = 0;
					$nfunc_len = count($nfunc);
					//echo implode(",", $nfunc)."\n";
					for($i=0; $i<$nfunc_len; $i++){
						//echo ($i*10).";".$nfunc[$i]."\n";
						$math += $nfunc[$i];
						//echo $nfunc[$i]."\n";
					}
					//echo "Math: ".$math/$nfunc_len."\n";
					$math = $math/$nfunc_len;
					echo $nfunc[$nfunc_len-1]." > ".$math." Func: ".derivative($nfunc[$nfunc_len-2], $nfunc[$nfunc_len-1], $period)."\n";
					if($nfunc[$nfunc_len-1]>$math && derivative($nfunc[$nfunc_len-2], $nfunc[$nfunc_len-1], $period)>0){
						$cj=file_get_contents('http://localhost/tools/cashjob.php?order_id='.$temp['order_id'].'&start='.$day_start.'&end='.$day_end);
						//echo 'http://localhost/tools/cashjob.php?order_id='.$temp['order_id'].'&start='.$day_start.'&end='.$day_end."\n";
						$headers='noreply@wobot.ru';
						$text_message="Здравствуйте, в Вашей теме ".$temp['order_name']." был зарегистрирован всплеск упоминаний. Для перехода в кабинет пройдите по <a href=\"http://analytics.wobot.ru/theme_page.html#".$temp['order_id']."\">ссылке</a>";
						$text_message.="<br> С уважением,<br>Поддержка Wobot<br><a href=\"mailto:mail@wobot.ru\">mail@wobot.ru</a><br><div><i><img src='http://www.wobot.ru/new/assets/logo.png'></i></div><br>Это письмо было сгенерировано автоматически, пожалуйста, не отвечайте на него.";
						for($z=0; $z<count($user_email_arr); $z++){
							parseUrlmail('http://91.218.246.79/api/service/sendmail.php',$user_email_arr[$z],'Зарегистрирован всплеск упоминаний',urlencode($text_message),$headers);
						}
						//echo "ALLLErT!";
					}

					unset($posts);
		    	}
		    }
	}
	//die();
	echo 'sleep...';
	sleep(3600);
}

function derivative($x, $xdx, $dx){
	return ($xdx - $x)/$dx;
}

function parseUrlmail($url,$to,$subj,$body,$from)
{
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='mail='.$to.'&title='.$subj.'&content='.$body.'&from='.$from;
// echo $postvars;
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;*/
  return $content;
}
?>