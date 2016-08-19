<?
$operators = array(/*"diff",*/"and", "or", "not");
$num_operands = array(/*"diff" => 2,*/"and" => 2, "or" => 2, "not" => 1);
$parenthesis  = array("(", ")");

function is_operator($token) {
    global $operators;
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
    // "not" always comes first
    if ($b == "not")
        return true;

    if ($a == "not")
        return false;

    if ($a == "or" and $b == "and")
        return true;

    if ($a == "and" and $b == "or")
        return false;
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
            while (is_operator($stack[count($stack)-1]) && is_precedence_less_or_equal($token, $stack[count($stack)-1])) {
                    $o2 = array_pop($stack);
                    array_push($output_queue, $o2);
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
                    echo ("parse error");
                    die();
                }
                $lp = array_pop($stack);
            }
        } else {
            array_push($output_queue, $token);  
        }
    }

    while (count($stack) > 0) {
        $op = array_pop($stack);
        if (is_parenthesis($op))
            die("mismatched parenthesis");
        array_push($output_queue, $op);
    }

    return $output_queue;
}

function str2bool($s) {
    if ($s == "true")
        return true;
    if ($s == "false")
        return false;
    die('$s doesn\'t contain valid boolean string: '.$s.'\n');
}

function apply_operator($operator, $a, $b) {
	//echo "calc: $operator ".($a ? "true":"false")." ".($b ? "true":"false")."\n";
	echo "$operator===========\n";
	print_r($a);
	print_r($b);
	
    /*if (is_string($a))
        $a = str2bool($a);
    if (!is_null($b) and is_string($b))
        $b = str2bool($b);*/

	//оператор &
    if ($operator == "and")
        //return $a and $b;
		return array_intersect($a,$b);
	//оператор |
    else if ($operator == "or")
        //return $a or $b;
		return array_merge($a,$b);
	//оператор ~array_diff
	else if ($operator == "diff")
        return array_diff($a,$b);
    else if ($operator == "not")
        return ! $a;
    else die("unknown operator `$function'");
}

function get_num_operands($operator) {
    global $num_operands;
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
        if (is_operator($t)) {
            if (is_unary($t)) {
                $o1 = array_pop($stack);
                $r = apply_operator($t, $o1, null);
                array_push($stack, $r);
            } else { // binary
                $o1 = array_pop($stack);
                $o2 = array_pop($stack);
                $r = apply_operator($t, $o1, $o2);
                array_push($stack, $r);
            }
        } else { // operand
            array_push($stack, $t);
        }
    }

    if (count($stack) != 1)
        die("invalid token array");

    return $stack[0];
}

function text_scan($text, $input)
{
	global $operators, $parenthesis;
	unset($input2);
	$sents=explode('.',$text);
	foreach($sents as $l=>$sent)
	{
		if (strlen($sent)==0) continue;
		echo $l." ".$sent."\n";
		$level=$l;
		foreach ($input as $k=>$pie)
		{
			if (!isset($input2[$k])) $input2[$k]=array();

			if ((!in_array($pie,$operators))&&(!in_array($pie,$parenthesis)))
			{
				if (mb_strpos($sent,$pie,0,'UTF-8')===FALSE)
				{

				}
				else
				{
					$input2[$k][]=$level;
				}
			}
			else $input2[$k]=$pie;
		}
	}
	return $input2;
}

$text='медведев и путин. медведев. путин.';

// $input = array("A", "and", "B", "or", "C", "and", "(", "D", "or", "F", "or", "not", "G", ")");
//$input2 = 'путин|медведев';
//$input2 = array('путин','|','медведев');
//$input = array("false", "and", "true", "or", "true", "and", "(", "false", "or", "false", "or", "not", "true", ")");
$input = array('медведев', 'and', 'путин');
//print_r($input);
$input2=text_scan($text,$input);
//text_scan($text,$input);
//die();

$tokens = shunting_yard($input2);
$result = eval_rpn($tokens);
foreach($input as $t)
    echo $t." ";
//echo "==> ".($result ? "true" : "false")."\n";
echo "==>\n";
print_r($result);
?>
