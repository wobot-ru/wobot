<?php /* Smarty version 2.6.26, created on 2012-03-27 18:03:59
         compiled from dashboard.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'urlencode', 'dashboard.tpl', 15, false),array('modifier', 'get_plugin_path', 'dashboard.tpl', 48, false),array('modifier', 'capitalize', 'dashboard.tpl', 52, false),array('modifier', 'relative_datetime', 'dashboard.tpl', 54, false),array('modifier', 'number_format', 'dashboard.tpl', 80, false),array('modifier', 'count', 'dashboard.tpl', 131, false),array('modifier', 'round', 'dashboard.tpl', 185, false),array('modifier', 'ucwords', 'dashboard.tpl', 401, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_statusbar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="container_24">
  <div class="clearfix">

    <!-- begin left nav -->
    <div class="grid_4 alpha omega">
        <?php if ($this->_tpl_vars['instance']): ?>
      <div id="nav">
        <ul id="top-level-sidenav">
        <?php endif; ?>
        <?php if ($this->_tpl_vars['instance']): ?>
              <li<?php if ($_GET['v'] == ''): ?> class="selected"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?u=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
">Dashboard</a>
              </li>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['sidebar_menu']): ?>
          <?php $_from = $this->_tpl_vars['sidebar_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['smenuloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['smenuloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['smkey'] => $this->_tpl_vars['sidebar_menu_item']):
        $this->_foreach['smenuloop']['iteration']++;
?>
          <?php if (! $this->_tpl_vars['sidebar_menu_item']->parent): ?>
                <li<?php if ($_GET['v'] == $this->_tpl_vars['smkey'] || $this->_tpl_vars['parent'] == $this->_tpl_vars['smkey']): ?> class="selected"<?php endif; ?>>
                                <?php if ($this->_tpl_vars['parent'] == $this->_tpl_vars['smkey']): ?><?php $this->assign('parent_name', $this->_tpl_vars['sidebar_menu_item']->name); ?><?php endif; ?>
                <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?v=<?php echo $this->_tpl_vars['smkey']; ?>
&u=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
"><?php echo $this->_tpl_vars['sidebar_menu_item']->name; ?>
</a></li>
             <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>

        <?php endif; ?>
        <?php if ($this->_tpl_vars['instance']): ?>
        </ul>
      </div>
        <?php endif; ?>
    </div>

    <div class="thinkup-canvas round-all grid_20 alpha omega prepend_20 append_20" style="min-height:340px">
      <div class="prefix_1 suffix_1">

        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        <?php if ($this->_tpl_vars['instance']): ?>
          <!--begin public user dashboard-->
          <?php if ($this->_tpl_vars['user_details']): ?>
            <div class="grid_18 alpha omega">
              <div class="clearfix alert stats round-all" id="">
                <div class="grid_2 alpha">
                  <div class="avatar-container">
                    <img src="<?php echo $this->_tpl_vars['user_details']->avatar; ?>
" class="avatar2"/>
                    <img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo ((is_array($_tmp=$this->_tpl_vars['user_details']->network)) ? $this->_run_mod_handler('get_plugin_path', true, $_tmp) : smarty_modifier_get_plugin_path($_tmp)); ?>
/assets/img/favicon.png" class="service-icon2"/>
                  </div>
                </div>
                <div class="grid_15 omega">
                  <span class="tweet"><?php echo $this->_tpl_vars['user_details']->username; ?>
 <span style="color:#ccc"><?php echo ((is_array($_tmp=$this->_tpl_vars['user_details']->network)) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>
</span></span><br />
                  <div class="small">
                    <?php if ($this->_tpl_vars['instance']->crawler_last_run == 'realtime'): ?><span style="color:green;">&#9679;</span> Updated in realtime<?php else: ?>Updated <?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->crawler_last_run)) ? $this->_run_mod_handler('relative_datetime', true, $_tmp) : smarty_modifier_relative_datetime($_tmp)); ?>
 ago<?php endif; ?><?php if (! $this->_tpl_vars['instance']->is_active): ?> (paused)<?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($this->_tpl_vars['data_template']): ?>
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['data_template'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
          <?php else: ?> <!-- else if no $data_template -->

            <?php if ($this->_tpl_vars['hot_posts_data']): ?>
                <div class="section">
                        <h2>Response Rates</h2>
                        <div class="clearfix article">
                            <div id="hot_posts"></div>
                        </div>
                </div>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['least_likely_followers']): ?>
              <div class="clearfix section">
                <h2>This Week's Most Discerning Followers</h2>
                <div class="clearfix article" style="padding-top : 0px;">
                <?php $_from = $this->_tpl_vars['least_likely_followers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['uid'] => $this->_tpl_vars['u']):
        $this->_foreach['foo']['iteration']++;
?>
                  <div class="avatar-container" style="float:left;margin:7px;">
                    <a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['u']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['u']['user_name']; ?>
 has <?php echo ((is_array($_tmp=$this->_tpl_vars['u']['follower_count'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 followers and <?php echo ((is_array($_tmp=$this->_tpl_vars['u']['friend_count'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 friends"><img src="<?php echo $this->_tpl_vars['u']['avatar']; ?>
" class="avatar2"/><img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo $this->_tpl_vars['u']['network']; ?>
/assets/img/favicon.png" class="service-icon2"/></a>
                  </div>
                <?php endforeach; endif; unset($_from); ?>
                <br /><br /><br />    
                </div>
                <div class="clearfix view-all">
                    <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?v=followers-leastlikely&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=<?php echo $this->_tpl_vars['instance']->network; ?>
">More...</a>
                </div>
                </div>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['click_stats_data']): ?>
            <div class="section">
                    <h2>Clickthrough Rates</h2>
                    <div class="clearfix article">
                            <div id="click_stats"></div>
                    </div>
            </div>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['most_replied_to_1wk']): ?>
              <div class="section">
                <h2>This Week's Most <?php if ($this->_tpl_vars['instance']->network == 'google+'): ?>Discussed<?php else: ?>Replied-To<?php endif; ?> Posts</h2>
                <?php $_from = $this->_tpl_vars['most_replied_to_1wk']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['t']):
        $this->_foreach['foo']['iteration']++;
?>
                    <?php if ($this->_tpl_vars['instance']->network == 'twitter'): ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.counts_no_author.tpl", 'smarty_include_vars' => array('post' => $this->_tpl_vars['t'],'headings' => 'NONE')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php else: ?>
                        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.counts_no_author.tpl", 'smarty_include_vars' => array('post' => $this->_tpl_vars['t'],'headings' => 'NONE','show_favorites_instead_of_retweets' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                    <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
              </div>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['most_faved_1wk']): ?>
              <div class="section">
                <h2>This Week's Most <?php if ($this->_tpl_vars['instance']->network == 'google+'): ?>+1ed<?php else: ?>Liked<?php endif; ?> Posts</h2>
                <?php $_from = $this->_tpl_vars['most_faved_1wk']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['t']):
        $this->_foreach['foo']['iteration']++;
?>
                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.counts_no_author.tpl", 'smarty_include_vars' => array('post' => $this->_tpl_vars['t'],'headings' => 'NONE','show_favorites_instead_of_retweets' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <?php endforeach; endif; unset($_from); ?>
              </div>
            <?php endif; ?>

            <?php if ($this->_tpl_vars['follower_count_history_by_day']['history'] && $this->_tpl_vars['follower_count_history_by_week']['history']): ?>
              
                <div class="section" style="float : left; clear : none; width : 345px;">
                  <h2>
                    <?php if ($this->_tpl_vars['instance']->network == 'twitter'): ?>Followers <?php elseif ($this->_tpl_vars['instance']->network == 'facebook page'): ?>Fans <?php elseif ($this->_tpl_vars['instance']->network == 'facebook'): ?>Friends <?php endif; ?>By Day
                    <?php if ($this->_tpl_vars['follower_count_history_by_day']['trend']): ?>
                        (<?php if ($this->_tpl_vars['follower_count_history_by_day']['trend'] > 0): ?><span style="color:green">+<?php else: ?><span style="color:red"><?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['follower_count_history_by_day']['trend'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</span>/day)
                    <?php endif; ?>
                  </h2>
                  <?php if (! $this->_tpl_vars['follower_count_history_by_day']['history'] || count($this->_tpl_vars['follower_count_history_by_day']['history']) < 2): ?>
                    <div class="alert helpful">Not enough data to display chart</div>
                  <?php else: ?>
                      <div class="article">
                        <div id="follower_count_history_by_day"></div>
                    </div>
                    <div class="view-all">
                    <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?v=<?php if ($this->_tpl_vars['instance']->network != 'twitter'): ?>friends<?php else: ?>followers<?php endif; ?>&u=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
">More...</a>
                  </div>
                    
                  <?php endif; ?>
                </div>
                <div class="section" style="float : left; clear : none;margin-left : 16px; width : 345px;">
                  <h2>
                    <?php if ($this->_tpl_vars['instance']->network == 'twitter'): ?>Followers <?php elseif ($this->_tpl_vars['instance']->network == 'facebook page'): ?>Fans <?php elseif ($this->_tpl_vars['instance']->network == 'facebook'): ?>Friends <?php endif; ?> By Week
                    <?php if ($this->_tpl_vars['follower_count_history_by_week']['trend'] != 0): ?>
                        (<?php if ($this->_tpl_vars['follower_count_history_by_week']['trend'] > 0): ?><span style="color:green">+<?php else: ?><span style="color:red"><?php endif; ?><?php echo ((is_array($_tmp=$this->_tpl_vars['follower_count_history_by_week']['trend'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
</span>/week)
                    <?php endif; ?>
                  </h2>
                  <?php if (! $this->_tpl_vars['follower_count_history_by_week']['history'] || count($this->_tpl_vars['follower_count_history_by_week']['history']) < 2): ?>
                      <div class="alert helpful">Not enough data to display chart</div>
                  <?php else: ?>
                    <div class="article">
                        <div id="follower_count_history_by_week"></div>
                    </div>
                    <?php if ($this->_tpl_vars['follower_count_history_by_week']['milestone'] && $this->_tpl_vars['follower_count_history_by_week']['milestone']['will_take'] > 0): ?>
                    <div class="stream-pagination"><small style="color:gray">
                        <span style="background-color:#FFFF80;color:black"><?php echo $this->_tpl_vars['follower_count_history_by_week']['milestone']['will_take']; ?>
 week<?php if ($this->_tpl_vars['follower_count_history_by_week']['milestone']['will_take'] > 1): ?>s<?php endif; ?></span> till you reach <span style="background-color:#FFFF80;color:black"><?php echo ((is_array($_tmp=$this->_tpl_vars['follower_count_history_by_week']['milestone']['next_milestone'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 followers</span> at this rate.
                    </small></div>
                    <?php endif; ?>
                  <div class="view-all">
                    <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?v=<?php if ($this->_tpl_vars['instance']->network != 'twitter'): ?>friends<?php else: ?>followers<?php endif; ?>&u=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
">More...</a>
                  </div>
                  <?php endif; ?>
                </div>

            <?php endif; ?>

            <?php if ($this->_tpl_vars['most_retweeted_1wk']): ?>
              <div class="clearfix section">
                <h2>This Week's Most <?php if ($this->_tpl_vars['instance']->network == 'google+'): ?>Reshared<?php else: ?>Retweeted<?php endif; ?> Posts</h2>
                <?php $_from = $this->_tpl_vars['most_retweeted_1wk']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['t']):
        $this->_foreach['foo']['iteration']++;
?>
                  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.counts_no_author.tpl", 'smarty_include_vars' => array('post' => $this->_tpl_vars['t'],'show_favorites_instead_of_retweets' => false)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                <?php endforeach; endif; unset($_from); ?>
              </div>
            <?php endif; ?>
            <?php if ($this->_tpl_vars['instance']->network == 'twitter'): ?>
              <div class="section" style="float : left; clear : none; width : 345px;">
                  <div class="alpha">
                      <h2>Post Types</span></h2>
                      <div class="small prepend article">
                        <div id="post_types"></div>
                       </div>
                       <div class="stream-pagination"><small style="color:#666;padding:5px;">
                          <?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->percentage_replies)) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
% posts are replies<br>
                          <?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->percentage_links)) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
% posts contain links
                          </small>
                       </div>
                       <script>
                          var replies = <?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->percentage_replies)) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
;
                          var links = <?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->percentage_links)) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
;
                       </script>
                </div>
            </div>

            <div class="section" style="float : left; clear : none;margin-left : 10px; width : 345px;">
                   <div class="omega">
                        <h2>Client Usage <span class="detail">(all posts)</span></h2>
                        <div class="article">
                        <div id="client_usage"></div>
                        </div>
                        <div class="stream-pagination">
                        <small style="color:#666;padding:5px;">Recently posting about <?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->posts_per_day)) ? $this->_run_mod_handler('round', true, $_tmp) : round($_tmp)); ?>
 times a day<?php if ($this->_tpl_vars['latest_clients_usage']): ?>, mostly using <?php $_from = $this->_tpl_vars['latest_clients_usage']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['name'] => $this->_tpl_vars['num_posts']):
        $this->_foreach['foo']['iteration']++;
?><?php echo $this->_tpl_vars['name']; ?>
<?php if (! ($this->_foreach['foo']['iteration'] == $this->_foreach['foo']['total'])): ?> and <?php endif; ?><?php endforeach; endif; unset($_from); ?><?php endif; ?></small>
                        </div>
                   </div>
              </div>

            <?php endif; ?>
            <script type="text/javascript">
                // Load the Visualization API and the standard charts
                google.load('visualization', '1');
                // Set a callback to run when the Google Visualization API is loaded.
                google.setOnLoadCallback(drawCharts);

                <?php echo '
                function drawCharts() {
                '; ?>


                  var follower_count_history_by_day_data = new google.visualization.DataTable(
                  <?php echo $this->_tpl_vars['follower_count_history_by_day']['vis_data']; ?>
);
                  var follower_count_history_by_week_data = new google.visualization.DataTable(
                  <?php echo $this->_tpl_vars['follower_count_history_by_week']['vis_data']; ?>
);

                  var hot_posts_data = new google.visualization.DataTable(<?php echo $this->_tpl_vars['hot_posts_data']; ?>
);
                  var client_usage_data = new google.visualization.DataTable(<?php echo $this->_tpl_vars['all_time_clients_usage']; ?>
);
                  var click_stats_data = new google.visualization.DataTable(<?php echo $this->_tpl_vars['click_stats_data']; ?>
);

                  <?php echo '

                  var formatter = new google.visualization.NumberFormat({fractionDigits: 0});
                  var formatter_date = new google.visualization.DateFormat({formatType: \'medium\'});

                  var hot_posts_chart = new google.visualization.ChartWrapper({
                      containerId: \'hot_posts\',
                      chartType: \'BarChart\',
                      dataTable: hot_posts_data,
                      options: {
                          colors: [\'#3e5d9a\', \'#3c8ecc\', \'#BBCCDD\'],
                          isStacked: true,
                          width: 650,
                          height: 250,
                          chartArea:{left:300,height:"80%"},
                          legend: \'bottom\',
                          hAxis: {
                            textStyle: { color: \'#fff\', fontSize: 1 }
                          },
                          vAxis: {
                            minValue: 0,
                            baselineColor: \'#ccc\',
                            textStyle: { color: \'#999\' },
                            gridlines: { color: \'#eee\' }
                          },
                      }
                  });
                  hot_posts_chart.draw();

                  formatter.format(click_stats_data, 1);
                  var click_stats_chart = new google.visualization.ChartWrapper({
                      containerId: \'click_stats\',
                      chartType: \'BarChart\',
                      dataTable: click_stats_data,
                      options: {
                          colors: [\'#3c8ecc\'],
                          isStacked: true,
                          width: 650,
                          height: 250,
                          chartArea:{left:300,height:"80%"},
                          legend: \'none\',
                          hAxis: {
                            textStyle: { color: \'#fff\', fontSize: 1 }
                          },
                          vAxis: {
                            minValue: 0,
                            baselineColor: \'#ccc\',
                            textStyle: { color: \'#999\' },
                            gridlines: { color: \'#eee\' }
                          },
                      }
                  });
                  click_stats_chart.draw();

                  formatter.format(follower_count_history_by_day_data, 1);
                  formatter_date.format(follower_count_history_by_day_data, 0);

                  var follower_count_history_by_day_chart = new google.visualization.ChartWrapper({
                      containerId: \'follower_count_history_by_day\',
                      chartType: \'LineChart\',
                      dataTable: follower_count_history_by_day_data,
                      options: {
                          width: 325,
                          height: 250,
                          legend: "none",
                          interpolateNulls: true,
                          pointSize: 2,
                          hAxis: {
                              baselineColor: \'#eee\',
                              format: \'MMM d\',
                              textStyle: { color: \'#999\' },
                              gridlines: { color: \'#eee\' }
                          },
                          vAxis: {
                              baselineColor: \'#eee\',
                              textStyle: { color: \'#999\' },
                              gridlines: { color: \'#eee\' }
                          },
                      },
                  });
                  follower_count_history_by_day_chart.draw();

                  formatter.format(follower_count_history_by_week_data, 1);
                  formatter_date.format(follower_count_history_by_week_data, 0);

                  var follower_count_history_by_week_chart = new google.visualization.ChartWrapper({
                      containerId: \'follower_count_history_by_week\',
                      chartType: \'LineChart\',
                      dataTable: follower_count_history_by_week_data,
                      options: {
                          width: 325,
                          height: 250,
                          legend: "none",
                          interpolateNulls: true,
                          pointSize: 2,
                          hAxis: {
                              baselineColor: \'#eee\',
                              format: \'MMM d\',
                              textStyle: { color: \'#999\' },
                              gridlines: { color: \'#eee\' }
                          },
                          vAxis: {
                              baselineColor: \'#eee\',
                              textStyle: { color: \'#999\' },
                              gridlines: { color: \'#eee\' }
                          },
                      },
                  });
                  follower_count_history_by_week_chart.draw();

                  if (typeof(replies) != \'undefined\') {
                    var post_types = new google.visualization.DataTable();
                    post_types.addColumn(\'string\', \'Type\');
                    post_types.addColumn(\'number\', \'Percentage\');
                    post_types.addRows([
                        [\'Conversationalist\', {v: replies/100, f: replies + \'%\'}], 
                        [\'Broadcaster\', {v: links/100, f: links + \'%\'}]
                    ]);

                    var post_type_chart = new google.visualization.ChartWrapper({
                        containerId: \'post_types\',
                        chartType: \'ColumnChart\',
                        dataTable: post_types,
                        options: {
                            colors: [\'#3c8ecc\'],
                            width: 300,
                            height: 200,
                            legend: \'none\',
                            hAxis: {
                                minValue: 0,
                                maxValue: 1,
                                textStyle: { color: \'#000\' },
                            },
                            vAxis: {
                                textStyle: { color: \'#666\' },
                                gridlines: { color: \'#ccc\' },
                                format:\'#,###%\',
                                baselineColor: \'#ccc\',
                            },
                        }
                    });
                    post_type_chart.draw();
                  }

                  formatter.format(client_usage_data, 1);
                  var client_usage_chart = new google.visualization.ChartWrapper({
                      containerId: \'client_usage\',
                      // chartType: \'ColumnChart\',
                      chartType: \'PieChart\',
                      dataTable: client_usage_data,
                      options: {
                          titleTextStyle: {color: \'#848884\', fontSize: 19},
                          width: 300,
                          height: 300,
                          sliceVisibilityThreshold: 1/100,
                          chartArea: { width: \'100%\' },
                          pieSliceText: \'label\',
                      }
                  });
                  client_usage_chart.draw();
                }
            
                  '; ?>

            </script>

          <?php endif; ?> <!-- end if $data_template -->
        <?php endif; ?>

        <?php if (! $this->_tpl_vars['instance']): ?>
          <div style="width:60%;text-align:center;">
          <?php if ($this->_tpl_vars['add_user_buttons']): ?>
          <br ><br>
            <?php $_from = $this->_tpl_vars['add_user_buttons']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['smenuloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['smenuloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['smkey'] => $this->_tpl_vars['button']):
        $this->_foreach['smenuloop']['iteration']++;
?>
                <div style="float:right;padding:5px;"><a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
account/?p=<?php echo $this->_tpl_vars['button']; ?>
" class="linkbutton emphasized">Add a <?php if ($this->_tpl_vars['button'] == 'googleplus'): ?>Google+<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['button'])) ? $this->_run_mod_handler('ucwords', true, $_tmp) : ucwords($_tmp)); ?>
<?php endif; ?> Account &rarr;</a></div>
                <div style="clear:both;">&nbsp;</div>
             <?php endforeach; endif; unset($_from); ?>
          <?php endif; ?>
          <?php if ($this->_tpl_vars['logged_in_user']): ?>
          <div style="float:right;padding:5px;"><a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
account/" class="linkbutton emphasized">Adjust Your Settings</a></div>
          <?php else: ?>
          <div style="float:right;padding:5px;"><a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
session/login.php" class="linkbutton emphasized">Log In</a></div>
          <?php endif; ?>
          </div>
        <?php endif; ?>

      </div> <!-- /.prefix_1 -->
    </div> <!-- /.thinkup-canvas -->

  </div> <!-- /.clearfix -->
</div> <!-- /.container_24 -->

<script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/js/linkify.js"></script>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>