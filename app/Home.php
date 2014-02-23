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
		$f3->set('pageTitle', ":Picss: Simple pictures with sounds");
		$f3->set('bgImage', 'mugs.jpg');
		$f3->set('pageCss', 'home.css');
		$f3->set('content', 'home.htm');
	}

	// function showIndex($f3, $args) {
	// 	$picss=new DB\Mongo\Mapper($this->db,'picss');
	// 	$p=empty($args['p'])?'1':$args['p'];
	// 	// find 6 Picss starting at page $p (default 0), ordered by date DESC, in the database
	// 	$items = $picss->paginate($p-1, 6, NULL, array('order' => array('date' => -1)));
	// 	$f3->set('items', $items['subset']);
	// 	$f3->set('pageTitle', "Picss :: Simple pictures with sounds");
	//  $f3->set('pageCss', 'allpicss.css');
	// 	$f3->set('content', 'allpicss.htm');
	// 	$pos = $items['pos']+1; // the page number
	// 	if ($pos >= 2) { // we can display the previous link
	// 		$f3->set('prev', $pos-1);
	// 	}
	// 	if ($pos < $items['count']) { // we can display the next link
	// 		$f3->set('next', $pos+1);	
	// 	}
	// }

}

?>