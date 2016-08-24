<?

/*
	description: Module for sending updated clients' theme gathered data to the 'old cabinet', for
	authors: Gleb Chernov, Vladimir Rybakov
	release date: *.06.2014
*/

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

 function get_new_graph_data($order_id,$xtype,$ytype,$separator)
{
echo "\nstart: ".date('r',microtime(true))."\n\n";

//	Объявляем переменные, которые понадобятся в тестовом запросе
global $_GET,$_POST,$wobot,$db,$user,$order,$word,$morphy;

$graphtype=$xtype;
if ($ytype!='') {$graphtype=$ytype;}
#$ytype=$_POST['ytype'];

// при расчёте охвата нужно искусственно изменить xtype для нормальной работы запроса из БД
switch ($_POST['xtype'])
{
    case 'blog_readers':
        $_POST['xtype']="blog_id,b.blog_readers,";
        $xtype='blog_id,b.blog_readers';   
        break;
    case 'post_retweets':
        $graphtype="post_retweets";
        $_POST['xtype'] = "post_advengage";
        $xtype="post_advengage";
        break;
    case 'post_likes':
        $graphtype="post_likes";
        $_POST['xtype'] = "post_advengage";
        $xtype="post_advengage";
        break;
    case 'post_comments':
        $graphtype="post_comments";
        $_POST['xtype'] = "post_advengage";
        $xtype="post_advengage";
        break;
}

switch ($_POST['ytype'])
{
    case 'blog_readers':
        $_POST['ytype']="blog_id,b.blog_readers,";
        $ytype='blog_id,b.blog_readers';
        $graphtype='blog_readers';  
        break;
    case 'post_retweets':
        $graphtype="post_retweets";
        $_POST['ytype'] = "post_advengage";
        $ytype="post_advengage";
        break;
    case 'post_likes':
        $graphtype="post_likes";
        $_POST['ytype'] = "post_advengage";
        $ytype="post_advengage";
        break;
    case 'post_comments':
        $graphtype="post_comments";
        $_POST['ytype'] = "post_advengage";
        $ytype="post_advengage";
        break;

}

// получаем общий запрос для графика
$qw_results = get_query();

preg_match_all('/(?<cont>.*?)\./isu',$_POST['start'],$start_date);
preg_match_all('/\.(?<cont>.*?)\./isu',$_POST['start'],$start_month);
preg_match_all('/.....\.(?<cont>.*)/isu',$_POST['start'],$start_year);
preg_match_all('/(?<cont>.*?)\./isu',$_POST['end'],$end_date);
preg_match_all('/\.(?<cont>.*?)\./isu',$_POST['end'],$end_month);
preg_match_all('/.....\.(?<cont>.*)/isu',$_POST['end'],$end_year);

$start_to_mktime = mktime(0,0,0,$start_month['cont'][0],$start_date['cont'][0],$start_year['cont'][0]);
$end_to_mktime = mktime(0,0,0,$end_month['cont'][0],$end_date['cont'][0],$end_year['cont'][0]);

$difference = $end_to_mktime - $start_to_mktime;
$difference_days = $difference/86400;

// режем результаты на части согласно формату выходного графика
if (($difference_days == 1) or ($difference_days == 0)) { $graphname="hour";}
if ($difference_days > 1 and $difference_days <= 23) { $split=1; $graphname="day";}
if ($difference_days > 23 and $difference_days <= 167) { $split=7; $graphname="week";}
if ($difference_days > 167 and $difference_days <= 730) { $split=30; $graphname="month";}
if ($difference_days > 730 and $difference_days <= 1825) { $split=90; $graphname="quarter";}
if ($difference_days > 1825) { $split=180; $graphname="halfyear";}

// $parting_array - массив из таймстампов, на которые мы режем присланные starttime, endtime. чтобы оные заюзать в постройке точек графика.
$loc_date=1;
$timed_array_parted = true;
$first_period=true;
$parting_array[0]=$start_to_mktime;
$parting_array[1]=+$start_to_mktime+$split*86400;

$offset=3;
if ($ytype=="blog_id,b.blog_readers") $offset=4;
$razd_offset=4;
if ($ytype=="blog_id,b.blog_readers") $razd_offset=5;

//создаём массив таймстампов для графика

switch ($difference_days)
{
    case '0':
    case '1':
        $part_offs=0;
        $loc_date=0;
        while ($timed_array_parted)
        {

            $parting_array[$loc_date] = $start_to_mktime+($loc_date*3600);
   #         echo date(r,$parting_array[$loc_date])." ".($loc_date)." \n";
            $loc_date++;
            if ($loc_date==24) $timed_array_parted = false;
        }
        break;
    
    default:
        $part_offs=1;
        while ($timed_array_parted)
        {
            if (((($end_to_mktime-$start_to_mktime)%($split*86400))!=0) and $first_period==true)
            {
                $parting_array[1] = $start_to_mktime + ($end_to_mktime-$start_to_mktime)%($split*86400);
                $loc_date = 1;
                $first_period=false;
            }   
            $parting_array[$loc_date+1] = $parting_array[1] + $split*$loc_date*86400;
            if ($end_to_mktime == $parting_array[$loc_date+1]) $timed_array_parted = false;
            $loc_date++;
        }
        break;
}

#foreach ($parting_array as $key => $value) {
#    echo date(r,$value)."\n";
#}

# создаём запрос в БД согласно особенностям нашего графика
switch ($ytype)
{
    case '':
        $qw_results=preg_replace('/\*/isu','post_time,'.'p.'.$xtype,$qw_results);
        $qw_results=preg_replace('/\DESC/isu','ASC',$qw_results);
        break;
    
// remove DESC from stack graphic
    default:
        switch ($separator)
        {
            case "":
                switch ($xtype)
                {
                    case 'blog_gender':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype,$qw_results);
                     //   $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype,$qw_results);
                        break;
                    
                    case 'blog_location':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype,$qw_results);
                     //   $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype,$qw_results);
                        break;

                    case 'post_tag':
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype,$qw_results);
                        break;

                    default:
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype,$qw_results);
                        break;
                }
                $qw_results=preg_replace('/\DESC/isu','ASC',$qw_results);
                break;
            case 'blog_location':
                switch ($xtype)
                {
                    case 'post_tag' :
                    case 'post_host':
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];                    
                        break;
                    case 'blog_gender':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];                                        
                        break;
                    case 'post_nastr':                        
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                        $qw_results=preg_replace('/\DESC/isu','ASC',$qw_results);
                        break;

                    default:
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                        $qw_results=preg_replace('/\DESC/isu','ASC',$qw_results);
                        break;
                }
                break;
            case 'blog_gender':
                if ($xtype == 'post_host') $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                if ($xtype == 'blog_location') $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                if ($xtype == 'post_time') $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                if ($xtype == 'post_tag')
                {
                    $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                    $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];                    
                }
                $qw_results=preg_replace('/\DESC/isu','ASC',$qw_results);
                break;
            case 'post_tag':
                switch ($xtype) {
                    case 'post_host':
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];  
                        break;

                    case 'blog_location':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];  
                        break;
                    
                    case 'blog_gender':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];  
                        break;

                    case 'post_nastr':
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];  
                        break;

                    default:
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                        $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];  
                        break;
                }
                $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',b.'.$separator,$qw_results);
                $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];   
            break;
            case 'post_host':
                switch ($xtype)
                {
                    case 'post_nastr':
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;

                    case 'blog_location':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;
                    
                    case 'blog_gender':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;
                    
                    default:
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;
                }
            case 'post_nastr':
                switch ($xtype)
                {
                    case 'blog_gender':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;

                    case 'blog_location':
                        $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;
                    
                    default:
                        $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                        break;
                }
                $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];               
            break;
            default:
                if ($xtype == 'post_tag')
                {
                    $qw_results=preg_replace('/\*/isu','post_time,p.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                    $qw_results_tag="SELECT tag_tag,tag_name FROM blog_tag WHERE order_id = ".$_POST['order_id'];                    
                }
                if ($xtype != 'post_tag')
                {
                    $qw_results=preg_replace('/\*/isu','post_time,b.'.$xtype.',p.'.$ytype.',p.'.$separator,$qw_results);
                    $qw_results=preg_replace('/\DESC/isu','ASC',$qw_results);
                }
                break;
        }
        break;
}

#print_r($qw_results);
#echo "\n";
#print_r($qw_results_tag);

$qw_post = $db->query($qw_results);
$post = $db->fetch($qw_post);

if ($xtype=='post_tag' or $separator=='post_tag')
{
    //echo "\n\n";
    //print_r($qw_results_tag);
    $qw_post_tag = $db->query($qw_results_tag);
    //$post_tag = $db->fetch($qw_post_tag);

    while ($post_tag = $db->fetch($qw_post_tag))
    {
        //print_r($post_tag);
        //$temp_array_tag[$i]=$post_tag['post_time'];
        $temp_array_tag[$post_tag['tag_tag']]=$post_tag['tag_name'];
        $i++;
    }
}

//print_r($temp_array_tag);

$i=0;

# записываем наши данные в массив

switch ($ytype)
{
    case '':
        switch ($xtype)
        {
            case 'blog_id,b.blog_readers':
                # линейный охват
                while ($post = $db->fetch($qw_post))
                {
                    $temp_array[$i]=$post['post_time'];
                    $temp_array[$i+1]=$post['blog_id'];
                    $temp_array[$i+2]=$post['blog_readers'];
                    $i+=3;
                }
            break;
            default:
                # линейный простой
                while ($post = $db->fetch($qw_post))
                {
                    $temp_array[$i]=$post['post_time'];
                    $temp_array[$i+1]=$post[$xtype];
                    $i+=2;
                }
            break;
        }
    break;
    default:
    switch ($separator)
    {
        case '':
            # стековый без разделителя
            switch ($xtype)
            {
                case 'blog_id,b.blog_readers':
                    # стековый без разделителя охват
                    while ($post = $db->fetch($qw_post))
                    {
                        $temp_array[$i]=$post['post_time'];
                        $temp_array[$i+1]=$post['blog_id'];
                        $temp_array[$i+2]=$post['blog_readers'];
                        $temp_array[$i+3]=$post[$ytype];
                        $i+=4;
                    }             
                break;
                default:
                    # стековый без разделителя простой
                    //echo "stack no separator\n";
                    switch ($ytype)
                    {
                        case 'blog_id,b.blog_readers':
                            while ($post = $db->fetch($qw_post))
                            {
                                $temp_array[$i]=$post['post_time'];
                                $temp_array[$i+1]=$post[$xtype];
                                $temp_array[$i+2]=$post['blog_id'];
                                $temp_array[$i+3]=$post['blog_readers'];
                                $i+=4;
                            }
                        break;

                        default:
                            while ($post = $db->fetch($qw_post))
                            {
                                $temp_array[$i]=$post['post_time'];
                                $temp_array[$i+1]=$post[$xtype];       // kach
                                $temp_array[$i+2]=$post[$ytype];       // kol!
                                $i+=3;
                            }
                        break;
                    }
                break;
            }
            break;
        
        default:
            switch ($ytype)
            {
                case 'blog_id,b.blog_readers':
                    # стековый с разделителем охват
                    while ($post = $db->fetch($qw_post))
                    {
                        $temp_array[$i]=$post['post_time'];
                        $temp_array[$i+1]=$post['blog_id'];
                        $temp_array[$i+2]=$post['blog_readers'];
                        $temp_array[$i+3]=$post[$xtype];
                        $temp_array[$i+4]=$post[$separator];
                        $i+=5;
                    }
                break;
                default:
                    # стековый с разделителем простой
                    while ($post = $db->fetch($qw_post))
                    {
                        $temp_array[$i]=$post['post_time'];
                        $temp_array[$i+1]=$post[$xtype];
                        $temp_array[$i+2]=$post[$ytype];
                        $temp_array[$i+3]=$post[$separator];
                        $i+=4;
                    }
                break;
            }
            break;
    }
    break;
}

//echo "\ntemp_array:\n";
//print_r($temp_array);
$final_kach_array[0]=$temp_array[1];

# сбор 10 наиболее часто встречающихся разделителей
//echo "\$i=".$i."\n";
$sep_offset=4;
$sep_start=7;
$sep_array[0]=$temp_array[3];
if ($ytype=="blog_id,b.blog_readers")
{
    $sep_start=9;
    $sep_offset=5;
    $sep_array[0]=$temp_array[4];
}

if ($separator!='')
{

    $sep_local_count=0;
    for ($sep_count=$sep_start;$sep_count<=$i;$sep_count+=$sep_offset)
    {
        // если новый разделитель не равен текущему, запихнём его в массив разделителей
        $sep_new_member = true;
 //     echo "started cycle\n";
        for ($sep_local_count=0;$sep_local_count<=count($sep_array); $sep_local_count++)
        {
  //        echo $temp_array[$sep_count]."==".$sep_array[$sep_local_count]." ";
            if($temp_array[$sep_count]==$sep_array[$sep_local_count]) $sep_new_member = false;
        }
        if ($sep_new_member) $sep_array[count($sep_array)]=$temp_array[$sep_count];
        // у нас есть массив всех разделителей ($sep_array)
        // для каждого из разделителей пробегаемся по создаваемому нами массиву разделителей и смотрим сколько каждый из них наберет
        for ($sep_grow_count=0;$sep_grow_count<=count($sep_array);$sep_grow_count++)
        {
            if ($sep_array[$sep_grow_count] == $temp_array[$sep_count]) $sep_hit_array[$sep_grow_count]++;
        }
    }
    // далее пытаемся понять какие из разделителей наиболее популярные
    for($type_count=0;$type_count<=count($sep_array);$type_count++)
    {
        $sep_summed_array[$sep_array[$type_count]]=$sep_hit_array[$type_count];
    }
//if ($sep_summed_array[0]=='') echo "\nError: no separators gathered";
}

#arsort($sep_summed_array);
#print_r($sep_summed_array);

#foreach ($sep_summed_array as $key => $value) {
#    echo $wobot['destn1'][$key]."\n";
#}


//print_r($sep_summed_array);

# сбор 10 наибольших качественных показателей для стекового графика
if ($ytype!='')
{
    $kachestv_start=1;
    $kachestv_offset=3;
    if ($ytype=="blog_id,b.blog_readers") $kachestv_offset=4;
    if ($separator!='') $kachestv_offset=4;
    if ($separator!='' and $ytype=="blog_id,b.blog_readers")
    {
        $kachestv_start=3;
        $kachestv_offset=5;
    }
   // if ($xtype == 'blog_id,b.blog_readers') $kachestv_offset=1;
 //   if ($separator!='') $kachestv_offset=1;

    for ($kachestv_count=$kachestv_start;$kachestv_count<=$i;$kachestv_count+=$kachestv_offset)
    {
        // если новый xtype не равен текущему, запихнём его в массив xtype
        $kachestv_new_member = true;
        for ($kachestv_local_count=0;$kachestv_local_count<=count($final_kach_array); $kachestv_local_count++)
        {
   //         echo $temp_array[$kachestv_count]."==".$final_kach_array[$kachestv_local_count]." ";
           if($temp_array[$kachestv_count]==$final_kach_array[$kachestv_local_count]) $kachestv_new_member = false;
        }
        if ($kachestv_new_member == true) $final_kach_array[count($final_kach_array)]=$temp_array[$kachestv_count];
        // у нас есть массив всех xtype
        // для каждого из xtype пробегаемся по создаваемому нами массиву и смотрим сколько каждый из них наберет хитов в постах
        for ($xtype_grow_count=0;$xtype_grow_count<=count($final_kach_array);$xtype_grow_count++)
        {
            if ($final_kach_array[$xtype_grow_count] == $temp_array[$kachestv_count]) $xtype_hit_array[$xtype_grow_count]++;
        }
    }
}

arsort($xtype_hit_array);

# собираем массив количественных данных, общий сбор
$kolich_offset = 2;
if ($xtype == "blog_readers") $kolich_offset = 3;
for($klo=0;$klo<10;$klo++)
{
    $final_sep_array_comp[$klo]=key($sep_summed_array);
    next($sep_summed_array);
    if ($final_sep_array_comp[$klo]=='') unset($final_sep_array_comp[$klo]);
}
$largest_sep_count = count($final_sep_array_comp);

if ($xtype=='blog_location') foreach ($final_kach_array as $key => $value) if ($wobot['destn1'][$value]=='') unset($final_kach_array[$key]);

for($klo=0;$klo<=10;$klo++)
{
    $final_kach_array_comp[$klo]=current($final_kach_array);
    next($final_kach_array);
    if ($final_kach_array_comp[$klo]=='') unset($final_kach_array_comp[$klo]);
}


if (($xtype == "blog_gender"))
{
    unset($final_kach_array_comp);
    $final_kach_array_comp[0]=0; 
    $final_kach_array_comp[1]=1; 
    $final_kach_array_comp[2]=2; 
}

if ($separator == "blog_gender")
{
    unset($final_sep_array_comp);
    $final_sep_array_comp[0]="0"; 
    $final_sep_array_comp[1]="1"; 
    $final_sep_array_comp[2]="2"; 
}

if ($separator == "post_nastr")
{
    unset($final_sep_array_comp);
    $final_sep_array_comp[0]="-1"; 
    $final_sep_array_comp[1]="0"; 
    $final_sep_array_comp[2]="1"; 
} 

$largest_kach_count = count($final_kach_array_comp);

$s_local=0;
$secs_offset=86400;

if (($difference_days==1) or ($difference_days==0)) { $secs_offset = 0;}
echo "\n".date('r',microtime(true))."\n\n";

$cpa=count($parting_array);

echo count($temp_array);

switch ($separator)
{
    case "":
        switch ($ytype)
        {
            case '':
                // линейный                                                             ЛИНЕЙНЫЕ
            	for ($d=0;$d<count($parting_array);$d++)
            	{
            		while (($temp_array[$s_local]<($parting_array[$d+1]+$secs_offset)) and $temp_array[$s_local]>$parting_array[$d])
            		{
            			switch ($graphtype)
            			{
                            // √∆ Количество авторов (authors)                          blog_id
                            // выделяем количество уникальных blog_id попериодно
                            case "blog_id":
                            // √∆ Количество уникальных ресурсов (unique_resources)     post_host
                            // суммарно попериодно уникальные post_host
                            case "post_host":
            				// √∆ Количество упоминаний									post_id
        		        	// выделяем count массива уникальных post_id попериодно
                            case "post_id":
                            //echo date('r',microtime(true))."  ";
           					$not_the_new_param = false;
            				for ($slc=0;$slc<count($gather_array[$d]);$slc++)
            				{
        	    				if ($temp_array[$s_local+1]==$gather_array[$d][$slc]) $not_the_new_param = true;
            				}
            				if ($not_the_new_param == false)
                            {
                                $cga=count($gather_array[$d]);
                                $gather_array[$d][$cga]=$temp_array[$s_local+1];
                            }
                            if ($temp_array[$s_local] > $parting_array[$cpa-1]) $arr_out[$parting_array[$cpa-1]] = $cga;
            				$s_local+=2;
            				break;
                            // ø Охват (ohvat)                                          blog_readers
                            // blog_readers у всех уникальных blog_id попериодно
                            // сначала выделяем массив уникальных blog_id попериодно, потом для них суммируем ридерзов.1
                            case "blog_readers":
                            $not_the_new_param = false;
                            for ($slc=0;$slc<=count($gather_array[$d]);$slc++)
                            {
                                if ($temp_array[$s_local+1]==$gather_array[$d][$slc]) $not_the_new_param = true;
                            }
                            if ($not_the_new_param == false)
                            {
                            // собираем xtypes
                                $gather_array[$d][count($gather_array[$d])]=$temp_array[$s_local+1];
                                $readers_array[$d]+=$temp_array[$s_local+2];
                            }
                            $s_local+=3;
                            if ($temp_array[$s_local] > $parting_array[count($parting_array)-1]) $arr_out[$parting_array[count($parting_array)-1]] = $readers_array[$d];
                            break;
            				
            				// √∆ Вовлеченность (vovlechennost)							post_engage
        					// выделяем суммарную post_engage попериодно
                            // она ж не равна blog_id
                            case "post_engage":
                            // собираем xtypes
  //                          echo "timestamp is ".date('r',$temp_array[$s_local])."\n";
                            $gather_array[$d]+=$temp_array[$s_local+1];
                            
                            if ($temp_array[$s_local] > $parting_array[count($parting_array)-1]) $arr_out[$parting_array[count($parting_array)-1]] = $gather_array[$d];
                            $s_local+=2;
                            break;

            				// √∆ Количество ретвитов (retweets)							post_retweets
                			// суммарно за период post_advengage
            				case "post_retweets":
                            $temporary_buffer=json_decode($temp_array[$s_local+1],true);
                            $gather_array[$d]+=$temporary_buffer["retweet"];
                            if ($temp_array[$s_local] > $parting_array[count($parting_array)-1]) $arr_out[$parting_array[count($parting_array)-1]] = $gather_array[$d];
                            $s_local+=2;
            				break;
            				
            				// √∆ Количество лайков (likes)								post_likes
                			// суммарно за период post_advengage
            				case "post_likes":
                            $temporary_buffer=json_decode($temp_array[$s_local+1],true);
                            $gather_array[$d]+=$temporary_buffer["likes"];                    
                            if ($temp_array[$s_local] > $parting_array[count($parting_array)-1]) $arr_out[$parting_array[count($parting_array)-1]] = $gather_array[$d];
                            $s_local+=2;
            				break;
            				
            				// √∆ Количество комментариев (comments)						post_comments
                			// суммарно за период post_advengage
            				case "post_comments":
                            $temporary_buffer=json_decode($temp_array[$s_local+1],true);
                            $gather_array[$d]+=$temporary_buffer["comment"];                    
                            if ($temp_array[$s_local] > $parting_array[count($parting_array)-1]) $arr_out[$parting_array[count($parting_array)-1]] = $gather_array[$d];
                            $s_local+=2;
            				break;
            			}
            		}
            		if ($temp_array[$s_local] > ($parting_array[$d+1]))
            		{
                        switch ($graphtype)
                        {
                            case "blog_id":
                            case "post_host":
                            case "post_id":
                            $arr_out[$parting_array[$d+$part_offs]] = count($gather_array[$d]);
                            break;
                            case "blog_readers":
       #                     echo date(r,$parting_array[$d+$part_offs])."=".$readers_array[$d]."\n";
                            $arr_out[$parting_array[$d+$part_offs]] = $readers_array[$d];
                            break;
                            case "post_engage":
                            case "post_retweets":
                            case "post_likes":
                            case "post_comments":
                            $arr_out[$parting_array[$d+$part_offs]] = $gather_array[$d];
                            break;
                        }
            		}
   //             echo date('r',$parting_array[$d])."||";
            	}
                break;
            default:
            //$graphtype=$ytype;
//            echo "xtype = ".$xtype."\n";
//            echo "ytype = ".$ytype."\n";                                      СТЕКОВЫЕ БЕЗ РАЗДЕЛИТЕЛЯ
//            echo "graphtype = ".$graphtype."\n";
                // стековый с ytype но без разделителя
                for($n_2=0;$n_2<=count($final_kach_array_comp);$n_2++)
                {
                    // каждая инстанция поста, собранная в запросе
                    for ($n_3=0;$n_3<=count($temp_array);$n_3+=$offset)
                    {
                        switch ($graphtype)
                        {
                            // √∆ Количество авторов (authors)                          blog_id
                            // выделяем количество уникальных blog_id 
                            case "blog_id":
                            // √∆ Количество уникальных ресурсов (unique_resources)     post_host
                            // суммарно попериодно уникальные post_host
                            case "post_host":
                            // √∆ Количество упоминаний                                 post_id
                            // выделяем count массива уникальных post_id 
                            case "post_id":
                               // $gather_array[$n_2][0]='vk.com';
                            if ($final_kach_array_comp[$n_2]==$temp_array[$n_3+1]){
                                $not_the_new_param = false;
                                for ($slc=0;$slc<count($gather_array[$n_2]);$slc++)
                                {
                                    //echo $temp_array[$n_3+2]."<>".$gather_array[$n_2][$slc]."\n";
                                    if ($temp_array[$n_3+2]==$gather_array[$n_2][$slc]) $not_the_new_param = true;
                                }
                            if ($not_the_new_param == false) $gather_array[$n_2][count($gather_array[$n_2])]=$temp_array[$n_3+2];
                            $out_arr_stack[$final_kach_array_comp[$n_2]]=count($gather_array[$n_2]);
                            }
                            break;
                            // ø Охват (ohvat)                                          blog_readers
                            // blog_readers у всех уникальных blog_id попериодно
                            // сначала выделяем массив уникальных blog_id, потом для них суммируем ридерзов.
                            case "blog_readers":

                            if ($temp_array[$n_3+1]==$final_kach_array_comp[$n_2])
                            {
                                $not_the_new_param = false;
                                for ($slc=0;$slc<count($gather_array[$n_2]);$slc++)
                                {
                                    if ($temp_array[$n_3+2]==$gather_array[$n_2][$slc]) $not_the_new_param = true;
                                }
                                if ($not_the_new_param == false)
                                {
                                // собираем xtypes
                                    $gather_array[$n_2][count($gather_array[$n_2])]=$temp_array[$n_3+2];
                                    $out_arr_stack[$final_kach_array_comp[$n_2]]+=$temp_array[$n_3+3];
                                }
                            }
                            break;
                            
                            // √∆ Вовлеченность (vovlechennost)                         post_engage
                            // выделяем суммарную post_engage
                            // она ж не равна blog_id
                            case "post_engage":
                            //echo $temp_array[$n_3+2]."\n";
                            //echo "test";
                            if ($temp_array[$n_3+1]==$final_kach_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n_2]]+=$temp_array[$n_3+2];
                            break;

                            // √∆ Количество ретвитов (retweets)                            post_retweets
                            // суммарно post_advengage
                            case "post_retweets":
                            $temporary_buffer=json_decode($temp_array[$n_3+2],true);
                            if ($temp_array[$n_3+1]==$final_kach_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n_2]]+=$temporary_buffer["retweet"];
                            break;
                            
                            // √∆ Количество лайков (likes)                             post_likes
                            // суммарно post_advengage
                            case "post_likes":
                            $temporary_buffer=json_decode($temp_array[$n_3+2],true);              
                            if ($temp_array[$n_3+1]==$final_kach_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n_2]]+=$temporary_buffer["likes"];
                            break;
                            
                            // √∆ Количество комментариев (comments)                        post_comments
                            // суммарно post_advengage
                            case "post_comments":
                            //echo $temp_array[$n_3+2]."\n";
                            $temporary_buffer=json_decode($temp_array[$n_3+2],true);                   
                            if ($temp_array[$n_3+1]==$final_kach_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n_2]]+=$temporary_buffer["comment"];
                            break;
                        }
                    }
                }
                break;
        }
        break;
    default:
        // стековый с ytype и разделителем                                  СТЕКОВЫЕ С РАЗДЕЛИТЕЛЕМ
        if ($ytype=='') {echo "Wrong parameters\n"; die();}


        // качественные данные
        for($n=0;$n<count($final_kach_array_comp);$n++)
        {
        unset($gather_array);
            // разделитель
            for ($n_2=0;$n_2<=$largest_sep_count;$n_2++)
            {
                // каждая инстанция поста, собранная в запросе
                for ($n_3=0;$n_3<=count($temp_array);$n_3+=$razd_offset)
                {
                    switch ($graphtype)
                    {
                        // √∆ Количество авторов (authors)                          blog_id
                        // выделяем количество уникальных blog_id 
                        case "blog_id":
                        // √∆ Количество уникальных ресурсов (unique_resources)     post_host
                        // суммарно попериодно уникальные post_host
                        case "post_host":
                        // √∆ Количество упоминаний                                 post_id
                        // выделяем count массива уникальных post_id 
                        case "post_id":
                        #echo $temp_array[$n_3+3]."==".$final_sep_array_comp[$n]."\n";
                        if ($final_kach_array_comp[$n]==$temp_array[$n_3+1])
                        {
                         #   echo ' '.$final_kach_array_comp[$n_2].'=='.$temp_array[$n_3+1]."\n";
                            if ($temp_array[$n_3+3]==$final_sep_array_comp[$n_2])
                            {
                                $not_the_new_param = false;
                                for ($slc=0;$slc<count($gather_array[$n_2]);$slc++)
                                {
                                    //echo "found a new unique timez";
                                    //echo $temp_array[$n_3+2]."<>".$gather_array[$n_2][$slc]."\n";
                                    if ($temp_array[$n_3+2]==$gather_array[$n_2][$slc]) $not_the_new_param = true;
                                }
                                if (!$not_the_new_param) $gather_array[$n_2][count($gather_array[$n_2])]=$temp_array[$n_3+2];
                                $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]]=count($gather_array[$n_2]);
                            }
                        }
                        // ø Охват (ohvat)                                          blog_readers
                        // blog_readers у всех уникальных blog_id попериодно
                        // сначала выделяем массив уникальных blog_id, потом для них суммируем ридерзов.
                        case "blog_readers":
                        if ($temp_array[$n_3+3]==$final_kach_array_comp[$n])
                        {
 #                       echo $temp_array[$n_3+3]." = ".$final_kach_array_comp[$n]."; \n"; 
                            if ($temp_array[$n_3+4]==$final_sep_array_comp[$n_2])
                            {
  #                          echo "   ".$temp_array[$n_3+4]." = ".$final_sep_array_comp[$n_2]." \n";
                                $not_the_new_param = false;
                                for ($slc=0;$slc<=count($gather_array[$d]);$slc++) if ($temp_array[$s_local+1]==$gather_array[$d][$slc]) $not_the_new_param = true;
                                if ($not_the_new_param == false)
                                {
                                // собираем xtypes
                                    $gather_array[$d][count($gather_array[$d])]=$temp_array[$s_local+1];
                                    $readers_array[$d]+=$temp_array[$s_local+2];
                                }
                                $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]]=$readers_array[$d];
                                $s_local+=5;
                                if ($temp_array[$s_local] > $parting_array[count($parting_array)-1]) $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]] = $readers_array[$d];
                                $readers_array[$d]=0;
                            }
                        }
                        break;
                        
                        // √∆ Вовлеченность (vovlechennost)                         post_engage
                        // выделяем суммарную post_engage
                        // она ж не равна blog_id
                        case "post_engage":
                        //echo "\$n_3=".($n_3)." ";
                        //echo $temp_array[$n_3+3]."==".$final_sep_array_comp[$n]."\n"; 
                        if ($temp_array[$n_3+1]==$final_kach_array_comp[$n]) if ($temp_array[$n_3+3]==$final_sep_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]]+=$temp_array[$n_3+2];
                        break;
                         // √∆ Количество ретвитов (retweets)                            post_retweets
                        // суммарно post_advengage
                        case "post_retweets":
                        $temporary_buffer=json_decode($temp_array[$n_3+2],true);
                        if ($temp_array[$n_3+1]==$final_kach_array_comp[$n]) if ($temp_array[$n_3+3]==$final_sep_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]]+=$temporary_buffer["retweet"];
                        break;
                        
                        // √∆ Количество лайков (likes)                             post_likes
                        // суммарно post_advengage
                        case "post_likes":
                        $temporary_buffer=json_decode($temp_array[$n_3+2],true);              
                        if ($temp_array[$n_3+1]==$final_kach_array_comp[$n]) if ($temp_array[$n_3+3]==$final_sep_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]]+=$temporary_buffer["likes"];
                        break;
                        
                        // √∆ Количество комментариев (comments)                        post_comments
                        // суммарно post_advengage
                        case "post_comments":
                        $temporary_buffer=json_decode($temp_array[$n_3+2],true);                   
                        if ($temp_array[$n_3+1]==$final_kach_array_comp[$n]) if ($temp_array[$n_3+3]==$final_sep_array_comp[$n_2]) $out_arr_stack[$final_kach_array_comp[$n]][$final_sep_array_comp[$n_2]]+=$temporary_buffer["comment"];
                        break;
                    }
                   //if ($temp_array[$n_3+1]==$final_kach_array_comp[$n_2]) $out_arr_stack[$final_sep_array_comp[$n]][$final_kach_array_comp[$n_2]]+=$temp_array[$n_3+1];
                }
            }
        }
    break;
}

echo "\nafter data gather:".date('r',microtime(true))."\n\n";

if (!isset($arr_out[$parting_array[count($parting_array)]])) $arr_out[$parting_array[count($parting_array)]]=0;

#echo "\nfinal kach array:\n";
#print_r($final_kach_array);

//foreach ($final_kach_array as $key => $value) {
//    echo $wobot['destn1'][$value]."\n";
//}

// print_r($out_arr_stack);

#echo "\nfinal kach array comp:\n";
# print_r($final_kach_array_comp);

//print_r($temp_array);

#echo "final sep array comp:\n";
#print_r($final_sep_array_comp);

#foreach ($final_sep_array_comp as $key => $value) {
#    echo $wobot['destn1'][$value]."\n";
#}

#echo "gather array:\n";
#print_r($gather_array);

#print_r($out_arr_stack);

//foreach ($arr_out as $key => $value) {
//    echo date(r,$key)." => ".$value."\n";
//}

#print_r($temp_array_tag);
#print_r($out_arr_stack);

if ($xtype=='blog_location')
{
    foreach ($out_arr_stack as $key => $value)
    {

        $out_arr_stack2[$wobot['destn1'][$key]]=$value;
        if ($key == '')
        {
            $out_arr_stack2['Не определено']=$value;
            unset($out_arr_stack2['']);
        }
    }
    unset($out_arr_stack2['']);
    $out_arr_stack=$out_arr_stack2;
}

# распиливаем массив на теги
$dam=0;
if ($separator=='' and $xtype == 'post_tag')
{
    foreach ($out_arr_stack as $key => $value)
    {
        if (preg_match('/\,/isu', $key))
        {
            foreach (explode(',', $key) as $vv)
            {
                $out_arr_stack[$vv]+=$value;
            }
            unset($out_arr_stack[$key]);
        }
        if ($key=='')
        {
            $tm=$value;
            unset($out_arr_stack[$key]);
            $out_arr_stack['без тегов']=$value;
        }
    }
    foreach ($out_arr_stack as $key => $value)
    {
        if ($key!='без тегов')
        {
            $out_arr_stack[$temp_array_tag[$key]]=$value;
            unset($out_arr_stack[$key]);
        }
    }
}

# повторяем то же самое, для случая с фактически имеющимся разделителем
$dam=0;
if ($separator!='' and $xtype == 'post_tag')
{
    foreach ($out_arr_stack as $key => $value)
    {
        if (preg_match('/\,/isu', $key))
        {
            foreach (explode(',', $key) as $v_g)
            {
                foreach ($out_arr_stack[$key] as $kk => $vv)
                {
                    $out_arr_stack2[$v_g][$kk]+=$vv;
                }
            }
            unset($out_arr_stack[$key]);
        }
        if ($key=='')
        {
            $tm=$value;
            unset($out_arr_stack[$key]);
            $out_arr_stack2['без тегов']=$value;
        }
        if (!preg_match('/\,/isu', $key))
        {
            $out_arr_stack2[$key]=$value;
        }
    }
    unset($out_arr_stack);
    foreach ($out_arr_stack2 as $key => $value)
    {
        if ($key!='без тегов')
        {
            $out_arr_stack[$temp_array_tag[$key]]=$value;
        }
        if ($key=='без тегов') $out_arr_stack[$key]=$value;
    }
    unset($out_arr_stack['']);
    unset($out_arr_stack2);
}

# подсчитываем уникальность каждого элемента массива
foreach ($out_arr_stack as $key => $value)
{
    foreach ($out_arr_stack[$key] as $kk => $vv)
    {
        $mkeys[$kk]++;
    }
}

# дополняет каждый элемент массива, задавая нули не существовавшим подэлементам. чтобы всё было красиво для строящего график 
foreach ($out_arr_stack as $key => $value)
{
    foreach ($out_arr_stack[$key] as $kk => $vv)
    {
        foreach ($mkeys as $mk => $mv)
        {
        if (!isset($out_arr_stack[$key][$mk])) $out_arr_stack[$key][$mk]=0;
        }
    }
}

# сортирование на убыль \ прибыль?
foreach ($out_arr_stack as $key => $value)
{
    ksort($out_arr_stack[$key]);
}

$err_mas['error']='no_data';

switch ($ytype)
{
    case '':
        $final_arr_out['graph_type']=$graphname;
        $final_arr_out['data']=$arr_out;
        foreach ($final_arr_out['data'] as $key => $value) 
        {
            if (is_null($value)) $final_arr_out['data'][$key]=0;
        }
        unset($final_arr_out[0]);
        array_pop($final_arr_out['data']);
        if ($final_arr_out == '') {unset($final_arr_out); $final_arr_out=$err_mas;}
        echo "\nfinal:".date('r',microtime(true))."\n\n";
        echo json_encode($final_arr_out, true);
        break;
    
    default:
        switch ($separator)
        {
            case '':
                if ($xtype=='blog_gender')
                {
                    $out_arr_stack3['не определён']=$out_arr_stack[0];
                    $out_arr_stack3['женский']=$out_arr_stack[1];
                    $out_arr_stack3['мужской']=$out_arr_stack[2];
                    $out_arr_stack=$out_arr_stack3;
                }
                if ($xtype=='post_nastr')
                {
                    $out_arr_stack3['положительная']=$out_arr_stack[0];
                    $out_arr_stack3['отрицательная']=$out_arr_stack[1];
                    $out_arr_stack3['нейтральная']=$out_arr_stack[2];
                    foreach ($out_arr_stack3 as $key => $value) if (is_null($value)) $out_arr_stack3[$key]=0;
                    $out_arr_stack=$out_arr_stack3;
                }
                if ($out_arr_stack == '') {unset($out_arr_stack); $out_arr_stack=$err_mas;}
                arsort($out_arr_stack);
                echo json_encode($out_arr_stack,true);
                break;
            
            default:
                foreach ($out_arr_stack as $key => $value)
                {
                    $out_arr_stack_count[$key]=count($value);
                }
                arsort($out_arr_stack_count);
                $largest_separator_array_count = current($out_arr_stack_count);
                if ($xtype=='blog_gender')
                {
                    foreach ($out_arr_stack as $key => $value)
                    {
                        if ($key==0) $out_arr_stack_key['не определён']=$out_arr_stack[$key];
                        if ($key==1) $out_arr_stack_key['женский']=$out_arr_stack[$key];
                        if ($key==2) $out_arr_stack_key['мужской']=$out_arr_stack[$key];
                    }
                    $out_arr_stack=$out_arr_stack_key;
                }
                if ($separator=='blog_gender')
                {
                    foreach ($out_arr_stack as $key => $value)
                    {
                        foreach ($out_arr_stack[$key] as $kk => $vv)
                        {
                        if ($kk==0) $out_arr_stack_key[$key]['не определён']=$vv;
                        if ($kk==1) $out_arr_stack_key[$key]['женский']=$vv;
                        if ($kk==2) $out_arr_stack_key[$key]['мужской']=$vv;
                        }
                    }
                    $out_arr_stack=$out_arr_stack_key;
                }
                if ($separator=='post_nastr')
                {
                    foreach ($out_arr_stack as $key => $value)
                    {
                        foreach ($out_arr_stack[$key] as $kk => $vv)
                        {
                        if ($kk==0) $out_arr_stack_key[$key]['нейтральная']=$vv;
                        if ($kk==1) $out_arr_stack_key[$key]['положительная']=$vv;
                        if ($kk==-1) $out_arr_stack_key[$key]['отрицательная']=$vv;
                        }
                    }
                    $out_arr_stack=$out_arr_stack_key;
                }
                
                if ($xtype=='post_nastr')
                {
                    foreach ($out_arr_stack as $key => $value)
                    {
                        if ($key==0) { $out_arr_stack_key['нейтральная']=$value; unset($out_arr_stack[$key]); }
                        if ($key==1) { $out_arr_stack_key['положительная']=$value; unset($out_arr_stack[$key]); }
                        if ($key==-1) { $out_arr_stack_key['отрицательная']=$value; unset($out_arr_stack[$key]); }
                    }
                    unset($out_arr_stack_key[0]);
                    unset($out_arr_stack_key[-1]);
                    unset($out_arr_stack_key[1]);
                    $out_arr_stack=$out_arr_stack_key;
                }

                # перебиваем цифры на теги в случае разделителя, являющегося тегами
                if ($separator=='post_tag')
                {
                    foreach ($out_arr_stack as $key => $value)
                    {
                        foreach($out_arr_stack[$key] as $kk => $vv)
                        {
                            if (preg_match('/\,/isu', $kk))
                            {
                                foreach (explode(',', $kk) as $v_g)
                                {
                                    $out_arr_stack2[$key][$v_g]+=$vv;
                                }
                            }
                            if ($kk=='')
                            {
                                $tm=$value;
                                $out_arr_stack2[$key]['без тегов']=$vv;
                            }
                            if (!preg_match('/\,/isu', $kk))
                            {
                                $out_arr_stack2[$key][$kk]=$vv;
                            }
                        }
                    }
                    unset($out_arr_stack);
                    foreach ($out_arr_stack2 as $key => $value)
                    {
                        foreach ($out_arr_stack2[$key] as $kk => $vv)
                        {
                            if ($kk!='без тегов')
                            {
                                $out_arr_stack[$key][$temp_array_tag[$kk]]=$vv;
                            }
                            if ($kk=='без тегов') $out_arr_stack[$key][$kk]=$vv;
                        }
                    }
                    foreach ($out_arr_stack as $key => $value)
                    {
                        foreach($out_arr_stack[$key] as $kk=>$vv) if ($kk=='') unset($out_arr_stack[$key][$kk]);
                    }
                }

                if ($separator=='blog_location')
                {
                    foreach ($out_arr_stack as $key => $value)
                    {
                        foreach ($value as $kk => $vv)
                        {
                            if ($kk != '') $out_arr_stack2[$key][$wobot['destn1'][$kk]]=$vv;
                            if ($kk == '')
                            {
                                $out_arr_stack2[$key]['Не определено']=$vv;
                            }
                        }
                    }
                    foreach ($out_arr_stack2 as $key => $value) foreach ($value as $ks => $vs) if ($ks=='') unset($out_arr_stack2[$key][$ks]);
                    unset($out_arr_stack);
                    $out_arr_stack=$out_arr_stack2;
                }
                uasort($out_arr_stack, 'countsort');
                foreach ($out_arr_stack as $key => $value) foreach ($value as $ks => $vs) if ($ks=='') unset($out_arr_stack[$key][$ks]);
#echo "-----\n";
#              print_r($out_arr_stack);
#echo "-----\n";
                if ($out_arr_stack == '') {unset($out_arr_stack); $out_arr_stack=$err_mas;}
                echo json_encode($out_arr_stack, true);
                break;
        }
        break;
}
#echo "\n";
}
?>