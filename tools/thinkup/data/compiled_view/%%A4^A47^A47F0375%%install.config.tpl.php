<?php /* Smarty version 2.6.26, created on 2012-03-27 14:02:19
         compiled from install.config.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_install.header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <div id="installer-die" class="container_24 round-all">
    <div class="clearfix prepend_20 append_20">
      <div class="grid_22 push_1 clearfix">
       <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_usermessage.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

        <textarea style="width : 100%; margin-bottom : 30px;" rows="25"><?php echo $this->_tpl_vars['config_file_contents']; ?>
</textarea><br>
        
        <form name="form1" class="input" method="post" action="index.php?step=3">
        <?php $_from = $this->_tpl_vars['_POST']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
           <input type="hidden" name="<?php echo $this->_tpl_vars['k']; ?>
" value="<?php echo $this->_tpl_vars['v']; ?>
" />
        <?php endforeach; endif; unset($_from); ?>
        <div class="clearfix append_20">
        <div class="grid_10 prefix_7 left">
        <input type="submit" name="Submit" class="linkbutton ui-state-default ui-priority-secondary" value="Next Step &raquo">
        </div></div></form>
      </div>
    </div>
  </div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_install.footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>