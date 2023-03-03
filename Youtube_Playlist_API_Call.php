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
                .'.$playlist_name.'-youtube-video-item, .'.$playlist_name.'-youtube-video-item-text-overlay-enabled {
                    cursor: pointer;
                    position: relative;
                    width: 100%;
                    height: 100%;
                    aspect-ratio: 1.777 / 1;
                    display: block;
                    overflow: hidden;
                    background: #000000;
                    box-sizing: border-box;
                }
                .'.$playlist_name.'-youtube-video-item img, .'.$playlist_name.'-youtube-video-item-text-overlay-enabled img, 
                .'.$playlist_name.'-youtube-video-item svg, .'.$playlist_name.'-youtube-video-item-text-overlay-enabled svg  {
                    transition: 0.5s;
                }
                .'.$playlist_name.'-youtube-video-item-text-overlay-enabled:hover > img:nth-child(1) {
                    opacity: 0.25;
                }
                .'.$playlist_name.'-youtube-video-item-text-overlay-enabled:hover svg, .'.$playlist_name.'-youtube-video-item:hover > img:nth-child(2) {
                    opacity: 0;
                }
                .'.$playlist_name.'-youtube-video-item-thumbnail {
                    object-fit: cover;
                    max-width: 100%;
                    max-height: 100%;
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
                    gap: clamp(0.75rem, 15%, 3rem);
                    background: #00000050;
                    opacity: 0;
                    transition: 0.5s;
                    padding: 24px 4%;
                    box-sizing: border-box;
                    font-family: inherit !important;
                }
                .'.$playlist_name.'-youtube-text-overlay > * {
                    margin: 0;
                    text-align: center;
                    color: white;
                }
                .'.$playlist_name.'-youtube-text-overlay h3 {
                    font-size: min(10vw, 2.25rem);
                    font-family: inherit !important;
                }
                .'.$playlist_name.'-youtube-text-overlay p {
                    font-size: min(7vw, 1.375rem);
                    font-family: inherit !important;
                }
                .'.$playlist_name.'-youtube-text-overlay:hover {
                    opacity: 1;
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

                function '.$playlist_name.'_list_sorter(a, b) {
                    const aTime = Date.parse(a.publishedDate);
                    const bTime = Date.parse(b.publishedDate);
                    const sortVideosBy = "number-descending";
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

                // Render Video Item

                function '.$playlist_name.'_render_video_item(item, settings) {
                    const { title, thumbnail, publishedDate, id, description } = item;
                    const { showPlayButton, playButtonIconImgUrl, playButtonStyling, showTextOverlay } = settings;
                    const htmlRender = `
                        <!-- ' .$playlist_name. ' Youtube Playlist Video - ${title} (Published On - ${publishedDate}) -->
                        <a style=${settings.fontFamily} class="'.$playlist_name.'-youtube-video-item${settings.showTextOverlay ? `-text-overlay-enabled` : ``}" data-itemclickable="true" data-id="${id}">
                            <img class="'.$playlist_name.'-youtube-video-item-thumbnail" src="${thumbnail.url}" width="${thumbnail.width}" height="${thumbnail.height}" alt="${title}">
                            ${showPlayButton ? 
                                `
                                    <div class="'.$playlist_name.'-youtube-item-play-button" style="${playButtonStyling}">
                                        ${showPlayButton ? 
                                            `${playButtonIconImgUrl ? `<img src="${playButtonIconImgUrl}">` :
                                                `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" data-name="Layer 1" viewBox="0 0 145.2 145.2"><defs>
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
                                                </svg>`
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
                        </a>
                    `
                    return htmlRender;
                }

                // Look Through Videos Added On Page And Generate HTML Code Where Divs With Specified Data Attributes For Youtube Widget Access Were

                let '.$playlist_name.'_show_logo_img_url = null;

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

                            const playButtonStyling = itemData.playbuttonstyling ? itemData.playbuttonstyling :  "width: 40%; height: 40%; opacity: 0.5;"

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
                        });
                    }

                    // Generate Lightbox HTML Root Tag In Body

                    const lightboxDiv = document.createElement("div");
                    lightboxDiv.dataset.type = "youtube";
                    lightboxDiv.dataset.lightboxid = "'.$playlist_name.'";
                    document.body.appendChild(lightboxDiv);
                    const lightbox = document.querySelector(`[data-lightboxid = "'.$playlist_name.'"]`);

                    // Lightbox HTML

                    const lightboxHTML = `
                        <div class="lightbox-content-container">
                            <fieldset class="lightbox-playlist-container" data-lightboxplaylistcontainer="true">
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

                    let lightboxToggled = false;

                    function lightboxActivateHandler(itemClicked) {
                        lightbox.innerHTML = "";
                        lightbox.innerHTML = lightboxHTML;
                    }

                    // Monitor Click Of Video Items Rendered

                    const videoItemsRendered = document.querySelectorAll(`[data-itemclickable="true"]`);
                    
                    videoItemsRendered.forEach(item => {
                        item.addEventListener("click", () => {
                            lightboxActivateHandler(item);
                        });
                    });
                });

            </script>
            <div style="width: 88.85vw; height: 50vw;">
                <div data-widget="youtube" 
                    data-playlistname="living_large_tv"
                    data-nameselect="140" 
                    data-playbuttonstyling="width: 30%; height: 30%; opacity: 0.5;"
                    data-showplaybutton="true"
                    data-showTextOverlay="true"
                    data-fontfamily="Poppins"
                    >
                </div>
                <div data-widget="youtube" 
                    data-playlistname="living_large_tv"
                    data-nameselect="99" 
                    data-playbuttonstyling="width: 40%; height: 40%; opacity: 0.5;"
                    data-showplaybutton="true"
                    data-showTextOverlay="true"
                    data-fontfamily="Roboto"
                    >
                </div>
            </div>
        ';
    }

    get_youtube_playlist('living_large_tv', 'AIzaSyC182q2iM2ZBPSjwNysd2LiAvj-RElMdsw', 'PLE69e89d5PM1U_JyAV5U-dguNaMQ4yGJm');
?>