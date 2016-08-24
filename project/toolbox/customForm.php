<?php
require( "_common.php" );

class LoginForm extends Gecko_Form {
	protected function init() {
		$this->setFormDecorator(new Gecko_Form_Decorator_Simple());

		$login = Gecko_Form::fieldFactory("text", "login");
		$login->addValidator( self::validatorFactory("notEmpty") );

		$password = self::fieldFactory( "password", "password" );
		$password->addValidator( self::validatorFactory("notEmpty") );

		$submit = self::fieldFactory( "submit", "submit", "Login" );

		$hash = self::fieldFactory("hash", "token");

		$this->addField( $login );
		$this->addField( $password );
		$this->addField( $submit );
		$this->addField( $hash );
	}

	protected function getFormRenderer() {
		$renderer = new Gecko_Form_Renderer_Template( "template/LoginForm.php" );
		$renderer->setSeparateErrors(true);

		return $renderer;
	}
}

$login = new LoginForm();

if( $login->isValid() ) {
    $data = $login->getData();
    // Validate data and login user and redirect
    var_dump( $data );
    die( "Valid!! ");
}
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
		<?php echo $login->buildForm(); ?>
	</body>
</html>