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


class PluginEc_ModuleComment_EntityComment extends PluginEc_Inherit_ModuleComment_EntityComment
{

    public function getEditUserLogin()
    {
	return $this->User_GetUserById($this->getCommentEditUserId());
    }

}

?>
