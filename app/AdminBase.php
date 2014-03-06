<?php

/*
 * The Admin Base page controller
 */
class AdminBase extends AppBase {

	/*
	 * Called before transferring control to the method specified in the route
	 * Set the default layout, that can be changed in children classes
	 */
	function beforeRoute($f3) {

		// create DBs for picss and betausers
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		$users=new DB\Mongo\Mapper($this->db,'betausers');

		// redirect to /login if the session doesn't exist
		if ($f3->get('SESSION.auth') !== TRUE) {
			$f3->reroute('/login');
		}	

		// find 5 latest Picss
		$latestPicss = $picss->paginate(0, 5, NULL, array('order' => array('date' => -1)));
		$f3->set('latestPicss', $latestPicss['subset']);

		// find 5 latest registered beta users
		$latestBetaUsers = $users->paginate(0, 5, NULL, array('order' => array('date' => -1)));
		$f3->set('latestBetaUsers', $latestBetaUsers['subset']);

		$f3->set('layout','admlayout.htm');
		$f3->set('content','admdashboard.htm');
		$f3->set('format','text/html');
	}

}

?>