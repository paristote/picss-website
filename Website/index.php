<?php

$f3 = require('fff/base.php');

require('Picss.php');

$f3->set('UPLOADS','uploads/'); // don't forget to set an Upload directory, and make it writable!
$f3->set('TEMP','tmp/'); // don't forget to set an Temp directory, and make it writable!

$f3->route('GET /',
	       function($f3) {
	       	$f3->reroute('/picss');	
	       });

$f3->map('/picss/@name','Picss');

$f3->route('GET /picss', 'Picss->getAll');
$f3->route('POST /picss', 'Picss->post');

$f3->run();

?>