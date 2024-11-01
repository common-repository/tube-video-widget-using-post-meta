<?php

/* 
 * Plugin Name: Tube Video Widget
 * Plugin URI: https://amanurrahman.com/
 * Description: This will show a Youtube video widget in a single post view. Video link comes from the post meta called "Youtube Video link". You can use shortcode [ytvd] to show that video anywhere in the post
 * Version: 5.1
 * Author: Amanur Rahman
 * Author URI: https://amanurrahman.com/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

error_reporting(0);
if ( !defined( 'ABSPATH' ) ) {
        die; // Exit if accessed directly
    }

defined( 'ABSPATH' ) or die('You can not access this file you stupid');


// Show metabox in post editing page
add_action('add_meta_boxes','amanhstu_add_metabox');

// Save Metabox Data
add_action('save_post', 'amanhstu_save_metabox');

// Register widget
add_action('widgets_init','amanhstu_widget_init');



function amanhstu_add_metabox(){
    add_meta_box('amanhstu_youtube', 'Youtube Video Link', 'youtube_video_handler', 'post');
}

  /*
     * 
     *  Meta Box handler
     */
	 


function youtube_video_handler(){
    global $post;
    $value = get_post_custom($post->ID);
    $youtube_link = esc_attr($value['amanhstu_youtube']['0']);
    echo '<label>Youtube Link</label> <input type="text" id="amanhstu_youtube" name="amanhstu_youtube" value="'.$youtube_link.'"  />';
}


function amanhstu_save_metabox($post_id){
    //don't save meta data if it is autosave
    if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
       
        return;
    }
    
    if( !current_user_can( 'edit_post' )){
        return;
        
    }
    
    if( isset($_POST['amanhstu_youtube'])){
        
        update_post_meta($post_id, 'amanhstu_youtube', esc_url($_POST['amanhstu_youtube']));
    }
    
    
    
}


/*
 * Register Widget
 */

function amanhstu_widget_init(){
register_widget('amanhstu_widget');

}

/*
 * Widget Class
 * 
 */

class amanhstu_widget extends WP_Widget{ 
    function __construct(){
        $widget_options = array(
            
            'classname' => 'youtubewidget', // Css Class
            'description' => 'This widget will show youtube video from post metadata'
            
        );
        
        parent::__construct('amanhstu_id', 'Aman Youtube Video Widget', $widget_options);
        
    }
    
    /*
     * Show Widgets form in widget Appearance / widget
     */
    
    function form($instance){
        $defaults = array('title' => 'Youtube Video Link');
        $instance = wp_parse_args( (array)$instance, $defaults);
        
        $title = esc_attr($instance['title']);
        
        echo '<p>Title <input type="text" class="widefat" name="'.$this->get_field_name('title').'" value="'.$title.'" /></p>';
    }
    
    /*
     * Save Widget Form
     */
    
    function update($new_instance, $old_instance){
        
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        
        return $instance;
        
        
    }
    
    /*
     * Show widget in post or page
     */
    
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        
        // Show only if single post
        
        if (is_single()){
            
            echo $before_widget;
            echo $before_title.$title.$after_title;
            
            // get post meta data
            $amanhstu_youtube = esc_url(get_post_meta(get_the_ID(), 'amanhstu_youtube', true));

            // Print widget content
            echo '<iframe width="200" height="200" frameborder="0" allowfullscreen src="https://www.youtube.com/embed/'.get_yt_videoid($amanhstu_youtube).'"></iframe>';
 
            echo $after_widget;
            
        }
        
    }
    
}


/*
 * Get youtube video id from a link
 */

function get_yt_videoid($url){
    parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
    return $my_array_of_vars['v'];
    
    
}


// Shortcode
function amanhstu_youtube_shortcode(){
    
     // get post meta data
    $amanhstu_youtube = esc_url(get_post_meta(get_the_ID(), 'amanhstu_youtube', true));
    
    echo '<iframe width="400" height="300" frameborder="0" allowfullscreen src="https://www.youtube.com/embed/'.get_yt_videoid($amanhstu_youtube).'"></iframe>';
    
}


add_shortcode('ytvd', 'amanhstu_youtube_shortcode');




?>