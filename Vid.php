<?php

class Vid extends DataObject implements PermissionProvider {

    private static $db = array(
        'Title' => 'Text',
        'TrackName' => 'Varchar',
        'Artist' => 'Varchar',
        'Album' => 'Varchar',
        'VidID' => 'Varchar'
    );

    private static $summary_fields = array(
        'Artist',
        'TrackName',
        'Title'
    );

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main',
            new LiteralField(
                'Link',
                '<strong>Check link </strong><span style="display: inline-block;width: 126px;"></span>
                 <a href="https://www.youtube.com/watch?v=' . $this->VidID . '" target="_blank">' . $this->VidID . '</a><br><br>'));
        $fields->addFieldToTab('Root.Main',
            new LiteralField(
                'Preview',
                '<iframe width="420" height="315" src="https://www.youtube.com/embed/' . $this->VidID . '" frameborder="0" allowfullscreen></iframe>'));
        return $fields;
    }

    public function getCMSValidator() {
        return new RequiredFields(array('Url'));
    }


	//check if the vid is in the DB
    public static function db_exists($id) {
        return Vid::get()->filter('VidID', $id)->first();
    }

	//check if the vid is online and embeddable
    public static function yt_exists($id) {
        $theURL = "http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=$id&format=json";
        $headers = get_headers($theURL);

        if (substr($headers[0], 9, 3) == "200") {
            return true;
        } else {
            return false;
        }
    }

    //get best quality thumbnail from YouTube
    public function bestRes($VidID) {
        $theURL = "http://img.youtube.com/vi/" . $VidID . "/hqdefault.jpg";
        $headers = get_headers($theURL);
        if (substr($headers[0], 9, 3) == "200") {
            return $theURL;
        } else {
            $theURL = "http://img.youtube.com/vi/" . $VidID . "/default.jpg";
            $headers = get_headers($theURL);
            if (substr($headers[0], 9, 3) == "200") {
                return $theURL;
            }
        }
    }


	//import a vid from a YouTubeID
    public static function importVidViaYouTubeId($id) {
        $vid = new Vid();
        $vid->VidID = $id;
		$vid->Title = Vid::yt_getTitle($id);

		$vid->Artist = Vid::vid_getTitleParts(Vid::yt_getTitle($id), 'Artist');
		$vid->TrackName = Vid::vid_getTitleParts(Vid::yt_getTitle($id), 'TrackName');

        return $vid->write();
    }

	//retrieve title from YouTube
    public static function yt_getTitle($videoID) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://gdata.youtube.com/feeds/api/videos/'.$videoID);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $xml   = new SimpleXMLElement($response);
            return (string) $xml->title;
        }
    }

	//split the title into parts ie. 'Artist', 'TrackName'
    public static function vid_getTitleParts($title, $part) {
        $titleComponents = explode("-", $title);

		//If explode didn't split the title try a different splitter
		if ($title == $titleComponents[0]) {
			$titleComponents = explode("|", $title);

			//If second splitter wasn't found, give up
			if ($title == $titleComponents[0]) {
				return '';
			}
		}

		if ($part == 'Artist') {
			return $titleComponents[0];
		}
		if ($part == 'TrackName') {
			return $titleComponents[1];
		}

    }


	//permissions
    function providePermissions() {
        return array(
            'VID_EDIT' => 'Edit vids',
            'VID_DELETE' => 'Delete vids',
            'VID_CREATE' => 'Create vids',
        );
    }
    function canView($member = null) {
        return true;
    }
    function canCreate($member = null) {
        return Permission::check('VID_CREATE');
    }
    function canEdit($member = null) {
        return Permission::check('VID_EDIT');
    }
    function canDelete($member = null) {
        return Permission::check('VID_DELETE');
    }
}

