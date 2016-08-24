/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var words=Array({'name': 'слово1', 'num': 555},
                {'name': 'слово2', 'num': 454},
                {'name': 'слово3', 'num': 384},
                {'name': 'слово4', 'num': 346},
                {'name': 'слово5', 'num': 256},
                {'name': 'слово6', 'num': 243},
                {'name': 'слово7', 'num': 222},
                {'name': 'слово8', 'num': 211},
                {'name': 'слово9', 'num': 200},
                {'name': 'слово10', 'num': 199},
                {'name': 'слово11', 'num': 178},
                {'name': 'слово12', 'num': 135},
                {'name': 'слово13', 'num': 129},
                {'name': 'слово14', 'num': 111},
                {'name': 'слово15', 'num': 110},
                {'name': 'слово16', 'num': 99},
                {'name': 'слово17', 'num': 88},
                {'name': 'слово18', 'num': 77},
                {'name': 'слово19', 'num': 55});

var resources=[['twitter.com', 40,400],['livejournal.com', 36,360],['vkontakte.ru', 7, 70],['mail.ru', 1, 10],['другие', 11,110]
		         ];
var cities=[['Москва', 40,400],['Санкт-Петербург', 36,360],['Уфа', 11,110],['Самара', 7, 70],['Новосибирск', 1, 10],['другие', 11,110]
		         ];

$(document).ready(function(){
    var element=$('#speakers').find('.tablecontent');
    element.html(formtable(10,'speakers'));
    element=$('#speakersshowall').find('.tablecontent');
    element.html(formtable(speakers.length,'speakers'));
    
    element=$('#promouters').find('.tablecontent');
    element.html(formtable(10,'promouters'));
    element=$('#promoutersshowall').find('.tablecontent');
    element.html(formtable(promouters.length,'promouters'));
    
    element=$('#wordsshowall').find('.tablecontent');
    element.html(formtable(words.length,'words'));
    
    element=$('#res_distr').find('.tablecontent');
    formPieLegend('resources',element);
    element=$('#city_distr').find('.tablecontent');
    formPieLegend('cities',element);
    initFiveCorner($(".circle"));
    
    $('#sd').change(function(){
        //var event = jQuery.Event("selection");
//        var event=new Object();
        var min=Date.UTC(StrToDate($('#sd').val()).getFullYear(),StrToDate($('#sd').val()).getMonth(),StrToDate($('#sd').val()).getDate());
        var max=Date.UTC(StrToDate($('#ed').val()).getFullYear(),StrToDate($('#ed').val()).getMonth(),StrToDate($('#ed').val()).getDate());
//        alert(masterChart);
//        masterChart.xAxis.removePlotBand('mask-before');
//        masterChart.xAxis.addPlotBand({
//                     id: 'mask-before',
//                     from: Date.UTC(StrToDate(time_beg).getFullYear(),StrToDate(time_beg).getMonth(),StrToDate(time_beg).getDate()),
//                     to: sd,
//                     color: 'rgba(0, 0, 0, 0.2)'
//                  });
////        masterChart.xAxis[0].min=sd;
////        masterChart.xAxis[0].max=ed;
//        masterChart.redraw();
        //masterChart.trigger(event);
//        var detailData = [],
//                     xAxis = masterChart.xAxis[0];
//                   
//                     
//                  // reverse engineer the last part of the data
//                  jQuery.each(masterChart.series[0].data, function(i, point) {
//                     if (point.x > min && point.x < max) {
//                        detailData.push({
//                           x: point.x,
//                           y: point.y
//                        });
//                     }
//        });
});
});

function formtable(num,name)
{
    var mas;
    switch(name)
    {
        case 'speakers':
            mas=speakers;
            break;
        case 'promouters':
            mas=promouters;
            break;
        case 'words':
            mas=words;
            break;
    }
    var text_html='';
    for(var i=0;i<num;i++)
        {
            text_html+='<div class="clear">';
            text_html+='<p class="span-1 text-right">'+(i+1)+'</p>';
            text_html+='<a class="span-3 ">'+mas[i].name+'</a>';
            text_html+='<p class="span-2 last">'+mas[i].num+'</p>';
            text_html+='</div>';
        }
    return text_html;
}


function formPieLegend(name,element)
{
    var mas;
    switch(name)
    {
        case 'cities':
            mas=cities;
            break;
        case 'resources':
            mas=resources;
            break;
    }
    var text_html='';
    var others;
    for(var i=0;i<mas.length;i++)
        {
            if (mas[i][0]!='другие')
                {
                    text_html='';
                    text_html+='<div class="row span-7 last">';
                    text_html+='<div class="span-4"><div class="circle left"></div>';
                    text_html+=mas[i][0]+'</div>';
                    text_html+='<p class="span-1">'+mas[i][1]+'</p>';
                    text_html+='<p class="span-2 last">'+mas[i][2]+'</p></div>';
                    element.append(text_html);
                    element.find('.circle').last().css('background-color',colors[i%9]);
                }
                else
                    {
                        others=mas[i];
                        others[3]=i;
                    }
                    
        }
                    text_html='<div class="row span-7 last">';
                    text_html+='<div class="span-4"><div class="circle left"></div>';
                    text_html+='<a class="text-grey dottedgrey">'+others[0]+'</a></div>';
                    text_html+='<p class="span-1">'+others[1]+'</p>';
                    text_html+='<p class="span-2 last">'+others[2]+'</p></div>';
                    element.append(text_html);
                    element.find('.circle').last().css('background-color',colors[others[3]%9]);
    return text_html;
}