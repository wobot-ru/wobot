<?
// require_once('/var/www/bot/kernel.php');
/*

Roman Yudin: <[^<]*?>.*?[0-9]*.*?[ ]*comment(.*?)[ ]*.*?[0-9]*.*?<[^<]*?>
[14:12:14] Roman Yudin: <[^<]*?>.*?(?<data1>[0-9]*?).*?[ ]*comment(.*?)[ ]*.*?(?<data2>[0-9]*?).*?<[^<]*?>
[14:13:39] z_me_i: <div class="summary" id="fw_summary">5 комментариев</div>
[14:14:33] Roman Yudin: <[^<]*?>.*?[0-9]*.*?[ ]*(comment|коммент)(.*?)[ ]*.*?[0-9]*.*?<[^<]*?>


*/

function get_vk($url,$cont)
{
	// echo $cont;
	//$cont=iconv('windows-1251', 'utf-8', $cont);
	$regex_link='/(?<id_gr>[0-9\-]+)\_(?<id>\d+)/isu';
	preg_match_all($regex_link, $url, $out);
	// print_r($out);
	$regex='/<span class="like_count fl_l" id="like_count'.preg_replace('/\-/isu','\-',$out['id_gr'][0]).'_wall_reply'.$out['id'][0].'">(?<likes>\d+)<\/span>/isu';
	preg_match_all($regex, $cont, $out_likes);
	$regex2='/<span class="fw_like_count fl_l" id="like_count'.preg_replace('/\-/isu','\-',$out['id_gr'][0]).'_'.$out['id'][0].'">(?<likes>\d+)<\/span>/isu';
	preg_match_all($regex2, $cont, $out_likes2);
	 //print_r($out_likes);
	 //print_r($out_likes2);
	if (intval($out_likes['likes'][0])!=0) 
	{
		$outmas['likes']=intval($out_likes['likes'][0]);
		$outmas['comment']=0;
		$outmas['repost'] = 0;
	}
	elseif (intval($out_likes2['likes'][0])!=0) 
	{
		$regex_comment='/<div class="summary" id="fw_summary">(?<comments>.*?)<\/div>/isu';
		preg_match_all($regex_comment, $cont, $out_com);
		// print_r($out_com);
		$outmas['likes']=intval($out_likes2['likes'][0]);
		$outmas['comment']=intval(preg_replace('/[^0-9]/is', '', $out_com['comments'][0]));
		$outmas['repost'] = 0;
	}
	else 
	{
		$outmas['likes']=0;
		$outmas['comment']=0;
		$outmas['repost'] = 0;
	}
	if($outmas['likes']>0){
		$json=parseUrl('https://api.vkontakte.ru/method/wall.getById?posts='.$out['id_gr'][0].'_'.$out['id'][0].'&v=5.25');
		$rep_data = json_decode($json, true);
		$repost = $rep_data['response'][0]['reposts']['count'];
		if($repost){
			$outmas['repost'] = $repost;
		} else {
			$outmas['repost'] = 0;
		}
	}
	// print_r($outmas);
	$outtmas['count']=$outmas['comment']+$outmas['likes']+$outmas['repost'];
	$outtmas['data']=$outmas;
	//print_r($outtmas);
	//die();
	return $outtmas;
}
//get_vk('http://vk.com/wall100598159_1728');
// $cont=parseUrl('http://188.120.239.225/getlist.php');
// $mproxy=json_decode($cont,true);
// $link='http://vk.com/wall1555432_184';
// print_r(get_vk($link,parseUrl($link)));
//echo get_vk('http://vkontakte.ru/wall122784_870');

//get_vk('http://vk.com/wall-30666517_997512', parseUrl('http://vk.com/wall-30666517_997512'));
//get_vk('http://vk.com/wall3118373_830', parseUrl('http://vk.com/wall3118373_830'));
?>
