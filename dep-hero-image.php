<?php
/**
 * Plugin Name:     Hero Image
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Adds custom hero images for pages
 * Author:          David Eugene Pratt
 * Author URI:      www.davideugenepratt.com
 * Text Domain:     dep-hero-image
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Customizeable_Pages
 */


function dep_hero_image_html($post) {

    $dir = plugin_dir_path( __FILE__ );

    include $dir."/views/admin.php";

}


function add_custom_box()
{
    $screens = [ 'page' ];
    foreach ($screens as $screen) {
        add_meta_box(
            'dep_hero_image',           // Unique ID
            'Hero Image',  // Box title
            'dep_hero_image_html', // Content callback, must be of type callable
            $screen,                   // Post type
            'advanced',
            'high'
        );
    }
}

add_action('add_meta_boxes', 'add_custom_box');


function dep_hero_image_save_postdata($post_id)
{

        // Bail if we're doing an auto save
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // if our nonce isn't there, or we can't verify it, bail
       //  if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'dep_hero_image_nonce' ) ) return;

        // if our current user can't edit this post, bail
        if( !current_user_can( 'edit_post' ) ) return;

        // now we can actually save the data
        $allowed = array(
            'a' => array( // on allow a tags
                'href' => array() // and those anchors can only have href attribute
            ),
            'span' => array(
                'style' => array(),
                'class' => array()
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'div' => array()
        );

        // Make sure your data is set before trying to save it
        if( isset( $_POST['dep_hero_image_url'] ) ) {

            update_post_meta( $post_id, 'dep_hero_image_url', wp_kses( $_POST['dep_hero_image_url'], $allowed ) );

        }

        if( isset( $_POST['dep_hero_image_content'] ) ) {

            update_post_meta( $post_id, 'dep_hero_image_content', wp_kses( $_POST['dep_hero_image_content'], $allowed ) );

        }


}

add_action('save_post', 'dep_hero_image_save_postdata');

function dep_hero_image_move_metaboxes()
{

    // Move all "advanced" metaboxes above the default editor

    global $post, $wp_meta_boxes;
    do_meta_boxes(get_current_screen(), 'advanced', $post);
    unset($wp_meta_boxes[get_post_type($post)]['advanced']);

}

add_action('edit_form_after_title', 'dep_hero_image_move_metaboxes' );