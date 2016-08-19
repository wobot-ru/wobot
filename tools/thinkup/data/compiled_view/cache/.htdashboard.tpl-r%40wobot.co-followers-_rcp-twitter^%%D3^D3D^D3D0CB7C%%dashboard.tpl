304
a:4:{s:8:"template";a:6:{s:13:"dashboard.tpl";b:1;s:11:"_header.tpl";b:1;s:14:"_statusbar.tpl";b:1;s:16:"_usermessage.tpl";b:1;s:57:"/var/www/tools/thinkup/plugins/twitter/view/followers.tpl";b:1;s:11:"_footer.tpl";b:1;}s:9:"timestamp";i:1337207707;s:7:"expires";i:1337208307;s:13:"cache_serials";a:0:{}}<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/Article">
<head>
  <meta charset="utf-8">
  <title>_rcp on Twitter | ThinkUp</title>
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
                                                <option value="/tools/thinkup/?u=Roman+Yudin&n=facebook">Roman Yudin - Facebook</option>
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
                              <li>
                <a href="/tools/thinkup/?u=_rcp&n=twitter">Dashboard</a>
              </li>
                                                    <li>
                                                <a href="/tools/thinkup/?v=tweets&u=_rcp&n=twitter">Tweets</a></li>
                                                   <li class="selected">
                                                <a href="/tools/thinkup/?v=followers&u=_rcp&n=twitter">Followers</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=you-follow&u=_rcp&n=twitter">Who You Follow</a></li>
                                                   <li>
                                                <a href="/tools/thinkup/?v=links&u=_rcp&n=twitter">Links</a></li>
                                                                                                                                                                                                                                                                                                                                                                                                               
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
                    <img src="http://a0.twimg.com/profile_images/702090982/president_normal.JPG" class="avatar2"/>
                    <img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet">_rcp <span style="color:#ccc">Twitter</span></span><br />
                  <div class="small">
                    Updated 4 mins  ago                  </div>
                </div>
              </div>
            </div>
          
                      
<div class="section">
    <h2>All-Time Most Discerning Followers</h2>
    <div class="article" style="padding-left : 0px; padding-top : 0px;">
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=455645672" title="kamyninra has 15,267 followers and 1,862 friends"><img src="http://a0.twimg.com/profile_images/1960571148/ava_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=145192600" title="ikosmetika has 10,033 followers and 1,516 friends"><img src="http://a0.twimg.com/profile_images/1217239367/26637431_normal.jpeg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=179931035" title="suite48media has 75,934 followers and 32,795 friends"><img src="http://a0.twimg.com/profile_images/1552312680/logo-tiny_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=198154276" title="mikeyduturbure has 29,214 followers and 13,293 friends"><img src="http://a0.twimg.com/profile_images/1813324156/Michael_Duturbure_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=75122622" title="MyBinding has 61,770 followers and 28,567 friends"><img src="http://a0.twimg.com/profile_images/420949502/mybinding.mark_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=11633492" title="wili has 32,667 followers and 17,358 friends"><img src="http://a0.twimg.com/profile_images/1200216734/Ville_Miettinen_square_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=422074185" title="ArtBazi has 27,638 followers and 15,550 friends"><img src="http://a0.twimg.com/profile_images/1659288446/o2_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=190551728" title="harshavee has 19,020 followers and 10,894 friends"><img src="http://a0.twimg.com/profile_images/1932386112/5546_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=39365101" title="4thWeb has 89,787 followers and 51,513 friends"><img src="http://a0.twimg.com/profile_images/1653072276/4thWeb_logo_03_180x180_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=27087844" title="mandystadt has 68,703 followers and 48,110 friends"><img src="http://a0.twimg.com/profile_images/1789071655/profile_normal.JPG" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=391653134" title="Faust_S has 28,567 followers and 21,318 friends"><img src="http://a0.twimg.com/profile_images/1792417686/faust_s_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=409407347" title="New_Galleon has 11,880 followers and 9,021 friends"><img src="http://a0.twimg.com/profile_images/1632332543/5885635722-tipok_ru_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=8071902" title="zaibatsu has 167,337 followers and 128,840 friends"><img src="http://a0.twimg.com/profile_images/1765976458/1111_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
        <br /><br /><br />
    </div>
    <div class="view-all"><a href="?v=followers-leastlikely&u=_rcp&n=twitter">More...</a></div>
</div>

<div class="section">
    <h2>Most Popular Followers</h2>
    <div class="article" style="padding-left : 0px; padding-top : 0px;">
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=17850012" title="Radioblogger has 489,225 followers and 495,350 friends"><img src="http://a0.twimg.com/profile_images/1616831345/behind-the-curtain_normal.gif" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=21507883" title="pramitjnathan has 247,886 followers and 254,277 friends"><img src="http://a0.twimg.com/profile_images/766285098/pj11_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=56293707" title="Ujjwal_krishna has 242,325 followers and 246,833 friends"><img src="http://a0.twimg.com/profile_images/1403203591/loader1__1__normal.gif" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=12504732" title="kamper has 215,055 followers and 211,766 friends"><img src="http://a0.twimg.com/profile_images/321414936/ls_4168_til_twitter_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=18087450" title="Orrin_Woodward has 193,646 followers and 193,333 friends"><img src="http://a0.twimg.com/profile_images/272262606/OrrinWoodward_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=28428838" title="Sheri_ls has 179,168 followers and 146,949 friends"><img src="http://a0.twimg.com/profile_images/1161467780/nygala1_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=8071902" title="zaibatsu has 167,337 followers and 128,840 friends"><img src="http://a0.twimg.com/profile_images/1765976458/1111_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=14550649" title="eleesha has 155,354 followers and 152,848 friends"><img src="http://a0.twimg.com/profile_images/1929763779/Eleesha_Author_of_The_Soul_Whisperer_book_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=55882303" title="PersonhoodFL has 149,557 followers and 159,434 friends"><img src="http://a0.twimg.com/profile_images/1208897994/d29eaadb-7376-47f6-8f85-3612178dfc29_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=44403079" title="TCTaxTeaParty has 149,454 followers and 156,358 friends"><img src="http://a0.twimg.com/profile_images/1208899584/657d3589-6cb4-45ba-a897-cfe6811c3f45_normal.png" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=8820652" title="MariSmith has 143,740 followers and 131,917 friends"><img src="http://a0.twimg.com/profile_images/1908271154/005_Mari_Smith_8674_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=14720411" title="mikepfs has 138,672 followers and 139,174 friends"><img src="http://a0.twimg.com/profile_images/1734712455/image_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
          <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=46308074" title="megmark has 129,374 followers and 116,781 friends"><img src="http://a0.twimg.com/profile_images/1383314031/M_Logo_with_Picture_normal.jpg" class="avatar2"/><img src="/tools/thinkup/plugins/twitter/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
        <br /><br /><br />
</div>
<div class="view-all"><a href="?v=followers-mostfollowed&u=_rcp&n=twitter">More...</a></div>
</div>


<div class="section">
    <h2>Follower Count By Day </h2>
        <div class="article">
        <div id="follower_count_history_by_day"></div>
    </div>
        </div>

<div class="section">
    <h2>Follower Count By Week </h2>
        <div class="article">
        <div id="follower_count_history_by_week"></div>
    </div>
        </div>

<div class="section">
    <h2>Follower Count By Month </h2>
        <div class="article">
        <div id="follower_count_history_by_month"></div>
    </div>

        </div>

<div class="section">
    <h2>List Membership Count By Day </h2>
        <div class="article">

    <div id="list_membership_count_history_by_day"></div>

        </div>
    </div>

<div class="section">
    <h2>List Membership Count By Week </h2>
        <div class="article">

    <div id="list_membership_count_history_by_week"></div>

        </div>
    </div>

<div class="section">
    <h2>List Membership Count By Month </h2>
        <div class="article">

    <div id="list_membership_count_history_by_month"></div>
    
        </div>
    </div>

<script type="text/javascript">
// Load the Visualization API and the standard charts
google.load('visualization', '1');
// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawCharts);


function drawCharts() {

    var follower_count_history_by_day_data = new google.visualization.DataTable(
        {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27\/2012"},{"v":930}]},{"c":[{"v":new Date(2012,2,28),"f":"03\/28\/2012"},{"v":916}]},{"c":[{"v":new Date(2012,2,29),"f":"03\/29\/2012"},{"v":910}]},{"c":[{"v":new Date(2012,2,30),"f":"03\/30\/2012"},{"v":919}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21\/2012"},{"v":919}]},{"c":[{"v":new Date(2012,3,23),"f":"04\/23\/2012"},{"v":917}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30\/2012"},{"v":932}]},{"c":[{"v":new Date(2012,4,3),"f":"05\/03\/2012"},{"v":949}]},{"c":[{"v":new Date(2012,4,17),"f":"05\/17\/2012"},{"v":925}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Followers"}]});
    var follower_count_history_by_week_data = new google.visualization.DataTable(
        {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27"},{"v":930}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21"},{"v":919}]},{"c":[{"v":new Date(2012,3,23),"f":"04\/23"},{"v":917}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30"},{"v":932}]},{"c":[{"v":new Date(2012,4,17),"f":"05\/17"},{"v":925}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Followers"}]});
    var follower_count_history_by_month_data = new google.visualization.DataTable(
        {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/01\/2012"},{"v":930}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/01\/2012"},{"v":919}]},{"c":[{"v":new Date(2012,4,3),"f":"05\/01\/2012"},{"v":949}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Followers"}]});
    var list_membership_count_history_by_day_data = new google.visualization.DataTable(
        {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,2,28),"f":"03\/28\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,2,29),"f":"03\/29\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,2,30),"f":"03\/30\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,3,23),"f":"04\/23\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,4,17),"f":"05\/17\/2012"},{"v":8}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Lists"}]});
    var list_membership_count_history_by_week_data = new google.visualization.DataTable(
        {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/27"},{"v":8}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/21"},{"v":8}]},{"c":[{"v":new Date(2012,3,23),"f":"04\/23"},{"v":8}]},{"c":[{"v":new Date(2012,3,30),"f":"04\/30"},{"v":8}]},{"c":[{"v":new Date(2012,4,17),"f":"05\/17"},{"v":8}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Lists"}]});
    var list_membership_count_history_by_month_data = new google.visualization.DataTable(
        {"rows":[{"c":[{"v":new Date(2012,2,27),"f":"03\/01\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,3,21),"f":"04\/01\/2012"},{"v":8}]},{"c":[{"v":new Date(2012,4,17),"f":"05\/01\/2012"},{"v":8}]}],"cols":[{"type":"date","label":"Date"},{"type":"number","label":"Lists"}]});

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
        options: chart_options
    });
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

    formatter.format(list_membership_count_history_by_day_data, 1);
    formatter_date.format(list_membership_count_history_by_day_data, 0);
    var list_membership_count_history_by_day_chart = new google.visualization.ChartWrapper({
        containerId: 'list_membership_count_history_by_day',
        chartType: 'LineChart',
        dataTable: list_membership_count_history_by_day_data,
        options: chart_options
    });
    list_membership_count_history_by_day_chart.draw();

    formatter.format(list_membership_count_history_by_week_data, 1);
    formatter_date.format(list_membership_count_history_by_week_data, 0);
    var list_membership_count_history_by_week_chart = new google.visualization.ChartWrapper({
        containerId: 'list_membership_count_history_by_week',
        chartType: 'LineChart',
        dataTable: list_membership_count_history_by_week_data,
        options: chart_options
    });
    list_membership_count_history_by_week_chart.draw();
    
    formatter.format(list_membership_count_history_by_month_data, 1);
    formatter_date.format(list_membership_count_history_by_month_data, 0);
    var list_membership_count_history_by_month_chart = new google.visualization.ChartWrapper({
        containerId: 'list_membership_count_history_by_month',
        chartType: 'LineChart',
        dataTable: list_membership_count_history_by_month_data,
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
    list_membership_count_history_by_month_chart.draw();

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