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

// Callback function to insert 'styleselect' into the $buttons array
function my_mce_buttons_2( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
// Register our callback to the appropriate filter
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

// Callback function to filter the MCE settings
function my_mce_before_init_insert_formats( $init_array ) {
    // Define the style_formats array
    $style_formats = array(
        // Each array child is a format with it's own settings
        array(
            'title' => 'Call To Action Button',
            'inline' => 'span',
            'classes' => 'call-to-action',
            'wrapper' => true,

        )
    );
    // Insert the array, JSON ENCODED, into 'style_formats'
    $init_array['style_formats'] = json_encode( $style_formats );

    return $init_array;

}
// Attach callback to 'tiny_mce_before_init'
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );

function dep_hero_image_html($post)
{

    global $post;
    $values = get_post_custom( $post->ID );
    $image_url = isset( $values['dep_hero_image_url'] ) ? $values['dep_hero_image_url'][ 0 ] : '';
    $content = isset( $values['dep_hero_image_content'] ) ? $values['dep_hero_image_content'][ 0 ] : '';

    $editor_settings = array(
        // "tiny_mce" => '{ oninit : function() { alert(); } '
    );

    ?>
    <p class="hide-if-no-js">
        <a class="upload-custom-img <?php if ( $you_have_img  ) { echo 'hidden'; } ?>"
           href="<?php echo $upload_link ?>">
            <?php _e('Set background image') ?>
        </a>
        <a class="delete-custom-img <?php if ( ! $you_have_img  ) { echo 'hidden'; } ?>"
           href="#">
            <?php _e('Remove background image') ?>
        </a>
    </p>
    <input type="hidden" name="dep_hero_image_url" id="dep_hero_image_url" value="<?php echo $image_url; ?>" >

    <?php wp_editor( $content, "dep_hero_image_content" , $editor_settings ); ?>

    <script>

        jQuery(function($){

            tinymce.on('addeditor', function( event ) {

             var editor = tinyMCE.get('dep_hero_image_content');

             editor.on( "init" , function() {
                 editor.getBody().style.backgroundImage = "url(<?php echo $image_url; ?>)";
             });

            }, true );

            // Set all variables to be used in scope
            var frame,
                metaBox = $('#dep_hero_image.postbox'), // Your meta box id here
                addImgLink = metaBox.find('.upload-custom-img'),
                delImgLink = metaBox.find( '.delete-custom-img'),
                imgContainer = metaBox.find( '.custom-img-container'),
                imgIdInput = metaBox.find( '#dep_hero_image_url' );

            // ADD IMAGE LINK
            addImgLink.on( 'click', function( event ){

                event.preventDefault();

                // If the media frame already exists, reopen it.
                if ( frame ) {
                    frame.open();
                    return;
                }

                // Create a new media frame
                frame = wp.media({
                    title: 'Select or Upload Media Of Your Chosen Persuasion',
                    button: {
                        text: 'Use this media'
                    },
                    multiple: false  // Set to true to allow multiple files to be selected
                });


                // When an image is selected in the media frame...
                frame.on( 'select', function() {

                    // Get media attachment details from the frame state
                    var attachment = frame.state().get('selection').first().toJSON();

                    // Send the attachment URL to our custom image input field.
                    //imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

                    // Send the attachment id to our hidden input
                    imgIdInput.val( attachment.url );

                    var editor = tinyMCE.get('dep_hero_image_content');

                    editor.getBody().style.backgroundImage = "url("+attachment.url+")";

                    // Hide the add image link
                    addImgLink.addClass( 'hidden' );

                    // Unhide the remove image link
                    delImgLink.removeClass( 'hidden' );
                });

                // Finally, open the modal on click
                frame.open();
            });


            // DELETE IMAGE LINK
            delImgLink.on( 'click', function( event ){

                event.preventDefault();

                // Clear out the preview image
                imgContainer.html( '' );

                // Un-hide the add image link
                addImgLink.removeClass( 'hidden' );

                // Hide the delete image link
                delImgLink.addClass( 'hidden' );

                // Delete the image id from the hidden input
                imgIdInput.val( '' );

            });

        });

    </script>

    <?php
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

// Move all "advanced" metaboxes above the default editor
add_action('edit_form_after_title', function() {

    global $post, $wp_meta_boxes;
    do_meta_boxes(get_current_screen(), 'advanced', $post);
    unset($wp_meta_boxes[get_post_type($post)]['advanced']);

});