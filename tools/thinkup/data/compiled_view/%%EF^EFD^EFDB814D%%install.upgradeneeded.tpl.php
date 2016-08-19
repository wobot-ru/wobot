<?php /* Smarty version 2.6.26, created on 2012-05-03 17:21:05
         compiled from install.upgradeneeded.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_install.header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<!-- end #menu-bar --></div>
<!-- end .container -->
<div class="container_24 thinkup-canvas clearfix">
<div class="grid_22 prefix_1 alpha omega prepend_20 append_20 clearfix">
<div class="alert urgent" style="margin: 20px 0px; padding: 0.5em 0.7em;">
<!--  we are upgrading -->
<p>
<?php if ($this->_tpl_vars['user_is_admin']): ?>
ThinkUp's database needs an update. <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
install/upgrade-database.php">Update now</a>.
<?php else: ?>
ThinkUp is currently in the process of upgrading. Please try back again in a little while.<br /><br />
If you are the administrator of this ThinkUp installation, check your email to complete the upgrade process.<br />
(<a href="http://thinkupapp.com/docs/troubleshoot/messages/upgrading.html">What? Help!</a>)

<p>
<form method="get" action="<?php echo $this->_tpl_vars['site_root_path']; ?>
install/upgrade-database.php" style="margin-top: 20px">
<p>If you have an
<a href="http://thinkupapp.com/docs/troubleshoot/messages/upgrading.html">
upgrade token</a>, you can enter it here:
<input type="text" name="upgrade_token" />
<input type="submit" value="Submit Token" />
</form>
</p>

<?php endif; ?>
</p>
</div>
</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_install.footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>