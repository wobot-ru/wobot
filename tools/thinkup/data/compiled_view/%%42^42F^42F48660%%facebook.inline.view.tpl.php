<?php /* Smarty version 2.6.26, created on 2012-03-27 19:55:38
         compiled from /var/www/tools/thinkup/plugins/facebook/view/facebook.inline.view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'help_link', '/var/www/tools/thinkup/plugins/facebook/view/facebook.inline.view.tpl', 3, false),array('modifier', 'urlencode', '/var/www/tools/thinkup/plugins/facebook/view/facebook.inline.view.tpl', 4, false),)), $this); ?>
<div class="section">
<div class="clearfix">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'help_link', 'id' => $this->_tpl_vars['display'])), $this); ?>

    <h2><?php if ($this->_tpl_vars['parent_name']): ?><a href="?v=<?php echo $this->_tpl_vars['parent']; ?>
&u=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
"><?php echo $this->_tpl_vars['parent_name']; ?>
</a> &rarr; <?php endif; ?><?php echo $this->_tpl_vars['header']; ?>
</h2>
    <?php if ($this->_tpl_vars['description']): ?><h3><?php echo $this->_tpl_vars['description']; ?>
</h3><?php endif; ?>
</div>

<?php if (( $this->_tpl_vars['display'] == 'posts-all' && ! $this->_tpl_vars['all_facebook_posts'] )): ?>
  <div class="ui-state-highlight ui-corner-all" style="margin: 20px 0px; padding: .5em 0.7em;"> 
    <p>
      <span class="ui-icon ui-icon-info" style="float: left; margin:.3em 0.3em 0 0;"></span>
      No Facebook posts to display.
    </p>
  </div>
<?php endif; ?>

<div class="header">
    <?php if ($this->_tpl_vars['is_searchable']): ?><a href="#" class="grid_search" title="Search" onclick="return false;"><span id="grid_search_icon">Search</span></a><?php endif; ?>
    <?php if ($this->_tpl_vars['logged_in_user'] && $this->_tpl_vars['display'] == 'all_facebook_posts'): ?> | <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
post/export.php?u=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['instance']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
">Export</a><?php endif; ?>
</div>
    

<?php if ($this->_tpl_vars['all_facebook_posts'] && ( $this->_tpl_vars['display'] == 'posts-all' || $this->_tpl_vars['display'] == 'posts-questions' )): ?>
<div id="all-posts-div">
  <?php $_from = $this->_tpl_vars['all_facebook_posts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['t']):
        $this->_foreach['foo']['iteration']++;
?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.counts_no_author.tpl", 'smarty_include_vars' => array('post' => $this->_tpl_vars['t'],'show_favorites_instead_of_retweets' => 'true')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['most_replied_to_posts']): ?>
<div id="all-posts-div">
  <?php $_from = $this->_tpl_vars['most_replied_to_posts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['t']):
        $this->_foreach['foo']['iteration']++;
?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.counts_no_author.tpl", 'smarty_include_vars' => array('post' => $this->_tpl_vars['t'],'show_favorites_instead_of_retweets' => 'true')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['messages_to_you']): ?>
<div id="all-posts-div">
  <?php $_from = $this->_tpl_vars['messages_to_you']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['t']):
        $this->_foreach['foo']['iteration']++;
?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_post.tpl", 'smarty_include_vars' => array('t' => $this->_tpl_vars['t'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endforeach; endif; unset($_from); ?>
</div>
<?php endif; ?>


<?php if (( $this->_tpl_vars['display'] == 'followers_mostfollowed' && ! $this->_tpl_vars['facebook_users'] ) || ( $this->_tpl_vars['display'] == 'friends_mostactive' && ! $this->_tpl_vars['facebook_users'] )): ?>
  <div class="alert urgent"> 
    <p>
      <span class="ui-icon ui-icon-info" style="float: left; margin:.3em 0.3em 0 0;"></span>
      No Facebook users found.
    </p>
  </div>
<?php endif; ?>

<?php if ($this->_tpl_vars['facebook_users']): ?>
  <?php $_from = $this->_tpl_vars['facebook_users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['fid'] => $this->_tpl_vars['f']):
        $this->_foreach['foo']['iteration']++;
?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_user.tpl", 'smarty_include_vars' => array('t' => $this->_tpl_vars['f'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

<?php if (( $this->_tpl_vars['display'] == 'links_from_friends' && ! $this->_tpl_vars['links_from_friends'] )): ?>
  <div class="alert urgent">
    <p>
      <span class="ui-icon ui-icon-info" style="float: left; margin:.3em 0.3em 0 0;"></span>
      No data to display.
    </p>
  </div>
<?php endif; ?>

<?php if ($this->_tpl_vars['links_from_friends']): ?>
  <?php $_from = $this->_tpl_vars['links_from_friends']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['lid'] => $this->_tpl_vars['l']):
        $this->_foreach['foo']['iteration']++;
?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_link.tpl", 'smarty_include_vars' => array('t' => $this->_tpl_vars['f'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <?php endforeach; endif; unset($_from); ?>  
<?php endif; ?>

<?php if ($this->_tpl_vars['is_searchable']): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_grid.search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/js/grid_search.js"></script>
    
<?php endif; ?>

<div class="view-all" id="older-posts-div">
  <?php if ($this->_tpl_vars['next_page']): ?>
    <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?<?php if ($_GET['v']): ?>v=<?php echo $_GET['v']; ?>
&<?php endif; ?><?php if ($_GET['u']): ?>u=<?php echo $_GET['u']; ?>
&<?php endif; ?><?php if ($_GET['n']): ?>n=<?php echo ((is_array($_tmp=$_GET['n'])) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&<?php endif; ?>page=<?php echo $this->_tpl_vars['next_page']; ?>
" id="next_page">&#60; Older Posts</a>
  <?php endif; ?>
  <?php if ($this->_tpl_vars['last_page']): ?>
    | <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?<?php if ($_GET['v']): ?>v=<?php echo $_GET['v']; ?>
&<?php endif; ?><?php if ($_GET['u']): ?>u=<?php echo $_GET['u']; ?>
&<?php endif; ?><?php if ($_GET['n']): ?>n=<?php echo ((is_array($_tmp=$_GET['n'])) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&<?php endif; ?>page=<?php echo $this->_tpl_vars['last_page']; ?>
" id="last_page">Newer Posts  &#62;</a>
  <?php endif; ?>
</div>
</div>