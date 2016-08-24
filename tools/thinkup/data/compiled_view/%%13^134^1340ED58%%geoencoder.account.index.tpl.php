<?php /* Smarty version 2.6.26, created on 2012-03-27 18:17:53
         compiled from /var/www/tools/thinkup/plugins/geoencoder/view/geoencoder.account.index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'help_link', '/var/www/tools/thinkup/plugins/geoencoder/view/geoencoder.account.index.tpl', 4, false),)), $this); ?>

<div class="append_20 alert helpful">

<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'help_link', 'id' => 'geoencoder')), $this); ?>

<h2>GeoEncoder Plugin</h2>

<p>
The GeoEncoder plugin plots a post's responses on a Google Map and can lists them by distance from the original poster.
</p>

</div>

<div class="append_20">

<div id="contact-admin-div" style="display: none; margin-top: 20px;">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_plugin.admin-request.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<?php if ($this->_tpl_vars['options_markup']): ?>
<?php if ($this->_tpl_vars['user_is_admin']): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_plugin.showhider.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array('field' => 'setup')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<p style="padding:5px">To set up the GeoEncoder plugin:</p>
<ol style="margin-left:40px">
<li><a href="http://code.google.com/apis/console#access" target="_blank" style="text-decoration : underline;">Create a project in the Google APIs Console.</a></li>
<li>Click "Services" and switch Google Maps API v2 to "On." </li>
<li>Click "API Access." Under "Simple API Access", copy and paste the Google-provided API key here.</li>
</ol>
<?php endif; ?>
<p>
<?php echo $this->_tpl_vars['options_markup']; ?>

<p>
<?php echo '
<script type="text/javascript">
if( ! required_values_set && ! is_admin) {
    $(\'#contact-admin-div\').show();
}
'; ?>

</script>
<?php endif; ?>
</div>
<br>