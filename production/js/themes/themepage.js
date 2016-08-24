
var wasChanged = false;
var id;
var chart, chart2;
var minDate, maxDate, graphtype, tickInt;
var urlCom,shift/*,xmax*/;

function updateTips( t, tip ) {$(tip).text( t );}

function formatDate(unixTimestamp) {
    if (unixTimestamp == null) return null;
    var dt = new Date(parseInt(unixTimestamp));
    // alert()
    var day = dt.getDate();
    var month = dt.getMonth()+1;
    var year = dt.getFullYear();

    /*alert(day);
     alert(month);
     alert(year);
     */
    // the above dt.get...() functions return a single digit
    // so I prepend the zero here when needed
    if (day < 10)
        day = '0' + day;

    if (month < 10)
        month = '0' + month;

    //alert (day + "." + month + "." + year);
    return day + "." + month + "." + year;
}

/*
 Открывает модельное окно попапа
 */
function loadmodal(href,width,height,type) {
    if (!width) width=804;
    if (!height) height=500;
    $.fancybox({
        'href' 				: href,
        'width'				: width,
        'height'			: height,
        'scrolling'         : 'no',
        'titleShow'         : false,
        'padding'           : 0,
        'autoScale'         : false,
        'transitionIn'      : 'none',
        'transitionOut'     : 'none',
        type    			: type,
        onClosed : function() { $(".EXTERN").css('display','none');}
    });
}

var masterChart, detailChart;
// create the master chart
function createMaster(series) {
    /*Highcharts.setOptions({
     global: {
     useUTC: true
     }
     });*/

    /*masterChart = new Highcharts.Chart({
     colors: [
     '#7f7bdb',
     '#c0ac97',
     '#b0c78f',
     '#c09797',
     '#97c0c0',
     '#86c4d1',
     '#92A8CD',
     '#A47D7C',
     '#B5CA92'
     ],
     chart: {
     renderTo: 'master-container',
     reflow: false,
     borderWidth: 0,
     backgroundColor: null,
     marginLeft: 40,
     marginRight: 20,
     zoomType: 'x',
     type: 'area',
     events: {

     // listen to the selection event on the master chart to update the
     // extremes of the detail chart
     selection: function(event) {
     var extremesObject = event.xAxis[0],
     min = extremesObject.min,
     max = extremesObject.max,
     detailData = [],
     xAxis = this.xAxis[0],
     extr = xAxis.getExtremes ();
     if ((max - min) < 11 * 24 * 3600000) {
     min = Math.floor((min+max)/2) - 432000000;
     max = Math.ceil((min+max)/2) + 432000000;
     }

     detailChart.xAxis[0].setExtremes(min - 3 * 3600000,max + 3 * 3600000);

     // move the plot bands to reflect the new detail span
     xAxis.removePlotBand('mask-before');
     xAxis.addPlotBand({
     id: 'mask-before',
     from: extr.min,
     to: min,
     color: 'rgba(0, 0, 0, 0.2)'
     });

     xAxis.removePlotBand('mask-after');
     xAxis.addPlotBand({
     id: 'mask-after',
     from: max,
     to: extr.max,
     color: 'rgba(0, 0, 0, 0.2)'
     });




     return false;
     }
     }
     },
     title: {
     text: null
     },
     xAxis: {
     type: 'datetime',
     // showLastTickLabel: true,
     // maxZoom: 1 * 24 * 3600000,
     title: {
     text: null
     }
     },
     yAxis: {
     gridLineWidth: 0,
     labels: {
     enabled: false
     },
     title: {
     text: null
     },
     min: 0.6,
     showFirstLabel: false
     },
     tooltip: {
     formatter: function() {
     return false;
     }
     },
     legend: {
     enabled: false
     },
     credits: {
     enabled: false
     },
     plotOptions: {
     series: {
     fillOpacity: 0.4,

     lineWidth: 1,
     marker: {
     enabled: false
     },
     shadow: false,
     states: {
     hover: {
     lineWidth: 1
     }
     },
     enableMouseTracking: false
     }
     },

     series: series,

     exporting: {
     enabled: false
     }

     }, function(masterChart) {*/

    createDetail(masterChart,series)
    /*});*/
};

// create the detail chart
function createDetail(masterChart,series) {
    var detailData = series;
    // create a detail chart referenced by a global variable
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    detailChart = new Highcharts.Chart({
        chart: {
            marginBottom: 80,
            //renderTo: 'lines-diagramm',
            //renderTo: 'grafikk',
            renderTo: 'detail-container',
            reflow: false,
            marginLeft: 40,
            marginRight: 20,
            style: {
                position: 'absolute'
                //top: '200'
                //position: 'relative'
            },


            spacingLeft: 0,
            height: 281,
            width: 878

        },
        colors: [
            '#7f7bdb',
            '#c0ac97',
            '#b0c78f',
            '#c09797',
            '#97c0c0',
            '#86c4d1',
            '#92A8CD',
            '#A47D7C',
            '#B5CA92'
        ],
        legend: {
            enable:false
        },
        xAxis: {
            type: 'datetime',
            lineColor: '#000',
            //min: 1309204800000,
            labels: {
                formatter: function()
                {
                    if ($.cookie(id+"-fromDate-theme")!=null)
                    {
                        var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                    }
                    else
                    {
                        var rangestart=new Date(minDate);
                    }
                    var tttext='';
                    if (graphtype=='hour')
                    {
                        tttext= (new Date((this.value-1)*3600000+shift)).format("H:00");
                    }
                    else if (graphtype=='day')
                    {
                        tttext=(new Date((this.value-1)*86400000+shift)).format("d.mm");
                    }
                    else if (graphtype=='week')
                    {
                        var rngstart=new Date((this.value-1)*7*86400000-86400000*6+shift);
                        if (rngstart>rangestart) tttext=rngstart.format("d")+'-'+(new Date((this.value-1)*7*86400000+shift)).format("d.mm");
                        else tttext=rangestart.format("d")+'-'+(new Date((this.value-1)*7*86400000+shift)).format("d.mm");
                    }
                    else if (graphtype=='month')
                    {
                        var rngstart=new Date((this.value-1)*30*86400000-86400000*29+shift);
                        if (rngstart>rangestart) tttext=rngstart.format("m.yy")+'-'+(new Date(((this.value-1)*30*86400000+shift))).format("m.yy");
                        else tttext=rangestart.format("m.yy")+'-'+(new Date(((this.value-1)*30*86400000+shift))).format("m.yy");
                        //var rngstart=new Date(this.value-86400000*30-shift);
                        //if (rngstart>rangestart) tttext=rngstart.format("m")+'-' +(new Date(this.value-shift)).format("m.yy");
                        //else tttext=rangestart.format("m")+'-'+(new Date(this.value-shift)).format("m.yy");
                        //tttext=(new Date(this.value-shift)).format("d.mm.yy");
                        //alert('this.value: '+this.value+' shift: '+shift+' calc: '+((this.value-1)*30*86400000+shift));
                        //tttext=((this.value-1)*86400000*30)+shift;
                        //tttext=new Date(((this.value-1)*30*86400000+shift)).format("d.mm.yy");
                    }
                    else if (graphtype=='quarter')
                    {
                        var rngstart=new Date((this.value-1)*90*86400000-86400000*89+shift);
                        if (rngstart>rangestart) tttext=rngstart.format("m.yy")+'-'+(new Date(((this.value-1)*90*86400000+shift))).format("m.yy");
                        else tttext=rangestart.format("m.yy")+'-'+(new Date(((this.value-1)*90*86400000+shift))).format("m.yy");
                    }
                    else if (graphtype=='halfyear')
                    {
                        var rngstart=new Date((this.value-1)*180*86400000-86400000*179+shift);
                        if (rngstart>rangestart) tttext=rngstart.format("m.yy")+'-'+(new Date(((this.value-1)*180*86400000+shift))).format("m.yy");
                        else tttext=rangestart.format("m.yy")+'-'+(new Date(((this.value-1)*180*86400000+shift))).format("m.yy");
                    }
                    return tttext;
                },
                staggerLines : 2,
                y: 25
            },
            tickWidth: 1,
            tickColor: '#000',
            //min: 1,
            //max: xmax,
            //maxPadding: 0,
            minPadding: 0,
            //tickmarkPlacement: 'between',
            //tickmarkPlacement: 'on',
            tickInterval: tickInt,
            //startOnTick: true,
            //endOnTick: true,
            minorTickPosition: 'inside',
            //showFirstLabel: false,
            //showLastLabel: false,
            //minRange: 864000000
            gridLineWidth: 1
        },
        yAxis: {lineWidth: 1, lineColor: '#000',tickWidth: 1,tickColor: '#000',	gridLineWidth: 1,minorGridLineWidth:1, minorGridLineColor: '#F0F0F0',minorTickWidth: 1, minorTickInterval: 'auto',title: {text:""},allowDecimals: false,min: 0},
        title: { text: ""},
        plotOptions: {
            series: {
                animation: false,
                shadow: false,
                lineWidth: 3,
                cursor: 'pointer',
                point: {
                    events: {
                        'click': function() {
                            //alert(new Date(this.category).format("dd.mm.yyyy"));
                            //alert(this.series.data.length);
                            //day week month
                            if (graphtype=='week')
                            {
                                if ($.cookie(id+"-fromDate-theme")!=null)
                                {
                                    var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                                }
                                else
                                {
                                    var rangestart=new Date(minDate);
                                }
                                var pstart = new Date((this.category-1)*86400000*7+shift-86400*6*1000);
                                var pend = new Date((this.category-1)*86400000*7+shift);
                                if (rangestart>pstart) pstart=rangestart;
                            }
                            else if (graphtype=='month')
                            {
                                /*if ($.cookie(id+"-fromDate-theme")!=null)
                                 {
                                 var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                                 }
                                 else
                                 {
                                 var rangestart=new Date(minDate);
                                 }
                                 var pstart = new Date(this.category-86400*29*1000-29*4*3600000);
                                 var pend = new Date(this.category-29*4*3600000);
                                 if (rangestart>pstart) pstart=rangestart;*/
                                if ($.cookie(id+"-fromDate-theme")!=null)
                                {
                                    var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                                }
                                else
                                {
                                    var rangestart=new Date(minDate);
                                }
                                var pstart = new Date((this.category-1)*86400000*30+shift-86400*29*1000);
                                var pend = new Date((this.category-1)*86400000*30+shift);
                                if (rangestart>pstart) pstart=rangestart;
                            }
                            else if (graphtype=="quarter" )
                            {
                                if ($.cookie(id+"-fromDate-theme")!=null)
                                {
                                    var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                                }
                                else
                                {
                                    var rangestart=new Date(minDate);
                                }
                                var pstart = new Date((this.category-1)*86400000*90+shift-86400*89*1000);
                                var pend = new Date((this.category-1)*86400000*90+shift);
                                if (rangestart>pstart) pstart=rangestart;

                            }
                            else if (graphtype=="halfyear" )
                            {
                                if ($.cookie(id+"-fromDate-theme")!=null)
                                {
                                    var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                                }
                                else
                                {
                                    var rangestart=new Date(minDate);
                                }
                                var pstart = new Date((this.category-1)*86400000*180+shift-86400*179*1000);
                                var pend = new Date((this.category-1)*86400000*180+shift);
                                if (rangestart>pstart) pstart=rangestart;

                            }
                            else if (graphtype=='day')
                            {
                                var pstart = new Date((this.category-1)*86400000+shift);
                                var pend = new Date((this.category-1)*86400000+shift);
                            }
                            else if (graphtype=='hour')
                            {
                                var pstart = new Date(shift);
                                var pend = new Date(shift);
                            }
                            //alert(pstart+' '+pend+' '+graphtype);

                            if (graphtype=='hour')
                            {
                                $("#dp-begin").datepicker("setDate",pstart);
                                $("#dp-end").datepicker("setDate",pend);
                                $("#date #datepicker").val(
                                    dateToWords(pstart.format("dd.mm.yyyy"),true)+" - "+
                                        dateToWords(pend.format("dd.mm.yyyy"),true));
                                $.cookie(id+"-fromDate-theme",pstart.getTime());
                                $.cookie(id+"-toDate-theme",pend.getTime());
                                $.cookie(id + "-md5", '');
                                window.open(urlCom, '_blank');
                            }
                            else
                            {
                                $("#dp-begin").datepicker("setDate",pstart);
                                $("#dp-end").datepicker("setDate",pend);
                                $("#date #datepicker").val(
                                    dateToWords(pstart.format("dd.mm.yyyy"),true)+" - "+
                                        dateToWords(pend.format("dd.mm.yyyy"),true));
                                $.cookie(id+"-fromDate-theme",pstart.getTime());
                                $.cookie(id+"-toDate-theme",pend.getTime());
                                $.cookie(id + "-md5", '');
                                loadContent(id,pstart.format("dd.mm.yyyy"),pend.format("dd.mm.yyyy"));
                            }
                            //if (pstart.getTime() < minDate.getTime()) pstart = minDate;
                            //if (pend.getTime() > maxDate.getTime()) pend = maxDate;

                            //var boundaries = getDateBoundaries(minDate,maxDate,pstart,pend);
                            //today = boundaries[2];

                            //$("#dp-end").datepicker("option", "minDate", minDate);
                            //$("#dp-begin").datepicker("option", "maxDate", maxDate);

                            //$("#dp-begin").datepicker("setDate",today);
                            //$("#dp-end").datepicker("setDate",today);

                            //$("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
                            //$("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));


                            //$("#date #datepicker").val(
                            //    dateToWords(today.format("dd.mm.yyyy"),true)+" - "+
                            //        dateToWords(today.format("dd.mm.yyyy"),true));
                        }
                    }
                },
                marker: {
                    fillColor: '#FFFFFF',
                    lineWidth: 2,
                    lineColor: null, // inherit from series,
                    radius: 3,
                    symbol: "circle"
                }

            }
        },
        tooltip: {
            enabled: true,
            formatter: function() {
                if (graphtype=='hour') tttext=(new Date((this.x-1)*3600000+shift)).format("H:00");
                if (graphtype=='day') tttext=(new Date((this.x-1)*86400000+shift)).format("d.mm.yyyy");
                //var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                if ($.cookie(id+"-fromDate-theme")!=null)
                {
                    var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
                }
                else
                {
                    var rangestart=new Date(minDate);
                }
                //$.cookie(id+"-fromDate-theme")

                if (graphtype=='week')
                {
                    var rngstart=new Date((this.x-1)*86400000*7+shift-86400*6*1000);
                    if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*7+shift)).format("dd.mm.yy");
                    else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*7+shift)).format("dd.mm.yy");
                }
                if (graphtype=='month')
                {
                    /*var rngstart=new Date(this.x-86400*29*1000-30*4*3600000);
                     if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date(this.x-30*4*3600000)).format("dd.mm.yy");
                     else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date(this.x-30*4*3600000)).format("dd.mm.yy");*/
                    var rngstart=new Date((this.x-1)*86400000*30+shift-86400*29*1000);
                    if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*30+shift)).format("dd.mm.yy");
                    else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*30+shift)).format("dd.mm.yy");
                }
                if (graphtype=='quarter')
                {
                    var rngstart=new Date((this.x-1)*86400000*90+shift-86400*89*1000);
                    if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*90+shift)).format("dd.mm.yy");
                    else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*90+shift)).format("dd.mm.yy");
                }

                if (graphtype=='halfyear')
                {
                    var rngstart=new Date((this.x-1)*86400000*180+shift-86400*179*1000);
                    if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*180+shift)).format("dd.mm.yy");
                    else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date((this.x-1)*86400000*180+shift)).format("dd.mm.yy");
                }
                //Дата: '+ (new Date(this.x)).format("dd.mm.yyyy") +'
                return '<b>'+ this.series.name +'</b><br/>Постов '+ this.y + '<br/>'+tttext;
            }
        },
        series: detailData,
        navigation: {
            buttonOptions: {
                enabled: false
            }
        }
    });
};



Array.prototype.shuffle = function() {
    var len = this.length;
    var i = len;
    while (i--) {
        var p = parseInt(Math.random()*len);
        var t = this[i];
        this[i] = this[p];
        this[p] = t;
    }
};


function loadmodal(href,width,height)
{
    if (!width) width=804;
    if (!height) height=500;
    $.fancybox({
        'href' 				: href,
        'width'				: width,
        'height'			: height,
        'scrolling'         : 'no',
        'titleShow'         : false,
        'padding'           : 0,
        'autoScale'         : false,
        'transitionIn'      : 'none',
        'transitionOut'     : 'none',
        type    			: "inline",
        onClosed : function() {
            $(".EXTERN").css('display','none');
        }
    });
}

function showPopup(idd,title,h1,h2,data,chart) {



    if (typeof(data[0]) == 'undefined')
    {
        //alert('0');
    }
    else
    {
        var strr ="";

        var inline;



        if ($("#"+idd).length != 0) $("#"+idd).remove();
        inline+= ' <div id="'+idd+'" class="EXTERN" style="display: block;">\
	<div  class="span-7 last" style="margin: 5px; width:780px; height:465px;"> \
	<table> \
	<tr> \
	<td> ';

        if (chart=='map')
        {
            inline += '<iframe src="/geochart.html#'+id+'" width="485" height="300" align="left" style="scrolling:no;">\
	    Ваш браузер не поддерживает плавающие фреймы!\
	 </iframe>';
        }
        else
        {
            inline += '<div class="inline pie-diag" id="diagramm-popup"></div> ';
        }

        inline +='	</td> \
	<td> \
    	<h4 class="span-3" style="margin-bottom: -6px; text-align: center; width: 285px;">'+title+'</h4>';




        inline += '<div class="row clear"></div> \
		<div class="text-black"> \
				<div class="row span-6 last text-lightgrey bold " style="border-bottom: 1px solid; width: 290px;"> \
		            <p class="span-2 text-center" style="width: 55px">№</p> \
		            <p class="span-3 text-center" style="width: 85px">'+h1+'</p>';

        //alert (typeof(data[0]));

        if (data[0].count_posts != undefined) inline+='<p class="span-2 text-center" style="width: 50px">'+h2+'</p> \
	<p class="span-1 text-center" style="width: 45px">Постов</p>';
        else inline+='<p class="span-2 text-right" style="width: 75px">'+h2+'</p>';


        inline +='	        </div>';



        inline +='      <div class="tableheaderborder clear"></div> \
                <div class="tablecontent span-7 last scroll" style="margin-left: 10px;">';

        var scrollname;
        //alert(title);
        if (title == 'Города') scrollname='scrollbarGor';
        else if (title == 'Ресурсы') scrollname='scrollbarRes';
        else if (title == 'Облако тегов') scrollname='scrollbarObl';
        else if (title == 'Лидеры мнений') scrollname='scrollbarLid';
        else if (title == 'Список слов') scrollname='scrollbarWord';
        else if (title == 'Охват') scrollname='scrollbarOhv';
        else if (title == 'Вовлеченность') scrollname='scrollbarVovl';

        //alert(title);
        //скроллинг начало
        inline += '<div id=\"'+scrollname+'\" class="scrollbar3"><div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div><div class="viewport"><div class="overview">';
        inline += '<table style="width: 250px;" class="dialog-List2">';



        if (typeof(data[0])=='undefined')
        {
            // alert(typeof(data[0]));
        }
        else if (data[0].count_posts == undefined)
        {
            $.each(data, function(i,e) {
                var name = e.name;
                var namenl = name;
                if (name == undefined) name= e.nick;
                if (name == undefined) name= e.word;
                if (idd == 'resources-popup')
                {
                    //lnk='http://'+name;
                    if (name=='.') return true;
                    name='<a href="http://'+name+'" target="_blank">'+name.substr(0,20)+'</a>';
                }
                if (idd == 'tags-popup') {namenl = e.word;}
                //else if (idd == '')
                strr+=" "+namenl+"\t"+e.count+"\n";
                inline += '\
			<tr bgcolor="#fff"> \
				<td bgcolor="#fff"><div style="width: 35px; overflow: hidden">'+(i+1)+'</div></td> \
				<td bgcolor="#fff"><div style="width: 130px; overflow: hidden">'+name+'</div></td> \
				<td bgcolor="#fff"><div style="width: 45px; overflow: hidden">'+e.count+'</div></td> \
			</tr>';
            });
        }
        else
        {
            $.each(data, function(i,e) {
                var name = e.name;
                if (name == undefined) name= e.nick;
                if (name == undefined) name= e.word;
                strr+=" "+name+"\t"+e.count+"\t"+e.count_posts+"\n";
                inline += '\
			<tr bgcolor="#fff"> \
				<td bgcolor="#fff"><div style="width: 35px; overflow: hidden">'+(i+1)+'</div></td> \
				<td bgcolor="#fff"><div style="width: 85px; overflow: hidden">'+name+'</div></td> \
				<td bgcolor="#fff"><div style="width: 45px; overflow: hidden">'+e.count+'</div></td> \
				<td bgcolor="#fff"><div style="width: 43px; overflow: hidden">'+e.count_posts+'</div></td> \
			</tr>';
            });
        }

        inline +='\
				</table>';

        //скроллинг конец
        inline += '</div></div></div>';


        inline+=   '</div> \
		<div class="row clear"></div> \
		<div class="row clear" style="position:relative;">\
        <a class="span-7 last text-right text-lightgrey" id="'+idd+'-copy-button" style="font-size: 12px;">копировать в буфер</a></div> \
		</td> \
		</tr> \
		</table> \
</div></div>';



        $("body").append($(inline));


        //скроллинг инициализация
        $('#'+scrollname).tinyscrollbar();
        $('#'+scrollname).tinyscrollbar_update();
        //$('#scrollbar3').tinyscrollbar_update();

        loadmodal("#"+idd,1000,500);



        if (chart=='map')
        {

        }
        else
        {
            $('#diagramm-popup').html('');
            chart2 = new Highcharts.Chart({
                chart: {
                    renderTo: 'diagramm-popup',
                    width: 470,
                    height: 300,
                    plotBorderWidth: null
                },
                colors: [
                    "#7f7bdb","#c0ac97","#b0c78f","#c09797","#97c0c0","#86c4d1"
                ],
                legend : {
                    enabled: false
                    /*	style: {
                     fontSize: '10px'
                     }*/
                },
                credits : {
                    enabled: false
                },
                title: {
                    text: ''
                },
                tooltip: {
                    formatter: function() {
                        //return '<p style="font-size: 13px;"><b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %</p>';
                        return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
                    },
                    borderRadius: 3,
                    borderWidth: 1,
                    style: {
                        fontSize: '10px'
                    }
                },
                plotOptions: {
                    pie: {
                       // cropThreshold: 20,
                        animation: false,
                        shadow: false,
                        /*dataLabels: {
                         enabled: false,		*/
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            softConnector: true,
                            borderRadius: 0,
                            connectorWidth: 1,
                            overflow: 'justify',
                            distance: 30,
                            formatter: function() {
                                
                                if (Math.round(this.percentage)>0) {
                                    return '<div style="font-size: 10px; width: 20px; border: 1px solid #f00; display: block;">'+ this.point.name +': '+ Math.round(this.percentage) +'%</div>';
                                }
                            }

                        },
                        showInLegend: true
                    }
                },
                series: [{
                    type: 'pie',
                    data: chart/*,
                     dataLabels: { distance: 10 }*/
                }],
                navigation: {
                    buttonOptions: {
                        enabled: false
                    }
                }
            });
        }
//<a id="various1" href="#inline1" title="Lorem ipsum dolor sit amet">Inline</a><
        /*
         <div style="display: none;">
         <div id="inline1" style="width:400px;height:100px;overflow:auto;">
         Lorem ipsum dolor sit amet, consectetur adipiscing elit ...
         </div>

         </div>*/


        $("#"+idd+"-copy-button").zclip({
            path: "js/ZeroClipboard.swf",
            beforeCopy: function() {/*alert('pressed');*/},
            afterCopy: function(){ return false; },
            copy: function(){
                return strr;
            }
        });

    }
};



function dateToWords(inDate, haveYear) {
    var date = inDate.split(".");

    var month = "";
    switch (parseInt(date[1],10)) {
        case 1 : month = "января"; break;
        case 2 : month = "февраля";break;
        case 3 : month = "марта";break;
        case 4 : month = "апреля";break;
        case 5 : month = "мая";break;
        case 6 : month = "июня";break;
        case 7 : month = "июля";break;
        case 8 : month = "августа";break;
        case 9 : month = "сентября";break;
        case 10 : month = "октября";break;
        case 11: month = "ноября";break;
        case 12: month = "декабря";		break;
    };

    var result = parseInt(date[0],10)+" "+month;
    if (haveYear == true) result = result + " '"+parseInt(date[2],10)%100;
    return result;
}

function today() {
    var today = Date.today();
    if (today.getTime() > maxDate.getTime()) today = maxDate;

    var boundaries = getDateBoundaries(minDate,maxDate,today,today);
    today = boundaries[2];

    $("#dp-end").datepicker("option", "minDate", minDate);
    $("#dp-begin").datepicker("option", "maxDate", maxDate);

    $("#dp-begin").datepicker("setDate",today);
    $("#dp-end").datepicker("setDate",today);

    $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
    $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));


    $("#date #datepicker").val(
        dateToWords(today.format("dd.mm.yyyy"),true)+" - "+
            dateToWords(today.format("dd.mm.yyyy"),true));

    $.cookie(id+"-fromDate-theme",$("#dp-begin").datepicker("getDate").getTime());
    $.cookie(id+"-toDate-theme",$("#dp-end").datepicker("getDate").getTime());
    $.cookie(id + "-md5", '');
    loadContent(id,today.format("dd.mm.yyyy"),today.format("dd.mm.yyyy"));
}

function yesterday() {
    var a = Date.today().add(-1).days();
    if (a.getTime() > maxDate.getTime()) a = maxDate.add(-1).days();

    var boundaries = getDateBoundaries(minDate,maxDate,a,Date.today());
    a = boundaries[2];

    $("#dp-end").datepicker("option", "minDate", minDate);
    $("#dp-begin").datepicker("option", "maxDate", maxDate);

    $("#dp-begin").datepicker("setDate",a);
    $("#dp-end").datepicker("setDate",a);

    $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
    $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

    $("#date #datepicker").val(
        dateToWords(a.format("dd.mm.yyyy"),true)+" - "+
            dateToWords(a.format("dd.mm.yyyy"),true));

    $.cookie(id+"-fromDate-theme",$("#dp-begin").datepicker("getDate").getTime());
    $.cookie(id+"-toDate-theme",$("#dp-end").datepicker("getDate").getTime());
    $.cookie(id + "-md5", '');
    loadContent(id,a.format("dd.mm.yyyy"),a.format("dd.mm.yyyy"));
}

function correctWeekDay(day) {
    if (day == 0) return 7; else return day;
}

function week() {
    var o = Date.today();
    if (o.getTime() > maxDate.getTime()) o = maxDate;

    var a = new Date(o.getTime());
    var b = new Date(o.getTime());
    a.setDate(a.getDate() - 6);
    b.setDate(b.getDate());

    var boundaries = getDateBoundaries(minDate,maxDate,a,b);
    a = boundaries[2];
    b = boundaries[1];

    $("#dp-end").datepicker("option", "minDate", minDate);
    $("#dp-begin").datepicker("option", "maxDate", maxDate);

    $("#dp-begin").datepicker("setDate",a);
    $("#dp-end").datepicker("setDate",b);

    $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
    $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));


    $("#date #datepicker").val(
        dateToWords(a.format("dd.mm.yyyy"),true)+" - "+
            dateToWords(b.format("dd.mm.yyyy"),true));

    $.cookie(id+"-fromDate-theme",$("#dp-begin").datepicker("getDate").getTime());
    $.cookie(id+"-toDate-theme",$("#dp-end").datepicker("getDate").getTime());
    $.cookie(id + "-md5", '');
    loadContent(id,a.format("dd.mm.yyyy"),b.format("dd.mm.yyyy"));
}

function month() {
    var o = Date.today();
    if (o.getTime() > maxDate.getTime()) o = maxDate;

    var a = new Date(o.getTime());
    var b = new Date(o.getTime());

    //a.setFullYear(a.getFullYear(),a.getMonth()-1);
    //b.setFullYear(b.getFullYear(),b.getMonth());
    //var b = new Date(o.getTime());
    a.setDate(a.getDate() - 29);
    b.setDate(b.getDate());

    var boundaries = getDateBoundaries(minDate,maxDate,a,b);
    a = boundaries[2];
    b = boundaries[1];

    $("#dp-end").datepicker("option", "minDate", minDate);
    $("#dp-begin").datepicker("option", "maxDate", maxDate);

    $("#dp-begin").datepicker("setDate",a);
    $("#dp-end").datepicker("setDate",b);

    $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
    $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

    $("#date #datepicker").val(
        dateToWords(a.format("dd.mm.yyyy"),true)+" - "+
            dateToWords(b.format("dd.mm.yyyy"),true));

    $.cookie(id+"-fromDate-theme",$("#dp-begin").datepicker("getDate").getTime());
    $.cookie(id+"-toDate-theme",$("#dp-end").datepicker("getDate").getTime());
    $.cookie(id + "-md5", '');
    loadContent(id,a.format("dd.mm.yyyy"),b.format("dd.mm.yyyy"));
}

function year() {
    var o = Date.today();
    if (o.getTime() > maxDate.getTime()) o = maxDate;

    var a = new Date(o.getTime());
    var b = new Date(o.getTime());

    /*a.setFullYear(a.getFullYear(),0,1);
     b.setFullYear(b.getFullYear(),11,31);
     */
    a.setFullYear(a.getFullYear()-1);
    b.setFullYear(b.getFullYear());


    var boundaries = getDateBoundaries(minDate,maxDate,a,b);
    a = boundaries[2];
    b = boundaries[1];

    $("#dp-end").datepicker("option", "minDate", minDate);
    $("#dp-begin").datepicker("option", "maxDate", maxDate);

    $("#dp-begin").datepicker("setDate",a);
    $("#dp-end").datepicker("setDate",b);

    $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));
    $("#dp-begin").datepicker("option", "maxDate", $("#dp-end").datepicker("getDate"));

    $("#date #datepicker").val(
        dateToWords(a.format("dd.mm.yyyy"),true)+" - "+
            dateToWords(b.format("dd.mm.yyyy"),true));

    $.cookie(id+"-fromDate-theme",$("#dp-begin").datepicker("getDate").getTime());
    $.cookie(id+"-toDate-theme",$("#dp-end").datepicker("getDate").getTime());
    $.cookie(id + "-md5", '');
    loadContent(id,a.format("dd.mm.yyyy"),b.format("dd.mm.yyyy"));
}

function visibleItem(it,isVisible) {
    if (isVisible == true) {
        $(it).parent().removeClass("nosub");
        $(it).parent().find(".sub").toggle();
    } else {
        $(it).parent().addClass("nosub");
        $(it).parent().find(".sub").hide();
    }
}

function getDateBoundaries(fromBoundary,toBoundary,fromAct, toAct) {
    var a1 =  new Date(fromAct.getTime()) ,a2 = new Date(fromAct.getTime());
    // alert(fromBoundary); alert(toBoundary);
    // alert(fromAct); alert(toAct);
    return [
        new Date( Math.min(new Date(Math.max(a1.add(-1).months().getTime(), fromBoundary.getTime() )).getTime(),fromBoundary.getTime()) )
        ,
        new Date(Math.min(toAct.getTime(), toBoundary.getTime() ) ),
        new Date(Math.max(a2.getTime(), fromBoundary.getTime() ))
    ];
}

var theme;
function loadContent(id,dstart,dend) {
    $( "#progressbar" ).progressbar({
        value: 0
    });
    $( ".progress" ).fadeIn(1);

    $.postJSON(ajaxURL_Order,{order_id: id, start: dstart, end: dend}, function(data) {


        //alert(exp+' '+data.user_tarif+' '+data.user_email+' '+data.user_money);

        $("#previewref a").attr("href",inernalURL_messages+id);
        urlCom=inernalURL_messages+id;
        //$("#previewref2").attr("href",inernalURL_messages+id);


        //ЛИСТАЛКА
        /*
         if ((data.order_next != "-1")&&(data.order_next != id))
         {
         $("#next-order").attr('href','#'+data.order_next);
         $("#next-order").unbind().click( function(e) {
         loadContent(data.order_next,null,null);
         //return false;
         });
         $("#next-order").css({"visibility":"visible"});
         }
         else {
         $("#next-order").css({"visibility":"hidden"});
         //$("#next-order").parent().html("&nbsp;");
         }


         if ((data.order_prev != "-1")&&(data.order_prev != id))
         {
         $("#prev-order").attr('href','#'+data.order_prev);
         $("#prev-order").unbind().click( function(e) {
         loadContent(data.order_prev,null,null);
         //return false;
         });
         $("#prev-order").css({"visibility":"visile"});
         }
         else {
         $("#prev-order").css({"visibility":"hidden"});
         //$("#next-order").css("margin-left","21px");
         }*/

        theme = data;

        minDate =  Date.parseExact(data.start,"d.M.yyyy");
        maxDate = Date.parseExact(data.end,"d.M.yyyy");
        graphtype = data.graphtype;

        var boundaries = getDateBoundaries(minDate,maxDate,Date.today(),Date.today());
        var d1;
        if ($.cookie(id+"-fromDate-theme") == null)
            d1 = boundaries[0];
        else d1 = new Date(parseInt($.cookie(id+"-fromDate-theme"),10));

        var d2;
        if ($.cookie(id+"-toDate-theme") == null)
            d2 = boundaries[1];
        else
            d2 = new Date(parseInt($.cookie(id+"-toDate-theme"),10));

        // Установка выбора даты для фильтра
        if (!$("#dp-begin").hasClass("hasDatepicker")) {
            $("#date #datepicker").val(
                dateToWords(d1.format("dd.mm.yyyy"),true)+" - "+
                    dateToWords(d2.format("dd.mm.yyyy"),true));



            $("#dp-begin").datepicker( {
                dateFormat: "dd.mm.yy",
                firstDay: 1,
                minDate: minDate,
                maxDate: maxDate,
                onSelect: function(dateText, inst) {
                    var str = $("#date #datepicker").val();
                    var subs = str.split("-");
                    wasChanged = true;
                    $("#date #datepicker").val(dateToWords(dateText,true)+" -"+ subs[1] );
                    $("#dp-end").datepicker("option", "minDate", $(this).datepicker("getDate"));
                }
            }).datepicker("setDate" , d1);




            $("#dp-end").datepicker( {
                dateFormat: "dd.mm.yy",
                firstDay: 1,
                minDate: minDate,
                maxDate: maxDate,
                onSelect: function(dateText, inst) {
                    var str = $("#date #datepicker").val();
                    var subs = str.split("-");
                    wasChanged = true;
                    $("#date #datepicker").val(subs[0] +"- "+ dateToWords(dateText,true) );

                    $("#dp-begin").datepicker("option", "maxDate", $(this).datepicker("getDate"));
                }
            }).datepicker( "setDate" , d2);

            $("#dp-end").datepicker("option", "minDate", $("#dp-begin").datepicker("getDate"));


        }
        //:~

        $("#dyn").text(''+data.order_name+'');

        $( "#progressbar" ).progressbar( "option", "value", 25 );
        // Панель с миниграффиками

        var value;
        // Постов
        $("#posts").text(data.posts);
        $("#posts").parent().find(" .sub img").attr("src", themapage_Templates.posts.replace("%order_id%", id));
        value = parseInt(data.posts_dyn,10);
        $("#posts").parent().find(".sub-txt p")
            .addClass((value >= 0)?"dyn_plus":((value < 0)?"dyn_minus":""))
            .text((value > 0)?"+"+value:((value < 0)?value:""));
        visibleItem("#posts", false);

        // Уникальных
        $("#uniq").text(data.uniq);
        $("#uniq").parent().find(" .sub img").attr("src", themapage_Templates.uniq.replace("%order_id%", id));
        value = parseInt(data.uniq_dyn,10);
        $("#uniq").parent().find(".sub-txt p")
            .addClass((value >= 0)?"dyn_plus":((value < 0)?"dyn_minus":""))
            .text((value > 0)?"+"+value:((value < 0)?value:""));
        visibleItem("#uniq", false);


        // Ресурсы
        $("#src").text(data.src);
        $("#src").parent().find(" .sub img").attr("src", themapage_Templates.src.replace("%order_id%", id));
        value = parseInt(data.src_dyn,10);
        $("#src").parent().find(".sub-txt p")
            .addClass((value >= 0)?"dyn_plus":((value < 0)?"dyn_minus":""))
            .text((value > 0)?"+"+value:((value < 0)?value:""));
        visibleItem("#src", false);

        // Аудитория
        $("#value").text(data.value);
        $("#value").parent().find(" .sub img").attr("src", themapage_Templates.aud.replace("%order_id%", id));
        value = parseInt(data.value_dyn,10);
        $("#value").parent().find(".sub-txt p")
            .addClass((value >= 0)?"dyn_plus":((value < 0)?"dyn_minus":""))
            .text((value > 0)?"+"+value:((value < 0)?value:""));
        visibleItem("#value", false);


        //alert(data.engage);
        // Вовлеченность
        //$("#engage").text(data.engage);
        /*$("#engage").text("_");
         $("#engage").parent().find(".sub img").attr("src", themapage_Templates.eng.replace("%order_id%", id));
         value = parseInt(data.engage_dyn,10);
         $("#engage").parent().find(".sub-txt p")
         .addClass((value >= 0)?"dyn_plus":((value < 0)?"dyn_minus":""))
         .text((value > 0)?"+"+value:((value < 0)?value:""));	*/
        visibleItem("#engage", false);

        //:~
        $("#L4 .item").each(function(index, element) {
            $(element).mouseenter(function(e) {
                var elem = $(e.target);
                if ($(elem).hasClass("nosub") == true) {
                    $(".sub",elem).show();
                    $(elem).removeClass("nosub");
                }
            });
            $(element).mouseleave(function(e) {
                var elem = $(e.target);
                if ($(elem).hasClass("nosub") != true) {
                    if ($(elem).hasClass("item") == true) {
                        $(".sub",elem).hide();
                        $(elem).addClass("nosub");
                    } else {
                        $(elem).parent(".item").addClass("nosub");
                        $(".sub",$(elem).parent(".item")).hide();
                    }
                }
            });
        });

        $("#resources-diagramm").html("");
        var sources = [];
        var sources_count = 0;
        $(data.sources).each(function(index, element) {
            //if (sources.length <= 5)
            if (sources.length <= 5)
                sources[sources.length] = [element.name, element.count];
            else {
                sources[5][0] = "Другие";
                sources[5][1] += element.count;
            }
            sources_count += element.count;
        });

        $("#promoters-diagramm").html("");
        var promotions = [];
        var promotions_count = 0;
        $(data.promotions).each(function(index, element) {
            //if (sources.length <= 5)
            if (promotions.length <= 5)
                promotions[promotions.length] = [element.nick, element.count];
            else {
                promotions[5][0] = "Другие";
                promotions[5][1] += element.count;
            }
            promotions_count += element.count;
        });

        $("#value-diagramm").html("");
        var value_mdin = [];
        var value_count = 0;
        $(data.value_mdin).each(function(index, element) {
            //if (sources.length <= 5)
            if (value_mdin.length <= 5)
                value_mdin[value_mdin.length] = [element.name, element.count];
            else {
                value_mdin[5][0] = "Другие";
                value_mdin[5][1] += element.count;
            }
            value_count += element.count;
        });

        $("#engage-diagramm").html("");
        var eng_mdin = [];
        var eng_count = 0;
        $(data.eng_mdin).each(function(index, element) {
            //if (sources.length <= 5)
            if (eng_mdin.length <= 5)
                eng_mdin[eng_mdin.length] = [element.name, element.count];
            else {
                eng_mdin[5][0] = "Другие";
                eng_mdin[5][1] += element.count;
            }
            eng_count += element.count;
        });

        $("#cities-diagramm").html("");
        $("#speakers-diagramm").html("");
        $("#tags-diagramm").html("");
        /*
         <table>
         <col width="15px"/>
         <col width="126px"/>
         <col width="36px"/>
         <col width="26px"/>
         <tr class="row-0">
         <td><div class="t t1">&nbsp;</div></td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td><b>&nbsp;</b></td>
         </tr>
         <tr class="row-1">
         <td><div class="t t2">&nbsp;</div></td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td><b>&nbsp;</b></td>
         </tr>
         <tr class="row-2">
         <td><div class="t t3">&nbsp;</div></td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td><b>&nbsp;</b></td>
         </tr>
         <tr class="row-3">
         <td><div class="t t4">&nbsp;</div></td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td><b>&nbsp;</b></td>
         </tr>
         <tr class="row-4">
         <td><div class="t t5">&nbsp;</div></td>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
         <td><b>&nbsp;</b></td>
         </tr>
         <tr class="row-5">
         <td><div class="t t6">&nbsp;</div></td>
         <td><a href="#" class="a-dotted">Другие</a></td>
         <td>&nbsp;</td>
         <td><b>&nbsp;</b></td>
         </tr>
         </table>
         */
        //alert($(sources).size());
        sources_tpl='<table><col width="126px"/><col width="36px"/><col width="26px"/>\
        <tr><th></th><th class="txt-algn-right"><img src="img/pie.png"></th><th class="txt-algn-right"><img src="img/paper.png" class="paperTip"></th></tr>';

        src2draw=(($(sources).size()<5)?$(sources).size():5);
        for(index=0;index<src2draw;index++)
        {
            sources_tpl+='<tr class="row-'+index+'">' +
                /*'<td><div class="t t'+(index+1)+'">&nbsp;</div></td>' +*/
                '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        }
        if ($(sources).size()>5)
        {
            sources_tpl+='<tr class="row-5">' +
                /* '<td><div class="t t6">&nbsp;</div></td>' +*/
                '<td><a href="#" class="a-dotted">Другие</a></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
        }
        sources_tpl+='</table>';
        $("#resources-table").html(sources_tpl);

        //alert(sources_tpl);
        //alert('sources count: '+sources_count);
        $(sources).each(function(index, element) {
            sources[index][2] = Math.round(sources[index][1] / sources_count * 100);
            if (index < 5){
            //$($("#resources-table .row-"+index+" td")[1]).text(sources[index][0]);
                //$($("#resources-table .row-"+index+" td")[0]).html('<a href="http://'+sources[index][0]+'" target="_blank">'+sources[index][0]+'</a>');
                $($("#resources-table .row-"+index+" td")[0]).html('<a href="messages_list.html#'+id+'"  onclick="$.cookie(\''+id+'-resources-msg\',\''+sources[index][0]+'\'); window.location.href = \'messages_list.html#'+id+'\'; return false;">'+sources[index][0]+'</a>');
            }
            else $("a",$($("#resources-table .row-"+index+" td")[0])).unbind().click(function(e) {
                showPopup("resources-popup","Ресурсы", "Ресурс","Постов",theme.sources, sources);
                return false;
            });
            $($("#resources-table .row-"+index+" td")[1]).text(sources[index][2]+"%").addClass('txt-algn-right');
            $($("#resources-table .row-"+index+" td")[2]).html(""+sources[index][1]+"").addClass('txt-algn-right');
        });

        /* TODO: uncomment this if error
         var engg=[];
         var engt1=[];
         var engt2=[];
         var engt3=[];
         engt1.nick="Ретвиты";
         engt1.count="83";
         engg[0]=engt1;
         engt2.nick="Лайки";
         engt2.count="23";
         engg[1]=engt2;
         engt3.nick="Комментарии";
         engt3.count="132";
         engg[2]=engt3;*/
        
        $(".renewed span").html(data.cash_update.slice(0, 16)+" ");
        
        //data.engage
        if (data.engage == '0') $("#engage-block").css("cursor","default").fadeTo(0, 0.5);
        else {
            $("#engage-block").css("cursor","pointer").fadeTo(0, 1);
            $("#engage-block").unbind().click(function(e) {
                //showPopup("engage-popup","Вовлеченность", "Ресурс","Вовлеченность",theme.eng_mdin, eng_mdin);
                //alert(eng_mdin);
                $(eng_mdin).each(function (index, value)
                {
                    //alert(value[0]);
                    if (value[0]=="\"Мне нравится\" Вконтакте") eng_mdin[index][0]="vk.com";
                    if (value[0]=="Лайки Facebook") eng_mdin[index][0]="facebook.com";
                    if (value[0]=="Ретвиты Twitter") eng_mdin[index][0]="twitter.com";
                    if (value[0]=="Комментарии Livejournal") eng_mdin[index][0]="livejournal.com";
                });
                showPopup("engage-popup","Вовлеченность", "Ресурс","Значение",theme.eng_mdin, eng_mdin);
                return false;
            });}

        $(".quot").click( function (e) { e.stopPropagation(); } );

        if (data.src == '0') $("#src-block").css("cursor","default").fadeTo(0, 0.5);
        else {
            $("#src-block").css("cursor","pointer").fadeTo(0, 1);
            $("#src-block").unbind().click(function(e) {
                showPopup("resources-popup","Ресурсы", "Ресурс","Постов",theme.sources, sources);
                return false;
            });}

        //alert(data.value);
        if (data.value == '0') $("#value-block").css("cursor","default").fadeTo(0, 0.5);
        else {
            $("#value-block").css("cursor","pointer").fadeTo(0, 1);
            $("#value-block").unbind().click(function(e) {
                showPopup("value-popup","Охват", "Ресурс","Охват",theme.value_mdin, value_mdin);
                return false;
            });}

        if (data.uniq == '0')
            $("#uniq-block").css("cursor","default").fadeTo(0, 0.5);
        else
        {
            $("#uniq-block").css("cursor","pointer").fadeTo(0, 1);
            $("#uniq-block").unbind().click(function(e) {
                showPopup("promoters-popup","Лидеры мнений", "Ник","Охват",theme.promotions, promotions);
                return false;
            });
        }


        if (data.posts == '0')
            $("#post-block").css("cursor","default").fadeTo(0, 0.5);
        else
        {
            $("#post-block").css("cursor","pointer").fadeTo(0, 1);
            $("#post-block").unbind().click(function(e) {
                document.location.href=inernalURL_messages+id;
                return false;
            });}



        //alert(sources);
        /*
         chart = new Highcharts.Chart({
         chart: {
         renderTo: 'resources-diagramm',
         width: 155,
         height: 155
         },
         colors: [
         '#7f7bdb',
         '#c0ac97',
         '#b0c78f',
         '#c09797',
         '#97c0c0',
         '#86c4d1',
         '#92A8CD',
         '#A47D7C',
         '#B5CA92'

         "#7f7bdb","#c0ac97","#b0c78f","#c09797","#97c0c0","#86c4d1"
         ],
         title: {
         text: ''
         },
         tooltip: {
         formatter: function() {
         //return '<p style="font-size: 13px;"><b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %</p>';
         return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
         },
         borderRadius: 3,
         borderWidth: 1,
         style: {
         fontSize: '10px'
         }
         },
         plotOptions: {
         pie: {
         animation: false,
         shadow: false,
         dataLabels: {
         enabled: false,
         }
         }
         },
         series: [{
         type: 'pie',
         data: sources
         }],
         navigation: {
         buttonOptions: {
         enabled: false
         }
         }
         });*/
        $( "#progressbar" ).progressbar( "option", "value", 50 );
        var points = [];
        var cities = [];
        var cities_count = 0;
        $(data.city).each(function(index, element) {



            if (cities.length <= 5)
                cities[cities.length] = [element.name, element.count];
            else {
                cities[5][0] = "Другие";
                cities[5][1] += element.count;
            }
            cities_count += element.count;
        });

        //var ctln = cities.length;
        $("#cities td").text('');
        //$("a",$($("#cities-table td")[0])).text('');

        $(cities).each(function(index, element) {
            //alert(ctln);

            cities[index][2] = Math.round(cities[index][1] / cities_count * 100);
            if (index < 5)
            { //alert(cities[index][0]);
                //$($("#cities-table .row-"+index+" td")[0]).text(cities[index][0]);
                $($("#cities-table .row-"+index+" td")[0]).html('<a href="messages_list.html#'+id+'"  onclick="$.cookie(\''+id+'-cities-msg\',\''+cities[index][0]+'\'); window.location.href = \'messages_list.html#'+id+'\'; return false;">'+cities[index][0]+'</a>');
            }
            /*else $("a",$($("#cities-table .row-"+index+" td")[0])).text('Другие').unbind().click(function(e) {
             showPopup("cities-popup","Города", "Город","Постов",theme.city,'map');
             return false;
             }); */
            //alert(cities.length);
            //if (cities.length <5) { alert("else"); $("#cities-table .row-"+5+" td")[0].html('')};

            $($("#cities-table .row-"+index+" td")[1]).text(cities[index][2]+"%");
            $($("#cities-table .row-"+index+" td")[2]).html("<b>"+cities[index][1]+"</b>");
        });

        //alert($(cities).length);
        if ($(cities).length >5) {
            $($("#cities-table .row-"+5+" td")[0]).html('<a href="#">Другие</a>').unbind().click(function(e) {
                showPopup("cities-popup","Города", "Город","Постов",theme.city,'map');
                return false;
            });}


        var width = 176;
        var height = 97;

        function v2d_length(v)         { return Math.sqrt((v.x*v.x) + (v.y*v.y));}
        function v2d_1d_devide(v,d)    { return {x: v.x/d, y: v.y/d}; }
        function v2d_normalize(v)      { return v2d_1d_devide(v,v2d_length(v));}
        function v2d_1d_multiplex(v,d) { return { x: v.x*d, y: v.y*d};}
        function gradTorad(grad)       { return grad * (Math.PI / 180.0); }
        function v2d_rotate(v,grad)    { return {x: v.x*Math.cos(gradTorad(grad)) - v.y*Math.sin(gradTorad(grad)),
            y: v.x*Math.sin(gradTorad(grad)) + v.y*Math.cos(gradTorad(grad))};
        };
        function v2d_add(v1,v2) {return {x: v1.x+v2.x, y:v1.y+v2.y};}

        function normalizeMouseEvent(e,container) {
            var ePos,
                chartPosLeft,
                chartPosTop,
                chartX,
                chartY;

            // common IE normalizing
            e = e || win.event;
            if (!e.target) {
                e.target = e.srcElement;
            }

            // jQuery only copies over some properties. IE needs e.x and iOS needs touches.
            if (e.originalEvent) {
                e = e.originalEvent;
            }

            // The same for MooTools. It renames e.pageX to e.page.x. #445.
            if (e.event) {
                e = e.event;
            }

            // iOS
            ePos = e.touches ? e.touches.item(0) : e;

            // get mouse position
            chartPosition = $(container).offset();
            chartPosLeft = chartPosition.left;
            chartPosTop  = chartPosition.top;


            var isIE = /msie/i.test(navigator.userAgent) && !win.opera;
            // chartX and chartY
            if (isIE) { // IE including IE9 that has pageX but in a different meaning
                chartX = e.x;
                chartY = e.y;
            } else {
                chartX = ePos.pageX - chartPosLeft;
                chartY = ePos.pageY - chartPosTop;
            }

            return {x: Math.round(chartX), y: Math.round(chartY)};
        }

//$('#map').empty();

        /*$('#map').mousemove(function(mouse) {

         mouse = normalizeMouseEvent(mouse,$('#map'));
         $.each(points,function(i,e) {
         var distance = Math.sqrt((e.x-mouse.x)*(e.x-mouse.x) + (e.y-mouse.y)*(e.y-mouse.y));
         if (distance <= e.r) {
         if ( e.tooltip != null) e.tooltip.hide();
         e.tooltip = createTooltip(renderer,e.data,e.x,e.y);
         //e.tooltip.show();
         } else if ( e.tooltip != null)
         e.tooltip.hide();
         });
         });

         var renderer = new Highcharts.Renderer($('#map')[0], width, height);
         var oX =  {x: (275*width/500),y:  0};



         function createTooltip(renderer, data,x,y) {

         var group = renderer.g("tooltip-"+data.id+"-"+x+"-"+y).add();
         var text = renderer.text(data.name+': '+data.count, x, y)
         .attr({
         zIndex: 105,
         color: "#333333",
         fill: "#333333"
         }).add(group);

         var box = text.getBBox();
         //box.x = x-10; box.y = y-10;

         renderer.rect(box.x - 5, box.y - 5, box.width + 10, box.height + 10, 3)
         .attr({
         fill: 'none',
         stroke: 'black',
         'stroke-width': 5,
         'stroke-opacity': 0.05,
         'fill-opacity': 0.85,
         isShadow: true,
         zIndex: 104
         })
         .add(group);

         renderer.rect(box.x - 5, box.y - 5, box.width + 10, box.height + 10, 3)
         .attr({
         fill: 'none',
         stroke: 'black',
         'stroke-width': 3,
         'stroke-opacity': 0.01,
         'fill-opacity': 0.85,
         isShadow: true,
         zIndex: 104
         })
         .add(group);

         renderer.rect(box.x - 5, box.y - 5, box.width + 10, box.height + 10, 3)
         .attr({
         fill: 'none',
         stroke: 'black',
         'stroke-width': 1,
         'stroke-opacity': 0.15,
         'fill-opacity': 0.85,
         isShadow: true,
         zIndex: 104
         })
         .add(group);

         renderer.rect(box.x - 5, box.y - 5, box.width + 10, box.height + 10, 3)
         .attr({
         fill: '#FFFFFF',
         'stroke-width': 1,
         'stroke-opacity': 0.15,
         stroke: "#c0ac97",
         zIndex: 104
         })
         .add(group);

         //group.hide();
         return group;
         };

         function drawTown(x,y,value,color,data) {
         var v = {x: -1, y: 0},
         grad =  0.9326 * (x-4) ,
         r    =  (4.85 * (103- y))*height/278;
         v    = v2d_add(oX,v2d_1d_multiplex(v2d_rotate(v, -grad),r));
         renderer.circle(v.x, v.y-(10)*height/278, value).attr({
         fill: color,
         opacity: 0.7,
         'stroke-width': 1
         }).add();
         points.push({
         x: v.x,
         y: v.y-(10)*height/278,
         r: value,
         data: data,
         tooltip: null//createTooltip(data,v.x,v.y-(10)*height/278,value)
         });
         };*/



        var cities = data.city;
        var colors = ["#7f7bdb","#c0ac97","#b0c78f","#c09797","#97c0c0","#86c4d1"];
        if (cities[0] != undefined && cities[0] != null && cities[0] != "") //Список городов пуст
        {
            var min = cities[0].count, max=cities[0].count;
            $.each(cities, function(i,e) {
                if (e.count < min) min = e.count;
                if (e.count > max) max = e.count;
            });
            //alert(max + ' ' + min);
            var size;
            for (var i = 0; i <6; i++){
                if (cities[i] == undefined) break; //Городов меньше 6
                size = (cities[i].count - min) / (max - min) * 7 +1;
                if (min==max) size = 1*7+1; // Один город - размер не апроксимируем
                //drawTown(parseFloat(cities[i].x,10),parseFloat(cities[i].y,10),size,colors[i],{id: id,name: cities[i].name, count: cities[i].count});
            };
        }
        /*
         $.each(points[id], function(n,e) {
         e.tooltip = createTooltip(renderer,e.data,e.x,e.y);
         points[id][n] = e;
         });*/



        $(data.speakers).each(function(index, element) {
            if (index < 7) {
                $($("#speakers .row-"+index+" td")[0]).text(data.speakers[index].nick);
                $($("#speakers .row-"+index+" td")[1]).text(data.speakers[index].count);
            }
        });


        $("#speakers .seemore a").unbind().click(function(e) {
            showPopup("speakers-popup","Спикеры", "Ник","Постов",theme.promotions,promotions);
            return false;
        });

        $("#promouters td").text('');
        $("#promouters .seemore a").text('');
        $(data.promotions).each(function(index, element) {
            if (index < 6) {

                /*
                 $.cookie(cookieName,cookieValue);
                 для лидеров: selpromo
                 для слов: selword
                 */
                //$($("#speakers .row-"+index+" td")[0]).text('<a href="">'+data.speakers[index].nick+'</a>');


                $($("#promouters .row-"+index+" td")[0]).html('<a href="messages_list.html#'+id+'"  onclick="$.cookie(\''+id+'-selpromo\',\''+data.promotions[index].id+'\'); window.location.href = \'messages_list.html#'+id+'\'; return false;">'+data.promotions[index].nick+'</a>');
                //$($("#promouters .row-"+index+" td")[0]).text(data.promotions[index].nick);

                $($("#promouters .row-"+index+" td")[1]).text(data.promotions[index].count);

                //if (data[0].count_posts == undefined)

                $($("#promouters .row-"+index+" td")[2]).text(data.promotions[index].count_posts);

            }
            else
            {
                $("#promouters .seemore a").text('Другие').unbind().click(function(e) {
                    showPopup("promoters-popup","Лидеры мнений", "Ник","Охват",theme.promotions, promotions);
                    return false;
                });

            }
        });


        //alert('promo update');



        $("#engage-block").tipTip({content: 'Вовлеченность – оценка участия аудитории в обсуждении темы и ее распространении.<br/> <a href="http://www.wobot.ru/faq#1_13" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
        $("#value-block").tipTip({content: 'Потенциальный охват аудитории.<br/><a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true});
        $("#src-block .h").tipTip({content: 'Количество ресурсов, на которых были найдены упоминания.'});

        $("#resources .h")
            .tipTip({content: 'Распределение упоминаний по площадкам', defaultPosition:"top"})
            .css("cursor","help");

        $("#cities .h")
            .tipTip({content: 'Города – распределение упоминаний по городам, указанным в анкетах пользователей.<br/> <a href="http://www.wobot.ru/faq#1_34" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition:"top"})
            .css("cursor","help");

        $("#promouters .h")
            .tipTip({content: 'Лидеры мнений – пользователи с наибольшим числом аудитории и наиболее часто упоминающие тему мониторинга.<br/> <a href="http://www.wobot.ru/faq#1_21">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition:"top"})
            .css("cursor", "help");

        $("#words .h").tipTip({content: 'Список слов – наиболее часто встречающие слова в одном упоминании по теме мониторинга. <br/> <a href="http://www.wobot.ru/faq#1_32">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition:"top"})
            .css("cursor", "help");
        $("#uniq-block").tipTip({content: 'Количество уникальных авторов'});
        $("#referenceButton").tipTip({content: 'Посмотреть все упоминания по вашей теме'});
        //$(".paperTip").tipTip({content: 'Посмотреть все упоминания по вашей теме'});
        $(".paperTip").tipTip({content: 'Количество упоминаний', defaultPosition:"top"});

        $(".manTip").tipTip({content: 'Охват автора. <br/> <a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition:"top"});

        $(".checkTip").tipTip({content: 'Вес слова. <br/> <a href="http://www.wobot.ru/faq#1_12" target="_blank">Подробное описание (FAQ).</a>', keepAlive: true, defaultPosition:"top"});
        $("#exportmailTip").tipTip({content: 'Отправить экспорт на почту', defaultPosition:"top"});
        $("#exportTip").tipTip({content: 'Скачать экспорт', defaultPosition:"top"});


        var minWordCount = 1000000000;
        var maxWordCount = 0;
        var words = "";
        //data.words.shuffle();

        var wordss = [];
        var wordss_count = 0;
        $(data.words).each(function(index, element) {
            if (wordss.length <= 5)
                wordss[wordss.length] = [element.word, element.count];
            else {
                wordss[5][0] = "Другие";
                wordss[5][1] += element.count;
            }
            wordss_count += element.count;

            if (element.count > maxWordCount) maxWordCount = element.count;
            if (element.count < minWordCount) minWordCount = element.count;

        });

        $(wordss).each(function(index, element) {
            //sources[index][2] = Math.round(sources[index][1] / sources_count * 100);
            if (index < 5)
            //$($("#resources-table .row-"+index+" td")[1]).text(sources[index][0]);
                $($("#words-table .row-"+index+" td")[0])
                    .html('<a href="#" onclick="$.cookie(\''+id+'-selword\',\''+element[0]+'\'); window.location.href = \'messages_list.html#'+id+'\';  return false;">  '+wordss[index][0]+"</a>");
            else $("a",$($("#words-table .row-"+index+" td")[0])).unbind().click(function(e) {
                showPopup("tags-popup","Список слов", "Тег","Вес",theme.words,wordss);
                return false;
            });
            $($("#words-table .row-"+index+" td")[1]).text(wordss[index][1]).addClass('txt-algn-right');
            //$($("#words-table .row-"+index+" td")[2]).html(""+wordss[index][1]+"").addClass('txt-algn-right');
        });

        $( "#progressbar" ).progressbar( "option", "value", 75 );
        //alert(wordss);

        //$(data.words).each(function(index, element) {

        //});


        /*
         var lenChunk = Math.round((maxWordCount - minWordCount) / 6);
         $(data.words).each(function(index, element) {
         var chunk = 0;
         var count = element.count - minWordCount;
         if (count >=0 && count < lenChunk) chunk = 3;
         else if (count >= lenChunk   && count < 2*lenChunk) chunk = 4;
         else if (count >= 2*lenChunk && count < 3*lenChunk) chunk = 4;
         else if (count >= 3*lenChunk && count < 4*lenChunk) chunk = 5;
         else if (count >= 4*lenChunk && count < 5*lenChunk) chunk = 6;
         else chunk = 6;

         words += '<span class="c'+chunk +'" onclick="$.cookie(\'selword\',\''+element.word+'\'); window.location.href = \'messages_list.html#'+id+'\';  return false;">' + element.word + '</span> ';
         });
         $("#cloud").html(words);*/



        /*$("#tags .seemore a").unbind().click(function(e) {
         showPopup("tags-popup","Облако слов", "Тег","Вес",theme.words,wordss);
         return false;
         });*/



        var lines_date = [];
//alert(graphtype);
        //var rangestart=new Date(parseInt($.cookie(id+"-fromDate-theme"),10));
        $.each(data.graph,function(index, element) {
            //lines_date.push([parseInt(index,10)*1000+shift*4*3600*1000, element]);
            //alert(new Date(parseInt(index,10)*1000+shift));
            //lines_date.push([parseInt(index,10)*1000+shift, element]);
            //shift=new Date(parseInt(index,10)*1000);
            shift=parseInt(index,10)*1000;
            return false;
        });
        if (graphtype=='hour')
        {
            //tickInt=1*3600*1000;
            tickInt=1;
            //shift=1;
            //shift=0;
        }
        else if (graphtype=='day')
        {
            //tickInt=86400*1000;
            tickInt=1;
            //shift=1;
            //shift=4*3600000;
            //shift=0;
        }
        else if (graphtype=='week')
        {
            //tickInt=86400*7*1000;
            tickInt=1;
            //shift=7;
            //shift=7+((maxDate-minDate)%(86400000*7)/86400000);
            //shift=((maxDate-minDate)%(86400000*7));
            //shift=-2*86400000;
            //shift=0;
            //shift=-2*86400000;
        }
        else if (graphtype=="month" || graphtype=="quarter" || graphtype=="halfyear")
        {
            //tickInt=86400*30*1000;
            //tickInt=30*86400000;
            tickInt=1;
            //);
            //tickInt=86400*1000;
            //shift=-4*3600*1000;
            //shift=((d2-d1)%(86400000*30));
            //shift=(d2-d1)%(86400000*30);
            //alert((d2-d1)%(86400000*30)/86400000+' '+2*86400000);
            //shift=6*86400000;
            //shift=-parseInt(d1.format("d"),10)*86400000;
            //alert(parseInt(shft.format("d"),10));
            //shift=-(parseInt(shft.format("d"),10)-1)*86400000;
            //shift=0;
        }
        i=1;
        $.each(data.graph,function(index, element) {
            //lines_date.push([parseInt(index,10)*1000+shift*4*3600*1000, element]);
            //alert(new Date(parseInt(index,10)*1000+shift));
            lines_date.push([i, element]);
            i++;
        });
        //max=i-1;
        /*		var rangestart=new Date(minDate);

         if (graphtype=='hour')
         {
         $.each(data.graph,function(index, element) {
         var dte = new Date(parseInt(index,10)*1000);
         lines_date.push([dte.format("hh:mm:ss"), element]);
         });
         }
         else if (graphtype=='day')
         {
         $.each(data.graph,function(index, element) {
         lines_date.push([parseInt(index,10)*1000, element]);
         });
         }
         else if (graphtype=='week')
         {
         $.each(data.graph,function(index, element) {
         var rngstart=new Date(parseInt(index,10)*1000-86400*6*1000);
         if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date(parseInt(index,10)*1000)).format("dd.mm.yy");
         else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date(parseInt(index,10)*1000)).format("dd.mm.yy");
         lines_date.push([tttext, element]);
         });
         }
         else if (graphtype=='month')
         {
         $.each(data.graph,function(index, element) {
         var rngstart=new Date(parseInt(index,10)-86400*29);
         if (rngstart>rangestart) tttext=rngstart.format("dd.mm.yy")+' - '+(new Date(parseInt(index,10))).format("dd.mm.yy");
         else tttext=rangestart.format("dd.mm.yy")+' - '+(new Date(parseInt(index,10))).format("dd.mm.yy");
         lines_date.push([tttext, element]);
         });
         }
         */

        var series = [];

        series.push(
            { name: data.order_name,
                data: lines_date,
                showInLegend: false ,
                marker: {
                    symbol: "circle"
                }
            });


        $('#large').empty();
        // make the container smaller and add a second container for the master chart
        var $container = $('#lines-diagramm').css('position', 'relative');

        var $detailContainer = $('<div id="detail-container">').appendTo($container);

        var $masterContainer = $('<div id="master-container">')
            .css({ position: 'absolute', top: 218, height: 60, width: '100%' })
            .appendTo($container);


        createMaster(series);
        /*
         chart2 = new Highcharts.Chart({
         chart: {
         renderTo: 'lines-diagramm',
         zoomType: 'x'
         },

         legend: {
         enable:false
         },

         xAxis: {
         type: 'datetime',
         minRange: 864000000
         },

         yAxis: {
         title: {
         text:""
         },
         allowDecimals: false
         },
         title: {
         text: ""
         },

         plotOptions: {
         series: {
         animation: false,
         shadow: false,
         lineWidth: 1,

         marker: {
         fillColor: '#FFFFFF',
         lineWidth: 1,
         lineColor: null, // inherit from series,
         radius: 4
         }

         }
         },
         tooltip: {
         enabled: true,
         formatter: function() {
         return 'Постов '+ this.y + '<br/>Дата: '+ (new Date(this.x)).format("dd.mm.yyyy") +'';
         }
         },
         series: [{
         data:   lines_date,
         color: '#7A9483' ,
         showInLegend: false
         }],
         navigation: {
         buttonOptions: {
         enabled: false
         }
         },
         exporting: {
         enabled: true
         }

         });
         */
        $("tspan").each(function(index, element) {
            if ($(element).text() == "Highcharts.com")$(element).text("");
        });

        $( "#progressbar" ).progressbar( "option", "value", 100 );
        $( ".progress" ).fadeOut(1000);
    });

}


$(document).ready(function () {
    createDropDown("export-as","34px");

    /*var settingsName = $("#dialog-Settings-name"),
     settingsCompany = $("#dialog-Settings-company"),
     settingsPassnew = $("#dialog-Settings-passnew"),
     settingsPasscheck = $("#dialog-Settings-passcheck"),
     settingsEmail = $("#dialog-Settings-email"),
     settingsPhone = $("#dialog-Settings-phone");

     //settings
     var settingsTips = $( [] ).add($("#dialog-Settings-name-tip"))
     .add($("#dialog-Settings-company-tip"))
     .add($("#dialog-Settings-passnew-tip"))
     .add($("#dialog-Settings-passcheck-tip"))
     .add($("#dialog-Settings-email-tip"));

     var allSettings = $( [] ).add(settingsName)
     .add(settingsCompany)
     .add(settingsPassnew)
     .add(settingsPasscheck)
     .add(settingsEmail)
     .add(settingsPhone);*/

    //$('#scrollbar1').tinyscrollbar();


    //settings
    //$('#scrollbar2').tinyscrollbar();
    //$('#robokassaSubmit').button();
    //getSettings();




    var loc = location.href.split('#');
    id = loc[loc.length - 1];

	if ((id=='')|(id==null))
	{
		window.location.replace("/");
	}

    $("#date #datepicker").click(function(e) {
        $("#date .dp").toggle();
        $("#datepicker").blur();
        return false;
    });

    $("#date .ddp").click(function(e) {
        $("#date .dp").toggle();
        return false;
    });

    $("#home-url").attr("href",inernalURL_themesList);
    //$("#logo-href").attr("href",inernalURL_themesList);
    //$("#exit").attr("href",inernalURL_logout);


	//alert($.cookie("user_id"));

    $("#home").attr("href", inernalURL_themesList);

    $("#user_email").text($.cookie("user_email"));

    //$("#user_email").unbind("click").click(function(e) {/* заглушка для popup-а */ return false;});
    exp=$.cookie("user_exp");
    if (exp==null) exp=0;
    //if (exp==null||exp<4) $("#user_exp").addClass("warn");
    if (exp==null||exp<4||exp=="0"||exp=="Аккаунт заблокирован") $("#user_exp").addClass("warn");
    $("#user_exp").text(exp);

    $("#exit").attr("href",inernalURL_logout);
    $("#access").attr("href", inernalURL_accessSetup);

    $("#user_tariff").text($.cookie("user_tariff"));
    $("#user_tariff").attr("href",inernalURL_tariff+$.cookie("tarif_id"));
    $("#user_tariff").unbind("click").click(function(e) { loadmodal(inernalURL_tariff+$.cookie("tarif_id"),300,400,"iframe"); return false;});

    $("#user_money").html($.cookie("user_money")+"&nbsp;<span class=\"rur\">p<span></span></span>");

    $("#billing").attr("href",inernalURL_billing+"?user_id="+$.cookie("user_id"));
    $("#billing").unbind("click").click(function(e) { loadmodal(inernalURL_billing+"?user_id="+$.cookie("user_id"),"50","100%","iframe"); return false;});

    $("#user_consultant").text($.cookie("user_consultant"));
    /* НЕ ОПРЕДЕЛЕНО ПО ТЗ */
    $("#user_consultant").attr("href","FOO");
    $("#user_consultant").unbind("click").click(function(e) { loadmodal("FOO","75%","75%","iframe"); return false;});
    /*:~ */

    $("#faq").attr("href",inernalURL_faq);
    //$("#faq").unbind("click").click(function(e) { loadmodal(inernalURL_faq,"75%","75%","iframe"); return false;});



    $("#export-btn").css("cursor","pointer").click(function(e){
        $("#export-form").attr("action",postURL_Export);
        var form = $("#export-form");
        $('[name="order_id"]',form).val(id);
        $('[name="start"]'   ,form).val($("#dp-begin").datepicker("getDate").format("dd.mm.yyyy"));
        $('[name="end"]'     ,form).val($("#dp-end"  ).datepicker("getDate").format("dd.mm.yyyy"));
        $('[name="format"]'  ,form).val($("#tdd-export-as").attr("value"));
        form.submit();
    });
    $("#mail-btn").css("cursor","pointer").click(function(e){
        $("#export-form").attr("action",postURL_Email);
        var form = $("#export-form");
        $('[name="order_id"]',form).val(id);
        $('[name="start"]'   ,form).val($("#dp-begin").datepicker("getDate").format("dd.mm.yyyy"));
        $('[name="end"]'     ,form).val($("#dp-end"  ).datepicker("getDate").format("dd.mm.yyyy"));
        $('[name="format"]'  ,form).val($("#tdd-export-as").attr("value"));
        form.submit();
    });



    $(document).bind('click', function(e) {
        var $clicked = $(e.target);
        if (! $clicked.parents().hasClass("dd") && ! $clicked.parents().hasClass("ui-datepicker-calendar") && !$clicked.parents().hasClass("ui-datepicker-prev") && !$clicked.parents().hasClass("ui-datepicker-next"))  {
            $("#date .dp").hide();
            if (wasChanged) {
                wasChanged = false;

                loadContent(id,$("#dp-begin").datepicker("getDate").format("dd.mm.yyyy"),$("#dp-end").datepicker("getDate").format("dd.mm.yyyy"));
                $.cookie(id+"-fromDate-theme",$("#dp-begin").datepicker("getDate").getTime());
                $.cookie(id+"-toDate-theme",$("#dp-end").datepicker("getDate").getTime());
                $.cookie(id + "-md5", '');
                $.cookie(id + "-page-msg", 0);
            }
        }
    });

    $("#export .btn a").click(function() {
        $(".dropdown dd ul").toggle();
        return false;
    });
    
    //alert($.cookie(id+"-fromDate-theme"));
    var dstart = formatDate($.cookie(id+"-fromDate-theme"));
    var dend = formatDate($.cookie(id+"-toDate-theme"));

    //alert(dstart);
    //alert(dend);

    loadContent(id,dstart,dend);

    //change
    var notices = new Array('themeNotice');
    showNotices(notices);

    //themeNotice();


    //	$(".fill").css("height", $($("#L4 .item.nosub").get(0)).css("height"));

//prepareToolTips();

});


function themeNotice()
{
    if ($.cookie("themeNoticeNot")!=1)
    {
        var mainNoticeId = $.gritter.add({
            title: 'Вы находитесь на странице аналитики по выбранной теме.',
            text: 'На этой странице представлена обзорная аналитика по выбранной теме в удобной графической ' +
                'форме. Вы можете просмотреть динамику упоминаний на графике, распределение упоминаний' +
                'по ресурсам и городам, увидеть наиболее активных авторов упоминаний. ' +
                '<br>Вы можете оперативно загрузить эту аналитику в удобном для вас формате или распечатать ее.<br> \
                Чтобы изменить интервал отображения данных, выберите нужные вам даты в поле период или \
                выделите его на графике под динамикой упоминаний. \
			<a href="#" id="themeNoticeNot" class="a-dotted close-info-popup">Больше не показывать</a>',
            sticky: true,
            time: '',
            class_name: 'my-sticky-class',
            after_close: function(e){
                //newresNotice();
            },
        });
        $('#themeNoticeNot').click(function(){
            $.cookie("themeNoticeNot",1);
            hideNotice("themeNotice", 1);
            $.gritter.remove(mainNoticeId);
            return false;
        });
    }
}

