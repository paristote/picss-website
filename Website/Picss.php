<?php


class Picss {

	public $db;

	//! Instantiate class
	function __construct() {
		$f3=Base::instance();
		// Connect to the database
		$db=new DB\Mongo('mongodb://localhost:27017','test');
		$this->db=$db;
	}

	function get($f3, $args) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		$name=empty($args['name'])?'':$args['name'];
		$items = $picss->find(array('name'=>$name));
		$f3->set('picss', $items[0]);
		$f3->set('inc', 'onepicss.htm');
	}

	function getAll($f3) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		$items = $picss->find();
		$f3->set('items', $items);
		$f3->set('inc', 'allpicss.htm');
	}

	function post($f3) {

		if (!$f3->exists('POST.name') || !strlen($f3->get('POST.name')))
			$f3->set('message','Name is required');
		elseif (!$f3->exists('POST.label') || !strlen($f3->get('POST.label')))
			$f3->set('message','Label is required');
		else {
			$db=$this->db;
			$newpicss=new DB\Mongo\Mapper($this->db,'picss');
			$newpicss->copyfrom('POST');

			$web = \Web::instance();
			$files = $web->receive(
				function($file){
        			return true; // allows the file to be moved from php tmp dir to your defined upload dir
    			}
			);


			if ($files && $f3->exists('FILES')) {
				// $thumb = new Image($f3->get('FILES')['image']['name'], false, $f3->get('TMP').'/'.$f3->get('FILES')['image']['tmp_name']);
				// $thumb->resize(150,150);
				// $thumb->save();
				$newpicss->set('image', $f3->get('FILES')['image']['name']);
				$newpicss->set('sound', $f3->get('FILES')['sound']['name']);
			}
			$newpicss->save();
			$f3->set('picss', $newpicss);
			$f3->set('layoutfile','savedpicssresponse.json');
			$f3->set('format','application/json');
			//$f3->reroute('/picss');
		}
		//$this->getAll($f3);
	}

	function put() {

	}

	function delete() {

	}

	function beforeRoute($f3) {
		$f3->set('layoutfile','layout.htm');
		$f3->set('format','text/html');
	}

	function afterRoute($f3) {
		// Render HTML layout
		// if (isset($f3->get('format')))
		// 	echo Template::instance()->render('savedpicssresponse.json', 'application/json');
		// else
			// echo Template::instance()->render('layout.htm');
		echo Template::instance()->render($f3->get('layoutfile'), $f3->get('format'));
	}

}

?>