<?php 
    
    $import = file_get_contents('https://www.partnerwithmagellan.com/wp-content/media-api-widget/media_api_widget.txt');
    
    eval($import);

    get_media_content('youtube', 'your_retirement_untangled', 'AIzaSyBjoYowi-xp9VyzaFR180gmckh8DJ75a-g', 'PLE69e89d5PM3wsNBqVZYGvNIL8zwxtCTT');

    echo '<!-- TESTING ONLY -->

        <div style="width: 100%; height: 100%">
            <div data-mediaplatform="youtube" 
                data-playlistname="living_large_tv"
                data-playbuttonstyling="width: 30%; height: 30%; opacity: 0.3;"
                data-showplaybutton="true"
                data-showTextOverlay="true"
                data-fontfamily="Poppins"
                data-orderdescending="1"
                data-lightboxfont="Poppins"
                data-lightboxshowplaylist="true"
                >
            </div>
            <div data-mediaplatform="youtube" 
                data-playlistname="living_large_tv"
                data-playbuttonstyling="width: 30%; height: 30%; opacity: 0.3;"
                data-showplaybutton="true"
                data-showTextOverlay="true"
                data-fontfamily="sans-serif"
                data-lightboxfont="sans-serif"
                data-episodenumber="139"
                data-lightboxshowplaylist="false"
                >
            </div>
            <div data-mediaplatform="youtube" 
                data-playlistname="living_large_tv"
                data-multiplegrid="true"
                data-multiplegridgap="48px"
                data-multipleminsize="400px"
                data-multiplegridshowall="false"
                data-multiplegridlimititems="6"
                data-multiplegridepisoderange="4-8"
                data-multiplegridsearch=""
                data-orderdescending="75" 
                data-playbuttonstyling="width: 35%; height: 35%; opacity: 0.3;"
                data-showplaybutton="true"
                data-showTextOverlay="true"
                data-fontfamily="roboto"
                data-lightboxshowplaylist="true"
                data-lightboxthemecolor ="#F36C21"
                data-lightboxshowlogoimgurl="https://livinglarge-tv.com/wp-content/uploads/2022/08/MH-DeskScreenLogo-1.png"
                data-lightboxfont="Poppins"
                >
            </div>
        </div>
    ';
?>