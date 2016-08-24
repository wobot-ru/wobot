<?
//Авто пересбор запросов с нулевыми последними 2мя днями
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

error_reporting(E_ERROR);

$db = new database();
$db->connect();
/*while (1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
	+71231234567
	(903) 538 61 38 -> +79035386138
	
*/
	function extract_phone($phone)
	{
		$phone = preg_replace("/[^0-9]/", "", $phone);
		if ((mb_substr($phone, 0,2,'utf-8')=='89')||(mb_substr($phone, 0,2,'utf-8')=='88')||(mb_substr($phone, 0,1,'utf-8')=='9')&&mb_strlen($phone,'utf-8')==10) $phone='+7'.mb_substr($phone, 2,'utf-8');
		if (mb_substr($phone, 0,2,'utf-8')=='84') $phone='+74'.mb_substr($phone, 2,'utf-8');
		if (mb_strlen($phone,'utf-8')<7) $phone='';
		elseif (mb_strlen($phone,'utf-8')==7) $phone='+7495'.$phone;
		elseif (mb_strlen($phone,'utf-8')>10) $phone='+'.$phone;

		if (mb_strlen($phone,'utf-8')>12) $phone='';
		return $phone;
	}
	function extract_phone1($phone)
	{
		$regex='/(?<dig>[\d])/isu';
		preg_match_all($regex, $phone, $out);
		//print_r($out);
		if (count($out['dig'])==11)
		{
			$phone=preg_replace('/\+8/isu','+7','+'.$out['dig'][0].$out['dig'][1].$out['dig'][2].$out['dig'][3].$out['dig'][4].$out['dig'][5].$out['dig'][6].$out['dig'][7].$out['dig'][8].$out['dig'][9].$out['dig'][10]);
		}
		elseif (count($out['dig'])==10) 
		{
			$phone='+7'.$out['dig'][0].$out['dig'][1].$out['dig'][2].$out['dig'][3].$out['dig'][4].$out['dig'][5].$out['dig'][6].$out['dig'][7].$out['dig'][8].$out['dig'][9];
		}
		elseif (count($out['dig'])==7) 
		{
			$phone='+7495'.$out['dig'][0].$out['dig'][1].$out['dig'][2].$out['dig'][3].$out['dig'][4].$out['dig'][5].$out['dig'][6];
		}
		else
		{
			$phone='';
		}
		return $phone;
	}
	$i=0;
	$orders=$db->query('SELECT blog_id, blog_mphone, blog_hphone FROM robot_blogs4 WHERE blog_mphone!="" or blog_hphone!=""');
	while ($blog=$db->fetch($orders))
	{
		$mphone=extract_phone1($blog['blog_mphone']);
		$hphone=extract_phone1($blog['blog_hphone']);
		if ($mphone!=''||$hphone!='')
			{
				echo $blog['blog_id']."\t\t|".$mphone."\t\t|".$hphone."\n";
				$db->query('UPDATE robot_blogs4 set blog_mphone_sha1="'.($mphone==''?'':sha1($mphone)).'", blog_hphone_sha2="'.($hphone==''?'':sha1($hphone)).'"  WHERE blog_id='.$blog['blog_id']);
		//echo 'UPDATE robot_blogs4 set blog_mphone_sha1="'.($mphone==''?'':sha1($mphone)).'", blog_hphone_sha2="'.($hphone==''?'':sha1($hphone)).'"  WHERE blog_id='.$blog['blog_id'];
		}
	}
/*
	foreach ($ords as $key => $item)
	{
		$cc=$db->query('SELECT count(post_id) as cnt FROM blog_post WHERE order_id='.$key.' AND post_time>'.((time()-2*86400)<$ords['order_start']?$ords['order_start']:(time()-2*86400)));
		$count=$db->fetch($cc);
		//print_r($count);
		if ($count['cnt']==0)
		{
			echo $key."\n";
			echo 'UPDATE blog_orders SET third_sources=1 WHERE order_id='.$key."\n";
			$db->query('UPDATE blog_orders SET third_sources=1 WHERE order_id='.$key);
		}
	}*/
//	sleep(10800);
//}
?>