{if $oUserCurrent}	
    {if !$oConfig->GetValue('view.tinymce')}
	<script type="text/javascript">
	jQuery(document).ready(function($){
	    ls.lang.load({lang_load name="panel_b,panel_i,panel_u,panel_s,panel_url,panel_url_promt,panel_code,panel_video,panel_image,panel_cut,panel_quote,panel_list,panel_list_ul,panel_list_ol,panel_title,panel_clear_tags,panel_video_promt,panel_list_li,panel_image_promt,panel_user,panel_user_promt"});
	    // Подключаем редактор
	    $('#form_comment_edit_text').markItUp(getMarkitupCommentSettings());
	});
	</script>  
    {/if}

    <div class="edit-comment" id="edit-comment" style="margin: 10px; display: none;">
	<div> 
	    <textarea name="form_comment_edit_text" id="form_comment_edit_text" class="input-wide" style="width: 98%; height: 100px;"></textarea>
	</div>
    </div>

{/if}    



