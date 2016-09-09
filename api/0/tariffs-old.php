<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

auth();

$db=new database();
$db->connect();

//print_r($user);
$qorders=$db->query('SELECT order_id FROM blog_orders WHERE ut_id='.$user['ut_id']);
$count_orders=$db->num_rows($qorders);

$qtariffs=$db->query('SELECT * FROM blog_tariff WHERE (tariff_id>=5 AND tariff_id<=7) OR (tariff_id>=10 AND tariff_id<=11)');
while ($tariff=$db->fetch($qtariffs))
{
	$mtar[]=$tariff;
}
//print_r($mtar);
$i=0;
$j=0;
foreach ($mtar as $key => $item)
{
	if (($item['tariff_posts']==0) && ($count_orders<$item['tariff_quot']))
	{
		$outmas[0]['name']='subscribe';
		$outmas[0]['rates'][$j]['tariff_id']=$item['tariff_id'];
		$outmas[0]['rates'][$j]['title']=$item['tariff_name'];
		$outmas[0]['rates'][$j]['description']=$item['tariff_desc'];
		$outmas[0]['rates'][$j]['text_price']='от '.$item['tariff_price'].' р./мес';
		$outmas[0]['rates'][$j]['periods'][0]['count']=1;
		$outmas[0]['rates'][$j]['periods'][0]['title']='1 месяц';
		$outmas[0]['rates'][$j]['periods'][0]['text_price']=$item['tariff_price'].' р.';
		$outmas[0]['rates'][$j]['periods'][0]['price']=$item['tariff_price'];
		$outmas[0]['rates'][$j]['periods'][1]['count']=6;
		$outmas[0]['rates'][$j]['periods'][1]['title']='6 месяцев';
		$outmas[0]['rates'][$j]['periods'][1]['text_price']=($item['tariff_price']*6).' р.';
		$outmas[0]['rates'][$j]['periods'][1]['price']=($item['tariff_price']*6);
		$outmas[0]['rates'][$j]['periods'][2]['count']=12;
		$outmas[0]['rates'][$j]['periods'][2]['title']='12 месяцев';
		$outmas[0]['rates'][$j]['periods'][2]['text_price']=($item['tariff_price']*12).' р.';
		$outmas[0]['rates'][$j]['periods'][2]['price']=($item['tariff_price']*12);
		$j++;
	}
	elseif ($item['tariff_quot']==0)
	{
		$outmas[1]['name']='messages';
		$outmas[1]['rates'][count($outmas[1]['rates'])]['tariff_id']=$item['tariff_id'];
		$outmas[1]['rates'][count($outmas[1]['rates'])-1]['title']=$item['tariff_name'];
		$outmas[1]['rates'][count($outmas[1]['rates'])-1]['text_price']=$item['tariff_price'].' р.';
		$outmas[1]['rates'][count($outmas[1]['rates'])-1]['price']=$item['tariff_price'];
		$i++;
	}
}

//print_r($outmas);
echo json_encode($outmas);

?>