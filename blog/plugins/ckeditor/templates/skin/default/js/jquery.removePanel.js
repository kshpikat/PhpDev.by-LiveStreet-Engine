$(document).ready(function() {
	$('form[id!=form_comment] .panel-form').remove();
	$('form[id!=form_comment] .panel_form').remove();
	setTimeout(function() {
		$("form[id!=form_comment] .markItUpHeader").remove();
		$("form[id!=form_comment] .markItUpFooter").remove();
	},12);
	if ($('#topic_text').attr('id') != undefined) {
		CKEDITOR.replace( 'topic_text' );

		$("input[name=submit_preview]").attr('onclick','');
		$("input[name=submit_preview]").click(function(e){
			jQuery('#text_preview').parent().show();
			$('#topic_text').html($('#cke_contents_topic_text iframe').contents().find('body').html());
			ls.tools.textPreview('topic_text',false); 
			return false;
		});
	}
});

function ajaxUploadImgEx (form, sToLoad) {
		ls.ajaxSubmit('upload/image/',form,function(data){
			if (data.bStateError) {
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				
				oEditor = CKEDITOR.instances.topic_text;
				if ( oEditor.mode == 'wysiwyg' ) {
					// Insert HTML code.
					oEditor.insertHtml( data.sText );
				} else {
					alert( 'You must be in WYSIWYG mode!' );
				}
				$('#form_upload_img').find('input[type="text"], input[type="file"]').val('');
				$('#form_upload_img').jqmHide();
			}
		});
}


function ink_prepare_button() {

		$('input[value=Загрузить]').attr('onclick','');
		$("input[value=Загрузить]").bind('click',  function() {
			ajaxUploadImgEx('form_upload_img','topic_text');
			return false;
		});

}