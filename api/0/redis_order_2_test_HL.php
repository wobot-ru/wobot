<?
require_once('/var/www/api/0/redis_order2.php');

var_dump(get_statistics('SELECT post_time,p.post_id FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id  LEFT JOIN blog_full_com as f ON p.post_id=f.ful_com_post_id WHERE  order_id=6909 AND post_time>=1397332800 AND post_time<1400270400 AND (p.post_nastr=1 or p.post_nastr=-1 or p.post_nastr=0) ORDER BY p.post_time ASC'));

?>
