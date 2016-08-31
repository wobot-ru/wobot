<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/userjob/bot/kernel.php');

require_once('/var/www/daemon/fulljob/adv_src_func.php');

require_once('/var/www/daemon/userjob/get_twitter2.php');
require_once('/var/www/daemon/userjob/get_livejournal.php');
require_once('/var/www/daemon/userjob/get_li.php');
require_once('/var/www/daemon/userjob/get_mail.php');
require_once('/var/www/daemon/userjob/get_rutwit.php');
require_once('/var/www/daemon/userjob/get_yaru.php');
require_once('/var/www/daemon/userjob/get_plus_google.php');
// require_once('userjob/get_vkontakte2.php');
require_once('/var/www/daemon/userjob/parsers/babyblog/get_babyblog.php');
require_once('/var/www/daemon/userjob/parsers/foursquare/get_foursquare.php');
require_once('/var/www/daemon/userjob/parsers/friendfeed/get_friendfeed.php');
require_once('/var/www/daemon/userjob/parsers/kp/get_kp.php');

require_once('/var/www/daemon/3rd_cs/google_news/get_gnews.php');
require_once('/var/www/daemon/3rd_cs/novoteka_news/get_novoteka.php');
require_once('/var/www/daemon/3rd_cs/yandex_news/get_yandex_news.php');

require_once('/var/www/daemon/3rd_cs/banki_forum.php');
require_once('/var/www/daemon/3rd_cs/banki_friends.php');
require_once('/var/www/daemon/3rd_cs/banki_question.php');
require_once('/var/www/daemon/3rd_cs/banki_responses.php');
require_once('/var/www/daemon/3rd_cs/facebook-gr.php');
require_once('/var/www/daemon/3rd_cs/google_plus.php');
require_once('/var/www/daemon/3rd_cs/instagram.php');
require_once('/var/www/daemon/3rd_cs/mail.php');
require_once('/var/www/daemon/3rd_cs/market2.php');
require_once('/var/www/daemon/3rd_cs/ok.php');
require_once('/var/www/daemon/3rd_cs/tag_instagram.php');
require_once('/var/www/daemon/3rd_cs/torgmail.php');
require_once('/var/www/daemon/3rd_cs/twitter.php');
require_once('/var/www/daemon/3rd_cs/vk-ac.php');
require_once('/var/www/daemon/3rd_cs/vk-board.php');
require_once('/var/www/daemon/3rd_cs/vk-gr.php');
require_once('/var/www/daemon/3rd_cs/vk-video.php');

//error_reporting(0);

$server='beta.wobot.ru';

$db=new database();
$db->connect();

$redis = new Redis();    
$redis->connect('127.0.0.1');

function parseUrlmail($url,$to,$subj,$body,$from)
{
	$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
	$keyword=$word;
	$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
	//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
	$postvars='mail='.$to.'&title='.$subj.'&content='.$body.'&from='.$from;
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
	return $content;
}

// echo 'SELECT SUM(post_engage) FROM blog_post WHERE post_host=\'twitter.com\' post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y'));
$t='Вовлеченность: <br>';
$qeng=$db->query('SELECT SUM(post_engage) as cnt,post_host FROM blog_post WHERE post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' GROUP BY post_host ORDER BY cnt DESC');
while ($eng=$db->fetch($qeng))
{
	if ($eng['cnt']!=0) $output.=$eng['cnt'].' '.$eng['post_host'].'<br>';
	if ($eng['post_host']=='facebook.com' && $eng['cnt']==0) $additional_title.='*';
	if ($eng['post_host']=='vk.com' && $eng['cnt']==0) $additional_title.='*';
	if ($eng['post_host']=='livejournal.com' && $eng['cnt']==0) $additional_title.='*';
	if ($eng['post_host']=='twitter.com' && $eng['cnt']==0) $additional_title.='*';
}

$cont=$redis->get('proxy_list');
$mproxy=json_decode($cont,true);

$html='<table border="1"><tr><td>source:</td><td>nick</td><td>login</td><td>loc</td><td>fol</td><td>gender</td><td>age</td><td>ico</td></tr>';
$inf=get_liveinternet('3206612');
$html.='<tr><td>liveinternet</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_lj('martin');
$html.='<tr><td>livejournal</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_rutwit('Mayskiy');
$html.='<tr><td>rutwit</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_twitter('ru_wobot');
$html.='<tr><td>twitter</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_yaru('tatya-pichugi');
$html.='<tr><td>yaru</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_babyblog('Mari_Kowalsky');
$html.='<tr><td>babyblog</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_foursquare('25376859');
$html.='<tr><td>foursquare</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_ff('ramjam');
$html.='<tr><td>friendfeed</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_kp('4125286');
$html.='<tr><td>kp</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_google_plus('103014885508349779123');
$html.='<tr><td>google plus</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$html.='</table>';
$output.=$html;





$html='</br><i>вторая таблица</i></br><table border="1"><tr><td>source:</td><td>count</td><td></td></tr>';
$inf=get_google_news('Путин');
//print_r($inf);
$html.='<tr><td>google_news</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_novoteka('Путин', mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>Novoteka</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_news('da89b2126ca0d849ae7cd1d49a19a36b', '9ecc28acb11d45029df59025d604d713');
$html.='<tr><td>Yandex news</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_forum_banki(12,78991,mktime(0,0,0,1,1,2014),mktime(0,0,0,4,9,2015));;
$html.='<tr><td>forum_banki</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';

$inf=get_friends_banki('tcs-bank',92507,mktime(0,0,0,5,1,2010),mktime(0,0,0,5,30,2015));;
$html.='<tr><td>banki_friends</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';

$inf=get_banki_question_page(2836732,mktime(0,0,0,date('n'),date('j')-5,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>banki_question</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_banki_responses_page('tcs',mktime(0,0,0,date('n'),date('j')-5,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>banki_responses</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_facebook_group('DaikinRussia',mktime(0,0,0,date('n'),date('j')-5,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>facebook-gr</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_instagram('kadyrov_95',mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>Instagram</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_tag_instagram('vtb',1417387000,1417389999);
$html.='<tr><td>Instagram_tag</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_mail_ru('tinkoff_ins',mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>Mail</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
get_market(json_decode('{"shop_id":"174505"}',true),mktime(0,0,0,9,1,2013),mktime(0,0,0,10,20,2013));
$html.='<tr><td>Market</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_ok('tinkoff.ins',mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>OK</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_tmail('http://torg.mail.ru/review/shops/iqmobile-ru-cid1097/',mktime(0,0,0,7,1,2010),mktime(0,0,0,7,20,2013));
$html.='<tr><td>torg_mail</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_twitter('park_muzeon',mktime(0,0,0,date('n'),date('j')-30,date('Y')), mktime(0,0,0,date('n'),date('j')+1,date('Y')));
$html.='<tr><td>Twitter</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_vk_account('1555432',mktime(0,0,0,12,1,2012),mktime(0,0,0,12,5,2015));
$html.='<tr><td>VK_ac</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_vk_board(1,mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>VK_board</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_vk_board_topic('20225241','27216172',mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>VK_board_topic</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_vk_group('tinkoffbank',mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>VK_group</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';
$inf=get_vk_video_album(1,mktime(0,0,0,date('n'),date('j')-5,date('Y')), mktime(0,0,0,date('n'),date('j'),date('Y')));
$html.='<tr><td>VK_video</td><td>'.count($inf['link']).'</td><td>'.(count($inf['link'])>0?'<font color="green">OK</font>':'<font color="red">Not good</font>').'</td></tr>';

$html.='</table>';

$not_goods=substr_count($html,'Not good');

$html.='<br>Итого ресурсов отвалилось: '.$not_goods;
$output.=$html;

//echo ('123finish123');

//if ($not_goods>0) {
	parseUrlmail('http://188.120.239.225/api/service/sendmail.php','wobottest@yandex.ru','Статистика работы сервера ('.$server.') '.$additional_title.' | '.$not_goods,$output,'noreply2@wobot.ru');
//}
?>