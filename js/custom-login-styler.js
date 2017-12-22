jQuery(document).ready(function($) {

    // Uploading files
    var file_frame;
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    var set_to_post_id = $('#post_id').val(); // Set this

    //on media button click
    jQuery('.tmprc-media-upload').on('click', function( event ){

        //check what image was clicked
        var imgElement = $(this).closest('.img-wrap').find('.image-id');
        var imgPreview = $(this).closest('.img-wrap').find('.image-preview');
        var context = $(this);

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            // Set the post ID to what we want
            file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
            // Open frame
            file_frame.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = set_to_post_id;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to upload',
            button: {
                text: 'Use this image',
            },
            multiple: false	// Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function(  ) {

            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();

            // Do something with attachment.id and/or attachment.url here
            imgElement.val( attachment.id );

            if( imgPreview.length > 0 ){
                imgPreview.attr( 'src', attachment.url ).css( 'width', 'auto' );
            }else{
                context.closest('.img-wrap').find('.image-preview-wrapper').append('<img style="width:auto;height:150px;" class="image-preview" src="' + attachment.url + '">');
            }
            // Restore the main post ID
            wp.media.model.settings.post.id = wp_media_post_id;

        });

        // Finally, open the modal
        file_frame.open();

    });

    // Restore the main ID when the add media button is pressed
    jQuery( 'a.add_media' ).on( 'click', function() {
        wp.media.model.settings.post.id = wp_media_post_id;
    });

});