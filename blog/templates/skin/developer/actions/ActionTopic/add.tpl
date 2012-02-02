{include file='header.tpl' menu='topic_action'}


{literal}
<script language="JavaScript" type="text/javascript">
document.addEvent('domready', function() {	
	new Autocompleter.Request.LS.JSON($('topic_tags'), aRouter['ajax']+'autocompleter/tag/?security_ls_key='+LIVESTREET_SECURITY_KEY, {
		'indicatorClass': 'autocompleter-loading', // class added to the input during request
		'minLength': 2, // We need at least 1 character
		'selectMode': 'pick', // Instant completion
		'multiple': true // Tag support, by default comma separated
	}); 
});
</script>
{/literal}


{if $oConfig->GetValue('view.tinymce')}
<script type="text/javascript" src="{cfg name='path.root.engine_lib'}/external/tinymce/tiny_mce.js"></script>

<script type="text/javascript">
{literal}
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_buttons1 : "lshselect,bold,italic,underline,strikethrough,|,bullist,numlist,|,undo,redo,|,lslink,unlink,lsvideo,lsimage,pagebreak,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : 0,
	theme_advanced_resizing_use_cookie : 0,
	theme_advanced_path : false,
	object_resizing : true,
	force_br_newlines : true,
        inline_styles:false,
         formats : {
             underline : {inline : 'u', exact : true},
            strikethrough : {inline : 's', exact : true}
        },
    forced_root_block : '', // Needed for 3.x
    force_p_newlines : false,    
    plugins : "lseditor,safari,inlinepopups,media,pagebreak",
    convert_urls : false,
    extended_valid_elements : "embed[src|type|allowscriptaccess|allowfullscreen|width|height]",
    pagebreak_separator :"<cut>",
    media_strict : false,
    language : TINYMCE_LANG
});
{/literal}
</script>

{else}
	{include file='window_load_img.tpl' sToLoad='topic_text'}
{/if}



<div class="topic" style="display: none;">
	<div class="content" id="text_preview"></div>
</div>


<div class="profile-user">
	{if $sEvent=='add'}
		<h2>{$aLang.topic_topic_create}</h2>
	{else}
		<h2>{$aLang.topic_topic_edit}</h2>
	{/if}

	{hook run='add_topic_topic_begin'}
	<form action="" method="POST" enctype="multipart/form-data">
		{hook run='form_add_topic_topic_begin'}

		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />

		<p><label for="blog_id">{$aLang.topic_create_blog}</label><br />
		<select name="blog_id" id="blog_id" onChange="ajaxBlogInfo(this.value);" class="input-300">
			<option value="0">{$aLang.topic_create_blog_personal}</option>
			{foreach from=$aBlogsAllow item=oBlog}
				<option value="{$oBlog->getId()}" {if $_aRequest.blog_id==$oBlog->getId()}selected{/if}>{$oBlog->getTitle()|escape:'html'}</option>
			{/foreach}
		</select></p>

		<script language="JavaScript" type="text/javascript">
			ajaxBlogInfo($('blog_id').value);
		</script>

		<p><label for="topic_title">{$aLang.topic_create_title}:</label><br />
		<input type="text" id="topic_title" name="topic_title" value="{$_aRequest.topic_title}" class="input-wide" /><br />
		<span class="note">{$aLang.topic_create_title_notice}</span></p>

		<label for="topic_text">{$aLang.topic_create_text}{if !$oConfig->GetValue('view.tinymce')} ({$aLang.topic_create_text_notice}){/if}:</label>
		{if !$oConfig->GetValue('view.tinymce')}
			<div class="panel-form">
				{hook run='form_add_topic_panel_begin'}
				<select onchange="lsPanel.putTagAround('topic_text',this.value); this.selectedIndex=0; return false;">
					<option value="">{$aLang.panel_title}</option>
					<option value="h4">{$aLang.panel_title_h4}</option>
					<option value="h5">{$aLang.panel_title_h5}</option>
					<option value="h6">{$aLang.panel_title_h6}</option>
				</select>
				<select onchange="lsPanel.putList('topic_text',this); return false;">
					<option value="">{$aLang.panel_list}</option>
					<option value="ul">{$aLang.panel_list_ul}</option>
					<option value="ol">{$aLang.panel_list_ol}</option>
				</select>
				<a href="#" onclick="lsPanel.putTagAround('topic_text','b'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/bold.png" title="{$aLang.panel_b}" /></a>
				<a href="#" onclick="lsPanel.putTagAround('topic_text','i'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/italic.png" title="{$aLang.panel_i}" /></a>
				<a href="#" onclick="lsPanel.putTagAround('topic_text','u'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/underline.png" title="{$aLang.panel_u}" /></a>
				<a href="#" onclick="lsPanel.putTagAround('topic_text','s'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/strikethrough.png" title="{$aLang.panel_s}" /></a>
				&nbsp;
				<a href="#" onclick="lsPanel.putTagUrl('topic_text','{$aLang.panel_url_promt}'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/link.png"  title="{$aLang.panel_url}" /></a>
				<a href="#" onclick="lsPanel.putTagUser('topic_text','{$aLang.panel_user_promt}'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/user.png"  title="{$aLang.panel_user}" /></a>
				<a href="#" onclick="lsPanel.putQuote('topic_text'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/quote.png" title="{$aLang.panel_quote}" /></a>
				<a href="#" onclick="lsPanel.putTagAround('topic_text','code'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/code.png" title="{$aLang.panel_code}" /></a>
				<a href="#" onclick="lsPanel.putTagAround('topic_text','video'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/video.png" title="{$aLang.panel_video}" /></a>

				<a href="#" onclick="showImgUploadForm(); return false;"><img src="{cfg name='path.static.skin'}/images/panel/img.png" title="{$aLang.panel_image}" /></a>
				<a href="#" onclick="lsPanel.putText('topic_text','<cut>'); return false;"><img src="{cfg name='path.static.skin'}/images/panel/cut.png" title="{$aLang.panel_cut}" /></a>
				{hook run='form_add_topic_panel_end'}
			</div>
		{/if}
		<textarea name="topic_text" id="topic_text" rows="20" class="input-wide">{$_aRequest.topic_text}</textarea><br /><br />

		<p><label for="topic_tags">{$aLang.topic_create_tags}:</label><br />
		<input type="text" id="topic_tags" name="topic_tags" value="{$_aRequest.topic_tags}" class="input-wide" /><br />
		<span class="note">{$aLang.topic_create_tags_notice}</span></p>

		<p><label for=""><input type="checkbox" id="topic_forbid_comment" name="topic_forbid_comment" class="checkbox" value="1" {if $_aRequest.topic_forbid_comment==1}checked{/if} />
		{$aLang.topic_create_forbid_comment}</label><br />
		<span class="note">{$aLang.topic_create_forbid_comment_notice}</span></p>

		{if $oUserCurrent->isAdministrator()}
			<p><label for=""><input type="checkbox" id="topic_publish_index" name="topic_publish_index" class="checkbox" value="1" {if $_aRequest.topic_publish_index==1}checked{/if} />
			{$aLang.topic_create_publish_index}</label><br />
			<span class="note">{$aLang.topic_create_publish_index_notice}</span></p>
		{/if}

		{hook run='form_add_topic_topic_end'}

		<input type="submit" name="submit_topic_publish" value="{$aLang.topic_create_submit_publish}" />
		<input type="submit" name="submit_preview" value="{$aLang.topic_create_submit_preview}" onclick="$('text_preview').getParent('div').setStyle('display','block'); ajaxTextPreview('topic_text',false); return false;" />
		<input type="submit" name="submit_topic_save" value="{$aLang.topic_create_submit_save}" />
	</form>
	{hook run='add_topic_topic_end'}
	
</div>


{include file='footer.tpl'}