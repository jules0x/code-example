<?php

class VideoPage extends Page {

	private static $many_many = array(
		'Vids' => 'Vid'
	);

	private static $description = 'Holds vids; first alternative';

}

class VideoPage_Controller extends Page_Controller {

	public static $allowed_actions = array(
		'yt_submit',
        'videoset'
	);

	public function yt_submit() {

		if (Permission::check('VID_CREATE')) {

			$vidID = $_GET['VidID'];

			if (Vid::yt_exists($vidID)) {

				if (!Vid::db_exists($vidID)) {

					$vid = new Vid();
					$vid->VidID = $vidID;

					$vid->Title = Vid::yt_getTitle($vidID);

					$vid->Artist = Vid::vid_getTitleParts(Vid::yt_getTitle($vidID), 'Artist');
					$vid->TrackName = Vid::vid_getTitleParts(Vid::yt_getTitle($vidID), 'TrackName');

					$vid->write();

					return 'Added video';
				} else {
					return 'Video already exists!';
				}
			} else {
				return 'No video found';
			}
		} else {
			return 'Denied';
		}

	}

	public function videoset() {
        $vids = Vid::get()->sort('rand()')->limit(24);

		$vidSetHTML = '';

		foreach ($vids as $vid) {
			$vidSetHTML .= $vid->renderWith('Video');
		}

		return $vidSetHTML;
	}

}
