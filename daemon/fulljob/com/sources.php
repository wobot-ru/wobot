<?

// require_once('/var/www/bot/kernel.php');

function twitter($content)
{
	// echo $content;
	// $regex='/<p class="js-tweet-text tweet-text\s*">(?<cont>.*?)<\/p>/isu';
	$regex='/<p class="[^\"]*?js-tweet-text tweet-text[^\"]*?"[\s\S]*?>(?<cont>.*?)<\/p>/isu';
	preg_match_all($regex, $content, $out);
	// print_r($out);
	return $out['cont'][0];
}

function vk($link,$content)
{
	// echo $content;
	if (preg_match('/wall-?\d+\_\d+$/isu',$link))
	{
		$regex='/wall(?<idus>\-?\d+)\_(?<id>\d+)$/isu';
		preg_match_all($regex, $link, $out);
		$regex='/<div id="wpt'.$out['idus'][0].'_'.$out['id'][0].'"><div class="(fw_reply_text|wall_post_text)">(?<cont>[\s\S]*?)<div class="fw_post_bottom"[\s\S]*?>
/isu';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]=='')
		{
			$regex='/<div class="pi_text">(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		return $out['cont'][0];
	}
	if (preg_match('/id\d+\?status\=\d+$/isu',$link))
	{
		$regex='/id(?<idus>\d+)\?status=(?<id>\d+)$/isu';
		preg_match_all($regex, $link, $out);
		$regex='/<div id="wpt'.$out['idus'][0].'_'.$out['id'][0].'"><div class="(fw_reply_text|wall_post_text)">(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $content, $out);
		return $out['cont'][0];
	}
	if (preg_match('/note\d+\_\d+/isu',$link))
	{
		$regex='/note(?<idus>\d+)\_(?<id>\d+)$/isu';
		preg_match_all($regex, $link, $out);
		$regex='/<div id="note'.$out['idus'][0].'_'.$out['id'][0].'" class="fwn_post wk_text">(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $content, $out);
		return $out['cont'][0];
	}
	if (preg_match('/video\-?\d+\_\d+/isu', $link))
	{
		$regex='/<div id=."mv_description." class=."mv_desc.">(?<cont>.*?)<.\/div>/isu';
		preg_match_all($regex, $content, $out);
		return $out['cont'][0];
	}
	if (preg_match('/page\-?\d+\_\d+/isu', $link))
	{
		$regex='/<div class="wk_text">(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $content, $out);
		//print_r($out);
		return $out['cont'][0];
	}
	if (preg_match('/topic\-?\d+\_\d+\?post=\d+/isu',$link))
	{
		$regex='/topic(?<idus>\-\d+)\_\d+\?post\=(?<id>\d+)/isu';
		preg_match_all($regex, $link, $out);
		$regex='/<div id="bp_data'.$out['idus'][0].'_'.$out['id'][0].'"><div class="bp_text">(?<cont>.*?)<div class="bp_bottom[\s\S]*?>/isu';
		preg_match_all($regex, $content, $out);
		return $out['cont'][0];
	}
}

function mailru($link,$content)
{
	if (preg_match('/torg\.mail\.ru/isu',$link))
	{
		//echo $content;
		$regex='/<section class="content_review js-review_item ">(?<cont>.*?)<\/section>/isu';
		preg_match_all($regex, $content, $out);
		$out['cont'][0]=preg_replace('/<div class="card__responses__response__information2__date">.*?<\/div>/isu', '', $out['cont'][0]);
		$out['cont'][0]=preg_replace('/<div class="card__responses__response__information2__usefulness">.*?<\/div>/isu', '', $out['cont'][0]);
		$out['cont'][0]=preg_replace('/<div class="card__responses__response__information__author">.*?<\/div>/isu', '', $out['cont'][0]);
		$out['cont'][0]=preg_replace('/<div class="card__responses__response__information">.*?<\/div>/isu', '', $out['cont'][0]);
		$out['cont'][0]=preg_replace('/>Подробнее<\//isu', '', $out['cont'][0]);
		if ($out['cont'][0]!='') return $out['cont'][0];
	}
	if (preg_match('/\.html\?thread\=.*$/isu', $link))
	{
		$regex='/\.html\?thread\=(?<id>.*)$/isu';
		preg_match_all($regex, $link, $outid);
		$regex='/<table id="cit_'.$outid['id'][0].'" class="mb20">(?<cont>.*?)<\/table>/isu';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]!='') return $out['cont'][0];
		$regex='/<span id="cit_'.$outid['id'][0].'">(?<cont>.*?)<\/span>/isu';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]!='') return $out['cont'][0];
	}
	if (preg_match('/soft\.mail\.ru/isu',$link))
	{
		//echo $content;
		$regex='/<div class="article-body">(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]!='') return $out['cont'][0];
	}
	// echo $content;
	$regex='/<td class="content">\s*<div>(?<cont>.*?)<\/div><\/td>/isu';
	preg_match_all($regex, $content, $out);
	if ($out['cont'][0]!='') return $out['cont'][0];
	$regex='/\/(?<id>[a-z0-9]+)\.html/isu';
	preg_match_all($regex, $link, $out);
	$regex='/<div[^<]*?id="post_te[sx]t_'.$out['id'][0].'"[^<]*?>(?<cont>.*?)<\/div>\s*<\/(td|div)>/isu';
	preg_match_all($regex, $content, $out);
	$out['cont'][0]=preg_replace('/<a target=[\'\"]\_blank[\'\"]>Читать полностью »<\/a>/isu', ' ', $out['cont'][0]);
	// print_r($out);
	return $out['cont'][0];
}

function blogspot($content)
{
	// echo $content;
	$content=preg_replace('/<style[^<]*?>.*?<\/style>/isu', ' ', $content);
	$content=preg_replace('/<scrypt[^<]*?>.*?<\/scrypt>/isu', ' ', $content);
	$regex='/<div class=[\'\"]postcontent post\-body[\'\"]>(?<cont>.*?)<\/div>/isu';
	preg_match_all($regex, $content, $out);
	if ($out['cont'][0]!='') return $out['cont'][0];
	$regex='/<div[^<]*?class=[\'\"]post\-body entry\-content[\'\"][^<]*?>(?<cont>.*?)<\/div>\s+<div class=\'post-footer\'>/isu';
	preg_match_all($regex, $content, $out);
	$out['cont'][0]=preg_replace('/(<ol class="commentlist">.*?<\/ol>|<h2 id="comments">.*?<\/h2>|<div id="rl-\d+">.*?<\/div>|<div id="[a-z][a-z]-\d+">.*?<\/div>)/isu', '', $out['cont'][0]);
	$out['cont'][0]=preg_replace('/<script[^<]*?>.*?<\/script>/isu','',$out['cont'][0]);
	return $out['cont'][0];
}

function ya($content)
{
	// echo $content;
	$regex='/<div class="b-text">(?<cont>.*?)<noindex>/isu';
	preg_match_all($regex, $content, $out);
	$out['cont'][0]=preg_replace('/<b>\s*\(\s*<a[^<]*?>читать дальше<\/a>\s*\)\s*<\/b>/isu','',$out['cont'][0]);
	if ($out['cont'][0]!='') return $out['cont'][0];
	$regex='/<td class="b-review__right">(?<cont>.*?)<\/td>/isu';
	preg_match_all($regex, $content, $out);
	$out['cont'][0]=preg_replace('/<b>\s*\(\s*<a[^<]*?>читать дальше<\/a>\s*\)\s*<\/b>/isu','',$out['cont'][0]);
	return $out['cont'][0];	
}

function liveinternet($link,$content)
{
	// echo $content;
	// echo '-------------'."\n".'-------------'."\n".'-------------'."\n".'-------------'."\n";
	$regex='/\#BlCom(?<id>\d+)/isu';
	preg_match_all($regex, $link, $out);
	if ($out['id'][0]!='')
	{
		$id=$out['id'][0];
		$regex='/<script>_spamlink\('.$out['id'][0].'\)\;<\/script><\/span>\s+<div class="GL_MAR10T GL_MAR10B">(?<cont>.*?)<\/div>/is';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]=='')
		{
			$regex='/<div class="BlInnrcomInnrRightInnrText" id="BlInnrcomCont'.$id.'">(?<cont>.*?)<\/div>/is';
			preg_match_all($regex, $content, $out);
		}
		//print_r($out);
		return preg_replace('/(прочитать целиком|читать далее|смотреть далее|смотреть всю историю|в свой цитатник или сообщество\!)/isu',' ',iconv('windows-1251','UTF-8',$out['cont'][0]));
	}
	else
	{
		$regex='/\/post(?<id>\d+)/isu';
		preg_match_all($regex, $link, $out);
		$content=preg_replace('/<div[^<]*?><\/div>/is','',$content);
		// echo $content;
		$regex='/<div class="GL_MAR10T  GL_MAR10B MESS">(?<cont>.*?)(<span id="DI_TAG_'.$out['id'][0].'">|<div class="li-earlap li-earlap_Narrow">)/is';
		preg_match_all($regex, $content, $out);
		// print_r($out);
		//echo $content;
		return preg_replace('/(прочитать целиком|читать далее|смотреть далее|смотреть всю историю|в свой цитатник или сообщество\!)/isu',' ',iconv('windows-1251','UTF-8',$out['cont'][0]));
		//$regex='//is';
	}
}

function baby($link,$content)
{
	$regex='/(?<id>\d+)\_\d+\_\d+/isu';
	preg_match_all($regex, $link, $out);
	if ($out['id'][0]!='')
	{
		$regex='/<table id="comment'.$out['id'][0].'" class="block_comments">.*?<div class="rounded-content">(?<cont>.*?)<div class="uderblock_right_2 color_gray fs12 fr">.*?<\/table>/isu';
		preg_match_all($regex, $content, $out);
		return $out['cont'][0];
	}
	else
	{
		// echo $content;
		$regex='/<div[^<]*?id="post_body"[^<]*?>(?<cont>.*?)<\/div>\s*<script type="text\/javascript">/isu';
		preg_match_all($regex, $content, $out);
		// print_r($out);
		return $out['cont'][0];
	}
}

function diary($link,$content)
{
	$regex='/#(?<id>\d+)/isu';
	preg_match_all($regex, $link, $out);
	if ($out['id'][0]!='')
	{
		// echo $content;
		// print_r($out);
		$regex='/<div class="singleComment  count(Second|First)" id="comment'.$out['id'][0].'">.*?<div class="paragraph">(?<cont>.*?)<\/div>/isu';
		// echo $regex;
		preg_match_all($regex, $content, $out);
		// print_r($out);
		$out['cont'][0]=preg_replace('/url\sзаписи/isu',' ',$out['cont'][0]);
		return $out['cont'][0];
	}
	else
	{
		$regex='/<div class="paragraph">(?<cont>.*?)<\/div>\s*<div class="clear">/isu';
		preg_match_all($regex, $content, $out);
		$out['cont'][0]=preg_replace('/url\sзаписи/isu',' ',$out['cont'][0]);
		return $out['cont'][0];
	}
}

function e1($link,$content)
{
	// echo $content;
	$regex='/\&i\=(?<id>\d+)/isu';
	preg_match_all($regex, $link, $out);
	$regex='/<div id="goto'.$out['id'][0].'" style="height : auto;"><\/div><\/div>.*?<b>дата:<\/b>.*?<br>(?<cont>.*?)<tr bgcolor="#ededed">/isu';
	preg_match_all($regex, $content, $out);
	return html_entity_decode($out['cont'][0],ENT_QUOTES,'UTF-8');
}

function gov($link,$content)
{
	if (preg_match('/fas-in-press\_\d+\.htm/isu',$link)) $content=iconv('windows-1251', 'UTF-8', $content);
	preg_match_all('/charset=([-a-z0-9_]+)/is',$content,$charset);
	//print_r($charset);
	if (($charset[1][0]!='') || ($charset[1][0]!='utf-8'))
	{
		if (mb_strtolower($charset[1][0],'UTF-8')!="utf-8")
		{
			$content=iconv($charset[1][0], "utf-8", $content);
		}
	}
	// echo $content;
	$regex='/<p style=" text-align: left;">(?<cont>.*?)<\/p>/isu';
	preg_match_all($regex, $content, $out);
	foreach ($out['cont'] as $key => $item)
	{
		if ($key==0) continue;
		$out['cont'][0].=' '.$item;
	}
	if ($out['cont'][0]=='')
	{
		$regex='/<div id=[\"\']div_full[\"\']>(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $content, $out);
	}
	if ($out['cont'][0]=='')
	{
		$regex='/<div class="content clear-block">(?<cont>.*?)<\/div>\s*<div class="clear-block">/isu';
		preg_match_all($regex, $content, $out);
	}
	if ($out['cont'][0]=='')
	{
		$regex='/<div class="news-detail">(?<cont>.*?)<div style="margin\: 5px 0px;">/isu';
		preg_match_all($regex, $content, $out);
	}
	if ($out['cont'][0]=='')
	{
		$regex='/<div class=[\"\']content_block[\"\'] style=[\"\']font-size: 12px;[\"\']>(?<cont>.*?)<p class=[\"\']content_caption_small[\"\']>/isu';
		preg_match_all($regex, $content, $out);
	}
	if ($out['cont'][0]=='')
	{
		$regex='/<div class="news-full">(?<cont>.*?)<noindex>/isu';
		preg_match_all($regex, $content, $out);
	}
	// print_r($out);
	return $out['cont'][0];
}

function blog($link,$content)
{
	// echo $content;
	$regex='/\/(?<id>\d+)\.htm/isu';
	preg_match_all($regex, $link, $out);
	$regex='/<div id="fav_\d+:'.$out['id'][0].'"[^<]*?>(?<cont>.*?)<\/div>/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function babyblog($link,$content)
{
	$regex='/<div class="post-box">(?<cont>.*?)<\/div>/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function molotok($link,$content)
{
	$regex='/<fieldset id="user_field"[^<]*?>(?<cont>.*?)<\/fieldset>/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function rutwit($content)
{
	// echo $content;
	$regex='/<span class=[\"\']tvit[\"\']>(?<cont>.*?)<span class=[\'\"]meta clearfix[\'\"]>/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function mamba($content)
{
	$regex='/<div class="post-content">(?<cont>.*?)<\/div>/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function foursquare($content)
{
	// echo $content;
	$regex='/<div class="newShout">(?<cont>.*?)<\/div>/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function materinstvo($link,$content)
{
	// echo $content;
	$regex='/\&p\=(?<id>\d+)/isu';
	preg_match_all($regex, $link, $out);
	$regex='/<table cellspacing="1" id="posttable_'.$out['id'][0].'">.*?<div class="postcolor">(?<cont>.*?)<div id=[\"\']thanks\_div\_'.$out['id'][0].'[\"\']/isu';
	preg_match_all($regex, $content, $out);
	return $out['cont'][0];
}

function livejournal($link,$content)
{

	if (!preg_match('/thread\=\d+/isu',$link))
	{
		$content=preg_replace('/<script[^>]*?>.*?<\/script>/isu','',$content);
		$content=preg_replace('/<style[^>]*?>.*?<\/style>/isu','',$content);
		$regex='/<div class=[\"\']entry-content[\"\']>(?<cont>.*?)<\/div>\s*<\/dd>/isu';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']text[\"\'][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<(td|table)[^<]*?class=[\"\']entry[\"\'][^<]*?>(?<cont>.*?)<div[^<]*?id=[\'\"]ljqrttopcomment[\'\"][^<]*?style=[\'\"]display: none;[\'\"]>/isu';
			preg_match_all($regex, $content, $out);
			//print_r($out);
			if ($out['cont'][1]!='') return $out['cont'][1];
		}
		if ($out['cont'][0]=='')
		{
			$regex='/(<table[^<]*?class=[\"\']entrybox[\"\'][^<]*?>|<div[^<]*?class=[\"\']asset-content[\"\'][^<]*?>)(?<cont>.*?)<div class=[\"\']quickreply[\"\']/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']entrytext[\"\'][^<]*?>(?<cont>.*?)<div[^<]*?id=[\"\']ljqrttopcomment[\"\']/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']entrytext[\"\'][^<]*?>(?<cont>.*?)<a[^<]*?name=[\'\"]cutid1\-end[\'\"]>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div class="b-singlepost-body">(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']b-singlepost-body[\"\'][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']entry-item[\"\'][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']entry\-body[\"\'][^<]*?>(?<cont>.*?)<p[^<]*?class=[\'\"]entry\-footer[\'\"]>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']entrycontent[\"\'][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']contentbody[\"\'][^<]*?>\s*<div[^<]*?class=[\"\']entrymeta[\"\']>.*?<\/div>(?<cont>.*?)<div[^<]*?class=[\"\']commentlinks[\"\']>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\"\']entry[\"\'][^<]*?>(?<cont>.*?)<\/div>/isu';
			// print_r($out);
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<index>(?<cont>.*?)<\/index>/isu';
			preg_match_all($regex, $content, $out);
		}
	}
	else
	{
		$regex='/<div[^<]*?class=[\'\"]comment-(text|body)[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $content, $out);
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]b-leaf-article[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/thread\=(?<id>\d+)/isu';
			preg_match_all($regex, $link, $out);
			$regex='/[\"\']dtalkid[\"\']:'.$out['id'][0].',.*?[\'\"]article[\'\"]:[\'\"](?<cont>.*?)[\'\"],[\'\"]/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/[\'\"]article[\'\"]:[\'\"](?<cont>.*?)[\'\"],[\'\"]/isu';
			preg_match_all($regex, $content, $out);
			// print_r($out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]commentreply[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/thread\=(?<id>\d+)/isu';
			preg_match_all($regex, $link, $out);
			$regex='/<div[^<]*?id=[\'\"]cmtbar'.$out['id'][0].'[\'\"][^<]*?>.*?<\/div>\s*<div[^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/thread\=(?<id>\d+)/isu';
			preg_match_all($regex, $link, $out);
			$regex='/<table[^<]*?id=[\'\"]cmtbar'.$out['id'][0].'[\'\"][^<]*?>.*?<\/table>.*?<div[^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]text_box[\'\"][^<]*?>(?<cont>.*?)<div[^<]*?class=[\'\"]clear[\'\"][^<]*?>/isu';
			preg_match_all($regex, $content, $out);
			// print_r($out);
			if ($out['cont'][1]!='') return $out['cont'][1];
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]entry(text|\-item)[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
			if ($out['cont'][1]!='') return $out['cont'][1];
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]comment-content[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]body[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]j\-c\-text[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
		if ($out['cont'][0]=='')
		{
			$regex='/<div[^<]*?class=[\'\"]commentText[\'\"][^<]*?>(?<cont>.*?)<\/div>/isu';
			preg_match_all($regex, $content, $out);
		}
	}
	$out['cont'][0]=preg_replace('/(<a[^<]*?rel\=[\'\"]tag[\'\"][^<]*?>.*?<\/a>\s*\,?|метки:|tags:)/isu',' ',$out['cont'][0]);
	$out['cont'][0]=preg_replace('/(<a[^<]*?rel\=[\'\"]nofollow[\'\"][^<]*?>leave a comment<\/a>)/isu',' ',$out['cont'][0]); //1
	#var_dump($out);
	return $out['cont'][0];
}

function instagram($link,$content)
{
	$regex='/\<script type=\"text\/javascript\"\>window\.\_sharedData\ \=\ (?<cont>.*?)\;\<\/script\>/isu';
	preg_match_all($regex, $content, $out);
	$result = json_decode($out['cont'][0], true);
	$result_total=$result['entry_data']['DesktopPPage']['0']['media']['caption']." ".$result['entry_data']['DesktopPPage']['0']['media']['location']['name'];
	return $result_total;
}


// echo strip_tags(twitter(parseUrl('http://twitter.com/Imoutozone/statuses/534435730232442880')));
// echo instagram('http://instagram.com/p/nn2JnuDOQC/',file_get_contents('http://instagram.com/p/nn2JnuDOQC/'));
/*
	if ($post['post_host']=='twitter.com') echo strip_tags(twitter(parseUrl($link)));
	if ($post['post_host']=='mail.ru') echo strip_tags(mailru($post['post_link'],iconv('windows-1251','UTF-8',parseUrl($link))));
	if ($post['post_host']=='vk.com') echo strip_tags(vk($post['post_link'],iconv('windows-1251','UTF-8',parseUrl($link))));
	if ($post['post_host']=='blogspot.com') echo strip_tags(blogspot(parseUrl($link)));
	if ($post['post_host']=='ya.ru') echo strip_tags(ya(parseUrl($link)));
	if ($post['post_host']=='liveinternet.ru') echo strip_tags(liveinternet($link,parseUrl($link)));
	if ($post['post_host']=='baby.ru') echo strip_tags(baby($link,parseUrl($link)));
	if ($post['post_host']=='diary.ru') echo strip_tags(diary($link,iconv('windows-1251','UTF-8',parseUrl($link))));
	if ($post['post_host']=='e1.ru') echo strip_tags(e1($link,iconv('windows-1251','UTF-8',parseUrl($link))));
	if ($post['post_host']=='gov.ru') echo strip_tags(gov($link,parseUrl($link)));
	if ($post['post_host']=='blog.ru') echo strip_tags(blog($link,parseUrl($link)));
	if ($post['post_host']=='babyblog.ru') echo strip_tags(babyblog($link,parseUrl($link)));
	if ($post['post_host']=='molotok.ru') echo strip_tags(molotok($link,parseUrl($link)));
	if ($post['post_host']=='rutwit.ru') echo strip_tags(rutwit(parseUrl($link)));
	if ($post['post_host']=='mamba.ru') echo strip_tags(mamba(parseUrl($link)));
	if ($post['post_host']=='foursquare.com') echo strip_tags(foursquare(parseUrl($link)));
	if ($post['post_host']=='materinstvo.ru') echo strip_tags(materinstvo($link,iconv('windows-1251','UTF-8',parseUrl($link))));
	if ($post['post_host']=='livejournal.com') echo stripslashes(preg_replace('/<[^<]*?>/isu',' ',livejournal($link,parseUrl($link))));
*/

?>