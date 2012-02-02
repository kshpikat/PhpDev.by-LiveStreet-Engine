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
 * @File Name: ActionAdminPluginsList.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_AdminPluginsList extends AceAdminPlugin
{

    public function Init()
    {
        $this->Viewer_Assign('tpl_include', HelperPlugin::GetTemplateActionPath('plugins_list.tpl'));
    }

    public function Event()
    {
        // * Обработка удаления плагинов
        if (isPost('submit_plugins_del')) {
            $this->EventPluginsDelete();
        } elseif (isPost('submit_plugins_save')) {
            $this->EventPluginsSave();
        } else {
            if ($sPlugin = getRequest('plugin', null, 'get') AND $sAction = getRequest('action', null, 'get')) {
                if ($sAction == 'deactivate') {
                    return $this->SubmitManagePlugin($sPlugin, $sAction);
                } else {
                    return $this->EventPluginsActivate($sPlugin);
                }
            }
        }
        $sMode = $this->GetParam(1);
        if (!$sMode) $sMode == 'all';
        $aPlugins = $this->PluginAceadminpanel_Plugin_GetPluginList();
        if ($sMode != 'all') {
            foreach ($aPlugins as $sPlugin => $oPlugin) {
                if (($sMode == 'active' AND !$oPlugin->IsActive())
                    OR ($sMode == 'inactive' AND $oPlugin->IsActive())
                ) {
                    unset($aPlugins[$sPlugin]);
                }
            }
        }
        $this->Viewer_Assign('aPluginList', $aPlugins);
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugins_administartion_title'));
        $this->Viewer_Assign('sMode', $sMode);
    }

    /**
     * Отработка активации/деактивации плагина
     *
     * @param   string  $sPlugin
     * @param   string  $sAction
     *
     * @return  void
     */
    protected function SubmitManagePlugin($sPlugin, $sAction)
    {
        if (in_array($sAction, array('activate', 'deactivate'))) {
            // * Активируем\деактивируем плагин
            if ($sAction == 'deactivate' AND ($bResult = $this->EventPluginsDeactivate($sPlugin))) {
                $this->Message_AddNotice($this->Lang_Get('plugins_action_ok'), $this->Lang_Get('attention'), true);
            } else {
                if (!($aMessages = $this->Message_GetErrorSession()) OR !sizeof($aMessages))
                    $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'), true);
            }
        } else {
            $this->Message_AddError($this->Lang_Get('plugins_unknown_action'), $this->Lang_Get('error'), true);
        }
        // * Возвращаемся на страницу управления плагинами
        if ($this->sPageRef)
            Router::Location($this->sPageRef);
        else
            Router::Location(Router::GetPath('admin') . 'site/plugins/');
    }

    protected function EventPluginsDelete()
    {
        $this->Security_ValidateSendForm();

        $aPluginsDelete = getRequest('plugin_del');
        if (is_array($aPluginsDelete)) {
            $this->Plugin_Delete(array_keys($aPluginsDelete));
        }
    }

    protected function EventPluginsSave()
    {
        $aPlugins = array();
        foreach ($_POST as $key => $val) {
            if (preg_match('/(\w+)_priority/', $key, $matches)) {
                $sPluginCode = $matches[1];
                $aPlugins[$sPluginCode] = array('code' => $sPluginCode, 'priority' => intVal($val), 'is_active' => intVal(@$_POST[$sPluginCode . '_active']));
            }
        }
        if ($aPlugins) $this->PluginAceadminpanel_Plugin_SetPluginsData($aPlugins);
    }

    protected function EventPluginsActivate($sPlugin)
    {
        if (($bResult = $this->PluginAceadminpanel_Plugin_Activate($sPlugin))) {
            $this->Message_AddNotice($this->Lang_Get('plugins_action_ok'), $this->Lang_Get('attention'), true);
        } else {
            if (!($aMessages = $this->Message_GetErrorSession()) OR !sizeof($aMessages)) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error') . ' (unknown error)', $this->Lang_Get('error'), true);
            }
        }
        // * Возвращаемся на страницу управления плагинами
        if ($this->sPageRef)
            Router::Location($this->sPageRef);
        else
            Router::Location(Router::GetPath('admin') . 'site/plugins/');
    }

    protected function EventPluginsDeactivate($sPlugin)
    {
        return $this->PluginAceadminpanel_Plugin_Deactivate($sPlugin);
    }

}

// EOF