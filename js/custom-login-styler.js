jQuery(document).ready(function($) {

    var frame,
        addImgLink = $('.tmprc-media-upload');

    // ADD IMAGE LINK
    addImgLink.on( 'click', function( event ){

        event.preventDefault();

        var imgIdInput = $(this).closest('.img-wrap').find('.image-id').first();
        var imgContainer = $(this).closest('.img-wrap').find('.image-preview-wrapper').first();

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media',
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
            imgContainer.find( 'img' ).remove();
            imgContainer.append( '<img src="'+attachment.url+'" alt="" style="width:auto;height:150px;">' );

            // Send the attachment id to our hidden input
            imgIdInput.val( attachment.id );

        });

        // Finally, open the modal on click
        frame.open();
    });

    // remove image
    $( '.tmprc_delete_image').click( function(){
        $(this).closest('.img-wrap').find('.image-id').val('');
        $(this).closest('.img-wrap').find('.image-preview').hide();
        $(this).hide();
    });

});