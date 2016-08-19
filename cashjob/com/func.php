<?

function get_cash($orderr,$time)
{
	global $db,$wobot,$arr_nau,$redis,$prom_info;
	//print_r($orderr);
	$nword=array("здесь"=>1,"всем"=>1,"кто"=>1,"что"=>1,"сколько"=>1,"какой"=>1,"каков"=>1,"чей"=>1,"который"=>1,"столько"=>1,"этот"=>1,"тот"=>1,"такой"=>1,"таков"=>1,"весь"=>1,"всякий"=>1,"сам"=>1,"самый"=>1,"каждый"=>1,"любой"=>1,"другой"=>1,"иной"=>1,"никто"=>1,"ничто"=>1,"некого"=>1,"нечего"=>1,"нисколько"=>1,"никакой"=>1,"ничей"=>1,"onclick"=>1,"onclick="=>1,"return"=>1,"style="=>1,"to=http%"=>1,"fwww"=>1,"href="=>1,"factions%"=>1,"onmouseout="=>1,"textdecoration="=>1,"this"=>1,"style"=>1,"html"=>1,"cursor"=>1,"style"=>1,"pointer"=>1,"onmouseover="=>1,"underline"=>1,"none"=>1,"style="=>1,"http"=>1);
	$stime=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
	$etime=mktime(0,0,0,date('n',$stime),date('j',$stime)+1,date('Y',$stime));
	$settings=json_decode($orderr['order_settings'],true);
	//echo 'SELECT * FROM blog_orders WHERE order_id='.$order_id.' LIMIT 1';
	//$qorder=$db->query('SELECT order_id,order_keyword,order_start,order_end FROM blog_orders WHERE order_id='.$order_id.' LIMIT 1');
	//$order=$db->fetch($qorder);
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',mb_strtolower($orderr['order_keyword'],'UTF-8'));
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\.]/isu','  ',$keyword);
	$keyword=explode('  ',$keyword);
	foreach ($keyword as $item)
	{
		$assoc_kw[$item]=1;
	}
	if (($time<$orderr['order_start']) || ($orderr['order_end']!=0?($time>=($orderr['order_end']+86400)):($time>=time()))) return 0;
	$qpost=$db->query('SELECT p.post_link,p.post_content,p.post_nastr,p.post_engage,r.blog_id,r.blog_nick,r.blog_login,r.blog_readers,r.blog_location FROM blog_post as p LEFT JOIN robot_blogs2 as r ON p.blog_id=r.blog_id WHERE p.order_id='.$orderr['order_id'].' AND p.post_time>='.$stime.' AND p.post_time<'.$etime.(intval($settings['remove_spam'])==0?'':' AND p.post_spam!=1'));
	//echo 'SELECT p.post_link,p.post_content,p.post_nastr,p.post_engage,r.blog_id,r.blog_nick,r.blog_login,r.blog_readers,r.blog_location FROM blog_post as p LEFT JOIN robot_blogs2 as r ON p.blog_id=r.blog_id WHERE p.order_id='.$order_id.' AND p.post_time>='.$stime.' AND p.post_time<'.$etime;
	while ($post=$db->fetch($qpost))
	{
		$count_post++;
		$nick=$post['blog_nick'];
		$login=$post['blog_login'];
		$blog_id=$post['blog_id'];
		$hn=parse_url($post['post_link']);
		$hn=$hn['host'];
		$ahn=explode('.',$hn);
		$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh=$ahn[count($ahn)-2];
		if ($hn=='.')
		{
			$hn=$post['phost'];
		}
		if (($hn!='') && ($hn!='.'))
		{
			$beta_data['src'][$hn]++;
			$beta_data['src_nastr'][$post['post_nastr']]++;
			$beta_data['nastr'][$post['post_nastr']]++;
			$beta_data['notes']+=$post['post_note_count'];
			$beta_data['not_process']+=$post['post_read'];
			if (isset($arr_nau[$hn]))
			{
				$innf['blog_id']=0;
				if ((strpos($post['post_link'], 'thread')!==false) && ($hn=='livejournal.com'))
				{
					$rgx='/\/\/(?<nk>.*?)\./is';
					preg_match_all($rgx,$post['post_link'],$out);
					$login_lj=$out['nk'][0];
					if (!isset($prom_info[$login_lj]))
					{
						$bl_info=$db->query('SELECT blog_id,blog_readers,blog_location FROM robot_blogs2 WHERE blog_login=\''.$login_lj.'\' AND blog_link=\'livejournal\.com\' LIMIT 1');
						$innf=$db->fetch($bl_info);
						//print_r($innf);
						//echo $blog_id."\n------\n";
						//$post['blog_readers']=$innf['blog_readers'];
						$post['blog_location']=$innf['blog_location'];
						$post['blog_nick']=$login_lj;
						//$nick=$login_lj;
						$prom_info[$login_lj]['blog_readers']=$innf['blog_readers'];
						$prom_info[$login_lj]['blog_location']=$innf['blog_location'];
						$prom_info[$login_lj]['blog_nick']=$login_lj;
						$prom_info[$login_lj]['blog_id']=$innf['blog_id'];
						//$blog_id=$innf['blog_id'];
						if ($innf['blog_id']==$blog_id)
						{
							$beta_data['sp_pr'][$innf['blog_id']][$hn]['count']=1;
						}
						else
						{
							$beta_data['sp_pr'][$innf['blog_id']][$hn]['count']=0;	
						}
						$beta_data['sp_pr'][$innf['blog_id']][$hn]['readers']=$innf['blog_readers'];
						$beta_data['sp_pr'][$innf['blog_id']][$hn]['nick']=$login_lj;
						$beta_data['sp_pr'][$innf['blog_id']][$hn]['login']=$login_lj;
					}
					else
					{
						//$post['blog_readers']=$prom_info[$login_lj]['blog_readers'];
						$post['blog_location']=$prom_info[$login_lj]['blog_location'];
						//$nick=$prom_info[$login_lj]['blog_nick'];
						if ($innf['blog_id']==$blog_id)
						{
							$beta_data['sp_pr'][$prom_info[$login_lj]['blog_id']][$hn]['count']++;
						}
						else
						{
							if (!isset($beta_data['sp_pr'][$prom_info[$login_lj]['blog_id']][$hn]['count']))
							$beta_data['sp_pr'][$prom_info[$login_lj]['blog_id']][$hn]['count']=0;
						}
						$beta_data['sp_pr'][$prom_info[$login_lj]['blog_id']][$hn]['readers']=$prom_info[$login_lj]['blog_readers'];
						$beta_data['sp_pr'][$prom_info[$login_lj]['blog_id']][$hn]['nick']=$login_lj;
						$beta_data['sp_pr'][$prom_info[$login_lj]['blog_id']][$hn]['login']=$login_lj;
					}
					//echo $nick.' '.$post['blog_readers'].' '.$post['blog_location']."\n";
				}
				$beta_data['eng']+=$post['post_engage'];
				$beta_data['eng_time'][$hn]+=$post['post_engage'];
				if ($innf['blog_id']!=$blog_id)
				{
					$beta_data['sp_pr'][$blog_id][$hn]['count']++;
					$beta_data['sp_pr'][$blog_id][$hn]['readers']=$post['blog_readers'];
					$beta_data['sp_pr'][$blog_id][$hn]['nick']=$nick;
					$beta_data['sp_pr'][$blog_id][$hn]['login']=$login;
				}
				$beta_data['value2'][$blog_id][$hn]+=$post['blog_readers'];
				$beta_data['geo'][$wobot['destn1'][$post['blog_location']]]++;
			}
		}
		$arrword=getWords(mb_strtolower(strip_tags(html_entity_decode($post['post_content'],ENT_QUOTES,'UTF-8')),'UTF-8'));
		foreach ($arrword as $item)
		{
			//Убрать слова из ключевого запроса!!!!
			if (isset($assoc_kw[$item])||(isset($nword[$item]))) continue;
			if (mb_strlen($item,'UTF-8')>3)
			$beta_data['words'][$item]++;
		}
	}
	//print_r($beta_data['words']);
	$beta_data['count_post']=$count_post;
	$redis->set('order_'.$orderr['order_id'].'_'.$stime, json_encode($beta_data));
	//$o=$memcache->flush();
	//print_r($o);
	//$var=$redis->get('order_712_'.$stime);
	//echo $var;
}

function getWords($text)
{
	$text=preg_replace('/\s+/isu',' ',$text);
	$text=preg_replace('/([^а-яА-Яa-zA-ZёЁ])/isu',' ',$text);
	$words=preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $text, null, PREG_SPLIT_NO_EMPTY);
	return $words;
}

function get_orders_cash($order_id)
{
	global $db,$redis;
	$qorder=$db->query('SELECT order_start,order_end FROM blog_orders WHERE order_id='.$order_id.' LIMIT 1');
	$order=$db->fetch($qorder);
	$start=$order['order_start'];
	$end=($order['order_end']==0?time():$order['order_end']);
	if ($end>time())
	{
		$end=mktime(0,0,0,date('n'),date('j'),date('Y'));
	}
	for($t=$start;$t<=$end;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		$var=$redis->get('order_'.$order_id.'_'.$t);
		$m_dinams=json_decode($var,true);
		$out['count_post']+=$m_dinams['count_post'];
		$out['note_count']+=$m_dinams['notes'];
		$nastr['positive']+=$m_dinams['nastr'][1];
		$nastr['neutral']+=$m_dinams['nastr'][0];
		$nastr['negative']+=$m_dinams['nastr'][-1];
		$nastr['undefined']+=$m_dinams['nastr'][2];
		$not_process+=$m_dinams['not_process'];
		$din_post[$t]=$m_dinams['count_post'];
		unset($day_mhn);
		foreach ($m_dinams['src'] as $key => $item)
		{
			$mhn[$key]=1;
			$day_mhn[$key]=1;
		}
		$din_hn[$t]=count($day_mhn);
		$day_value=0;
		foreach ($m_dinams['sp_pr'] as $key => $item)
		{
			foreach ($item as $k => $i)
			{
				if (!isset($yet_prom[$key]))
				{
					$value+=$i['readers'];
					$day_value+=$i['readers'];
				}
				$yet_prom[$key]=1;
			}
		}
		$din_value[$t]=$day_value;
	}
	$out['not_process']=$out['count_post']-$not_process;
	$out['nastr']=$nastr;
	$out['count_src']=count($mhn);
	$out['value']=$value;
	$out['din_posts']=intval(($din_post[$end]-$din_post[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))])/$din_post[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))]*100);
	$out['din_hn']=intval(($din_hn[$end]-$din_hn[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))])/$din_hn[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))]*100);
	$out['din_value']=intval(($din_value[$end]-$din_value[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))])/$din_value[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))]*100);
	$mval[]=$din_post[mktime(0,0,0,date('n',$end),date('j',$end)-4,date('Y',$end))];
	$mval[]=$din_post[mktime(0,0,0,date('n',$end),date('j',$end)-3,date('Y',$end))];
	$mval[]=$din_post[mktime(0,0,0,date('n',$end),date('j',$end)-2,date('Y',$end))];
	$mval[]=$din_post[mktime(0,0,0,date('n',$end),date('j',$end)-1,date('Y',$end))];
	$mval[]=$din_post[mktime(0,0,0,date('n',$end),date('j',$end),date('Y',$end))];
	get_main_img($mval,$order_id,'main_2');
	$redis->set('orders_'.$order_id, json_encode($out));
	//print_r($din_post);
	//print_r($din_hn);
	//print_r($din_value);
	//print_r($out);
}

function get_main_img($mval,$order_id,$type)
{
	$max=0;
	foreach ($mval as $item)
	{
		if ($item>$max)
		{
			$max=$item;
		}
	}
	define("ALIAS_K",3);
	define("IMAGE_WIDTH",54);
	define("IMAGE_HEIGHT",24);
	define("MAX_LINE_WIDTH",10);
	define("COLOR_DEVIATION",18);
	$img = imagecreate(IMAGE_WIDTH*ALIAS_K,IMAGE_HEIGHT*ALIAS_K);
	//imageantialias($img,true);
	$lr = $lg = $lb = 0;
	while($p < IMAGE_WIDTH*ALIAS_K) {
	//$linecolor = imagecolorallocate($img,$cr = cmax(ncolor($lr)),$cg = cmax(ncolor($lg)),$cb = cmax(ncolor($lb)));
	if ($type!='main_2')
	{
		$linecolor = imagecolorallocate($img,237,237,237);
	}
	else
	{
		//echo $type.' ';
		$linecolor = imagecolorallocate($img,249,251,254);
	}
	$linecolor2 = imagecolorallocate($img,0,0,0);
	$linecolor1 = imagecolorallocate($img,88,167,183);
	//$linewidth = rand(50,MAX_LINE_WIDTH);
	$linewidth = 10;
	$nx=6;
	//$max=11000;
	$my=24/($max*1.3);
	//foreach ($mval as $key => $item)
	for ($i=0;$i<count($mval)-1;$i++)
	{
		//echo $mval[$i]*$my.'|';
		imageBoldLine($img,($nx+$i*10)*ALIAS_K,(22-$mval[$i]*$my)*ALIAS_K,($nx+($i+1)*10)*ALIAS_K,(22-$mval[$i+1]*$my)*ALIAS_K,$linecolor1,2);
		$prev=22-intval($my*$item);
	}
	imagedashedline($img,3*ALIAS_K,23*ALIAS_K,51*ALIAS_K,23*ALIAS_K,$linecolor1);
	//imagefilledrectangle($img,$p,0,$p+$linewidth,IMAGE_HEIGHT,$linecolor);
	//imageline($img,0,239,90,239,$linecolor1);
	$p = $p + $linewidth;
	$lr = $cr;
	$lg = $cg;
	$lb = $cb;
	}
	//$fp = fopen('/var/www/beta/img/graph/'.$order_id.'.png', 'w');
	$imd=imagecreatetruecolor(IMAGE_WIDTH,IMAGE_HEIGHT);
	imagecopyresampled($imd,$img,0,0,0,0,IMAGE_WIDTH,IMAGE_HEIGHT,IMAGE_WIDTH*ALIAS_K,IMAGE_HEIGHT*ALIAS_K);
	imagepng($imd,'/var/www/production/img/graph/'.$order_id.'_'.$type.'.png');
}

function imageBoldLine($resource, $x1, $y1, $x2, $y2, $Color, $BoldNess=2, $func='imageLine') 
{ 
 $center = round($BoldNess/2); 
 for($i=0;$i<$BoldNess;$i++) 
 {  
  $a = $center-$i; if($a<0){$a -= $a;} 
  for($j=0;$j<$BoldNess;$j++) 
  { 
   $b = $center-$j; if($b<0){$b -= $b;} 
   $c = sqrt($a*$a + $b*$b); 
   if($c<=$BoldNess) 
   { 
    $func($resource, $x1 +$i, $y1+$j, $x2 +$i, $y2+$j, $Color); 
   } 
  } 
 }         
}

function refresh_memcash($order_id,$start,$end)
{
	global $redis;
	$var=$redis->get('order_caches');
	$m_dinams=json_decode($var,true);
	print_r($m_dinams);
	foreach ($m_dinams as $key => $item)
	{
		if ($item<time()) unset($m_dinams[$key]);
		$mord=explode('_',$key);
		if ((($start>$key[1]) && ($start<$key[2])) || (($end>$key[1]) && ($end<$key[2])))
		{
			get_order_cash($row,date('d.m.Y',$key[1]),date('d.m.Y',$key[2]));
		}
	}
}
?>