<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('dup_func.php');

$db=new database();
$db->connect();

$order_delta = $_SERVER['argv'][1];
$debug_mode = $_SERVER['argv'][2];
$fp = fopen('/var/www/pids/dj' . $order_delta . '.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

error_reporting(0);

while (1)
{
	$ressec = $db->query('SELECT * FROM blog_orders WHERE (similar_text!=0 OR order_date>'.(time()-86400*3).')' . $condition . '
	                    AND MOD (order_id, ' . $_SERVER['argv'][2] . ') = ' . $_SERVER['argv'][1] . ' AND user_id!=145 AND (user_id!=194 AND order_id!=712) AND ut_id!=0 AND user_id!=0');
	// $ressec=$db->query('SELECT * FROM blog_orders WHERE order_id=6906');
	while ($blog = $db->fetch($ressec))
	{
		if ($blog['order_end']>time())
		{
			if ($blog['similar_text']>1)
			{
				if (($blog['order_end']-7*86400)>$blog['order_start']) 
				{
					$start=time()-7*86400;
					$end=time();
				}
				else 
				{
					$start=$blog['order_start'];
					$end=time();
				}
			}
			else
			{
				$start=$blog['order_start'];
				$end=time();
			}
		}
		else
		{
			// if ($blog['similar_text']>1) continue;
			// else 
			{
				$start=$blog['order_start'];
				$end=$blog['order_end'];
			}
		}
		$offset=0;
		do
		{
		    $sql = "SELECT post_id, post_content, post_time, ful_com_post, parent
		             FROM blog_post as p
		             LEFT JOIN blog_full_com as f ON p.post_id=f.ful_com_post_id
		             WHERE p.order_id=".$blog['order_id']." 
		             AND ((post_time>" . $start . " AND post_time<" . $end . ") OR parent=0)
		             ORDER BY post_time ASC LIMIT ".intval($offset).",5000";
		    $offset+=1000;
		             echo $sql;
		             // die();
		    $res = $db->query($sql);
		    $i=0;
		    unset($mpost);
		    while ($post=$db->fetch($res))
		    {
		    	$mpost[$i]['post_id']=$post['post_id'];
		    	$mpost[$i]['hashes']=get_hashes(mb_strtolower($post['post_content'].' '.$post['ful_com_post'],'UTF-8'));
		    	$mpost[$i]['parent']=$post['parent'];
		    	$mpost[$i]['time']=$post['post_time'];
		    	$i++;
		    }
		    // die();
		    for ($i=0;$i<count($mpost)-1;$i++)
		    {
		    	echo '.';
		    	$not_similar=0;
		    	if ($mpost[$i]['parent']!=0) continue;
		    	for ($j=0;$j<count($mpost)-1;$j++)
		    	{
		    		if ($i==$j) continue;
		    		// echo "\n".$i.' !'.$j.'! '.check_similar($mpost[$i]['hashes'],$mpost[$j]['hashes'])."\n";
		    		if (check_similar($mpost[$i]['hashes'],$mpost[$j]['hashes'])>0.9)
		    		{
		    			if ($mpost[$j]['parent']!=0) 
						{
							// echo '!';
							$db->query('UPDATE blog_post SET parent='.$mpost[$j]['parent'].' WHERE post_id='.$mpost[$i]['post_id']);
							$mpost[$i]['parent']=$mpost[$j]['parent'];
							$not_similar=0;
							break;
						}
		    			else 
						{
							if ($mpost[$i]['time']<$mpost[$j]['time'])
							{
								// echo '+';
								$db->query('UPDATE blog_post SET parent='.$mpost[$i]['post_id'].' WHERE post_id='.$mpost[$i]['post_id']);
								$db->query('UPDATE blog_post SET parent='.$mpost[$i]['post_id'].' WHERE post_id='.$mpost[$j]['post_id']);
								$mpost[$i]['parent']=$mpost[$i]['post_id'];
								$mpost[$j]['parent']=$mpost[$i]['post_id'];
								$not_similar=0;
							}
							else
							{
								// echo '-';
								$db->query('UPDATE blog_post SET parent='.$mpost[$j]['post_id'].' WHERE post_id='.$mpost[$i]['post_id']);
								$db->query('UPDATE blog_post SET parent='.$mpost[$j]['post_id'].' WHERE post_id='.$mpost[$j]['post_id']);
								$mpost[$i]['parent']=$mpost[$j]['post_id'];
								$mpost[$j]['parent']=$mpost[$j]['post_id'];
								$not_similar=0;
							}
							break;
						}
		    		}
		    		else $not_similar=1;
		    	}
		    	if ($not_similar==1) 
				{
					// echo '*';
					$db->query('UPDATE blog_post SET parent='.$mpost[$i]['post_id'].' WHERE post_id='.$mpost[$i]['post_id']);
					$mpost[$i]['parent']=$mpost[$i]['post_id'];
				}
		    }
		}
		while ($db->num_rows($res)!=0);
		$db->query('UPDATE blog_orders SET similar_text=' . @mktime(0, 0, 0, date("n"), date("j"), date("Y")) . ' WHERE order_id='.$blog['order_id']);
	}
	echo 'sleep...';
	sleep(600);
}

?>