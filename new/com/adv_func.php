<?

require_once( '/var/www/new/com/phpmorphy/src/common.php');

function query_maker($post,$update_value,$type)
{
	global $wobot,$user;
	$dir = '/var/www/new/com/phpmorphy/dicts';
	$lang = 'ru_RU';
	$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
	$morphy = new phpMorphy($dir, $lang, $opts);
	foreach ($post as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
		if (($key=='location') && ($item!='')) $loc=explode(',', $item);
		if (($key=='cou') && ($item!=''))
		{
			$mcou=explode(',', $item);
			foreach ($mcou as $kmcou => $imcou)
			{
				foreach ($wobot['destn3'] as $kdest => $idest)
				{
					if ($imcou==$idest)	$loc[]=$kdest;
				}
			}
		}
		if (($key=='res') && ($item!='')) $resorrr=explode(',', $item);
		if (($key=='shres') && ($item!='')) $short_resorrr=explode(',', $item);
		if ((substr($key, 0, 4)=='loc_'))
		{
			if (isset($wobot['destn2'][str_replace('_',' ',substr($key, 4))]))
			{
				$loc[]=str_replace('_',' ',substr($key,4));
			}
			if (substr($key, 4)=='не_определено')
			{
				$loc[]='na';
			}
		}
		if ((substr($key, 0, 4)=='tag_'))
		{
			if (!preg_match('/tag\_\d+/isu', $key)) continue;
			//$tags[]=str_replace("_",".",substr($key,5));
			$tags[]=intval(substr($key,4));
			//echo $key;
		}
		if ((substr($key, 0, 3)=='mw_'))
		{
			$addjoin=' LEFT JOIN blog_full_com f ON p.post_id=f.ful_com_post_id ';
			$addjoin2=' AND p.post_id=f.ful_com_post_id';
			$word[]=str_replace("_",".",substr($key,3));
		}
		if ((substr($key, 0, 5)=='word_'))
		{
			$addjoin=' LEFT JOIN blog_full_com f ON p.post_id=f.ful_com_post_id ';
			$addjoin2=' AND p.post_id=f.ful_com_post_id';
			$word[]=str_replace("_",".",substr($key,5));
			$lem_words=$morphy->getAllFormsWithGramInfo(mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8'), true);
			// $fp = fopen('logquery.txt', 'a');
			// fwrite($fp, mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8').' '.json_encode($lem_words)."\n");
			// fclose($fp);
			foreach ($lem_words[0]['forms'] as $item_lem_words)
			{
				$word[]=mb_strtolower($item_lem_words,'UTF-8');
			}
			$word[]=mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8');
		}
		if ((substr($key, 0, 4)=='mew_'))
		{
			$addjoin=' LEFT JOIN blog_full_com f ON p.post_id=f.ful_com_post_id ';
			$addjoin2=' AND p.post_id=f.ful_com_post_id';
			$eword[]=str_replace("_",".",substr($key,4));
		}
		if ((substr($key, 0, 6)=='speak_') && (substr($key,7,11)!='link'))
		{
			$speak[str_replace("_",".",substr($key,6))]=$post['speak_link_'.str_replace("_",".",substr($key,6))];
			$speakid[str_replace("_",".",substr($key,6))]=1;//$post['speak_link_'.str_replace("_",".",substr($key,6))];
		}
		if ((substr($key, 0, 5)=='prom_') && (substr($key,6,10)!='link'))
		{
			$prom[str_replace("_",".",substr($key,5))]=$post['prom_link_'.str_replace("_",".",substr($key,5))];
			$speakid[str_replace("_",".",substr($key,5))]=1;//$post['speak_link_'.str_replace("_",".",substr($key,6))];
		}
	}

	if ($post['except_id']!='') $mexcept_id=explode(',', $post['except_id']);

	if ($type=='update') $fquery='UPDATE blog_post p LEFT JOIN robot_blogs2 b ON p.blog_id=b.blog_id '.$addjoin.' SET '.$update_value.' WHERE ';
	elseif ($type=='select') $fquery='SELECT p.post_id FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id '.($addjoin!=''?'LEFT JOIN blog_full_com as f ON p.post_id=f.ful_com_post_id':'').' WHERE ';
	$qw=$fquery.' p.order_id='.$post['order_id'].' AND p.post_time>='.strtotime($post['stime']).' AND p.post_time<'.(mktime(0,0,0,date('n',strtotime($post['etime'])),date('j',strtotime($post['etime']))+1,date('Y',strtotime($post['etime'])))).' ';

	if ($post['positive']=='true')
	{
		$mton[]=1;
	}
	if ($post['negative']=='true')
	{
		$mton[]=-1;		
	}
	if ($post['neutral']=='true')
	{
		$mton[]=0;		
	}
	if ($post['undefined']=='true')
	{
		$mton[]=2;		
	}
	$or='';
	if (count($mton)!=0)
	{
		$qw.='AND (';
		foreach ($mton as $item)
		{
			$qw.=$or.'p.post_nastr='.$item;
			$or=' or ';
		}
		$qw.=')';
	}
	if (strlen($wh1)) $wh.='('.$wh1.')';
	$or='';
	// if (count($resorrr)!=0)
	// {
	// 	$or='';
	// 	$wh1=' AND (';
	// 	foreach ($resorrr as $item)
	// 	{
	// 		$wh1.=$or.' p.post_host=\''.$item.'\'';
	// 		$or=' OR ';
	// 	} 
	// 	$wh1.=')';
	// 	$qw.=$wh1;
	// }
	if (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($post['hosts']=='selected'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host=\''.$item.'\'';
			$or=' OR ';
		} 
		foreach ($short_resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host LIKE \''.$item.'%\'';
			$or=' OR ';
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	elseif (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($post['hosts']=='except'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host!=\''.$item.'\'';
			$or=' AND ';
		} 
		foreach ($short_resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host NOT LIKE \''.$item.'%\'';
			$or=' AND ';
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	if (count($loc)!=0)
	{
		$or='';
		$wh1=' AND (';
		foreach ($loc as $item)
		{
			if ($item=='na')
			{
				$wh1.=$or.' b.blog_location=\'\'';
				$or=' OR ';
			}
			else
			{
				if (isset($wobot['destn2'][$item]))
				{
					$wh1.=$or.' b.blog_location=\''.$wobot['destn2'][$item].'\'';
					$or=' OR ';
				}
			}
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	$or='';
	if (count($tags)!=0)
	{
		if ($post['tags']!='all')
		{
			$wh1.=' AND (';
			foreach ($tags as $item)
			{	if ($post['tags']=='selected')
				{
					$wh1.=$or.'(FIND_IN_SET(\''.$item.'\',p.post_tag)>0)';
					$or=' OR ';
				}
				else
				if ($post['tags']=='except')
				{
					$wh1.=$or.'(FIND_IN_SET(\''.$item.'\',p.post_tag)=0)';
					$or=' AND ';
				}
			}
			$wh1.=')';
			$qw.=$wh1;
		}
	}
	switch ($post['post_type']) {
	    case 'fav':
	        $qw.=' AND (p.post_fav=1)';
	        break;
	    case 'nospam':
        	$qw.=' AND (p.post_spam!=1)';
	        break;
	    case 'spam':
        	$qw.=' AND (p.post_spam=1)';
	        break;
	}
	if ((intval($post['post_read'])==1)&&(isset($post['post_read']))) $qw.=' AND p.post_read=1 ';
	if ((intval($post['post_read'])==0)&&(isset($post['post_read']))) $qw.=' AND p.post_read=0 ';
	if ((intval($post['post_imp'])==1)&&(isset($post['post_imp']))) $qw.=' AND (p.post_fav2=\''.$user['user_id'].'\' OR p.post_fav2 LIKE \''.$user['user_id'].',%\' OR p.post_fav2 LIKE \'%,'.$user['user_id'].'\' OR p.post_fav2 LIKE \'%,'.$user['user_id'].',%\') ';
	if ((intval($post['post_imp'])==0)&&(isset($post['post_imp']))) $qw.=' AND p.post_fav2=\'\' ';
	$or='';
	if ((count($speakid)!=0) && ($post['Promotions']!='all'))
	{
		$qw.=' AND (';
		foreach ($speakid as $key => $item)
		{
			if ($key!=0)
			{
				if ($post['Promotions']=='selected')
				{
					$qw.=$or.'(b.blog_id=\''.$key.'\')';
					$or=' OR ';
				}
				else
				if ($post['Promotions']=='except')
				{
					$qw.=$or.'(IFNULL(b.blog_id,0)!=\''.$key.'\')';
					$or=' AND ';
				}
			}
		}
		$qw.=')';
	}
	$or='';
	if (count($word)!=0)
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
			$qw.=' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "'.$item.'%")';
			$or=' OR ';
		}
		$qw.=')';
	}
	$or='';
	if (count($eword)!=0)
	{
		$qw.=' AND (';
		foreach ($eword as $key => $item)
		{
			$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "'.$item.'%")';
			$qw.=' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "'.$item.'%")';
			$or=' AND ';
		}
		$qw.=')';
	}
	if ($post['gender']=='2')
	{
		$qw.=' AND b.blog_gender=2';
	}
	else
	if ($post['gender']=='1')
	{
		$qw.=' AND b.blog_gender=1';
	}
	if ($post['age_min']!=null)
	{
		$qw.=' AND b.blog_age>'.intval($post['age_min']);
	}
	if ($post['age_max']!=null)
	{
		$qw.=' AND b.blog_age<'.intval($post['age_max']);
	}
	$or='';
	$post['except_id']=preg_replace('/^(.*[^\,])\,?$/isu','$1',$post['except_id']);
	$post['byparent']=preg_replace('/^(.*[^\,])\,?$/isu','$1',$post['byparent']);
	$post['except_byparent']=preg_replace('/^(.*[^\,])\,?$/isu','$1',$post['except_byparent']);
	if (trim($post['except_id'])!='') $qw.=' AND p.post_id NOT IN ('.$post['except_id'].')';
	if (trim($post['byparent'])!='') $qw.=' AND p.parent IN ('.$post['byparent'].')';
	if (trim($post['except_byparent'])!='') $qw.=' AND p.parent NOT IN ('.$post['except_byparent'].')';
	// if (count($mexcept_id)!=0)
	// {
	// 	foreach ($mexcept_id as $item)
	// 	{
	// 		$qw.=' AND p.post_id!='.$item.' ';
	// 	}
	// }
	// echo $qw;
	// die();
	return $qw;
}

?>