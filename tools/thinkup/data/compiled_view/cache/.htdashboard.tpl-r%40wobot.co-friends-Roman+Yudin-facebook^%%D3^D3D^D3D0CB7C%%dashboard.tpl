303
a:4:{s:8:"template";a:6:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:56:"/var/www/tools/thinkup/plugins/facebook/view/friends.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1340569831;s:7:"expires";i:1340570431;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>Roman Yudin on Facebook | ThinkUp</title>
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
  
  <div id="app-title"><a href="/tools/thinkup/?u=Roman+Yudin&n=facebook">
    <h1><span id="headerthink">Think</span><span id="headerup">Up</span></h1>
  </a></div> <!-- end #app-title -->
  
</div> <!-- end .container -->
<div class="container_24">
  <div class="clearfix">

    <!-- begin left nav -->
    <div class="grid_4 alpha omega">
              <div id="nav">
        <ul id="top-level-sidenav">
                              <li>
                <a href="/tools/thinkup/?u=Roman+Yudin&n=facebook">Dashboard</a>
              </li>
                                                    <li>
                                                <a href="/tools/thinkup/?v=posts&u=Roman+Yudin&n=facebook">Posts</a></li>
                                                   <li class="selected">
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
          
                      <div class="section">
    <h2>Friends By Day </h2>

        <div class="article">
        <div id="follower_count_history_by_day"></div>
    </div>

        </div>

<div class="section">
    <h2>Friends By Week </h2>

     

    <div class="article">
        <div id="follower_count_history_by_week"></div>
    </div>

        </div>

<div class="section">
    <h2>Friends By Month </h2>

     

    <div class="article">        
        <div id="follower_count_history_by_month"></div>
    </div>

        </div>

<script type="text/javascript">
// Load the Visualization API and the standard charts
google.load('visualization', '1');
// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawCharts);


function drawCharts() {

    var follower_count_history_by_day_data = new google.visualization.DataTable(
    {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27\/2012"},{"v":496}]},{"c":[{"v":new Date(2012,2,28),"f":"03\/28\/2012"},{"v":496}]},{"c":[{"v":new Date(2012,2,29),"f":"03\/29\/2012"},{"v":496}]},{"c":[{"v":new Date(2012,2,30),"f":"03\/30\/2012"},{"v":499}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21\/2012"},{"v":525}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30\/2012"},{"v":533}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Friends"}]});
    var follower_count_history_by_week_data = new google.visualization.DataTable(
    {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27"},{"v":496}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21"},{"v":525}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30"},{"v":533}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Friends"}]});
    var follower_count_history_by_month_data = new google.visualization.DataTable(
    {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/01\/2012"},{"v":496}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/01\/2012"},{"v":525}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Friends"}]});


    var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
    var formatter_date = new google.visualization.DateFormat({formatType: 'medium'});

    var chart_options = {
            colors: ['#3c8ecc'],
            width: '100%',
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
    };
    
    formatter.format(follower_count_history_by_day_data, 1);
    formatter_date.format(follower_count_history_by_day_data, 0);
    var follower_count_history_by_day_chart = new google.visualization.ChartWrapper({
        containerId: 'follower_count_history_by_day',
        chartType: 'LineChart',
        dataTable: follower_count_history_by_day_data,
        options: chart_options
    });
    follower_count_history_by_day_chart.draw();

    formatter.format(follower_count_history_by_week_data, 1);
    formatter_date.format(follower_count_history_by_week_data, 0);
    var follower_count_history_by_week_chart = new google.visualization.ChartWrapper({
        containerId: 'follower_count_history_by_week',
        chartType: 'LineChart',
        dataTable: follower_count_history_by_week_data,
        options:  chart_options    });
    follower_count_history_by_week_chart.draw();

    formatter.format(follower_count_history_by_month_data, 1);
    formatter_date.format(follower_count_history_by_month_data, 0);
    var follower_count_history_by_month_chart = new google.visualization.ChartWrapper({
        containerId: 'follower_count_history_by_month',
        chartType: 'LineChart',
        dataTable: follower_count_history_by_month_data,
        options: {
            colors: ['#3c8ecc'],
            width: '100%',
            height: 250,
            legend: "none",
            interpolateNulls: true,
            pointSize: 2,
            hAxis: {
                baselineColor: '#eee',
                format: 'MMM yyyy',
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
    follower_count_history_by_month_chart.draw();
}

</script>           <!-- end if $data_template -->
        
        
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