{assign var="oBlog" value=$oTopic->getBlog()} 
{assign var="oUser" value=$oTopic->getUser()}
{assign var="oVote" value=$oTopic->getVote()} 

<script language="JavaScript" type="text/javascript">
window.addEvent('load', function() {	
	photosetShowMainPhoto({$oTopic->getId()});
});
</script>

<div class="topic photo">
	<div class="favorite {if $oUserCurrent}{if $oTopic->getIsFavourite()}active{/if}{else}fav-guest{/if}"><a href="#" onclick="lsFavourite.toggle({$oTopic->getId()},this,'topic'); return false;"></a></div>
	
	
	<h1 class="title">
		{if $oTopic->getPublish()==0}	
			<img src="{cfg name='path.static.skin'}/images/topic_unpublish.gif" border="0" title="{$aLang.topic_unpublish}" width="16" height="16" alt="{$aLang.topic_unpublish}">
		{/if}
		{if $bTopicList}
			<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
		{else}
			{$oTopic->getTitle()|escape:'html'}
		{/if}
	</h1>
	
	
	<ul class="action">					
		<li><a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>&nbsp;&nbsp;</li>						
		{if $oUserCurrent and ($oUserCurrent->getId()==$oTopic->getUserId() or $oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oBlog->getOwnerId()==$oUserCurrent->getId())}
			<li class="edit"><a href="{cfg name='path.root.web'}/{$oTopic->getType()}/edit/{$oTopic->getId()}/" title="{$aLang.topic_edit}">{$aLang.topic_edit}</a></li>
		{/if}
		{if $oUserCurrent and ($oUserCurrent->isAdministrator() or $oBlog->getUserIsAdministrator() or $oBlog->getOwnerId()==$oUserCurrent->getId())}
			<li class="delete"><a href="{router page='topic'}delete/{$oTopic->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" title="{$aLang.topic_delete}" onclick="return confirm('{$aLang.topic_delete_confirm}');">{$aLang.topic_delete}</a></li>
		{/if}
	</ul>
	
	{assign var=iPhotosCount value=$oTopic->getPhotosetCount()}
	<!-- Topic Photo Preview -->
	<div class="topic-photo-preview" id="photoset-main-preview-{$oTopic->getId()}">
        {assign var=oMainPhoto value=$oTopic->getPhotosetMainPhoto()}
		<div class="topic-photo-count" onclick="window.location='{$oTopic->getUrl()}#photoset'" id="photoset-photo-count-{$oTopic->getId()}">{$oTopic->getPhotosetCount()} {$aLang.topic_photoset_photos}</div>
		{if $oMainPhoto->getDescription()}
			<div class="topic-photo-desc" id="photoset-photo-desc-{$oTopic->getId()}">{$oMainPhoto->getDescription()}</div>
		{/if}
		<img src="{$oMainPhoto->getWebPath(500)}" alt="image" id="photoset-main-image-{$oTopic->getId()}" />
	</div>
	<!-- /Topic Photo Preview -->
	
	
	<div class="content">
		{if $bTopicList}
			{$oTopic->getTextShort()}
			{if $oTopic->getTextShort()!=$oTopic->getText()}
				<br /><a href="{$oTopic->getUrl()}#cut" title="{$aLang.topic_read_more}">
				{if $oTopic->getCutText()}
					{$oTopic->getCutText()}
				{else}
					{$aLang.topic_photoset_show_all|ls_lang:"COUNT%%`$iPhotosCount`"} &rarr;
				{/if}                           
				</a>
			{/if}
		{else}
			{$oTopic->getText()}
		{/if}
	</div>	
	
	<!-- Topic Photo Image List -->
	{if !$bTopicList}
		<script type="text/javascript" src="{cfg name='path.root.engine_lib'}/external/milkbox-3.0.1/js/milkbox.js"></script>	
		<link rel='stylesheet' type='text/css' href="{cfg name='path.root.engine_lib'}/external/milkbox-3.0.1/css/milkbox.css" />
		
		<div class="topic-photo-images">
			<h2>{$oTopic->getPhotosetCount()} {$oTopic->getPhotosetCount()|declension:$aLang.topic_photoset_count_images}</h2>
			<a name="photoset"></a>
			<ul id="topic-photo-images" >
                            {assign var=aPhotos value=$oTopic->getPhotosetPhotos(0, $oConfig->get('module.topic.photoset.per_page'))}
                            {if count($aPhotos)}                                
                                {foreach from=$aPhotos item=oPhoto}
                                    <li><a  class="photoset-image" href="{$oPhoto->getWebPath(1000)}" rel="milkbox[photoset]"  title="{$oPhoto->getDescription()}"><img src="{$oPhoto->getWebPath('50crop')}" alt="{$oPhoto->getDescription()}" /></a></li>
                                    {assign var=iLastPhotoId value=$oPhoto->getId()}
                                {/foreach}
                            {/if}
                            <script type="text/javascript">
                            	idLastPhotoset='{$iLastPhotoId}';
                            </script>
			</ul>
			{if count($aPhotos)<$oTopic->getPhotosetCount()}
				<a href="javascript:getMorePhotos({$oTopic->getId()})" id="topic-photo-more" class="topic-photo-more">{$aLang.topic_photoset_show_more} &darr;</a>
			{/if}
		</div>
	{/if}
	<!-- /Topic Photo Image List -->
	
	
	<ul class="tags">
		{foreach from=$oTopic->getTagsArray() item=sTag name=tags_list}
			<li><a href="{router page='tag'}{$sTag|escape:'url'}/">{$sTag|escape:'html'}</a>{if !$smarty.foreach.tags_list.last}, {/if}</li>
		{/foreach}									
	</ul>	

	
	<ul class="voting {if $oVote || ($oUserCurrent && $oTopic->getUserId()==$oUserCurrent->getId())|| strtotime($oTopic->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}{if $oTopic->getRating()>0}positive{elseif $oTopic->getRating()<0}negative{/if}{/if} {if !$oUserCurrent || $oTopic->getUserId()==$oUserCurrent->getId() || strtotime($oTopic->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')}guest{/if} {if $oVote} voted {if $oVote->getDirection()>0}plus{elseif $oVote->getDirection()<0}minus{/if}{/if}">
		<li class="plus"><a href="#" onclick="lsVote.vote({$oTopic->getId()},this,1,'topic'); return false;"></a></li>
		<li class="total" title="{$aLang.topic_vote_count}: {$oTopic->getCountVote()}">{if $oVote || ($oUserCurrent && $oTopic->getUserId()==$oUserCurrent->getId()) || strtotime($oTopic->getDateAdd())<$smarty.now-$oConfig->GetValue('acl.vote.topic.limit_time')} {if $oTopic->getRating()>0}+{/if}{$oTopic->getRating()} {else} <a href="#" onclick="lsVote.vote({$oTopic->getId()},this,0,'topic'); return false;">&mdash;</a> {/if}</li>
		<li class="minus"><a href="#" onclick="lsVote.vote({$oTopic->getId()},this,-1,'topic'); return false;"></a></li>
		<li class="date">{date_format date=$oTopic->getDateAdd()}</li>
		<li class="author"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
		{if $bTopicList}
			<li class="comments-total">
				{if $oTopic->getCountComment()>0}
					<a href="{$oTopic->getUrl()}#comments" title="{$aLang.topic_comment_read}"><span class="red">{$oTopic->getCountComment()}</span>{if $oTopic->getCountCommentNew()}<span class="green">+{$oTopic->getCountCommentNew()}</span>{/if}</a>
				{else}
					<a href="{$oTopic->getUrl()}#comments" title="{$aLang.topic_comment_add}"><span class="red">{$aLang.topic_comment_add}</span></a>
				{/if}
			</li>
		{/if}
		{hook run='topic_show_info' topic=$oTopic}
	</ul>
	
	{if !$bTopicList}
		{hook run='topic_show_end' topic=$oTopic}
	{/if}
</div>