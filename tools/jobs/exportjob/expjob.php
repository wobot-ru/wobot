<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/new/com/auth.php');
require_once('Classes/PHPExcel.php');
function getpng($par,$fname)
{
	$descriptorspec=array(
		0 => array("file","/var/www/tools/jobs/exportjob/gg.log","a"),
		1 => array("file","/var/www/tools/jobs/exportjob/gg.log","a"),
		2 => array("file","/var/www/tools/jobs/exportjob/gg.log","a")
		);

	$cwd='/Applications/MAMP/htdocs/xlstest/';
	$end=array();
	
	$process=proc_open('php /var/www/tools/jobs/exportjob/example_line.php '.$par.' > \''.$fname.'.png\' '.json_encode($descriptorspec).' &',$descriptorspec,$pipes,$cwd,$end);
	
	if (is_resource($process))
	{
		$return_value=proc_close($process);
		//echo $return_value;
	}
}
$tfar=array('true'=>'on','false'=>'off');
$db = new database();
$db->connect();
error_reporting(E_ALL);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set('Europe/London');
$resajaxq=$db->query("SELECT * FROM blog_export");
//echo "SELECT * from blog_export WHERE order_id=".$_GET['order_id']." AND export_time>".(time()-10);
//if (mysql_num_rows($resajaxq)!=0)
//{
	$order['ful_com']=0;
while($orderajaxq = $db->fetch($resajaxq))
{
	$id=$orderajaxq['export_id'];
	if (strpos($orderajaxq['name_file'],'xlsx')===false)
	{
		$or_inf=$db->query("SELECT * from blog_orders WHERE order_id=".$orderajaxq['order_id']." LIMIT 1");
		$infor = $db->fetch($or_inf);
		$metrics=json_decode($infor['order_metrics'],true);
		//echo $id;
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		$data=json_decode(urldecode($orderajaxq['export_arg']),true);
		$_POST=$data;
		$_POST['order_id']=$orderajaxq['order_id'];
		//print_r($_POST);
		foreach ($_POST as $inddd => $posttt)
		{
			if ((substr($inddd, 0, 4)=='res_'))
			{
				if ($posttt=='true')
				{
					//str_replace("_",".",$inddd);
					$resorrr[]=str_replace("_",".",substr($inddd,4));
				}
			}
		}
		foreach ($_POST as $indddloc => $postttloc)
		{
			if ((substr($indddloc, 0, 4)=='loc_'))
			{
				if ($indddloc!='loc_othr')
				{
					//str_replace("_",".",$inddd);
					$loc[]=substr($indddloc,4);
				}
			}
		}
		$_POST['positive']=$tfar[$_POST['positive']];
		$_POST['negative']=$tfar[$_POST['negative']];
		$_POST['neutral']=$tfar[$_POST['neutral']];
		foreach ($_POST as $inddd1 => $posttt1)
		{
			if ((substr($inddd1, 0, 2)=='tg'))
			{
				if (($posttt1!='false') && ($inddd1!='tgn'))
				{
					$tgv[]=substr($inddd1,2);
				}
				//$tgv[]=$posttt1;
			}
		}
		if ($_POST['tgn']=='true')
		{
			$tgv[]='na';
		}
		//print_r($tgv);
		$ressrc=$infor['order_src'];
		$sources=json_decode($ressrc, true);
		$src_count=count($sources);
		foreach ($sources as $i => $source)
		{
				$other+=$source;
		}		// Set properties
		$graph=$infor['order_graph'];
		$graph=(json_decode($graph,true));
		$graph=$graph['all'];
		//print_r($graph);
		foreach ($graph as $hn=>$gtime)
		{
			//if (in_array($hn,$av_host)||(($indother==1)&&(!in_array($hn,$all_host)))) {
				//$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;
				foreach($gtime as $year=>$years) {
					foreach($years as $month=>$months){
						foreach($months as $day=>$days){
							$timet[$year][$month][$day]+=$days;
						}
					}
				}
			//}
		}
		//print_r($timet);
		getpng(urlencode(json_encode($timet)),$orderajaxq['name_file']);
		$objPHPExcel->getProperties()->setCreator("WOBOT")
									 ->setLastModifiedBy("WOBOT")
									 ->setTitle("Export_WOBOT_".date('h:i:s d.m.Y'))
									 ->setSubject("Export XLSX")
									 ->setDescription("Export XLSX")
									 ->setKeywords("Export XLSX")
									 ->setCategory("XLSX");
									$where=get_isshow2();
									$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
									$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(60);
									$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
									$objPHPExcel->setActiveSheetIndex(0)
								            ->setCellValue('A26', 'Упоминания: '.$other)
								            ->setCellValue('B26', 'Ресурсов: '.$src_count)
								    		->setCellValue('C26', 'Аудитория: '.$metrics['value'])
								    		->setCellValue('D26', 'Engagement: '.$metrics['engagement'])
							            	->setCellValue('A27', 'Дата')
							            	->setCellValue('B27', 'Ресурс')
							    			->setCellValue('C27', 'Ссылка')
							    			->setCellValue('D27', 'Тип ресурса')
						            		->setCellValue('E27', 'Изб')
						            		->setCellValue('F27', 'Спам')
						    				->setCellValue('G27', 'Эмо')
						    				->setCellValue('H27', 'Ник')
					    					->setCellValue('I27', 'Аудитория')
					    					->setCellValue('J27', 'Текст упоминания');
		$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id '.(($order['ful_com']=='1')?"LEFT JOIN blog_full_com AS f ON f.ful_com_post_id=p.post_id ":" ").'WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' ORDER BY p.post_time DESC';
		//echo $query;
								$respost=$db->query($query);
								$qw1='SELECT * FROM blog_tag WHERE user_id='.$infor['user_id'];
								$restag=$db->query($qw1);
								while($ttg = $db->fetch($restag))
								{
									$mtag[$ttg['tag_tag']]=$ttg['tag_name'];
								}
								$ii=0;
								while($pst = $db->fetch($respost))
								{
									$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
									$outcash['time'][$ii]=$pst['post_time'];
									$outcash['content'][$ii]=$pst['post_content'];
									//$outcash['fcontent'][$ii]=$pst['ful_com_post'];
									$outcash['isfav'][$ii]=$pst['post_fav'];
									$outcash['nastr'][$ii]=$pst['post_nastr'];
									$outcash['isspam'][$ii]=$pst['post_spam'];
									$outcash['nick'][$ii]=$pst['blog_nick'];
									$outcash['tag'][$ii]=$pst['post_tag'];
									$outcash['comm'][$ii]=$pst['blog_readers'];
									$outcash['eng'][$ii]=$pst['post_engage'];
									$outcash['loc'][$ii]=$pst['blog_location'];
									$ii++;
								}
								//print_r($outcash);
								foreach ($outcash['link'] as $key => $llink)
								{
									$link=urldecode($llink);
									$time=$outcash['time'][$key];
									$content=$outcash['content'][$key];
									$fcontent=$outcash['fcontent'][$key];
									$comm=intval($outcash['comm'][$key]);
									$gn_time_start = microtime(true);
									$isfav=$outcash['isfav'][$key];
									$tag=$outcash['tag'][$key];
									$rtag=explode(',',$tag);
									$eng=$outcash['eng'][$key];
									$loc=$outcash['loc'][$key];
									$nick=$outcash['nick'][$key];
								    $hn=parse_url($link);
								    $hn=$hn['host'];
								    $ahn=explode('.',$hn);
								    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
									$hh = $ahn[count($ahn)-2];
									if (!in_array($hn,$resorrr))
									{
										continue;
									}
									if (($hn=="facebook.com") || ($hn=="vkontakte.ru"))
									{
										$type_re='социальная сеть';
									}
									else
									if ($hn=="mail.ru")
									{
										$type_re='новостной ресурс';
									}
									else
									if (($hn=="twitter.com") || ($hn=="rutvit.ru"))
									{
										$type_re='микроблог';
									}
									else
									{
										$type_re='форум или блог';
									}
									if ($isfav==1) $isfav='+';
									else $isfav='-';
									$nastr=$outcash['nastr'][$key];
									if ($nastr==1) $nastr='+';
									elseif ($nastr==-1) $nastr='-';
									else $nastr='0';
									$isspam=$outcash['isspam'][$key];
									if ($isspam==1) $isspam='+';
									else $isspam='-';
									$strtag='';
									$ll=-1;
									foreach ($rtag as $item)
									{
										$ll++;
										if ($ll>0)
										{
											$strtag.=', ';
										}
										if ($item!='')
										{
											$strtag.=$mtag[$item];
										}
									}
									$strtag=mb_substr($strtag,0,mb_strlen($strtag,"UTF-8")-2,"UTF-8");
									$objPHPExcel->setActiveSheetIndex(0)
								            ->setCellValue('A'.($key+28), date('d.m.Y',$time))
								            ->setCellValue('B'.($key+28), $hn)
								    		->setCellValue('C'.($key+28), $link)
							            	->setCellValue('D'.($key+28), $type_re)
								            ->setCellValue('E'.($key+28), $isfav)
								            ->setCellValue('F'.($key+28), $isspam)
								            ->setCellValue('G'.($key+28), $nastr)
								            ->setCellValue('H'.($key+28), $nick)
								            ->setCellValue('I'.($key+28), $comm)
								            ->setCellValue('J'.($key+28), $content)
							            	->setCellValue('K'.($key+28), $strtag);
								}
								$objDrawing = new PHPExcel_Worksheet_Drawing();
								$objDrawing->setName('Logo');
								$objDrawing->setDescription('Logo');
								$objDrawing->setPath('140_03:02:34 20.09.2011.png');
								$objDrawing->setHeight(450);
								$objDrawing->setCoordinates('A1');
								$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
								
								//$objPHPExcel->setActiveSheetIndex(0);
								header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
								header('Content-Disposition: attachment;filename="01simple.xlsx"');
								header('Cache-Control: max-age=0');
								$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
								$objWriter->save('php://output');
								//$objWriter->save('../../../new/data/export/g2.xlsx');
								//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
								
	}
}
?>