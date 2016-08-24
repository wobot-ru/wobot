<?php
require("_common.php");
$errors = array();
$valid = false;

$fname = new Gecko_Form_Field_Text("firstname");
$fname->addValidator(new Gecko_Form_Validator_NotEmpty());
$lname = new Gecko_Form_Field_Text("lastname");
$lname->addValidator(new Gecko_Form_Validator_NotEmpty());
$submit = new Gecko_Form_Field_Submit("submit", "Save");

$form = new Gecko_Form(array("name" => "person"));
$form->addField($fname);
$form->addField($lname);
$form->addField($submit);

if($form->isValid()) {
	$fields = $form->getData();
}

$renderer = new Gecko_Form_Renderer_View();
$form->buildForm($renderer);
$formVars = $renderer->getFormVars();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Form Advanced Test</title>
		<script type="text/javascript" src="/library/Gecko/Assets/JavaScriptHelpers/prototype.js"></script>
		<script type="text/javascript" src="/library/Gecko/Assets/JavaScriptHelpers/GeckoValidator.js"></script>
	</head>
	<body>
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
	</body>
</html>