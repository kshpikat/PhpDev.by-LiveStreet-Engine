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
 * @File Name: ActionAdminToolsComments.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_AdminToolsComments extends AceAdminPlugin {

    public function Init() {
    }

    public function Event() {
        if (isPost('adm_submit')) {
            $this->Security_ValidateSendForm();
            $this->ClearComments();
        }
    }

    protected function ClearComments() {
        $this->PluginAceadminpanel_Tools_ClearComments();
    }
}

// EOF