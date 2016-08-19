<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
//require_once('/var/www/new/com/porter.php');
require_once('/var/www/daemon-dev/fsearch3/ch.php');

//$word=new Lingua_Stem_Ru();
//$msg=$word->stem_word('белоцкий');
//  путин | медведев && (( прохоров & жириновский | зюганов) | миронов)
//  путин | медведев && (//1 | миронов)
//  путин | медведев && //2
/*function check_post($post,$keyword) 
{
	global $word;
	preg_match_all('/\~(?<word>[^\~]*)/isu',$keyword,$mnot);
	foreach ($mnot['word'] as $key => $item)
	{
		if (preg_match('/'.trim($item).'/isu',$post))
		{
			return 'NO';
		}
		$keyword=preg_replace('/'.$mnot[0][$key].'/is','',$keyword);
	}
	$post=preg_replace('/([a-zA-Z\/\?])([а-яА-Я])/isu','$1 $2',$post); // удаляем ссылки
	$post=preg_replace('/([а-яА-Я])([a-zA-Z\/\?])/isu','$1 $2',$post); // удаляем ссылки
	$post=preg_replace('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#','',$post); // удаляем ссылки
	//echo $keyword."\n";
	$m_predl=explode('.',$post);// разбиение по предложениям
	//echo strpos($keyword,'(');
	$gl_sum=0;
	//echo $keyword;
	if (mb_strpos($keyword,'(',0,'UTF-8')!==false) // проверка есть скобки дальше или нету
	{
		$regex='/\((?<kw>[^()]*?)\)/is'; // ресурсивно проходимся по запросу удаляя скобки и вычисляя подходит ли скобка посту
		preg_match_all($regex,$keyword,$out,PREG_OFFSET_CAPTURE); // ищем самые глубокие скобки
		//print_r($out);
		//echo mb_strlen($keyword,'UTF-8').'|||';
		foreach ($out['kw'] as $mkey => $item) // проходимся по найденым скобкам, разбиваем вначале на | затем на && или &
		{
			$gl_sum=0;
			//echo '{{{}}}'.$out[0][$mkey][0]."\n";
			$m1=explode('|',$item[0]); // делим запрос на | затем на && и на &
			foreach ($m1 as $k => $i)
			{
				//echo $i.' '.$sumc.'|'.trim($i).'|'."---\n";
				if (trim($i)=='true') // если есть true или false полученные из прошлых итераций
				{
					//echo 'SUMCCC';
					$sumc=1;// sumc - либо результат предыдущей итерации, либо значени подходит пост выражению в скобках
				}
				elseif (trim($i)=='false') // если есть true или false
				{
					$sumc=0;
				}
				else
				{
					if (mb_strpos($i,'&&',0,'UTF-8')!==false) // проверям что между | (мб &&,&,ничего)
					{
						//echo '+++';
						$sumc=1;
						$m2=explode('&&',$i);
						foreach ($m2 as $i1)
						{
							if (mb_strpos($i1,'true',0,'UTF-8')!==false) // проверяем результаты прошлых операций
							{
								$sumc*=1;
							}
							elseif (mb_strpos($i1,'false',0,'UTF-8')!==false) // проверяем результаты прошлых операций
							{
								$sumc*=0;
							}
							else
							{
								if (mb_strpos($i1,'"',0,'UTF-8')!==false) // если запрос в кавычках
								{
									if (mb_strpos(' '.preg_replace('/[\.\/\,\?\!]/is',' $1 ',$post).' ',' '.preg_replace('/\"/is','',trim($i1)).' ',0,'UTF-8')!==false)// не стемя проверяем есть ли словосочетание в посте
									{}
									else
									{
										//preg_match_all('/(?<data>\s'.preg_replace('/\"/is','',preg_replace('/^(\s+)(.*)(\s+)$/is','$2',$i1)).'\s)/isu',$post,$pp);
										//print_r($pp);
										echo $i1.' GGGG';
										$sumc*=0;
									}
								}
								else
								{
									$m_p_word=explode(' ',trim($i));
									if (!preg_match('/'.$word->stem_word($m_p_word[0]).($m_p_word[1]!=''?'.*?'.$word->stem_word($m_p_word[1]):'').'/isu',$post))// стемим и проверяем наличие в посте
									{
										$sumc*=0;
									}
								}
							}
						}
					}
					elseif (mb_strpos($i,'&',0,'UTF-8')!==false)
					{// если & в скобках
						$m2=explode('&',$i);
						$sumc=0;
						foreach ($m_predl as $predl)
						{
							$c=1;
							foreach ($m2 as $i1)
							{
								if (mb_strpos($i1,'true',0,'UTF-8')!==false) // -|- выше
								{
									$c*=1;
								}
								elseif (mb_strpos($i1,'false',0,'UTF-8')!==false) // -|- выше
								{
									$c*=0;
								}
								else
								{
									if (mb_strpos($i1,'"',0,'UTF-8')!==false) // -|- выше
									{
										if (mb_strpos(' '.preg_replace('/[\.\/\,\?\!]/is',' $1 ',$predl).' ',' '.preg_replace('/\"/is','',trim($i1)).' ',0,'UTF-8')!==false) // -|- выше
										{}
										else
										//if (!preg_match('/\s'.preg_replace('/\"/is','',preg_replace('/^(\s+)(.*)(\s+)$/is','$2',$i1)).'\s/isu',$predl))// + стеминг
										{
											$c*=0;
										}
									}
									else
									{
										$m_p_word=explode(' ',trim($i));
										if (!preg_match('/'.$word->stem_word($m_p_word[0]).($m_p_word[1]!=''?'.*?'.$word->stem_word($m_p_word[1]):'').'/isu',$predl)) // -|- выше
										{
											$c*=0;
										}
									}
								}
							}
							$sumc+=$c;
						}
					}
					else
					{
						//echo '#####'.$i.'?'.$word->stem_word(trim($i)).'?';
						$sumc=0;
						if (mb_strpos($i,'"',0,'UTF-8')!==false)
						{
							if (mb_strpos(' '.preg_replace('/[\.\/\,\?\!]/is',' $1 ',$post).' ',' '.preg_replace('/\"/is','',trim($i)).' ',0,'UTF-8')!==false)
							{
								$sumc=1;
							}
							else
							//if (!preg_match('/\s'.preg_replace('/\"/is','',preg_replace('/^(\s+)(.*)(\s+)$/is','$2',$i1)).'\s/isu',$predl))// + стеминг
							{
								$sumc=0;
							}
						}
						else
						{
							$m_p_word=explode(' ',trim($i));
							if (preg_match('/'.$word->stem_word($m_p_word[0]).($m_p_word[1]!=''?'.*?'.$word->stem_word($m_p_word[1]):'').'/isu',$post))
							{
								$sumc=1;
							}
						}
					}
				}
				$gl_sum+=$sumc;	
				//echo $i.' '.$gl_sum."\n";			
			}
			if ($gl_sum>0)
			{
				$val='true';
			}
			else
			{
				$val='false';
			}
			//echo (mb_strlen(substr($keyword, 0, $item[1]), 'UTF-8')-3).'!!'.$keyword.' ex: '.'/'.preg_replace('/([\|\&])/isu','\$1',addslashes($out[0][$mkey][0])).'/isu'."\n";
			//echo preg_replace('/([\(\)\&\|])/isu','\\'."\\$1",$out[0][$mkey][0]).'/isu '.$gl_sum."\n";
			$keyword=preg_replace('/'.preg_replace('/([\(\)\&\|])/isu','\\'."\\$1",$out[0][$mkey][0]).'/isu',$val,$keyword);
			//echo '}}}'.$keyword."\n";
			//echo '-----------'."\n\n\n\n";
		}
		//print_r($out);
		check_post($post,$keyword);
	}
	else
	{
		$m1=explode('|',$keyword); // делим запрос на | затем на && и на &
		foreach ($m1 as $k => $i)
		{
			//echo $i.' '.$sumc.'|'.trim($i).'|'."---\n";
			if (trim($i)=='true') // если есть true или false
			{
				//echo 'SUMCCC';
				$sumc=1;// sumc - либо результат предыдущей итерации, либо значени подходит пост выражению в скобках
			}
			elseif (trim($i)=='false') // если есть true или false
			{
				$sumc=0;
			}
			else
			{
				if (mb_strpos($i,'&&',0,'UTF-8')!==false)
				{
					//echo '+++';
					$sumc=1;
					$m2=explode('&&',$i);
					foreach ($m2 as $i1)
					{
						if (mb_strpos($i1,'true',0,'UTF-8')!==false)
						{
							$sumc*=1;
						}
						elseif (mb_strpos($i1,'false',0,'UTF-8')!==false)
						{
							$sumc*=0;
						}
						else
						{
							if (mb_strpos($i1,'"',0,'UTF-8')!==false)
							{
								if (mb_strpos(' '.preg_replace('/[\.\/\,\?\!]/is',' $1 ',$post).' ',' '.preg_replace('/\"/is','',trim($i1)).' ',0,'UTF-8')!==false)// + стеминг
								{}
								else
								{
									//preg_match_all('/(?<data>\s'.preg_replace('/\"/is','',preg_replace('/^(\s+)(.*)(\s+)$/is','$2',$i1)).'\s)/isu',$post,$pp);
									//print_r($pp);
									echo $i1.' GGGG';
									$sumc*=0;
								}
							}
							else
							{
								$m_p_word=explode(' ',trim($i));
								if (!preg_match('/'.$word->stem_word($m_p_word[0]).($m_p_word[1]!=''?'.*?'.$word->stem_word($m_p_word[1]):'').'/isu',$post))// + стеминг
								{
									$sumc*=0;
								}
							}
						}
					}
				}
				elseif (mb_strpos($i,'&',0,'UTF-8')!==false)
				{// если & в скобках
					$m2=explode('&',$i);
					$sumc=0;
					foreach ($m_predl as $predl)
					{
						$c=1;
						foreach ($m2 as $i1)
						{
							if (mb_strpos($i1,'true',0,'UTF-8')!==false)
							{
								$c*=1;
							}
							elseif (mb_strpos($i1,'false',0,'UTF-8')!==false)
							{
								$c*=0;
							}
							else
							{
								if (mb_strpos($i1,'"',0,'UTF-8')!==false)
								{
									if (mb_strpos(' '.preg_replace('/[\.\/\,\?\!]/is',' $1 ',$predl).' ',' '.preg_replace('/\"/is','',trim($i1)).' ',0,'UTF-8')!==false)
									{}
									else
									//if (!preg_match('/\s'.preg_replace('/\"/is','',preg_replace('/^(\s+)(.*)(\s+)$/is','$2',$i1)).'\s/isu',$predl))// + стеминг
									{
										$c*=0;
									}
								}
								else
								{
									$m_p_word=explode(' ',trim($i));
									if (!preg_match('/'.$word->stem_word($m_p_word[0]).($m_p_word[1]!=''?'.*?'.$word->stem_word($m_p_word[1]):'').'/isu',$predl))// + стеминг
									{
										$c*=0;
									}
								}
							}
						}
						$sumc+=$c;
					}
				}
				else
				{
					//echo '#####'.$i;
					$sumc=0;
					if (mb_strpos($i,'"',0,'UTF-8')!==false)
					{
						if (mb_strpos(' '.preg_replace('/[\.\/\,\?\!]/is',' $1 ',$post).' ',' '.preg_replace('/\"/is','',trim($i)).' ',0,'UTF-8')!==false)
						{
							$sumc=1;
						}
						else
						//if (!preg_match('/\s'.preg_replace('/\"/is','',preg_replace('/^(\s+)(.*)(\s+)$/is','$2',$i1)).'\s/isu',$predl))// + стеминг
						{
							$sumc=0;
						}
					}
					else
					{
						$m_p_word=explode(' ',trim($i));
						if (preg_match('/'.$word->stem_word($m_p_word[0]).($m_p_word[1]!=''?'.*?'.$word->stem_word($m_p_word[1]):'').'/isu',$post))
						{
							$sumc=1;
						}
					}
				}
			}
			$gl_sum+=$sumc;	
			//echo $i.' '.$gl_sum."\n";			
		}
		//echo 'GL_SUM='.$gl_sum."";
		if ($gl_sum>0)
		{
			$val='YES';
		}
		else
		{
			$val='NO';
		}
		return $val;
	}
}*/

$db = new database();
$db->connect();

$mex=explode('|','скачать|песня|продать|купить|отдать|орейро|sapato|oreiro|пиво|одежда|"esso_besso"|"играем командами"| ассортимент|автомагазин|кроссовки|босоножки|обувь| туфли|сапоги|сандалии|сабо|"Sergio esso"|"de_esso"|игры|смс|Las Ketchup|продажа|порно|секс|казино|игровые автоматы|лучшие рингтоны|мобильная версия');
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//print_r($mex);
if ($_GET['id']!='')
{
	echo '<form method="GET"><input type="text" name="id"><input type="submit"></form>';
	$q=$db->query('SELECT post_id,post_content,order_keyword,ful_com_post FROM blog_post as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_full_com as c ON a.post_id=c.ful_com_post_id WHERE b.order_id='.intval($_GET['id']).' ORDER BY post_id DESC LIMIT 500');
	while ($p=$db->fetch($q))
	{
		//if (check_post($p['post_content'],$p['order_keyword'])=='YES')
		$c=0;
		/*foreach ($mex as $item)
		{
			if (preg_match('/'.$item.'/isu',$p['post_content']))
			{
				$c=2;
				break;
			}
			else
			{
				$c=1;
			}
		}*/
		//echo check_post($p['order_keyword'],$p['post_content']);
		if((check_post(strip_tags($p['post_content']),$p['order_keyword'])==1)
		//if (check_post(strip_tags($p['post_content']),$p['order_keyword'])==1)
		//if ($c==1)
		{
			echo '<div style="background: #0f0;">'.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'|||'.strip_tags($p['ful_com_post']).'</div><br>';
		}
		elseif ((check_post(strip_tags($p['ful_com_post']),$p['order_keyword'])==1))
		{
			echo ']]]<div style="background: #0f0;">'.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'|||'.strip_tags($p['ful_com_post']).'</div><br>';
		}
		else
		{
			//echo 'DELETE FROM blog_post WHERE post_id='.$p['post_id'].'<br>';
			//$db->query('DELETE FROM blog_post WHERE post_id='.$p['post_id']);
			echo '<div style="background: #f00;">'.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'</div><br>';
		}
	}
}
else
{
	echo '<form method="GET"><input type="text" name="id"><input type="submit"></form>';
}

//echo check_post('путина краб','путин ~краб');
//for ($i=0;$i<3000;$i++)
/*$cont=file_get_contents('http://blogs.mail.ru/cgi-bin/journal/last_public_posts?rss=1');
$cont=iconv('windows-1251','UTF-8',$cont);
//echo $cont;
$regex='/<item>.*?<description>(?<cont>.*?)<\/description>.*?<pubDate>(?<time>.*?)<\/pubDate>.*?<link>(?<link>.*?)<\/link>.*?<\/item>/is';
preg_match_all($regex,$cont,$out);
//print_r($out);
foreach ($out['cont'] as $key => $item)
{
	echo $key;
	if (check_post($item,'путин')=='YES')
	{
		echo $item."\n\n\n\n";
	}
}

preg_match_all('/\((?<kw>[^()]*?)\)/is','путин | медведев && (( прохоров && жириновский | зюганов) | миронов) | (кудрин | дядя ваня)',$out,PREG_OFFSET_CAPTURE);*/
//echo mb_strlen(substr('путин | медведев && (( прохоров && жириновский | зюганов) | миронов) | (кудрин | дядя ваня)', 0, $out['kw'][0][1]), "UTF-8");
//print_r($out);
?>