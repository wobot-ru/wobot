<?


require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/new/com/auth.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");

//print_r($_GET);
//echo mb_strtolower(urldecode($_GET['kword']),"UTF-8");
$db = new database();
$db->connect();
//echo 'SELECT * FROM blog_ful_com WHERE ful_com_post_id='.$_GET['id'];
$kword=$_GET['keyword'];
//echo $kword;
$kword=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$kword);
$kword=preg_replace('/[ ]+/isu',' ',$kword);
$kword=explode(' ',$kword);
//print_r($kword);
$ressec=$db->query('SELECT * FROM blog_full_com WHERE ful_com_post_id='.$_GET['id'].' AND ful_com_order_id='.$_GET['order_id']);
//echo 'SELECT * FROM blog_full_com WHERE ful_com_post_id='.$_GET['id'];
while($blog=$db->fetch($ressec))
{
	$text=preg_replace('/<iframe.*?>/','',preg_replace('/\s/is',' ',$blog['ful_com_post']));
	//$pat="/(>[^<]*?)(".mb_strtolower(urldecode($_GET['kword']),"UTF-8").")([^<]*?)/is";	
	//$rep='$1<b style="color:white;background-color: #83b226; -moz-border-radius: 2px; padding: 1px;">$2</b>$3';
	//echo $cont;
	foreach ($kword as $item)
	{
		if (($item!='') && ($item!=' '))
		{
			$regex1='/\s(?<str>.{1,100}'.mb_strtolower($item,"UTF-8").'.{1,200})[\.\s]/isu';
			$regex='/\.(?<str>.*?'.mb_strtolower($item,"UTF-8").'.*?)\./isu';
			//echo $regex;
			preg_match_all($regex,$text,$out);
			preg_match_all($regex1,$text,$out1);
			//echo $out['str'][0].'<br>';
			//print_r($out);
			foreach ($out['str'] as $kk)
			{
				$outmm[]=$kk;
			}
			foreach ($out1['str'] as $kk)
			{
				$outmm1[]=$kk;
			}
		}
	}
	foreach ($outmm as $itt)
	{
		if ($itt!='')
		{
			$tf=$itt;
			break;
		}
	}
	foreach ($outmm1 as $itt)
	{
		if ($itt!='')
		{
			$tf1=$itt;
			break;
		}
	}
	//$text=$outmm[0].'<br><br><br><br><br><br><br>'.$outmm1[0];
	//$text=$tf.'<br><br><br><br><br><br><br>'.$tf1;
	if ($tf>$tf1)
	{
		$text=$tf;
	}
	else
	{
		$text=$tf1;
	}
	if ($text=='')
	{
		$text=urldecode($_GET['cont']);
	}
	foreach ($kword as $item)
	{
		if (($item!='') && ($item!=' '))
		{
			$text=preg_replace('/('.mb_strtolower(urldecode($item),"UTF-8").')/isu', '<b style="color:white;background-color: #3C6087; -moz-border-radius: 2px; padding: 1px;">$1</b>', $text);
		}
	}
	echo "<html><head>
		<link href='/css/wobot_lk.css' rel='stylesheet' type='text/css' /> 
				    <link href='/img/favicon_lk.gif' rel='shortcut icon' /> 
				    <link href='/css/details_lk.css' rel='stylesheet' type='text/css' /> 
		<link href='/css/old_details_lk.css' rel='stylesheet' type='text/css' />
		<script type=\"text/javascript\" src=\"/js/jquery.js\"></script> 
		<script>
		$(document).ready(function(){
			/*alert(parent.jQuery.$('#othr5').attr('id'));*/
			/*alert(parent.$('#othr5').attr('class'));*/
			parent.$('#ifrfull').attr('width','500');";
		//if (mb_strlen($text,"UTF-8")>500)	
		{
			//echo "parent.$('#ifrfull').attr('height','".(100+(mb_strlen($text,"UTF-8")-500)/65*14)."');";
		}
		//else
		{
			echo "parent.$('#ifrfull').attr('height','100');";
		}
		echo "
			$('.gg').parent.parent.attr('width','900');
			/*$(\\\'#othr5\\\').attr(\\\'width\\\',\\\'900\\\');*/
		});
		</script>
		</head><body>";
		if ($text!='')
		{
			echo $text;
		}
		else
		{
			echo '1.) '.urldecode($_GET['cont']);
		}
}
echo '</body></html>';
//print_r($_GET);
?>