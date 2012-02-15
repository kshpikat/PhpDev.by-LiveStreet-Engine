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

class PluginEc_ModuleEc extends Module
{

    protected $oMapper;

    public function Init()
    {
	$this->oMapper = Engine::GetMapper(__CLASS__);
    }

    public function UpdateComment(ModuleComment_EntityComment $oComment)
    {
	if ($this->oMapper->UpdateComment($oComment)) {
	    //чистим зависимые кеши
	    $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("comment_update", "comment_update_{$oComment->getTargetType()}_{$oComment->getTargetId()}"));
	    $this->Cache_Delete("comment_{$oComment->getId()}");
	    return true;
	}
	return false;
    }
    public function GetCountChildrenByCommentId($sId)
    {
	return $this->oMapper->GetCountChildrenByCommentId($sId);
    }

}

?>
