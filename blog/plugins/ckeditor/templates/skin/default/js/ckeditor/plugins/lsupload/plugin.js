CKEDITOR.plugins.add( 'lsupload',
{
	init: function( editor )
	{
	
		editor.addCommand( 'lsUpload',
			{
				exec : function( editor )
				{    
					//showImgUploadForm();
					$('#form_upload_img').jqmShow();
					ink_prepare_button(); // Из-за двойственности LS пришлось вынести функционал в JS-lib-зависимые файлы.
				}
			});

		// Plugin logic goes here...
		editor.ui.addButton( 'LsUpload',
		{
			label: 'Загрузить и вставить фотографию',
			command: 'lsUpload',
			icon: this.path + 'images/lsupload.png'
		} );
	}
} );