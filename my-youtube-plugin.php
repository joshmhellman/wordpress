<?php
/*
Plugin Name: My YouTube Plugin
Description: Display YouTube videos from your channel.
Version: 1.0
Author: Your Name
Author URI: Your Website
*/

// Enqueue YouTube API script
function my_youtube_plugin_scripts() {
    wp_enqueue_script( 'youtube-api', 'https://www.youtube.com/iframe_api' );
}
add_action( 'wp_enqueue_scripts', 'my_youtube_plugin_scripts' );

// Generate YouTube video shortcode
function my_youtube_video_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'channel_id' => '',
        'number'     => 5,
        'layout'     => 'default',
    ), $atts, 'my_youtube_video' );

    $channel_id = $atts['channel_id'];
    $number     = $atts['number'];
    $layout     = $atts['layout'];

    $output = '';

    if ( ! empty( $channel_id ) ) {
        $api_key = 'AIzaSyD1D50OgZ89nQ9vRgZCOPjmnGlyoSUhBa4';

        $response = wp_remote_get( "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId={$channel_id}&maxResults={$number}&key={$api_key}" );

        if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
            $videos = json_decode( wp_remote_retrieve_body( $response ) );

            if ( isset( $videos->items ) && ! empty( $videos->items ) ) {
                $output .= '<div class="my-youtube-videos ' . esc_attr( $layout ) . '">';

                foreach ( $videos->items as $video ) {
                    $video_id    = $video->id->videoId;
                    $title       = $video->snippet->title;
                    $description = $video->snippet->description;
                    $thumbnail   = $video->snippet->thumbnails->medium->url;

                    $output .= '<div class="my-youtube-video">';
                    $output .= '<a href="https://www.youtube.com/watch?v=' . esc_attr( $video_id ) . '" target="_blank">';
                    $output .= '<img src="' . esc_url( $thumbnail ) . '" alt="' . esc_attr( $title ) . '">';
                    $output .= '<h3>' . esc_html( $title ) . '</h3>';
                    $output .= '</a>';
                    $output .= '</div>';
                }

                $output .= '</div>';
            }
        }
    }

    return $output;
}
add_shortcode( 'my_youtube_video', 'my_youtube_video_shortcode' );
