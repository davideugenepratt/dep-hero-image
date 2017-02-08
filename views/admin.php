<?php

global $post;
$values = get_post_custom( $post->ID );
$image_url = isset( $values['dep_hero_image_url'] ) ? $values['dep_hero_image_url'][ 0 ] : '';
$content = isset( $values['dep_hero_image_content'] ) ? $values['dep_hero_image_content'][ 0 ] : '';

$editor_settings = array();

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