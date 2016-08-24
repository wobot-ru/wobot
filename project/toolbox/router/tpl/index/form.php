<?php setTitle("Form Test"); ?>
<?php if(!$valid) { ?>
	<?php echo $formVars['start']; ?>
		<table>
			<tr>
				<th>First Name</th>
				<td><?php echo $formVars['items']['firstname']; ?></td>
			</tr>
			<tr>
				<th>Last Name</th>
				<td><?php echo $formVars['items']['lastname']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $formVars['items']['submit']; ?></td>
			</tr>
		</table>
		<?php echo $formVars['html']; ?>
	<?php echo $formVars['end']; ?>
<?php } else { ?>
	<p>Success!</p>
	<p>First Name: <?php echo $fields['firstname'] ?></p>
	<p>Last Name: <?php echo $fields['lastname']; ?></p>
<?php } ?>
<?php echo link_to_action("Return", "index"); ?>