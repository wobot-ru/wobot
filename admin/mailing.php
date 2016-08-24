
<?php 
require_once('com/config.php');
require_once('com/db.php');
require_once('com/auth.php');
$db = new database();
$db->connect();

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
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;*/
  return $content;
}

auth();
if (!$loged) die();
if(!isset($_GET['user_id']) || $_GET['user_id']==""){
					echo "Нет user_id";
					die();
				}
if(isset($_GET['user_id']) && isset($_GET['firsttheme']) && $_GET['firsttheme']==1){
	//$res=$db->query('SELECT * FROM `blog_orders` AS a LEFT JOIN users AS b ON a.user_id=b.user_id LEFT JOIN user_tariff AS c ON b.user_id=c.user_id WHERE a.user_id='.(intval($_GET['user_id'])).' ORDER BY order_id ASC limit 1');
	$res=$db->query('SELECT * FROM `users` WHERE user_id='.(intval($_GET['user_id'])));
				/*var_dump($row);
				if($row==NULL){
					echo "Нет тем";
					die();
				}*/
				while ($row=$db->fetch($res))
				{
						$username=$row['user_name'];
						//$order_name=$row['order_name'];
						$user_login=$row['user_email'];
						//$user_pass=$row['user_pass'];
						//$start_date=date('j.n.Y',$row['user_ctime']);
						//$end_date=date('j.n.Y',$row['ut_date']);

                        //$userpass=mb_substr(md5(date('H:i:s')),0,8,"UTF-8");
                        $userpass=md5(date('H:i:s'));
                        //$userpass="<a href='$userpass'>задайте свой пароль</a> ";
                        
				}
		$firsttheme_tpl=1;
}

// function parseUrlmail($url,$to,$subj,$body,$from)
// {
// $message=str_replace('\\\'','\'',urldecode($body));
// $headers = "From: ".$from."\r\nReply-To: ".$from."\r\n";
// $headers .= "Content-Type: text/html; charset=utf-8";
// $theme = 'Заявка с сайта '.date("d.m.Y");
// $mailst =mail($to,$subj, stripslashes($message),$headers);
// }

if(isset($_POST['send']) && isset($_GET['user_id'])){
	if($_POST['text']!=""){
		$subj="Wobot: доступ к системе Wobot Monitor.";
		$user_text=$_POST['text'];
		$from=$_POST['from'];
		$up=$db->query('UPDATE `users` SET user_pass=\''.addslashes(md5($_POST['pass'])).'\' WHERE user_id='.(intval($_GET['user_id'])));
		var_dump($user_text);
		$up_act=$db->query('UPDATE users SET user_active=2 WHERE user_id='.intval($_GET['user_id']));
		//parseUrlmail('http://wobot.ru/mail_send.php',"axestal.post@gmail.com",$subj,$user_text,$from);
		parseUrlmail('http://91.218.246.79/api/service/sendmail.php',$user_login,$subj,urlencode($user_text),$from);
		header('location:mailing.php?'.$_SERVER['QUERY_STRING']);
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>	
			Переписка
		</title>
		<link rel="stylesheet" type="text/css" href="css/mailing.css">
		<script type="text/javascript" src="js/jquery190.js"></script>
		<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				var template_old='<div><?php echo $username; ?>, здравствуйте!</div><br>\
				<div>Упоминания по вашей теме <b><?php echo $order_name; ?></b> собраны, доступ в систему мониторинга Wobot активирован.</div>\
				<div>Для доступа в систему перейдите, пожалуйста, по ссылке: production.wobot.ru.</div>\
				<div>Ваш логин: <?php echo $user_login; ?></div>\
				<div>Ваш пароль: <?php echo $userpass; ?></div>\
				<div>В тестовом режиме вы можете пользоваться системой в течение 2-х недель: с <?php echo $start_date; ?> по <?php echo $end_date; ?>.</div>\
				<div>\
				В дополнение к уже созданной теме вы можете создать еще две новых. Если у вас возникнут сложности с составлением поискового запроса или с использованием системы, свяжитесь с нами, мы с удовольствием вам поможем. Помните, что на сбор тем в тестовом доступе действует ограничение: не более 10 тысяч упоминаний в каждой теме.</div>\
				<div>Команда Wobot.</div>\
				<div>Поддержка клиентов:</div>\
				<div>+7 (495) 669-27-33,</div>\
				<div>account-one@wobot-research.com</div>\
				<div>skype ask.wobot</div><div><i><img src=\'http://wobot.ru/new/assets/logo.png\'></i></div>';
				var template='<div><?php echo $username; ?>, здравствуйте!</div><div> </div>\
				<div>Для доступа в систему перейдите, пожалуйста, по <a href="http://production.wobot.ru?mp=1&token=<?php echo $userpass; ?>&uid=<?php echo $_GET['user_id']; ?>">ссылке</a>.</div>\
				<div>Ваш логин: <?php echo $user_login; ?></div>\
				<div>Пароль задается Вами самостоятельно. Обратите внимание, что пароль должен быть не менее шести символов и обязательно содержать одну заглавную букву и одну цифру. </div>\
				<div>Ваш Демо-кабинет в системе будет доступен в течение одной недели.</div>\
				<div>\
				Ограничения пользования Демо-кабинетом: создавать можно только две темы, экспорт доступен только для последних 100 сообщений, ретроспективный поиск доступен за последний месяц.</div>\
				<div>\
				Если у вас возникнут сложности с составлением поискового запроса или с использованием системы, свяжитесь с нами, мы с удовольствием Вам поможем.</div>\
				<div> </div>\
				<div>С уважением,</div>\
				<div>Мария Поддубная</div>\
				<div>Аккаунт менеджер</div>\
				<img src=\'http://wobot.ru/new/assets/logo.png\'>\
				<div>Тел.: +7 (495) 669-27-33</div><div>Моб.: +7 (968) 531-79-73</div><div><a href="http://wobot.ru">www.wobot.ru</a></div>';
				var config = {width:800, height:400};
				editor = CKEDITOR.appendTo( 'editor', config, '');
				<?php if ($firsttheme_tpl==1) {
						echo 'CKEDITOR.instances.editor1.setData( template );';
					}
				?>

				$("#send").click(function(){
					var text=CKEDITOR.instances.editor1.getData();
					var from="<?php if($user_login!=""){
						echo $user_login;
					}
					else{
						echo "no-reply@wobot.ru";
					} ?>";
					var pass="<?php echo $userpass; ?>";
					$("#send_text").val(text);
					// $("#send_from").val(from);
					$("#send_pass").val(pass);
					$("#form_send").submit();
				});
				$("#send2").click(function(){
					var text=CKEDITOR.instances.editor1.getData();
					var from="<?php if($user_login!=""){
						echo $user_login;
					}
					else{
						echo "no-reply@wobot.ru";
					} ?>";
					var pass="<?php echo $userpass; ?>";
					$("#send_text2").val(text);
					// $("#send_from2").val(from);
					$("#send_pass2").val(pass);
					$("#form_send2").submit();
				});
			});
		</script>
	</head>
	<body>
		<?php //echo 'UPDATE `users` SET user_pass=\''.addslashes(md5($userpass)).'\' WHERE user_id='.(intval($_GET['user_id'])); ?>
		<div id="editor">
		</div>
		<div><button id="send">Send from account-one@wobot-research.com</button></div>
		<form method="post" action="?<?php echo $_SERVER['QUERY_STRING'];?>" id="form_send">
			<input type="hidden" name="send" value="1">
			<input type="hidden" name="text" id="send_text" value="">
			<input type="hidden" name="from" id="send_from" value="account-one@wobot-research.com">
			<input type="hidden" name="pass" id="send_pass" value="">
			<!--<input type="hidden" name="" value="">-->
		</form>
		<div><button id="send2">Send from account-two@wobot-research.com</button></div>
		<form method="post" action="?<?php echo $_SERVER['QUERY_STRING'];?>" id="form_send2">
			<input type="hidden" name="send" value="1">
			<input type="hidden" name="text" id="send_text2" value="">
			<input type="hidden" name="from" id="send_from2" value="account-two@wobot-research.com">
			<input type="hidden" name="pass" id="send_pass2" value="">
			<!--<input type="hidden" name="" value="">-->
		</form>
	</body>
</html>