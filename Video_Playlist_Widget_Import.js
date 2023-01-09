// VERSION 1.9

function initializeVideoPlaylist(inputData, elementRoot) { // ALL VIDEO PLAYLIST API WIDGET CODE WRAPPED IN FUNCTION SO ALL VARIABLES ARE LOCALLY SCOPED TO AVOID ERRORS WITH UTILIZING THE WIDGET MORE THAN ONCE ON THE SAME PAGE
  
  // ALL HTML, CSS, JAVASCRIPT CODE FOR THE VIDEO PLAYLIST WIDGET API IS PRESENT HERE, MINUS THE ROOT WIDGET HTML CONTAINER.  A CONTAINER MUST BE PRESENT IN THE HTML WITH THE ID OF THE WIDGET FOR THE "widgetRoot" DOM SELECTOR TO INITIATE THE WIDGET.

  // DOM ROOT DIV CREATION FOR CODE INSERTION.  inputData ARGUMENT MUST CONTAIN AN id KEY AND MUST HAVE ITS CORRESPONDING PARENT SCRIPT TAG WITH A data-playlist-id ATTRIBUTE SET TO IT ALSO
  
  const existingWidgetsLength = document.querySelectorAll('[data-type="video-playlist-widget"]').length;
  
  const widgetId = `video-playlist-${existingWidgetsLength}`;
  const widgetIdSelector = `#${widgetId}`;
  const createdDiv = document.createElement('div');
  createdDiv.id = widgetId;
  elementRoot.outerHTML = createdDiv.outerHTML;
  
  // Variable Declarations

    // Value Declarations.  Elementors Unlimited Elements Widget Creator Inserts User Input Values Into Variables With Curly Bracket Surroundings.

      // API Keys

      function backupAPIKeys() {
        let apiKeyPicked;
        if (playlistService === 'youtube') {
          const youtubeAPIKeys = ['AIzaSyC182q2iM2ZBPSjwNysd2LiAvj-RElMdsw', 'AIzaSyA_pSMRbSVW5c9RhCxmLM4bL2ExqWw_leg', 'AIzaSyA-uTYg5ut6MW2fklFi5gNEAIImLFfUGaM', 'AIzaSyCI7Y5K9XF-l1G0mb7qGNp3vd7-1vm2PrU']
          apiKeyPicked = youtubeAPIKeys[Math.floor(Math.random() * youtubeAPIKeys.length)];
        }
        return apiKeyPicked
      }

      // Input Data Variables Declarations
    
      const resultLimitPerRequest = typeof inputData.resultLimitPerRequest === 'number' ? inputData.resultLimitPerRequest : 50; // Youtube API Has A Limit Of 50 Results Per Request.
      const limitVideos = typeof inputData.limitVideos === 'boolean' ? inputData.limitVideos : true; // Limit Videos Displayed.  Default Value Is: true
      const maxResults = typeof inputData.maxResults === 'number' ? inputData.maxResults : 6; // If limitVideos Is Set To: true, Limit Number Of Videos Displayed.  Default Value Is: 6
      const filterByName = typeof inputData.filterByName === 'boolean' ? inputData.filterByName : false; // Filter Video Results Based On Characters In Title.  Default Value Is: false
      const filterNameParameters = typeof inputData.filterNameParameters === 'string' ? inputData.filterNameParameters.toLowerCase() : ''; // If filterByName Is Set To: true, Filter String Parameters For filterByName.  Default Value Is: ''
      const sortVideosBy = typeof inputData.sortVideosBy === 'string' ? inputData.sortVideosBy : 'number-descending'; // How Video Items Should Be Sorted.  Default Value Is: 'number-descending'
      const listByRange = typeof inputData.listByRange === 'boolean' ? inputData.listByRange : false; // Show Specific Range Of Videos By Episode Number.  Default Value Is: false
      const fromEpisodeNumber = typeof inputData.fromEpisodeNumber === 'number' ? inputData.fromEpisodeNumber : 1; // If listByRange Is Set To: true, Set Starting Episode Number To Filter From. Default Value Is: 1
      const toEpisodeNumber = typeof inputData.toEpisodeNumber === 'number' ? inputData.toEpisodeNumber : 1; // If listByRange Is Set To: true, Set Starting Episode Number To Filter From.  Default Value Is: 1
      const showInformationBelow = typeof inputData.showInformationBelow === 'boolean' ? inputData.showInformationBelow : false; // Show Text Information About Video Below Video Thumbnail.  Default Value Is: false
      const showNonNumberedEpisodesOnly = typeof inputData.showNonNumberedEpisodesOnly === 'boolean' ? inputData.showNonNumberedEpisodesOnly : false; // Shows Videos That Do Not Have An Episode Number Only. Default Value Is: false 
      const hideNonNumberedVideos = typeof inputData.hideNonNumberedVideos === 'boolean' ? inputData.hideNonNumberedVideos : true; // Hides Non-Numbered Videos In Playlist.  Default Value Is: true   
      const timeStorageInterval = typeof inputData.timeStorageInterval === 'number' ? inputData.timeStorageInterval * 60000 : 3600000; // Sets Time Interval That Another API Call Request Can Be Made Instead Of Loading From Local Storage In Minutes That Is Multiplied By 60000 To Convert Minutes To Milliseconds.  Default Value is 60
      const showPlayButtons = typeof inputData.showPlayButtons === 'boolean' ? inputData.showPlayButtons : true; // Sets If Play Button Icons Should Be Shown Over Video Thumbnails.  Default Value Is: true
      const showVideoInfo = typeof inputData.showVideoInfo === 'boolean' ? inputData.showVideoInfo : false; // Sets If Text Information About Video Should Be Displayed Below Video Thumbnail.  Default Value Is: false
      const showDescriptionText = typeof inputData.showDescriptionText === 'boolean' ? inputData.showDescriptionText : false; // If showVideoInfo Is Set To True, Sets If Video Description Text Should Be Displayed.  Default Value Is: false
      const showAllInLightbox = typeof inputData.showAllInLightbox === 'boolean' ? inputData.showAllInLightbox : true; // Determines If Lightbox Will Allow User To See All Videos On PlayList If True. If False, User Can Only View Videos Shown On Page  
      const fastForwardSpeed = typeof inputData.fastForwardSpeed === 'number' ? inputData.fastForwardSpeed : 150; // Set The Speed That Frames Should Be Fast Forwarded Through On Carousel And Lightbox When User Holds Down Arrow.
      const showLogoImgUrl = typeof inputData.showLogoImgUrl === 'string' ? inputData.showLogoImgUrl : ''; // Show Logo Image URL For Playlist Heading.  If Nothing, Then TV Episodes Text Will Be Shown 
      let playlistButton = typeof inputData.playlistButton === 'boolean' ? inputData.playlistButton : true; // Sets If Playlist Button Will Be Shown On Page.  Default Value Is: true
      const playlistLayout = typeof inputData.playlistLayout === 'grid' ? 'grid' : inputData.playlistLayout === 'carousel' ? 'carousel' : 'grid'; // Determines The Layout Of Videos On Page.  Default value is 'grid'.  Other value is 'carousel'
      const playlistService = typeof inputData.playlistService === 'youtube' ? 'youtube' : inputData.playlistService === 'vimeo' ? 'vimeo' : 'youtube'; // Determines If Video Playlist Is Coming From.  Default Value Is 'youtube'
      const apiKey = typeof inputData.apiKey === 'string' ? inputData.apiKey : backupAPIKeys(); // API Key.  Loads Backup Keys If No Key Is Passed In
      const playlistId = inputData.playlistId; // Playlist Id.  REQUIRED FOR WIDGET TO WORK.

      // Input CSS Variable Declarations

      const fontFamily = typeof inputData.fontFamily === 'string' ? inputData.fontFamily : 'Roboto'; // Sets Font Family Style For All Text In Widget.  Default Value Is: 'Roboto'
      const thumbnailFontHeadingSize = typeof inputData.thumbnailFontHeadingSize === 'string' ? inputData.thumbnailFontHeadingSize : '2.25rem'; // Sets Font Size Of Video Thumbnail Episode Heading Text.  Default Value is: 2rem
      const fontBodySize = typeof inputData.fontBodySize === 'string' ? inputData.fontBodySize : '1.375rem'; // Sets Font Size Of Video Thumbnail Instruction Text.  Default Value is: 1.5rem
      const playButtonColor = typeof inputData.playButtonColor === 'string' ? inputData.playButtonColor : '#ffffff'; // Sets Color Of Play Button Icon Over Video Thumbnail.  Default Value IS: '#ffffff'
      const gapBetweenGridVideos = typeof inputData.gapBetweenGridVideos === 'number' ? inputData.gapBetweenGridVideos : 48; // Sets Spacing Between Video Items In Grid Layout In Pixels.  Default Value Is: 48
      const gapBetweenPlaylistVideos = typeof inputData.gapBetweenPlaylistVideos === 'number' ? inputData.gapBetweenPlaylistVideos : 48; // Sets Spacing Between Video Items In Playlist Layout In Pixels.  Default Value Is: 48
      const videoContainerBorderThickness = typeof inputData.videoContainerBorderThickness === 'string' ? inputData.videoContainerBorderThickness : '0px'; // Sets Video Container Border Thickness: Default Value Is: '0px'
      const videoContainerBorderColor = typeof inputData.videoContainerBorderColor === 'string' ? inputData.videoContainerBorderColor : '#000000'; // Sets Video Container Border Color: Default Value Is: '#000000'
      const dropShadowValues = typeof inputData.dropShadowValues === 'string' ? inputData.dropShadowValues: '4px 4px 12px'; // Sets Drop-Shadow Values For Video Container.  Default Value Is: 0px 0px 0px
      const dropShadowColor = typeof inputData.dropShadowColor === 'string' ? inputData.dropShadowColor : '#01010147'; // Sets Drop-Shadow Color For Video Container.  Default Value Is: '#00000036'
      const textBelowThumbnailTopBottomMargin = typeof inputData.textBelowThumbnailTopBottomMargin === 'string' ? inputData.textBelowThumbnailTopBottomMargin : '24px'; // Sets Text Below Video Thumbnail Margins On Top And Bottom. Default Value Is: '24px'
      const textBelowThumbnailSideMargins = typeof inputData.textBelowThumbnailSideMargins === 'string' ? inputData.textBelowThumbnailSideMargins : '24px'; // Sets Text Below Video Thumbnail Margins On Sides.  Default Value Is: '24px'
      const titleTextColorBelowThumbnail = typeof inputData.titleTextColorBelowThumbnail === 'string' ? inputData.titleTextColorBelowThumbnail : '#000000'; // Sets Color Of Video Title Text Below Video Thumbnail.  Default Value Is: '#000000'
      const spaceBetweenTitleAndDescriptionBelowThumbnail = typeof inputData.spaceBetweenTitleAndDescriptionBelowThumbnail === 'string' ? inputData.spaceBetweenTitleAndDescriptionBelowThumbnail : '24px'; // Sets Vertical Space Gap Between Title Text And Description Text Below Video Thumbnail.  Default Value Is: '24px'
      const descriptionTextColorBelowThumbnail = typeof inputData.descriptionTextColorBelowThumbnail === 'string' ? inputData.descriptionTextColorBelowThumbnail : '#000000'; // Sets Color Of Description Text Below Video Thumbnail.  Default Value Is: '#000000'
      const imageDarkeningOpacity = typeof inputData.imageDarkeningOpacity === 'string' ? inputData.imageDarkeningOpacity : '20'; // Sets How Dark The Video Thumbnail Should Get Opacity On Mouse Hover.  Default Value Is: '20'
      const textOverThumbnailColor = typeof inputData.textOverThumbnailColor === 'string' ? inputData.textOverThumbnailColor : '#ffffff'; // Sets Color Of Text Over Video Thumbnail On Mouse Hover.  Default Value Is: '#ffffff'
      const textContainerMargin = typeof inputData.textContainerMargin === 'string' ? inputData.textContainerMargin : '24px'; // Sets Padding Of Text Container That Cover Over Video Thumbnail For Text On Mouse Hover.  Default Value Is: '24px'
      const spaceBetweenTitleAndClickToPlayText = typeof inputData.spaceBetweenTitleAndClickToPlayText === 'string' ? inputData.spaceBetweenTitleAndClickToPlayText : '2.25rem'; // Sets Vertical Spacing Between Episode Number Or Video Title Text And Instruction Text On Mouse Hover Over Thumbnail.  Default Value Is: '40px'
      const playButtonSizing = typeof inputData.playButtonSizing === 'string' ? inputData.playButtonSizing : '30%'; // Sets Sizing Of Play Button Icon Over Video Thumbnail Relative To The HEight Of The Video Thumbnail In Percentage.  Default Value Is: '35%'
      const playButtonOpacity = typeof inputData.playButtonOpacity === 'number' ? inputData.playButtonOpacity / 100 : .75; // Sets Opacity Of Play Button Icon Over Video Thumbnail.  Divides Number By 100 To Convert Percentage To Decimal. Default Value Is: 75
      const minimumWidthOfEachGridVideoItem = typeof inputData.minimumWidthOfEachGridVideoItem === 'number' ? inputData.minimumWidthOfEachGridVideoItem : 400; // Sets Minimun Width Size Of Each Video.  Default Value Is: 480
      const frameTransition = typeof inputData.frameTransition === 'number' ? inputData.frameTransition : 500; // Sets Transition Time Of LightBox Carousel Frame Transitioning In Milliseconds
      const carouselTransition = typeof inputData.carouselTransition === 'number' ? inputData.carouselTransition : 2000; // Sets Transition Time Of Carousel Layout Frame Transitioning In Milliseconds
      const carouselDelay = typeof inputData.carouselDelay === 'number' ? inputData.carouselDelay : 6000; // Sets Delay Carousel Layout Between Transitioning In Milliseconds
      const carouselWidth = typeof inputData.carouselWidth === 'number' ? inputData.carouselWidth : 40 // Set Width Of Carousel Layout Thumbnails In REMs
      const carouselArrowsColor = typeof inputData.carouselArrowsColor === 'string' ? inputData.carouselArrowsColor : '#949494' // Sets Color Of Carousel Layout Arrows And Text Overlay During Fast Forward
      const showThemeColor = typeof inputData.showThemeColor === 'string' ? inputData.showThemeColor : '#949494' // Sets Theme Color For LightBox Playlist Outline
      const playlistButtonColor = typeof inputData.playlistButtonColor === 'string' ? inputData.playlistButtonColor : '#FFF' // Sets Playlist Button Text Color
      const playlistButtonBackgroundColor = typeof inputData.playlistButtonBackgroundColor === 'string' ? inputData.playlistButtonBackgroundColor : typeof inputData.showThemeColor === 'string' ? inputData.showThemeColor : '#949494';  
      const playlistButtonPadding = typeof inputData.playlistButtonPadding === 'string' ? inputData.playlistButtonPadding : '1em 2em' // Sets Playlist Button Padding.  Default Value is: '1em 2em'
      const carouselArrowSize = typeof inputData.carouselArrowSize === 'string' ? inputData.carouselArrowSize : '48px' ; // Sets Arrows Size On Carousel Layout In Percentage
      const thumbnailAspectRatio = typeof inputData.thumbnailAspectRatio === 'string' ? inputData.thumbnailAspectRatio : '1.777 / 1' // Sets Aspect Ratio Of Video Thumbnails.  Default Value Is '1.777 / 1'
      const lightboxPlayerIconSize = typeof inputData.lightboxPlayerIconSize === 'number' ? inputData.lightboxPlayerIconSize : 4.75; // Sets Size Of Lightbox Arrows In Viewport Height.  Default Value Is 4.75

      // Programming Variables Declarations

      let storedPlaylistRetrieved = false; // Determines If Data Was Loaded From Local Storage.  Utilized In getPlaylistItems Function.  Default Staring Value Is: false
      const instructionMessage = window.innerWidth > 1200 ? 'Click Here To Watch' : 'Tap Here To Watch'; // Instruction Message Text.  Default Value is: 'Click Here To Watch'
      let playListSorted = []; // Captures All Videos After Filtering And Sorting Before They're Reduced To Output Amount For Showing All Videos When Navigating Through Lightbox
      let pageOutputList = []; // The Actual List Of Video That Will Be Shown On The Page (Grid Or Carousel Layout)
      let lightboxToggled = false; // Used To Determine If Lightbox Is Toggled
      const playlistButtonText = window.innerWidth > 1200 ? 'Click Here For More Episodes' : 'Tap Here For More Episodes' // Playlist Button Text
      let showPlaylist = false; // Toggles Lightbox Playlist On And Off
      const lightboxFrameVideoBaseUrl = playlistService === 'youtube' ? 'https://www.youtube.com/embed/' : ''; // Video Base Url For Lightbox Video Iframes
      const baseUrl = playlistService === 'youtube' ? `https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=` : ``;
      const fullPath = playlistService === 'youtube' ? `${baseUrl}${playlistId}&key=${apiKey}&maxResults=1000` : ``;

      // CSS Programming Variable Declarations

      const mediaQueryMobileBreakpoint = (minimumWidthOfEachGridVideoItem * 2) + (gapBetweenGridVideos * 1.5); // Sets Media Query Break Point Of When Video Containers Should Stack In One Column.  Calculation Based Upon minimumWidthOfEachVideo Number.  Default Value Is: minimumWidthOfEachGridVideoItem * 2

    // Error Handling Variables

      const apiErrMsg = `An error occured during the ${playlistService} playlist API call.  The same ${playlistService} playlist was found in local storage and has been loaded instead.`;
      let failedItemTally = 0; 

    // Initialization Loading Spinner

      const loadingSpinner = `<svg version="1.1" id="L7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve"
            class="loading-spinner">
          <path fill="#fff" d="M31.6,3.5C5.9,13.6-6.6,42.7,3.5,68.4c10.1,25.7,39.2,38.3,64.9,28.1l-3.1-7.9c-21.3,8.4-45.4-2-53.8-23.3
          c-8.4-21.3,2-45.4,23.3-53.8L31.6,3.5z">
              <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
          </path>
          <path fill="#fff" d="M42.3,39.6c5.7-4.3,13.9-3.1,18.1,2.7c4.3,5.7,3.1,13.9-2.7,18.1l4.1,5.5c8.8-6.5,10.6-19,4.1-27.7
          c-6.5-8.8-19-10.6-27.7-4.1L42.3,39.6z">
              <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="-360 50 50" repeatCount="indefinite"></animateTransform>
          </path>
          <path fill="#fff" d="M82,35.7C74.1,18,53.4,10.1,35.7,18S10.1,46.6,18,64.3l7.6-3.4c-6-13.5,0-29.3,13.5-35.3s29.3,0,35.3,13.5
          L82,35.7z">
              <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
          </path>
        </svg>
      `;

    // HTML Base Container Initialization.  Loading Spinner Is Included On Initialization

      const widgetRoot = document.querySelector(`${widgetIdSelector}`);

      widgetRoot.innerHTML = 
        `
          <style class="playlist-styling"></style>
          <div class="video-layout-container">
              ${loadingSpinner}
            <div style="height: 400px; width: 100%;"></div>
              </div>
      `;

    // Lightbox Container Created And Appended To Body Of HTML

      const lightboxDiv = document.createElement('div');
      lightboxDiv.dataset.lightboxid = widgetIdSelector;
      document.body.appendChild(lightboxDiv);

    // DOM Element Selector Declarations

      const stylesContainer = document.querySelector(`${widgetIdSelector} .playlist-styling`)
      const playlistItems = document.querySelector(`${widgetIdSelector} .video-layout-container`);
      const lightbox = document.querySelector(`[data-lightboxid="${widgetIdSelector}"]`);
      const html = document.querySelector('html');

  // END - Variable Declarations

  // CSS Generation 

    const playlistStyling = `
      @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
      ${widgetIdSelector} > *,
      [data-lightboxid="${widgetIdSelector}"] > * {
        box-sizing: border-box;
      }
      ${widgetIdSelector} {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      ${widgetIdSelector} h2 {
        display: block;
        width: 100vw;
        text-align: center;
        font-size: 1.5rem;
        color: ${showThemeColor};
        font-family: ${fontFamily} !important;
        line-height: 1.5em;
      }
      ${widgetIdSelector} h3 {
        margin: 0!important;
        font-family: ${fontFamily} !important;
        font-weight: 700;
      }
      ${widgetIdSelector} .grid-layout {
        display: grid;
        grid-template-rows: min-content;
        gap: ${gapBetweenGridVideos}px;
      }
      [data-lightboxid="${widgetIdSelector}"] .grid-layout {
        display: grid;
        grid-template-rows: min-content;
        gap: ${gapBetweenPlaylistVideos}px;
      }
      [data-lightboxid="${widgetIdSelector}"] .grid-layout {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      }
      ${widgetIdSelector} .loading-spinner {
        filter: drop-shadow(2px 4px 6px #00000090);
        position: absolute;
        width: 50vw;
        height: 50vw;
        max-width: 240px;
        max-height: 240px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
      }
      ${widgetIdSelector} .video-item {
        max-width: 100%;
        text-align: center;
        overflow: hidden;
        border: ${videoContainerBorderThickness} ${videoContainerBorderColor} solid;
        width: 100%;
        height: 100%;
        filter: drop-shadow(${dropShadowValues} ${dropShadowColor});
        animation-name: ${widgetId}-fade-in;
        animation-duration: 1s;
      }
      @keyframes ${widgetId}-fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
      }
      ${widgetIdSelector} .video-thumbnail-wrapper,
      [data-lightboxid="${widgetIdSelector}"] .video-thumbnail-wrapper {
        background: #000000;
        position: relative;
        cursor: pointer;
        width: 100%;
        ${!showInformationBelow ? `height: 100% !important;` : ''}
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper {
        position: relative;
        cursor: pointer;
      }
      ${widgetIdSelector} .video-info-text-container {
        padding: ${textBelowThumbnailTopBottomMargin} ${textBelowThumbnailSideMargins};	
      }
      ${widgetIdSelector} .video-info-text-container h3 {
        color: ${titleTextColorBelowThumbnail};
        margin-bottom: ${spaceBetweenTitleAndDescriptionBelowThumbnail};
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .video-info-text-container span {
        color: ${descriptionTextColorBelowThumbnail};
      }
      ${widgetIdSelector} .video-thumbnail-wrapper:hover img {
        opacity: calc(${imageDarkeningOpacity} / 100);
      }
      ${widgetIdSelector} .video-thumbnail-wrapper:hover svg {
        opacity: 0;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper:hover .hover-text-container {
        opacity: 1;
      }
      ${widgetIdSelector} .frame-transitioning-show-text img,
      [data-lightboxid="${widgetIdSelector}"] .frame-transitioning-show-text img {
        opacity: calc(${imageDarkeningOpacity} / 100);
      }
      ${widgetIdSelector} .frame-transitioning-show-text iframe,
      [data-lightboxid="${widgetIdSelector}"] .frame-transitioning-show-text iframe {
        opacity: calc(${imageDarkeningOpacity} / 100);
      }
      ${widgetIdSelector} .frame-transitioning-show-text svg,
      [data-lightboxid="${widgetIdSelector}"] .frame-transitioning-show-text svg {
        opacity: 0 !important;
      }
      ${widgetIdSelector} .frame-transitioning-show-text .hover-text-container,
      [data-lightboxid="${widgetIdSelector}"] .frame-transitioning-show-text .hover-text-container {
        opacity: 1 !important;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper img,
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper img
      {
        object-fit: cover;
        width: 100%!important;
        height: inherit !important;
        min-width: 100%!important;
        max-height: 100%!important;
        transition: 0.75s;
        aspect-ratio: ${thumbnailAspectRatio};
      }
      [data-lightboxid="${widgetIdSelector}"] .video-item {
        filter: grayscale(75%);
        transition: 0.5s;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }
      [data-lightboxid="${widgetIdSelector}"] .video-item:hover svg{
        opacity: 0.75 !important;
      }
      [data-lightboxid="${widgetIdSelector}"] .video-item:hover {
        filter: grayscale(0%) !important;
        transform: scale(1.25);
      }
      [data-lightboxid="${widgetIdSelector}"] .video-thumbnail-wrapper iframe,
      [data-lightboxid="${widgetIdSelector}"] .video-thumbnail-wrapper h1 {
        transition: 0.75s;
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper .hover-text-container,
      [data-lightboxid="${widgetIdSelector}"] .hover-text-container {
        position: absolute;
        box-sizing: border-box;
        width: 100%;
        height: 100%;
        color: ${textOverThumbnailColor};
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: ${textContainerMargin};
        display: flex;
        flex-direction: column;
        justify-content: center;
        opacity: 0;
        transition: 0.5s;
        gap: ${spaceBetweenTitleAndClickToPlayText};
      }
      ${widgetIdSelector} .video-thumbnail-wrapper .hover-text-container,
      ${widgetIdSelector} .carousel-layout [data-fastforwardoverlay="true"],
      [data-lightboxid="${widgetIdSelector}"] .hover-text-container, 
      [data-lightboxid="${widgetIdSelector}"] .lightbox-video-frame [data-fastforwardoverlay="true"] {
        font-family: ${fontFamily} !important;
      }
      [data-lightboxid="${widgetIdSelector}"] .hover-text-container {
        z-index: 0;
        pointer-events: none;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper .hover-text-container h3,
      ${widgetIdSelector} .carousel-video-frame [data-fastforwardoverlay="true"] h3 {
        font-size: ${thumbnailFontHeadingSize} !important;
        font-weight: 600;
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper .hover-text-container p {
        font-size: ${fontBodySize} !important;
        font-weight: 400;
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .video-info-text-container {
        opacity: 1;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper .hover-text-container h3 {
        margin: 0;
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper .hover-text-container p {
        margin: 0;
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper svg,
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper svg
      {
        position: absolute;
        transition: 0.5s;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
      }
      ${widgetIdSelector} .video-thumbnail-wrapper svg {
        width: ${playButtonSizing};
        height: ${playButtonSizing};
        opacity: ${playButtonOpacity};
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-video-thumbnail-wrapper svg {
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
      .hide-header {
        display: none!important;
      }
      .frame-transition-left {
        transform: translateX(-60%) rotateY(90deg) scale(0);
        opacity: 0;
        pointer-events: none !important;
      }
      .frame-transition-right {
        transform: translateX(60%) rotateY(-90deg) scale(0);
        opacity: 0;
        pointer-events: none !important;
      }
      ${widgetIdSelector} .carousel-arrow-hold-transitioning {
        animation-iteration-count: infinite;
        animation-duration: ${fastForwardSpeed / 1000}s;
        animation-name: ${widgetId}-carousel-fast-forward;
      }
      @keyframes ${widgetId}-carousel-fast-forward {
        from { filter: drop-shadow(0px 0px 0px ${carouselArrowsColor}) }
        to { filter: drop-shadow(calc(${carouselArrowSize} / 1.75) 0px 0px ${carouselArrowsColor}) }
      }
      .lightbox-arrow-hold-transitioning {
        animation-iteration-count: infinite;
        animation-duration: ${fastForwardSpeed / 1000}s;
        animation-name: ${widgetId}-lightbox-fast-forward;
      }
      @keyframes ${widgetId}-lightbox-fast-forward {
        from { filter: drop-shadow(0px 0px 0px white) }
        to { filter: drop-shadow(${lightboxPlayerIconSize / 1.5}vh 0px 0px white) }
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
      [data-lightboxid="${widgetIdSelector}"] {
        width: 100%;
        height: 100vh;
        display: none;
        background: #000000E2;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100000;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-content-container {
        max-width: 220vh;
        margin: 0 auto;
        position: relative;
        height: 100%;
        width: 100%;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container,
      [data-lightboxid="${widgetIdSelector}"] .lightbox-player-container {
        width: 100%;
        height: 100%;
        margin: 0 auto;
        box-sizing: border-box;
        position: absolute;
        top: 0;
        transition: 0.5s;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container {
        max-width: 1920px;
        max-height: 90vh;
        width: calc(100% - ${lightboxPlayerIconSize}vh * 4.5);
        height: calc(100% - ${lightboxPlayerIconSize}vh * 1.5);
        min-width: 75vw;
        min-height: 90vh;
        background: #00000090;
        overflow-y: auto;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        padding: 24px 40px 24px 40px;
        border: 8px ${showThemeColor} solid;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-heading {
        font-family: ${fontFamily};
        color: white;
        text-align: left;
        font-size: 3rem;
        padding: 0 1.5rem;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-logo {
        top: 0;
        height: auto !important;
        padding: 0 16px 24px;
        min-width: 18vw !important;
        max-width: 30vh !important;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container legend {
        min-width: 22vw;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container::-webkit-scrollbar {
        width: 8px;
        box-sizing: border-box;
        background: transparent;
        border-radius: 0px;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container::-webkit-scrollbar-thumb {
        background: transparent;
        border-right: 8px solid white;
        height: 25%;
        max-height: 100%;
        border-radius: 8px;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-player-container {
        padding: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-video-frame {
        margin: 0 3%;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-player-container .lightbox-video-frame iframe {
        width: 100%;
        height: 100%;
        position: absolute;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-video-frame .fast-forward-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
      }
      [data-lightboxid="${widgetIdSelector}"] [data-itemclickable="true"] {
        cursor: pointer;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-video-frame .fast-forward-overlay h1,
      [data-lightboxid="${widgetIdSelector}"] .lightbox-video-frame [data-lightboxframe="true"] h1 {
        color: white;
        font-size: 9.75vh;
        font-family: ${fontFamily} !important;
        text-align: center;
        font-weight: 700;
        z-index: 100;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-close-button {
        color: white;
        position: fixed;
        top: ${lightboxPlayerIconSize / 3.5}vh;
        left: calc(100% - ${lightboxPlayerIconSize / 2.75}vh);
        transform: translateX(-100%);
        font-family: 'Ruda', sans-serif;
        font-weight: 700;
        font-style: normal;
        padding: ${lightboxPlayerIconSize / 5}vh;
        font-size: ${lightboxPlayerIconSize / 1.5}vh;
        cursor: pointer;
        border: ${lightboxPlayerIconSize / 9}vh white solid;
        border-radius: 50%;
        width: 12vw;
        height: 12vw;
        max-width: ${lightboxPlayerIconSize / 1.5}vh;
        max-height: ${lightboxPlayerIconSize / 1.5}vh;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: 0.25s;
        z-index: 10 !important;
        box-sizing: content-box;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-button {
        position: fixed;
        top: ${lightboxPlayerIconSize / 2.25}vh;
        left: ${lightboxPlayerIconSize / 2.25}vh;
        max-width: ${lightboxPlayerIconSize}vh;
        max-height: ${lightboxPlayerIconSize}vh;
        width: 15vh;
        height: 15vh;
        transition: 0.25s;
        cursor: pointer;
        z-index: 10 !important;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-close-button:hover {
        transform: translateX(-100%) scale(1.25);
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-button:hover {
        transform: scale(1.25);
      } 
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-button:active {
        transform: scale(0.9);
      } 
      [data-lightboxid="${widgetIdSelector}"] .lightbox-player-container svg {
        min-width: 4vw;
        max-height: 6.664vw;
        width: ${lightboxPlayerIconSize * 0.65}vh;
        height: ${lightboxPlayerIconSize * 1.082}vh;
        cursor: pointer;
        z-index: 3;
        transition: 0.25s;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-video-frame {
        width: 78vw!important;
        height: 43.9vw!important;
        max-height: 90vh!important;
        max-width: 160vh!important;
        position: relative;
      }
      [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-episode-text {
        font-family: ${fontFamily} !important;
        color: white;
        margin: 1rem 0 0;
        text-align: center;
        font-size: 1.75rem;
        font-weight: 700;
      }
      .arrow-left {
        cursor: pointer;
      }
      .arrow-left:hover {
        transform: scale(1.25);
        transition: 0.25s;
      }
      .arrow-right {
        cursor: pointer;
        transform: rotate(180deg);
      }
      .arrow-right:hover {
        transform: scale(1.25) rotate(180deg);
        transition: 0.25s;
      }
      .arrow-left:active {
        transform: translate(-10%);
        transition: 0.1s;
      }
      .arrow-right:active {
        transform: translate(10%) rotate(180deg);
        transition: 0.1s;
      }
      ${widgetIdSelector} .carousel-fast-transition {
        transition: ${frameTransition / 1000}s !important;
      }
      .carousel-zero-transition a {
        transition: 0.05s !important;
      }
      .lightbox-container-off {
        opacity: 0;
        pointer-events: none !important;
        transition: 0.5s;
        overflow: hidden !important;
      }
      [data-lightboxid="${widgetIdSelector}"] [data-lightboxframe="true"] {
        width: 100%;
        height: 100%;
        border: 0px;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0px 0px 48px black;
        filter: drop-shadow(rgba(255, 255, 255, 0.44) 0px 0px 80px);
        transition: ${frameTransition / 1000}s !important;
        z-index: 10;
      }
      ${widgetIdSelector} .carousel-layout {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
      }
      ${widgetIdSelector} .carousel-layout [data-carouselarrow="true"] g,
      ${widgetIdSelector} .carousel-layout [data-fastforwardoverlay="true"] h1
      {
        fill: ${carouselArrowsColor};
        color: ${carouselArrowsColor};
        font-weight: 700;
        font-family: ${fontFamily} !important;
      }
      ${widgetIdSelector} .carousel-layout a {
        position: absolute;
        transition: ${carouselTransition / 1000}s;
      }
      ${widgetIdSelector} [data-carouselarrow="true"] {
        width: ${carouselArrowSize};
      }
      ${widgetIdSelector} .carousel-layout .carousel-video-frame {
        position: relative;
        height: ${carouselWidth / 1.777}rem;
        width: ${carouselWidth}rem;
        margin: 0 3%;
      }
      ${widgetIdSelector} .playlist-button-page {
        font-family: ${fontFamily};
        display: block;
        margin: ${gapBetweenGridVideos}px auto 0;
        padding: ${playlistButtonPadding};
        font-size: ${fontBodySize};
        background: ${playlistButtonBackgroundColor};
        border-width: 0px;
        color: ${playlistButtonColor};
        cursor: pointer;
        transition: 0.25s;
      }
      ${widgetIdSelector} .playlist-button-page:hover {
        transform: scale(1.1);
      }
      @media(max-width: ${mediaQueryMobileBreakpoint}px) {
        ${widgetIdSelector} .grid-layout {
          grid-template-columns: 1fr;
        }
        [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container {
          width: 90vw;
          height: 90vh;
          padding: 24px 4%;
        }
        [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-heading {
          font-family: ${fontFamily};
          color: white;
          text-align: left;
          font-size: 2.15rem;
          padding: 0 1rem;
        }
        [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-logo {
          top: 0;
          height: auto;
          padding: 0 16px 24px;
          max-width: 300px;
          width: 90%;
          margin-top: 0;
        }
        [data-lightboxid="${widgetIdSelector}"] .lightbox-close-button {
          top: 12px;
          left: calc(100% - 12px);
        }
        [data-lightboxid="${widgetIdSelector}"] .video-item {
          filter: grayscale(0%) !important;
          transform: scale(1);
        }
        [data-lightboxid="${widgetIdSelector}"] .video-item:hover {
          transform: scale(1);
        }
        ${widgetIdSelector} .playlist-button-page {
          padding: 1em;
          
        }
      }
      @media(min-width: ${mediaQueryMobileBreakpoint + 1}px) {
        ${widgetIdSelector} .grid-layout {
          grid-template-columns: repeat(auto-fill, minmax(${minimumWidthOfEachGridVideoItem}px, 1fr));
        }
      }
      @media(max-width: ${carouselWidth * 20}px) {
        ${widgetIdSelector} .carousel-layout .carousel-video-frame {
          width: 72vw;
          height: calc(72vw / 1.777);
        }
        ${widgetIdSelector} .carousel-layout .carousel-video-frame .hover-text-container {
          gap: 7vw;
        }
        ${widgetIdSelector} .carousel-layout .carousel-video-frame h3 {
          font-size: 8vw;
        }
        ${widgetIdSelector} .carousel-layout .carousel-video-frame p {
          font-size: 5vw;
        }
      }
      @media(max-width: 576px) {
        ${widgetIdSelector} .video-layout-container .video-item h3 {
          font-size: 8.5vw;
        }
        ${widgetIdSelector} .video-layout-container .video-item p,
        ${widgetIdSelector} .video-layout-container .playlist-button-page {
          font-size: 6vw;
        }
        ${widgetIdSelector} .video-layout-container .hover-text-container {
          gap: 8vw;
        }
      }
      @media(min-width: 1920px) {
        [data-lightboxid="${widgetIdSelector}"] .fast-forward-overlay h1,
        [data-lightboxid="${widgetIdSelector}"] [data-lightboxframe="true"] h1 {
          font-size: 80px !important;
        }

        [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container .playlist-logo {
          min-width: 380px !important;
          max-width: 380px !important;
        }

        [data-lightboxid="${widgetIdSelector}"] .lightbox-playlist-container legend {
          min-width: 416px !important;
          max-width: 416px !important;
        }
      }
    `
    stylesContainer.innerHTML = playlistStyling;

  // END - CSS Generation

  // HTML Generation Methods

    // Lightbox HTML

    const lightboxHTML = `
      <div class="lightbox-content-container">
        <fieldset class="lightbox-playlist-container" data-lightboxplaylistcontainer="true">
          <legend>
            ${showLogoImgUrl ? `<img class="playlist-logo" src="${showLogoImgUrl}">` : ''}
            ${!showLogoImgUrl ? `<h1 class="playlist-heading">TV Episodes</h1>` : ''}
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

    // Carousel Layout HTML

      const carouselHTML = `
        <div class="carousel-layout">
          <svg version="1.0" xmlns="http://www.w3.org/2000/svg" class="arrow-left" data-hideonidle="true" data-carouselarrowdirection="left" data-carouselarrow="true"
            width="100.000000pt" height="100.000000pt" viewBox="0 0 100.000000 100.000000"
            preserveAspectRatio="xMidYMid meet">
            <g data-hideonidle="true" transform="translate(0.000000,100.000000) scale(0.100000,-0.100000)"
            fill="#ffffff" stroke="none">
            <path data-hideonidle="true" d="M415 720 l-220 -220 223 -222 222 -223 72 73 73 72 -148 148 -147 147 145 145 c80 80 145 149 145 155 0 0 -140 145 -140 145 0 0 -104 -99 -225 -220z"/>
            </g>
          </svg>
          <div class="carousel-video-frame">
            <div class="carousel-video-items" data-carouselvideoitems="true">
              <a class="video-item frame-transition-left video-thumbnail-wrapper" data-itemclickable="true" data-frameposition="-1" class="frame-transition-left">
                <img src="" width="" height="" alt="">
                ${showPlayButtons ? playButtonIcon() : ''}
                <div class="hover-text-container">
                  <h3></h3>
                  ${instructionMessage === '' ? '' : `<p>${instructionMessage}</p>`}
                </div>
              </a>
              <a class="video-item video-thumbnail-wrapper" data-itemclickable="true" data-frameposition="0">
                <img src="" width="" height="" alt="">
                ${showPlayButtons ? playButtonIcon() : ''}
                <div class="hover-text-container">
                  <h3></h3>
                  ${instructionMessage === '' ? '' : `<p>${instructionMessage}</p>`}
                </div>
              </a>
              <a class="video-item frame-transition-right video-thumbnail-wrapper" data-itemclickable="true" data-frameposition="1" class="frame-transition-right">
                <img src="" width="" height="" alt="">
                ${showPlayButtons ? playButtonIcon() : ''}
                <div class="hover-text-container">
                  <h3></h3>
                  ${instructionMessage === '' ? '' : `<p>${instructionMessage}</p>`}
                </div>
              </a>
            </div>
            <div class="fast-forward-overlay element-invisible" data-fastforwardoverlay="true">
              <h3></h3>
            </div>
          </div>
            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" class="arrow-right" data-hideonidle="true" data-carouselarrowdirection="right" data-carouselarrow="true"
              width="100.000000pt" height="100.000000pt" viewBox="0 0 100.000000 100.000000"
              preserveAspectRatio="xMidYMid meet">
              <g data-hideonidle="true" transform="translate(0.000000,100.000000) scale(0.100000,-0.100000)"
              fill="#ffffff" stroke="none">
              <path data-hideonidle="true" d="M415 720 l-220 -220 223 -222 222 -223 72 73 73 72 -148 148 -147 147 145 145 c80 80 145 149 145 155 0 0 -140 145 -140 145 0 0 -104 -99 -225 -220z"/>
              </g>
            </svg>
        </div>
      `;

    // Video Item Play Button Icon HTML

    function playButtonIcon() {
      const playbackButton = `
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve">
        <metadata> Svg Vector Icons : http://www.onlinewebfonts.com/icon </metadata>
        <g><path d="M500,10C229.4,10,10,229.4,10,500c0,270.6,219.4,490,490,490c270.6,0,490-219.4,490-490C990,229.4,770.6,10,500,10z M691.7,538.7L398.6,772.9c-20.7,16.5-37.5,7.5-37.5-20.2V249.7c0-27.7,16.8-36.7,37.5-20.1l293.1,234.3C717.6,484.5,717.6,518,691.7,538.7z" style="
        fill: ${playButtonColor};
        "/></g>
      </svg>
    `
      return playbackButton
    }

    // Grid Layout Video Item HTML

    function renderGridItem(item, playlistItem) {
      const { title, thumbnail, publishedDate, id, description } = item;
      const htmlRender = 
        `
          <!-- ${title} (Published On - ${publishedDate}) -->
          <a class="video-item" data-itemclickable="true" data-id="${id}">
            <div class="${!playlistItem ? 'video-thumbnail-wrapper' : 'playlist-video-thumbnail-wrapper'}">
              <img src="${thumbnail.url}" width="${thumbnail.width}" height="${thumbnail.height}" alt="${title}">
              ${showPlayButtons ? playButtonIcon() : ''}
              ${ !playlistItem ? 
                `<div class="hover-text-container">
                  <h3>${setThumbnailText(item)}</h3>
                  ${instructionMessage === '' ? '' : `<p>${instructionMessage}</p>`}
                </div>` : ''
              }
            </div>
            ${ showVideoInfo  && !playlistItem ? 
              `<div class="video-info-text-container">
                <h3>${setThumbnailText(item)}</h3>
                ${description !== '' && showDescriptionText ? `<span>${description}</span>` : ''}
              </div>`
              : ''
            }
            ${ playlistItem ? `<h3 class="playlist-episode-text">${setThumbnailText(item)}</h3>` : ''} 
          </a>
        `;
      return htmlRender
    }

  // Set Thumbnail Episode Number Or Title Text

    function setThumbnailText(item) {
      const { titledEpisode, title } = item;
      return titledEpisode !== -1 ? `Episode ${titledEpisode}` : `${title}`
    }

  // Event Methods

    function setCarouselThumbnail(element, item) {
      const { thumbnail } = item;
      element.querySelector('img').src = thumbnail.url;
      element.querySelector('img').width = thumbnail.width;
      element.querySelector('img').height = thumbnail.height;
      element.querySelector('h3').innerHTML = setThumbnailText(item);
      element.dataset.videoid = item.id;
    }

  // Carousel Layout Video Event Handling

    function carouselEventHandling() {

      // Select DOM Elements

      const carouselContainer = document.querySelector(`${widgetIdSelector} .carousel-layout`);
      const carouselFramesContainer = carouselContainer.querySelector('[data-carouselvideoitems="true"]');
      const carouselFastForwardOverlay = carouselContainer.querySelector('[data-fastforwardoverlay="true"]');
      const carouselFastForwardText = carouselFastForwardOverlay.querySelector('h3');
      const carouselArrowLeft = carouselContainer.querySelector(`[data-carouselarrowdirection="left"]`);
      const carouselArrowRight = carouselContainer.querySelector(`[data-carouselarrowdirection="right"]`);
      const carouselArrows = [carouselArrowLeft, carouselArrowRight];
      const startingCarouselFrame = carouselContainer.querySelector('[data-frameposition="0"]');
      const leftCarouselFrame = carouselContainer.querySelector('[data-frameposition="-1"]');
      const rightCarouselFrame = carouselContainer.querySelector('[data-frameposition="1"]');
      const carouselFrames = carouselContainer.querySelectorAll('.video-item');
      
      // Video Index Start

      let currentVideoIndex = 0;

      // Initialize Carousel Frame Images

      // Starting Frame

      setCarouselThumbnail(startingCarouselFrame, playListSorted[currentVideoIndex]);

      // Left Frame

      setCarouselThumbnail(leftCarouselFrame, playListSorted[playListSorted.length - 1]);

      // Right Frame

      setCarouselThumbnail(rightCarouselFrame, playListSorted[currentVideoIndex + 1] ? 
        playListSorted[currentVideoIndex + 1] : playListSorted[currentVideoIndex]
      );

      // Records When Last Time Carousel Was Advanced To Avoid User Click/Auto Interval Conflict

      let lastTimeAdvanced = new Date().getTime();

      // Fast Forward Handling

      function toggleFastForward(enabled, item) {
        if (enabled) {
          carouselFastForwardOverlay.classList.remove('element-invisible');
          carouselFramesContainer.classList.add('fast-forward-transitioning');
          carouselFastForwardText.innerHTML = setThumbnailText(item);
        } else {
          carouselFastForwardText.innerHTML = '';
          carouselFastForwardOverlay.classList.add('element-invisible');
          carouselFramesContainer.classList.remove('fast-forward-transitioning');
        }
      }

      // Left Arrow Click/Mousedown Event Handling

      function advanceCarouselLeft(transitionType, fastForward) {
        if (playListSorted[currentVideoIndex - 1]) {
          currentVideoIndex -= 1;
        } else currentVideoIndex = playListSorted.length - 1

        if (fastForward && mouseDown) {
          toggleFastForward(true, playListSorted[currentVideoIndex]);
        } else toggleFastForward()

        carouselFrames.forEach(frame => 
          frameTransitionHandler(frame, 'left', 'carousel', currentVideoIndex, transitionType, fastForward)
        );
      }

      // Right Arrow Click/Mousedown And Auto Transitioning 

      function advanceCarouselRight(transitionType, fastForward) {
        if (playListSorted[currentVideoIndex + 1]) {
          currentVideoIndex += 1;
        } else currentVideoIndex = 0;

        if (fastForward && mouseDown) {
          toggleFastForward(true, playListSorted[currentVideoIndex]);
        } else toggleFastForward()

        carouselFrames.forEach(frame => 
          frameTransitionHandler(frame, 'right', 'carousel', currentVideoIndex, transitionType, fastForward)
        );
      }

      // Left Arrow Click Event Listener

      carouselArrowLeft.addEventListener('click', () => {
        if (!mouseDown) {
          lastTimeAdvanced = new Date().getTime();
          advanceCarouselLeft()
        } else {
          mouseDown = false;
        }
        clearInterval(mouseDownInterval);
      });

      // Right Arrow Click Event Listener

      carouselArrowRight.addEventListener('click', () => {
        if (!mouseDown) {
          lastTimeAdvanced = new Date().getTime();
          advanceCarouselRight()
        } else {
          mouseDown = false;
        }
        clearInterval(mouseDownInterval);
      });

      // Mouse Hold Speed Through Carousel Frames Event Handling.  Variables For Checking For Mouse Down And Running Repeat Interval

      let mouseDownInterval;
      let mouseDown = false;

      // Left Arrow Mouse Down Event Handler

      carouselArrowLeft.addEventListener('mousedown', () => { 
        mouseDown = true;
        setTimeout(() => {
          if (mouseDown) {
            mouseDownInterval = setInterval(() => {
              if (mouseDown) {
                advanceCarouselLeft(null, true), fastForwardSpeed;
              }
            }, fastForwardSpeed);
            carouselArrowLeft.classList.add('carousel-arrow-hold-transitioning');
          }
        }, 500)
      })

      // Right Arrow Mouse Down Event Handler

      carouselArrowRight.addEventListener('mousedown', () => { 
        mouseDown = true;
        setTimeout(() => {
          if (mouseDown) {
            mouseDownInterval = setInterval(() => {
              if (mouseDown) {
                advanceCarouselRight(null, true), fastForwardSpeed;
              }
            }, fastForwardSpeed);
            carouselArrowRight.classList.add('carousel-arrow-hold-transitioning');
          }
        }, 500)
      })

      // Arrow Mouse Up Event Handling

      window.addEventListener('mouseup', () => {
        mouseDown = false;
        clearInterval(mouseDownInterval);
        carouselArrowLeft.classList.remove('carousel-arrow-hold-transitioning');
        carouselArrowRight.classList.remove('carousel-arrow-hold-transitioning');
        toggleFastForward();
        setTimeout(() => { 
          mouseDown = false;
          carouselArrowLeft.classList.remove('carousel-arrow-hold-transitioning');
          carouselArrowRight.classList.remove('carousel-arrow-hold-transitioning'); 
          clearInterval(mouseDownInterval);
        }, 500)
      });

      // Automatic Advance Right Interval Handler.  Checks That User Hasn't Advanced The Carousel Via Clicking The Arrows During The carouselDelay Time Period

      setInterval(() => {
        if (new Date().getTime() - lastTimeAdvanced > carouselDelay * 0.5 && !lightboxToggled) {
          advanceCarouselRight('carouselAutomated')
        }
      }, carouselDelay);
    }

    // Clear Lightbox

    function lightboxClear() {
      lightbox.innerHTML = '';
      lightboxToggled = false;
      lightbox.classList.remove('show-lightbox');
      html.classList.remove('hide-scroll');
    }

    // Escape Key Event Listener

    function escapeKeyListener(e) {
      if(e.key === 'Escape') {
        lightboxClear();
      }
    }

    // Lightbox and Carousel Arrow Click Event Handlers

    function frameTransitionHandler(frame, direction, type, currentVideoIndex, transitionType, fastForward) {
      
      const currentFrameData = playListSorted[currentVideoIndex];
      const nextFrameData = playListSorted[currentVideoIndex + 1];
      const previousFrameData = playListSorted[currentVideoIndex - 1];

      const iframe = frame.querySelector('iframe');
      const iframeText = frame.querySelector('h1');
      const currentIframeUrl = currentFrameData ? lightboxFrameVideoBaseUrl + currentFrameData.id : null;
      const nextIframeUrl = nextFrameData ? lightboxFrameVideoBaseUrl + nextFrameData.id : null;
      const previousIframeUrl = previousFrameData ? lightboxFrameVideoBaseUrl + previousFrameData.id : null;
      
      const transitionSpeed = type === 'lightbox' ? frameTransition : transitionType === 'carouselAutomated' ? carouselTransition
        : type === 'carousel' && transitionType !== 'carouselAutomated' ? frameTransition : frameTransition

      if (type === 'carousel' && transitionType !== 'carouselAutomated') {
        frame.classList.add('carousel-fast-transition');
        setTimeout(() => frame.classList.remove('carousel-fast-transition') ,transitionSpeed)
      }

      if (fastForward) {
        frame.classList.remove('frame-transitioning-show-text');
      }

      switch(frame.dataset.frameposition) {
        case '-1':
          if (direction === 'left') {
            if (type === 'lightbox' && currentFrameData && 
              iframe.src !== currentIframeUrl ) {
                iframe.src = currentIframeUrl
                iframeText.innerHTML= setThumbnailText(currentFrameData);
            }
            if (type === 'carousel' && currentFrameData) {
              setCarouselThumbnail(frame, currentFrameData)
            }
            setTimeout(() => {
              frame.classList.remove('frame-transitioning-show-text');
            }, transitionSpeed)
            frame.classList.remove('frame-transition-left');
            frame.dataset.frameposition = 0;
          }
          if (direction === 'right') {
            frame.classList.remove('frame-transition-left');
            frame.classList.add('frame-transition-right');
            frame.dataset.frameposition = 1;
            setTimeout(() => { 
              if (type === 'lightbox' && nextFrameData) {
                iframe.src = nextIframeUrl; 
                iframeText.innerHTML = setThumbnailText(nextFrameData);
              }
              if (type === 'carousel' && nextFrameData) {
                setCarouselThumbnail(frame, nextFrameData)
              }
              if (nextFrameData && !fastForward) {
                frame.classList.add('frame-transitioning-show-text');
              }
            }, transitionSpeed)
          }
          break;
        case '0':
          if (direction === 'left') {
            frame.classList.add('frame-transition-right');
            frame.dataset.frameposition = 1;
            setTimeout(() => { 
              if (type === 'lightbox' && nextFrameData) {
                iframe.src = nextIframeUrl;
                iframeText.innerHTML = setThumbnailText(nextFrameData);
              }
              if (type === 'carousel' && nextFrameData) {
                setCarouselThumbnail(frame, nextFrameData)
              }
              if (nextFrameData && !fastForward) {
                frame.classList.add('frame-transitioning-show-text');
              }
            }, transitionSpeed)
          }
          if (direction === 'right') { 
            frame.classList.add('frame-transition-left');
            frame.dataset.frameposition = -1;
            setTimeout(() => { 
              if (type === 'lightbox' && previousFrameData) {
                iframe.src = previousIframeUrl;
                iframeText.innerHTML = setThumbnailText(previousFrameData);
              }
              if (type === 'carousel' && previousFrameData) {
                setCarouselThumbnail(frame, previousFrameData)
              }
              if (previousFrameData && !fastForward) {
                frame.classList.add('frame-transitioning-show-text');
              }
            }, transitionSpeed)
          }
          break;
        case '1':
          if (direction === 'left') {
            frame.classList.remove('frame-transition-right');
            frame.classList.add('frame-transition-left');
            frame.dataset.frameposition = -1;
            setTimeout(() => { 
              if (type === 'lightbox' && previousFrameData) {
                iframe.src = previousIframeUrl;
                iframeText.innerHTML = setThumbnailText(previousFrameData);
              }
              if (type === 'carousel' && previousFrameData) {
                setCarouselThumbnail(frame, previousFrameData)
              }
              if (previousFrameData && !fastForward) {
                frame.classList.add('frame-transitioning-show-text');
              }
            }, transitionSpeed) 
          }
          if (direction === 'right') { 
            if (type === 'lightbox' && currentFrameData && 
              iframe.src !== currentIframeUrl) {
                iframe.src = currentIframeUrl;
                iframeText.innerText = setThumbnailText(currentFrameData);
            }
            if (type === 'carousel' && currentFrameData) {
              setCarouselThumbnail(frame, currentFrameData)
            }
            if (currentFrameData && !fastForward) {
              frame.classList.add('frame-transitioning-show-text');
              setTimeout(() => {
                frame.classList.remove('frame-transitioning-show-text');
              }, transitionSpeed)
            }
            frame.classList.remove('frame-transition-right');
            frame.dataset.frameposition = 0;
          }
          break;
      }
    }

    // Lightbox Activated Event Handler

    function lightboxActivateHandler(itemClicked, loadPlaylist) {

      lightboxToggled = true;

      // Select DOM Elements

      lightbox.innerHTML = '';
      lightbox.innerHTML = lightboxHTML;
      let startingLightboxActive = lightbox.querySelector('[data-frameposition="0"]');
      let lightboxStartingLeft = lightbox.querySelector('[data-frameposition="-1"]');
      let lightboxStartingRight = lightbox.querySelector('[data-frameposition="1"]');
      const lightboxFramesContainer = lightbox.querySelector('[data-lightboxframes="true"]');
      const lightboxFrames = lightbox.querySelectorAll('[data-lightboxframe="true"]');
      const lightboxFastForwardOverlay = lightbox.querySelector('[data-fastforwardoverlay="true"]');
      const lightboxFastForwardText = lightboxFastForwardOverlay.querySelector('h1');
      const lightboxArrowLeft = lightbox.querySelector('[data-lightboxarrowdirection="left"]');
      const lightboxArrowRight = lightbox.querySelector('[data-lightboxarrowdirection="right"]');
      const lightboxCloseButton = lightbox.querySelector('[data-lightboxclosebutton="true"]');
      const lightboxPlayerContainer = lightbox.querySelector('[data-lightboxplayer="true"]');
      const lightboxPlaylistContainer = lightbox.querySelector('[data-lightboxplaylistcontainer="true"]');
      const lightboxPlaylistContent = lightbox.querySelector('[data-lightboxplaylistcontent="true"]');
      const lightboxPlaylistButton = lightbox.querySelector('[data-lightboxplaylistbutton="true"]');

      // Elements To Hide When User Idle In Lightbox After 5 Seconds

      const elementsToHideWhenIdle = lightbox.querySelectorAll('[data-hideonidle="true"]');

      let mouseOverElement = false;
      let lastMouseOverTime = new Date().getTime();
      const idleDelayTime = 5000;

      // Checks If Playlist Button Was Clicked And If So, Loads Playlist On Load

      showPlaylist = loadPlaylist ? true : false;

      // Checks If Lightbox Arrow Was Clicked

      let lightboxArrowClicked = false;

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
              element.classList.remove('element-invisible');
            }
            if (element.dataset.lightboxplaylistbutton && showPlaylist && !element.classList.toString().includes('element-invisible')) {
              element.classList.add('element-invisible');
            }
            if (!showPlaylist && element.dataset.lightboxplaylistbutton && element.classList.toString().includes('element-invisible')) {
              element.classList.remove('element-invisible');
            }
          });
          setTimeout(() => {
            if (!mouseOverElement && (new Date().getTime() - lastMouseOverTime) >= idleDelayTime && !showPlaylist) {
              elementsToHideWhenIdle.forEach(element => element.classList.add('element-invisible'));
            }
          }, idleDelayTime)
        }
        if (!playlistButton) {
          lightboxPlaylistButton.classList.add('element-invisible');
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

      lightbox.addEventListener('mousemove', e => lightboxMouseMoveHandler(e, elementsToHideWhenIdle));
      lightbox.addEventListener('click', e => lightboxMouseMoveHandler(e, elementsToHideWhenIdle));

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
        startingLightboxActive = lightbox.querySelector('[data-frameposition="0"]');
        lightboxStartingLeft = lightbox.querySelector('[data-frameposition="-1"]');
        lightboxStartingRight = lightbox.querySelector('[data-frameposition="1"]');
        
        lightboxFrames.forEach(frame => {
          frame.classList.remove('frame-transitioning-left');
          frame.classList.remove('frame-transitioning-right');
        });

        if (!playListSorted[currentVideoIndex + 1]) {
          lightboxArrowRight.classList.add('disable-arrow');
        } else { 
          lightboxArrowRight.classList.remove('disable-arrow');
          lightboxStartingRight.classList.add('frame-transitioning-right');
          lightboxStartingRight.querySelector('iframe').src = lightboxFrameVideoBaseUrl + setVideoId(1);
          lightboxStartingRight.querySelector('h1').innerHTML = setThumbnailText(setData(1));
        }
        if (!playListSorted[currentVideoIndex - 1]) {
          lightboxArrowLeft.classList.add('disable-arrow');
        } else { 
          lightboxArrowLeft.classList.remove('disable-arrow');
          lightboxStartingRight.classList.add('frame-transitioning-left');
          lightboxStartingLeft.querySelector('iframe').src = lightboxFrameVideoBaseUrl + setVideoId(-1);
          lightboxStartingLeft.querySelector('h1').innerHTML = setThumbnailText(setData(-1));
        }

        // Check If Video Index Has Video And Check For Browser Window Width And Set For Lightbox Or Full Screen Iframe On Mobile, Tablet

        if (playListSorted[currentVideoIndex]) {
          if (window.innerWidth > mediaQueryMobileBreakpoint || showPlaylist) {
            startingLightboxActive.querySelector('iframe').src = lightboxFrameVideoBaseUrl + setVideoId(0);
            startingLightboxActive.querySelector('h1').innerHTML = setThumbnailText(setData(0));
          } else window.open(lightboxFrameVideoBaseUrl + setVideoId(0))
        }
        if (window.innerWidth > mediaQueryMobileBreakpoint || showPlaylist) {
          lightbox.classList.add('show-lightbox');
          html.classList.add('hide-scroll');
        } else {
          lightboxClear();
        }      
      }

      initLightboxFrames();

      // Render Grid Items In Playlist Container

      lightboxPlaylistContent.innerHTML = gridItemsProcessing(playListSorted, true);

      // Event Listeners On Playlist Items Clicked

      const lightboxPlaylistItems = lightboxPlaylistContent.querySelectorAll('[data-itemclickable="true"]');

      lightboxPlaylistItems.forEach(itemClicked => 
        itemClicked.addEventListener('click', () => {
          currentVideoIndex = playListSorted.findIndex(video => video.id === itemClicked.dataset.id);
          toggleLightboxPlayerOrPlaylist();
          initLightboxFrames();
        })
      );

      // Records When Last Time Carousel Was Advanced To Avoid User Click/Auto Interval Conflict

      let lastTimeAdvanced = new Date().getTime();

      // Event Listener On Exit Button Click To Exit LightBox

      lightboxCloseButton.addEventListener('click', lightboxClear);

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
        if (enabled) {
          lightboxFastForwardOverlay.classList.remove('element-invisible');
          lightboxFramesContainer.classList.add('fast-forward-transitioning');
          lightboxFastForwardText.innerHTML = setThumbnailText(item);
        } else {
          lightboxFastForwardText.innerHTML = '';
          lightboxFastForwardOverlay.classList.add('element-invisible');
          lightboxFramesContainer.classList.remove('fast-forward-transitioning');
        }
      }

      // Left Arrow Click/Mousedown And Auto Transitioning

      function advanceLightboxLeft(fastForward) {
        currentVideoIndex -= 1;

        if (playListSorted[currentVideoIndex - 1]) {
          lightboxArrowLeft.classList.remove('disable-arrow');
        } else { 
          clearFastForwarding();
          lightboxArrowLeft.classList.add('disable-arrow');
        }

        if (playListSorted[currentVideoIndex + 1]) {
          lightboxArrowRight.classList.remove('disable-arrow')
        } else lightboxArrowRight.classList.add('disable-arrow')

        if (fastForward && mouseDown && playListSorted[currentVideoIndex]) {
          lightboxArrowFastForwarded = true;
          toggleFastForward(true, playListSorted[currentVideoIndex]);
        } else {
          toggleFastForward();
        }
        
        setLightboxArrowToggled();

        lightboxFrames.forEach(frame => frameTransitionHandler(frame, 'left', 'lightbox', currentVideoIndex));
      }

      // Right Arrow Click/Mousedown And Auto Transitioning

      function advanceLightboxRight(fastForward) {
        currentVideoIndex += 1;

        if (playListSorted[currentVideoIndex + 1]) {
          lightboxArrowRight.classList.remove('disable-arrow');
        } else { 
          clearFastForwarding();
          lightboxArrowRight.classList.add('disable-arrow');
        }

        if (playListSorted[currentVideoIndex - 1]) {
          lightboxArrowLeft.classList.remove('disable-arrow');
        } else lightboxArrowLeft.classList.add('disable-arrow');

        if (fastForward && mouseDown && playListSorted[currentVideoIndex]) {
          lightboxArrowFastForwarded = true;
          toggleFastForward(true, playListSorted[currentVideoIndex]);
        } else { 
          toggleFastForward();
        }
        
        setLightboxArrowToggled();

        lightboxFrames.forEach(frame => frameTransitionHandler(frame, 'right', 'lightbox', currentVideoIndex));
      }

      // Left Arrow Click Event Handler

      lightboxArrowLeft.addEventListener('click', () => {
        if (!mouseDown) {
          lastTimeAdvanced = new Date().getTime();
          advanceLightboxLeft()
        } else {
          clearInterval(mouseDownInterval);
          mouseDown = false;
        }
      });

      // Right Arrow Click Event Handler

      lightboxArrowRight.addEventListener('click', () => {
        if (!mouseDown) {
          lastTimeAdvanced = new Date().getTime();
          advanceLightboxRight()
        } else {
          clearInterval(mouseDownInterval);
          mouseDown = false;
        }
      });

      // Left Arrow Mouse Down Event Handler

      lightboxArrowLeft.addEventListener('mousedown', () => {
        mouseDown = true;
        setTimeout(() => {
          if (mouseDown && playListSorted[currentVideoIndex - 1]) {
            mouseDownInterval = setInterval(() => {
              if (mouseDown) {
                advanceLightboxLeft(true), fastForwardSpeed;
              }
            }, fastForwardSpeed);
            lightboxArrowLeft.classList.add('lightbox-arrow-hold-transitioning');
          }
        }, 500)
      });

      // Right Arrow Mouse Down Event Handler

      lightboxArrowRight.addEventListener('mousedown', () => {
        mouseDown = true;
        setTimeout(() => {
          if (mouseDown && playListSorted[currentVideoIndex + 1]) {
            mouseDownInterval = setInterval(() => {
              if (mouseDown) {
                advanceLightboxRight(true), fastForwardSpeed;
              }
            }, fastForwardSpeed);
            lightboxArrowRight.classList.add('lightbox-arrow-hold-transitioning');
          }
        }, 500)
      });

      // Arrow Mouse Up Event Handling

      function clearFastForwarding() {
        mouseDown = false;
        clearInterval(mouseDownInterval);
        mouseDownInterval = '';
        lightboxArrowLeft.classList.remove('lightbox-arrow-hold-transitioning');
        lightboxArrowRight.classList.remove('lightbox-arrow-hold-transitioning');
        toggleFastForward();
        setTimeout(() => { 
          mouseDown = false;
          clearInterval(mouseDownInterval);
          lightboxArrowLeft.classList.remove('lightbox-arrow-hold-transitioning');
          lightboxArrowRight.classList.remove('lightbox-arrow-hold-transitioning');
          clearInterval(mouseDownInterval);
        }, 500);
      }

      window.addEventListener('mouseup', clearFastForwarding);

      // Playlist Button Event Listener

      function toggleLightboxPlayerOrPlaylist(initialized) {
        if (!initialized) {
          showPlaylist = !showPlaylist;
        }
        if (showPlaylist) {
          lightboxFrames.forEach(frame => {
            frame.querySelector('iframe').src = '';
            frame.querySelector('h1').innerHTML = '';
          });
          lightboxPlaylistButton.classList.add('element-invisible');
          lightboxPlayerContainer.classList.add('lightbox-container-off');
          lightboxPlaylistContainer.classList.remove('lightbox-container-off');
        } else {
          if (playlistButton) {
            lightboxPlaylistButton.classList.remove('element-invisible');
          } else lightboxPlaylistButton.classList.add('element-invisible');
          lightboxPlayerContainer.classList.remove('lightbox-container-off');
          lightboxPlaylistContainer.classList.add('lightbox-container-off');
        }
      }

      toggleLightboxPlayerOrPlaylist(true);

      lightboxPlaylistButton.addEventListener('click', () => toggleLightboxPlayerOrPlaylist());
    }

  // Data Retrieval From LocalStorage / API Method

    async function getPlaylistItems() {

      // Local Storage Check And Retrieval If Found.  If Found, Checks Timing So That The Current Time Minus The Stored Time Do Not Exceed timeStorageInterval Declaration

      let storedPlaylists = localStorage.getItem(playlistService === 'youtube' ? 'youtubePlaylists' : '');
      let storedPlaylistId;
      if (storedPlaylists) {
        storedPlaylists = JSON.parse(storedPlaylists)
        const currentTime = new Date().getTime();
        if (playlistService === 'youtube') {
          storedPlaylistId = storedPlaylists.find(list => list.items[0].snippet.playlistId === playlistId);
        }
        if (storedPlaylistId) {
          if (currentTime - storedPlaylistId.storedTime < timeStorageInterval) {
              storedPlaylistRetrieved = true;
              console.log(`Video playlist loaded from localStorage since the time stamp period was less than the ${timeStorageInterval / 60000} minute specified API call period.`);
              return { data: storedPlaylistId, ok: true }
          }
        }
      } 

      // API Fetch If Local Storage Data Not Found Or If Local Storage Data Found And Time Stored Exceeded timeStorageInterval Declaration

      if (!storedPlaylistRetrieved){
        try {
          const res = await fetch(fullPath);
          if (res.ok) {
            let data = await res.json();

            // Youtube API Has A Limit Of Results Per Request Based Upon resultLimitPerRequest Declaration.  If Total Results In Playlist Exceeds This, Multiple Requests Are Made To The Youtube API Until Total Results Retrieved Equals Total In Playlist.

            if (playlistService === 'youtube') {
              if (data.pageInfo.totalResults > resultLimitPerRequest) {
                let nextPageToken = data.nextPageToken;
                for (let totalVideos = data.items.length; totalVideos < data.pageInfo.totalResults; totalVideos) {
                  const nextPageTokenPath = fullPath + `&pageToken=${nextPageToken}`;
                  const page = await fetch(nextPageTokenPath);
                  if (res.ok) {
                    const pageData = await page.json();
                    data.items = [...data.items, ...pageData.items];
                    data.pageInfo.resultsPerPage += pageData.items.length;
                    if (pageData.nextPageToken) {
                      nextPageToken = pageData.nextPageToken
                    }
                    totalVideos += pageData.items.length
                  } else {
                    console.error(`There Was An Error Attempting To Load Additional Pages Of Videos.  Total Videos Displayed May Be Less Than The Max Results Or Total On Playlist`);
                    break;
                  }
                }
              }
            }

            // Data Retrieved From API Saved / Updated In Local Storage

            data.storedTime = new Date().getTime();

            if (playlistService === 'youtube') {
              if (storedPlaylists) {
                  let revisedStoredPlaylists;
                  if (storedPlaylistId) {
                    revisedStoredPlaylists = storedPlaylists.map(list => {
                        if (list.items[0].snippet.playlistId === playlistId) {
                            return data
                        } else return list
                    });
                  } else revisedStoredPlaylists = [...storedPlaylists, data]
                  localStorage.removeItem('youtubePlaylists');
                  localStorage.setItem('youtubePlaylists', JSON.stringify(revisedStoredPlaylists));
              } else {
                  localStorage.setItem('youtubePlaylists', JSON.stringify([data]));
              }
              console.log(`Video playlist loaded via API Call.  Playlist saved/updated in localStorage`);
              return { status: res.status, ok: true, data }
            }

          // Error Handling If res.ok Is False

          } else {
            console.error(`Server responded with a ${res.status}.`);
            if (storedPlaylistId) {
              console.warn(apiErrMsg);
              return { data: storedPlaylistId, ok: true }
            } else return { status: res.status, ok: false, data: await res.json()}
          }

        // Error Handling If Connection To API Fails

        } catch(err) {
          console.error(err);
          if (storedPlaylistId) {
              console.warn(apiErrMsg)
              return { data: storedPlaylistId, ok: true }
          } else return { status: res.status, ok: false, data: {msg: 'Failed To Fetch'} }
        }
      }
    }

  // Data List Management Methods

    // Data List Sorting

    function listSorter(a, b) {
      const aTime = Date.parse(a.publishedDate);
      const bTime = Date.parse(b.publishedDate);
      switch (sortVideosBy) {
        case 'number-descending':
          return a.titledEpisode < b.titledEpisode ? 1 : -1;
        case 'number-ascending':
          return a.titledEpisode < b.titledEpisode ? -1 : 1;
        case 'date-descending':
          return aTime < bTime ? 1 : -1;
        case 'date-ascending':
          return aTime < bTime ? -1 : 1;
      }
    }

    // Episode Number Assigning

    function numberReducer(input) {
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

  // Grid Layout Handling

    function gridItemsProcessing(outputList, playlist) {
      return outputList.map(item => {
        if (!item.id) {
          console.error(`Playlist Item Failed To Load Due To Insufficient Data`);
          failedItemTally++
        } else if (!item.title) {
          console.error(`Video Playlist Item ${item.id} Did Not Have A Title And Could Not Be Loaded.`)
        } else if (item.title.toLowerCase() === 'deleted video' || item.title.toLowerCase() === 'private video') {
          console.error(`Video Playlist Item ${item.id} Could Not Be Loaded As Its Status Is: ${item.title}`);
        } else if (sortVideosBy === 'number-ascending' || sortVideosBy === 'number-descending' && item.titledEpisode.toString() === 'NaN') {
          console.error(`Video Playlist Item ${item.id} Entitled "${item.title}" Could Not Be Loaded As Number(s) Were Detected In Its Title Name But Could Not Generate An Episode Number.  This Can Occur If The Video Title Has Two Or More Numbers In It.  It Must Have Only One Number In Its Title Name That Pertains To Its Episode Number When Sorting Videos In The "${sortVideosBy}" Mode.  If This Is The Case, Please Change It's Title Name Accordingly.`)
        } else return renderGridItem(item, playlist);
      }).join('')
    }

  // END - Data List Management Methods

  // CONTROLLER - Initialization For Data Retrieval, Data Management, HTML Render, And Event Listener Initializations

    getPlaylistItems().then(res => {
        if (res.ok) {
          pageOutputList = playlistService === 'youtube' ? [...res.data.items] : [];

          // Map Out Title, Episode Number, Video Id, And Thumbnail Image

          pageOutputList = pageOutputList.map(item => {
            const itemOutput = { };
            if (item) {
              if (playlistService === 'youtube') {
                if (item.snippet) {
                  const { snippet } = item;
                  if (snippet.title) {
                    itemOutput.titledEpisode = numberReducer(snippet.title)
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
                  } itemOutput.description = null;
                } else return null
                itemOutput.titledEpisode = numberReducer(item.snippet.title)
              }
            } else itemOutput.id = null
            return itemOutput
          });

          // Checks If Playlist Is Empty

          if (!pageOutputList[0].id) {
            console.warn('Failed To Load Playlist Due To Lack Of Sufficient Data.  Playlist May Be Empty');
            playlistItems.innerHTML = `
              <h2>The Playlist Is Currently Empty.</h2>
            `
            return
          }

          // Filter By Name

          if (filterByName) {
              pageOutputList = pageOutputList.filter(item => item.title.toLowerCase().includes(filterNameParameters));
          }  

          // Sorting 

          for (let i = 0; i < 2; i++) {
              pageOutputList.sort((a, b) => listSorter(a, b));
          }

          // Removes Duplicates If Found 

          pageOutputList = pageOutputList.filter((item, index) => {
            if (pageOutputList[index + 1]) {
              return pageOutputList[index + 1].titledEpisode !== item.titledEpisode ? true : false;
            } else return true
          });

          // Filter By Episode Range If listByRange Is True

          if (listByRange) {
            pageOutputList = pageOutputList.filter(item => item.titledEpisode >= fromEpisodeNumber && item.titledEpisode <= toEpisodeNumber);
          }

          // Filter Out Numbered Episodes Only Non Numbered Episodes Are Shown If showNonNumberedEpisodesOnly Is True

          if (showNonNumberedEpisodesOnly) {
              pageOutputList = pageOutputList.filter(item => item.titledEpisode === - 1);
          }

          // Filter Out Non Numbered Episodes If hideNonNumberedVideos Is True

          if (hideNonNumberedVideos) {
              pageOutputList = pageOutputList.filter(item => item.titledEpisode !== - 1);
          }

          // Capture All Videos After Filtering And Sorting Before Quantity For Output Is Reduced If showAllInLightbox Is True To Show All Videos In Lightbox

          if (showAllInLightbox) {
            playListSorted = [...pageOutputList];
          }

          // Reduces Amount Of Video Thumbnails To Be Displayed On Page

          if (maxResults < pageOutputList.length && limitVideos) {
              pageOutputList = pageOutputList.filter((item, index) => (index + 1) <= maxResults);
          }

          // Make playListSorted Show The Same as pageOutputList if showAllInLightbox Is False

          if (!showAllInLightbox) {
            playListSorted = [...pageOutputList];
          }

          // HTML Rendering

          // Grid Layout Rendering

          const gridListMapping = 
            '<div class="grid-layout">' + 
              gridItemsProcessing(pageOutputList)
            + '</div>';       

          if (playlistLayout === 'grid') {
            playlistItems.innerHTML = gridListMapping;
          }

          // Carousel Layout Rendering

          if (playlistLayout === 'carousel') {
            playlistItems.innerHTML = carouselHTML;
            carouselEventHandling();
          }

          // Playlist View Button If playlistButton Is Set To True

          // Checks If Total Items Is Less Than Or Equal To maxResults

          if (!limitVideos) {
            playlistButton = false;
          }

          if (limitVideos && playListSorted.length <= maxResults) {
            playlistButton = false;
          }
          
          function initializePlaylistPageButton() {
            if (playlistButton) {
              const buttonContainer = document.createElement('button');
              buttonContainer.classList.add('playlist-button-page');
              buttonContainer.dataset.playlistbuttonpage = 'true';
              buttonContainer.innerText = playlistButtonText;
              playlistItems.appendChild(buttonContainer);
              document.querySelector(`${widgetIdSelector} [data-playlistbuttonpage="true"]`)
                .addEventListener('click', () => lightboxActivateHandler(null, true))
            }
          }

          initializePlaylistPageButton();

          // Error Logging

          if (failedItemTally > 0) {
              console.error(`${failedItemTally} Playlist Items Failed To Load.`)
          }

          // Click Event Listener Initialization After HTML Rendering Of Video Items

          let items = document.querySelectorAll(`${widgetIdSelector} [data-itemclickable="true"]`);

          function listenOnItemsClick() {
            items = document.querySelectorAll(`${widgetIdSelector} [data-itemclickable="true"]`);
            items.forEach(item => {
              item.addEventListener('click', () => lightboxActivateHandler(item));
            });
          }
          
          listenOnItemsClick();

          // Escape Key And Click Event Listener Initialization For Lightbox

          window.addEventListener('keyup', escapeKeyListener);
          // lightbox.addEventListener('click', (e) => lightboxActivateHandler(lightbox, e));

          window.addEventListener('resize', () => {
            if (!showPlaylist && window.innerWidth < mediaQueryMobileBreakpoint) {
              lightboxClear();
            }
          });

      } else {

        let errMsg;

        console.error(res.data)

        if (playlistService === 'youtube') {
          errMsg = res.data.error.message
        }

        // Error Message If Data Failed To Load

        playlistItems.innerHTML = `
          <h2>There was an error loading the ${playlistService} playlist. ${errMsg}</h2>
        `
      }
      
    });

  // END CONTROLLER
}
