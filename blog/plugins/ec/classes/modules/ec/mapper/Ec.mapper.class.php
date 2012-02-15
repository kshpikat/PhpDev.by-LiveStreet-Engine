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

class PluginEc_ModuleEc_MapperEc extends Mapper
{

    public function UpdateComment(ModuleComment_EntityComment $oComment)
    {
	$sql = "UPDATE " . Config::Get('db.table.comment') . " 
		SET 
			comment_date_edit = ?,
			comment_edit_user_id = ?d
		WHERE
			comment_id = ?d
		";
	if ($this->oDb->query($sql, $oComment->getCommentDateEdit(), $oComment->getCommentEditUserId(), $oComment->getId())) {
	    return true;
	}
	return false;
    }

    public function GetCountChildrenByCommentId($sId)
    {
	$sql = "SELECT 
			count(comment_id) as c
		FROM 
			" . Config::Get('db.table.comment') . "
		WHERE 
			comment_pid = ?d 
			AND comment_publish = '1';";

	if ($aRow = $this->oDb->selectRow($sql, $sId)) {
	    return $aRow['c'];
	}
    }

}

?>
