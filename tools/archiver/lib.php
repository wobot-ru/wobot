<?
//-------дампим blog_post----------
function get_blog_post($order_id)
{
	global $db;
	$dump_bp='INSERT INTO blog_post ';
	$qpost=$db->query('SELECT * FROM blog_post WHERE order_id='.$order_id);
	while ($post=$db->fetch($qpost))
	{
		$attr='';
		$items.=$glob_zp.'(';
		$glob_zp=',';
		$zap='';
		$zp='';
		foreach ($post as $key => $value)
		{
			$attr.=$zap.$key;
			$zap=',';
			$items.=$zp.'\''.addslashes($value).'\'';
			$zp=',';
		}
		$items.=')';
	}
	$items=preg_replace('/[\s\t]+/isu', ' ', $items);
	$items=preg_replace('/\s+/isu', ' ', $items);

	$dump_bp.='('.$attr.') VALUES '.$items;
	return $dump_bp;
}
//-----------дампим blog_full_com--------
function get_blog_full_com($order_id)
{
	global $db;
	$dump_bfc='INSERT INTO blog_full_com ';
	$qpost=$db->query('SELECT * FROM blog_full_com WHERE ful_com_order_id='.$order_id);
	while ($post=$db->fetch($qpost))
	{
		$attr='';
		$items.=$glob_zp.'(';
		$glob_zp=',';
		$zap='';
		$zp='';
		foreach ($post as $key => $value)
		{
			$attr.=$zap.$key;
			$zap=',';
			$items.=$zp.'\''.addslashes($value).'\'';
			$zp=',';
		}
		$items.=')';
	}
	$items=preg_replace('/[\s\t]+/isu', ' ', $items);
	$items=preg_replace('/\s+/isu', ' ', $items);

	$dump_bfc.='('.$attr.') VALUES '.$items;
	return $dump_bfc;
}

?>