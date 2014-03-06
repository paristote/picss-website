<?php

/*
 * The home page controller
 * Show all Picss in a single page
 */
class Admin extends AdminBase {

	/*
	 * Called when / is requested with GET
	 */
	function main($f3) {

		$f3->set('pageTitle', ":Picss: Admin Dashboard");
	}

	/*
	 * Called when /picss is requested with GET
	 */
	function picss($f3, $args) {

		$picss=new DB\Mongo\Mapper($this->db,'picss');

		$f3->set('pageTitle', ":Picss: All Picss");
		$f3->set('content','admpicss.htm');

		// always find and display the list of 25 latest Picss
		$items = $picss->paginate(0, 25, NULL, array('order' => array('date' => -1)));
		$f3->set('items', $items['subset']);

		if (empty($args['pid']))
		{
			// TODO handle pagination only when we don't display a specific picss
		}
		else
		{
			// specific picss part
			$pid = $args['pid'];
			$picss->load(array('_id' => new MongoId($pid)));
			if (!$picss->dry()) // if the Picss object exists
			{
				$f3->set('picss', $picss);
			}
		}

	}

	function deletePicss($f3, $args) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		$pid = $args['pid'];
		$picss->load(array('_id' => new MongoId($pid)));
		if (!$picss->dry()) {
			$picss->erase();
			$message = "Picss erased succesfully.";
		}

		$f3->reroute('/aaa/picss/');

	}

	function hidePicss($f3, $args) {

	}

	function users($f3, $args) {

		$users=new DB\Mongo\Mapper($this->db,'betausers');

		$f3->set('pageTitle', ":Picss: All Users");
		$f3->set('content','admusers.htm');

		// always find and display the list of 25 latest Picss
		$items = $users->paginate(0, 25, NULL, array('order' => array('date' => -1)));
		$f3->set('items', $items['subset']);

		if (empty($args['uid']))
		{
			// TODO handle pagination only when we don't display a specific user
		}
		else
		{
			// specific user part
			$uid = $args['uid'];
			$users->load(array('_id' => new MongoId($uid)));
			if (!$users->dry()) // if the Picss object exists
			{
				$f3->set('users', $users);
			}
		}

	}

}

?>