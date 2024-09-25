<?php

/**
 * Class used to get youtube playlist data via the youtube API.
 */

    class WP_Youtube_API {

        /**
         * PROPERTY - private
         * @var string $route_name
         * Used to create route utilizing the Wordpress Rest API. Route will be {origin}/wp-json/youtube/api/${$route_name}
        */

        private $route_name;

        /**
         * PROPERTY - private
         * @var string $youtube_api_key
         * API Key for accessing youtube data.
        */

        private $youtube_api_key;

        /**
         * PROPERTY - private
         * @var string $youtube_playlist_id
         * Used to retrieve specific youtube playlist data
        */

        private $youtube_playlist_id;

        /**
         * PROPERTY - private
         * @var int $youtube_caching_duration
         * Sets the timing for Wordpress Transient to store data for caching in seconds
        */

        private $youtube_caching_duration;

        /**
         * CONSTANT
         * @const string YOUTUBE_API_BASE_URL
         * Base url path for youtube API.  Used to concatenate api_key and youtube_playlist_id
        */

        const YOUTUBE_API_BASE_URL = "https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=";

        /**
         * CONSTRUCTOR
         * Takes in two parameters for creating an API Route and providing the API Key for Youtube.  Initializes the REST API route also
         * @param route_name $route_name - The name of the api route. Route will be {origin}/wp-json/youtube/api/${$route_name}
         * @param api_key $api_key - The youtube API key
         * @param youtube_playlist_id $youtube_playlist_id - The youtube playlist id
         * @param youtube_caching_duration $youtube_caching_duration - Sets the timing for Wordpress Transient to store data for caching in seconds
        */

        public function __construct($route_name = null, $youtube_api_key = null, $youtube_playlist_id = null, $youtube_caching_duration = 7200) {
            if ($route_name && $youtube_api_key && $youtube_playlist_id) {
                $this->route_name = $route_name;
                $this->youtube_api_key = $youtube_api_key;
                $this->youtube_playlist_id = $youtube_playlist_id;
                $this->youtube_caching_duration = $youtube_caching_duration;

                // Initialize REST API Route Registration

                add_action('rest_api_init', [$this, 'register_youtube_api']);
            }
        }

        /**
         * METHOD - Register_Youtube_Api
         * Registers Wordpress REST API Route 
         * @return void - Does not return anything
        */

        final public function register_youtube_api() {
            register_rest_route('/youtube/api', '/' . $this->route_name, [
                'methods' => 'GET',
                'permission_callback' => '__return_true',
                'callback' => [$this, 'get_youtube_data'],
            ]);
        }

        /**
         * METHOD - Get_Youtube_Data
         * Retrieves youtube data be either through existing Wordpress transient, or through HTTP request to youtube API if existing transient not found.
         * @return json - Parses data into json to return through the API
        */

        final public function get_youtube_data() {

            // Check if data is stored in transient.  If so, return transient data

            $youtube_transient_data = get_transient($this->route_name . '_youtube_api_data');

            if ($youtube_transient_data) {
                return new WP_REST_Response($youtube_transient_data, 200);
            }

            // Make HTTP request to youtube if no transient data found.

            $youtube_req_url = self::YOUTUBE_API_BASE_URL . $this->youtube_playlist_id . '&key=' . $this->youtube_api_key . '&maxResults=50';

            $youtube_get_req = wp_remote_get($youtube_req_url);

            // If unable to get data, return error

            if (is_wp_error($youtube_get_req)) {
                return new WP_Error('error accessing youtube', 'Youtube data could not be retrieved.', array('status' => 500));
            }

            $youtube_response_code = wp_remote_retrieve_response_code($youtube_get_req);

            $youtube_data = json_decode(wp_remote_retrieve_body($youtube_get_req), false);

            // If status code was not 200, return with error message from youtube api

            if ($youtube_response_code !== 200) {
                return new WP_REST_Response($youtube_data, $youtube_response_code);
            }

            // Get Episodes that are embedded in items key.  Get count of items to check against totalResults

            $youtube_items = $youtube_data->items;

            $youtube_items_tally = count($youtube_items);

            $next_page_token = null;

            if ($youtube_data->nextPageToken ?? null) {
                $next_page_token = $youtube_data->nextPageToken;
            }

            // Youtube Has A Limit Of 50 Results Per Request.  If Playlist Item Total Is Greater Than 50, Then A While Loop Runs Until Total Items Received Equals Total Results

            while ($youtube_data->pageInfo->totalResults > $youtube_items_tally) {
                $youtube_loop_req = wp_remote_get($youtube_req_url . '&pageToken=' . $next_page_token);
                if (is_wp_error($youtube_loop_req)) {
                    return new WP_Error('error getting additional youtube videos', 'There was an error in retrieving additional youtube videos.', array('status' => 500));
                }
                $youtube_loop = json_decode(wp_remote_retrieve_body($youtube_loop_req));
                $youtube_items_tally += count($youtube_loop->items);
                $next_page_token = $youtube_loop->nextPageToken;
                $youtube_items = array_merge($youtube_items, $youtube_loop->items);
            }

            // Merge All Items back into youtube data

            $youtube_data->items = $youtube_items;

            // Store data as transient

            set_transient($this->route_name . '_youtube_api_data', $youtube_data, $this->youtube_caching_duration);

            return new WP_REST_Response($youtube_data, $youtube_response_code);
        }
    }      