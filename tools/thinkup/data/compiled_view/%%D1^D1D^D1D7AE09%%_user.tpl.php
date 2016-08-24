<?php /* Smarty version 2.6.26, created on 2012-03-27 19:55:02
         compiled from _user.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'get_plugin_path', '_user.tpl', 5, false),array('modifier', 'number_format', '_user.tpl', 15, false),array('modifier', 'relative_datetime', '_user.tpl', 24, false),)), $this); ?>
<div class="individual-tweet prepend_20 clearfix<?php if ($this->_tpl_vars['t']['is_protected']): ?> private<?php endif; ?> article">
  <div class="grid_2 alpha">
    <div class="avatar-container">
        <?php if ($this->_tpl_vars['f']['network'] == 'twitter'): ?><a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['f']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['f']['user_name']; ?>
 on Twitter"><?php endif; ?>
      <img src="<?php echo $this->_tpl_vars['f']['avatar']; ?>
" class="avatar"/><img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo ((is_array($_tmp=$this->_tpl_vars['f']['network'])) ? $this->_run_mod_handler('get_plugin_path', true, $_tmp) : smarty_modifier_get_plugin_path($_tmp)); ?>
/assets/img/favicon.png" class="service-icon"/>
      <?php if ($this->_tpl_vars['f']['network'] == 'twitter'): ?></a><?php endif; ?>
    </div>
  </div>
  <div class="grid_4 small">
    <?php if ($this->_tpl_vars['f']['network'] == 'twitter'): ?><a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['f']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['f']['user_name']; ?>
 on Twitter"><?php endif; ?>
    <?php echo $this->_tpl_vars['f']['user_name']; ?>

    <?php if ($this->_tpl_vars['f']['network'] == 'twitter'): ?></a><?php endif; ?>
    <div class="small gray">
      <?php if ($this->_tpl_vars['f']['follower_count'] > 0 && $this->_tpl_vars['f']['friend_count'] > 0): ?>
      <?php echo ((is_array($_tmp=$this->_tpl_vars['f']['follower_count'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 followers, <?php echo ((is_array($_tmp=$this->_tpl_vars['f']['friend_count'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 friends<br>
      <?php endif; ?>
      <?php if ($this->_tpl_vars['f']['network'] == 'twitter'): ?><a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['f']['user_id']; ?>
" title="<?php echo $this->_tpl_vars['f']['user_name']; ?>
 on Twitter"><span class="sprite ui-icon-person"></span></a><?php endif; ?>
    </div>
  </div>
  <div class="grid_12 omega">
    <?php if ($this->_tpl_vars['f']['description']): ?><p><?php echo $this->_tpl_vars['f']['description']; ?>
</p><?php else: ?>&#160;<?php endif; ?>
    <span class="small gray">
      <?php if ($this->_tpl_vars['f']['location']): ?><?php echo $this->_tpl_vars['f']['location']; ?>
<?php endif; ?>
      <?php if ($this->_tpl_vars['f']['avg_tweets_per_day'] > 0): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['f']['avg_tweets_per_day'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
 posts per day over the past <?php echo ((is_array($_tmp=$this->_tpl_vars['f']['joined'])) ? $this->_run_mod_handler('relative_datetime', true, $_tmp) : smarty_modifier_relative_datetime($_tmp)); ?>
<?php endif; ?>
      <?php if ($this->_tpl_vars['f']['follower_count'] > $this->_tpl_vars['f']['friend_count'] && $this->_tpl_vars['f']['friend_count'] > 0): ?>
        <?php $this->assign('follower', ($this->_tpl_vars['f']['follower_count']/$this->_tpl_vars['f']['friend_count'])); ?>
        <br><?php echo ((is_array($_tmp=$this->_tpl_vars['follower'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>
x more followers than friends
      <?php endif; ?>
    </span>
  </div>
</div>