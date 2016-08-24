var series = [ { "name": "Заглушка 1", "data": [ [ 1343678400000, 38.80417176667018 ], [ 1344542400000, 37.251765546178376 ], [ 1344628800000, 25.080814783469886 ], [ 1344715200000, 40.592613157005154 ], [ 1344801600000, 61.503898951367056 ], [ 1344888000000, 38.46809895758675 ], [ 1344974400000, 25.629799857264597 ], [ 1345060800000, 92.57404901862664 ], [ 1345147200000, 83.71265997318433 ], [ 1345233600000, 38.22389858458331 ], [ 1345320000000, 63.809727709251135 ], [ 1345406400000, 39.78415660188891 ], [ 1345492800000, 138.94635188423152 ], [ 1345579200000, 40.477548855943894 ], [ 1345665600000, 88.51209693284062 ], [ 1345752000000, 81.48497268393876 ], [ 1345838400000, 67.37084379509866 ], [ 1345924800000, 58.20271234091249 ], [ 1346011200000, 42.11418009939693 ], [ 1346097600000, 44.73769539292775 ] ], "showInLegend": false, "marker": { "symbol": "circle" } }, { "name": "Заглушка 3", "data": [ [ 1343678400000, 82.1994098298909 ], [ 1344542400000, 52.0145021493636 ], [ 1344628800000, 51.66572933672852 ], [ 1344715200000, 54.82929585088182 ], [ 1344801600000, 110.94381711792451 ], [ 1344888000000, 75.80946499473183 ], [ 1344974400000, 16.932183118938433 ], [ 1345060800000, 109.36166969484408 ], [ 1345147200000, 93.84437771393627 ], [ 1345233600000, 69.63562247626689 ], [ 1345320000000, 83.83066189895388 ], [ 1345406400000, 65.64947427356408 ], [ 1345492800000, 144.34053952090852 ], [ 1345579200000, 64.94906759446738 ], [ 1345665600000, 125.34059010193215 ], [ 1345752000000, 73.77338098969241 ], [ 1345838400000, 70.6701836320875 ], [ 1345924800000, 42.78253616595623 ], [ 1346011200000, 80.75799261390998 ], [ 1346097600000, 69.44653691648969 ] ], "showInLegend": false, "marker": { "symbol": "circle" } }, { "name": "Заглушка 2", "data": [ [ 1343678400000, 28.868511933412783 ], [ 1344542400000, 30.21944990310201 ], [ 1344628800000, 20.132032211561306 ], [ 1344715200000, 67.03767443308745 ], [ 1344801600000, 63.3301716141795 ], [ 1344888000000, 57.82984600205505 ], [ 1344974400000, 42.18906190656499 ], [ 1345060800000, 93.51830314815264 ], [ 1345147200000, 102.95736400915695 ], [ 1345233600000, 59.63532007978527 ], [ 1345320000000, 62.556264816531296 ], [ 1345406400000, 44.497322600271566 ], [ 1345492800000, 145.15670084725352 ], [ 1345579200000, 60.6665552013645 ], [ 1345665600000, 102.56833491848285 ], [ 1345752000000, 87.49570256143615 ], [ 1345838400000, 81.45742342383926 ], [ 1345924800000, 41.42698075283918 ], [ 1346011200000, 48.96959201918757 ], [ 1346097600000, 66.10151388765951 ] ], "showInLegend": false, "marker": { "symbol": "circle" } } ];

var masterChart,
    detailChart;

$(document).ready(function() {


    // create the master chart
    function createMaster() {
       masterChart = new Highcharts.Chart({
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
                marginLeft: 50,
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
                        if ((max - min) < 864000000) {
                            min = Math.floor((min+max)/2) - 432000000;
                            max = Math.ceil((min+max)/2) + 432000000;
                    }

                     detailChart.xAxis[0].setExtremes(min,max);
                            
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
                showLastTickLabel: true,
                maxZoom: 14 * 24 * 3600000, // fourteen days
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

        }, function(masterChart) {
            createDetail(masterChart)
        });
    };

    // create the detail chart
    function createDetail(masterChart) {

        // prepare the detail chart
        var detailData = series;
            /*detailStart = Date.UTC(2012, 7, 10);
 
        jQuery.each(series, function(i,line) {                                                                line.data = jQuery.grep(line.data, function(i,point) {
                                return i[0] >=detailStart;
                            });
                                                                                                               detailData.push(line);
                        });                    
*/
        // create a detail chart referenced by a global variable
        detailChart = new Highcharts.Chart({
                chart: {
                marginBottom: 120,
                renderTo: 'detail-container',
                reflow: false,
                marginLeft: 50,
                marginRight: 20,
                style: {
                    position: 'absolute'
                }
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
                  minRange: 864000000   
                },    
                yAxis: {title: {text:""}},
                title: { text: ""},        
                plotOptions: {
                    series: {
                        animation: false,
                        shadow: false,
                        lineWidth: 1,
            
                        marker: {
                            fillColor: '#FFFFFF',
                            lineWidth: 1,
                            lineColor: null, // inherit from series,
                            radius: 3,
                            symbol: "circle"
                        }            
                        
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>Постов '+ this.y + '<br/>Дата: '+ (new Date(this.x)).format("dd.mm.yyyy") +'';
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

    // make the container smaller and add a second container for the master chart
    var $container = $('#container')
        .css('position', 'relative');

    var $detailContainer = $('<div id="detail-container">')
        .appendTo($container);

    var $masterContainer = $('<div id="master-container">')
        .css({ position: 'absolute', top: 300, height: 80, width: '100%' })
        .appendTo($container);

    // create master and in its callback, create the detail chart
    createMaster();
});