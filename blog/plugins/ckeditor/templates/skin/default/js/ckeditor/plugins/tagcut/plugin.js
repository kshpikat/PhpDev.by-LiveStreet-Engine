CKEDITOR.plugins.add( 'tagcut',
{
	init: function( editor )
	{
	
		editor.addCommand( 'insertCut',
			{
				exec : function( editor )
				{    
						var theSelectedText = editor.getSelection().getSelectedText();
						var sResult = "<cut>"+theSelectedText+"</cut>";
						editor.insertHtml(sResult);
				}
			});

		// Plugin logic goes here...
		editor.ui.addButton( 'TagCut',
		{
			label: 'Insert CUT',
			command: 'insertCut',
			icon: this.path + 'images/tagcut.png'
		} );
	}
} );