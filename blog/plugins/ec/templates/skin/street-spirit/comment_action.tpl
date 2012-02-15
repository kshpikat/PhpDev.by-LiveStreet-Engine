{if $oUserCurrent and ($oUserCurrent->isAdministrator() or $oComment->getUserId()==$oUserCurrent->getId())}   										
    <li class="edit" style="float: left;"><a href="#" onclick="ls.comments.showform(this, {$oComment->getId()}); return false;">{$aLang.ec_comment_edit}</a></li>
    <li class="save" style="float: left;"><a href="#" onclick="ls.comments.edit(this, {$oComment->getId()}); return false;">{$aLang.ec_comment_save}</a></li>
    <li class="cancel" style="float: left;"><a href="#" onclick="ls.comments.cancel(this, {$oComment->getId()}); return false;">{$aLang.ec_comment_cancel}</a></li>
{/if}
