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


if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginEc extends Plugin
{
    protected $aInherits = array(
	'entity' => array('ModuleComment_EntityComment'),
    );
    public $aDelegates = array(
	'template' => array(
	    'comment.tpl' => '_comment.tpl',
	    'comment_tree.tpl' => '_comment_tree.tpl',
	),
    );
    public function Activate()
    {
	if (!$this->isFieldExists('prefix_comment','comment_date_edit') and !$this->isFieldExists('prefix_comment','comment_edit_user_id')){
	    $this->ExportSQL(dirname(__FILE__) . '/dump.sql');
	}
	return true;
    }

    public function Init()
    {
	$this->Viewer_Assign('sTPEc', rtrim(Plugin::GetTemplatePath('ec'), '/'));
	$this->Viewer_Assign('sTWEc', rtrim(Plugin::GetTemplateWebPath('ec'), '/'));

	$this->Viewer_AppendScript(Plugin::GetTemplateWebPath('ec') . 'js/comments.js');
	$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath('ec') . 'css/style.css');
    }

}

?>
