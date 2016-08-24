<?php setTitle("Grid test"); ?>
<?php echo $grid->getNavMsg(); ?><br /><br />
<?php echo $grid; ?><br />
<?php echo $grid->getNavLinks(); ?>
<?php echo link_to_action("Return", "index"); ?>