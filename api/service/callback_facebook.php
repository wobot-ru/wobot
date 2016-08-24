<?php
//echo "GET ".print_r($_GET)."<br> POST ".print_r($_POST)."<br> SESSION ".print_r($_SESSION);
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/bot/kernel.php');

	$db=new database();
	$db->connect();
$app_id = "1563349350640421";
$app_secret = "aedead448a75193b3f5123ba666d6e61";
//$response = parseURL('https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=698121240265476&client_secret=604458cd5600b9d82604a716fc535c1b&fb_exchange_token=CAAJ68ASJQwQBADKwKeaZBZCCJ2wC2zEyCZBwalSZA4BZBkZCVZCsMoPzYmcr6FsX4nwDwA4RGs0v29oZAyZAHVwDuotiMCA9Suiey6nII2QvNDK7KuoB4GB6ysYX8aByY9PeUDtpLi3HstJWZBL4jv1mYD5J99XZBUyx23NOZA7zut3akV13sxpPpncz9IRossWwZCVV4QzNgJYWyqX3wlrUfjcZBA&redirect_uri=http://31.28.5.35/api/service/callback_facebook.php&response_type=token');
//var_dump($response);
/*print_r($_GET);
print_r($_POST);
print_r($_SESSION);
print_r($_COOKIE);
print_r($_REQUEST);
print_r($_SERVER);*/
if(isset($_GET['newToken'])){
	$response = parseURL(makeRequestURL($app_id, $app_secret, $_GET['newToken']));
	//$response = makeRequestURL($app_id, $app_secret, $_GET['newToken']);
	echo $response;
	die();
}
if(isset($_GET['longToken'])){

	$count=0;
	$qtoken=$db->query('SELECT * FROM tp_keys WHERE key=\''.$_GET['longToken'].'\'');
	//echo 'SELECT * FROM tp_keys WHERE key=\''.$_GET['longToken'].'\'';
	// while ($token=$db->fetch($qtoken))
	// {
	// 	$count++;
	// }
	if($db->num_rows($qtoken)==0){
		$input = $db->query('INSERT INTO tp_keys (`key`, `type`, `in_use`) VALUES (\''.$_GET['longToken'].'\',\'fb\',\'3\')');
	}
	echo json_encode($input);
	die();
}

function makeRequestURL($app_id, $app_secret, $token){
	return 'https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id='.$app_id.'&client_secret='.$app_secret.'&fb_exchange_token='.$token.'&redirect_uri=http://31.28.5.35/api/service/callback_facebook.php&response_type=token';
}
?>

<html>
	<head>
		<meta charset="utf-8"> 
		<title>TheMightyToken</title>
		<script>
			var queryReg = new RegExp("access_token=((.)*)&","img");

			function getXmlHttp(){
			  var xmlhttp;
			  try {
			    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			  } catch (e) {
			    try {
			      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			    } catch (E) {
			      xmlhttp = false;
			    }
			  }
			  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
			    xmlhttp = new XMLHttpRequest();
			  }
			  return xmlhttp;
			}

			window.onload = function (){
					var out;
					
					out = queryReg.exec(document.location.hash);
					//console.log(111);
					//console.log(out[1]);
					document.getElementById("output").innerHTML = out[1];
					sendDATA("newToken", out[1]);
			}

			function sendDATA(param,token){
				var xmlhttp = getXmlHttp()
				xmlhttp.open('GET', 'callback_facebook.php?'+param+'='+token, true);
				xmlhttp.onreadystatechange = function() {
				  if (xmlhttp.readyState == 4) {
				    if(xmlhttp.status == 200) {
				       	//alert(xmlhttp.responseText);
				       	console.log(xmlhttp.responseText);
				       	//document.getElementById("output").innerHTML = xmlhttp.responseText;
				       	if(param == "newToken") {
				       		saveNewToken(xmlhttp.responseText);
				       	} else if (param == "longToken"){
				       		document.getElementById("output").innerHTML = "Токен занесен в базу";
				       	} else {	
				       		document.getElementById("output").innerHTML = "Ошибка: неизвестные данные";
				       	}
				    } else {
				    	document.getElementById("output").innerHTML = "Ошибка: ajax запрос не выполняется";
				    }
				  }
				};
				xmlhttp.send();
			}

			function saveNewToken(token){
				var re = new RegExp("access_token=((.)*)&","img");
				var longToken = re.exec(token);
				console.log(longToken[1]);
				sendDATA("longToken", longToken[1]);
			}

		</script>
	</head>
	<body>
		<div id="output">
		
		</div>
	</body>
</html>