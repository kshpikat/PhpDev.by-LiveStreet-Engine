/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.extraPlugins = 'tagcut,lsupload,youtube';

	config.language = 'ru';

	config.toolbar_NewFull =
	[
	{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Print','-','Templates' ] },
	{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo', '-','Redo', 'TagCut' ] },
	{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	'/',
	{ name: 'insert', items : [ 'Youtube', 'LsUpload', 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	'/',
	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];

	config.toolbar = 'NewFull';
//	config.filebrowserUploadUrl = '/plugins/ckeditor/ckupload.php';
//	{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button','ImageButton', 'HiddenField' ] },
//	{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
//	{ name: 'colors', items : [ 'TextColor','BGColor' ] },

};
