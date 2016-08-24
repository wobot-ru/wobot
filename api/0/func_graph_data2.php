<?

function countsort($a, $b)
{
    foreach ($a as $key => $value)
    {
        $sum1+=$value;
    }
    foreach ($b as $key => $value)
    {
        $sum2+=$value;
    }
    return ($sum2 > $sum1) ? 1 : -1;
}

function get_linear_data($order_id,$start,$end,$ytype)
{
    global $wobot,$db,$_POST;
    //echo $step.' '.$ytype.' '.$xtype;
    // $step=1/24;
    // $ytype='post_tag';
    // $xtype='post_time';
    $difference = $end - $start;
    $difference_days = $difference/86400;
    // echo $difference_days;
    // die();
    // режем результаты на части согласно формату выходного графика
    if ($ytype=='post_tag')
    {
        $qtag=$db->query('SELECT * FROM blog_tag WHERE order_id='.$_POST['order_id']);
        while ($tag=$db->fetch($qtag))
        {
            $mktag[$tag['tag_tag']]=$tag['tag_name'];
        }
    }
    if (($difference_days == 1) or ($difference_days == 0)) { $step=1/24; $graphname="hour";}
    if ($difference_days > 1 and $difference_days <= 23) { $step=1; $graphname="day";}
    if ($difference_days > 23 and $difference_days <= 167) { $step=7; $graphname="week";}
    if ($difference_days > 167 and $difference_days <= 730) { $step=30; $graphname="month";}
    if ($difference_days > 730 and $difference_days <= 1825) { $step=90; $graphname="quarter";}
    if ($difference_days > 1825) { $split=180; $graphname="halfyear";}    
    $m_split['post_host']='post_host';
    $m_split['blog_location']='blog_location';
    $m_split['blog_gender']='blog_gender';
    $m_ytype['post_id']='post_id';
    $m_ytype['blog_id']='b.blog_id';
    $m_ytype['blog_readers']='blog_readers,b.blog_id';
    $m_ytype['post_engage']='post_engage';
    $m_ytype['post_retweets']='post_advengage';
    $m_ytype['post_comments']='post_advengage';
    $m_ytype['post_likes']='post_advengage';
    $m_ytype['post_host']='post_host';
    $m_ytype['post_tag']='post_tag';
    $qw = get_query();
    $qw=preg_replace('/\s\*\s/isu',' post_time,'.$m_ytype[$ytype].' ',$qw);
    // echo 'step='.$step."\n";
    // echo $qw;
    // echo date('r',$end)."\n";
    $qdata=$db->query($qw);
    // echo '|'.$db->num_rows($qdata).'|';
    while ($data=$db->fetch($qdata))
    {
        $index=mktime(0,0,0,date('n',$data['post_time']),date('j',$data['post_time']),date('Y',$data['post_time']));
        switch ($ytype)
        {
            case 'post_id':
                $value=1;
                break;
            case 'blog_id':
                $value=(isset($yet_auth[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$data['blog_id']])?0:1);
                $yet_auth[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$data['blog_id']]=1;
                break;
            case 'blog_readers':
                $value=(isset($yet_auth_count[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$data['blog_id']])?0:$data['blog_readers']);
                $yet_auth_count[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$data['blog_id']]=1;
                break;
            case 'post_engage':
                $value=$data['post_engage'];
                break;
            case 'post_retweets':
                $eng=json_decode($data['post_advengage'],true);
                $value=$eng['retweet'];
                break;
            case 'post_likes':
                $eng=json_decode($data['post_advengage'],true);
                $value=$eng['likes'];
                break;
            case 'post_comments':
                $eng=json_decode($data['post_advengage'],true);
                $value=$eng['comment'];
                break;
            case 'post_host':
                $value=(isset($yet_host[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$data['post_host']])?0:1);
                $yet_host[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$data['post_host']]=1;
                break;
            case 'post_tag':
                $mtag=explode(',',$data['post_tag']);
                break;
            default:
                $value=1;
        }
        // echo (intval(($end-$data['post_time'])/(86400*$step)))."\n";
        if ($ytype!='post_tag') $out[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)]+=$value;
        else 
        {
            foreach ($mtag as $ktag)
            {
                if ($ktag=='') continue;
                $yet_tag[$ktag]=1;
                $out[($end-(intval(($end+86400-$data['post_time'])/(86400*$step))+1)*$step*86400)][$mktag[$ktag]]++;
            }
        }
    }
    // print_r($yet_tag);
    // print_r($mktag);
    // die();
    // echo date('r',$start).' '.date('r',$end);
    // die();
    // print_r($out);
    for ($t=$end-$step*86400;$t>=$start-($graphname=='hour'?86400:0)-($step*86400);$t-=$step*86400)
    {
        // echo date('r',($end-$step*86400)).' '.date('r',$t).' '.$t.' '.($step*86400)."\n";
        if ($ytype!='post_tag')
        {
            if (!isset($out[$t])) $out[$t]=0;
        }
        else
        {
            foreach ($yet_tag as $ktag => $itag)
            {
                // if ($ktag=='') continue;
                // echo $ktag."\n";
                if (!isset($out[$t][$mktag[$ktag]])) $out[$t][$mktag[$ktag]]=0;
            }
        }
    }
    // print_r($out);
    ksort($out);
    // die();
    // if (($xtype!='') && ($xtype!='post_time')) 
    // {
    //     arsort($out);
    //     $i=0;
    //     foreach ($out as $key => $item)
    //     {
    //         $i++;
    //         if ($i>10) break;
    //         $outmas[$key]=$item;
    //     }
    //     return $outmas;
    // }
    // else 
    // {
    //     for ($t=$end;$t>$start;$t-=$step*86400)
    //     {
    //         //if (!isset($out[$t])) $out[$t]=0;
    //     }
    //     krsort($out);
    // }
    $outmas['data']=$out;
    $outmas['graph_type']=$graphname;
    return $outmas;
}

function get_stack_data($order_id,$start,$end,$xtype,$ytype)
{
    global $db,$wobot,$_POST;
    // print_r($_POST);
    // $step=30;
    // $ytype='value';
    // $xtype='post_time';
    // $split='blog_location';
    $m_xtype['post_host']='post_host';
    $m_xtype['blog_location']='blog_location';
    $m_xtype['blog_gender']='blog_gender';
    $m_xtype['post_nastr']='post_nastr';
    $m_xtype['post_tag']='post_tag';
    $m_ytype['post_id']='post_id';
    $m_ytype['blog_id']='b.blog_id';
    $m_ytype['blog_readers']='blog_readers,b.blog_id';
    $m_ytype['post_engage']='post_engage';
    $m_ytype['post_retweets']='post_advengage';
    $m_ytype['post_comments']='post_advengage';
    $m_ytype['post_likes']='post_advengage';
    $m_ytype['post_host']='post_host';
    // echo $step.' '.$xtype.' '.$ytype.' '.$split.' '.$m_xtype[$xtype].' '.$m_ytype[$ytype];
    $q = get_query();
    if ($_POST['xtype']=='post_tag')
    {
        $qtag=$db->query('SELECT * FROM blog_tag WHERE order_id='.$_POST['order_id']);
        while ($tag=$db->fetch($qtag))
        {
            $mktag[$tag['tag_tag']]=$tag['tag_name'];
        }
    }
    $q=preg_replace('/\s\*\s/isu',' '.$m_xtype[$xtype].','.$m_ytype[$ytype].' ',$q);
    // echo $q;
    // die();
    $qdata=$db->query($q);
    while ($data=$db->fetch($qdata))
    {
        // print_r($data);
        switch ($xtype)
        {
            case 'post_host':
                $mainindex=$data['post_host'];
                break;
            case 'blog_location':
                $mainindex=$wobot['destn1'][$data['blog_location']];
                break;
            case 'blog_gender':
                $mainindex=$data['blog_gender'];
                break;
            case 'post_nastr':
                $mainindex=$data['post_nastr'];
                break;
            case 'post_tag':
                $mainindex=$data['post_tag'];
                break;
        }
        switch ($ytype) 
        {
            case 'post_id':
                $value=1;
                break;
            case 'blog_id':
                $value=(isset($yet_auth[$data['blog_id']])?0:1);
                $yet_auth[$data['blog_id']]=1;
                break;
            case 'blog_readers':
                $value=(isset($yet_auth[$data['blog_id']])?0:$data['blog_readers']);
                $yet_auth[$data['blog_id']]=1;
                break;
            case 'post_engage':
                $value=$data['post_engage'];
                break;
            case 'post_retweets':
                $post_advengage=json_decode($data['post_advengage'],true);
                $value=$post_advengage['retweet'];
                break;
            case 'post_comments':
                $post_advengage=json_decode($data['post_advengage'],true);
                $value=$post_advengage['comment'];
                break;
            case 'post_likes':
                $post_advengage=json_decode($data['post_advengage'],true);
                $value=$post_advengage['likes'];
                break;
            case 'post_host':
                $value=(isset($yet_host[$data['post_host']])?0:1);
                $yet_host[$data['post_host']]=1;
                break;
            default:
                $value=1;
                break;
        }
        $out[$mainindex]+=$value;
    }
    arsort($out);
    foreach ($out as $key => $value)
    {
        $k++;
        if ($k>10) unset($out[$key]);
    }
    if ($_POST['xtype']=='blog_location')
    {
        $out['Не определено']=$out[''];
        unset($out['']);
    }
    if ($_POST['xtype']=='post_nastr')
    {
        $out['Нейтральная']=$out[0];
        unset($out[0]);
        $out['Позитивная']=$out[1];
        unset($out[1]);
        $out['Негативная']=$out[-1];
        unset($out[-1]);
    }
    if ($_POST['xtype']=='blog_gender')
    {
        $out['Не определено']=$out[0];
        unset($out[0]);
        $out['Мужчины']=$out[2];
        unset($out[2]);
        $out['Женщины']=$out[1];
        unset($out[1]);
    }
    if ($_POST['xtype']=='post_tag')
    {
        unset($out[0]);
        foreach ($out as $key => $value)
        {
            if (preg_match('/\,/isu',$key))
            {
                $mtags=explode(',', $key);
                foreach ($mtags as $ktag => $vtag)
                {
                    $out[$ktag]+=$vtag;
                }
                unset($out[$key]);
            }
        }
        foreach ($out as $key => $value)
        {
            $out[$mktag[$key]]=$value;
            unset($out[$key]);
        }
    }
    unset($out['']);
    arsort($out);
    //die();
    //echo json_encode($outmas);
    //echo $q;
    return $out;
}

function get_stack_separator_data($order_id,$start,$end,$xtype,$ytype,$separator)
{
    global $db,$wobot,$_POST;
    // print_r($_POST);
    // die();
    // $step=30;
    // $ytype='value';
    // $xtype='post_time';
    // $split='blog_location';
    $m_xtype['post_host']='post_host';
    $m_xtype['blog_location']='blog_location';
    $m_xtype['blog_gender']='blog_gender';
    $m_xtype['post_nastr']='post_nastr';
    $m_xtype['post_tag']='post_tag';
    $m_ytype['post_id']='post_id';
    $m_ytype['blog_id']='b.blog_id';
    $m_ytype['blog_readers']='blog_readers,b.blog_id';
    $m_ytype['post_engage']='post_engage';
    $m_ytype['post_retweets']='post_advengage';
    $m_ytype['post_comments']='post_advengage';
    $m_ytype['post_likes']='post_advengage';
    $m_ytype['post_host']='post_host';
    $m_separator['post_host']='post_host';
    $m_separator['blog_location']='blog_location';
    $m_separator['blog_gender']='blog_gender';
    $m_separator['post_nastr']='post_nastr';
    $m_separator['post_tag']='post_tag';
    // echo $step.' '.$xtype.' '.$ytype.' '.$split.' '.$m_xtype[$xtype].' '.$m_ytype[$ytype];
    $q = get_query();
    if ($_POST['xtype']=='post_tag' || $_POST['separator'])
    {
        $qtag=$db->query('SELECT * FROM blog_tag WHERE order_id='.$_POST['order_id']);
        while ($tag=$db->fetch($qtag))
        {
            $mktag[$tag['tag_tag']]=$tag['tag_name'];
        }
    }
    $q=preg_replace('/\s\*\s/isu',' '.$m_xtype[$xtype].','.$m_ytype[$ytype].','.$m_separator[$separator].' ',$q);
    // echo $q;
    // die();
    $qdata=$db->query($q);
    while ($data=$db->fetch($qdata))
    {
        // print_r($data);

        switch ($separator)
        {
            case 'post_host':
                $index=$data['post_host'];
                break;
            case 'blog_location':
                $index=$wobot['destn1'][$data['blog_location']];
                break;
            case 'blog_gender':
                $index=$data['blog_gender'];
                break;
            case 'post_nastr':
                $index=$data['post_nastr'];
                break;
            case 'post_tag':
                $index=$data['post_tag'];
                break;
        }        switch ($xtype)
        {
            case 'post_host':
                $mainindex=$data['post_host'];
                break;
            case 'blog_location':
                $mainindex=$wobot['destn1'][$data['blog_location']];
                break;
            case 'blog_gender':
                $mainindex=$data['blog_gender'];
                break;
            case 'post_nastr':
                $mainindex=$data['post_nastr'];
                break;
            case 'post_tag':
                $mainindex=$data['post_tag'];
                break;
        }
        switch ($ytype) 
        {
            case 'post_id':
                $value=1;
                break;
            case 'blog_id':
                $value=(isset($yet_auth[$data['blog_id']])?0:1);
                $yet_auth[$data['blog_id']]=1;
                break;
            case 'blog_readers':
                $value=(isset($yet_auth[$data['blog_id']])?0:$data['blog_readers']);
                $yet_auth[$data['blog_id']]=1;
                break;
            case 'post_engage':
                $value=$data['post_engage'];
                break;
            case 'post_retweets':
                $post_advengage=json_decode($data['post_advengage'],true);
                $value=$post_advengage['retweet'];
                break;
            case 'post_comments':
                $post_advengage=json_decode($data['post_advengage'],true);
                $value=$post_advengage['comment'];
                break;
            case 'post_likes':
                $post_advengage=json_decode($data['post_advengage'],true);
                $value=$post_advengage['likes'];
                break;
            case 'post_host':
                $value=(isset($yet_host[$data['post_host']])?0:1);
                $yet_host[$data['post_host']]=1;
                break;
            default:
                $value=1;
                break;
        }
        $out[$mainindex][$index]+=intval($value);
    }
    uasort($out, 'countsort');
    foreach ($out as $key => $value)
    {
        arsort($value);
        $iter++;
        if ($iter>10)
        {
            unset($out[$key]);
        }
        $i=0;
        foreach ($value as $kvalue => $vvalue)
        {
            $i++;
            if ($i==10) break;
            $mtopkey[$kvalue]+=$vvalue;
        }
    }
    arsort($mtopkey);
    foreach ($mtopkey as $key => $value)
    {
        $j++;
        if ($j>10) unset($mtopkey[$key]);
    }
    foreach ($out as $key => $value)
    {
        foreach ($value as $kvalue => $vvalue)
        {
            if (!isset($mtopkey[$kvalue])) unset($out[$key][$kvalue]);
        }
    }
    if ($_POST['xtype']=='blog_location')
    {
        $out['Не определено']=$out[''];
        unset($out['']);
    }
    if ($_POST['xtype']=='post_nastr')
    {
        $out['Нейтральная']=$out[0];
        unset($out[0]);
        $out['Позитивная']=$out[1];
        unset($out[1]);
        $out['Негативная']=$out[-1];
        unset($out[-1]);
    }
    if ($_POST['xtype']=='blog_gender')
    {
        $out['Не определено']=$out[0];
        unset($out[0]);
        $out['Мужчины']=$out[2];
        unset($out[2]);
        $out['Женщины']=$out[1];
        unset($out[1]);
    }
    if ($_POST['xtype']=='post_tag')
    {
        foreach ($out as $key => $value)
        {
            if (preg_match('/\,/isu', $key))
            {
                $mindtag=explode(',', $key);
                foreach ($value as $kvalue => $vvalue)
                {
                    foreach ($mindtag as $kindtag => $vindtag)
                    {
                        $out[$kindtag][$kvalue]+=$vvalue;
                    }
                }
                unset($out[$key]);
            }
        }
        foreach ($out as $key => $value)
        {
            $out[$mktag[$key]]=$value;
            unset($out[$key]);
        }
        unset($out[0]);
    }
    if ($_POST['separator']=='blog_location')
    {
        foreach ($out as $key => $value)
        {
            $out[$key]['Не определено']=$out[$key][''];
            unset($out[$key]['']);
        }
    }
    if ($_POST['separator']=='post_nastr')
    {
        foreach ($out as $key => $value)
        {
            $out[$key]['Нейтральная']=$out[$key][0];
            unset($out[$key][0]);
            $out[$key]['Позитивная']=$out[$key][1];
            unset($out[$key][1]);
            $out[$key]['Негативная']=$out[$key][-1];
            unset($out[$key][-1]);
            unset($out[$key]['']);
        }
    }
    if ($_POST['separator']=='blog_gender')
    {
        foreach ($out as $key => $value)
        {
            $out[$key]['Не определено']=$out[$key][0];
            unset($out[$key][0]);
            $out[$key]['Мужчины']=$out[$key][2];
            unset($out[$key][2]);
            $out[$key]['Женщины']=$out[$key][1];
            unset($out[$key][1]);
            unset($out[$key]['']);
        }
    }
    if ($_POST['separator']=='post_tag')
    {
        foreach ($out as $key => $value)
        {
            foreach ($value as $kvalue => $vvalue)
            {
                if (preg_match('/\,/isu', $kvalue))
                {
                    $mtagkvalue=explode(',', $kvalue);
                    foreach ($mtagkvalue as $vtagkvalue)
                    {
                        $out[$key][$vtagkvalue]+=$vvalue;
                    }
                    unset($out[$key][$kvalue]);
                    unset($out[$key][0]);
                    unset($out[$key]['']);
                }
            }
            // print_r($out[$key]);
            foreach ($out[$key] as $kvalue => $vvalue)
            {
                // echo $kvalue.' '.$mktag[$kvalue]."\n";
                $out[$key][$mktag[$kvalue]]=$vvalue;
                unset($out[$key][$kvalue]);
            }
        }
    }
    foreach ($out as $key => $value)
    {
        foreach ($value as $kvalue => $vvalue)
        {
            $isset_kvalue[$kvalue]=1;
        }
    }
    foreach ($out as $key => $value)
    {
        foreach ($isset_kvalue as $isset_kkvalue => $isset_kvvalue)
        {
            if (!isset($out[$key][$isset_kkvalue])) $out[$key][$isset_kkvalue]=0;
        }
    }
    unset($out['']);
    uasort($out, 'countsort');
    //die();
    //echo json_encode($outmas);
    //echo $q;
    return $out;
}

?>