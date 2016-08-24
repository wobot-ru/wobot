<?php /* Smarty version 2.6.26, created on 2012-03-27 18:08:38
         compiled from /var/www/tools/thinkup/plugins/facebook/view/facebook.account.index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'help_link', '/var/www/tools/thinkup/plugins/facebook/view/facebook.account.index.tpl', 5, false),array('insert', 'csrf_token', '/var/www/tools/thinkup/plugins/facebook/view/facebook.account.index.tpl', 46, false),array('modifier', 'urlencode', '/var/www/tools/thinkup/plugins/facebook/view/facebook.account.index.tpl', 36, false),array('modifier', 'substr', '/var/www/tools/thinkup/plugins/facebook/view/facebook.account.index.tpl', 101, false),)), $this); ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
<div class="append_20 alert helpful">
    <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'help_link', 'id' => 'facebook')), $this); ?>

    <h2>Facebook Plugin</h2>
    
    <div class="">
    <p>The Facebook plugin collects posts and status updates for Facebook users and the Facebook pages those users like and manage.</p>
    
    </div>
    

</div>


<div id="add-account-div" style="display: none; clear : left; margin-bottom : 35px;">
<br />
    <?php if ($this->_tpl_vars['fbconnect_link']): ?>
<br>
     <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array('field' => 'authorization')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<a href="<?php echo $this->_tpl_vars['fbconnect_link']; ?>
" class="linkbutton emphasized">Add a Facebook User</a>
    <?php endif; ?>
    
</div>

<?php if (count ( $this->_tpl_vars['owner_instances'] ) > 0): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array('field' => 'user_add')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>

<?php if (count ( $this->_tpl_vars['owner_instances'] ) > 0): ?>
<div class="section">
    <h2>Facebook User Profiles</h2>

    <?php $_from = $this->_tpl_vars['owner_instances']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['iid'] => $this->_tpl_vars['i']):
        $this->_foreach['foo']['iteration']++;
?>
    <div class="clearfix article">
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
account/?p=facebook"><input type="hidden" name="instance_id" value="<?php echo $this->_tpl_vars['i']->id; ?>
">
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'csrf_token')), $this); ?>
<!-- delete account csrf token -->
            <input onClick="return confirm('Do you really want to delete this Facebook account from ThinkUp?');"  type="submit" name="action" class="linkbutton" value="delete" /></form></span>
        </div>
    </div>
    <?php endforeach; endif; unset($_from); ?>
</div>

    <?php if (isset ( $this->_tpl_vars['owner_instance_pages'] ) && count ( $this->_tpl_vars['owner_instance_pages'] ) > 0): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array('field' => 'page_add')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>


    <?php if (isset ( $this->_tpl_vars['owner_instance_pages'] ) && count ( $this->_tpl_vars['owner_instance_pages'] ) > 0): ?>
<div class="section">
    <h2>Facebook Pages</h2>
    <div class="article">
    <?php $_from = $this->_tpl_vars['owner_instance_pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
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
"><input type="submit" name="submit" class="linkbutton <?php if ($this->_tpl_vars['i']->is_public): ?>btnPriv<?php else: ?>btnPub<?php endif; ?>" id="<?php echo $this->_tpl_vars['i']->id; ?>
" value="<?php if ($this->_tpl_vars['i']->is_public): ?>set private<?php else: ?>set public<?php endif; ?>" /></span>
        </div>
        <div class="grid_4 right">
            <span id="divactivate<?php echo $this->_tpl_vars['i']->id; ?>
"><input type="submit" name="submit" class="linkbutton <?php if ($this->_tpl_vars['i']->is_active): ?>btnPause<?php else: ?>btnPlay<?php endif; ?>" id="<?php echo $this->_tpl_vars['i']->id; ?>
" value="<?php if ($this->_tpl_vars['i']->is_active): ?>pause crawling<?php else: ?>start crawling<?php endif; ?>" /></span>
        </div>
        <div class="grid_8 right">
            <span id="delete<?php echo $this->_tpl_vars['i']->id; ?>
"><form method="post" action="<?php echo $this->_tpl_vars['site_root_path']; ?>
account/?p=facebook"><input type="hidden" name="instance_id" value="<?php echo $this->_tpl_vars['i']->id; ?>
">
            <?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'csrf_token')), $this); ?>
<!-- delete page csrf token -->
            <input onClick="return confirm('Do you really want to delete this page?');"  type="submit" name="action" class="linkbutton" value="delete" /></form></span>
        </div>

    </div><?php endforeach; endif; unset($_from); ?>
    </div>
</div>
    <?php endif; ?>

<div class="section">
<h2>Add a Facebook Page</h2>
<?php $_from = $this->_tpl_vars['owner_instances']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['iid'] => $this->_tpl_vars['i']):
        $this->_foreach['foo']['iteration']++;
?>
  <?php $this->assign('facebook_user_id', $this->_tpl_vars['i']->network_user_id); ?>
  <?php if ($this->_tpl_vars['user_pages'][$this->_tpl_vars['facebook_user_id']] || $this->_tpl_vars['user_admin_pages'][$this->_tpl_vars['facebook_user_id']]): ?>
    <div class="clearfix article">
        <div class="grid_4 right" style="padding-top:.5em;">
            <?php echo $this->_tpl_vars['i']->network_username; ?>
&nbsp;likes:
        </div>
        <form name="addpage" action="index.php?p=facebook">
        <div class="grid_8">
            <input type="hidden" name="instance_id" value="<?php echo $this->_tpl_vars['i']->id; ?>
">
            <input type="hidden" name="p" value="facebook">
            <input type="hidden" name ="viewer_id" value="<?php echo $this->_tpl_vars['i']->network_user_id; ?>
" />
            <input type="hidden" name ="owner_id" value="<?php echo $this->_tpl_vars['owner']->id; ?>
" />
            <select name="facebook_page_id">
                <?php if ($this->_tpl_vars['user_admin_pages'][$this->_tpl_vars['facebook_user_id']]): ?>
                    <optgroup label="Pages You Manage">
                        <?php $_from = $this->_tpl_vars['user_admin_pages'][$this->_tpl_vars['facebook_user_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['p'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['p']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['page_id'] => $this->_tpl_vars['page']):
        $this->_foreach['p']['iteration']++;
?>
                            <option value="<?php echo $this->_tpl_vars['page']->id; ?>
"><?php if (strlen ( $this->_tpl_vars['page']->name ) > 27): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['page']->name)) ? $this->_run_mod_handler('substr', true, $_tmp, 0, 27) : substr($_tmp, 0, 27)); ?>
...<?php else: ?><?php echo $this->_tpl_vars['page']->name; ?>
<?php endif; ?></option> <br />
                        <?php endforeach; endif; unset($_from); ?>
                    </optgroup>
                <?php endif; ?>
                <?php if ($this->_tpl_vars['user_pages'][$this->_tpl_vars['facebook_user_id']]): ?>
                    <optgroup label="Pages You Like">
                    <?php $_from = $this->_tpl_vars['user_pages'][$this->_tpl_vars['facebook_user_id']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['p'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['p']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['page_id'] => $this->_tpl_vars['page']):
        $this->_foreach['p']['iteration']++;
?>
                        <option value="<?php echo $this->_tpl_vars['page']->id; ?>
"><?php if (strlen ( $this->_tpl_vars['page']->name ) > 27): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['page']->name)) ? $this->_run_mod_handler('substr', true, $_tmp, 0, 27) : substr($_tmp, 0, 27)); ?>
...<?php else: ?><?php echo $this->_tpl_vars['page']->name; ?>
<?php endif; ?></option> <br />
                    <?php endforeach; endif; unset($_from); ?>
                    </optgroup>
                <?php endif; ?>
             </select>
        </div>
        <div class="grid_7">
             <span id="divaddpage<?php echo $this->_tpl_vars['i']->network_username; ?>
"><input type="submit" name="action" class="linkbutton
addPage"  id="<?php echo $this->_tpl_vars['i']->network_username; ?>
" value="add page" /></span>
        </div>
        </form>
    </div>
    <?php else: ?>
    <div class="article">
    To add a Facebook page to ThinkUp, create a new page on Facebook.com or "like" an existing one, and refresh this page.
    </div>
    <?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

</div>

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

<p style="padding:5px">To set up the Facebook plugin:</p>
<ol style="margin-left:40px">
<li><a href="https://developers.facebook.com/apps" target="_blank" style="text-decoration: underline;">Click the "Create New App" button on Facebook.</a></li>
<li>
    Fill in the following settings.<br />
    App Display Name: <span style="font-family:Courier;">ThinkUp</span><br />
    App Namespace: (leave blank)
</li>
<li>
  At the bottom of the page, click the "Website" section and add the Site URL:
    <small>
      <code style="font-family:Courier;" id="clippy_2988"><?php echo $this->_tpl_vars['thinkup_site_url']; ?>
</code>
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
<li>Enter the Facebook-provided App ID and App Secret here.</li>
</ol>

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