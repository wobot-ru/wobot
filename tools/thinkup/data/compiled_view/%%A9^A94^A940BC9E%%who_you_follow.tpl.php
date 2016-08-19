<?php /* Smarty version 2.6.26, created on 2012-03-27 18:42:14
         compiled from /var/www/tools/thinkup/plugins/twitter/view/who_you_follow.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/var/www/tools/thinkup/plugins/twitter/view/who_you_follow.tpl', 1, false),)), $this); ?>
<?php if (count($this->_tpl_vars['chatterboxes']) > 1): ?>
    <div class="section">
    <h2>Chatterboxes</h2>
    <div class="article" style="padding-left : 0px; padding-top : 0px;">
    <?php $_from = $this->_tpl_vars['chatterboxes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['u']):
        $this->_foreach['foo']['iteration']++;
?>
      <div class="avatar-container" style="float:left;margin:7px;">
        <a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['u']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['u']['user_name']; ?>
"><img src="<?php echo $this->_tpl_vars['u']['avatar']; ?>
" class="avatar2"/><img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo $this->_tpl_vars['u']['network']; ?>
/assets/img/favicon.png" class="service-icon2"/></a>
      </div>
    <?php endforeach; endif; unset($_from); ?>
    <br /><br /><br />
    </div>
    <div class="view-all"><a href="?v=friends-mostactive&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=twitter">More...</a></div>
    </div>
<?php else: ?>
        <div class="alert urgent">No users to display. <?php if ($this->_tpl_vars['logged_in_user']): ?>Update your data and try again.<?php endif; ?></div>
<?php endif; ?>

<?php if (count($this->_tpl_vars['deadbeats']) > 1): ?>
    <div class="section">
        <h2>Quietest</h2>
        <div class="article" style="padding-left : 0px; padding-top : 0px;">
        <?php $_from = $this->_tpl_vars['deadbeats']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['u']):
        $this->_foreach['foo']['iteration']++;
?>
          <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['u']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['u']['user_name']; ?>
"><img src="<?php echo $this->_tpl_vars['u']['avatar']; ?>
" class="avatar2"/><img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo $this->_tpl_vars['u']['network']; ?>
/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
        <?php endforeach; endif; unset($_from); ?>
        <br /><br /><br />
        </div>
        <div class="view-all"><a href="?v=friends-leastactive&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=twitter">More...</a></div>
    </div>
<?php endif; ?>

<?php if (count($this->_tpl_vars['popular']) > 1): ?>
    <div class="section">
        <h2>Popular</h2>
        <div class="article" style="padding-left : 0px; padding-top : 0px;">
        <?php $_from = $this->_tpl_vars['popular']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['u']):
        $this->_foreach['foo']['iteration']++;
?>
          <div class="avatar-container" style="float:left;margin:7px;">
            <a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['u']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['u']['user_name']; ?>
"><img src="<?php echo $this->_tpl_vars['u']['avatar']; ?>
" class="avatar2"/><img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo $this->_tpl_vars['u']['network']; ?>
/assets/img/favicon.png" class="service-icon2"/></a>
          </div>
        <?php endforeach; endif; unset($_from); ?>
        <br /><br /><br />
        </div>
        <div class="view-all"><a href="?v=friends-mostfollowed&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=twitter">More...</a></div>
    </div>
<?php endif; ?>