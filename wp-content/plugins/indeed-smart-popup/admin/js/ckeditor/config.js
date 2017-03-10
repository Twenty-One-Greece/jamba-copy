/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	 config.language = 'en';
	 config.height = 400; // 500 pixels.
	 config.skin = 'moonocolor'; 
	 config.allowedContent = true;
config.toolbar = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates', '-', 'Maximize' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
	{ name: 'extra1', items: [ 'wenzgmap','tliyoutube' ] },
	'/',
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
	'/',
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat' ] },
	{ name: 'others', items: [ '-' ] },
	'/',
	'/',
	{ name: 'Subscribe Builder', items: [ 'Form', '-','Checkbox', 'Radio', 'TextField', 'Textarea', 'Select','-', 'Button', 'ImageButton','-', 'HiddenField' ] }
];

// Toolbar groups configuration.
config.toolbarGroups = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
	'/',
	{ name: 'extra1' },
	'/',
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	{ name: 'links' },
	{ name: 'insert' },
	'/',
	{ name: 'styles' },
	{ name: 'colors' },
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	{ name: 'others' },
	'/',
	'/',
	{ name: 'Subscribe Builder' },
];
	 //config.uiColor = '#d9dde3';
CKEDITOR.on( 'dialogDefinition', function( ev ) {
    // Take the dialog name and its definition from the event data.
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;
	console.log(dialogDefinition);
    // Check if the definition is from the dialog window you are interested in (the "Link" dialog window).
    if ( dialogName == 'form' ) {
        // Get a reference to the "Link Info" tab.
        var infoTab = dialogDefinition.getContents( 'info' );

        // Set the default value for the URL field.
        var idField = infoTab.get( 'id' );
        idField[ 'default' ] = 'isp_form_'+jQuery('#popup_id').val();
		idField.inputStyle = 'visibility: hidden; display:none;';
		idField.label = '';
		var idField = infoTab.get( 'enctype' );
		idField.inputStyle = 'visibility: hidden; display:none;';
		idField.label = '';
		var idField = infoTab.get( 'target' );
		idField.inputStyle = 'visibility: hidden; display:none;';
		idField.label = '';
		idField = infoTab.get( 'method' );
		idField[ 'default' ] = 'post';		
    }
});

config.extraPlugins = 'tliyoutube,wenzgmap';	 
};