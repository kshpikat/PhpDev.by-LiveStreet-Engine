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
 * @File Name: PluginAceadminpanel.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel extends Plugin
{
    private $sPlugin = 'aceadminpanel';

    static $sUpdateInfo;

    /**
     * Делегирование (т.е. полное переопределение)
     *
     * @var array
     */
    public $aDelegates = array(
        'module' => array(
            //'Admin' => 'PluginAceadminpanel_ModuleAdmin',
        ),
        'entity' => array(
        ),
        'template' => array(
            'statistics_performance.tpl',
        ),
    );

    /**
     * Наследование
     *
     * @var array
     */
    public $aInherits = array(
        'action' => array(
            'ActionAdmin',
            'ActionError',
            'ActionTopic',
            'ActionAjax',
        ),
        'module' => array(
            'ModulePlugin' => '_ModulePlugin',
            'ModuleViewer' => '_ModuleViewer',
            'ModuleAdmin' => '_ModuleAdmin',
            'ModuleTopic' => '_ModuleTopic',
            'ModuleLang' => '_ModuleLang',
            'ModuleVote' => '_ModuleVote',
            'ModuleNotify' => '_ModuleNotify',
        ),
        'mapper' => array(
            'ModuleTopic_MapperTopic' => '_ModuleTopic_MapperTopic',
        ),
    );

    public function GetXml()
    {
        preg_match('/^Plugin([\w]+)$/i', get_class($this), $aMatches);
        $sPluginXML = Config::Get('path.root.server') . '/plugins/' . strtolower($aMatches[1]) . '/' . ModulePlugin::PLUGIN_XML_FILE;
        if ($oXml = @simplexml_load_file($sPluginXML)) {
            return $oXml;
        }
        return null;
    }

    /**
     * Активация плагина
     *
     * @return bool
     */
    public function Activate()
    {
        $oXml = $this->GetXml();
        $sPhpNeed = (string)$oXml->requires->system->php;
        if (version_compare(PHP_VERSION, $sPhpNeed) < 0) {
            $this->Message_AddErrorSingle('You need PHP version ' . $sPhpNeed . ' or more', $this->Lang_Get('error'), true);
            $result = false;
        } else {
            // Создание таблиц в базе данных при их отсутствии.
            $result = true;
            $data = $this->ExportSQL(dirname(__FILE__) . '/sql.sql');
            if (!$data['result']) {
                foreach ($data['errors'] as $err) {
                    if ($err > '') $result = false;
                }
            }

            if ($result) {
                $this->ClearCache();
                $this->Session_Set($this->sPlugin . '_activate', 1);
            } else {
                $this->Message_AddErrorSingle('Cannot update database for this plugin', $this->Lang_Get('error'), true);
            }
        }
        return $result;
    }

    /**
     * Инициализация плагина
     *
     * @return void
     */
    public function Init()
    {
        HelperPlugin::InitPlugin($this);
        $sDataFile = $this->PluginAceadminpanel_Admin_GetCustomConfigFile();
        if (!file_exists($sDataFile)) {
            $aConfigSet = $this->PluginAceadminpanel_Admin_GetValueArrayByPrefix('config.all.');
            @file_put_contents($sDataFile, serialize($aConfigSet));
        }

        $this->LoadPluginsConfig();
    }

    /**
     * Деактивация плагина
     *
     * @return bool
     */
    public function Deactivate()
    {
        $this->ClearCache();
        return true;
    }

    protected function LoadPluginsConfig()
    {
        $aPlugins = $this->Plugin_GetActivePlugins();
        foreach ($aPlugins as $sPlugin) {
            $sFile = admFilePath(Config::Get('sys.cache.dir') . 'adm.' . $sPlugin . '.cfg');
            if (is_file($sFile)) {
                $sData = file_get_contents($sFile);
                if ($sData) {
                    $aConfig = unserialize($sData);
                    Config::Set('plugin.' . $sPlugin, $aConfig);
                }
            }
        }
    }

    protected function ClearCache()
    {
        if (!admClearDir(Config::Get('path.smarty.compiled'))) {
            $this->Message_AddErrorSingle(
                'Unable to remove content of dir <b>' . admFilePath(Config::Get('path.smarty.compiled'))
                . '</b>. It is recommended to do it manually',
                $this->Lang_Get('attention'), true);
        }
        if (!admClearDir(Config::Get('path.smarty.cache'))) {
            $this->Message_AddErrorSingle(
                'Unable to remove content of dir <b>' . admFilePath(Config::Get('path.smarty.cache'))
                . '</b>. It is recommended to do it manually',
                $this->Lang_Get('attention'), true);
        }
        $result = admClearAllCache();
        return $result;
    }

    public function GetUpdateInfo()
    {
        if (!self::$sUpdateInfo)
            self::$sUpdateInfo = $this->PluginAceadminpanel_Admin_CheckDbo($this->sPlugin);
        return self::$sUpdateInfo;
    }

    public function __call($sName, $aArgs = array())
    {
        return parent::__call($sName, $aArgs);
    }

}

if (!function_exists('admClearSmartyCache')) {
    include_once 'include/adm_function.php';
}

// EOF