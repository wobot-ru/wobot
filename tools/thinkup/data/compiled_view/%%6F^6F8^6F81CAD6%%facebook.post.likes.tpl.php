<?php /* Smarty version 2.6.26, created on 2012-03-27 20:47:16
         compiled from /var/www/tools/thinkup/plugins/facebook/view/facebook.post.likes.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/var/www/tools/thinkup/plugins/facebook/view/facebook.post.likes.tpl', 9, false),array('modifier', 'relative_datetime', '/var/www/tools/thinkup/plugins/facebook/view/facebook.post.likes.tpl', 12, false),)), $this); ?>

          <?php if ($this->_tpl_vars['post']): ?>
            <div class="clearfix alert stats">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "post.index._top-post.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

              <div class="grid_6 center keystats omega">
                <div class="big-number">
                       <?php if ($this->_tpl_vars['likes']): ?>
                      <h1><?php echo count($this->_tpl_vars['likes']); ?>
</h2>
                      <h3>like<?php if (count($this->_tpl_vars['likes']) != 1): ?>s<?php endif; ?>
                    
                     in <?php echo ((is_array($_tmp=$this->_tpl_vars['post']->adj_pub_date)) ? $this->_run_mod_handler('relative_datetime', true, $_tmp) : smarty_modifier_relative_datetime($_tmp)); ?>
</h3>
                   <?php endif; ?> <!-- end if favds -->
                </div>
              </div>
            </div> <!-- /.clearfix -->
          <?php endif; ?> <!-- end if post -->


<?php if ($this->_tpl_vars['likes']): ?>
<div class="prepend">
  <div class="append_20 clearfix section">
      <h2>Likes</h2>
    <?php $_from = $this->_tpl_vars['likes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['fid'] => $this->_tpl_vars['f']):
        $this->_foreach['foo']['iteration']++;
?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_user.tpl", 'smarty_include_vars' => array('f' => $this->_tpl_vars['f'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <?php endforeach; endif; unset($_from); ?>
  </div>
</div>

<?php endif; ?>


<script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/js/linkify.js"></script>
<?php if ($this->_tpl_vars['is_searchable']): ?>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_grid.search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <script type="text/javascript" src="<?php echo $this->_tpl_vars['site_root_path']; ?>
assets/js/grid_search.js"></script>
    
<?php endif; ?>
