<?php

    // SNIPPET CODE BELOW MUST BE SET TO RUN EVERYWHERE FOR COOKIE TO BE SET

        // START
	
                // DO NOT MODIFY CODE BELOW EXCEPT FOR $cookie_name.  THE NAME MUST BE UNIQUE TO THE SITE(SHOW) NAME
	
                // Checks For Cookie.  This allows greater efficiency of the API and improves performance by resuing Local Storage data for a given period of time without having to call the API on every page load.

                add_action('template_redirect', function() {
                    global $media_cookie_expired;
                    global $cookie_name;
                    $cookie_name = "winning_retirement"; // SET UNIQUE NAME THAT NO OTHER MICROSITE NAME HAS
                    $cookie_expiration_time = time() + 3600;
                    
                    if (isset($_COOKIE[$cookie_name])) {
                        $media_cookie_expired = false;
                    } else {
                        $media_cookie_expired = true;
                        setcookie($cookie_name, $cookie_expiration_time, $cookie_expiration_time, "/"); // 1 HOUR
                    }
                });

                // DO NOT MODIFY THIS CODE BELOW

                // Media SEO Updater Import.  Imports And Executes Code To Allow Updating of <meta> tags for better SEO performance
                
                $seo_updater_url = 'https://www.partnerwithmagellan.com/wp-content/media-api-widget/media_api_widget_seo_updater.txt';

                $media_widget_seo_updater = @file_get_contents($seo_updater_url);

                $stored_seo_updater_path = 'media_api_widget_seo_updater_stored.txt';

                if (is_readable($stored_seo_updater_path)) {
                    $seo_stored_data = file_get_contents($stored_seo_updater_path);
                    if ($media_widget_seo_updater && $media_widget_seo_updater !== $seo_stored_data) {
                        file_put_contents($stored_seo_updater_path, $media_widget_seo_updater);
                    }
                    if ($media_widget_seo_updater === $seo_stored_data || !$media_widget_seo_updater) {
                        $media_widget_seo_updater = $seo_stored_data;
                    }
                } else {
                    file_put_contents($stored_seo_updater_path, $media_widget_seo_updater);
                }

                eval($media_widget_seo_updater);
        // END

    // SNIPPET CODE BELOW MUST BE SET TO SITE WIDE HEADER TO INITIALIZE WIDGET

        // START

            // DO NOT MODIFY CODE UNTIL WHERE INDICATED.  MAKE SURE THAT $cookie_name WAS PROVIDED A UNIQUE NAME IN THE CODE SNIPPET NAMED "Media API Storage Time Expiration Cookie"

            // Media API Import 

                $media_widget_import = @file_get_contents('https://www.partnerwithmagellan.com/wp-content/media-api-widget/media_api_widget.txt');

                $stored_widget_path = 'media_api_widget_stored.txt';

                if (is_readable($stored_widget_path)) {
                    $stored_data = file_get_contents($stored_widget_path);
                    if ($media_widget_import && $media_widget_import !== $stored_data) {
                        file_put_contents($stored_widget_path, $media_widget_import);
                    }
                    if ($media_widget_import === $stored_data || !$media_widget_import) {
                        $media_widget_import = $stored_data;
                    }
                } else {
                    file_put_contents($stored_widget_path, $media_widget_import);
                }
                
                eval($media_widget_import);

                // Checks for cookie to determine if API should be called again or reuse existing data in Local Storage if cookie exists and has not expired.
                
                global $media_cookie_expired;
                global $cookie_name;

                if ($media_cookie_expired !== true && $media_cookie_expired !== false) {
                    $media_cookie_expired = true;
                }

                // CODE BELOW CAN BE MODIFIED TO GET MEDIA DATA WHERE INDICATED(DO NOT MODIFY COOKIE DATA).  DO NOT MODIFY CODE ABOVE THIS LINE.  CODE BELOW IS FOR GETTING YOUTUBE, PODCAST DATA

                // Get Youtube "Winning Retirement" Playlist Data
            
                get_media_content([
                    'type' => 'youtube', // For youtube, type 'youtube', for podcast, type 'podcast'
                    'playlist_name' => 'winning_retirement',  // Provide a name.  Youtube and Podcast can have the same names as long as two youtube playlists or two podcast playlist don't have the same name
                    'api_key' => 'AIzaSyD0FQvquSLXqaFIiB9syv7HXQLoZt4EFCw', // Enter Youtube api key here.  For podcast, type null
                    'media_data' => 'PLE69e89d5PM3dNFAwiZswtg9x-lvoE5bh', // For Youtube, type Playlist ID.  For podcast, type podcast Id.
                    'sort_mode' => 'number_in_title', // For youtube playlist with episode numbers, type 'number_in_title'.  For youtube playlist without episode numbers or podcasts, type 'normal'
                    'cookie_name' => $cookie_name, // DO NOT MODIFY - This must be passed in for error handling.  Should always be $cookie_name
                    'cookie_expired' => $media_cookie_expired // DO NOT MODIFY - This must be passed in to determine if a fresh call to the API should be made or if the data that is in local storage can be loaded
                ]);
            
                // Get Podcast "Winning Retirement" Playlist Data From Omny
                
                get_media_content([
                    'type' => 'podcast', // For youtube, type 'youtube', for podcast, type 'podcast'
                    'podcast_platform' => 'omny', // Used for podcast only.  Usually this will be 'omny' unless told otherwise
                    'playlist_name' => 'winning_retirement', // Provide a name.  Youtube and Podcast can have the same names as long as two youtube playlists or two podcast playlist don't have the same name
                    'api_key' => null, // Enter Youtube api key here.  For podcast, type null
                    'media_data' => '1452271687', // For Youtube, type Playlist ID.  For podcast, type podcast Id.
                    'sort_mode' => 'normal', // For youtube playlist with episode numbers, type 'number_in_title'.  For youtube playlist without episode numbers or podcasts, type 'normal'
                    'cookie_name' => $cookie_name, // DO NOT MODIFY - This must be passed in for error handling.  Should always be $cookie_name
                    'cookie_Expired' => $media_cookie_expired // DO NOT MODIFY - This must be passed in to determine if a fresh call to the API should be made or if the data that is in local storage can be loaded
                ]);
        
        // END
?>
