window.addEvent('domready', function(){
	var oDiv = $$('form .panel-form');
//	var oEditor;

	if (oDiv) {
		oDiv.destroy();
	}
	var oDiv = $$('form .panel_form');
	if (oDiv) {
		oDiv.destroy();
	}
	if ($('topic_text')!== null) {
		var oEditor = CKEDITOR.replace( 'topic_text' );
		$$("input[name=submit_preview]").set('onclick','');

		$$("input[name=submit_preview]").addEvent('click',  function(e) {
			e.preventDefault();
			$('text_preview').getParent('div').setStyle('display','block');
			$('topic_text').set('html',window.frames[0].document.body.innerHTML);
			ajaxTextPreview('topic_text',false);
			return false;
		});

	}
});

function ajaxUploadImgEx(form,sToLoad) {
	if (typeof(form)=='string') {
		form=$(form);
	}

	var iFrame = new iFrameFormRequest(form.getProperty('id'),{
		url: aRouter['ajax']+'upload/image/',
		dataType: 'json',
		params: {security_ls_key: LIVESTREET_SECURITY_KEY},
		onComplete: function(response){
			if (response.bStateError) {
				msgErrorBox.alert(response.sMsgTitle,response.sMsg);				
			} else {

				oEditor = CKEDITOR.instances.topic_text;
				// Check the active editing mode.
				if ( oEditor.mode == 'wysiwyg' ) {
					// Insert HTML code.
					oEditor.insertHtml( response.sText );
				} else {
					alert( 'You must be in WYSIWYG mode!' );
				}
				hideImgUploadForm();
			}
		}
	});
	iFrame.send();
}

function ink_prepare_button() {

		$$("input[value=Загрузить]").set('onclick','');
		$$("input[value=Загрузить]").addEvent('click',  function(e) {
			e.preventDefault();
			ajaxUploadImgEx(document.getElementById('form_upload_img'),'topic_text');
			return false;
		});
}

