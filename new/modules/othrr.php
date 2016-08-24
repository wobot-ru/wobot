<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/new/com/auth.php');
require_once('/var/www/com/loc.php');
$db = new database();
$db->connect();
$qw=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' LIMIT 1');
$order=$db->fetch($qw);
//print_r($order);
echo '
<head>
        <link rel="stylesheet" href="/new_css/css/blueprint/screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="/new_css/css/blueprint/print.css" type="text/css" media="print">
<!--        <link rel="stylesheet" type="text/css" href="/new_css/files/example.css"></link>-->

          <!--[if lt IE 8]>
            <link rel="stylesheet" href="/new_css/css/blueprint/ie.css" type="text/css" media="screen, projection">
          <![endif]-->

        <link rel="stylesheet" href="/new_css/css/style.css" type="text/css">
        <link rel="stylesheet" href="/new_css/css/form.css" type="text/css">
		<script type="text/javascript" src="/new_js/js/jquery.js"></script>
		<script type="text/javascript" src="/new_js/js/ZeroClipboard.js"></script>
		<script type="text/javascript" src="/new_js/js/jquery.zclip.js"></script>
		 <script language="Javascript">

		$(document).ready(function(){
			var strr=\'';
			if ($_GET['res']=='resources')
			{
				$res=$order['order_src'];
				$res=json_decode($res,true);
				foreach ($res as $key => $item)
				{
					echo ' '.$key.' '.$item;
				}			
			}
			else
			if ($_GET['res']=='cities')
			{
				$res=$order['order_metrics'];
				$res=json_decode($res,true);
				arsort($res['location']);
				foreach ($res['location'] as $key => $item)
				{
					if ($key!='')
					{
						echo ' '.$key.' '.$item;
					}
				}
			}
			else
			if ($_GET['res']=='speakers')
			{
				$metrics=$order['order_metrics'];
				$metrics=json_decode($metrics,true);
				//print_r($res['speakers']);
				//arsort($res['location']);
				$i=0;
				foreach ($metrics['speakers']['site'] as $key => $item)
				{
					$i++;
					//echo $item.' ';
					$mpspeak='';
					if ($item=='twitter.com')
					{
						//echo 'gg';
						$text_link='http://twitter.com/'.$metrics['speakers']['nick'][$key];
						$text_nick=$metrics['speakers']['nick'][$key];
						//echo $text_nick.'|';
						$mpspeak.=' '.$metrics['speakers']['nick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
					}
					else
					if ($item=='livejournal.com')
					{
						$text_link='http://'.$metrics['speakers']['nick'][$key].'.livejournal.com/';
						$text_nick=$metrics['speakers']['nick'][$key];
						$mpspeak.=' '.$metrics['speakers']['nick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
					}
					else
					if ($item=='vkontakte.ru')
					{
						$text_link='http://vkontakte.ru/id'.$metrics['speakers']['nick'][$key];
						$text_nick=$metrics['speakers']['rnick'][$key];
						$mpspeak.=' '.$metrics['speakers']['rnick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
					}
					else
					if ($item=='facebook.com')
					{
						$text_link='http://facebook.com/'.$metrics['speakers']['nick'][$key];
						$text_nick=$metrics['speakers']['rnick'][$key];
						$mpspeak.=' '.$metrics['speakers']['rnick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
					}
					echo $mpspeak;
					//echo '<tr><td>'.$i.'</td><td><a href="'.$text_link.'">'.$text_nick.'</a></td><td>'.intval($metrics['speakers']['posts'][$key]).'</td></tr>';
				}
			}
			else
			if ($_GET['res']=='promotion')
			{
				$metrics=$order['order_metrics'];
				$metrics=json_decode($metrics,true);
				//print_r($res['speakers']);
				//arsort($res['location']);
				$i=0;
				foreach ($metrics['promotion']['site'] as $key => $item)
				{
					if ($item=='twitter.com')
					{
						$text_link='http://twitter.com/'.$metrics['promotion']['nick'][$key];
						$text_nick=$metrics['promotion']['nick'][$key];
						$mpprom.=' '.$metrics['promotion']['nick'][$key].' '.intval($metrics['promotion']['readers'][$key]);
					}
					else
					if ($item=='livejournal.com')
					{
						$text_link='http://'.$metrics['promotion']['nick'][$key].'.livejournal.com/';
						$text_nick=$metrics['promotion']['nick'][$key];
						$mpprom.=' '.$metrics['promotion']['nick'][$key].' '.intval($metrics['promotion']['readers'][$key]);
					}
					echo $mpprom;
					$mpprom='';
				}
			}
			else
			if ($_GET['res']=='words')
			{
				$metrics=$order['order_metrics'];
				$metrics=json_decode($metrics,true);
				//print_r($res['speakers']);
				//arsort($res['location']);
				$i=0;
				foreach ($metrics['topwords'] as $key => $item)
				{
					echo ' '.$key.' '.$item;
				}
			}
			echo '\';
			$("#copy-button").zclip({
			    path: "http://bmstu.wobot.ru/new_js/js/ZeroClipboard.swf",
				afterCopy: function(){ return false; },
			    copy: function(){
				return strr;
				}
			});

		});

		 </script>

</head>
';
if ($_GET['res']=='resources')
{
	//print_r($speakers['speakers']);
	$i=0;
	echo '

		<div id="speakersshowall" class="span-7 last" style="margin: 5px;">
		                             <h4 class="span-3">Ресурсы</h4>
		                             <div class="row clear"></div>
		                            <div class="text-black">
		                            <div class="row span-6 last text-lightgrey bold ">
		                            <p class="span-1 text-right">№</p>
		                            <p class="span-3">Ресурс</p>
		                            <p class="span-1 last">постов</p>
		                            </div>
		                            <div class="tableheaderborder clear"></div>

		                            <div class="tablecontent span-7 last scroll">';
								foreach ($res as $key => $item)
								{
									$i++;
									echo '<div class="clear"><p class="span-1 text-right">'.($i).'</p><a href="http://'.$key.'" target="_blank" class="span-3 ">'.$key.'</a><p class="span-2 last">'.$item.'</p></div>';
								}		
		echo '
									</div>
		                              </div>
		                             <div class="row clear"></div>
		                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" id="copy-button">копировать в буфер</a></div>
		                      </div>
	';
}
else
if ($_GET['res']=='cities')
{

	//print_r($res['location']);
	$i=0;
	echo '
		<div id="speakersshowall" class="span-7 last" style="margin: 5px;">
		                             <h4 class="span-3">Ресурсы</h4>
		                             <div class="row clear"></div>
		                            <div class="text-black">
		                            <div class="row span-6 last text-lightgrey bold ">
		                            <p class="span-1 text-right">№</p>
		                            <p class="span-3">Город</p>
		                            <p class="span-1 last">постов</p>
		                            </div>
		                            <div class="tableheaderborder clear"></div>

		                            <div class="tablecontent span-7 last scroll" style="margin-left: 10px;"><table style="width: 250px;">';
								foreach ($res['location'] as $key => $item)
								{
									if ($key!='')
									{
										$i++;
										echo '<tr bgcolor="#fff"><td bgcolor="#fff" width="10">'.($i).'</td><td bgcolor="#fff" width="100">'.$key.'</td><td bgcolor="#fff">'.$item.'</td></tr>';
									}
								}		
		echo '</table>
									</div>
		                              </div>
		                             <div class="row clear"></div>
		                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" onclick="" id="copy-button">копировать в буфер</a></div>
		                      </div>
	';
}
else
if ($_GET['res']=='speakers')
{

	//print_r($res['location']);
	$i=0;
	echo '
		<div id="speakersshowall" class="span-7 last" style="margin: 5px;">
		                             <h4 class="span-3">Ресурсы</h4>
		                             <div class="row clear"></div>
		                            <div class="text-black">
		                            <div class="row span-6 last text-lightgrey bold ">
		                            <p class="span-1 text-right">№</p>
		                            <p class="span-3">Город</p>
		                            <p class="span-1 last">постов</p>
		                            </div>
		                            <div class="tableheaderborder clear"></div>

		                            <div class="tablecontent span-7 last scroll" style="margin-left: 10px;"><table style="width: 250px;">';
								$i=0;
								foreach ($metrics['speakers']['site'] as $key => $item)
								{
									$i++;
									//echo $item.' ';
									if ($item=='twitter.com')
									{
										//echo 'gg';
										$text_link='http://twitter.com/'.$metrics['speakers']['nick'][$key];
										$text_nick=$metrics['speakers']['nick'][$key];
										//echo $text_nick.'|';
										$mpspeak.=' '.$metrics['speakers']['nick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
									}
									else
									if ($item=='livejournal.com')
									{
										$text_link='http://'.$metrics['speakers']['nick'][$key].'.livejournal.com/';
										$text_nick=$metrics['speakers']['nick'][$key];
										$mpspeak.=' '.$metrics['speakers']['nick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
									}
									else
									if ($item=='vkontakte.ru')
									{
										$text_link='http://vkontakte.ru/id'.$metrics['speakers']['nick'][$key];
										$text_nick=$metrics['speakers']['rnick'][$key];
										$mpspeak.=' '.$metrics['speakers']['rnick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
									}
									else
									if ($item=='facebook.com')
									{
										$text_link='http://facebook.com/'.$metrics['speakers']['nick'][$key];
										$text_nick=$metrics['speakers']['rnick'][$key];
										$mpspeak.=' '.$metrics['speakers']['rnick'][$key].' '.intval($metrics['speakers']['posts'][$key]);
									}
									if (mb_strpos($text_nick,' ')!==false)
									{
										$text_nick=preg_replace('/([А-Яа-яA-Za-z])[А-Яа-яA-Za-z]*?\s([А-Яа-яA-Za-z]*)/isu','$1. $2',$text_nick);
									}
									if (mb_strlen($text_nick,'UTF-8')>13)
									{
										$text_nick=mb_substr($text_nick,0,11,'UTF-8').'...';
									}
									echo '<tr><td>'.$i.'</td><td><a href="'.$text_link.'">'.$text_nick.'</a></td><td>'.intval($metrics['speakers']['posts'][$key]).'</td></tr>';
								}		
		echo '</table>
									</div>
		                              </div>
		                             <div class="row clear"></div>
		                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" onclick="" id="copy-button">копировать в буфер</a></div>
		                      </div>
	';
}
if ($_GET['res']=='promotion')
{

	//print_r($res['location']);
	$i=0;
	echo '
		<div id="speakersshowall" class="span-7 last" style="margin: 5px;">
		                             <h4 class="span-3">Ресурсы</h4>
		                             <div class="row clear"></div>
		                            <div class="text-black">
		                            <div class="row span-6 last text-lightgrey bold ">
		                            <p class="span-1 text-right">№</p>
		                            <p class="span-3">Город</p>
		                            <p class="span-1 last">постов</p>
		                            </div>
		                            <div class="tableheaderborder clear"></div>

		                            <div class="tablecontent span-7 last scroll" style="margin-left: 10px;"><table style="width: 250px;">';
								$i=0;
								foreach ($metrics['speakers']['site'] as $key => $item)
								{
									$i++;
									$mpprom='';
									if ($item=='twitter.com')
									{
										$text_link='http://twitter.com/'.$metrics['promotion']['nick'][$key];
										$text_nick=$metrics['promotion']['nick'][$key];
										$mpprom.=' '.$metrics['promotion']['nick'][$key].' '.intval($metrics['promotion']['readers'][$key]);
									}
									else
									if ($item=='livejournal.com')
									{
										$text_link='http://'.$metrics['promotion']['nick'][$key].'.livejournal.com/';
										$text_nick=$metrics['promotion']['nick'][$key];
										$mpprom.=' '.$metrics['promotion']['nick'][$key].' '.intval($metrics['promotion']['readers'][$key]);
									}
									//echo $mpprom;
									echo '<tr><td>'.$i.'</td><td><a href="'.$text_link.'">'.$text_nick.'</a></td><td>'.intval($metrics['promotion']['readers'][$key]).'</td></tr>';
								}		
		echo '</table>
									</div>
		                              </div>
		                             <div class="row clear"></div>
		                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" onclick="" id="copy-button">копировать в буфер</a></div>
		                      </div>
	';
}
if ($_GET['res']=='words')
{

	//print_r($res['location']);
	$i=0;
	echo '
		<div id="speakersshowall" class="span-7 last" style="margin: 5px;">
		                             <h4 class="span-3">Ресурсы</h4>
		                             <div class="row clear"></div>
		                            <div class="text-black">
		                            <div class="row span-6 last text-lightgrey bold ">
		                            <p class="span-1 text-right">№</p>
		                            <p class="span-3">Город</p>
		                            <p class="span-1 last">постов</p>
		                            </div>
		                            <div class="tableheaderborder clear"></div>

		                            <div class="tablecontent span-7 last scroll" style="margin-left: 10px;"><table style="width: 250px;">';
								$i=0;
								foreach ($metrics['topwords'] as $key => $item)
								{
									$i++;
									echo '<tr><td>'.$i.'</td><td><a href="#">'.$key.'</a></td><td>'.$item.'</td></tr>';
								}		
		echo '</table>
									</div>
		                              </div>
		                             <div class="row clear"></div>
		                             <div class="row clear"><a class="span-7 last text-right text-lightgrey" onclick="" id="copy-button">копировать в буфер</a></div>
		                      </div>
	';
}
?>