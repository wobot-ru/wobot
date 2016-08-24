<?php
/**
 * Created by PhpStorm.
 * User: hellzeine
 * Date: 20.06.14
 * Time: 12:30±±
 */
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php'); // 
require_once('/var/www/new/bot/kernel.php'); // 
require_once('auth.php');
require_once('func_export.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/com/sent.php');
require_once('/var/www/new/com/porter.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');
require_once( '/var/www/api/0/get_order_info.php');

$_POST['order_ids']= '6917,6909,6906,6908,6916'; // ,6909,6908
$_POST['start']= '06.05.2014';
$_POST['end']= '17.05.2014';
$_POST['format']= 'xls';
$_POST['stime']= '12.05.2014';
$_POST['etime']= '18.06.2014';
$_POST['positive']= 'true';
$_POST['negative']= 'true';
$_POST['neutral']= 'true';
$_POST['post_type']= 'null';
$_POST['md5']= '';
$_POST['words']= 'selected';
$_POST['location']= '';
$_POST['cou']= '';
$_POST['locations']= 'selected';
$_POST['res']= '';
$_POST['shres']= '';
$_POST['hosts']= 'selected';

// error_reporting(E_ERROR 1| E_WARNING | E_PARSE | E_NOTICE);
date_default_timezone_set ( 'Europe/Moscow' );
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
$db = new database();
$db->connect();
auth();
if (!$loged) die();
// добавить обработку foreach
//$_POST['order_id']=2074;
//$_POST['start']='01.10.2012';
//$_POST['end']='11.11.2012';
// тут у нас будет фрагмент разбивания квери для каждой из тем. для каждой темы свой квери
$morder_ids=explode(',',$_POST['order_ids']);
// далее для каждой из тем мы берём запрос и оным выдёргиваем query
// get_query(local_parameter_set)
// для каждой темы согласно выбитому query запрашиваем данные этой темы
$number_order=0;
foreach ($morder_ids as $order_id)
{
    $_POST['order_id']=$order_id;
    //  данные для 1 страницы
    $outinfo=get_order_info($order_id,$order_post);
    #print_r($outinfo['2']['din_post']);
    if ($order_id==$morder_ids[0]) $outmas[1]['order_day_column']=$outinfo['2']['din_post'][0];
    $outmas[1]['order_names'][$number_order][0]=$outinfo['1']['order_name'];
    $outmas[1]['order_names'][$number_order][1]=date('d.m.Y',$outinfo['1']['start']).'-'.date('d.m.Y',$outinfo['1']['end']);
    $outmas[1]['order_names'][$number_order][2]=$outinfo['1']['order_keyword'];
    // общие упоминания
    $outmas[1]['order_data_ment'][$number_order][0]=$outinfo['2']['din_post'][1];
    // уникальные упоминания
    $outmas[1]['order_data_ment_uniq'][$number_order][0]=$outinfo['2']['din_post'][2];
    // тональность подневно
    $outmas[1]['order_data_nastr_negative'][$number_order][0]=$outinfo['2']['din_nastr'][1];
    $outmas[1]['order_data_nastr_neutral'][$number_order][0]=$outinfo['2']['din_nastr'][2];
    $outmas[1]['order_data_nastr_positive'][$number_order][0]=$outinfo['2']['din_nastr'][3];
    // тональность суммарная
    $outmas[1]['order_tonal_mentionings'][$number_order][0]=$outinfo['2']['nastr_all'];
    // распределение упоминаний по хостам
    $outmas[1]['data_host_total_hosts'][$number_order][0]=$outinfo['2']['mhost'][0];
    $outmas[1]['data_host_total_hosts_mentionings'][$number_order][0]=$outinfo['2']['mhost'][1];
    // // распределение тональных упоминаний (Нег,Нейт,Поз) по хостам
    $outmas[1]['data_ment_tonal_host'][$number_order][0]=$outinfo['2']['mhost_proc_nastr'][0];
    $outmas[1]['data_ment_tonal_negative'][$number_order][0]=$outinfo['2']['mhost_proc_nastr'][1];
    $outmas[1]['data_ment_tonal_neutral'][$number_order][0]=$outinfo['2']['mhost_proc_nastr'][2];
    $outmas[1]['data_ment_tonal_positive'][$number_order][0]=$outinfo['2']['mhost_proc_nastr'][3];
    // // распределение упоминаний по типам ресурсов (СМИ, блог и так далее)
    $outmas[1]['order_resources_type_column'][$number_order][0]=$outinfo['2']['mtype_all'][0];
    $outmas[1]['order_resources_type_data'][$number_order][0]=$outinfo['2']['mtype_all'][1];
    // // распределение упоминаний по городам
    $outmas[1]['order_city_data_city_list'][$number_order][0]=$outinfo['2']['mloc_all'][0];
    $outmas[1]['order_city_data_data_itself'][$number_order][0]=$outinfo['2']['mloc_all'][1];
    // // распределение упоминаний по полу
    $outmas[1]['data_by_gender_gender_list'][$number_order][0]=$outinfo['2']['mgen_all'][0];
    $outmas[1]['data_by_gender_data_itself'][$number_order][0]=$outinfo['2']['mgen_all'][4];
    // // распределение упоминаний по возрасту
    $outmas[1]['order_data_by_age_age_list'][$number_order][0]=$outinfo['2']['mage_all']['0'];
    $outmas[1]['order_data_by_age_data_itself'][$number_order][0]=$outinfo['2']['mage_all'][4];
    // // распределение упоминаний по тегам
    $outmas[1]['order_tag_data_tag_list'][$number_order][0]=$outinfo['2']['mtag_all'][0];
    $outmas[1]['order_tag_data_data_itself'][$number_order][0]=$outinfo['2']['mtag_all'][4];

    $number_order++;
   # print_r($outinfo);

}
print_r($outmas[1]['data_ment_tonal_host']);
die();

$outmas[1]['data_ment_tonal_host']=$outmas[1]['data_ment_tonal_host'][1][0];

#$outmas[1]['order_data_by_age_age_list']=$outmas[1]['order_data_by_age_age_list'][0][0];
$outmas[1]['total_themes_count']=count($morder_ids); //count($morder_ids)

#die();
//die(json_encode($outmas));
//echo json_encode($outinfo);
// далее пихаем данные в Перл (там будет свой цикл обработки данных)
//$tset = json_encode($outinfo['6914']); 11

#print_r($outmas);
#die();

$descriptorspec=array(
    0 => array("pipe","r"),
    1 => array("pipe","w"),
    2 => array("file", "/tmp/error-output.txt", "a")
);
$cwd='/var/www/new/modules';
$end=array('');
$process=proc_open('perl /var/www/project/excel/xlsx_compare.pl',$descriptorspec,$pipes,$cwd,$end);
if (is_resource($process))
{
    fwrite($pipes[0], json_encode($outmas));
    fclose($pipes[0]);
    $fulltext=stream_get_contents($pipes[1]);
    $return_value=proc_close($process);
    $file_name=preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',($order['order_name']!=''?$order['order_name']:$order['order_keyword']));
    $file_name=preg_replace('/\_+/isu','_',$file_name);
    if (mb_strlen($file_name,'UTF-8')>100) $file_name=mb_substr($file_name,0,100,'UTF-8');
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=wobot_".
        date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name.".xlsx");
    echo $fulltext;
    //echo (stream_get_contents($pipes[1]);
} 
?>