<?php /* Smarty version 2.6.26, created on 2012-03-27 18:45:01
         compiled from /var/www/tools/thinkup/plugins/twitter/view/links.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/var/www/tools/thinkup/plugins/twitter/view/links.tpl', 1, false),)), $this); ?>
<?php if (count($this->_tpl_vars['linksinfaves']) > 1): ?>
<div class="section">
    <h2>Links in Favorites</h2>
    <?php $_from = $this->_tpl_vars['linksinfaves']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['l']):
        $this->_foreach['foo']['iteration']++;
?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_link.tpl", 'smarty_include_vars' => array('t' => $this->_tpl_vars['f'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endforeach; endif; unset($_from); ?>
    <div class="view-all"><a href="?v=links-favorites&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=twitter">More...</a></div>
</div>
<?php endif; ?>

<?php if (count($this->_tpl_vars['linksbyfriends']) > 1): ?>
<div class="section">
    <h2>Links by Friends</h2>
    <?php $_from = $this->_tpl_vars['linksbyfriends']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['l']):
        $this->_foreach['foo']['iteration']++;
?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_link.tpl", 'smarty_include_vars' => array('t' => $this->_tpl_vars['f'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endforeach; endif; unset($_from); ?>
    <div class="view-all"><a href="?v=links-friends&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=twitter">More...</a></div>
</div>
<?php endif; ?>

<?php if (count($this->_tpl_vars['photosbyfriends']) > 1): ?>

<div class="section">
    <h2>Photos by Friends</h2>
    <?php $_from = $this->_tpl_vars['photosbyfriends']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['tid'] => $this->_tpl_vars['l']):
        $this->_foreach['foo']['iteration']++;
?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_link.tpl", 'smarty_include_vars' => array('t' => $this->_tpl_vars['f'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endforeach; endif; unset($_from); ?>
    <div class="view-all"><a href="?v=links-photos&u=<?php echo $this->_tpl_vars['instance']->network_username; ?>
&n=twitter">More...</a></div>
</div>
<?php endif; ?>

<?php if (count($this->_tpl_vars['linksinfaves']) < 1 && count($this->_tpl_vars['linksbyfriends']) < 1 && count($this->_tpl_vars['photosbyfriends']) < 1): ?>
    <div class="alert urgent">No posts to display. <?php if ($this->_tpl_vars['logged_in_user']): ?>Update your data and try again.<?php endif; ?></div>
<?php endif; ?>