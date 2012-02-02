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
 * @File Name: Tools.mapper.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_ModuleTools_MapperTools extends Mapper {
    public function ClearComments() {
        $sql = 
            "SELECT co.comment_id FROM ".Config::Get('db.table.comment_online')." AS co
                LEFT JOIN ".Config::Get('db.table.topic')." AS t ON co.target_type='topic' AND co.target_id=t.topic_id
                WHERE t.topic_id IS NULL";
        if (($aCommentId = $this->oDb->selectCol($sql))) {
            Engine::getInstance()->Comment_DeleteCommentOnlineByArrayId($aCommentId, 'topic');
        }
        return true;
    }
}

// EOF