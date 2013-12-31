<?php

// Picss controller
class Picss {

	public $db;

	//! Instantiate class
	function __construct() {
		$f3=Base::instance();
		// Connect to the database
		$db=new DB\Mongo('mongodb://localhost:27017','test');
		$this->db=$db;
	}

	// called when the URL /picss/@name is requested with GET
	// get the Picss by its name and render it in a separate page
	function get($f3, $args) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		$name=empty($args['name'])?'':$args['name'];
		$items = $picss->find(array('name'=>$name));
		$f3->set('picss', $items[0]);
		$f3->set('inc', 'onepicss.htm');
	}

	// called when /picss is requested in GET
	// get all Picss and render them on the front / list page
	function getAll($f3) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		$items = $picss->find();
		$f3->set('items', $items);
		$f3->set('inc', 'allpicss.htm');
	}

	// called when the URL /picss/@name is requested with POST
	// create a Picss with the data sent in the request
	function post($f3) {
		// check whether name and label have been set
		if (!$f3->exists('POST.name') || !strlen($f3->get('POST.name')))
			$f3->set('message','Name is required');
		elseif (!$f3->exists('POST.label') || !strlen($f3->get('POST.label')))
			$f3->set('message','Label is required');
		else {
			$db=$this->db;
			$newpicss=new DB\Mongo\Mapper($this->db,'picss');
			// populate the newpicss attributes with those existing in the POST request
			// for this to work, form fields *must* be named after the attributes of the document stored in Mongo
			$newpicss->copyfrom('POST');
			// move uploaded files to the UPLOADS dir (aka /uploads)
			$web = \Web::instance();
			$files = $web->receive(
				function($file){
        			return true; // allows the file to be moved from php tmp dir to your defined upload dir
    			}
			);

			// if files have been uploaded and moved
			// set their local path in the Mongo document via 2 attributes image and sound
			if ($files && $f3->exists('FILES')) {
				// $thumb = new Image($f3->get('FILES')['image']['name'], false, $f3->get('TMP').'/'.$f3->get('FILES')['image']['tmp_name']);
				// $thumb->resize(150,150);
				// $thumb->save();
				$newpicss->set('image', $f3->get('FILES')['image']['name']);
				$newpicss->set('sound', $f3->get('FILES')['sound']['name']);
			}
			// save the new document in Mongo
			$newpicss->save();
			// send the json response with the newpicss data (TODO fix the json formatting)
			// $f3->set('picss', $newpicss);
			// $f3->set('layoutfile','savedpicssresponse.json');
			// $f3->set('format','application/json');

			// for tests: rather than rendering json, simply reroute to the list of Picss
			$f3->reroute('/picss');
		}
	}

	function put() {

	}

	function delete() {

	}

	// called before any other function
	function beforeRoute($f3) {
		// set the layout file and its format. by default it is layout.htm in text/html format
		$f3->set('layoutfile','layout.htm');
		$f3->set('format','text/html');
	}

	// called after any other function
	function afterRoute($f3) {
		// final rendering of the response with the specified layout file and format

		// if (isset($f3->get('format')))
		// 	echo Template::instance()->render('savedpicssresponse.json', 'application/json');
		// else
			// echo Template::instance()->render('layout.htm');
		echo Template::instance()->render($f3->get('layoutfile'), $f3->get('format'));
	}

}

?>