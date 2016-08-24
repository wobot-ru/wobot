<?php setTitle( "Bienvenido" ); ?>
<div align="center">
	<?php sayHi(); ?><br />
	<?php echo $message; ?><br />
	<p>Choose a Test:</p>
	<ul>
		<li><?php echo link_to_action("Full DataGrid", "grid"); ?></li>
		<li><?php echo link_to_action("Form", "form"); ?></li>
		<li><a href="/test/">Return</a></li>
	</ul>
</div>