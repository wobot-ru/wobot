<?php
require_once 'src/apiClient.php';
require_once 'src/contrib/apiPlusService.php';
session_start();

$client = new apiClient();
$client->setApplicationName('google+');
// Visit https://code.google.com/apis/console?api=plus to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('844035275813-8uas7p7ui7gsvt94c6jsc23k9dshb5es.apps.googleusercontent.com');
$client->setClientSecret('hwSldTKTqXFIK9eQq9_QAu8b');
$client->setRedirectUri('http://bmstu.wobot.ru/tools/fsearch/google_plus/ind.php');
$client->setDeveloperKey('AIzaSyB8uR3Pp44Cfj-JvpUipsx7F89zt4tSORM');
//$client->setAccessToken(json_encode('ya29.AHES6ZQB3hVnv5csLXD8cWXW7hX_mZHUjOgT6PvYgFa0o3yKkzPu'));
//$client->setAccessToken(json_encode('4/qB44dGFC7TzP1dePTHcPfEKvbB-A'));
//$client->refreshToken('1/uNSe5jKPYf-8fmppBcTyv_DhoFDSHI9j2S8dU3rPFT0');
//$client->createAuthUrl('https://www.googleapis.com/auth/userinfo.profile');
//$client->authenticate();
$plus = new apiPlusService($client);

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $activities = $plus->activities->listActivities('me', 'public');
  print 'Your Activities: <pre>' . print_r($activities, true) . '</pre>';

  // The access token may have been updated.
  $_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  print "<a href='$authUrl'>Connect Me!</a>";}
//4/zYoOa2FtXLo1PDYKk0IAweCPLqSF  4/jKWwVMm1sFT0TmL0MPi63qgW8YvW
/*function parseUrl1medcanal($url,$word)
{
  $uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;//4/TIYGLSvI5sOjz77RqNEvFhTBpacH
//$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='code=4/jKWwVMm1sFT0TmL0MPi63qgW8YvW&client_id=844035275813.apps.googleusercontent.com&redirect_uri=urn:ietf:wg:oauth:2.0:oob&access_type=offline&response_type=code&scope=https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
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
  //return $content;
//}
//echo parseUrl1medcanal('https://accounts.google.com/o/oauth2/auth','');
//}
//print_r($_SESSION);
?>