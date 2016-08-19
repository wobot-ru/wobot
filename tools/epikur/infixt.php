<?
//beta:var/www/tagjob
//beta:tpjob
//production:tpjob
require_once('/var/www/new/com/porter.php');
require_once('worker.php');
require_once('splitter.php');

$memcache = memcache_connect('localhost', 11211);

$operators = array(/*"diff",*/"&","&&", "|", "~","~~");
$num_operands = array(/*"diff" => 2,*/"&" => 2,"&&"=>2, "|" => 2, "~" => 2,"~~"=>2);
$parenthesis  = array("(", ")");

function tokenize($line) {
	// Numbers are tokens, as are all other non-whitespace characters.
	// Note: This isset(var)n't a particularly efficent tokenizer, but it gets the
	// job done.
	$out = array();
    //echo "=====\n";
	while (strlen($line)) {
		$line = trim($line);
		// echo $line."\n";
		if (preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$line,$regs))
		{
			// print_r($regs);
			// $mregs=preg_split('/[^а-яa-zё\'0-9\.\-]/isu', trim(mb_substr($line, 0, mb_strlen($regs[0],'UTF-8')-mb_strlen($regs[1],'UTF-8'),'UTF-8')));
			$mregs=preg_split('/[^а-яa-zё\'0-9\.\-]/isu', trim(mb_substr($line, 0, mb_strlen($regs[0],'UTF-8'),'UTF-8')));
			foreach ($mregs as $key => $item)
			{
				if (trim($item)=='') array_shift($mregs);
			}
			if (count($mregs)!=1)
			{
				$plus_one='';
				foreach ($mregs as $key_key_mreg => $key_mregs)
				{
					if ($plus_one!='') $out[]=$plus_one;
					$out[]=$key_mregs;
					$plus_one='/+1';
				}
			}
			else
			{
				// $out[] = mb_substr($line, 0, mb_strlen($regs[0],'UTF-8')-mb_strlen($regs[1],'UTF-8'),'UTF-8');
				$out[] = mb_substr($line, 0, mb_strlen($regs[0],'UTF-8'),'UTF-8');
			}
            // $out[] = $regs[1];
            // print_r($out);
            $line = mb_substr($line, mb_strlen($regs[0],'UTF-8'),mb_strlen($line,'UTF-8'),'UTF-8');			
            // echo $line."\n";
		}
		elseif (preg_match('/^[\"]/u', $line, $regs)) {
			$regex='/^(?<quot>\"[^\"]+\")/isu';
			preg_match_all($regex, $line, $regs);
			// print_r($regs);
			$out[]=$regs['quot'][0];
			$line = substr($line, strlen($regs['quot'][0]));
			// sleep(1);
		}
		elseif (preg_match('/^[A-Za-zА-Яа-яёЁ0-9\"\-\.\s\,\’\µ\'\!\*\$\@\+\:\_\#]+/u', $line, $regs)) {
			# It's a variable name
			// echo $regs[0].' ';
			if ((substr($regs[0],0,1)=='"') && (substr($regs[0], -1)=='"'))
			{
				// print_r($regs);
				$out[] = $regs[0];
				$line = substr($line, strlen($regs[0]));
			}
			else
			{
				$mregs=preg_split('/[^а-яa-zё\'0-9\.\-\:\/]/isu', trim($regs[0]));
				// print_r($mregs);
				foreach ($mregs as $key_key_mreg => $key_mregs)
				{
					if (trim($key_mregs)=='') unset($mregs[$key_key_mreg]);
				}
				// print_r($mregs);
				// echo '!'.count($mregs).'!';
				if (count($mregs)!=1)
				{
					$plus_one='';
					foreach ($mregs as $key_key_mreg => $key_mregs)
					{
						// echo $k_key.' ';
						if ($plus_one!='') $out[]=$plus_one;
						$out[]=$key_mregs;
						$plus_one='/+1';
						// if (trim($key_mregs)=='') continue;
						// if ($key_key_mreg!=(count($mregs)-1)) $out[]='/+1';
					}
				}
				else
				{
					$out[] = $regs[0];
				}
				$line = substr($line, strlen($regs[0]));
			}
            //echo $regs[0]."\n";
		} else {
			# It's some other character
			$out[] = $line[0];
            //echo $line[0]."\n";
            $line = substr($line, 1);
		}

	}
	// print_r($out);
	//die();
	//исправляем && и ~~
	foreach ($out as $key=>$value)
	{
		//if ($key>0)
		//{
			$out[$key]=trim($out[$key]);
			if (($out[$key]=='&')&&($out[$key-1]=='&'))
			{
				$out[$key-1]='&&';
				unset($out[$key]);
				continue;
			}
			if (($out[$key]=='~')&&($out[$key-1]=='~'))
			{
				$out[$key-1]='~~';
				unset($out[$key]);
				continue;
			}
			
			// echo '|'.$out[$key].'='.substr($out[$key],0,1).'='.substr($out[$key], -1)."=\n";

			// Не делаем стемминг для двойных кавычек и восклицательного знака
			if ((substr($out[$key],0,1)=='"') && (substr($out[$key], -1)=='"'))
			{
				$out[$key]=substr($out[$key],1,-1); //отбрасываем кавычки
				//echo 'кавычки: '.$out[$key]."\n";
				$out[$key]=mb_strtolower($out[$key],"UTF-8");
			}
			elseif (substr($out[$key],0,1)=='!')
			{
				$out[$key]=substr($out[$key],1); //отбрасываем !
				//echo 'кавычки: '.$out[$key]."\n";
				$out[$key]=mb_strtolower($out[$key],"UTF-8");
				// print_r($out[$key]);
			}
			elseif (substr($out[$key],0,1)=='$')
			{
				//первая буква
				$stemmer=new Lingua_Stem_Ru();
				$firstletter=mb_substr($out[$key],1,1,'UTF-8');
				// echo $out[$key].'<=>';
				// $out[$key] = $stemmer->stem_word($out[$key]);
				// echo $out[$key]."\n";
				// echo $firstletter;
				$out[$key]='$'.$firstletter.mb_strtolower(substr($out[$key],2),"UTF-8"); //отбрасываем !
				// print_r($out);
				//echo 'кавычки: '.$out[$key]."\n";

			}
			//Для остальных случаев делаем
			else
			{
				if (strlen($out[$key])>3) //если меньше трех в длину, нах стеммить?
				{
					//$stemmer = new Lingua_Stem_Ru();
					//$out[$key]
					$stemmer=new Lingua_Stem_Ru();
					// echo $out[$key].' '.$stemmer->stem_word($out[$key])."\n";//стеммим
					$out[$key] = $stemmer->stem_word($out[$key]);//стеммим
					//$msg=$word->stem_word('бдбд');
					//echo '|';
					//echo $msg;
				}
				//echo 'без кавычек: '.$out[$key]."\n";
				$out[$key]=mb_strtolower($out[$key],"UTF-8");
			}
		//}
	}
	
	return $out;
}


function is_operator($token) {
    global $operators;
    if (preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$token)) return true;
    return in_array($token, $operators);
}

function is_right_parenthesis($token) {
    global $parenthesis;
    return $token == $parenthesis[1];
}

function is_left_parenthesis($token) {
    global $parenthesis;
    return $token == $parenthesis[0];
}

function is_parenthesis($token) {
    return is_right_parenthesis($token) || is_left_parenthesis($token);
}

// check whether the precedence if $a is less than or equal to that of $b
function is_precedence_less_or_equal($a, $b) {
	// echo $a.'<=>'.$b."\n";
    // "not" always comes first
    // if ($b == "not")
    //     return true;

    // if ($a == "not")
    //     return false;

    if ($a == "|" and $b == "&&")
        return true;
    if ($a == "|" and $b == "&")
        return true;
    if ($a == "&" and $b == "|")
        return false;
    if ($a == "&&" and $b == "|")
        return false;
    if ($a == "|" and preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$b))
        return true;
    if ($a == "|" and preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$b))
        return true;
    if (preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$a) and $b == "|")
        return false;
    if (preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$a) and $b == "|")
        return false;
    // if ($a == "|" and $b == "&")
    // 	return true;
    // if ($a == "&" and $b == "|")
    //     return false;
    // if ($a == "and" and $b == "or")
    //     return false;
/*
    if ($a == "diff" and $b == "and")
        return true;

    if ($a == "diff" and $b == "or")
        return true;
*/
    // otherwise they're equal
    return true;
}


function shunting_yard($input_tokens) {
	$stack = array();
    $output_queue = array();

    foreach ($input_tokens as $token) {
        if (is_operator($token)) {
			if (count($stack)>0)//PHP Notice:  Undefined offset: -1 in /Users/rcp/coding/infix.php on line 130
			{
	            while (is_operator($stack[count($stack)-1]) && is_precedence_less_or_equal($token, $stack[count($stack)-1])) {
	                    $o2 = array_pop($stack);
	                    array_push($output_queue, $o2);
	            }
			}
            array_push($stack, $token);

        } else if (is_parenthesis($token)) {
            if (is_left_parenthesis($token)) {
                array_push($stack, $token);
            } else {
                while (!is_left_parenthesis($stack[count($stack)-1]) && count($stack) > 0) {
                    array_push($output_queue, array_pop($stack));
                }
                if (count($stack) == 0) {
                    //echo ("parse error");
                    //die();
                    return 0;
                }
                $lp = array_pop($stack);
            }
        } else {
            array_push($output_queue, $token);  
        }
    }
 //    echo "<br>";
	// print_r($stack);
	// echo "<br>";
    while (count($stack) > 0) {
        $op = array_pop($stack);
        if (is_parenthesis($op))
            //die("mismatched parenthesis");
            return 0;
        array_push($output_queue, $op);
    }

    return $output_queue;
}

function str2bool($s) {
    if ($s == "true")
        return true;
    if ($s == "false")
        return false;
    return 0;//die('$s doesn\'t contain valid boolean string: '.$s.'\n');
}

function apply_operator($operator, $a, $b) {
	//echo "calc: $operator ".($a ? "true":"false")." ".($b ? "true":"false")."\n";
	//Отладка операторов
	
	/*echo '!'.$operator."!===========<br>\n";
	print_r($a);
	echo "\n";
	print_r($b);
	echo "\n";*/
	// echo "===========<br>\n";
	//"&","&&", "|", "~","~~"

    /*if (is_string($a))
        $a = str2bool($a);
    if (!is_null($b) and is_string($b))
        $b = str2bool($b);*/

	/*//оператор &
    if ($operator == "&")
		return array_intersect($a,$b);
	//оператор |
    else if ($operator == "|")
		return array_merge($a,$b);
	//оператор ~
	else if ($operator == "~")
        return array_diff($b,$a);
	//оператор &&
	else if ($operator == "&&")
        return (((count($a))>0&&(count($b)>0))?array_merge($a,$b):array());
	//оператор ~~
	else if ($operator == "~~")
        return ((count($a)==0)?$b:array());*/

    if ($operator == '&') 
	{
		if ((count($a)==0)||(count($b)==0)) return array();
		foreach ($a as $key => $item)
		{
			if (isset($b[$key]))
			{
				foreach ($b[$key] as $bi)
				{
					// echo $bi.' ';
					if (!in_array($bi, $outmas[$key])) $outmas[$key][]=$bi;
				}
				foreach ($a[$key] as $ai)
				{
					// echo $ai.' ';
					if (!in_array($ai, $outmas[$key])) $outmas[$key][]=$ai;
				}
			}
		}
		return $outmas;
	}
    elseif ($operator == '|') 
	{
		foreach ($a as $key => $item)
		{
			foreach ($a[$key] as $ai)
			{
				if (!in_array($ai, $outmas[$key])) $outmas[$key][]=$ai;
			}
		}
		foreach ($b as $key => $item)
		{
			foreach ($b[$key] as $bi)
			{
				if (!in_array($bi, $outmas[$key])) $outmas[$key][]=$bi;
			}
		}
		return $outmas;
	}
    elseif ($operator == '~') 
	{
		foreach ($a as $key => $item)
		{
			unset($b[$key]);
		}
		return $b;
	}
    elseif ($operator == '&&') 
	{
		if (count($a)>0&&count($b)>0)
		{
			foreach ($a as $key => $item)
			{
				foreach ($a[$key] as $ai)
				{
					if (!in_array($ai, $outmas[$key])) $outmas[$key][]=$ai;
				}
			}
			foreach ($b as $key => $item)
			{
				foreach ($b[$key] as $bi)
				{
					if (!in_array($bi, $outmas[$key])) $outmas[$key][]=$bi;
				}
			}
			return $outmas;
		}
		else return array();
	}
    elseif ($operator == '~~') 
	{
		if (count($a)!=0) return array();
		else return $b;
	}
    elseif (preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$operator)) 
	{
		$outarray=array();
		// echo 'GG';
		$regex='/\+(?<pos>\d+)/isu';
		preg_match_all($regex, $operator, $outpos);
		// print_r($outpos);
		$pos=abs($outpos['pos'][0]);
		$regex='/\-(?<neg>\d+)/isu';
		preg_match_all($regex, $operator, $outneg);
		// print_r($outneg);
		$neg=abs($outneg['neg'][0]);
		if (($pos=='') && ($neg==''))
		{
			$regex='/\/(?<all>\d+)/isu';
			preg_match_all($regex, $operator, $out);
			// print_r($out);
			$pos=abs($out['all'][0]);
			$neg=abs($out['all'][0]);
		}
		// echo '!!!!!!!';
		// print_r($a);
		// print_r($b);
		// echo '!!!!!!!';
		// echo $pos.' '.$neg;
		foreach ($a as $key => $aitem)
		{
			$merge=0;
			if (isset($b[$key]))
			{
				foreach ($b[$key] as $bi)
				{
					foreach ($a[$key] as $ai)
					{
						// echo $ai.' '.$bi.' '.($ai-$bi)."\n";
						// if ((($ai-$bi)<=($pos+1) && ($ai-$bi)>=0) || (($ai-$bi)>=($neg+1)*(-1) && ($ai-$bi)<0)) $merge=1;
						if (($pos==0)||($neg==0))
						{
							if ((($ai-$bi)<=($pos+1) && ($ai-$bi)>=0)&&($pos!=0)) $merge=1;
							if ((($ai-$bi)>=($neg+1)*(-1) && ($ai-$bi)<0)&&($neg!=0)) $merge=1;
						}
						else
						{
							if ((($ai-$bi)<=($pos+1) && ($ai-$bi)>=0) || (($ai-$bi)>=($neg+1)*(-1) && ($ai-$bi)<0)) $merge=1;
						}
					}
				}
				// print_r($a[$key]);
				// print_r($b[$key]);
				// echo 'merge='.$merge;
				if ($merge==1) $outarray[$key]=array_merge($a[$key],$b[$key]);
				//print_r($outarray[$key]);
			}
		}
		return $outarray;
	}

    //else if ($operator == "not")
    //    return ! $a;
    else return 0;//die("unknown operator `$function'");
}

function get_num_operands($operator) {
    global $num_operands;
    //echo intval(preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$operator));
    if (preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$operator)) return 2;
    return $num_operands[$operator];
}

function is_unary($operator) {
    return get_num_operands($operator) == 1;
}

function is_binary($operator) {
    return get_num_operands($operator) == 2;
}

function eval_rpn($tokens) {
    $stack = array();
    foreach ($tokens as $t) {
    	if (is_operator($t)||preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$t)) {
            if (is_unary($t)) {
                $o1 = array_pop($stack);
                $r = apply_operator($t, $o1, null);
                // echo 'res1: ';
                // print_r($r);
                array_push($stack, $r);
            } else { // binary
            	// echo 'binary';
                $o1 = array_pop($stack);
                $o2 = array_pop($stack);
                $r = apply_operator($t, $o1, $o2);
                // echo 'res2: ';
                // print_r($r);
                array_push($stack, $r);
            }
        } else { // operand
        	array_push($stack, $t);
        }
    }
    // print_r($stack);
    if (count($stack) != 1)
        // die("invalid token array");
        return 0;

    return $stack[0];
}

//преобразователь
//ключевик -> (0,5,6) - массив предложений где есть слово
function text_scan($text, $input)
{
	global $operators, $parenthesis;
	$stemmer=new Lingua_Stem_Ru();
	unset($input2);
	//$sents=preg_split('/[\.\?\!]/isu',$text);// заменять точки в ссылках
	$sents=split_sentences($text);
	foreach($sents as $l=>$sent)
	{
		if (strlen($sent)==0) continue;
		//echo $l." ".$sent."\n";
		$level=$l;
		foreach ($input as $k=>$pie)
		{
			if (trim($pie)=='') continue;

			if (!isset($input2[$k])) $input2[$k]=array();
			
			if ((!in_array($pie,$operators))&&(!in_array($pie,$parenthesis))&&(!preg_match('/^(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/[\-\+]?\d+)/isu',$pie)))
			{
				// echo $pie.' ';
				$c=0;
				$free_form=0;
				$check_first_letter=0;
				$stem_pie=$stemmer->stem_word($pie);
				if ($stem_pie[0]=='$') 
				{
					$stem_pie_firstletter=mb_substr($pie, 1, 1, 'UTF-8');
					$stem_pie=$stem_pie_firstletter.mb_substr($stem_pie, 2, mb_strlen($stem_pie,'UTF-8')-2,'UTF-8');
					$regex='/[^a-zа-яё](?<words>'.$stem_pie.'[а-яa-zё]*)[^a-zа-яё]/isu';
					$pie=mb_substr($pie, 1, mb_strlen($pie,'UTF-8')-1,'UTF-8');
					$check_first_letter=1;
				}
				elseif (mb_substr($stem_pie,mb_strlen($stem_pie,'UTF-8')-1,1,'UTF-8')=='*')
				{
					$regex='/[^a-zа-яё](?<words>'.mb_substr($stem_pie,0,mb_strlen($stem_pie,'UTF-8')-1,'UTF-8').'[а-яa-zё]*)[^a-zа-яё]/isu';				
					$free_form=1;
				}
				else
				{
					$stem_pie=$stemmer->stem_word($pie);
					$stem_pie=preg_replace('/([^а-яa-zё0-9])/isu','\\\\$1',$stem_pie);
					$regex='/[^a-zа-яё](?<words>'.$stem_pie.'[а-яa-zё]*)[^a-zа-яё]/isu';				
				}
				// echo $regex.' ! '.' '.preg_replace('/\s/isu',' ',$sent).' '." !\n";
				preg_match_all($regex, ' '.preg_replace('/\s/isu',' ',$sent).' ', $out);
				// print_r($out);
				if ($free_form==0)
				{
					foreach ($out['words'] as $item)
					{
						// echo mb_strtolower($item,'UTF-8').' '.$stemmer->stem_word($item).' '.$stemmer->stem_word($pie).' '.mb_strtolower($pie,'UTF-8')."\n";
						if ($check_first_letter==0)
						{
							if (($stemmer->stem_word($item)==mb_strtolower($pie,'UTF-8')) || (mb_strtolower($item,'UTF-8')==mb_strtolower($pie,'UTF-8')))
							{
								$c=1;
								break;
							}
						}
						else
						{
							// echo $item.' '.$pie.' '.$stemmer->stem_word(mb_strtolower($item,'UTF-8')).' '.$stem_pie_firstletter.' '.mb_substr($item, 0,1,'UTF-8')."\n";
							if ((($stemmer->stem_word($item)==mb_strtolower($pie,'UTF-8')) || (mb_strtolower($item,'UTF-8')==mb_strtolower($pie,'UTF-8'))) && (mb_substr($item, 0,1,'UTF-8')==$stem_pie_firstletter))
							{
								// echo 'c===1;';
								$c=1;
								break;
							}
						}
					}
					if ($c==1) 
					{
						$input2[$k][$level]=get_position_in_sentence($pie,$sent);
						//$input2[$k]['pos']=get_position_in_sentence($pie,$sent);
					}
				}
				else
				{
					if (count($out['words'])!=0)
					{
						$input2[$k][$level]=get_position_in_sentence($pie,$sent);		
						// $input2[$k]['pos']=get_position_in_sentence($pie,$sent);
					}
				}
			}
			else $input2[$k]=$pie;
		}
	}
	return $input2;
}

function get_position_in_sentence($word,$sent)
{
	// echo $word;
	$mword=preg_split('/[\.\,\s]+/isu', $word);
	$msent=preg_split('/[\.\,\s]+/isu', $sent);
	// $msent=split_sentences($sent);
	// print_r($msent);
	// print_r($mword);
	foreach ($msent as $key => $item)
	{
		foreach ($mword as $it_word)
		{
			// echo '/[^а-яa-zё]'.$it_word.'/isu'.' '.$item."\n";
			$it_word=preg_replace('/([^а-яa-zё0-9])/isu','\\\\$1',$it_word);
			if (preg_match('/^'.$it_word.'/isu', $item)&&!in_array($it_word, $mout)) $mout[]=$key;
			if (preg_match('/[^а-яa-zё]'.$it_word.'/isu', $item)&&!in_array($it_word, $mout)) $mout[]=$key;
			// print_r($mout);
		}
	}
	// print_r($mout);
	return $mout;
}

function check_post($post,$kw)
{
	global $memcache;
	$md5_cache=md5($post.' '.$kw);
	$output=$memcache->get('infix_'.$md5_cache);
	// echo $output.'|';
	// if (is_numeric($output)) return $output;
	// usleep(100000);
	$kw=preg_replace('/(\/)(\()([\s\d\-\+]+)(\))/isu','$1{$3}',$kw);
	//$post=
	//$kw=mb_strtolower($kw,'UTF-8');
	if ($post=='') return 0;
	//$query=mb_strtolower($kw,'UTF-8');
	$query=$kw;
	$text=' '.$post.' ';//mb_strtolower(' '.$post.' ','UTF-8');
	//echo '|||'.$text.'|||';
	// $workers=get_worker();
	// $output=parseCheckUrl($workers[0],$post,$kw);
	// if ($output!=-1) return $output;
	$input = tokenize($query,$post,$kw);
	print_r($input);
	// die();
	// print_r($input);
	$input2=text_scan($text,$input);
	print_r($input2);
	// die();
	$tokens = shunting_yard($input2);
	// print_r($tokens);
	// die();
	$result = eval_rpn($tokens);
	// print_r($result);
	$memcache->set('infix_'.$md5_cache, (count($result)==0||$result==0?0:1), MEMCACHE_COMPRESSED, 86400);
	return (count($result)==0||$result==0?0:1);
}

function parseCheckUrl($host,$post,$kw)
{
	$curl = curl_init();
 
	//уcтанавливаем урл, к которому обратимся
	curl_setopt($curl, CURLOPT_URL, 'http://'.$host.'/tools/epikur/infix_post.php');
	//включаем вывод заголовков
	//curl_setopt($curl, CURLOPT_HEADER, 1);
	 
	//передаем данные по методу post
	curl_setopt($curl, CURLOPT_POST, 1);
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // таймаут соединения
  	curl_setopt($curl, CURLOPT_TIMEOUT, 5);        // таймаут ответа 
	//переменные, которые будут переданные по методу post
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'post='.urlencode($post).'&kw='.urlencode($kw));
	//я не скрипт, я браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
	
	$content = curl_exec( $curl );
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl );
	curl_close( $curl );
	$fp = fopen('/var/www/tools/epikur/apache.log', 'a');
	fwrite($fp, $content."\n");
	fclose($fp);
	if (is_numeric($content)) return intval($content);
	else return -1;
	// return intval($content);
}

$text='ПОЯВИЛАСЬ ТАВРИЯ В СИНЕМ ЦВЕТЕ. ФОТО В АЛЬБОМЕ. ЦЕНЫ И НАЛИЧИИ УТОЧНЯЙТЕ В ЛИЧКУ И 097-787-19-10 С 9-00 ДО 18-00.';
$kw='(tavriav|таврияв|((("таврия"|"tavria") /+1 ("В"|"V"))&(супермаркет|(супер /+1 маркет)|сеть|магазин|продукты|товары|доставка)))~~("ФК"|"СК"|футбол|футбольный|игра|щахтер|коньяк|металлург|мануал|трансляция|динамо|матч|симферополь|клуб|сезон)';
echo check_post($text,$kw);
// $stemmer=new Lingua_Stem_Ru();
// echo $stemmer->stem_word('рекламной');
/*$text='Более того, премиум-бренд всерьез заинтересовался данным вопросом, и работы по созданию первых прототипов WP8-смартфонов уже ведутся. Таким образом, Android остается единственным вариантом для смартфонов в <b>...</b>';
$query='(window&phone|windows&phone|winphone|"win phone"|виндоуз&фон|винфон|виндофон|телефон&windows|смартфон&винда|телефон&винда|коммуникатор&винда|смартфон&windows|телефон&microsoft|вендофон|телефон&венда|виндовс&фон|"wp 7"|wp7|"phone 7"|"фон 7"|"wp 7.5"|wp7.5|"phone 7.5"|"фон 7.5"|"wp 7,5"|wp7,5|"phone 7,5"|"фон 7,5"|"wp 7.8"|wp7.8|"phone 7.8"|"фон 7.8"|"wp 7,8"|wp7,8|"phone 7,8"|"фон 7,8"|WP8|"WP 8"|"phone 8"|"фон 8")~~скачать~~установка~~игры~~программы~~разработка~~приложений~~WhatsApp';

echo "================================================\n";
echo "Упоминание:\n".$text."\n";
echo "Запрос:\n".$query."\n";
echo "================================================\n";

// $input = array("A", "and", "B", "or", "C", "and", "(", "D", "or", "F", "or", "not", "G", ")");
//$input2 = 'путин|медведев';
//$input2 = array('путин','|','медведев');
//$input = array("false", "and", "true", "or", "true", "and", "(", "false", "or", "false", "or", "not", "true", ")");
//$input = array('медведев', 'and', 'путин');
$input = tokenize($query);
echo "Парсинг и стемминг:\n";
print_r($input);
//echo "================================================\n";
//die();
//print_r($input);
$input2=text_scan($text,$input);
//text_scan($text,$input);
//die();

$tokens = shunting_yard($input2);
$result = eval_rpn($tokens);
//foreach($input as $t)
//    echo $t." ";
//echo "==> ".($result ? "true" : "false")."\n";
echo "================================================\n";
echo "Результат:\n";
print_r($result);
echo "* Номера предлжений в тексте удовлетворяющие запросу\n";*/
?>
