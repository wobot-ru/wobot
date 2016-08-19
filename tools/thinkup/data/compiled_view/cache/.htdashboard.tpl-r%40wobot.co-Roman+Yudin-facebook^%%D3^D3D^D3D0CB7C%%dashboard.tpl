235
a:4:{s:8:"template";a:5:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1340569822;s:7:"expires";i:1340570422;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>Roman Yudin's Dashboard | ThinkUp</title>
  <link rel="shortcut icon" type="image/x-icon" href="/tools/thinkup/assets/img/favicon.png">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/base.css">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/positioning.css">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/style.css">
    <!-- jquery -->
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/jquery-ui-1.8.13.css">
  <link type="text/css" rel="stylesheet" href="/tools/thinkup/assets/css/jquery-ui-1.7.1.custom.css">
  <script type="text/javascript" src="/tools/thinkup/assets/js/jquery.min-1.4.js"></script>
  <script type="text/javascript" src="/tools/thinkup/assets/js/jquery-ui.min-1.8.js"></script>

  <!-- google chart tools -->
  <!--Load the AJAX API-->
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>

  <script type="text/javascript" src="/tools/thinkup/plugins/twitter/assets/js/widgets.js"></script>
  <script type="text/javascript">var site_root_path = '/tools/thinkup/';</script>
    

  <!-- custom css -->
  
  <style>
  .line { background:url('/tools/thinkup/assets/img/border-line-470.gif') no-repeat center bottom;
  margin: 8px auto;
  height: 1px;
  }

  </style>
  
  

  <script type="text/javascript">
  $(document).ready(function() {
      $(".post").hover(
        function() { $(this).children(".small").children(".metaroll").show(); },
        function() { $(this).children(".small").children(".metaroll").hide(); }
      );
      $(".metaroll").hide();
    });
  </script>


</head>
<body>
  <script type="text/javascript">
    $(document).ready(function() {
      function changeMe() {
        var _mu = $("select#instance-select").val();
        if (_mu != "null") {
          document.location.href = _mu;
        }
      }
    });
  </script>


<div id="status-bar" class="clearfix"> 

  <div class="status-bar-left">
          <!-- the user has selected a particular one of their instances -->
      
        <script type="text/javascript">
          function changeMe() {
            var _mu = $("select#instance-select").val();
            if (_mu != "null") {
              document.location.href = _mu;
            }
          }
        </script>
      
      
            <span id="instance-selector">
        <select id="instance-select" onchange="changeMe();">
          <option value="">-- Switch service user --</option>
                                    <option value="/tools/thinkup/?u=Roman+Yudin&n=google%2B">Roman Yudin - Google+</option>
                                                <option value="/tools/thinkup/?u=Wobot&n=facebook+page">Wobot - Facebook Page</option>
                                                                      <option value="/tools/thinkup/?u=_rcp&n=twitter">_rcp - Twitter</option>
                              </select>
      </span>
                <a href="/tools/thinkup/crawler/updatenow.php?log=full" class="linkbutton">Update now</a>  </div> <!-- .status-bar-left -->
  
  <div class="status-bar-right text-right">
    <ul> 
              <li>Logged in as admin: r@wobot.co <script src="/tools/thinkup/install/checkversion.php"></script><a href="/tools/thinkup/account/?m=manage" class="linkbutton">Settings</a> <a href="/tools/thinkup/session/logout.php" class="linkbutton">Log Out</a></li>
          </ul>
  </div> <!-- .status-bar-right -->

  
</div> <!-- #status-bar -->

<div id="page-bkgd">

<div class="container clearfix">
  
  <div id="app-title"><a href="/tools/thinkup/?u=_rcp&n=twitter">
    <h1><span id="headerthink">Think</span><span id="headerup">Up</span></h1>
  </a></div> <!-- end #app-title -->
  
</div> <!-- end .container -->
<div class="container_24">
  <div class="clearfix">

    <!-- begin left nav -->
    <div class="grid_4 alpha omega">
              <div id="nav">
        <ul id="top-level-sidenav">
                              <li class="selected">
                <a href="/tools/thinkup/?u=Roman+Yudin&n=facebook">Dashboard</a>
              </li>
                                                    <li>
                                                <a href="/tools/thinkup/?v=posts&u=Roman+Yudin&n=facebook">Posts</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=friends&u=Roman+Yudin&n=facebook">Friends</a></li>
                                                                                                                                       
                        </ul>
      </div>
            </div>

    <div class="thinkup-canvas round-all grid_20 alpha omega prepend_20 append_20" style="min-height:340px">
      <div class="prefix_1 suffix_1">

                    
                  <!--begin public user dashboard-->
                      <div class="grid_18 alpha omega">
              <div class="clearfix alert stats round-all" id="">
                <div class="grid_2 alpha">
                  <div class="avatar-container">
                    <img src="https://graph.facebook.com/1548406154/picture" class="avatar2"/>
                    <img src="/tools/thinkup/plugins/facebook/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet">Roman Yudin <span style="color:#ccc">Facebook</span></span><br />
                  <div class="small">
                    Updated 5 mins  ago                  </div>
                </div>
              </div>
            </div>
          
           <!-- else if no $data_template -->

                            <div class="section">
                        <h2>Response Rates</h2>
                        <div class="clearfix article">
                            <div id="hot_posts"></div>
                        </div>
                </div>
            
            
            
            
            
                          
                <div class="section" style="float : left; clear : none; width : 345px;">
                  <h2>
                    Friends By Day
                                            (<span style="color:green">+7</span>/day)
                                      </h2>
                                        <div class="article">
                        <div id="follower_count_history_by_day"></div>
                    </div>
                    <div class="view-all">
                    <a href="/tools/thinkup/?v=friends&u=Roman+Yudin&n=facebook">More...</a>
                  </div>
                    
                                  </div>
                <div class="section" style="float : left; clear : none;margin-left : 16px; width : 345px;">
                  <h2>
                    Friends  By Week
                                      </h2>
                                      <div class="article">
                        <div id="follower_count_history_by_week"></div>
                    </div>
                                      <div class="view-all">
                    <a href="/tools/thinkup/?v=friends&u=Roman+Yudin&n=facebook">More...</a>
                  </div>
                                  </div>

            
                                    <script type="text/javascript">
                // Load the Visualization API and the standard charts
                google.load('visualization', '1');
                // Set a callback to run when the Google Visualization API is loaded.
                google.setOnLoadCallback(drawCharts);

                
                function drawCharts() {
                

                  var follower_count_history_by_day_data = new google.visualization.DataTable(
                  {"rows":[{"c":[{"v":new Date(2012,2,28),"f":"03\/28\/2012"},{"v":496}]},{"c":[{"v":new Date(2012,2,29),"f":"03\/29\/2012"},{"v":496}]},{"c":[{"v":new Date(2012,2,30),"f":"03\/30\/2012"},{"v":499}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21\/2012"},{"v":525}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30\/2012"},{"v":533}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Friends"}]});
                  var follower_count_history_by_week_data = new google.visualization.DataTable(
                  {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27"},{"v":496}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21"},{"v":525}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30"},{"v":533}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Friends"}]});

                  var hot_posts_data = new google.visualization.DataTable({"rows":[{"c":[{"v":"\u0420\u0430\u0441\u043f\u0438\u043b \u043f\u043e-\u0431\u0430\u0443\u043c\u0430\u043d\u0441\u043a\u0438http:\/\/anna-makhortova.livejournal.com\/4451.html?thread=6243..."},{"v":1},{"v":0},{"v":0}]},{"c":[{"v":"\u041d\u043e\u0432\u044b\u0439 \u0434\u0438\u0437\u0430\u0439\u043d \u044f\u043d\u0434\u0435\u043a\u0441\u0430:\nhttp:\/\/new.yandex.ru..."},{"v":0},{"v":0},{"v":1}]},{"c":[{"v":"\u041c\u0438\u0445\u0430\u0438\u043b, \u0442\u0430\u043a \u0434\u0435\u0440\u0436\u0430\u0442\u044c!..."},{"v":0},{"v":0},{"v":1}]},{"c":[{"v":"\u0432 \u0432\u043e\u0435\u043d\u043a\u043e\u043c\u0430\u0442\u0435 \u043d\u0435 \u0437\u043d\u0430\u043b\u0438......"},{"v":1},{"v":0},{"v":1}]},{"c":[{"v":"Apr 3..."},{"v":0},{"v":0},{"v":2}]},{"c":[{"v":"Honeypot \u0432 social media \u0438\u043b\u0438 \u043a\u0430\u043a \u0437\u0430\u0449\u0438\u0442\u0438\u0442\u044c \u0433\u0440\u0443\u043f\u043f\u0443 \u043e\u0442 \u0431\u043e\u0442\u043e\u0432 =)..."},{"v":0},{"v":0},{"v":1}]},{"c":[{"v":"\u041c\u043d\u0438\u0448\u044c, \u0447\u0442\u043e \u0442\u044b \u043a\u0440\u0443\u0442\u043e\u0439 \u043c\u0430\u0440\u043a\u0435\u0442\u043e\u043b\u043e\u0433? \u0417\u043d\u0430\u0435\u0448\u044c \u043d\u0435 \u043f\u043e \u043d\u0430\u0441\u043b\u044b\u0448\u043a\u0435, ..."},{"v":0},{"v":0},{"v":3}]},{"c":[{"v":"\u0410\u0439\u0444\u043e\u043d\u044b \u043e\u0431\u0449\u0430\u044e\u0442\u0441\u044f =)..."},{"v":0},{"v":0},{"v":2}]},{"c":[{"v":"\u0420\u043e\u0441\u0441\u0438\u0439\u0441\u043a\u0438\u0439 \u043f\u0440\u043e\u0433\u0440\u0430\u043c\u043c\u0438\u0441\u0442 \u0437\u0430 \u043d\u0435\u0441\u043a\u043e\u043b\u044c\u043a\u043e \u043c\u0438\u043d\u0443\u0442 \u0432\u0437\u043b\u043e\u043c\u0430\u043b \u0431\u0440\u0430..."},{"v":0},{"v":0},{"v":2}]},{"c":[{"v":null},{"v":0},{"v":0},{"v":1}]}],"cols":[{"type":"string","label":"Post"},{"type":"number","label":"Comments"},{"type":"number","label":"Shares"},{"type":"number","label":"Likes"}]});
                  var client_usage_data = new google.visualization.DataTable({"rows":[{"c":[{"v":"","f":""},{"v":335}]}],"cols":[{"type":"string","label":"Client"},{"type":"number","label":"Posts"}]});
                  var click_stats_data = new google.visualization.DataTable();

                  

                  var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
                  var formatter_date = new google.visualization.DateFormat({formatType: 'medium'});

                  var hot_posts_chart = new google.visualization.ChartWrapper({
                      containerId: 'hot_posts',
                      chartType: 'BarChart',
                      dataTable: hot_posts_data,
                      options: {
                          colors: ['#3e5d9a', '#3c8ecc', '#BBCCDD'],
                          isStacked: true,
                          width: 650,
                          height: 250,
                          chartArea:{left:300,height:"80%"},
                          legend: 'bottom',
                          hAxis: {
                            textStyle: { color: '#fff', fontSize: 1 }
                          },
                          vAxis: {
                            minValue: 0,
                            baselineColor: '#ccc',
                            textStyle: { color: '#999' },
                            gridlines: { color: '#eee' }
                          },
                      }
                  });
                  hot_posts_chart.draw();

                  formatter.format(click_stats_data, 1);
                  var click_stats_chart = new google.visualization.ChartWrapper({
                      containerId: 'click_stats',
                      chartType: 'BarChart',
                      dataTable: click_stats_data,
                      options: {
                          colors: ['#3c8ecc'],
                          isStacked: true,
                          width: 650,
                          height: 250,
                          chartArea:{left:300,height:"80%"},
                          legend: 'none',
                          hAxis: {
                            textStyle: { color: '#fff', fontSize: 1 }
                          },
                          vAxis: {
                            minValue: 0,
                            baselineColor: '#ccc',
                            textStyle: { color: '#999' },
                            gridlines: { color: '#eee' }
                          },
                      }
                  });
                  click_stats_chart.draw();

                  formatter.format(follower_count_history_by_day_data, 1);
                  formatter_date.format(follower_count_history_by_day_data, 0);

                  var follower_count_history_by_day_chart = new google.visualization.ChartWrapper({
                      containerId: 'follower_count_history_by_day',
                      chartType: 'LineChart',
                      dataTable: follower_count_history_by_day_data,
                      options: {
                          width: 325,
                          height: 250,
                          legend: "none",
                          interpolateNulls: true,
                          pointSize: 2,
                          hAxis: {
                              baselineColor: '#eee',
                              format: 'MMM d',
                              textStyle: { color: '#999' },
                              gridlines: { color: '#eee' }
                          },
                          vAxis: {
                              baselineColor: '#eee',
                              textStyle: { color: '#999' },
                              gridlines: { color: '#eee' }
                          },
                      },
                  });
                  follower_count_history_by_day_chart.draw();

                  formatter.format(follower_count_history_by_week_data, 1);
                  formatter_date.format(follower_count_history_by_week_data, 0);

                  var follower_count_history_by_week_chart = new google.visualization.ChartWrapper({
                      containerId: 'follower_count_history_by_week',
                      chartType: 'LineChart',
                      dataTable: follower_count_history_by_week_data,
                      options: {
                          width: 325,
                          height: 250,
                          legend: "none",
                          interpolateNulls: true,
                          pointSize: 2,
                          hAxis: {
                              baselineColor: '#eee',
                              format: 'MMM d',
                              textStyle: { color: '#999' },
                              gridlines: { color: '#eee' }
                          },
                          vAxis: {
                              baselineColor: '#eee',
                              textStyle: { color: '#999' },
                              gridlines: { color: '#eee' }
                          },
                      },
                  });
                  follower_count_history_by_week_chart.draw();

                  if (typeof(replies) != 'undefined') {
                    var post_types = new google.visualization.DataTable();
                    post_types.addColumn('string', 'Type');
                    post_types.addColumn('number', 'Percentage');
                    post_types.addRows([
                        ['Conversationalist', {v: replies/100, f: replies + '%'}], 
                        ['Broadcaster', {v: links/100, f: links + '%'}]
                    ]);

                    var post_type_chart = new google.visualization.ChartWrapper({
                        containerId: 'post_types',
                        chartType: 'ColumnChart',
                        dataTable: post_types,
                        options: {
                            colors: ['#3c8ecc'],
                            width: 300,
                            height: 200,
                            legend: 'none',
                            hAxis: {
                                minValue: 0,
                                maxValue: 1,
                                textStyle: { color: '#000' },
                            },
                            vAxis: {
                                textStyle: { color: '#666' },
                                gridlines: { color: '#ccc' },
                                format:'#,###%',
                                baselineColor: '#ccc',
                            },
                        }
                    });
                    post_type_chart.draw();
                  }

                  formatter.format(client_usage_data, 1);
                  var client_usage_chart = new google.visualization.ChartWrapper({
                      containerId: 'client_usage',
                      // chartType: 'ColumnChart',
                      chartType: 'PieChart',
                      dataTable: client_usage_data,
                      options: {
                          titleTextStyle: {color: '#848884', fontSize: 19},
                          width: 300,
                          height: 300,
                          sliceVisibilityThreshold: 1/100,
                          chartArea: { width: '100%' },
                          pieSliceText: 'label',
                      }
                  });
                  client_usage_chart.draw();
                }
            
                  
            </script>

           <!-- end if $data_template -->
        
        
      </div> <!-- /.prefix_1 -->
    </div> <!-- /.thinkup-canvas -->

  </div> <!-- /.clearfix -->
</div> <!-- /.container_24 -->

<script type="text/javascript" src="/tools/thinkup/assets/js/linkify.js"></script>

  <div class="small center" id="footer">
    <script type="text/javascript" src="/tools/thinkup/assets/js/linkify.js"></script>
  
    <div id="ft" role="contentinfo">
    <div id="">
      <p>
       <a href="http://thinkupapp.com">ThinkUp 1.0.6</a> &#8226; 
       <a href="http://thinkupapp.com/docs/">Documentation</a> 
       &#8226; <a href="http://groups.google.com/group/thinkupapp">Mailing List</a> 
       &#8226; <a href="http://webchat.freenode.net/?channels=thinkup">IRC Channel</a><br>
        It is nice to be nice.
        <br /> <br /><a href="http://twitter.com/thinkupapp"><img src="/tools/thinkup/assets/img/favicon_twitter.png"></a>
        <a href="http://facebook.com/thinkupapp"><img src="/tools/thinkup/assets/img/favicon_facebook.png"></a>
        <a href="http://gplus.to/thinkup"><img src="/tools/thinkup/assets/img/favicon_googleplus.png"></a>
      </p>
    </div>
    </div> <!-- #ft -->

  </div> <!-- .content -->

<div id="screen"></div>
</body>

</html>