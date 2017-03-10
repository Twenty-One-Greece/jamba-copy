function ips_open_media_up(target, callback){
          //If the uploader object has already been created, reopen the dialog
              var custom_uploader;
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery(target).val(attachment.url);
            if(typeof callback!='undefined'){
            	window[callback]();
            }
        });
        //Open the uploader dialog
        custom_uploader.open();
}