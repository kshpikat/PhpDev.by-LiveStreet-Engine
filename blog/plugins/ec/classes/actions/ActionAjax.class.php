<?php

/* -------------------------------------------------------
 *
 *   LiveStreet (v0.5x)
 *   Plugin Edit comment (v.0.2)
 *   Copyright © 2011 Bishovec Nikolay
 *
 * --------------------------------------------------------
 *
 *   Plugin Page: http://netlanc.net
 *   Contact e-mail: netlanc@yandex.ru
 *
  ---------------------------------------------------------
 */


class PluginEc_ActionAjax extends ActionPlugin
{

    protected $oUserCurrent = null;

    /**
     * Инициализация
     *
     */
    public function Init()
    {
	$this->Viewer_SetResponseAjax('json');
	$this->oUserCurrent = $this->User_GetUserCurrent();
    }

    /**
     * Регистрируем необходимые евенты
     *
     */
    protected function RegisterEvent()
    {
	$this->AddEvent('savecomment', 'SaveComment');
    }

    protected function SaveComment()
    {
	
	if (!$this->oUserCurrent or (!$this->oUserCurrent->isAdministrator() and !Config::Get('plugin.ec.edit_author'))) {
	    $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
	    return;
	} 
	
	if (!($oComment = $this->Comment_GetCommentById(getRequest('commentId', null)))) {
	    $this->Message_AddErrorSingle($this->Lang_Get('ce_comment_error_not_found'), $this->Lang_Get('error'));
	    return;
	}
	
	if (!$this->oUserCurrent->isAdministrator()){
	    if ($this->oUserCurrent and Config::Get('plugin.ec.edit_author') and $oComment->getUserId()!=$this->oUserCurrent->getId()){
		$this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
		return;
	    }
	    if (Config::Get('plugin.ec.limit_edit_time') and strtotime($oComment->getDate()) < strtotime(date("Y-m-d H:i:s", time()-Config::Get('plugin.ec.limit_edit_time')))){
		$this->Message_AddErrorSingle($this->Lang_Get('not_access_time_limit'), $this->Lang_Get('error'));
		return;
	    }
	    if (Config::Get('plugin.ec.children_isset') and $this->PluginEc_Ec_GetCountChildrenByCommentId($oComment->getId())){
		$this->Message_AddErrorSingle($this->Lang_Get('not_access_children_isset'), $this->Lang_Get('error'));
		return;
	    }
	}
	
	$sText = $this->Text_Parser(getRequest('text'));
	if (!func_check($sText, 'text', 2, 10000)) {
	    $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'), $this->Lang_Get('error'));
	    return;
	}

	$oComment->setText($sText);
	$oComment->setCommentTextSource(getRequest('comment_text'));
	$oComment->setTextHash(md5($sText));
	$oComment->setCommentDateEdit(date("Y-m-d H:i:s"));
	$oComment->setCommentEditUserId($this->oUserCurrent->getId());
	
	if ($this->Comment_UpdateComment($oComment)) {
	    $this->PluginEc_Ec_UpdateComment($oComment);
	    $this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
	    $this->User_Update($this->oUserCurrent);
	}


	$this->Viewer_AssignAjax('sTextEdit', $this->Lang_Get('ce_comment_info_edit_user', array('login'=>$this->oUserCurrent->getLogin(),'date'=>$oComment->getCommentDateEdit())));
	$this->Viewer_AssignAjax('sText', $sText);
	$this->Message_AddNotice($this->Lang_Get('ce_comment_edit_ok'), $this->Lang_Get('attention'));
	return;
    }

}

?>