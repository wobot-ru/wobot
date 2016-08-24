<?
setlocale(LC_COLLATE, 'UTF8');
/* #INFO############################################
Author: Igor Feghali
Email: ifeghali@interveritas.net
################################################# */

/* #FILE DESCRIPTION################################
Example for the line graph
################################################# */

// #INCLUDE#########################################
require("charts.class.php");
// #################################################

// #INSTANTIATING CLASS#############################
$g = new chart;
// #################################################

// #X ELEMENTS FOR THE FIRST LINE###################
$elemx = Array();
$mas=($_SERVER['argv']);
//print_r($mas);
$mas=urldecode($mas[1]);
//echo $mas;
$mas=json_decode($mas,true);
//print_r($mas);
$kkk=0;
foreach ($mas as $year => $item)
{
	foreach ($item as $month => $item2)
	{
		foreach ($item2 as $day => $value)
		{
			$elemx[0][] = $year."-".$month."-".$day;
			$elemy[0][] = $value;
		}
	}
}
//print_r($elemx);
//print_r($elemy);
/*$elemx[0][] = "2005-01-01";
$elemx[0][] = "2005-01-02";
$elemx[0][] = "2005-01-03";
$elemx[0][] = "2005-01-04";
$elemx[0][] = "2005-01-05";
$elemx[0][] = "2005-01-06";
$elemx[0][] = "2005-01-07";
// #################################################

// #X ELEMENTS FOR THE SECOND LINE##################
$elemx[1][] = "2004-01-01";
$elemx[1][] = "2004-01-02";
$elemx[1][] = "2004-01-03";
$elemx[1][] = "2004-01-04";
$elemx[1][] = "2004-01-05";
$elemx[1][] = "2004-01-06";
$elemx[1][] = "2004-01-07";
// #################################################
*/
// #FINDING THE MAX NUMBER OF X ELEMENTS############
$xcount = 0;
foreach ($elemx as $v)
	$xcount = max($xcount, count($v));
	/*
// #################################################

// #Y ELEMENTS FOR THE FIRST LINE###################
$elemy = Array();
$elemy[0][] = 87;
$elemy[0][] = 8;
$elemy[0][] = 83;
$elemy[0][] = 94;
$elemy[0][] = 48;
$elemy[0][] = 49;
$elemy[0][] = 29;
// #################################################

// #Y ELEMENTS FOR THE SECOND LINE##################
$elemy[1][] = 97;
$elemy[1][] = 7;
$elemy[1][] = 36;
$elemy[1][] = 59;
$elemy[1][] = 87;
$elemy[1][] = 61;
$elemy[1][] = 15;*/
// #################################################

// #BIGGEST Y ELEMENT###############################
$ymax = 0;
foreach ($elemy as $v)
	$ymax = max($ymax,ceil(max($v)));
// #################################################

// #CALCULATING THE DIFFERENCE######################
$dif = array_sum($elemy[0]) - array_sum($elemy[1]);
// #################################################

// #POPULATING GRAPH################################
foreach ($elemy as $k => $v)
	foreach ($v as $kk => $vv)
	{
    	$g->xValue[$k][] = $elemx[$k][$kk];
    	$g->DataValue[$k][] = $vv;
	}
// #################################################

// #SETTING GRAPH PARAMETERS########################
$g->Title = "График упоминаний";
$g->SubTitle = "";
$g->Width = ($xcount*45) + 75;
$g->Height = 300;
$g->ShowBullets = TRUE;

$g->LineShowCaption = FALSE; // TO BE FIXED YET
$g->LineShowTotal = FALSE;   // DEPENDS ON LineShowCaption to be TRUE
$g->LineCaption[0] = "Period 1";
$g->LineCaption[1] = "Period 2";
$g->LineCount = 2;

$g->xCount = $xcount;
$g->xCaption = "Упоминайний: ".$dif;
$g->xShowValue = TRUE;
$g->xShowGrid = TRUE;

$g->yCount = 10;
$g->yCaption = "Упоминаний за день:";
$g->yShowValue = TRUE;
$g->yShowGrid = TRUE;

$g->DataDecimalPlaces = 0;
$g->DataMax = $ymax;
$g->DataMin = 0;
$g->DataShowValue = TRUE;
// #################################################
//$fp = fopen('data1.txt', 'w');
//fwrite($fp, json_encode($_SERVER['argv']));
//fclose($fp);// #ITS DRAWING TIME################################
$g->MakeLinePointChart();
//print_r($_SERVER['argv']);
// #################################################

?>
