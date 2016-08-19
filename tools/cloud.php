<?
require_once('/var/www/userjob/com/config.php');
require_once('/var/www/userjob/com/func.php');
require_once('/var/www/userjob/com/db.php');
require_once('/var/www/userjob/bot/kernel.php');
$db = new database();
$db->connect();
$data=$db->query('SELECT * FROM blog_orders WHERE order_id=135');//.$_GET['id']);
//echo 'SELECT * FROM blog_orders WHERE id='.$_GET['id'];
$data1=$db->fetch($data);
//print_r($data1);
$mastop=json_decode($data1['order_metrics'],true);
//print_r($data1['order_metrics']);
echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Jimpl Tag Cloud</title>

	<!--  jquery core -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>
	<script src="jimpl_cloud.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(function() {
		$.fn.tagCloud = function(options) {

	        var defaults = {
	            separator: \',\',
	            randomize: true
	        };

	        var options = $.extend(defaults, options);

	        var randomize = function(){
	            return (Math.round(Math.random())-0.5);
	        };

	        var trim = function(text){
	            return text.replace(/^\s\s*/, \'\').replace(/\s\s*$/, \'\');
	        };

	        return this.each(function() {
	            var arr = $(this).text().split(options.separator);
	            if (options.randomize) arr.sort(randomize);
	            var words_arr = {};
	            $(arr).each(function(i){
	                word = trim(this);
	                words_arr[word] = words_arr[word]? words_arr[word] + 1 : 1;
	            });
				';
				/*foreach ($mastop['topwords'] as $key => $item)
				{
					echo 'words_arr[\''.$key.'\'] = words_arr[\''.$key.'\']? words_arr[\''.$key.'\'] + 1 : 1;';
				}*/
				echo '
	            var html = \'\';
	            $.each(words_arr, function(k, v) {
					v=Math.round(5+Math.random()*20);
	                html += \'<a href="#\' + k + \'" style="font-size: \' + v + \'px" title="\' + k + \'"><span>\' + k + \'</span></a>\';
	            });
	            $(this).html(html);
	        });
	    };
	    $(\'#tagcloud\').tagCloud({separator: \'.\'});
	});
	</script>
	<style type="text/css">
	a {
	    color: #92b22c;
	}

	.tagcloud {
	    width: 300px;
	    height: 200px;
	    margin-left: 100px;
	    margin-top: 100px;
		border: 1px solid black;
	}

	.tagcloud a{
	    color: #92b22c;
	    text-decoration:none;
	    border-bottom:1px dotted #92b22c;
	    padding-bottom:1px;
	    margin: 4px;
	}

	.tagcloud a:hover {
	    text-decoration:none;
	    border-bottom:2px solid #92b22c;
	}

	</style>
	</head>
	<body>
<!--	<div id="tagcloud" class="tagcloud">
	                Image converter. Image converter. Image resizer. Png to ico. Png to ico. Png to ico.
	                Ico to png. Png to jpg. Jpg to png. Jpg to png. Bmp to jpg. Jpg to bmp.
	                Jpg to gif. Gif to jpg. Png to gif. Png to gif. Gif to png. Online. Online. Free. Free. Free.
	            </div>-->
	<div id="tagcloud" class="tagcloud">
	';
foreach ($mastop['topwords'] as $key => $item)
{
	for ($i=0;$i<(($item-$item%10)/10);$i++)
	{
		echo $key.'. ';
	}
}
	echo'
	</div>
	</body>
	</html>
';

?>