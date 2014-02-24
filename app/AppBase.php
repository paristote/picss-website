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
		$dbuser=$f3->get('dbuser');
		$dbpass=$f3->get('dbpass');
		$dbhost=$f3->get('dbhost');
		$dbname=$f3->get('dbname');
		$connect=$dbhost."/".$dbname;
		if (!empty($dbuser) && !empty($dbpass)) {
			$connect = $dbuser.":".$dbpass."@".$connect;
		}
		$db=new DB\Mongo('mongodb://'.$connect,$dbname);
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
