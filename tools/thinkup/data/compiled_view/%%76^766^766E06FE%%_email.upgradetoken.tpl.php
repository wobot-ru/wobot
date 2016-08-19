<?php /* Smarty version 2.6.26, created on 2012-05-03 17:21:05
         compiled from _email.upgradetoken.tpl */ ?>
Looks like you're upgrading your ThinkUp installation. Great! To complete the process, click on this link to apply new database updates:

http<?php if ($_SERVER['HTTPS']): ?>s<?php endif; ?>://<?php echo $this->_tpl_vars['server']; ?>
<?php echo $this->_tpl_vars['site_root_path']; ?>
install/upgrade-database.php?upgrade_token=<?php echo $this->_tpl_vars['token']; ?>
 

If you have trouble, get in touch with the ThinkUp community on the mailing list:

http://groups.google.com/group/thinkupapp

Or drop by our IRC channel #thinkup on irc.freenode.net.


Thanks for using ThinkUp.

http://thinkupapp.com