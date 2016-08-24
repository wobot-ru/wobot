<?php


//echo '123';

require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
require_once('modules/user/tcpdf/config/lang/rus.php');
require_once('modules/user/tcpdf/tcpdf.php');



ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();

$db = new database();
$db->connect();

//echo 'not loged';

auth();
if (!$loged) die();

//echo $_GET['time'];

$user['user_pagecount']=intval($user['user_pagecount']);
if (($user['user_pagecount']<5)||($user['user_pagecount']>100)) $user['user_pagecount']=50;

if ($_GET['social']!='') {$social=htmlentities($_GET['social']); $makesocial=true;}

//echo 'loged';

// login information set
//function cabinet()
//{
if ($_GET['order_id']!=0)
{
//echo 'order_id here';

$fn = "data/blog/".intval($_GET['order_id']).".xml";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
fclose($h);
$data='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
'.$data.'
</head>
</html>
';
$pn=intval($_GET['p']);
        $dom = new DomDocument;
        $res = @$dom->loadHTML($data);
//$dom->encoding='utf-8';
//$dom->schemaValidateSource='';
$posts = $dom->getElementsByTagName("post");

//$xml = simplexml_load_file("data/blog/135.xml");
   // print_r($xml);
if ($_GET['time']!=0) {
list($_GET['time'],$tmp)=explode(',',$_GET['time'],2);
$_GET['time']/=1000;
$_GET['time']=mktime(0,0,0,date('n',$_GET['time']),date('j',$_GET['time']),date('Y',$_GET['time']));
//echo '<script>alert("'.date('n.j.Y',$_GET['time']).'");</script>';
}

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Wobot Research');
$pdf->SetTitle('Экспорт сообщений');
$pdf->SetSubject('Экспорт сообщений');
$pdf->SetKeywords('Wobot Research');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' за '.date('h:i:s d.m.Y'), PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();
$html.='
<style>
a {
	font-size: 20px;
	padding: 2px;
}

span {
	font-size: 18px;
	padding: 2px 2px 5px 2px;
}
</style>
';
// Set some content to print
//$html = <<<EOD
//123
//EOD;

//$user['user_pagecount']=50;

$i=0;
//echo '(($i>=50*'.$pn.'))&&($i<50*('.$pn.'+1))<br>';
foreach ($posts as $post)
{
	$link=$post->firstChild->nextSibling->textContent;//->firstChild->nextSibling
	$time=$post->firstChild->nextSibling->nextSibling->textContent;

	$content=$post->firstChild->nextSibling->nextSibling->nextSibling->textContent;
	$nick=$post->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
        $hn=parse_url($link);
        $hn=$hn['host'];
        $ahn=explode('.',$hn);
        $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];
	//echo 'link ['.$link.'] time ['.$time.'] content ['.$content.'] nick ['.$nick.']<br>';
	//$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;
//if (($i>=50*($pn))&&($i<50*($pn+1)))
//{
$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
if ($_GET['time']!=0)
{
	if ((intval($time)==intval($_GET['time']))) {
if (($makesocial==false)||($social==$hn))
{

//if (($i>=50*($pn))&&($i<50*($pn+1)))
//{   
//echo '<img src="/img/social/'.(file_exists('img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'" title="'.$hn.'" alt="'.$hn.'"> <a href="'.$link.'" target="_blank"><b>'.$nick.'</b>'.htmlentities($content,ENT_COMPAT,'utf-8').'</a><br>
//<span class="sl rln">'.($i+1).' ('.date("d.m.Y",$time).')</span><br>';
$html.='<a href="'.substr($link,0,strlen($link)-1).'" target="_blank">'.htmlentities($content,ENT_COMPAT,'utf-8').'</a><br>
<span class="sl rln">'.($i+1).' ('.date("d.m.Y",$time).')</span><br>
';
//}
$i++;
}

}
//echo $time." ".date('n.j.Y',$time)." ".$_GET['time']."<br>
//";
}else{

if (($makesocial==false)||($social==$hn))
{

//if (($i>=50*($pn))&&($i<50*($pn+1)))
//{   
	//echo '<img src="/img/social/'.(file_exists('img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'" title="'.$hn.'" alt="'.$hn.'"> <a href="'.$link.'" target="_blank"><b>'.$nick.'</b>'.htmlentities($content,ENT_COMPAT,'utf-8').'</a><br>
//<span class="sl rln">'.($i+1).' ('.date("d.m.Y",$time).')</span><br>';
$html.='<a href="'.substr($link,0,strlen($link)-1).'" target="_blank">'.htmlentities($content,ENT_COMPAT,'utf-8').'</a><br>
<span class="sl rln">'.($i+1).' ('.date("d.m.Y",$time).')</span><br>
';
//}

}

}
//}

//if ($i==(50*($pn+1))) break;
if (($_GET['time']==0)&&(($makesocial==false)||($social==$hn))) $i++;
}

// Print text using writeHTMLCell()
$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('wobot'.date('-dmy-his').'.pdf', 'I');



$j=0;
foreach ($posts as $post)
{
if ($_GET['time']!=0)
{

if (($makesocial==false)||($social==$hn))
{
        $time=$post->firstChild->nextSibling->nextSibling->textContent;
	$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
	if ($time==intval($_GET['time'])) {
	$j++;}
}

}else{

if (($makesocial==false)||($social==$hn))
{
	$j++;
}

}
}
/*echo '<table width="100%"><td align="center">';
if ($pn>7) echo '←&nbsp;';
for ($i=0;$i<intval($j/(50+1)+1);$i++)
if (($i>$pn-8)&&($i<$pn+8))
echo ($pn==$i?'':'<a href="#" onclick="loaditem(\'user/comment?order_id='.intval($_GET['order_id']).'&time='.intval($_GET['time']).'000&p='.$i.'\',\'#commentbox\');return false;">').($i+1).($pn==$i?' ':'</a> ');
if ($pn<intval($j/(50+1)-7)) echo '→';
echo '</td></table>';*/
}

?>