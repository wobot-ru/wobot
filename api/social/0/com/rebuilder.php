<?

function rebuild_json($json,$start,$end)
{
	$mas=json_decode($json,true);
	//print_r($mas);
	$outmas[2]['posts']=array();
	$outmas[3]['list']=array();
	$outmas[3]['more5']=0;
	$outmas[3]['less5']=0;
	$outmas[3]['passive']=0;
	$outmas[3]['all']=0;
	$outmas[2]['count_posts']=0;
	//$outmas[2]['types']=array();
	$outmas[2]['posts_graph']=array();
	$outmas[2]['count_comments']=0;
	$outmas[2]['count_likes']=0;
	$outmas[2]['count_reposts']=0;
	$outmas[5]['not_in_group']=array();
	$outmas[4]['count_without_photo']=0;
	$outmas[4]['count_woman']=0;
	$outmas[4]['count_man']=0;
	$outmas[4]['count_with_photo']=0;
	$outmas[1]['name_report']=$mas[0]['uid'].'_'.date('n_j_Y',$start).'_'.date('n_j_Y',$end).'.xls';
	$outmas[1]['name']=$mas[0]['first_name'].' '.$mas[0]['last_name'];
	$outmas[1]['userpic']=$mas[0]['photo_big'];
	$outmas[1]['count_people']=$mas[0]['counters']['friends'];
	$outmas[1]['mobile']=$mas[1]['count_activate'];//.'/'.$mas[0]['counters']['friends'];
	$outmas[1]['photo']=$mas[1]['count_uniq'];//.'/'.$mas[0]['counters']['friends'];
	$outmas[1]['link']='http://vk.com/'.$mas[0]['domain'];
	$outmas[1]['start']=date('j.n.Y',$start);
	$outmas[1]['end']=date('j.n.Y',$end);
	$outmas[1]['count_block']=$mas[1]['count_block'];
	$outmas[1]['count_delete']=$mas[1]['count_deleted'];
	$outmas[2]['posts']=array();
	$outmas[2]['count_posts']=0;
	$outmas[2]['posts_graph'][][0]='Дата:';
	foreach ($mas[4] as $key => $item)
	{
		$outmas[2]['types'][0][]=$key;
		$outmas[2]['types'][1][]=$item;
		$outmas[2]['posts_graph'][][0]=$key;
		$pos_post_graph[$key]=count($outmas[2]['posts_graph'])-1;
	}
	$pos=1;
	for($t=$start;$t<=$end;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		//print_r($mas[5][$t]);
		$outmas[2]['posts_graph'][0][$pos]=date('j.n.Y',$t);		
		for ($i=1;$i<count($outmas[2]['posts_graph']);$i++)
		{
			$outmas[2]['posts_graph'][$i][$pos]=0;		
		}
		foreach ($mas[5][$t] as $key => $item)
		{
			$outmas[2]['posts_graph'][$pos_post_graph[$key]][$pos]=$item;		
		}
		$pos++;
	}
	$outmas[2]['count_comments']=intval($mas[4]['comment']);
	$outmas[2]['count_likes']=intval($mas[4]['like']);
	$outmas[2]['count_reposts']=intval($mas[4]['reposts']);
	$outmas[3]['all']=$mas[0]['counters']['friends'];
	$outmas[3]['passive']=$mas[2]['passive'];//intval($mas[0]['counters']['friends'])-intval($mas[2]['k1']['count']);
	$outmas[3]['less5']=intval($mas[2]['k1']['count']);
	$outmas[3]['more5']=intval($mas[2]['k5']['count']);
	foreach ($mas[2]['k1']['users'] as $item)
	{
		$outmas[3]['list'][0][]='http://vk.com/id'.$item;
	}
	foreach ($mas[2]['k3']['users'] as $item)
	{
		$outmas[3]['list'][1][]='http://vk.com/id'.$item;
	}
	foreach ($mas[2]['k5']['users'] as $item)
	{
		$outmas[3]['list'][2][]='http://vk.com/id'.$item;
	}
	foreach ($mas[2]['k7']['users'] as $item)
	{
		$outmas[3]['list'][3][]='http://vk.com/id'.$item;
	}
	$outmas[3]['topactions'][0][0]=0;
	$outmas[3]['topactions'][0][1]=1;
	$outmas[3]['topactions'][0][2]=3;
	$outmas[3]['topactions'][0][3]=5;
	$outmas[3]['topactions'][0][4]=7;
	$outmas[3]['topactions'][1][0]=$mas[2]['passive'];//intval($mas[0]['counters']['friends'])-intval($mas[2]['k1']['count']);
	$outmas[3]['topactions'][1][1]=intval($mas[2]['k1']['count']);
	$outmas[3]['topactions'][1][2]=intval($mas[2]['k3']['count']);
	$outmas[3]['topactions'][1][3]=intval($mas[2]['k5']['count']);
	$outmas[3]['topactions'][1][4]=intval($mas[2]['k7']['count']);
	$outmas[4]['count_woman']=intval($mas[1]['count_woman']);
	$outmas[4]['count_man']=$mas[0]['counters']['friends']-intval($mas[1]['count_woman']);
	$outmas[4]['count_without_photo']=0;//intval($mas[0]['counters']['friends'])-intval($mas[1]['count_uniq']);
	$outmas[4]['count_with_photo']=0;//intval($mas[1]['count_uniq']);
	foreach ($mas[1]['age'] as $key => $item)
	{
		$outmas[4]['age'][0][]=$key;
		$outmas[4]['age'][1][]=$item;
	}
	foreach ($mas[1]['loc'] as $key => $item)
	{
		$outmas[4]['loc'][0][]=$key;
		$outmas[4]['loc'][1][]=$item;
	}
	foreach ($mas[1]['loc_cou_mas'] as $key => $item)
	{
		$outmas[4]['cou'][0][]=$key;
		$outmas[4]['cou'][1][]=$item;
	}
	$outmas[4]['count_without_photo']=$mas[1]['count_uniq'];
	foreach ($mas[1]['users']['uid'] as $key => $item)
	{
		$expn=explode(' ',$mas[1]['users']['name'][$key]);
		$outmas[5]['not_in_group'][0][]=$key+1;
		$outmas[5]['not_in_group'][1][]=$expn[0];
		$outmas[5]['not_in_group'][2][]=$expn[1];
		$outmas[5]['not_in_group'][3][]='http://vk.com/id'.(preg_match('/id[\d]+/isu',$item)?'id'.$item:$item);
		$outmas[5]['not_in_group'][4][]=$item;
		$outmas[5]['not_in_group'][5][]=($mas[1]['users']['sex'][$key]==2?'М':'Ж');
		$outmas[5]['not_in_group'][6][]=$mas[1]['users']['bdate'][$key];
		$outmas[5]['not_in_group'][7][]=$mas[1]['users']['city'][$key];
		$outmas[5]['not_in_group'][8][]=$mas[1]['users']['country'][$key];
		$outmas[5]['not_in_group'][9][]=$mas[1]['users']['timezone'][$key];
		$outmas[5]['not_in_group'][10][]=$mas[1]['users']['big_photo'][$key];
		$outmas[5]['not_in_group'][11][]=$mas[1]['users']['has_mobile'][$key];
		$outmas[5]['not_in_group'][12][]=$mas[1]['users']['rate'][$key];
		$outmas[5]['not_in_group'][13][]=$mas[1]['users']['mobile_phone'][$key];
		$outmas[5]['not_in_group'][14][]=$mas[1]['users']['home_phone'][$key];
		$outmas[5]['not_in_group'][15][]=$mas[1]['users']['university_name'][$key];
		$outmas[5]['not_in_group'][16][]=$mas[1]['users']['faculty_name'][$key];
		$outmas[5]['not_in_group'][17][]=$mas[1]['users']['graduation'][$key];
	}
	print_r($outmas);
	//die();
	return ($outmas);
}

/*rebuild_json('
{"0":{"uid":1555432,"first_name":"u0412u043eu043bu043eu0434u044f","last_name":"u0420u044bu0431u0430u043au043eu0432","nickname":"","sex":2,"bdate":"30.1","timezone":3,"photo":"http://cs10548.userapi.com/u1555432/e_adae3617.jpg","photo_medium":"http://cs10548.userapi.com/u1555432/d_d7dc36e3.jpg","photo_big":"http://cs10548.userapi.com/u1555432/a_26164727.jpg","domain":"id1555432","has_mobile":1,"rate":"85","home_phone":"","counters":{"albums":1,"videos":8,"audios":195,"notes":0,"photos":3,"groups":113,"friends":96,"online_friends":14,"mutual_friends":2,"user_videos":3,"followers":27}},"4":{"audio":27,"reposts":0,"like":6,"photo":4,"graffiti":5,"comment":7,"posted_photo":1,"video":2,"admin":null},"5":{"1346270400":{"audio":1},"1345320000":{"audio":1},"1344542400":{"audio":6},"1334520000":{"audio":1,"like":1},"1330722000":{"audio":1},"1329944400":{"photo":1},"1327870800":{"graffiti":1},"1325710800":{"photo":1},"1320958800":{"audio":1},"1320613200":{"comment":2},"1319832000":{"audio":1},"1316980800":{"audio":1},"1314216000":{"audio":1},"1314129600":{"audio":4},"1313352000":{"audio":1,"comment":1},"1313092800":{"audio":1},"1310932800":{"audio":1},"1307390400":{"audio":1},"1306440000":{"audio":1},"1302465600":{"like":1},"1299013200":{"photo":1,"comment":2,"like":3},"1299186000":{"comment":1},"1322514000":{"comment":1},"1296680400":{"audio":1},"1295125200":{"audio":1,"like":1},"1294779600":{"audio":1},"1294002000":{"graffiti":1},"1293656400":{"photo":1},"1287864000":{"posted_photo":1},"1281729600":{"video":2},"1266872400":{"graffiti":1},"1266181200":{"audio":1},"1264798800":{"graffiti":1},"1257973200":{"graffiti":1}},"2":{"k7":{"count":3,"users":[1555432,2497125,89575419]},"k1":{"count":30,"users":[1555432,2497125,89575419,64278146,1399624,2014401,27998151,432986,1593006,4367821,87628259,5041952,1345415,180180,1859617,1297925,634448,1878074,2530327,155310930,2071845,3058220,1925671,151989317,98455894,27762540,4103138,4558757,26496535,2949097]},"k3":{"count":9,"users":[1555432,2497125,89575419,64278146,1399624,2014401,27998151,432986,1593006]},"k5":{"count":7,"users":[1555432,2497125,89575419,64278146,1399624,2014401,27998151]}},"1":{"count_bot":5,"count_uniq":89,"count_woman":31,"count_man":65,"count_block":4,"count_deleted":0,"count_activate":75,"loc":{"u041cu043eu0441u043au0432u0430":31,"u0413u043eu043cu0435u043bu044c":1,"u0422u0432u0435u0440u044c":1},"loc_cou_mas":{"u0420u043eu0441u0441u0438u044f":33,"u0421u0428u0410":1,"u0411u0435u043bu0430u0440u0443u0441u044c":1},"age":{"23":11,"22":37,"25":1,"46":1,"21":1,"92":1,"27":1,"24":1}}}',1230757200,1348084800);*/
?>