<?php

/*
 * The base controller of the application
 * Extend this class to define a concrete controller, for a specific page
 */
class AppBase {

	/*
	 * Holds the database connection. Use $this->db from children classes
	 */
	protected $db;

	/*
	 * Constructor
	 * Init the $f3 and the $db variables
	 */
	function __construct() {
		$f3=Base::instance();
		$db=new DB\Mongo('mongodb://uc2vqr8kdj1ptt9e:8c88422484594258899dd812da5b66e3@bgpm2hfn7h84i9dl.mongo.clvrcld.net','bgpm2hfn7h84i9dl');
		$this->db=$db;
	}

	/*
	 * Called before transferring control to the method specified in the route
	 * Set the default layout, that can be changed in children classes
	 */
	function beforeRoute($f3) {
		$f3->set('layout','layout.htm');
		$f3->set('format','text/html');
	}

	/*
	 * Called after transferring control to the method specified in the route
	 * Render the specified layout in the specified format (cf beforeRoute)
	 * Possible formats: text/html ; application/json
	 */
	function afterRoute($f3) {
		echo Template::instance()->render($f3->get('layout'), $f3->get('format'));
	}
}

?>
