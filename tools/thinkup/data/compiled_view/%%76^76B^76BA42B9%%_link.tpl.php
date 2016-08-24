<?php /* Smarty version 2.6.26, created on 2012-03-27 18:45:01
         compiled from _link.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'get_plugin_path', '_link.tpl', 12, false),array('modifier', 'filter_xss', '_link.tpl', 29, false),array('modifier', 'link_usernames', '_link.tpl', 29, false),array('modifier', 'relative_datetime', '_link.tpl', 38, false),)), $this); ?>
<?php if (($this->_foreach['foo']['iteration'] <= 1)): ?>
  <div class="header clearfix">
    <div class="grid_1 alpha">&nbsp;</div>
    <div class="grid_3 right">name</div>
    <div class="grid_13">post</div>
  </div>
<?php endif; ?>

<div class="individual-tweet post clearfix article">
  <div class="grid_1 alpha">
    <a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['l']->container_post->author_user_id; ?>
">
    <img src="<?php echo $this->_tpl_vars['l']->container_post->author_avatar; ?>
" class="avatar"/><img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
plugins/<?php echo ((is_array($_tmp=$this->_tpl_vars['l']->container_post->network)) ? $this->_run_mod_handler('get_plugin_path', true, $_tmp) : smarty_modifier_get_plugin_path($_tmp)); ?>
/assets/img/favicon.png" class="service-icon"/></a>
  </div>
  <div class="grid_3 right small">
    <a href="https://twitter.com/intent/user?user_id=<?php echo $this->_tpl_vars['l']->container_post->author_user_id; ?>
"><?php echo $this->_tpl_vars['l']->container_post->author_username; ?>
</a>
  </div>
  <div class="grid_13">
    <?php if ($this->_tpl_vars['l']->image_src): ?>
      <a href="<?php echo $this->_tpl_vars['l']->url; ?>
"><div class="pic"><img src="<?php echo $this->_tpl_vars['l']->image_src; ?>
" /></div></a>
    <?php else: ?>
      <?php if ($this->_tpl_vars['l']->expanded_url): ?>
      <small>
        <a href="<?php echo $this->_tpl_vars['l']->expanded_url; ?>
" title="<?php echo $this->_tpl_vars['l']->expanded_url; ?>
"><?php if ($this->_tpl_vars['l']->title): ?><?php echo $this->_tpl_vars['l']->title; ?>
<?php else: ?><?php echo $this->_tpl_vars['l']->expanded_url; ?>
<?php endif; ?></a>
      </small>
      <?php endif; ?>
    <?php endif; ?>
    <div class="post">
      <?php if ($this->_tpl_vars['l']->container_post->post_text): ?>
        <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['l']->container_post->post_text)) ? $this->_run_mod_handler('filter_xss', true, $_tmp) : smarty_modifier_filter_xss($_tmp)))) ? $this->_run_mod_handler('link_usernames', true, $_tmp, $this->_tpl_vars['i']->network_username, $this->_tpl_vars['t']->network) : smarty_modifier_link_usernames($_tmp, $this->_tpl_vars['i']->network_username, $this->_tpl_vars['t']->network)); ?>

      <?php else: ?>
        <span class="no-post-text">No post text</span>
      <?php endif; ?>
      <?php if ($this->_tpl_vars['l']->container_post->in_reply_to_post_id): ?>
        [<a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
post/?t=<?php echo $this->_tpl_vars['t']->in_reply_to_post_id; ?>
&n=<?php echo $this->_tpl_vars['t']->network; ?>
">in reply to</a>]
      <?php endif; ?>
      <div class="small gray">
      <span class="metaroll">
      <a href="http://twitter.com/<?php echo $this->_tpl_vars['l']->container_post->author_username; ?>
/status/<?php echo $this->_tpl_vars['l']->container_post->post_id; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['l']->container_post->adj_pub_date)) ? $this->_run_mod_handler('relative_datetime', true, $_tmp) : smarty_modifier_relative_datetime($_tmp)); ?>
</a>
       <?php echo $this->_tpl_vars['l']->container_post->location; ?>
</span>&nbsp;</div>
  </div>
  </div>
</div>