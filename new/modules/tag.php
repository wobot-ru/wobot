<?

//print_r($_GET);
$tagsall=json_decode(urldecode($_GET['tall']),true);
//print_r($tagsall);
$tags=json_decode(urldecode($_GET['tags']),true);
$id=$_GET['id'];
//echo $id;
$order_id=$_GET['order_id'];
//$_POST['order_id']=$_GET['order_id'];
$respost=$db->query('SELECT * FROM blog_post WHERE post_id='.$id);
unset($tags);
while($ortag = $db->fetch($respost))
{
	$tagp1=$ortag['post_tag'];
}
$tags=explode(',',$tagp1);
echo '<html><head>
	<link href=\'/css/wobot_lk.css\' rel=\'stylesheet\' type=\'text/css\' /> 
			    <link href=\'/img/favicon_lk.gif\' rel=\'shortcut icon\' /> 
			    <link href=\'/css/details_lk.css\' rel=\'stylesheet\' type=\'text/css\' /> 
	<link href=\'/css/old_details_lk.css\' rel=\'stylesheet\' type=\'text/css\' />	
	<script type="text/javascript" src="/js/jquery.js"></script> 
	<script type="text/javascript" src="/js/jquery-ui.js"></script></head><body>';
echo '<table>';
foreach ($tagsall as $kk => $item)
{
	if (in_array($kk,$tags))
	{
		echo '<tr><td><input value="'.$item.'" type="checkbox" style="margin: 5px;" checked class="inp'.$id.'" id="inp'.$id.'_'.$kk.'_'.$item.'" onchange="var i=0; var vm=\'<a href=\\\'#\\\' onclick=\\\'return false;\\\' style=\\\'display: inline;\\\' class=\\\'vtip\\\' title=\\\'Теги\\\'><img src=\\\'/img/tag.png\\\' height=\\\'15\\\' border=\\\'0\\\' style=\\\'margin-top: 0px;\\\'></a> \'; var entags=\'\'; $(\'.inp'.$id.'\').each(function() { if ($(this).attr(\'checked\')==true){ if (i>0) {vm+=\',\';} i++; vm+=this.value;  entags=entags+\'|\'+$(this).attr(\'id\');} });  var tags=$(\'#inp'.$id.'_'.$kk.'_'.$item.'\').attr(\'checked\'); $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order_id.'&id='.intval($id).'&tags='.$item.'&taglinks='.urlencode(json_encode($tagsall)).'&tagsall='.urlencode(json_encode($tags)).'&valuet=\'+tags+\'&retags=\'+entags, success: function(msg1){  } }); if (vm.length!=164) {parent.$(\'#tt'.$id.'\').html(vm);} else {parent.$(\'#tt'.$id.'\').html(\'\');} ">&nbsp;'.$item.'</td></tr>';
	}
	else
	{
		//$tagp1.='<tr><td><input type="checkbox" class="inp'.$id.'" id="inp'.$id.'_'.$kk.'_'.$item.'" onchange="var tags=$(\\\'#inp'.$id.'_'.$kk.'\\\').attr(\\\'checked\\\'); $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&id='.intval($id).'&tags='.$item.'&tagsall='.urlencode(json_encode($tags)).'&valuet=\\\'+tags, success: function(msg1){ alert(msg1); } });">'.$item.'</td></tr>';
		echo '<tr><td><input value="'.$item.'" type="checkbox" style="margin: 5px;" class="inp'.$id.'" id="inp'.$id.'_'.$kk.'_'.$item.'" onchange="var i=0; var vm=\'<a href=\\\'#\\\' onclick=\\\'return false;\\\' style=\\\'display: inline;\\\' class=\\\'vtip\\\' title=\\\'Теги\\\'><img src=\\\'/img/tag.png\\\' height=\\\'15\\\' border=\\\'0\\\' style=\\\'margin-top: 0px;\\\'></a> \'; var entags=\'\'; $(\'.inp'.$id.'\').each(function() { if ($(this).attr(\'checked\')==true){ if (i>0) {vm+=\',\';} i++; vm+=this.value;  entags=entags+\'|\'+$(this).attr(\'id\');} });  var tags=$(\'#inp'.$id.'_'.$kk.'_'.$item.'\').attr(\'checked\'); $.ajax({ type: \'POST\', url: \'/new/ajax\', data: \'order_id='.$order_id.'&id='.intval($id).'&tags='.$item.'&taglinks='.urlencode(json_encode($tagsall)).'&tagsall='.urlencode(json_encode($tags)).'&valuet=\'+tags+\'&retags=\'+entags, success: function(msg1){  } }); if (vm.length!=164) {parent.$(\'#tt'.$id.'\').html(vm);} else {parent.$(\'#tt'.$id.'\').html(\'\');}">&nbsp;'.$item.'</td></tr>';
	}
}

echo '</table></body></html>';

?>