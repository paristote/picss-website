<?php

// F3
// loads fat free framework in the variable $f3
$f3 = require('fff/base.php');

// CONFIG
// debug mode, remove before putting in prod
$f3->set('DEBUG',3);
$f3->config('data/setup.cfg');


$f3->set('AUTOLOAD', 'app/'); // loads all classes in the app/ directory
$f3->set('UPLOADS','uploads/'); // uploaded files go here after validation
$f3->set('TEMP','tmp/'); // temp directory 
$f3->set('UI','views/'); // contains all the html views and json response formats

// ROUTES
// home page
$f3->route('GET /', 'Home->showIndex');
// register beta form action url
$f3->route('POST /register', 'Home->registerBeta');
// thank you message, automatically called after /register
$f3->route('GET /thankyou', 'Home->thankyou');
$f3->route('GET /bbb', 'Home->browse');
$f3->route('GET /bbb/@p', 'Home->browse');

// map HTTP methods (GET, POST, etc) to methods in the Picss class
$f3->map('/p/@id','Picss');
$f3->map('/p','Picss');

$f3->run();

?>