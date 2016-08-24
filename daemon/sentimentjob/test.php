<?php




//736 - 13-10-2012 ; 04-11-2012  /
//737 - 01-03-2012 ; 04-11-2012  /
//738 - 01-03-2012 ; 04-11-2012  /
//1355 - 17-09-2012 ; 30-09-2012 /
//1356 - 17-09-2012 ; 30-09-2012 /
//1179 - 01-10-2012 ; 18-11-2012 /
//961 - 01-10-2012 ; 18-11-2012  /

echo "\n";
echo "\n";
echo "\n";
echo strtotime('01-12-2012')."!\n";
echo strtotime('30-09-2012')."!!\n";


//            $this->mstart = strtotime($mstart);
//            $this->mend = strtotime($mend);


/*
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FTOM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 736 AND p.post_time > 1350072000 AND p.post_time<1351972800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 737 AND p.post_time > 1330545600 AND p.post_time<1351972800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 1355 AND p.post_time > 1347825600 AND p.post_time<1348948800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 1355 AND p.post_time > 1347825600 AND p.post_time<1348948800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 1356 AND p.post_time > 1347825600 AND p.post_time<1348948800
 *
 *
 */
?>