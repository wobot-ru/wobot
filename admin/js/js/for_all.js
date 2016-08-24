/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var time_beg='05.09.2011';
var time_end='18.10.2011';
var speakers = Array({'name': 'blogerprotiv', 'num': 129},
        {'name': 'sergeydolya', 'num': 55},
        {'name': 'kukmor', 'num': 38},
        {'name': 'tema', 'num': 34},
        {'name': 'al-lip', 'num': 34},
        {'name': 'Digga88', 'num': 34},
        {'name': 'alxr_gol', 'num': 28},
        {'name': 'antonprok', 'num': 25},
        {'name': 'ko-lenochka', 'num': 21},
        {'name': 'Turtseva', 'num': 20},
        {'name': 'спикер10', 'num': 444},
        {'name': 'спикер11', 'num': 555},
        {'name': 'спикер12', 'num': 666},
        {'name': 'спикер13', 'num': 777},
        {'name': 'спикер14', 'num': 888},
        {'name': 'спикер15', 'num': 999},
        {'name': 'спикер16', 'num': 1111},
        {'name': 'спикер17', 'num': 783},
        {'name': 'спикер18', 'num': 123},
        {'name': 'спикер19', 'num': 453},
        {'name': 'спикер20', 'num': 11});

var promouters = Array({'name': 'blogerprotiv', 'num': 129},
        {'name': 'sergeydolya', 'num': 55},
        {'name': 'kukmor', 'num': 38},
        {'name': 'tema', 'num': 34},
        {'name': 'al-lip', 'num': 34},
        {'name': 'Digga88', 'num': 34},
        {'name': 'alxr_gol', 'num': 28},
        {'name': 'antonprok', 'num': 25},
        {'name': 'ko-lenochka', 'num': 21},
        {'name': 'Turtseva', 'num': 20},
        {'name': 'sergeydolya', 'num': 55},
        {'name': 'kukmor', 'num': 38},
        {'name': 'tema', 'num': 34},
        {'name': 'al-lip', 'num': 34},
        {'name': 'Digga88', 'num': 34},
        {'name': 'alxr_gol', 'num': 28},
        {'name': 'antonprok', 'num': 25},
        {'name': 'ko-lenochka', 'num': 21},
        {'name': 'Turtseva', 'num': 20},
        {'name': 'спикер9', 'num': 322},
        {'name': 'спикер10', 'num': 444},
        {'name': 'спикер11', 'num': 555},
        {'name': 'спикер12', 'num': 666},
        {'name': 'спикер13', 'num': 777},
        {'name': 'спикер14', 'num': 888},
        {'name': 'спикер15', 'num': 999},
        {'name': 'спикер16', 'num': 1111},
        {'name': 'спикер17', 'num': 783},
        {'name': 'спикер18', 'num': 123},
        {'name': 'спикер19', 'num': 453},
        {'name': 'спикер20', 'num': 11});
        
        
        
        
        
$(document).ready(function(){
    //закругление углов
    $(".highlightpage").corner("20px");
    $(".greenbtn").corner("5px");
    $(".greybtn").corner("5px");
    $(".btntag").corner("5px hiddenParent");
    initSmallBtns($(".smallbtn"));
    $(".keyword").corner("5px");
    
    //$("#speakers_popup").corner("20px hiddenParent right");
    //$("#help").corner("20px bottom hiddenParent");
    //$("#headercenter").corner("20px hiddenParent");
    
    $(".fancypopup a").fancybox({
				'titlePosition'		: 'inside',
				'transitionIn'		: 'none',
				'transitionOut'		: 'none'
			});
    
    $('#need_help').click(function(){
        
        if($('#help').css("display")=='none')
            $('#help').fadeIn("slow");
        else
            $('#help').fadeOut("slow");
         
        });
   $('#hide_help').click(function(){
        $('#help').fadeOut("slow");
        });
//    $('#help').mouseleave(function(){
//        
//        $('#help').fadeOut("slow");
//         
//        });
//    $('#header').mouseleave(function(){
//        
//        $('#help').fadeOut("slow");
//         
//        });
});

function loadTime()
{
    var sd = datePickerController.getDatePicker("sd");
    var ed = datePickerController.getDatePicker("ed");
    var dt = datePickerController.dateFormat($('#sd').val(), sd.format.charAt(0) == "m");
    var et = datePickerController.dateFormat($('#ed').val(), ed.format.charAt(0) == "m");
    ed.setRangeLow( dt );ed.setRangeHigh( et );
    sd.setRangeLow( dt );sd.setRangeHigh( et );
}

function initSmallBtns(element)
{
    element.corner("5px hiddenParent");
}

function initFiveCorner(element)
{
    element.corner("5px");
}

