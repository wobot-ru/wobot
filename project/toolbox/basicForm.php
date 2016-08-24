<?php
require("_common.php");
$errors = array();
$valid = false;

$fname = new Gecko_Form_Field_Text("firstname");
$fname->addValidator(new Gecko_Form_Validator_NotEmpty());


$lname = new Gecko_Form_Field_Text("lastname");
$lname->addValidator(new Gecko_Form_Validator_NotEmpty());
if(strtolower($_SERVER['REQUEST_METHOD']) == "post") {
	$fnameValue = (isset($_POST['fields']['firstname']) ? $_POST['fields']['firstname'] : "");
	$lnameValue = (isset($_POST['fields']['lastname']) ? $_POST['fields']['lastname'] : "");
	$fname->setValue($fnameValue);
	$lname->setValue($lnameValue);
	$result = $fname->isValid();
	if(!$result->getResult()) {
		$fErrors = $result->getMessages();
		$errors[] = "First Name: " . implode("<br />", $fErrors);
	}
	$result = $lname->isValid();
	if(!$result->getResult()) {
		$fErrors = $result->getMessages();
		$errors[] = "Last Name: " . implode("<br />", $fErrors);
	}

	if( count($errors) <= 0 ) {
		$valid = true;
	}
}

$submit = new Gecko_Form_Field_Submit("submit", "Save");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Form Basic Test</title>
	</head>
	<body>
		<?php if(!$valid) { ?>
			<?php if(count($errors) > 0) { ?>
				<ul>
				<?php foreach($errors as $error) { ?>
					<li><?php echo $error; ?></li>
				<?php } ?>
				</ul>
			<?php } ?>
			<form action="<?php echo Gecko_URL::getSelfURI(); ?>" method="post">
				<table>
					<tr>
						<th>First Name</th>
						<td><?php echo $fname; ?></td>
					</tr>
					<tr>
						<th>Last Name</th>
						<td><?php echo $lname; ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $submit; ?></td>
					</tr>
				</table>
			</form>
		<?php } else { ?>
			<p>Success!</p>
			<p>First Name: <?php echo $fname->getValue(); ?></p>
			<p>Last Name: <?php echo $lname->getValue(); ?></p>
		<?php } ?>
	</body>
</html>