/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'document',	groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'basicstyles', groups: [ 'basicstyles' ] },
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar';

	// Set the most common block elements.
	//config.format_tags = 'p;h1;h2;h3;pre';

	config.allowedContent = 'h1 h2 h3 h4 h5 h6 p strong;';

	config.ignoreEmptyParagraph = false;
	
	//font-size: 15px; color: #000; text-align: justify; font-weight: 400;margin: 4px 0;

	// Simplify the dialog windows.
	//config.removeDialogTabs = 'image:advanced;link:advanced';
};
