<?php
/*---------------------------------------------------------------------------
 * @Plugin Name: aceAdminPanel
 * @Plugin Id: aceadminpanel
 * @Plugin URI: 
 * @Description: Advanced Administrator's Panel for LiveStreet/ACE
 * @Version: 1.5.271
 * @Author: Vadim Shemarov (aka aVadim)
 * @Author URI: 
 * @LiveStreet Version: 0.5
 * @File Name: Comment.mapper.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_ModuleComment_MapperComment extends PluginAceadminpanel_Inherit_ModuleComment_MapperComment
{

    public function ClearStreamByComment($aCommentsId)
    {
        if (!is_array($aCommentsId)) $aCommentsId = array($aCommentsId);
        $sql = "
            DELETE FROM " . Config::Get('db.table.stream_event') . "
            WHERE event_type LIKE '%_comment' AND target_id IN (?a)
        ";
    }

}

// EOF