<?php /* Smarty version 2.6.26, created on 2012-03-27 18:24:44
         compiled from /var/www/tools/thinkup/plugins/googleplus/view/googleplus.account.index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'help_link', '/var/www/tools/thinkup/plugins/googleplus/view/googleplus.account.index.tpl', 4, false),array('insert', 'csrf_token', '/var/www/tools/thinkup/plugins/googleplus/view/googleplus.account.index.tpl', 43, false),array('modifier', 'urlencode', '/var/www/tools/thinkup/plugins/googleplus/view/googleplus.account.index.tpl', 33, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
<div class="append_20 alert helpful">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'help_link', 'id' => 'googleplus')), $this); ?>

    <h2>Google+ Plugin</h2>
    
    <div class="">
    <p>The Google+ plugin collects posts, reply counts, and +1 counts from Google+ for an authorized user. <i>Note:</i> The Google+ API is in its early stages and its capabilities are limited.</p>
    
    </div>
    

</div>


<div class="append_20">

<?php if ($this->_tpl_vars['oauth_link']): ?>
<br>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array('field' => 'authorization')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<a href="<?php echo $this->_tpl_vars['oauth_link']; ?>
" class="linkbutton emphasized">Add a Google+ User</a>
<div style="clear:all">&nbsp;<br><br><br></div>
<?php endif; ?>

    <?php if (count ( $this->_tpl_vars['owner_instances'] ) > 0): ?>
    <h2 >Google+ Accounts</h2>

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array('field' => 'user_add')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
    <?php $_from = $this->_tpl_vars['owner_instances']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['iid'] => $this->_tpl_vars['i']):
        $this->_foreach['foo']['iteration']++;
?>
    <div class="clearfix">
        <div class="grid_4 right" style="padding-top:.5em;">
            <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
?u=<?php echo ((is_array($_tmp=$this->_tpl_vars['i']->network_username)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
&n=<?php echo ((is_array($_tmp=$this->_tpl_vars['i']->network)) ? $this->_run_mod_handler('urlencode', true, $_tmp) : urlencode($_tmp)); ?>
"><?php echo $this->_tpl_vars['i']->network_username; ?>
</a> 
        </div>
        <div class="grid_4 right">
            <span id="div<?php echo $this->_tpl_vars['i']->id; ?>
"><input type="submit" name="submit" id="<?php echo $this->_tpl_vars['i']->id; ?>
" class="linkbutton <?php if ($this->_tpl_vars['i']->is_public): ?>btnPriv<?php else: ?>btnPub<?php endif; ?>" value="<?php if ($this->_tpl_vars['i']->is_public): ?>set private<?php else: ?>set public<?php endif; ?>" /></span>
        </div>
        <div class="grid_4 right">
            <span id="divactivate<?php echo $this->_tpl_vars['i']->id; ?>
"><input type="submit" name="submit" id="<?php echo $this->_tpl_vars['i']->id; ?>
" class="linkbutton <?php if ($this->_tpl_vars['i']->is_active): ?>btnPause<?php else: ?>btnPlay<?php endif; ?>" value="<?php if ($this->_tpl_vars['i']->is_active): ?>pause crawling<?php else: ?>start crawling<?php endif; ?>" /></span>
        </div>
        <div class="grid_8 right">
            <span id="delete<?php echo $this->_tpl_vars['i']->id; ?>
"><form method="post" action="<?php echo $this->_tpl_vars['site_root_path']; ?>
account/?p=google%2B"><input type="hidden" name="instance_id" value="<?php echo $this->_tpl_vars['i']->id; ?>
">
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'csrf_token')), $this); ?>
<!-- delete account csrf token -->
            <input onClick="return confirm('Do you really want to delete this Google+ account from ThinkUp?');"  type="submit" name="action" class="linkbutton" value="delete" /></form></span>
        </div>
    </div>
    <?php endforeach; endif; unset($_from); ?>
    <br />
    <?php endif; ?>

<div id="contact-admin-div" style="display: none;">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_plugin.admin-request.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>


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
<p style="padding:5px">To set up the Google+ plugin:</p>
<ol style="margin-left:40px">
<li><a href="http://code.google.com/apis/console#access" target="_blank" style="text-decoration : underline;">Create a project in the Google APIs Console.</a></li>
<li>Click "Services" and switch Google+ API to "On." Next, click "API Access" then "Create an OAuth 2.0 client ID."</li>
<li>
  Edit the settings for your new Client ID then click "Next." Make sure "Application Type" is set to "Web Application" and set the first line of Authorized Redirect URIs to<br> 
    <small>
      <code style="font-family:Courier;" id="clippy_2988"><?php echo $this->_tpl_vars['thinkup_site_url']; ?>
account/?p=google%2B</code>
    </small>
    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
              width="100"
              height="14"
              class="clippy"
              id="clippy" >
      <param name="movie" value="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/flash/clippy.swf"/>
      <param name="allowScriptAccess" value="always" />
      <param name="quality" value="high" />
      <param name="scale" value="noscale" />
      <param NAME="FlashVars" value="id=clippy_2988&amp;copied=copied!&amp;copyto=copy to clipboard">
      <param name="bgcolor" value="#FFFFFF">
      <param name="wmode" value="opaque">
      <embed src="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/flash/clippy.swf"
             width="100"
             height="14"
             name="clippy"
             quality="high"
             allowScriptAccess="always"
             type="application/x-shockwave-flash"
             pluginspage="http://www.macromedia.com/go/getflashplayer"
             FlashVars="id=clippy_2988&amp;copied=copied!&amp;copyto=copy to clipboard"
             bgcolor="#FFFFFF"
             wmode="opaque"
      />
    </object>
</li>
<li>Enter the Google-provided Client ID and Client Secret here.</li></ol>
<?php endif; ?>

<?php if ($this->_tpl_vars['options_markup']): ?>
<p>
<?php echo $this->_tpl_vars['options_markup']; ?>

</p>
<?php endif; ?>

<?php if ($this->_tpl_vars['user_is_admin']): ?></div><?php endif; ?>

<?php echo '
<script type="text/javascript">
if( required_values_set ) {
    $(\'#add-account-div\').show();
} else {
    if(! is_admin) {
        $(\'#contact-admin-div\').show();
    }
}
'; ?>

</script>