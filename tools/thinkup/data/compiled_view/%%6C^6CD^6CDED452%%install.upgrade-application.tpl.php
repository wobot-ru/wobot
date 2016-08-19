<?php /* Smarty version 2.6.26, created on 2012-05-03 17:20:14
         compiled from install.upgrade-application.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_statusbar.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="container_24 thinkup-canvas round-all clearfix" style="margin-top : 10px;">
    
   <div class="prepend_20">
    <h1>Upgrade Your ThinkUp Application</h1>
  </div>
    
    <div class="clearfix prepend_20 append_20">

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <?php if ($this->_tpl_vars['show_try_again_button']): ?>
    <br>
    <div>
        <a href="upgrade-application.php" class="linkbutton emphasized">Try Again</a></div><br><br>
    </div>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['updateable']): ?> 
     <div class="alert helpful">
         <p>
           <span class="ui-icon ui-icon-check" style="float: left; margin:.3em 0.3em 0 0;"></span>
           Ready to upgrade ThinkUp to version <?php echo $this->_tpl_vars['latest_version']; ?>
.
         </p>
    </div>
    <br>
    <div>
        <p>
        <a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
install/upgrade-application.php?run_update=1" onclick="$('#update-spinner').show();" class="linkbutton emphasized">Upgrade ThinkUp</a>
        </p>
        <p id="update-spinner" style="text-align: center; display: none;">
            <img src="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/img/loading.gif" width="50" height="50" />
        </p>
    </div>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['updated']): ?>
     <div class="alert helpful">
         <p>
           <span class="ui-icon ui-icon-check" style="float: left; margin:.3em 0.3em 0 0;"></span>
           Success! You're running the latest version of ThinkUp.
         </p>
     </div>
     <br>
        <div>
            <p><a href="<?php echo $this->_tpl_vars['site_root_path']; ?>
install/upgrade-database.php" class="linkbutton emphasized">Upgrade ThinkUp's database</a></p>
        </div>
    <?php endif; ?>
    </div>
    </div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>