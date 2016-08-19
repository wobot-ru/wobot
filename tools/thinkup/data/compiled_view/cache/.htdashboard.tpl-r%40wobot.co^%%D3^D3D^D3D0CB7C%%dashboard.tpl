235
a:4:{s:8:"template";a:5:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1340569093;s:7:"expires";i:1340569693;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
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
                                                          <option value="/tools/thinkup/?u=Wobot&n=facebook+page">Wobot - Facebook Page</option>
                                                <option value="/tools/thinkup/?u=Roman+Yudin&n=facebook">Roman Yudin - Facebook</option>
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
  
  <div id="app-title"><a href="/tools/thinkup/">
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
                <a href="/tools/thinkup/?u=Roman+Yudin&n=google%2B">Dashboard</a>
              </li>
                                                    <li>
                                                <a href="/tools/thinkup/?v=posts&u=Roman+Yudin&n=google%2B">Posts</a></li>
                                                                                                                 
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
                    <img src="https://lh6.googleusercontent.com/-_iySUGMksE8/AAAAAAAAAAI/AAAAAAAAAAA/iHGlwPiQKYY/photo.jpg?sz=50" class="avatar2"/>
                    <img src="/tools/thinkup/plugins/googleplus/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet">Roman Yudin <span style="color:#ccc">Google+</span></span><br />
                  <div class="small">
                    Updated 1 month  ago                  </div>
                </div>
              </div>
            </div>
          
           <!-- else if no $data_template -->

            
            
            
            
            
            
                                    <script type="text/javascript">
                // Load the Visualization API and the standard charts
                google.load('visualization', '1');
                // Set a callback to run when the Google Visualization API is loaded.
                google.setOnLoadCallback(drawCharts);

                
                function drawCharts() {
                

                  var follower_count_history_by_day_data = new google.visualization.DataTable(
                  {"rows":[],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Followers"}]});
                  var follower_count_history_by_week_data = new google.visualization.DataTable(
                  {"rows":[],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Followers"}]});

                  var hot_posts_data = new google.visualization.DataTable();
                  var client_usage_data = new google.visualization.DataTable({"rows":[],"cols":[{"type":"string","label":"Client"},{"type":"number","label":"Posts"}]});
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