<?php if( count($errors) > 0 ) { ?>
	<ul>
	<?php foreach($errors as $control => $control_errors) { ?>
		<li><?php echo $control; ?>:<br />
			<ul>
			<?php foreach( $control_errors as $the_error ) { ?>
				<li><?php echo $the_error; ?></li>
			<?php } ?>
			</ul>
		</li>
	<?php } ?>
	</ul>
<?php } ?>
<?php echo $start; ?>
	<table>
		<tr>
			<th>Login:</th>
			<td><?php echo $items['login']; ?></td>
		</tr>
		<tr>
			<th>Password:</th>
			<td><?php echo $items['password']; ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $items['submit']; ?></td>
		</tr>
	</table>
	<?php echo $items['token']; ?>
	<?php echo $html; ?>
<?php echo $end; ?>