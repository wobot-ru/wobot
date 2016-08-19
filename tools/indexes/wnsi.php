<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

function send_data($order_id,$start,$end)
{
	global $db;
	$qorder=$db->query('SELECT * FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE order_id='.$order_id.' LIMIT 1');
	$order=$db->fetch($qorder);
	$token=md5(mb_strtolower($order['user_email'],'UTF-8').':'.$order['user_pass']);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://31.28.5.35/api/0/get_wnsi?test_user_id='.$order['user_id'].'&test_token='.$token);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //Нужно явно указать, что будет POST запрос
    curl_setopt($ch, CURLOPT_POST, true);
    //Здесь передаются значения переменных
    // echo $data;
    $query='order_id='.$order_id.'&start='.$start.'&end='.$end;
    // echo $query."\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // таймаут ответа
    curl_setopt($ch, CURLOPT_USERAGENT, 'FUCK');
    $data = curl_exec($ch);
    curl_close($ch);
    // echo $data;
    return $data;

// $x = curl_init('http://146.185.183.12/api/service/reciver.php');
//   curl_setopt($x, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
//   curl_setopt($x, CURLOPT_ENCODING, 'bzip');
//   curl_setopt($x, CURLOPT_POST, 1);
//   curl_setopt($x, CURLOPT_POSTFIELDS, $data);
//   curl_setopt($x, CURLOPT_FOLLOWLOCATION, 0);
//   curl_setopt($x, CURLOPT_RETURNTRANSFER, 1);
//   $data = curl_exec($x);

}

function send_data2($order_id,$start,$end)
{
	global $db;
	$qorder=$db->query('SELECT * FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE order_id='.$order_id.' LIMIT 1');
	$order=$db->fetch($qorder);
	$token=md5(mb_strtolower($order['user_email'],'UTF-8').':'.$order['user_pass']);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://31.28.5.35/api/0/redis_order2?test_user_id='.$order['user_id'].'&test_token='.$token);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //Нужно явно указать, что будет POST запрос
    curl_setopt($ch, CURLOPT_POST, true);
    //Здесь передаются значения переменных
    // echo $data;
    $query='order_id='.$order_id.'&start='.$start.'&end='.$end;
    // echo $query."\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // таймаут ответа
    curl_setopt($ch, CURLOPT_USERAGENT, 'FUCK');
    $data = curl_exec($ch);
    curl_close($ch);
    // echo $data;
    return $data;

// $x = curl_init('http://146.185.183.12/api/service/reciver.php');
//   curl_setopt($x, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
//   curl_setopt($x, CURLOPT_ENCODING, 'bzip');
//   curl_setopt($x, CURLOPT_POST, 1);
//   curl_setopt($x, CURLOPT_POSTFIELDS, $data);
//   curl_setopt($x, CURLOPT_FOLLOWLOCATION, 0);
//   curl_setopt($x, CURLOPT_RETURNTRANSFER, 1);
//   $data = curl_exec($x);

}

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
if ($_POST['order_id']!='') 
{
	// print_r($_POST);
	$index=send_data($_POST['order_id'],$_POST['start'],$_POST['end']);
	$mindex=json_decode($index,true);
	$index2=send_data2($_POST['order_id'],$_POST['start'],$_POST['end']);
	$mindex2=json_decode($index2,true);
	// print_r($mindex2);
	echo '<b>WNSI INDEX: '.$mindex['wnsi'].'</b><br>';
	echo '<b>NSI INDEX: '.$mindex2['nsi'].'</b>';
}
echo '<form method="POST">
order_id: <input type="text" name="order_id"><br>
start: <input type="date" name="start"><br>
end: <input type="date" name="end"><br>
<input type="submit" value="посчитать">
</form>';

?>