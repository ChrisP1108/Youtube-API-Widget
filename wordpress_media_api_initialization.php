<?php

    // SNIPPET CODE BELOW MUST BE SET TO RUN EVERYWHERE FOR COOKIE TO BE SET

        // START

            // Checks For Cookie.  This allows greater efficiency of the API and improves performance by resuing Local Storage data for a given period of time without having to call the API on every page load.
            
            global $media_cookie_expired;

            add_action('init', function() {
                global $media_cookie_expired;
                if (isset($_COOKIE['media_api_stored'])) {
                    $media_cookie_expired = false;
                } else {
                    $media_cookie_expired = true;
                    setcookie('media_api_stored', time() + 3600, time() + 3600, "/"); // 1 Hour
                }
            });

        // END

    // SNIPPET CODE BELOW MUST BE SET TO SITE WIDE HEADER TO INITIALIZE WIDGET

        // START

            // Media API Import And Execution To Load The "Your Retirement Untangled" Playlist.

            $media_widget_import = file_get_contents('https://www.partnerwithmagellan.com/wp-content/media-api-widget/media_api_widget.txt');
            
            eval($media_widget_import);

            // Checks for cookie to determine if API should be called again or reuse existing data in Local Storage if cookie exists and has not expired.
            
            global $media_cookie_expired;

            if ($media_cookie_expired !== true && $media_cookie_expired !== false) {
                $media_cookie_expired = true;
            }

            // Get "Your Retirement Untangled" Youtube Playlist 
            
            get_media_content('youtube', '{{ playlist name }}', '{{ API KEY }}', '{{ Playlist Id }}', $media_cookie_expired);
        
        // END

?>