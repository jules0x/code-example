<?php

class VidImportTask extends BuildTask {

    protected $description = 'Import YouTube vid ID\'s from a .txt file in "assets/Imports" dir. One vid ID per line.';

    public function run($request) {

        //Counter for dir contents
        $txtFileCount = 0;

        //Open imports directory
        $dir = dir(Director::baseFolder() . '/assets/Imports/');

        //Check if dir is empty
        if (count(glob($dir.'*')) === 0 ) {
            //No files found in dir
            echo 'To import YouTube vids, add a .txt file to "assets/Imports" dir with one vid ID per line.';
        } else {
            //List files in directory
            while (($file = $dir->read()) !== false) {

                //Make sure it's a .txt file
                if(strlen($file) < 5 || substr($file, -4) != '.txt') {
                    continue;
                }

                //A text file is present, so increment the counter
                $txtFileCount = $txtFileCount + 1;
                //Read the lines of the file into an array
                $lines = file(Director::baseFolder() . '/assets/Imports/'.$file, FILE_IGNORE_NEW_LINES);

                echo '<p><strong>' . count($lines) . '</strong> items found in <strong>' . $file . '</strong>.<br></p>';

                //Create an array to hold the final list of imported vids
                $importedVids = array();

                //For vids not yet in DB, create them...
                foreach ($lines as $vidID) {

                    if (!Vid::db_exists($vidID)) {
						if (Vid::yt_exists($vidID)) {
							if (Vid::importVidViaYouTubeId($vidID)) {
								array_push($importedVids, $vidID);
							}
                        }
                    }
                }

                //... then display the outcome
                if (count($importedVids) > 0) {
                    echo '<p><strong>' . count($importedVids) . '</strong> vid';
                    if (count($importedVids) > 1) {
                        echo 's';
                    }
                    echo ' added to database.<br>';
                    if (count($importedVids) < count($lines)) {
                        echo '<strong> ' . (count($lines) - count($importedVids)) . ' </strong> vid';
                        if ((count($lines) - count($importedVids)) > 1) {
                            echo 's';
                        }
                        echo ' already present.';
                    }
                    echo '</p>';
                } else {
                    echo '<p>No new vids found.<br></p>' ;
                }
                echo '<hr>';
            }

            if ($txtFileCount == 0) {
                //None of the files in the dir are .txt files
                echo 'To import YouTube vids, add a .txt file to "assets/Imports" dir with one vid ID per line.';
            }
        }

        $dir->close();
    }

}
