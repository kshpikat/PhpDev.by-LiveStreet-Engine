

jQuery(document).ready(function(){
	
    ls.comments.options.text = null;
    ls.comments.options.commentId = 0;
    ls.comments.options.newForm = $('#edit-comment').children('div');
	
    ls.comments.shbutton = function(formObj, targetId, t) {
	$('.info li.save, .info li.cancel').css('display', 'none');
	$('.info li.edit').css('display', 'block');
	if (t=='edit'){
	    var ulButon = $(formObj).parent('li').parent('ul');
	    ulButon.children('li.edit').css('display', 'block');
	    ulButon.children('li.save').css('display', 'block');
	    ulButon.children('li.cancel').css('display', 'block');
	    $(formObj).parent('li').css('display', 'none');
	}
    }
	
    ls.comments.showform = function(formObj, targetId) {
	ls.comments.shbutton(formObj,targetId,'edit');
	if (ls.comments.options.commentId==targetId){
	    return false;
	}
	if (this.options.wysiwyg) {	   
	    tinyMCE.execCommand('mceRemoveControl',false,'form_comment_edit_text');
	}
	var text = $.trim($('#comment_content_id_'+targetId).html());
	if (ls.comments.options.commentId!=targetId && ls.comments.options.commentId != 0){
	    $('#comment_content_id_'+targetId).empty();
	    $('#comment_content_id_'+ls.comments.options.commentId).children('div').appendTo($('#comment_content_id_'+targetId));
	    $('#comment_content_id_'+ls.comments.options.commentId).html(ls.comments.options.text);
	    //$('#comment_content_id_'+targetId).children('div').remove();
	    //$('#form_comment_edit_text').val('');
	} else {
	    $('#comment_content_id_'+targetId).empty();
	    $('#edit-comment').children('div').appendTo($('#comment_content_id_'+targetId));
	}	   
	ls.comments.options.text = text;
	ls.comments.options.commentId = targetId;
	$('#form_comment_edit_text').val(text); 
	if (this.options.wysiwyg) {
		tinyMCE.execCommand('mceAddControl',true,'form_comment_edit_text');
	}
	return false;
    };
	
    ls.comments.cancel = function(formObj, targetId) {
	ls.comments.shbutton(formObj,targetId,'cancel');
	$('#comment_content_id_'+ls.comments.options.commentId).children('div').appendTo($('#edit-comment'));
	$('#comment_content_id_'+targetId).html(ls.comments.options.text);
	ls.comments.options.commentId = 0;
	return false;
    };
	
    ls.comments.edit = function(formObj, targetId) { 
	if (this.options.wysiwyg) {
		$('#form_comment_edit_text').val(tinyMCE.activeEditor.getContent());
	}
	var text = $('#form_comment_edit_text').val();
	ls.ajax(aRouter['ec_ajax']+'savecomment/',{ commentId: targetId, text:text, security_ls_key: LIVESTREET_SECURITY_KEY  },function(result) {
	    if (result.bStateError) {
		ls.msg.error(null, result.sMsg);
	    } else {
		ls.msg.notice(null, result.sMsg);
		ls.comments.shbutton(formObj,targetId,'cancel');
		$('#comment_content_id_'+ls.comments.options.commentId).children('div').appendTo($('#edit-comment'));
		$('#comment_content_id_'+targetId).html(result.sText);
		$('#info_edit_'+targetId).html(result.sTextEdit).show();
		ls.comments.options.commentId = 0;
		return false;
	    }
	});		
    };

});