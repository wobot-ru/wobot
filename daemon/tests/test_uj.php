<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('userjob/bot/kernel.php');

require_once('fulljob/adv_src_func.php');

require_once('userjob/get_twitter2.php');
require_once('userjob/get_livejournal.php');
require_once('userjob/get_li.php');
require_once('userjob/get_mail.php');
require_once('userjob/get_rutwit.php');
require_once('userjob/get_yaru.php');
require_once('userjob/get_plus_google.php');
// require_once('userjob/get_vkontakte2.php');
require_once('userjob/parsers/babyblog/get_babyblog.php');
require_once('userjob/parsers/foursquare/get_foursquare.php');
require_once('userjob/parsers/friendfeed/get_friendfeed.php');
require_once('userjob/parsers/kp/get_kp.php');

error_reporting(0);

$db=new database();
$db->connect();

$cont=parseUrl('http://188.120.239.225/getlist.php');
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

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
mail('zmei123@yandex.ru','Userjob',$html,$headers);
mail('r@wobot.co','Userjob',$html,$headers);

?>