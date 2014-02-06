<?php

/*
 * The home page controller
 * Show all Picss in a single page
 */
class Home extends AppBase {

	/*
	 * Called when / is requested with GET
	 */
	function showIndex($f3) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		// find all Picss in the database
		$items = $picss->find();
		$f3->set('items', $items);
		$f3->set('pageTitle', "Picss :: Simple pictures with sounds");
		$f3->set('content', 'allpicss.htm');
	}

}

?>