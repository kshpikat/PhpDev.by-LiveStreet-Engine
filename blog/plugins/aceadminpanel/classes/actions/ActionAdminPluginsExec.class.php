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
 * @File Name: ActionAdminPluginsExec.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_AdminPluginsExec extends AceAdminPlugin
{

    public function Init()
    {
    }

    public function Event($sClass)
    {
        if (preg_match('/^Plugin([a-zA-Z0-9]+)_(\w+)/', $sClass, $aMatches)) {
            $sPluginCode = strtolower($aMatches[1]);
            $oPlugin = $this->PluginAceadminpanel_Plugin_GetPlugin($sPluginCode);

            if ($oPlugin) {
                $oAction = new $sClass($this->oEngine, 'admin');
                $oAction->SetAdminAction($this->oAdminAction);
                $oAction->SetPluginAddon($sPluginCode);

                $this->Viewer_Assign('oPlugin', $oPlugin);
                if ($oAction->Init() !== false) {
                    $xResult = $oAction->Admin();
                    $oAction->Done();

                    if (!is_null($oAction->sMenuItemSelect))
                        $this->oAdminAction->SetMenuItemSelect($oAction->sMenuItemSelect);
                    if (!is_null($oAction->sMenuSubItemSelect))
                        $this->oAdminAction->SetMenuSubItemSelect($oAction->sMenuSubItemSelect);
                    if (!is_null($oAction->sMenuNavItemSelect))
                        $this->oAdminAction->SetMenuNavItemSelect($oAction->sMenuNavItemSelect);

                    $aBlocks = $oAction->GetBlocks();
                    foreach ($aBlocks as $aBlockTemplate) {
                        $this->oAdminAction->PluginAddBlock('right', 'block.admin_empty_top.tpl', array('plugin'=>'aceadminpanel'), false);
                        $this->oAdminAction->PluginAddBlockTemplate('right', $aBlockTemplate['block'], $aBlockTemplate['params'], false);
                        $this->oAdminAction->PluginAddBlock('right', 'block.admin_empty_bottom.tpl', array('plugin'=>'aceadminpanel'), false);
                    }
                    return $xResult;
                }
                $this->Message('error', 'ADM_ERR_INIT_ACTION');
                return false;
            }
            $this->Message('error', 'ADM_ERR_NOT_ACTIVE_PLUGIN');
        } else {
            $this->Message('error', 'ADM_ERR_WRONG_PLUGIN_CLASS');
        }
        return false;
    }

}

// EOF