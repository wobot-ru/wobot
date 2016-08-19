<?php
    $mas = array(
        "Пришла HR-менеджер и говорит Я считаю, нам нужно улучшить дизайн сайта. А Я СЧИТАЮ, ЧТО НАМ НУЖЕН ВОЛНИСТЫЙ ПОПУГАЙЧИК НА ТВОЮ ДОЛЖНОСТЬ!",
                "Я на RIW", 'Закачал таки всю свою музыку в #GoogleMusic. Заняло примерно 30 часов.');
    $names=array('Anna','Michail','Alex','Stranger','X-man');
    $avatars=array('images/avatar.png','images/fb.png');
    
    $arr1=null;
    $arr2=null;
    
    $n=rand(0,3);
    for($i=0;$i<$n-1;$i++)
    {
        $arr1[$i] = array ('name'=>$names[rand(0,4)],'avatar'=>$avatars[rand(0,1)],'post'=>$mas[rand(0,2)],'time'=>@date("H:i:s"));
    }
    
    $n=rand(0,2);
    for($i=0;$i<$n-1;$i++)
    {
        $arr2[$i] = array ('name'=>$names[rand(0,4)],'avatar'=>$avatars[rand(0,1)],'post'=>$mas[rand(0,2)],'time'=>@date("H:i:s"));
    }
    
    $res = array ('twitter'=>$arr1,'fb'=>$arr2);
    $res = array ('res'=>$res);
    
    echo json_encode($res, true);
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
