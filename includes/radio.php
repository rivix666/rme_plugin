<?php

function rme_showRadioPlayer($atts) {
    
	// Attributes
	extract( shortcode_atts( array(
			'cover_img' => '',
			'title' => 'Radio Max Elektro',
			'description' => 'Tylko najlepsza muzyka',
		), $atts) );

    // Include required styles/scripts
    wp_enqueue_script('rmeAmplitudeJs', plugin_dir_url(__FILE__).'../'.'js/dist/amplitude.js');
    wp_enqueue_style('rmePlayerCss', plugin_dir_url(__FILE__).'../'.'css/radio.css');
    wp_enqueue_style('rmePlayerFoundationCss', plugin_dir_url(__FILE__).'../'.'css/foundation.min.css');
    wp_enqueue_style('rmePlayerFont', 'https://fonts.googleapis.com/css?family=Lato:400,400i');
    wp_enqueue_script('rmeRadioJs', plugin_dir_url(__FILE__).'../'.'js/radio.js');

    // Prepare buttons images
    $play_img_path = plugin_dir_url(__FILE__).'../img/play.svg';
    $pause_img_path = plugin_dir_url(__FILE__).'../img/pause.svg';

    // Prepare radio html code
    $radio_html = 
        '<div class="player">          
            <div class="meta-container">
              <div class="time-container">
                <div class="current-time">
                  <span class="amplitude-current-minutes" data-amplitude-song-index="0"></span>:<span class="amplitude-current-seconds" data-amplitude-song-index="0"></span>
                </div>

                <div class="duration">
                  <span class="amplitude-duration-minutes" data-amplitude-song-index="0">3</span>:<span class="amplitude-duration-seconds" data-amplitude-song-index="0">30</span>
                </div>
              </div>
              <progress class="amplitude-song-played-progress" data-amplitude-song-index="0" id="song-played-progress-1"></progress>
              <div class="control-container">
                <div class="amplitude-play-pause" data-amplitude-song-index="0">

                </div>
              </div>
            </div>
         </div>
         <div id="preload">
           <img src="'.$play_img_path.'"/>
           <img src="'.$pause_img_path.'"/>
         </div>';

    return $radio_html;
}

add_shortcode('rmeShowRadioPlayer', 'rme_showRadioPlayer');

?>