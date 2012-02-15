{assign var="oUser" value=$oComment->getUser()}
{assign var="oVote" value=$oComment->getVote()}

<div id="comment_id_{$oComment->getId()}" class="comment {if !$oUserCurrent or ($oUserCurrent and !$oUserCurrent->isAdministrator())}not-admin{/if} {if $oComment->getDelete()} deleted{elseif $oUserCurrent and $oComment->getUserId()==$oUserCurrent->getId()} self{elseif $sDateReadLast<=$oComment->getDate()} new{/if}" >
{if !$oComment->getDelete() or $bOneComment or ($oUserCurrent and $oUserCurrent->isAdministrator())}
	<a name="comment{$oComment->getId()}" ></a>
	
	
	<ul class="info">
		<li style="float: left;" class="avatar"><a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" /></a></li>
		<li style="float: left;" class="username"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
		<li style="float: left;" class="date">{date_format date=$oComment->getDate()}</li>
		<li style="float: left;"><a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}#comment{/if}{$oComment->getId()}">#</a></li>	
		{if $oComment->getPid()}
			<li style="float: left;" class="goto-comment-parent"><a href="#" onclick="ls.comments.goToParentComment({$oComment->getId()},{$oComment->getPid()}); return false;" title="{$aLang.comment_goto_parent}">↑</a></li>
		{/if}
		<li class="goto-comment-child"><a href="#" title="{$aLang.comment_goto_child}">↓</a></li>
		{if $oUserCurrent and !$bNoCommentFavourites}
			<li style="float: left;"><a href="#" onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="favourite {if $oComment->getIsFavourite()}active{/if}"></a></li>
		{/if}
		{if !$oComment->getDelete() and $oUserCurrent and $oUserCurrent->isAdministrator()}
			<li style="float: left;"><a href="#" class="delete" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_delete}</a></li>
		{/if}
		{if $oComment->getDelete() and $oUserCurrent and $oUserCurrent->isAdministrator()}   										
			<li style="float: left;"><a href="#" class="repair" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_repair}</a></li>
		{/if}
		{hook run='comment_action' comment=$oComment user_current=$oUserCurrent}
		{if $oComment->getTargetType()!='talk'}						
			<li id="vote_area_comment_{$oComment->getId()}" class="voting {if $oComment->getRating()>0}positive{elseif $oComment->getRating()<0}negative{/if} {if !$oUserCurrent || $oComment->getUserId()==$oUserCurrent->getId() ||  strtotime($oComment->getDate())<$smarty.now-$oConfig->GetValue('acl.vote.comment.limit_time')}guest{/if}   {if $oVote} voted {if $oVote->getDirection()>0}plus{else}minus{/if}{/if}  ">
				<a href="#" class="plus" onclick="return ls.vote.vote({$oComment->getId()},this,1,'comment');"></a>
				<span id="vote_total_comment_{$oComment->getId()}" class="total">{$oComment->getRating()}</span>
				<a href="#" class="minus" onclick="return ls.vote.vote({$oComment->getId()},this,-1,'comment');"></a>
			</li>
		{/if}
	</ul>
	
	
	<div id="comment_content_id_{$oComment->getId()}" class="content">						
		{if $oComment->isBad()}
			<div class="text-style" style="display: none;" id="comment_text_{$oComment->getId()}">
				{$oComment->getText()}
			</div>
			 <a href="#" onclick="jQuery('#comment_text_{$oComment->getId()}').show();jQuery(this).hide();return false;">{$aLang.comment_bad_open}</a>
		{else}	
			{$oComment->getText()}
		{/if}
	</div>
	
	<div id="info_edit_{$oComment->getId()}" class="info_edit" {if !$oComment->getCommentEditUserId()} style="display: none;"{/if}>
	    {if $oComment->getCommentEditUserId()}
		{assign var="oUserEdit" value=$oComment->getEditUserLogin()}
		{$aLang.ce_comment_info_edit_user|ls_lang:"login%%`$oUserEdit->getLogin()`":"date%%`$oComment->getCommentDateEdit()`"}
	    {/if}
	</div>
		
		
	{if $oUserCurrent}
		<div class="actions">
			{if !$oComment->getDelete() and !$bAllowNewComment}<a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link">{$aLang.comment_answer}</a>{/if}
		</div>
	{/if}
{else}				
	{$aLang.comment_was_delete}
{/if}
{if $oUserCurrent}
	<div class="comment" id="comment_preview_{$oComment->getId()}" style="display: none;"><div class="comment-inner"><div class="content"></div></div></div>					
	<div class="reply" id="reply_{$oComment->getId()}" style="display: none;"></div>	
{/if}	
</div>