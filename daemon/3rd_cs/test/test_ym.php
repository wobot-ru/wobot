<?

require_once('/var/www/daemon/3rd_cs/torgmail.php');
require_once('../market2.php');
require_once('../banki_forum.php');
require_once('../banki_friends.php');
require_once('../banki_question.php');

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

$db=new database();
$db->connect();

$torg=get_tmail('http://torg.mail.ru/review/shops/iqmobile-ru-cid1097/',mktime(0,0,0,1,1,2012),mktime(0,0,0,12,31,2012));
$market=get_market(array('shop_id'=>89991),mktime(0,0,0,date('n'),date('j')-30,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));
$banki_forum=get_forum_banki(14,14022,mktime(0,0,0,date('n'),date('j')-5,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));
$banki_friends=get_friends_banki('tcs-bank',92444,mktime(0,0,0,date('n'),date('j')-5,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));
$banki_question=get_banki_question_page(2836732,mktime(0,0,0,date('n'),date('j')-5,date('Y')),mktime(0,0,0,date('n'),date('j'),date('Y')));

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
mail('zmei123@yandex.ru','Тест 3rd_cs','torg: '.count($torg['time']).'<br>market: '.count($market['time']).'<br>banki_forum: '.count($banki_forum['time']).'<br>banki_friends: '.count($banki_friends['time']).'<br>banki_question: '.count($banki_question['time']),$headers);
mail('r@wobot.co','Тест 3rd_cs','torg: '.count($torg['time']).'<br>market: '.count($market['time']).'<br>banki_forum: '.count($banki_forum['time']).'<br>banki_friends: '.count($banki_friends['time']).'<br>banki_question: '.count($banki_question['time']),$headers);


?>