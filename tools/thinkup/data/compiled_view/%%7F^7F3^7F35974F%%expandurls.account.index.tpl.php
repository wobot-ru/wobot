<?php /* Smarty version 2.6.26, created on 2012-03-27 18:35:27
         compiled from /var/www/tools/thinkup/plugins/expandurls/view/expandurls.account.index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'help_link', '/var/www/tools/thinkup/plugins/expandurls/view/expandurls.account.index.tpl', 4, false),)), $this); ?>
<div class="append_20">

<div class="alert helpful">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'help_link', 'id' => 'expandurls')), $this); ?>

    <h2>Expand URLs Plugin</h2>
    <p>Expands shortened links, gathers link image thumbnails, and captures link clickthrough rates.</p><br>
    <p><strong>Important</strong>: To capture clickthrough rates, enter your Bitly API credentials in the Settings area below, and shorten URLs in your posts using Bitly.</p>
</div>

<div id="contact-admin-div" style="display: none; margin-top: 20px;">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_plugin.admin-request.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<br><br>

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
<?php endif; ?>
<?php echo $this->_tpl_vars['options_markup']; ?>

</p>
</div>
<?php endif; ?>