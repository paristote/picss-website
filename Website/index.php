<?php

$f3 = require('fff/base.php');

// the Picss controller
// TODO move to a separate folder with other model / controller classes
require('Picss.php');

$f3->set('UPLOADS','uploads/'); // don't forget to set an Upload directory, and make it writable!
$f3->set('TEMP','tmp/'); // don't forget to set an Temp directory, and make it writable!

// route / to /picss
$f3->route('GET /',
	       function($f3) {
	       	$f3->reroute('/picss');	
	       });

// map HTTP methods to Picss methods
$f3->map('/picss/@name','Picss');

// front page
$f3->route('GET /picss', 'Picss->getAll');

// add picss
$f3->route('POST /picss', 'Picss->post');

$f3->run();

?>