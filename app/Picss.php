<?php

/*
 * The Picss controller
 * Responsible for getting (get), creating (post) a Picss
 * Uses the F3 routing map() method
 */
class Picss extends AppBase {

	/*
	 * Called when the URL /picss/@name is requested with GET
	 * Get the Picss by its ID
	 */
	function get($f3, $args) {
		$picss=new DB\Mongo\Mapper($this->db,'picss');
		// get the ID from the arguments passed in the URL
		if (isset($args['id']) && !empty($args['id'])) {
			$id = trim($args['id']);

			// load a Picss object by its URL ID
			$picss->load(array('urlId' => $id));

			if ($picss->dry()) {
				try {
				    $picss->load(array('_id' => new MongoId($id)));
				    // http://php.net/manual/en/class.mongoid.php
				} catch (MongoException $ex) {}
			}
		}

		if (!$picss->dry()) // if the Picss object exists
		{
			$f3->set('picss', $picss);
			// if the sound attribute is a url, the sound will be loaded from Deezer directly
			// we don't need to point at the local uploads directory so the variable is empty
			if (substr_count($picss->get('sound'), "http"))
				$f3->set('uploadsDir', "");
			else // otherwise we set the uploads dir to where sounds are uploaded
				$f3->set('uploadsDir', "/".$f3->get('UPLOADS'));
			// set the page title after the Picss name
			$f3->set('pageTitle', ':Picss: '.$picss->get('name'));
			// set the layout file
			$f3->set('content', '1picss.htm');
			$f3->set('pageCss', 'onepicss.css');
		}
		else // if no Picss was found, show a Picss not found message
		{
			$f3->set('pageTitle', ':( Not found');
			$f3->set('content', 'notfound.htm');	
		}
	}


	/*
	 * Called when the URL /picss is requested with POST
	 * Create a Picss with the form-data sent in the request
	 */
	function post($f3) {
		// check whether description is set
		// TODO if not, return an error with an explicit description
		if (!$f3->exists('POST.description') || !strlen($f3->get('POST.description')))
		{
			// Name is required
		}
		else
		{
			$db=$this->db;
			$newpicss=new DB\Mongo\Mapper($this->db,'picss');
			// set the newpicss attributes with those existing in the POST request
			$newpicss->set('name', $f3->get('POST.description'));
			$newpicss->set('ip', $f3->get('IP'));
			// move uploaded files to the UPLOADS dir (i.e. /uploads)
			$web = \Web::instance();
			$files = $web->receive(
				function($file){
					// TODO check the size of the audio file and the photo
					// TODO send request to Zencoder
					// TODO resize photo here
        			return true; // allows the file to be moved from php tmp dir to the defined upload dir
    			}
			);
			
			// create the new document in Mongo, from now on it has an _id attribute
			$newpicss->save();

			// move and rename the photo and the audio
			// result: an image "img-{id}.jpg" and a sound "snd-{id}.mp4" in directory "{UPLOADS}/{id}/"
			$id = $newpicss->get('_id');
			$hashids = new Hashids\Hashids($id->{'$id'});
			$urlId = $hashids->encrypt($id->getTimestamp());
			$newpicss->set('urlId', $urlId);
			$newpicss->set('date', $id->getTimestamp());

			// get the full absolute path of the UPLOADS dir
			$uploadsDir = realpath('.')."/".$f3->get('UPLOADS');
			// create a new directory named after the new Picss ID, inside UPLOADS dir
			mkdir($uploadsDir.$id."/");
			if ($f3->exists('FILES.image')) // it's an image
			{
				// just move it to the newly created directory
				$image = $f3->get('FILES')["image"];
				$imageFile = $id."/img-".$id.".jpg";
				// TODO use lower case values only
				// TODO better name of thumbnail
				$this->createThumbnail(250, $image['name'], $uploadsDir, $f3);
				rename($uploadsDir.$image['name']."-250.jpg", $uploadsDir.$imageFile."-250.jpg");
				$this->createThumbnail(400, $image['name'], $uploadsDir, $f3);
				rename($uploadsDir.$image['name']."-400.jpg", $uploadsDir.$imageFile."-400.jpg");
				$this->createThumbnail(1280, $image['name'], $uploadsDir, $f3);
				rename($uploadsDir.$image['name']."-1280.jpg", $uploadsDir.$imageFile);
				//rename($uploadsDir.$image['name'], $uploadsDir.$imageFile);
				unlink($uploadsDir.$image['name']);
				// set the image attribute in the Picss object
				$newpicss->set('image', $imageFile);
			}
			if ($f3->exists('FILES.sound')) // it's an audio file
			{
				$sound = $f3->get('FILES')["sound"];
				$dot = strpos($sound["name"], "."); // get the position of the last . in the file name
				if ($dot > 0)
				{
					$oldFile = $sound['name'];
					$format = substr($sound["name"], $dot+1); // extract the file extension
					if ($format != "mp4") // if the extension is not mp4 we must convert to this format
					{
						// TODO make sure the host supports exec method and the ffmpeg tool
						// TODO use the PHP-FFmpeg library
						exec("/usr/local/bin/ffmpeg -i ".$f3->get('UPLOADS')."/".$sound['name']. // input
				 	 		" -b:a 64k -ar 44100 -acodec mp3".$f3->get('UPLOADS')."/".$sound['name'].".mp3"); // ouput
						// b:a : bitrate | ar : audio sampling freq
						unlink($uploadsDir.$sound['name']); // delete the file in the old format
						$oldFile = $sound['name'].".mp4";
					}
					// move the file (converted or original) to the newly created directory
					$audioFile = $id."/snd-".$id.".mp4";
					rename($uploadsDir.$oldFile, $uploadsDir.$audioFile);
					// set the sound attribute in the Picss object
					$newpicss->set('sound', $audioFile);
				}
			}
			else // it's a URL, set the sound attribute with it
			{
				$newpicss->set('sound', $f3->get('POST.soundUrl'));
			}
			// save the Picss object with the new attributes
			$newpicss->save();

			// create json response with the new Picss data
			$arrPicss = array(
				'id'    => $newpicss->get('_id'),
				'name'  => $newpicss->get('name'),
				'image' => $newpicss->get('image'),
				'sound' => $newpicss->get('sound')
			);
			$f3->set('picss', json_encode($arrPicss));
			// return the Picss document in JSON format
			$f3->set('layout','savedpicssresponse.json');
			$f3->set('format','application/json');

		}
	}

	function createThumbnail($max, $imageName, $uploadDir, $f3) {
		$img = new Image($imageName, false, $uploadDir);
		$orig_w = $img->width();
		$orig_h = $img->height();
		$new_w = 0;
		$new_h = 0;
		if ($orig_w > $orig_h) {
			$new_w = $max;
			$new_h = $orig_h / ($orig_w / $new_w);
		} else {
			$new_h = $max;
			$new_w = $orig_w / ($orig_h / $new_h);
		}
		$img->resize($new_w, $new_h);
		$img->save();
		$f3->write($uploadDir.$imageName."-".$max.".jpg", $img->dump('jpeg'));
	}

	// TODO we may need to implement these methods
	// function put() {
	// }
	// function delete() {
	// }

}

?>