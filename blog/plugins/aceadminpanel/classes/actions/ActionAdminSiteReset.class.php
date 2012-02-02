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
 * @File Name: ActionAdminSiteReset.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_AdminSiteReset extends AceAdminPlugin
{
    public function Event()
    {
        if (isPost('adm_reset_submit')) {
            $this->EventSiteResetSubmit();
            $this->Viewer_Assign('submit_cache_save', 1);
        }
    }

    protected function EventSiteResetSubmit()
    {
        if (isPost('adm_cache_clear_data')) $this->Cache_Clean();
        if (isPost('adm_cache_clear_headfiles')) admClearHeadfilesCache();
        if (isPost('adm_cache_clear_smarty')) admClearSmartyCache();
        if (isPost('adm_reset_config_data')) $this->ResetCustomConfig();
        $this->Message('notice', $this->Lang_Get('adm_action_ok'));
        admHeaderLocation(Router::GetPath('admin') . 'site/reset/');
    }

    protected function ResetCustomConfig()
    {
        $this->PluginAceadminpanel_Admin_DelValueArrayByPrefix('config.all.');
        $sFileName = $this->PluginAceadminpanel_Admin_GetCustomConfigFile();
        unlink($sFileName);
    }
}

// EOF