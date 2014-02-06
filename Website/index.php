<?php
// loads fat free framework in the variable $f3
$f3 = require('fff/base.php');

// debug mode, remove before putting in prod
$f3->set('DEBUG',3);

// An object oriented PHP driver for FFMpeg binary
// https://github.com/alchemy-fr/PHP-FFmpeg/tree/0.4.4
// require('FFMpeg/FFMpeg.php');

$f3->set('AUTOLOAD', 'app/'); // loads all classes in the app/ directory
$f3->set('UPLOADS','uploads/'); // don't forget to set an Upload directory, and make it writable!
$f3->set('TEMP','tmp/'); // don't forget to set an Temp directory, and make it writable!
$f3->set('UI','views/'); // don't forget to set an Temp directory, and make it writable!

// home page
$f3->route('GET /', 'Home->showIndex');

// map HTTP methods (GET, POST, etc) to methods in the Picss class
$f3->map('/picss/@id','Picss');
$f3->map('/picss','Picss');

$f3->run();

?>