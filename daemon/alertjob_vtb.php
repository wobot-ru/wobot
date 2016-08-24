<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
// error_reporting(E_ERROR);
// ignore_user_abort(true);
date_default_timezone_set ( 'Europe/Moscow' );
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$deltatime=7;

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

$db = new database();
$db->connect();
$gg1=0;
//while(1)
$count[1]=1;
$count[2]=7;
$count[3]=30;
{
	$ressec=$db->query('SELECT * FROM users as b LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE b.user_id=4208 ORDER BY b.user_id DESC');
	while($blog=$db->fetch($ressec))
	{
		echo $blog['user_id'].'!!!'."\n";
		$or_i=0;
		$csv_output='Дорогой пользователь, за последнее время были найдены следующие сообщения по Вашим авторам: <br><br>';
		if ($blog['tariff_id']==3)
		{
			$order_i=$db->query('SELECT * FROM blog_orders WHERE user_id=61');
		}
		else
		{
			$order_i=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($blog['user_id']));
			//echo 'SELECT * FROM blog_orders WHERE user_id='.intval($blog['user_id']);
		}
		while ($order=$db->fetch($order_i))
		{
			if ($order['order_end']==0)
			{
				$order['order_end']=mktime(0,0,0,date('n',time()),date('j',time())+1,date('Y',time()));
			}
			if ($order['order_end']<time())
			{
				continue;
			}
			$start=mktime(0,0,0,date('n',time()),date('j',time())-1,date('Y',time()));
			$end=mktime(0,0,0,date('n'),date('j'),date('Y'));
			echo $order['order_id'].' '.$blog['user_id'].' '.$start.' '.$end."\n";
			// die();
			//echo 'SELECT * FROM blog_post WHERE order_id='.$order['order_id'].' AND post_time>='.$start.' AND post_time<='.$end."\n";
			$posts=$db->query('SELECT * FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id WHERE order_id='.$order['order_id'].' AND post_time>='.$start.' AND post_time<='.$end.' AND b.blog_id IN (\'lebedevalex\',\'navalny\',\'saleksashenko\',\'borisnemtsov\',\'varlamov\',\'vinokurov12\',\'alburov\',\'vashurkov\',\'anna_Veduta\',\'sobollubov\',\'ilyayashin\',\'khodorkovsky\',\'dternovskiy\',\'kira_yarmysh\',\'fbkinfo\',\'kshn\',\'oleg_kozyrev,\'naganoff_ru\',\'i_m_ho\',\'aavst\',\'sadalskij\',\'sandy_mustache\',\'igor_sechin\') ORDER BY post_time DESC LIMIT 400');
			if ($db->num_rows($posts)==0) die();
			$id=0;
			$csv_output='<table style="width: 1100pt;"><tbody><tr><td style="width:25.35pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="34"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Дата<u></u><u></u></span></p></td><td style="width:31.9pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="43"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Время<u></u><u></u></span></p></td><td style="padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="670"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Текст<u></u><u></u></span></p></td><td style="width:50.6pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="50"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">URL<u></u><u></u></span></p></td><td style="width:36.25pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="48"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Автор<u></u><u></u></span></p></td><td style="width:27.3pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="36"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">Источник<u></u><u></u></span></p></td></tr>';
			while($post=$db->fetch($posts))
			{
				$id++;
				//if ((date('G',$post['post_time'])==0) && ((date('i',$post['post_time'])==0)))
				{
				//	$ttm='';
				}
				//else
				{
					$ttm=date('G:i:s',$post['post_time']);
				}
				$csv_output.='
				<tr>
				<td style="width:25.35pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="34"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">'.date('d.m.Y',$post['post_time']).'<u></u><u></u></span></td>
				<td style="width:31.9pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="43"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">'.$ttm.'<u></u><u></u></span></td>
				<td style="padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="670"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">'.preg_replace('/[^а-яА-Яa-zA-ZёЁ\s\'\:\"\.\,0-9\t\s\n]/isu',' ',str_replace("\n",' ',$post['post_content'])).'<u></u><u></u></span></p></td>
				<td style="width:50.6pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="50"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><a href="'.$post['post_link'].'" target="_blank">'.$post['post_link'].'</a><u></u><u></u></span></p></td>
				<td style="width:36.25pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="48"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">'.$post['blog_nick'].'<u></u><u></u></span></p></td>
				<td style="width:27.3pt;padding:0cm 3.75pt 0cm 3.75pt" valign="top" width="36"><p style="margin-right:0cm;margin-bottom:0cm;margin-left:0cm;margin-bottom:.0001pt"><span style="font-size:10pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;"><a href="http://'.$post['post_host'].'" target="_blank">'.$post['post_host'].'</a><u></u><u></u></span></p></td>
				</tr>';
			}
			$csv_output.='</tbody></table>';
			$gl_id+=$id;
			//echo $csv_output;
		}
		// echo $csv_output;
		// die();
		$text_message.="<br> Чтобы отключиться от подписки пройдите по ссылке <a href='http://wobot.ru/cancel_dgst?&token=".$blog['user_pass']."&user_id=".$blog['user_id']."&mt=";
		//echo $text_message."\n";
		//if ()
		$headers='noreply@wobot.ru';		// mail('zmei123@yandex.ru','Команда Wobot',$csv_output,$headers);
		// mail('diana@wobot-research.com','Команда Wobot',$csv_output,$headers);
		// die()
		$blog['user_mails'].=',zmei123@yandex.ru';
		$blog['user_mails'].=',research@wobot-research.com';
		$m_mails=explode(',',$blog['user_mails']);
		//print_r($m_mails);
		//die();
		//if ($gl_id!=0)
		{
			$m_mails=explode(',',$blog['user_mails']);
			foreach ($m_mails as $item)
			{
				echo $blog['user_id'].' '.$item.'!!!'."\n\n\n";
				// mail(trim($item),'Monitoring Social Media',$csv_output,$headers);
				// parseUrlmail('http://188.120.239.225/api/service/sendmail.php',trim($item),'Monitoring Social Media',$csv_output,$headers);
				// echo urlencode($csv_output);
				parseUrlmail('http://91.218.246.79/api/service/sendmail.php',trim($item),'Monitoring Social Media',urlencode($csv_output),$headers);
				// die();
				//mail(trim($item),'Команда Wobot',($text_message.md5($item)."'>ссылка</a><div><i><img src='http://www.wobot.ru/new/assets/logo.png'></i></div>"),$headers);
			}
		}
		/*else
		{
			//echo $blog['user_id'].' '.$item.'!!!'.$blog['user_mails']."\n\n\n";
			mail($blog['user_mails'],'Команда Wobot',("К сожалению в последнее время не нашлось упоминаний удовлетворяющих вашему запросу, возможно вам надо уточнить или поменять запрос на более корректный.<div><i><img src='http://www.wobot.ru/new/assets/logo.png'></i></div>"),$headers);
		}*/
		$gl_id=0;
	}
}
?>
