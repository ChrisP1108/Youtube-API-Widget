<?php

    // SNIPPET CODE BELOW MUST BE SET TO RUN EVERYWHERE FOR COOKIE TO BE SET

        // START

            // Checks For Cookie.  This allows greater efficiency of the API and improves performance by resuing Local Storage data for a given period of time without having to call the API on every page load.
	
                // DO NOT MODIFY CODE BELOW EXCEPT FOR $cookie_name.  THE NAME MUST BE UNIQUE TO THE SITE NAME
        
                // Checks For Cookie.  This allows greater efficiency of the API and improves performance by resuing Local Storage data for a given period of time without having to call the API on every page load.

                add_action('template_redirect', function() {
                    global $media_cookie_expired;
                    global $cookie_name;
                    $cookie_name = 'wise_money_stored'; // PROVIDE A UNIQUE NAME THAT NO OTHER MICROSITE NAME HAS.
                    $cookie_expiration_time = time() + 3600;
                    
                    if (isset($_COOKIE[$cookie_name])) {
                        $media_cookie_expired = false;
                    } else {
                        $media_cookie_expired = true;
                        setcookie($cookie_name, $cookie_expiration_time, $cookie_expiration_time, "/"); // 1 Hour
                    }
                });

        // END

    // SNIPPET CODE BELOW MUST BE SET TO SITE WIDE HEADER TO INITIALIZE WIDGET

        // START

            // Media API Import And Execution To Load The "Your Retirement Untangled" Playlist.

            // DO NOT MODIFY CODE UNTIL WHERE INDICATED
	
                // Media API Import 

                $media_widget_import = file_get_contents('https://www.partnerwithmagellan.com/wp-content/media-api-widget/media_api_widget.txt');
                
                eval($media_widget_import);

                // Checks for cookie to determine if API should be called again or reuse existing data in Local Storage if cookie exists and has not expired.
                
                global $media_cookie_expired;
                global $cookie_name;

                if ($media_cookie_expired !== true && $media_cookie_expired !== false) {
                    $media_cookie_expired = true;
                }

            // Call get_media_content() with following parameters for youtube
            
            get_media_content([
                'type' => 'youtube', 
                'playlist_name' => 'in_the_news', 
                'api_key' => 'AIzaSyCIPsc5gno1yyiK1LPDGnhmDDtkh00QXgc', 
                'media_data' => 'PLm-M-NpTwOQl5f5lPVjOtZObPx7Rh_IE4', 
                'sort_mode' => 'normal',
                'cookie_name' => $cookie_name,
                'cookie_expired' => $media_cookie_expired
            ]);

            // Call get_media_content() with following parameters for podcast

            get_media_content([
                'type' => 'podcast', 
                'podcast_platform' => 'omny',
                'playlist_name' => 'wise_money', 
                'api_key' => null, 
                'media_data' => '1642547365', 
                'sort_mode' => 'normal',
                'cookie_name' => $cookie_name,
                'cookie_expired' => $media_cookie_expired
            ]);
        
        // END

?>
