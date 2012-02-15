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


class PluginEc_HookEc extends Hook
{

    public function RegisterHook()
    {	
	$this->AddHook('template_comment_action', 'CommentAction', __CLASS__);	
    }

    public function InitAction()
    {

    }

    public function MenuSettingsTpl()
    {
	return $this->Viewer_Fetch(Plugin::GetTemplatePath('role') . 'menu.setting.users.tpl');
    }

    public function CommentAction($aVar)
    {	
	$this->Viewer_Assign('oComment', $aVar['comment']);
	$this->Viewer_Assign('oUserCurrent', $aVar['user_current']);
	return $this->Viewer_Fetch(Plugin::GetTemplatePath('ec') . 'comment_action.tpl');
    }


}

?>
