<?php
    $mas = array(
        'жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж',
                'Я на RIW', 
        'жжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжжж');
    $names=array('Anna','Michail','Alex','Stranger','X-man');
    $avatars=array('images/avatar.png','images/fb.png');
    $source=array("twitter.com","facebook.com");
    $arr1=null;
    
    $n=rand(0,3);
    for($i=0;$i<$n-1;$i++)
    {
        $arr1[$i] = array (post_source=>$source[rand(0,1)],'post_nick'=>$names[rand(0,4)],'post_avatar'=>$avatars[rand(0,1)],'post_msg'=>$mas[rand(0,2)],'post_date'=>@date());
    }
    
    //$res = array ('res'=>$arr1);
    
    echo json_encode($arr1, true);
//    $mas = array(
//        "Пришла HR-менеджер и говорит Я считаю, нам нужно улучшить дизайн сайта. А Я СЧИТАЮ, ЧТО НАМ НУЖЕН ВОЛНИСТЫЙ ПОПУГАЙЧИК НА ТВОЮ ДОЛЖНОСТЬ!",
//                "Я на RIW", 'Закачал таки всю свою музыку в #GoogleMusic. Заняло примерно 30 часов.');
//    $n=rand(0,2);
//    echo $mas[$n];
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
