<?php 

    // Gets Youtube Data.  API Key And PlaylistID Must Be Passed In

    function get_youtube_playlist($playlist_name = 'unnamed', $api_key = null, $playlist_id = null) {

        $cookie_time_storage_name = $playlist_name . '_playlist_stored';

        // Time That Playlist In Local Storage Will Be Utilized For Before Another API Call.  Time In Seconds
        
        $cookie_storage_time_interval = 3600;

        // Cookie Expiration Time

        $cookie_expiration_time = time() + $cookie_storage_time_interval;

        // Checks If Cookie Is Already Stored On Server And Is Within Time Interval.  If Not, Then New API Call Made And New Cookie Set

        if (!isset($_COOKIE[$cookie_time_storage_name]) || $_COOKIE[$cookie_time_storage_name] < time()) {
            $youtube_req_url = 'https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='. $playlist_id .'&key=' . $api_key . '&maxResults=10000';

            $youtube_get_req = @file_get_contents($youtube_req_url);

            if (!$youtube_get_req) {
                echo '<script>
                    console.error("There was an error getting the Youtube data.  Try reloading or check the API key.");
                </script>';
                return;
            }

            $youtube_data = json_decode($youtube_get_req, true);

            $youtube_items = $youtube_data['items'];

            $youtube_items_tally = count($youtube_data['items']);

            $nextPageToken = $youtube_data['nextPageToken'];

            // Youtube Has A Limit Of 50 Results Per Request.  If Playlist Item Total Is Greater Than 50, Then A While Loop Runs Until Total Items Received Equals Total Results

            while ($youtube_data['pageInfo']['totalResults'] > $youtube_items_tally) {
                $youtube_loop = json_decode(file_get_contents($youtube_req_url . '&pageToken=' . $nextPageToken), true);
                $youtube_items_tally += count($youtube_loop['items']);
                $nextPageToken = $youtube_loop['nextPageToken'];
                $youtube_items = array_merge($youtube_items, $youtube_loop['items']);
            }

            $youtube_data['items'] = $youtube_items;

            setcookie($cookie_time_storage_name, $cookie_expiration_time, $cookie_expiration_time, '/', '', 0, false);

            echo '<script>localStorage.setItem("'. $playlist_name .'_youtube_playlist", JSON.stringify('. json_encode($youtube_data) .')); console.log("'. $playlist_name .' youtube data loaded from API and stored on localStorage as `youtubePlaylists.`");</script>';
        } else {
            echo '<script>
                    console.log("'. $playlist_name .' youtube data loaded from localStorage as the existing data is still within the time storage interval.");
                </script>
            ';
        }

        // Javascript Output To Load Videos And Lightbox

        echo '
            <style>

                /* -- '.$playlist_name.' Styling. The CSS styling below is for CSS that will be universal throughout */

                /* -- START - Video Item Styling. Some Styling Applies To Lightbox Also -- */

                .'.$playlist_name.'-youtube-video-item, .'.$playlist_name.'-youtube-video-item-text-overlay-enabled {
                    cursor: pointer;
                    position: relative;
                    width: 100%;
                    height: 100%;
                    display: block;
                    box-sizing: border-box;
                }
                .'.$playlist_name.'-youtube-video-thumbnail-text-wrapper {
                    width: 100%;
                    background: #000000;
                    box-sizing: border-box;
                    position: relative;
                    aspect-ratio: 1.777 / 1;
                }
                .'.$playlist_name.'-youtube-video-item img, .'.$playlist_name.'-youtube-video-item-text-overlay-enabled img, 
                .'.$playlist_name.'-youtube-video-item svg, .'.$playlist_name.'-youtube-video-item-text-overlay-enabled svg  {
                    transition: 0.5s;
                }
                .'.$playlist_name.'-youtube-video-item-text-overlay-enabled:hover > * img:nth-child(1) {
                    opacity: 0.25;
                }
                .'.$playlist_name.'-youtube-video-item-text-overlay-enabled:hover svg, .'.$playlist_name.'-youtube-video-item:hover > * img:nth-child(2) {
                    opacity: 0;
                }
                .'.$playlist_name.'-youtube-video-item-thumbnail {
                    object-fit: cover;
                    width: 100%;
                    height: 100%;
                }
                .'.$playlist_name.'-youtube-item-play-button {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                }
                .'.$playlist_name.'-youtube-item-play-button img, .'.$playlist_name.'-youtube-item-play-button svg {
                    width: 100%;
                    height: 100%;
                }
                .'.$playlist_name.'-youtube-text-overlay {
                    position: absolute;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                    width: 100%;
                    height: 100%;
                    top: 0;
                    gap: clamp(1.125rem, 15%, 3rem);
                    background: #00000050;
                    opacity: 0;
                    transition: 0.5s;
                    padding: clamp(16px, 5vw, 60px);
                    box-sizing: border-box;
                    font-family: inherit !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .hover-text-container {
                    position: absolute;
                    box-sizing: border-box;
                    width: 100%;
                    height: 100%;
                    color: #fff;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    padding: 24px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    opacity: 0;
                    transition: 0.5s;

                }
                .'.$playlist_name.'-youtube-text-overlay > * {
                    margin: 0;
                    text-align: center;
                    color: #fff;
                }
                .'.$playlist_name.'-youtube-text-overlay h3 {
                    font-size: min(9.5vw, 40px);
                    font-family: inherit !important;
                }
                .'.$playlist_name.'-youtube-text-overlay p {
                    font-size: min(7vw, 1.375rem);
                    font-family: inherit !important;
                }
                .'.$playlist_name.'-youtube-text-overlay:hover {
                    opacity: 1;
                }

                /* -- END - Video Item Styling -- */

                /* -- START - Lightbox Styling -- */

                [data-lightboxid="'.$playlist_name.'"] .grid-layout {
                    display: grid;
                    grid-template-rows: min-content;
                    gap: 48px;
                }
                [data-lightboxid="'.$playlist_name.'"] .grid-layout {
                    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper {
                    position: relative;
                    cursor: pointer;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper img {
                    object-fit: cover;
                    width: 100%!important;
                    height: inherit !important;
                    min-width: 100%!important;
                    max-height: 100%!important;
                    transition: 0.75s;
                    aspect-ratio: 1.777 / 1;
                }
                [data-lightboxid="'.$playlist_name.'"] .'.$playlist_name.'-youtube-video-item {
                    filter: grayscale(75%);
                    transition: 0.5s;
                }
                [data-lightboxid="'.$playlist_name.'"] .'.$playlist_name.'-youtube-video-item svg {
                    opacity: 0;
                }
                [data-lightboxid="'.$playlist_name.'"] .'.$playlist_name.'-youtube-video-item:hover svg{
                    opacity: 1 !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .'.$playlist_name.'-youtube-video-item:hover {
                    filter: grayscale(0%) !important;
                    transform: scale(1.25);
                }
                [data-lightboxid="'.$playlist_name.'"] .video-thumbnail-wrapper iframe,
                [data-lightboxid="'.$playlist_name.'"] .video-thumbnail-wrapper h1 {
                    transition: 0.75s;
                    font-family: inherit !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .hover-text-container {
                    z-index: 0;
                    pointer-events: none;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper svg {
                    position: absolute;
                    transition: 0.5s;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 2;
                    width: 50%;
                    height: 50%;
                    opacity: 0;
                }
                .show-lightbox {
                    display: block!important;
                }
                .hide-scroll {
                    overflow: hidden!important;
                }
                [data-lightboxid="'.$playlist_name.'"] .frame-transition-left {
                    transform: translateX(-60%) rotateY(90deg) scale(0);
                    opacity: 0;
                    pointer-events: none !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .frame-transition-right {
                    transform: translateX(60%) rotateY(-90deg) scale(0);
                    opacity: 0;
                    pointer-events: none !important;
                }
                .disable-arrow {
                    opacity: 0;
                    pointer-events: none !important;
                }
                .element-invisible {
                    opacity: 0 !important;
                    z-index: -1 !important;
                    pointer-events: none;
                }
                .fast-forward-transitioning {
                    z-index: -1 !important;
                }
                .fast-forward-transitioning a {
                    opacity: 0.25 !important;
                }
                .fast-forward-transitioning [data-lightboxframe="true"] {
                    opacity: 0.20 !important;
                }
                .fast-forward-transitioning a,
                .fast-forward-transitioning [data-lightboxframe="true"] {
                    transition: 0.05s !important;
                }
                .fast-forward-transitioning a svg {
                    opacity: 0 !important;
                }
                .fast-forward-overlay {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: absolute;
                }
                [data-lightboxid="'.$playlist_name.'"] {
                    width: 100%;
                    height: 100vh;
                    display: none;
                    background: #000000E2;
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 100000;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-content-container {
                    max-width: 220vh;
                    margin: 0 auto;
                    position: relative;
                    height: 100%;
                    width: 100%;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container,
                [data-lightboxid="'.$playlist_name.'"] .lightbox-player-container {
                    width: 100%;
                    height: 100%;
                    margin: 0 auto;
                    box-sizing: border-box;
                    position: absolute;
                    top: 0;
                    transition: 0.5s;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container {
                    max-width: 1920px;
                    max-height: 90vh;
                    max-height: 90dvh;
                    width: 85vw;
                    height: 75dvh;
                    min-width: 75vw;
                    min-height: 90vh;
                    min-height: 90dvh;
                    background: #00000090;
                    overflow-y: auto;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    padding: 24px 40px 24px 40px;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-heading {
                    font-family: inherit !important;
                    color: white;
                    text-align: left;
                    font-size: clamp(28px, 9vw, 54px);
                    padding: 0 1.5rem;
                    margin: 0 0 min(3vw, 16px);
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-logo {
                    top: 0;
                    height: auto !important;
                    padding: 0 16px 24px;
                    min-width: 18vw !important;
                    max-width: 30vh !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container legend {
                    min-width: 22vw;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container::-webkit-scrollbar {
                    width: 8px;
                    box-sizing: border-box;
                    background: transparent;
                    border-radius: 0px;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container::-webkit-scrollbar-thumb {
                    background: transparent;
                    border-right: 8px solid white;
                    height: 25%;
                    max-height: 100%;
                    border-radius: 8px;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-player-container {
                    padding: 40px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-video-frame {
                    margin: 0 3%;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-player-container .lightbox-video-frame iframe {
                    width: 100%;
                    height: 100%;
                    position: absolute;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-video-frame .fast-forward-overlay {
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 100;
                }
                [data-lightboxid="'.$playlist_name.'"] [data-itemclickable="true"] {
                    cursor: pointer;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-video-frame .fast-forward-overlay h1,
                [data-lightboxid="'.$playlist_name.'"] .lightbox-video-frame [data-lightboxframe="true"] h1 {
                    color: #fff;
                    font-size: 9.75vh;
                    font-family: inherit !important;
                    text-align: center;
                    font-weight: 700;
                    z-index: 100;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-close-button {
                    color: white;
                    position: fixed;
                    top: 1.357vh;
                    left: calc(100% - 1.72vh);
                    transform: translateX(-100%);
                    font-family: Ruda, sans-serif;
                    font-weight: 700;
                    font-style: normal;
                    padding: 0.9vh;
                    font-size: 3.16vh;
                    cursor: pointer;
                    border: 0.6vh white solid;
                    border-radius: 50%;
                    width: 12vw;
                    height: 12vw;
                    max-width: 3.16vh;
                    max-height: 3.16vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    transition: 0.25s;
                    z-index: 10 !important;
                    box-sizing: content-box;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-button {
                    position: fixed;
                    top: 2.11vh;
                    left: 2.11vh;
                    max-width: 4.75vh;
                    max-height: 4.75vh;
                    width: 15vh;
                    height: 15vh;
                    transition: 0.25s;
                    cursor: pointer;
                    z-index: 10 !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-close-button:hover {
                    transform: translateX(-100%) scale(1.25);
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-button:hover {
                    transform: scale(1.25);
                } 
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-button:active {
                    transform: scale(0.9);
                } 
                [data-lightboxid="'.$playlist_name.'"] .lightbox-player-container svg {
                    min-width: 4vw;
                    max-height: 6.664vw;
                    width: 3.08vh;
                    height: 5.1395vh;
                    cursor: pointer;
                    z-index: 3;
                    transition: 0.25s;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-video-frame {
                    width: 78vw!important;
                    height: 43.9vw!important;
                    max-height: 90vh!important;
                    max-height: 90dvh!important;
                    max-width: 160vh!important;
                    position: relative;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-episode-text {
                    font-family: inherit !important;
                    color: white;
                    margin: 1rem 0 0;
                    text-align: center;
                    font-size: 1.75rem;
                    font-weight: 700;
                }
                [data-lightboxid="'.$playlist_name.'"] .arrow-left {
                    cursor: pointer;
                }
                [data-lightboxid="'.$playlist_name.'"] .arrow-left:hover {
                    transform: scale(1.25);
                    transition: 0.25s;
                }
                [data-lightboxid="'.$playlist_name.'"] .arrow-right {
                    cursor: pointer;
                    transform: rotate(180deg);
                }
                [data-lightboxid="'.$playlist_name.'"] .arrow-right:hover {
                    transform: scale(1.25) rotate(180deg);
                    transition: 0.25s;
                }
                [data-lightboxid="'.$playlist_name.'"] .arrow-left:active {
                    transform: translate(-10%);
                    transition: 0.1s;
                }
                [data-lightboxid="'.$playlist_name.'"] .arrow-right:active {
                    transform: translate(10%) rotate(180deg);
                    transition: 0.1s;
                }
                [data-lightboxid="'.$playlist_name.'"] .carousel-zero-transition a {
                    transition: 0.05s !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-container-off {
                    opacity: 0;
                    pointer-events: none !important;
                    transition: 0.5s;
                    overflow: hidden !important;
                }
                [data-lightboxid="'.$playlist_name.'"] [data-lightboxframe="true"] {
                    width: 100%;
                    height: 100%;
                    border: 0px;
                    position: absolute;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    box-shadow: 0px 0px 48px black;
                    filter: drop-shadow(rgba(255, 255, 255, 0.44) 0px 0px 80px);
                    background: #000;
                    transition: 0.5s !important;
                    z-index: 10;
                }
                [data-lightboxid="'.$playlist_name.'"] .frame-transitioning-show-text img,
                [data-lightboxid="'.$playlist_name.'"] .frame-transitioning-show-text iframe {
                    opacity: 0.2;
                }
                [data-lightboxid="'.$playlist_name.'"] .frame-transitioning-show-text svg {
                    opacity: 0 !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .frame-transitioning-show-text .hover-text-container {
                    opacity: 1 !important;
                }
                [data-lightboxid="'.$playlist_name.'"] .lightbox-arrow-hold-transitioning {
                    animation-iteration-count: infinite;
                    animation-duration: 0.15s;
                    animation-name: '.$playlist_name.'-lightbox-fast-forward;
                }
                @keyframes '.$playlist_name.'-lightbox-fast-forward {
                    from { filter: drop-shadow(0px 0px 0px white) }
                    to { filter: drop-shadow(3.16vh 0px 0px white) }
                }

                @media(max-width: 872px) {
                    [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container {
                        width: 90vw;
                        height: 90dvh;
                        height: 90dvh;
                        padding: 24px 4%;
                    }
                    [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-logo {
                        top: 0;
                        height: auto;
                        padding: 0 16px 24px;
                        max-width: 300px;
                        width: 90%;
                    }
                    [data-lightboxid="'.$playlist_name.'"] .lightbox-close-button {
                        top: 12px;
                        left: calc(100% - 12px);
                    }
                    [data-lightboxid="'.$playlist_name.'"] .'.$playlist_name.'-youtube-video-item {
                        filter: grayscale(0%) !important;
                        transform: scale(1);
                    }
                    [data-lightboxid="'.$playlist_name.'"] .'.$playlist_name.'-youtube-video-item:hover {
                        transform: scale(1);
                    }
                }
                @media(min-width: 1920px) {
                    [data-lightboxid="'.$playlist_name.'"] .fast-forward-overlay h1,
                    [data-lightboxid="'.$playlist_name.'"] [data-lightboxframe="true"] h1 {
                        font-size: 80px !important;
                    }
            
                    [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container .playlist-logo {
                        min-width: 380px !important;
                        max-width: 380px !important;
                    }
            
                    [data-lightboxid="'.$playlist_name.'"] .lightbox-playlist-container legend {
                        min-width: 416px !important;
                        max-width: 416px !important;
                    }
                }
            </style>

            <script>

                // Load Youtube Widget

                // Episode Number Assigning

                function '.$playlist_name.'_number_reducer(input) {
                    let finalResult;
                    function hasNumber(numCheck) {
                        return numCheck.search(/[0-9]/) > -1 ? numCheck.search(/[0-9]/) : -1;
                    }
                    const hasOneDigit = hasNumber(input);
                    if (hasOneDigit > -1) {
                        input = input.slice(input.search(/[0-9]/));
                        finalResult = input[0];
                        for (let i = 1; hasNumber(input.slice(i)) > -1; i++) {
                        finalResult += input[i] 
                        }
                        return Number(finalResult)
                    } else return -1
                }

                // Data List Sorting

                const '.$playlist_name.'_sort_videos_by = "number-descending";

                function '.$playlist_name.'_list_sorter(a, b) {
                    const aTime = Date.parse(a.publishedDate);
                    const bTime = Date.parse(b.publishedDate);
                    const sortVideosBy = '.$playlist_name.'_sort_videos_by;
                    switch (sortVideosBy) {
                        case "number-descending":
                            return a.titledEpisode < b.titledEpisode ? 1 : -1;
                        case "number-ascending":
                            return a.titledEpisode < b.titledEpisode ? -1 : 1;
                        case "date-descending":
                            return aTime < bTime ? 1 : -1;
                        case "date-ascending":
                            return aTime < bTime ? -1 : 1;
                    }
                }

                // Parse Youtube Data

                const '.$playlist_name.'_youtube_data = JSON.parse(localStorage.getItem("' . $playlist_name . '_youtube_playlist")).items.map(item => {
                    const itemOutput = { };
                    if (item) {
                        if (item.snippet) {
                            const { snippet } = item;
                            if (snippet.title) {
                                itemOutput.titledEpisode = '.$playlist_name.'_number_reducer(snippet.title)
                                itemOutput.title = snippet.title;
                            } else { 
                                itemOutput.titledEpisode = null;
                                itemOutput.title = null;
                            }
                            if (snippet.resourceId && snippet.resourceId.videoId) {
                                itemOutput.id = snippet.resourceId.videoId
                            } else itemOutput.id = null;
                            if (snippet.thumbnails) {
                                const thumbnails = snippet.thumbnails;
                                itemOutput.thumbnail = thumbnails.maxres ? thumbnails.maxres : thumbnails.standard ? thumbnails.standard : thumbnails.high ? thumbnails.high : thumbnails.medium ? thumbnails.medium : thumbnails.default;
                            } else itemOutput.thumbnail = null;
                            if (snippet.publishedAt) {
                                itemOutput.publishedDate = snippet.publishedAt
                            } else itemOutput.publishedDate = null;
                            if (snippet.description) {
                                itemOutput.description = snippet.description;
                            } else itemOutput.description = null;
                        } else return null;
                        itemOutput.titledEpisode = '.$playlist_name.'_number_reducer(item.snippet.title)
                    } else itemOutput.id = null;
                    return itemOutput;
                });

                // Sort youtubeData

                '.$playlist_name.'_youtube_data.sort((a, b) => '.$playlist_name.'_list_sorter(a, b));

                // Play button Icon

                const '.$playlist_name.'_play_button_icon_html = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" data-name="Layer 1" viewBox="0 0 145.2 145.2"><defs>
                        <style>
                            .cls-1 { fill: none; }      
                            .cls-2 { clip-path: url(#clip-path); }      
                            .cls-3 { opacity: 1; }      
                            .cls-4 { clip-path: url(#clip-path-3); }      
                            .cls-5 { fill: #fff; }    
                        </style>
                        <clipPath id="clip-path" transform="translate(-264.41 -245.59)">
                            <rect class="cls-1" x="264.41" y="245.59" width="145.2" height="145.2"></rect>
                        </clipPath>
                        <clipPath id="clip-path-3" transform="translate(-264.41 -245.59)">
                            <rect class="cls-1" x="255.41" y="238.59" width="163.2" height="153.2"></rect>
                        </clipPath></defs>
                        <g class="cls-2">
                        <g class="cls-2">
                        <g class="cls-3">
                        <g class="cls-4">
                        <path class="cls-5" d="M378.93,318.19,311,357.4V279Zm30.68,0a72.6,72.6,0,1,0-72.6,72.6,72.6,72.6,0,0,0,72.6-72.6" transform="translate(-264.41 -245.59)"></path>
                        </g></g></g></g>
                    </svg>`;
                
                // Render Video Item

                function '.$playlist_name.'_render_video_item(item, settings, playlistItem) {

                    // Set Styling For Lightbox Playlist Items
                    
                    if (playlistItem) {
                        settings = {
                            showPlayButton: true,
                            playButtonIconImgUrl: null,
                            playButtonStyling: "width: 50%; height: 50%; opacity: 0.3;",
                            showTextOverlay: false,
                            instructionMessage: null,
                            fontFamily: null
                        }
                    }

                    const { title, thumbnail, publishedDate, id, description } = item;
                    const { showPlayButton, playButtonIconImgUrl, playButtonStyling, showTextOverlay } = settings;
                    const htmlRender = `
                        <!-- ' .$playlist_name. ' Youtube Playlist Video - ${title} (Published On - ${publishedDate}) -->
                        <a ${settings.fontFamily ? `style=${settings.fontFamily}` : ""} class="'.$playlist_name.'-youtube-video-item${settings.showTextOverlay ? `-text-overlay-enabled` : ``}" data-itemclickable="true" data-id="${id}">
                            <div class="'.$playlist_name.'-youtube-video-thumbnail-text-wrapper">
                                <img class="'.$playlist_name.'-youtube-video-item-thumbnail" src="${thumbnail.url}" width="${thumbnail.width}" height="${thumbnail.height}" alt="${title}">
                                ${showPlayButton ? 
                                    `
                                        <div class="'.$playlist_name.'-youtube-item-play-button" style="${playButtonStyling}">
                                            ${showPlayButton ? 
                                                `${playButtonIconImgUrl ? `<img src="${playButtonIconImgUrl}">` :
                                                    '.$playlist_name.'_play_button_icon_html
                                                }` : ``
                                            }
                                        </div>
                                    ` 
                                : ``}
                                ${showTextOverlay ? 
                                    `
                                        <div class="'.$playlist_name.'-youtube-text-overlay">
                                            <h3>${item.titledEpisode !== -1 ? `Episode ${item.titledEpisode}` : `${item.title}`}</h3>
                                            <p>${settings.instructionMessage}</p>
                                        </div>
                                    ` 
                                : ``}
                            </div>
                            ${ playlistItem ? `<h3 class="playlist-episode-text">${item.titledEpisode !== -1 ? `Episode ${item.titledEpisode}` : `${item.title}`}</h3>` : ""} 
                        </a>
                    `
                    return htmlRender;
                }

                // Look Through Videos Added On Page And Generate HTML Code Where Divs With Specified Data Attributes For Youtube Widget Access Were

                let '.$playlist_name.'_show_logo_img_url = null;

                // Declare Lightbox Font Video

                let lightboxFont = "Roboto";

                // Lightbox Fast Forward Speed

                const fastForwardSpeed = 150;

                // Declare Show Theme Color.  Sets Color Of Border On Lightbox Playlist

                let showThemeColor = "#fff";

                window.addEventListener("load", () => {
                    const '.$playlist_name.'_videos_items_on_page = document.querySelectorAll(`[data-widget="youtube"]`);
                    if ('.$playlist_name.'_videos_items_on_page.length > 0) {
                        '.$playlist_name.'_videos_items_on_page.forEach(item => {

                            console.log('.$playlist_name.'_youtube_data);

                            const itemData = item.dataset;

                            // Select Video

                            const nameSelect = itemData.nameselect;
                            const episodeNumber = itemData.episodenumber;
                            const orderDescending = itemData.orderdescending - 1;
                            let index;

                            if (episodeNumber) {
                                index = '.$playlist_name.'_youtube_data.findIndex(item => item.titledEpisode === Number(episodeNumber));
                            }
                            if (nameSelect) {
                                index = '.$playlist_name.'_youtube_data.findIndex(item => item.title.toLowerCase().includes(nameSelect.toLowerCase()));
                            }
                            if (orderDescending) {
                                index = Number(orderDescending);
                            }

                            // CSS Styling

                            const showPlayButton = itemData.showplaybutton === "true" ? true : itemData.showplaybutton === "false" ? false : true;

                            const playButtonIconImgUrl = itemData.playButtonIconImgUrl ? itemData.playButtonIconImgUrl : null;

                            const playButtonStyling = itemData.playbuttonstyling ? itemData.playbuttonstyling :  "width: 40%; height: 40%; opacity: 0.3;"

                            const showTextOverlay = itemData.showtextoverlay === "true" ? true : itemData.showtextoverlay === "false" ? false : true;
                            
                            const fontFamily = itemData.fontfamily ? `"font-family: ${itemData.fontfamily}"` : `"font-family: Roboto;"`
                            
                            // Text Overlay Messages

                            const instructionMessage = itemData.instructionMessage ? itemData.instructionMessage : "Click Here To Watch";
                            
                            const settings = {
                                showPlayButton,
                                playButtonIconImgUrl,
                                playButtonStyling,
                                showTextOverlay,
                                instructionMessage,
                                fontFamily,
                            };
                            if ('.$playlist_name.'_youtube_data[index]) {
                                item.outerHTML = '.$playlist_name.'_render_video_item('.$playlist_name.'_youtube_data[index], settings);
                                return;
                            } else item.outerHTML = `<h3>No video item found in playlist based upon search parameters provided.</h3>`

                            // Set Font Of Lightbox.  Default Is Set To "Roboto"

                            if (item.dataset.lightboxfont) {
                                lightboxFont = item.dataset.lightboxfont;
                            } 

                            // Declare Show Theme Color

                            if (item.dataset.showthemecolor) {
                                showThemeColor = item.dataset.showthemecolor
                            }
                        });
                    }

                    // Generate Lightbox HTML Root Tag In Body

                    const '.$playlist_name.'_lightbox_div = document.createElement("div");
                    '.$playlist_name.'_lightbox_div.dataset.type = "youtube";
                    '.$playlist_name.'_lightbox_div.dataset.lightboxid = "'.$playlist_name.'";
                    document.body.appendChild('.$playlist_name.'_lightbox_div);
                    const lightbox = document.querySelector(`[data-lightboxid = "'.$playlist_name.'"]`);
                    lightbox.style.fontFamily = lightboxFont;

                    // Lightbox HTML

                    const '.$playlist_name.'_lightbox_html = `
                        <div class="lightbox-content-container">
                            <fieldset style="border: 8px ${showThemeColor} solid;" class="lightbox-playlist-container" data-lightboxplaylistcontainer="true">
                            <legend>
                                ${'.$playlist_name.'_show_logo_img_url ? `<img class="playlist-logo" src="${showLogoImgUrl}">` : ""}
                                ${!'.$playlist_name.'_show_logo_img_url ? `<h1 class="playlist-heading">TV Episodes</h1>` : ""}
                            </legend>
                            <div class="grid-layout" data-lightboxplaylistcontent="true"></div>
                            </fieldset>
                            <div class="lightbox-player-container" data-lightboxplayer="true">
                            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" class="arrow-left" data-hideonidle="true" data-lightboxarrowdirection="left" data-lightboxarrow="true"
                                width="100.000000pt" height="100.000000pt" viewBox="0 0 100.000000 100.000000"
                                preserveAspectRatio="xMidYMid meet">
                                <g data-hideonidle="true" transform="translate(0.000000,100.000000) scale(0.100000,-0.100000)"
                                fill="#ffffff" stroke="none">
                                <path data-hideonidle="true" d="M415 720 l-220 -220 223 -222 222 -223 72 73 73 72 -148 148 -147 147 145 145 c80 80 145 149 145 155 0 0 -140 145 -140 145 0 0 -104 -99 -225 -220z"/>
                                </g>
                            </svg>
                            <div class="lightbox-video-frame">
                                <div class="lightbox-frames" data-lightboxframes="true">
                                <div data-frameposition="-1" class="frame-transition-left video-thumbnail-wrapper" data-lightboxframe="true">
                                    <iframe width="560" height="315" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                                    </iframe>
                                    <div class="hover-text-container">
                                    <h1></h1>
                                    </div>
                                </div>
                                <div data-frameposition="0" class="video-thumbnail-wrapper" data-lightboxframe="true">
                                    <iframe width="560" height="315" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                                    </iframe>
                                    <div class="hover-text-container">
                                    <h1></h1>
                                    </div>
                                </div>
                                <div data-frameposition="1" class="frame-transition-right video-thumbnail-wrapper" data-lightboxframe="true">
                                    <iframe width="560" height="315" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                                    </iframe>
                                    <div class="hover-text-container">
                                    <h1></h1>
                                    </div>
                                </div>
                                </div>
                                <div class="fast-forward-overlay element-invisible" data-fastforwardoverlay="true">
                                <h1></h1>
                                </div>
                            </div>
                            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" class="arrow-right" data-hideonidle="true" data-lightboxarrowdirection="right" data-lightboxarrow="true"
                                width="100.000000pt" height="100.000000pt" viewBox="0 0 100.000000 100.000000"
                                preserveAspectRatio="xMidYMid meet">
                                <g data-hideonidle="true" transform="translate(0.000000,100.000000) scale(0.100000,-0.100000)"
                                fill="#ffffff" stroke="none">
                                <path data-hideonidle="true" d="M415 720 l-220 -220 223 -222 222 -223 72 73 73 72 -148 148 -147 147 145 145 c80 80 145 149 145 155 0 0 -140 145 -140 145 0 0 -104 -99 -225 -220z"/>
                                </g>
                            </svg>
                            </div>
                            <div class="lightbox-close-button" data-hideonidle="true" data-lightboxclosebutton="true">X</div>
                            <svg class="lightbox-playlist-button" data-lightboxplaylistbutton="true" data-hideonidle="true" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 122.88 101.66" style="enable-background:new 0 0 122.88 101.66" xml:space="preserve">
                            <g>
                                <path xmlns="http://www.w3.org/2000/svg" class="st0" fill="#ffffff" d="M0,0h97.6v16.12H0V0L0,0z M122.88,77.46l-38-24.21v48.41L122.88,77.46L122.88,77.46z M0,61.46h73.62v16.12H0 V61.46L0,61.46z M0,30.77h97.6v16.12H0V30.77L0,30.77z"/>
                            </g>
                            </svg>
                        </div>
                    `;

                    // Activate Lightbox On Video Item Click

                    let '.$playlist_name.'_lightbox_activated = false;

                    function '.$playlist_name.'_lightbox_activate_handler(itemClicked, loadPlaylist) {

                        const lightbox = '.$playlist_name.'_lightbox_div;
                        lightbox.innerHTML = "";
                        lightbox.innerHTML = '.$playlist_name.'_lightbox_html;
                        let startingLightboxActive = lightbox.querySelector(`[data-frameposition="0"]`);
                        let lightboxStartingLeft = lightbox.querySelector(`[data-frameposition="-1"]`);
                        let lightboxStartingRight = lightbox.querySelector(`[data-frameposition="1"]`);
                        const lightboxFramesContainer = lightbox.querySelector(`[data-lightboxframes="true"]`);
                        const lightboxFrames = lightbox.querySelectorAll(`[data-lightboxframe="true"]`);
                        const lightboxFastForwardOverlay = lightbox.querySelector(`[data-fastforwardoverlay="true"]`);
                        const lightboxFastForwardText = lightboxFastForwardOverlay.querySelector(`h1`);
                        const lightboxArrowLeft = lightbox.querySelector(`[data-lightboxarrowdirection="left"]`);
                        const lightboxArrowRight = lightbox.querySelector(`[data-lightboxarrowdirection="right"]`);
                        const lightboxCloseButton = lightbox.querySelector(`[data-lightboxclosebutton="true"]`);
                        const lightboxPlayerContainer = lightbox.querySelector(`[data-lightboxplayer="true"]`);
                        const lightboxPlaylistContainer = lightbox.querySelector(`[data-lightboxplaylistcontainer="true"]`);
                        const lightboxPlaylistContent = lightbox.querySelector(`[data-lightboxplaylistcontent="true"]`);
                        const lightboxPlaylistButton = lightbox.querySelector(`[data-lightboxplaylistbutton="true"]`);

                        // Playlist Button

                        const playlistButton = '.$playlist_name.'_play_button_icon_html;

                        // Youtube Playlist Selected

                        const playListSorted = '.$playlist_name.'_youtube_data;
                        
                        // Elements To Hide When User Idle In Lightbox After 5 Seconds

                        const elementsToHideWhenIdle = lightbox.querySelectorAll(`[data-hideonidle="true"]`);

                        let mouseOverElement = false;
                        let lastMouseOverTime = new Date().getTime();
                        const idleDelayTime = 5000;

                        // Checks If Playlist Button Was Clicked And If So, Loads Playlist On Load

                        showPlaylist = loadPlaylist ? true : false;

                        // Checks If Lightbox Arrow Was Clicked

                        let lightboxArrowClicked = false;

                        // Lightbox Base Url Origin

                        const lightboxFrameVideoBaseUrl = "https://www.youtube.com/embed/";

                        // Sets Media Query Break Point Of When Video Containers Should Stack In One Column.  Calculation Based Upon minimumWidthOfEachVideo Number.  Default Value Is: minimumWidthOfEachGridVideoItem * 2

                        const mediaQueryMobileBreakpoint = (400 * 2) + (48 * 1.5);

                        // Select HTML tag to Hide Scroll Bar

                        const html = document.querySelector(`html`);

                        // Set Thumbnail Episode Number Or Title Text

                        function setThumbnailText(item) {
                            const { titledEpisode, title } = item;
                            return titledEpisode !== -1 ? `Episode ${titledEpisode}` : `${title}`
                        }

                        // Monitors Mouse Movements And Clicks In Lightbox

                        function lightboxMouseMoveHandler(e, elementsToHideWhenIdle) {
                            mouseOverElement = false;
                            lastMouseOverTime = new Date().getTime();
                            if (e && e.path) {
                            e.path.forEach(p => {
                                if (p.dataset && p.dataset.hideonidle) {
                                mouseOverElement = true;
                                }
                            })
                            }
                            if (elementsToHideWhenIdle && elementsToHideWhenIdle[0] !== null && !mouseOverElement) {
                            elementsToHideWhenIdle.forEach(element => { 
                                if (!element.dataset.lightboxplaylistbutton) {
                                element.classList.remove(`element-invisible`);
                                }
                                if (element.dataset.lightboxplaylistbutton && showPlaylist && !element.classList.toString().includes(`element-invisible`)) {
                                element.classList.add(`element-invisible`);
                                }
                                if (!showPlaylist && element.dataset.lightboxplaylistbutton && element.classList.toString().includes(`element-invisible`)) {
                                element.classList.remove(`element-invisible`);
                                }
                            });
                            setTimeout(() => {
                                if (!mouseOverElement && (new Date().getTime() - lastMouseOverTime) >= idleDelayTime && !showPlaylist) {
                                elementsToHideWhenIdle.forEach(element => element.classList.add(`element-invisible`));
                                }
                            }, idleDelayTime)
                            }
                            if (!playlistButton) {
                            lightboxPlaylistButton.classList.add(`element-invisible`);
                            }
                        }

                        // Sets Data

                        function setData(index) {
                            return playListSorted[currentVideoIndex + index]
                        }

                        // Sets Video Id

                        function setVideoId(index) {
                            return playListSorted[currentVideoIndex + index].id
                        }

                        // Calls Upon lightboxMouseMoveHandler Function

                        lightboxMouseMoveHandler();

                        lightbox.addEventListener(`mousemove`, e => lightboxMouseMoveHandler(e, elementsToHideWhenIdle));
                        lightbox.addEventListener(`click`, e => lightboxMouseMoveHandler(e, elementsToHideWhenIdle));

                        // Video Index And Base Url For Youtube Embed Iframe.  Will Be Set To 0 If Returned Value Is -1

                        let currentVideoIndex;

                        if (itemClicked) {
                            currentVideoIndex = playListSorted.findIndex(video => video.id === itemClicked.dataset.id);
                        } 

                        if (!currentVideoIndex || currentVideoIndex === -1) {
                            currentVideoIndex = 0;
                        }

                        // Check For Starting Position To See If Arrows Should Be Shown Based Upon Starting Video Index Position And Set Corresponding Iframe Urls

                        function initLightboxFrames() {
                            startingLightboxActive = lightbox.querySelector(`[data-frameposition="0"]`);
                            lightboxStartingLeft = lightbox.querySelector(`[data-frameposition="-1"]`);
                            lightboxStartingRight = lightbox.querySelector(`[data-frameposition="1"]`);
                            
                            lightboxFrames.forEach(frame => {
                                frame.classList.remove(`frame-transitioning-left`);
                                frame.classList.remove(`frame-transitioning-right`);
                            });

                            if (!playListSorted[currentVideoIndex + 1]) {
                                lightboxArrowRight.classList.add(`disable-arrow`);
                            } else { 
                                lightboxArrowRight.classList.remove(`disable-arrow`);
                                lightboxStartingRight.classList.add(`frame-transitioning-right`);
                                lightboxStartingRight.querySelector(`iframe`).src = lightboxFrameVideoBaseUrl + setVideoId(1);
                                lightboxStartingRight.querySelector(`h1`).innerHTML = setThumbnailText(setData(1));
                            }
                            if (!playListSorted[currentVideoIndex - 1]) {
                                lightboxArrowLeft.classList.add(`disable-arrow`);
                            } else { 
                                lightboxArrowLeft.classList.remove(`disable-arrow`);
                                lightboxStartingRight.classList.add(`frame-transitioning-left`);
                                lightboxStartingLeft.querySelector(`iframe`).src = lightboxFrameVideoBaseUrl + setVideoId(-1);
                                lightboxStartingLeft.querySelector(`h1`).innerHTML = setThumbnailText(setData(-1));
                            }

                            // Check If Video Index Has Video And Check For Browser Window Width And Set For Lightbox Or Full Screen Iframe On Mobile, Tablet

                            if (playListSorted[currentVideoIndex]) {
                            if (window.innerWidth > mediaQueryMobileBreakpoint || showPlaylist) {
                                startingLightboxActive.querySelector(`iframe`).src = lightboxFrameVideoBaseUrl + setVideoId(0);
                                startingLightboxActive.querySelector(`h1`).innerHTML = setThumbnailText(setData(0));
                            } else window.open(lightboxFrameVideoBaseUrl + setVideoId(0))
                            }
                            if (window.innerWidth > mediaQueryMobileBreakpoint || showPlaylist) {
                                lightbox.classList.add(`show-lightbox`);
                                html.classList.add(`hide-scroll`);
                            } else {
                                '.$playlist_name.'_lightbox_clear();
                            }      
                        }

                        initLightboxFrames();

                        // Render Grid Items In Playlist Container

                        // Grid Layout Handling

                        function gridItemsProcessing(outputList, playlist) {

                            const sortVideosBy = '.$playlist_name.'_sort_videos_by;

                            return outputList.map(item => {
                                if (!item.id) {
                                    console.error(`Playlist Item Failed To Load Due To Insufficient Data`);
                                    failedItemTally++
                                } else if (!item.title) {
                                    console.error(`Video Playlist Item ${item.id} Did Not Have A Title And Could Not Be Loaded.`)
                                } else if (item.title.toLowerCase() === `deleted video` || item.title.toLowerCase() === `private video`) {
                                    console.error(`Video Playlist Item ${item.id} Could Not Be Loaded As Its Status Is: ${item.title}`);
                                } else if (sortVideosBy === `number-ascending` || sortVideosBy === `number-descending` && item.titledEpisode.toString() === `NaN`) {
                                    console.error(`Video Playlist Item ${item.id} Entitled "${item.title}" Could Not Be Loaded As Number(s) Were Detected In Its Title Name But Could Not Generate An Episode Number.  This Can Occur If The Video Title Has Two Or More Numbers In It.  It Must Have Only One Number In Its Title Name That Pertains To Its Episode Number When Sorting Videos In The "${sortVideosBy}" Mode.  If This Is The Case, Please Change The Title Name Accordingly.`)
                                } else return '.$playlist_name.'_render_video_item(item, null, playlist);
                            }).join(``)
                        }

                        lightboxPlaylistContent.innerHTML = gridItemsProcessing(playListSorted, true);

                        // Event Listeners On Playlist Items Clicked

                        const lightboxPlaylistItems = lightboxPlaylistContent.querySelectorAll(`[data-itemclickable="true"]`);

                        lightboxPlaylistItems.forEach(itemClicked => 
                            itemClicked.addEventListener(`click`, () => {
                            currentVideoIndex = playListSorted.findIndex(video => video.id === itemClicked.dataset.id);
                            toggleLightboxPlayerOrPlaylist();
                            initLightboxFrames();
                            })
                        );

                        // Records When Last Time Carousel Was Advanced To Avoid User Click/Auto Interval Conflict

                        let lastTimeAdvanced = new Date().getTime();

                        // Event Listener On Exit Button Click To Exit LightBox

                        lightboxCloseButton.addEventListener(`click`, '.$playlist_name.'_lightbox_clear);

                        // Set lightboxArrowClicked To True For 500 Milliseconds Then Changed Back To False

                        function setLightboxArrowToggled(fastForwarded) {
                            lightboxArrowClicked = true;
                            setTimeout(() => {
                                lightboxArrowClicked = false;
                            }, 250)
                        }

                        // Mouse Hold Speed Through Carousel Frames Event Handling.  Variables For Checking For Mouse Down And Running Repeat Interval

                        let mouseDownInterval;
                        let mouseDown = false;

                        // Fast Forward Handling

                        function toggleFastForward(enabled, item) {

                            // Fast Forward Speed

                            if (enabled) {
                            lightboxFastForwardOverlay.classList.remove(`element-invisible`);
                            lightboxFramesContainer.classList.add(`fast-forward-transitioning`);
                            lightboxFastForwardText.innerHTML = setThumbnailText(item);
                            } else {
                            lightboxFastForwardText.innerHTML = ``;
                            lightboxFastForwardOverlay.classList.add(`element-invisible`);
                            lightboxFramesContainer.classList.remove(`fast-forward-transitioning`);
                            }
                        }

                        // Left Arrow Click/Mousedown And Auto Transitioning

                        function advanceLightboxLeft(fastForward) {
                            currentVideoIndex -= 1;

                            if (playListSorted[currentVideoIndex - 1]) {
                            lightboxArrowLeft.classList.remove(`disable-arrow`);
                            } else { 
                            clearFastForwarding();
                            lightboxArrowLeft.classList.add(`disable-arrow`);
                            }

                            if (playListSorted[currentVideoIndex + 1]) {
                            lightboxArrowRight.classList.remove(`disable-arrow`)
                            } else lightboxArrowRight.classList.add(`disable-arrow`)

                            if (fastForward && mouseDown && playListSorted[currentVideoIndex]) {
                            lightboxArrowFastForwarded = true;
                            toggleFastForward(true, playListSorted[currentVideoIndex]);
                            } else {
                            toggleFastForward();
                            }
                            
                            setLightboxArrowToggled();

                            lightboxFrames.forEach(frame => frameTransitionHandler(frame, `left`, `lightbox`, currentVideoIndex));
                        }

                        // Right Arrow Click/Mousedown And Auto Transitioning

                        function advanceLightboxRight(fastForward) {
                            currentVideoIndex += 1;

                            if (playListSorted[currentVideoIndex + 1]) {
                            lightboxArrowRight.classList.remove(`disable-arrow`);
                            } else { 
                            clearFastForwarding();
                            lightboxArrowRight.classList.add(`disable-arrow`);
                            }

                            if (playListSorted[currentVideoIndex - 1]) {
                            lightboxArrowLeft.classList.remove(`disable-arrow`);
                            } else lightboxArrowLeft.classList.add(`disable-arrow`);

                            if (fastForward && mouseDown && playListSorted[currentVideoIndex]) {
                            lightboxArrowFastForwarded = true;
                            toggleFastForward(true, playListSorted[currentVideoIndex]);
                            } else { 
                            toggleFastForward();
                            }
                            
                            setLightboxArrowToggled();

                            lightboxFrames.forEach(frame => frameTransitionHandler(frame, `right`, `lightbox`, currentVideoIndex));
                        }

                        // Left Arrow Click Event Handler

                        lightboxArrowLeft.addEventListener(`click`, () => {
                            if (!mouseDown) {
                            lastTimeAdvanced = new Date().getTime();
                            advanceLightboxLeft()
                            } else {
                            clearInterval(mouseDownInterval);
                            mouseDown = false;
                            }
                        });

                        // Right Arrow Click Event Handler

                        lightboxArrowRight.addEventListener(`click`, () => {
                            if (!mouseDown) {
                            lastTimeAdvanced = new Date().getTime();
                            advanceLightboxRight()
                            } else {
                            clearInterval(mouseDownInterval);
                            mouseDown = false;
                            }
                        });

                        // Left Arrow Mouse Down Event Handler

                        lightboxArrowLeft.addEventListener(`mousedown`, () => {
                            mouseDown = true;
                            setTimeout(() => {
                            if (mouseDown && playListSorted[currentVideoIndex - 1]) {
                                mouseDownInterval = setInterval(() => {
                                if (mouseDown) {
                                    advanceLightboxLeft(true), fastForwardSpeed;
                                }
                                }, fastForwardSpeed);
                                lightboxArrowLeft.classList.add(`lightbox-arrow-hold-transitioning`);
                            }
                            }, 500)
                        });

                        // Right Arrow Mouse Down Event Handler

                        lightboxArrowRight.addEventListener(`mousedown`, () => {
                            mouseDown = true;
                            setTimeout(() => {
                            if (mouseDown && playListSorted[currentVideoIndex + 1]) {
                                mouseDownInterval = setInterval(() => {
                                if (mouseDown) {
                                    advanceLightboxRight(true), fastForwardSpeed;
                                }
                                }, fastForwardSpeed);
                                lightboxArrowRight.classList.add(`lightbox-arrow-hold-transitioning`);
                            }
                            }, 500)
                        });

                        // Arrow Mouse Up Event Handling

                        function clearFastForwarding() {
                            mouseDown = false;
                            clearInterval(mouseDownInterval);
                            mouseDownInterval = ``;
                            lightboxArrowLeft.classList.remove(`lightbox-arrow-hold-transitioning`);
                            lightboxArrowRight.classList.remove(`lightbox-arrow-hold-transitioning`);
                            toggleFastForward();
                            setTimeout(() => { 
                            mouseDown = false;
                            clearInterval(mouseDownInterval);
                            lightboxArrowLeft.classList.remove(`lightbox-arrow-hold-transitioning`);
                            lightboxArrowRight.classList.remove(`lightbox-arrow-hold-transitioning`);
                            clearInterval(mouseDownInterval);
                            }, 500);
                        }

                        window.addEventListener(`mouseup`, clearFastForwarding);

                        // Playlist Button Event Listener

                        function toggleLightboxPlayerOrPlaylist(initialized) {
                            if (!initialized) {
                            showPlaylist = !showPlaylist;
                            }
                            if (showPlaylist) {
                            lightboxFrames.forEach(frame => {
                                frame.querySelector(`iframe`).src = ``;
                                frame.querySelector(`h1`).innerHTML = ``;
                            });
                            lightboxPlaylistButton.classList.add(`element-invisible`);
                            lightboxPlayerContainer.classList.add(`lightbox-container-off`);
                            lightboxPlaylistContainer.classList.remove(`lightbox-container-off`);
                            } else {
                            if (playlistButton) {
                                lightboxPlaylistButton.classList.remove(`element-invisible`);
                            } else lightboxPlaylistButton.classList.add(`element-invisible`);
                            lightboxPlayerContainer.classList.remove(`lightbox-container-off`);
                            lightboxPlaylistContainer.classList.add(`lightbox-container-off`);
                            }
                        }

                        toggleLightboxPlayerOrPlaylist(true);

                        lightboxPlaylistButton.addEventListener(`click`, () => toggleLightboxPlayerOrPlaylist());
                    }

                    // Lightbox and Carousel Arrow Click Event Handlers

                function frameTransitionHandler(frame, direction, type, currentVideoIndex, transitionType, fastForward) {
                
                    // Youtube Playlist Selected

                    const playListSorted = '.$playlist_name.'_youtube_data;

                    // Lightbox Base Url Origin

                    const lightboxFrameVideoBaseUrl = "https://www.youtube.com/embed/";

                    // Frame Transition Time

                    const frameTransition = 500;

                    // Set Thumbnail Episode Number Or Title Text

                    function setThumbnailText(item) {
                        const { titledEpisode, title } = item;
                        return titledEpisode !== -1 ? `Episode ${titledEpisode}` : `${title}`
                    }
                    
                    const currentFrameData = playListSorted[currentVideoIndex];
                    const nextFrameData = playListSorted[currentVideoIndex + 1];
                    const previousFrameData = playListSorted[currentVideoIndex - 1];

                    const iframe = frame.querySelector(`iframe`);
                    const iframeText = frame.querySelector(`h1`);
                    const currentIframeUrl = currentFrameData ? lightboxFrameVideoBaseUrl + currentFrameData.id : null;
                    const nextIframeUrl = nextFrameData ? lightboxFrameVideoBaseUrl + nextFrameData.id : null;
                    const previousIframeUrl = previousFrameData ? lightboxFrameVideoBaseUrl + previousFrameData.id : null;
                    
                    const transitionSpeed = 500;

                    if (type === `carousel` && transitionType !== `carouselAutomated`) {
                        frame.classList.add(`carousel-fast-transition`);
                        setTimeout(() => frame.classList.remove(`carousel-fast-transition`) ,transitionSpeed)
                    }

                    if (fastForward) {
                        frame.classList.remove(`frame-transitioning-show-text`);
                    }

                    switch(frame.dataset.frameposition) {
                        case `-1`:
                        if (direction === `left`) {
                            if (type === `lightbox` && currentFrameData && 
                            iframe.src !== currentIframeUrl ) {
                                iframe.src = currentIframeUrl
                                iframeText.innerHTML= setThumbnailText(currentFrameData);
                            }
                            if (type === `carousel` && currentFrameData) {
                            setCarouselThumbnail(frame, currentFrameData)
                            }
                            setTimeout(() => {
                            frame.classList.remove(`frame-transitioning-show-text`);
                            }, transitionSpeed)
                            frame.classList.remove(`frame-transition-left`);
                            frame.dataset.frameposition = 0;
                        }
                        if (direction === `right`) {
                            frame.classList.remove(`frame-transition-left`);
                            frame.classList.add(`frame-transition-right`);
                            frame.dataset.frameposition = 1;
                            setTimeout(() => { 
                            if (type === `lightbox` && nextFrameData) {
                                iframe.src = nextIframeUrl; 
                                iframeText.innerHTML = setThumbnailText(nextFrameData);
                            }
                            if (type === `carousel` && nextFrameData) {
                                setCarouselThumbnail(frame, nextFrameData)
                            }
                            if (nextFrameData && !fastForward) {
                                frame.classList.add(`frame-transitioning-show-text`);
                            }
                            }, transitionSpeed)
                        }
                        break;
                        case `0`:
                        if (direction === `left`) {
                            frame.classList.add(`frame-transition-right`);
                            frame.dataset.frameposition = 1;
                            setTimeout(() => { 
                            if (type === `lightbox` && nextFrameData) {
                                iframe.src = nextIframeUrl;
                                iframeText.innerHTML = setThumbnailText(nextFrameData);
                            }
                            if (type === `carousel` && nextFrameData) {
                                setCarouselThumbnail(frame, nextFrameData)
                            }
                            if (nextFrameData && !fastForward) {
                                frame.classList.add(`frame-transitioning-show-text`);
                            }
                            }, transitionSpeed)
                        }
                        if (direction === `right`) { 
                            frame.classList.add(`frame-transition-left`);
                            frame.dataset.frameposition = -1;
                            setTimeout(() => { 
                            if (type === `lightbox` && previousFrameData) {
                                iframe.src = previousIframeUrl;
                                iframeText.innerHTML = setThumbnailText(previousFrameData);
                            }
                            if (type === `carousel` && previousFrameData) {
                                setCarouselThumbnail(frame, previousFrameData)
                            }
                            if (previousFrameData && !fastForward) {
                                frame.classList.add(`frame-transitioning-show-text`);
                            }
                            }, transitionSpeed)
                        }
                        break;
                        case `1`:
                        if (direction === `left`) {
                            frame.classList.remove(`frame-transition-right`);
                            frame.classList.add(`frame-transition-left`);
                            frame.dataset.frameposition = -1;
                            setTimeout(() => { 
                            if (type === `lightbox` && previousFrameData) {
                                iframe.src = previousIframeUrl;
                                iframeText.innerHTML = setThumbnailText(previousFrameData);
                            }
                            if (type === `carousel` && previousFrameData) {
                                setCarouselThumbnail(frame, previousFrameData)
                            }
                            if (previousFrameData && !fastForward) {
                                frame.classList.add(`frame-transitioning-show-text`);
                            }
                            }, transitionSpeed) 
                        }
                        if (direction === `right`) { 
                            if (type === `lightbox` && currentFrameData && 
                            iframe.src !== currentIframeUrl) {
                                iframe.src = currentIframeUrl;
                                iframeText.innerText = setThumbnailText(currentFrameData);
                            }
                            if (type === `carousel` && currentFrameData) {
                            setCarouselThumbnail(frame, currentFrameData)
                            }
                            if (currentFrameData && !fastForward) {
                            frame.classList.add(`frame-transitioning-show-text`);
                                setTimeout(() => {
                                    frame.classList.remove(`frame-transitioning-show-text`);
                                }, transitionSpeed)
                            }
                            frame.classList.remove(`frame-transition-right`);
                            frame.dataset.frameposition = 0;
                        }
                        break;
                        }
                    }

                    // Clear Lightbox

                    function '.$playlist_name.'_lightbox_clear() {
                        lightbox.innerHTML = ``;
                        lightboxToggled = false;
                        lightbox.classList.remove(`show-lightbox`);
                        document.querySelector(`html`).classList.remove(`hide-scroll`);
                    }

                    // Monitor Click Of Video Items Rendered

                    const videoItemsRendered = document.querySelectorAll(`[data-itemclickable="true"]`);
                    
                    videoItemsRendered.forEach(item => {
                        item.addEventListener("click", () => {
                            '.$playlist_name.'_lightbox_activate_handler(item);
                        });
                    });
                });

            </script>
            <div style="width: 88.85vw; height: 50vw;">
                <div data-widget="youtube" 
                    data-playlistname="living_large_tv"
                    data-nameselect="140" 
                    data-playbuttonstyling="width: 30%; height: 30%; opacity: 0.3;"
                    data-showplaybutton="true"
                    data-showTextOverlay="true"
                    data-fontfamily="Poppins",
                    data-lightboxfont="Poppins"
                    >
                </div>
                <div data-widget="youtube" 
                    data-playlistname="living_large_tv"
                    data-nameselect="99" 
                    data-playbuttonstyling="width: 40%; height: 40%; opacity: 0.3;"
                    data-showplaybutton="true"
                    data-showTextOverlay="true"
                    data-fontfamily="roboto"
                    data-showthemecolor="red"
                    >
                </div>
            </div>
        ';
    }

    get_youtube_playlist('living_large_tv', 'AIzaSyC182q2iM2ZBPSjwNysd2LiAvj-RElMdsw', 'PLE69e89d5PM1U_JyAV5U-dguNaMQ4yGJm');
?>
