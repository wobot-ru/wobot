<?

require_once('/var/www/daemon/com/porter.php');
require_once('/var/www/daemon/com/infix.php');

function val_or($part,$text)
{
	$text=' '.$text.' ';
	$word=new Lingua_Stem_Ru();
	$mpart=explode('|',$part);
	$result=0;
	foreach ($mpart as $key => $item)
	{
		if (trim($item)=='true')
		{
			$result+=1;
		}
		elseif (trim($item)=='false')
		{
			$result+=0;
		}
		elseif (preg_match('/\&/isu',$item))
		{
			$result+=val_and($item,$text);
		}
		elseif (preg_match('/\"/isu',$item))
		{
			if (preg_match('/[^a-zа-яё]'.preg_replace('/\"/is','',trim($item)).'[^a-zа-яё]/isu',$text))
			{
				$result+=1;
			}
		}
		else
		{
			$rgtxt='';
			$prob='';
			$words=explode(' ',trim($item));
			foreach ($words as $it)
			{
				$rgtxt.=$prob.$word->stem_word($it);
				$prob='[а-яА-ЯёЁa-zA-Z\-0-9]*?[\s\t]+';
			}
			if (preg_match('/[^a-zа-яё]'.$rgtxt.'[а-яА-ЯёЁa-zA-Z]{0,4}[^а-яa-zё]/isu',$text))
			{
				$regex='/[^a-zа-яё](?<words>'.$rgtxt.'[а-яА-Яa-zёЁA-Z]{0,4})[^a-zа-яё]/isu';
				preg_match_all($regex, $text, $out);
				//print_r($out);
				$c=0;
				foreach ($out['words'] as $it_out_words)
				{
					foreach ($words as $it_words)
					{
						if (($word->stem_word($it_words)==$word->stem_word($it_out_words)) || ($item==$it_out_words))
						{
							$c=1;
						}
						//echo $word->stem_word($it_words).' '.$word->stem_word($it_out_words).' '.$c."!\n"; 
					}
				}
				$result+=($c==1?1:0);
				//$result+=1;
			}
			//echo $result;
		}
	}
	//echo "\n".'RESULT='.$result."\n";
	//echo $result;
	if ($result!=0)
	{
		$result/=$result;
	}
	return $result;
}

function val_and($part,$text)
{
	$word=new Lingua_Stem_Ru();
	$text=' '.$text.' ';
	$part=preg_replace('/[\&]+/isu','&',$part);
	$mpart=explode('&',$part);
	//print_r($mpart);
	$result=1;
	foreach ($mpart as $key => $item)
	{
		$item=trim($item);
		if ($item=='true')
		{}
		elseif ($item=='false')
		{
			$result=0;
		}
		elseif (preg_match('/\"/isu',$item))
		{
			if (!preg_match('/[^a-zа-яё]'.preg_replace('/\"/isu','',$item).'[^a-zа-яё]/isu',$text))
			{
				$result=0;
			}
		}
		else
		{
			$rgtxt='';
			$prob='';
			$words=explode(' ',trim($item));
			foreach ($words as $it)
			{
				$rgtxt.=$prob.$word->stem_word($it);
				$prob='[а-яА-Яa-zёЁA-Z\-0-9]*?[\s\t]+';
			}
			if (!preg_match('/[^a-zа-яё]'.$rgtxt.'[а-яА-Яa-zёЁA-Z]{0,4}[^a-zа-яё]/isu',$text))
			{
				//echo '/[^a-zа-яё]'.$rgtxt.'[а-яА-Яa-zёЁA-Z]{0,4}[^a-zа-яё]/isu'."\n";
				$result=0;
			}
			else
			{
				$regex='/[^a-zа-яё](?<words>'.$rgtxt.'[а-яА-Яa-zёЁA-Z]{0,4})[^a-zа-яё]/isu';
				//echo 'regex='.$regex."\n";
				preg_match_all($regex, $text, $out);
				//print_r($out);
				$c=0;
				foreach ($out['words'] as $it_out_words)
				{
					foreach ($words as $it_words)
					{
						if ($word->stem_word($it_words)==$word->stem_word($it_out_words)) $c=1;
						//echo $word->stem_word($it_words).' '.$word->stem_word($it_out_words).' '.$c."!\n"; 
					}
				}
				if ($result==1)	$result=($c==1?1:0);
			}
		}
	}
	if ($result!=0)
	{
		$result/=$result;
	}
	return $result;
}

function val_not($keyword,$text)
{
	$word=new Lingua_Stem_Ru();
	$regex='/\~+\s*?\((?<words>[^(]*?)\)/isu';
	preg_match_all($regex,$keyword,$out);
	// print_r($out);
	$result=0;
	foreach ($out['words'] as $k => $i)
	{
		$mwords=explode('|',$i);
		//print_r($mwords);
		foreach ($mwords as $item)
		{
			$item=$word->stem_word($item);
			$keyword=preg_replace('/'.preg_replace('/([\&\|\"\(\)~)])/isu','\\\\$1',$out[0][$k]).'/isu','',$keyword);
			if (preg_match('/\b'.preg_replace('/\"/isu','',trim($item)).'/isu',$text))
			{
				$result=1;
			}
		}
	}
	$regex='/\~+[\s\"]*(?<words>[а-яёa-z0-9\-\s\.\/]+)[\s\"]*/isu';
	preg_match_all($regex,$keyword,$out);
	// print_r($out);
	foreach ($out['words'] as $key => $item)
	{
		$item=$word->stem_word($item);
		if (trim($item)=='') continue;
		$keyword=preg_replace('/'.preg_replace('/([\&\|\"\(\)~)\/])/isu','\\\\$1',$out[0][$key]).'/isu','',$keyword);
		if (preg_match('/\b'.preg_replace('/\"/isu','',trim($item)).'/isu',$text))
		{
			$result=1;
		}
	}
	$out1['result']=$result;
	$out1['kw']=$keyword;
	//print_r($out1);
	return $out1;
}

function check_post2($text,$keyword)
{
	$replace[0]='false';
	$replace[1]='true';
	$val=val_not($keyword,$text);
	if ($val['result']==1)
	{
		return 0;
	}
	else
	{
		$keyword=trim($val['kw']);
	}
	do
	{
		if ((preg_match('/\(/isu',$keyword)) && (preg_match('/\)/isu',$keyword)))
		{
			$regex='/\([\s\t]*?(?<word>[^(]*?)[\s\t]*?\)/is';
			preg_match_all($regex,$keyword,$out);
			foreach ($out['word'] as $key => $item)
			{
				$keyword=preg_replace('/\(\s*'.preg_replace('/([\&\|])/isu','\\\\$1',$item).'\s*\)/isu',$replace[val_or($item,$text)],$keyword);
			}
		}
		else
		{
			$result=$replace[val_or($keyword,$text)];
			$keyword='';//preg_replace('/'.preg_replace('/([\&\|])/isu','\\\\$1',$keyword).'/isu',$replace[val_or($keyword,$text)],$keyword);
		}
	}
	while ($keyword!='');
	if ($result=='true')
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

function check_local($text,$lan)
{
	$arr_part=array('возле','вокруг','вдоль','поперек','близ','из','с','со','из-за','из-под','около','от','у','в','на','к','по','перед','под','над','за','между','после','до','накануне','среди','по','к','через','за','при','ради','ввиду','вследствие','из','от','благодаря','согласно','для','ради','к','по','на','про','о','без','кроме','против','вместо','вопреки','при','а','но','да','и');
	$ot['ru']['regex']='/(?<bukv>[а-я])/isu';
	$ot['en']['regex']='/(?<bukv>[a-z])/isu';
	$regex_rab='/(?<rab>[\.\/\?\:\=\+\'\"\!\?\@\#\$\%\^\&\*\(\)0-9])/isu';
	preg_match_all($regex_rab,$text,$out_rab);
	$count_rab=count($out_rab['rab']);
	$count_=count(explode(' ',$text))-1;
	foreach ($ot as $key => $item)
	{
		preg_match_all($ot[$key]['regex'],$text,$out);
		$ot[$key]['count']=count($out['bukv']);
		$ot[$key]['proc']=count($out['bukv'])/(mb_strlen($text,'UTF-8')-$count_-$count_rab);
		if (count($out['bukv'])>$max_count)
		{
			$max_count=count($out['bukv']);
			$calc_lan=$key;
		}
	}
	if (($lan=='ru')&&($calc_lan!=$lan))
	{
		$text=mb_strtolower($text,'UTF-8');
		$mtext=preg_split('/[^а-яёa-z\-]/isu', $text);
		// print_r($mtext);
		foreach ($mtext as $item_mtext)
		{
			if (in_array($item_mtext, $arr_part)) return 1;
		}
	}
	if (($lan=='en') && (preg_match('/[а-я]/isu',$text)))
	{
		return 0;
	}
	if ($calc_lan==$lan)
	{
		return 1;
	}
	else
	{
		return 0;
	}
	//print_R($ot);
	//echo $count_.' '.count($outru['bukv']).' '.count($outen['bukv']);
}

function check_filters($link,$filters,$global_user=null)
{
	global $wobot;
	//echo '!'.$link.'!'."\n";
	if ($global_user==null)
	{
		$user=new users();
		$bb=$user->get_url($link,1);
	}
	else
	{
		$bb=$global_user;
	}
	$is_true=1;
	$filt=0;
	$outmas['value']=2;
	$outmas['rb_info']=$bb;
	// print_r($filters);
	// print_r($bb);
	if ((count($filters['loc'])!=0) && ($is_true==1))
	{
		// echo '-1-';
		if (($bb['last_update']==0) && ($bb['blog_id']!=0)) return $outmas;
		$filt=1;
		foreach ($filters['loc'] as $item)
		{
			$ml=explode('_',$item);
			$mloc[]=$ml[1];
		}
		// print_r($mloc);
		if ((!in_array($wobot['destn1'][$bb['loc']],$mloc)) && ($filters['loc_type']=='only')) return 0;
		if ((in_array($wobot['destn1'][$bb['loc']],$mloc)) && ($filters['loc_type']=='except')) return 0;
	}
	// echo '!1!'."\n";
	if ((count($filters['author_id'])!=0) && ($is_true==1))
	{
		// echo '-2-';
		if (($bb['last_update']==0) && ($bb['blog_id']!=0)) return $outmas;
		$filt=1;
		foreach ($filters['author_id'] as $item)
		{
			$mauth[]=$item;
		}
		//print_r($mauth);
		if ((!in_array($bb['blog_id'],$mauth)) && ($filters['author_type']=='only')) return 0;
		if ((in_array($bb['blog_id'],$mauth)) && ($filters['author_type']=='except')) return 0;
	}
	// echo '!2!'."\n";
	if ((count($filters['res'])!=0) && ($is_true==1) && ($filters['res'][0]!=''))
	{
		// echo '-3-';
		if (($bb['last_update']==0) && ($bb['blog_id']!=0)) return $outmas;
		$filt=1;
		$hn='';
		$hn=parse_url($link);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		if ($hh=='.') return 0;
		foreach ($filters['res'] as $item)
		{
			$hn1='';
			$hn1=parse_url($item);
			$hn1=($hn1['host']==''?$hn1['path']:$hn1['host']);
		    $ahn1=explode('.',$hn1);
		    $hn1 = $ahn1[count($ahn1)-2].'.'.$ahn1[count($ahn1)-1];
			$hh1 = $ahn1[count($ahn1)-2];
			$msrc[]=$hn1;
		}
		// print_r($msrc);
		if ((!in_array($hn,$msrc)) && ($filters['res_type']=='only')) return 0;
		if ((in_array($hn,$msrc)) && ($filters['res_type']=='except')) return 0;
	}
	// echo '!3!'."\n";
	if (($filters['from_age']!=0) || ($filters['to_age']!=0))
	{
		// echo '-4-';
		if (($bb['last_update']==0) && ($bb['blog_id']!=0)) return $outmas;
		$filt=1;
		if ($bb['age']<$filters['from_age']) return 0;
		if ($bb['age']>$filters['to_age']) return 0;
	}
	// echo '!4!'."\n";
	if ($filters['gender']!=0)
	{
		// echo '-5-';
		if (($bb['last_update']==0) && ($bb['blog_id']!=0)) return $outmas;
		$filt=1;
		if ($bb['gender']!=$filters['gender']) return 0;
	}
	// echo '!5!'."\n";
	// echo "\n".$filt.' '.$bb['blog_id'].'|'."\n";
	// echo '-6-';
	if (($filt==1) && ($bb['blog_id']==0)) return 0;
	// echo '-7-';
	//echo $link."\n";
	$outmas['value']=1;
	$outmas['rb_info']=$bb;
	return $outmas;
}

function checker_links($text) // проверяет наличие ссылки в тексте
{
	preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', strip_tags($text), $match);
	if (count($match[0])==0)
	{
		if (preg_match('/\bbit\.ly\/[a-z0-9]*\b/isu', strip_tags($text))) return true;
		else return false;
	}
	else
	{
		return true;
	}
}

function post_slice2($m1)
{
	global $db,$blog,$debug_mode,$text_lang,$memcache,$engage_src,$mstart,$mend;
	// $memcache->set('order_'.$order['order_id'].'_null_null', json_encode($out), MEMCACHE_COMPRESSED, 86400);
	// echo intval($memcache->add('isset_'.$blog['order_id'].'_'.$item, '1', MEMCACHE_COMPRESSED, 86400));
	// print_r($blog);
	echo "\n".'SLICE...'."\n";
	foreach ($m1['link'] as $key => $item)
	{
		if (($blog['order_analytics']==1) && (($m1['time'][$key]<$mstart) || ($m1['time'][$key]>=$mend)))
		{
			continue;
		}
		if (($blog['order_analytics']==0) && (($m1['time'][$key]<$blog['order_start']) || ($m1['time'][$key]>=(($blog['order_end'])==0?time():($blog['order_end']+86400)))))
		{
			//echo 'continue'."\n";
			continue;
		}
		// echo $item.' '.$m1['content'][$key].' '.mysql_num_rows($qw).' '.check_local($m1['content'][$key],$text_lang).' '.check_post($m1['content'][$key],$blog['order_keyword'])."\n";
		//continue;
		// if (($debug_mode!='debug') && isset($rep[$item])) continue;
		// $isset_post=intval($memcache->add('isset_'.$blog['order_id'].'_'.$item, '1', MEMCACHE_COMPRESSED, 86400));
		// // echo $isset_post;
		// if ($isset_post==0) continue;
		// if ($isset_post==1)
		// {
		// 	$qw=$db->query('SELECT post_id FROM blog_post WHERE order_id='.$blog['order_id'].' AND post_link=\''.addslashes($item).'\' LIMIT 1');
		// 	if (mysql_num_rows($qw)!=0) continue;
		// }
		$add_text='';
		if ($debug_mode!='debug')
		{
			if ((check_post(strip_tags($m1['content'][$key]),$blog['order_keyword'])==0) && (check_post(strip_tags($m1['fulltext'][$key]),$blog['order_keyword'])==0)) continue;
			if (check_local($m1['content'][$key],$text_lang)==0) continue;
		}
		else
		{
			if ($m1['flag'][$key]!='ya')
			{
				if (isset($rep[$item])) $add_text.=' REP';
				// if (mysql_num_rows($qw)!=0) continue;
				if (check_post($m1['content'][$key],$blog['order_keyword'])==0) $add_text.=' CHP';
				if (check_local($m1['content'][$key],$text_lang)==0) $add_text.=' LOC';
			}
			else
			{
				$add_text.=' YA';
				if (isset($rep[$item])) $add_text.=' REP';
				if ($text_lang=='en') continue;
				// if (mysql_num_rows($qw)!=0) continue;
				if (check_local($m1['content'][$key],$text_lang)==0) $add_text.=' LOC';
			}
		}
		echo $key.$m1['flag'][$key].' ';
		$rep[$item]=1;
		$check_filt=check_filters($item,$filters);
		$hn=parse_url($item);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		if ($engage_src[$hn])
		{
			$mas_eng=json_decode($m1['engage'][$key],true);
			$engage_val=intval($mas_eng['likes'])+intval($mas_eng['comment'])+intval($mas_eng['retweet']);
		}
		if ($check_filt['value']==1)
		{
			$bb1=$check_filt['rb_info'];
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			$m2['content'][$key]=$m1['content'][$key];
			if ($hn=='google.com')
			{
				$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
				if ($db->num_rows($quser)==0)
				{
					$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($quser);
				}
			}
			if ($hn=='twitter.com')
			{
				$m2['content'][$key]=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$m2['content'][$key]);
			}
			$isspam=0;
			if (isset($spams[$bb1['blog_id']]))
			{
				$isspam=1;
			}
			$cch++;
			if ($debug_mode=='debug') $m2['content'][$key]=trim($add_text).' '.$m2['content'][$key];
			if ($m1['time'][$key]<$cstart) $cstart=mktime(0,0,0,date('n',$m1['time'][$key]),date('j',$m1['time'][$key]),date('Y',$m1['time'][$key]));
			//echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':'').'blog_id'.((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':'').intval($bb1['blog_id']).((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')'."\n";
			$db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id'.((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.$m1['engage'][$key].'\',':'')).intval($bb1['blog_id']).((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')');
		}
		elseif ($check_filt['value']==2)
		{
			$bb1=$check_filt['rb_info'];
			$isspam=0;
			if (isset($spams[$bb1['blog_id']]))
			{
				$isspam=1;
			}
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			if ($hn=='google.com')
			{
				$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
				if ($db->num_rows($quser)==0)
				{
					$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($quser);
				}
			}
			//echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':'').'blog_id'.(($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':'').intval($bb1['blog_id']).(($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')'."\n";
			$db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id'.(($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.$m1['engage'][$key].'\',':'')).intval($bb1['blog_id']).(($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')');
		}
	}
	unset($m1);
	return $m1;
}

function post_slice($m1)
{
	global $db,$blog,$debug_mode,$text_lang,$memcache,$engage_src,$mstart,$mend,$tariff,$redis,$temp_orderkw;
	// $memcache->set('order_'.$order['order_id'].'_null_null', json_encode($out), MEMCACHE_COMPRESSED, 86400);
	// echo intval($memcache->add('isset_'.$blog['order_id'].'_'.$item, '1', MEMCACHE_COMPRESSED, 86400));
	// print_r($blog);
	echo "\n".'SLICE...'."\n";
	foreach ($m1['link'] as $key => $item)
	{
		// echo ')';
		if (($blog['order_analytics']==1) && (($m1['time'][$key]<$mstart) || ($m1['time'][$key]>=$mend))) continue;
		if (($blog['order_analytics']==0) && (($m1['time'][$key]<$blog['order_start']) || ($m1['time'][$key]>=(($blog['order_end'])==0?time():($blog['order_end']+86400))))) continue;

		$add_text='';
		if ($debug_mode!='debug')
		{
			if ((check_post(strip_tags($m1['content'][$key]),$blog['order_keyword'])==0) && (check_post(strip_tags($m1['fulltext'][$key]),$blog['order_keyword'])==0)) continue;
			if (check_local($m1['content'][$key],$text_lang)==0) continue;
		}
		else
		{
			if ($m1['flag'][$key]!='ya')
			{
				if (isset($rep[$item])) $add_text.=' REP';
				// if (mysql_num_rows($qw)!=0) continue;
				if (check_post($m1['content'][$key],$blog['order_keyword'])==0) $add_text.=' CHP';
				if (check_local($m1['content'][$key],$text_lang)==0) $add_text.=' LOC';
			}
			else
			{
				$add_text.=' YA';
				if (isset($rep[$item])) $add_text.=' REP';
				if ($text_lang=='en') continue;
				// if (mysql_num_rows($qw)!=0) continue;
				if (check_local($m1['content'][$key],$text_lang)==0) $add_text.=' LOC';
			}
		}
		echo $key.$m1['flag'][$key].' ';
		$rep[$item]=1;
		$check_filt=check_filters($item,$filters);
		$hn=parse_url($item);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		if ($engage_src[$hn])
		{
			$mas_eng=json_decode($m1['engage'][$key],true);
			$engage_val=intval($mas_eng['likes'])+intval($mas_eng['comment'])+intval($mas_eng['retweet']);
		}
		if ($check_filt['value']==1)
		{
			$bb1=$check_filt['rb_info'];
			// print_r($bb1);
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			$m2['content'][$key]=$m1['content'][$key];
			if ($hn=='google.com')
			{
				$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
				if ($db->num_rows($quser)==0)
				{
					$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($quser);
				}
			}
			if ($hn=='twitter.com')	$m2['content'][$key]=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$m2['content'][$key]);
			$isspam=0;
			if (isset($spams[$bb1['blog_id']])) $isspam=1;
			$cch++;
			if ($debug_mode=='debug') $m2['content'][$key]=trim($add_text).' '.$m2['content'][$key];
			if ($m1['time'][$key]<$cstart) $cstart=mktime(0,0,0,date('n',$m1['time'][$key]),date('j',$m1['time'][$key]),date('Y',$m1['time'][$key]));

			unset($queue_item);
			$queue_item['order_id']=$blog['order_id'];
			$queue_item['post_link']=$item;
			$queue_item['post_host']=$hn;
			$queue_item['post_time']=$m1['time'][$key];
			$queue_item['post_content']=$m2['content'][$key];
			$queue_item['post_engage']=($engage_src[$hn]!=1?0:($m1['engage'][$key]!=''?$engage_val:-1));
			$queue_item['post_advengage']=($engage_src[$hn]!=1?'':json_decode($m1['engage'][$key],true));
			$queue_item['blog_id']=intval($bb1['blog_id']);
			$queue_item['post_spam']=$isspam;
			$queue_item['post_ful_com']=$m1['fulltext'][$key];
			$queue_item['blog_location']=$check_filt['rb_info']['loc'];
			$queue_item['blog_last_update']=$check_filt['rb_info']['last_update'];
			$queue_item['blog_age']=$check_filt['rb_info']['age'];
			$queue_item['blog_gender']=$check_filt['rb_info']['gender'];
			$queue_item['order_keyword']=$temp_orderkw;
			$queue_item['order_name']=$blog['order_name'];
			$queue_item['order_start']=$blog['order_start'];
			$queue_item['order_end']=$blog['order_end'];
			$queue_item['order_settings']=$blog['order_settings'];
			$queue_item['user_id']=$blog['user_id'];
			$queue_item['tariff_id']=$tariff['tariff_id'];
			$queue_item['ut_date']=$tariff['ut_date'];
			$queue_item['ut_id']=$tariff['ut_id'];
			// print_r($queue_item);
			$redis->sAdd('prev_queue',json_encode($queue_item));
			//echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':'').'blog_id'.((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':'').intval($bb1['blog_id']).((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')'."\n";
			// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id'.((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.$m1['engage'][$key].'\',':'')).intval($bb1['blog_id']).((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')');
		}
		elseif ($check_filt['value']==2)
		{
			$bb1=$check_filt['rb_info'];
			print_r($bb1);
			$isspam=0;
			if (isset($spams[$bb1['blog_id']]))
			{
				$isspam=1;
			}
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			if ($hn=='google.com')
			{
				$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
				if ($db->num_rows($quser)==0)
				{
					$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($quser);
				}
			}
			unset($queue_item);
			$queue_item['order_id']=$blog['order_id'];
			$queue_item['post_link']=$item;
			$queue_item['post_host']=$hn;
			$queue_item['post_time']=$m1['time'][$key];
			$queue_item['post_content']=$m2['content'][$key];
			$queue_item['post_engage']=($engage_src[$hn]!=1?0:($m1['engage'][$key]!=''?$engage_val:-1));
			$queue_item['post_advengage']=($engage_src[$hn]!=1?'':json_decode($m1['engage'][$key],true));
			$queue_item['blog_id']=intval($bb1['blog_id']);
			$queue_item['post_spam']=$isspam;
			$queue_item['post_ful_com']=$m1['fulltext'][$key];
			$queue_item['blog_location']=$check_filt['rb_info']['loc'];
			$queue_item['blog_last_update']=$check_filt['rb_info']['last_update'];
			$queue_item['blog_age']=$check_filt['rb_info']['age'];
			$queue_item['blog_gender']=$check_filt['rb_info']['gender'];
			$queue_item['order_keyword']=$temp_orderkw;
			$queue_item['order_name']=$blog['order_name'];
			$queue_item['order_start']=$blog['order_start'];
			$queue_item['order_end']=$blog['order_end'];
			$queue_item['order_settings']=$blog['order_settings'];
			$queue_item['user_id']=$blog['user_id'];
			$queue_item['tariff_id']=$tariff['tariff_id'];
			$queue_item['ut_date']=$tariff['ut_date'];
			$queue_item['ut_id']=$tariff['ut_id'];

			print_r($queue_item);
			$redis->sAdd('prev_queue',json_encode($queue_item));
			//echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':'').'blog_id'.(($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':'').intval($bb1['blog_id']).(($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')'."\n";
			// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id'.(($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.$m1['engage'][$key].'\',':'')).intval($bb1['blog_id']).(($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')');
		}
	}
	unset($m1);
	return $m1;
}

function check_bracket($query)
{
	// echo $query."\n";
	do
	{
		$mcount_open_before=count(explode('(', $query))-1;
		$mcount_close_before=count(explode(')', $query))-1;
		$query_before=$query;
		$query=preg_replace('/\([^\(]*?\)/isu','',$query);
		// echo $query."\n";
		$mcount_open_after=count(explode('(', $query))-1;
		$mcount_close_after=count(explode(')', $query))-1;
		if (($mcount_close_after==0)&&($mcount_open_after==0)) break;
		if (($mcount_open_before==$mcount_open_after)&&($mcount_close_before==$mcount_close_after))
		{
			$mcount_open_after=-1;
			$mcount_close_after=-1;
		}
		if ($query_before==$query) break;
	}
	while (($mcount_open_after+$mcount_close_after)>=2);
		// echo $mcount_close_after.' '.$mcount_open_after.'!';
	if (($mcount_close_after==0)&&($mcount_open_after==0)) return 1;
	else return 0;
}

//$word=new Lingua_Stem_Ru();
//echo $word->stem_word('парковки');
//echo check_local('@Sobyanin а это где?)','en');
//val_or('владимир путин | собянин владимир && "прохоров"','владимиром путиным медведев собянина владимира и прохоров молодцы');
//echo check_post('Молодежка «Металлурга» второй контрольный матч сыграла вничью','Тиньков|Тинькоф|Тинькофф|Тиньковв|Тинков|Тинкоф|Тинкофф|ТКС|Тинькоффф|Tinkov|Tinkof|Tinkoff|"Tin’kov"|"Tin’koff"|TCS Bank|TCS-Bank|tcsbank');

?>