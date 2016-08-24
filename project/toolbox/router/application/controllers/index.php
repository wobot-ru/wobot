<?php
import("com.test.Common");

class indexController extends Gecko_Controller {
	public function __construct() {
	}

	public function indexAction($request) {
		$common = new Common("Hi! You");
		$this->message = $common;
	}

	public function gridAction($request) {
		$db = Gecko_DB::getInstance();
		$query = "SELECT * FROM `users`";
		$model = new Gecko_DataSource_Table_SQL($query, $db);

		$settings = array(
			"paginate" => 3,
			"sorting" => array(
				"sortColumn" => "id",
				"sortOrder" => "ASC"
			)
		);

		$grid = new Gecko_DataGrid("users", $model, $settings);
		$grid->buildTable();
		$this->grid = $grid;
	}

	public function formAction($request) {
		$valid = false;
		$fname = new Gecko_Form_Field_Text("firstname");
		$fname->addValidator(new Gecko_Form_Validator_NotEmpty(), true)->addValidator(new Zend_Validate_StringLength(8));
		$lname = new Gecko_Form_Field_Text("lastname");
		$lname->addValidator(new Gecko_Form_Validator_NotEmpty());
		$submit = new Gecko_Form_Field_Submit("submit", "Save");

		$form = new Gecko_Form(array("name" => "person"));
		$form->addField($fname);
		$form->addField($lname);
		$form->addField($submit);

		if($form->isValid()) {
			$fields = $form->getData();
			$valid = true;
			$this->fields = $fields;
		}

		$renderer = new Gecko_Form_Renderer_View();
		$form->buildForm($renderer);
		$this->formVars = $renderer->getFormVars();

		$this->valid = $valid;
	}
}
?>