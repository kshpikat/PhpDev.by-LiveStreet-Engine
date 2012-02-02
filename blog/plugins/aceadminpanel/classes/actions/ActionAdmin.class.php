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
 * @File Name: ActionAdmin.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

require_once 'AceAdminPlugin.class.php';

class PluginAceadminpanel_ActionAdmin extends PluginAceadminpanel_Inherit_ActionAdmin
{
    private $sPlugin = 'aceadminpanel';

    protected $sMenuHeadItemSelect; // Главное меню
    protected $sMenuItemSelect; // Активное меню
    protected $sMenuSubItemSelect; // Активное подменю
    protected $sMenuNavItemSelect; // Навигационное меню

    protected $sUserLogin = null;
    protected $sConfigFileName = 'config/config.php';

    protected $aConfig = array();

    protected $sParamPathThemes;
    protected $sParamPathLanguages;

    protected $oLogs = null;
    protected $aLogsMsg = array();

    protected $aBlocks = array();

    protected $aPluginInfo;

    protected $sPageRef = '';
    protected $aAddons = array();
    protected $bAddonsAutoCheck = true;
    protected $sRequestPath = '';

    protected $aExternalEvents = array();

    public function Init()
    {
        if (($result = parent::Init())) {
            return $result;
        }

        $this->SetDefaultEvent('info');
        $this->InitParams();
        $this->aConfig = array_merge($this->aConfig, HelperPlugin::GetConfig());

        if ($this->User_IsAuthorization()) {
            $this->oUserCurrent = $this->PluginAceadminpanel_Admin_GetUserCurrent();
        }
        if (!$this->oUserCurrent OR !$this->oUserCurrent->isAdministrator()) {
            return $this->EventDenied();
        }

        $this->oUserCurrent = $this->User_GetUserCurrent();

        $this->Viewer_Assign('ROUTE_PAGE_ADMIN', ROUTE_PAGE_ADMIN);
        $this->Viewer_Assign('sModuleVersion', $this->PluginAceadminpanel_Admin_getVersion(true));

        if (Config::Get('plugin.avalogs.admin_file') AND Config::Get('plugin.avalogs.admin_enable')) {
            if (!$this->oLogs) $this->oLogs = $this->Adminlogs_GetLogs();
            $this->oLogs->SetLogOptions('admin', array('file' => Config::Get('plugin.avalogs.admin_file')));
            $this->aLogsMsg[] = 'user=>' . $this->oUserCurrent->GetLogin() . ', ip=>' . $_SERVER["REMOTE_ADDR"]
                                . ', action=>' . Router::GetAction() . ', event=>' . Router::GetActionEvent()
                                . ', path=>' . Router::GetPathWebCurrent();
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->sPageRef = $_SERVER['HTTP_REFERER'];
        }

        $this->PluginSetTemplate(Router::GetActionEvent());
        $this->sMenuItemSelect = Router::GetActionEvent();
        $this->sMenuSubItemSelect = Router::GetParam(0);
        $this->aPluginInfo = array('version' => HelperPlugin::GetConfig('version'));

        //$this->PluginAppendStyle('admin.css');
        //$this->PluginAppendScript('admin.js');

        $this->_addBlock('right', 'admin_info');
        $sHtmlTitle = $this->Lang_Get('adm_title') . ' v.' . $this->PluginAceadminpanel_Admin_getVersion();
        $this->Viewer_AddHtmlTitle($sHtmlTitle);
        $this->Viewer_Assign('sAdminTitle', 'aceAdminPanel v.' . $this->PluginAceadminpanel_Admin_getVersion());
    }

    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEvent('info', 'EventInfo');
        $this->AddEvent('site', 'EventSite');
        $this->AddEvent('plugins', 'EventPlugins');
        $this->AddEvent('pages', 'EventPages');
        $this->AddEvent('blogs', 'EventBlogs');
        $this->AddEvent('topics', 'EventTopics');
        $this->AddEvent('users', 'EventUsers');
        $this->AddEvent('tools', 'EventTools');
        $this->AddEvent('userfields', 'EventUserfields');
        $this->CheckAdminEvents();
    }

    protected function CheckAdminEvents()
    {
        $aPlugins = $this->PluginAceadminpanel_Plugin_GetPluginList();
        foreach ($aPlugins as $oPlugin) {
            $aEvents = $oPlugin->GetAdminMenuEvents();
            if ($aEvents)
                foreach ($aEvents as $sEvent => $sClass) {
                    $this->aExternalEvents[$sEvent] = $sClass;
                    $this->AddEvent($sEvent, 'EventPluginsMenu');
                }
        }
    }

    protected function InitParams()
    {
        $this->aConfig = array(
            'reserverd_urls' => array('admin'),
            'votes_per_page' => 15,
            'items_per_page' => 15,
            'vote_value' => 10,
            'edit_footer_text' => '<div style="border-top:1px solid #CCC;color:#F99;text-align:right;font-size:0.9em;">Edited by admin at [@date]</div>',
            'path_themes' => Config::Get('path.root.server') . '/templates/skin',
            'path_languages' => Config::Get('path.root.server') . '/templates/language',
            'check_password' => 1,
        );
        $sReserverdUrls = $this->PluginAceadminpanel_Admin_GetValue('param_reserved_urls');
        if ($sReserverdUrls)
            $this->aConfig['reserverd_urls'] = array_unique(array_merge($this->aConfig['reserverd_urls'], explode(',', $sReserverdUrls)));

        $this->aConfig['items_per_page'] = $this->PluginAceadminpanel_Admin_GetValue('param_items_per_page', $this->aConfig['items_per_page']);
        $this->aConfig['votes_per_page'] = $this->PluginAceadminpanel_Admin_GetValue('param_votes_per_page', $this->aConfig['votes_per_page']);
        $this->aConfig['edit_footer_text'] = $this->PluginAceadminpanel_Admin_GetValue('param_edit_footer', $this->aConfig['edit_footer_text']);
        $this->aConfig['vote_value'] = $this->PluginAceadminpanel_Admin_GetValue('param_vote_value', $this->aConfig['vote_value']);

        //$this->bParamSiteClosed=defined('adm_SITE_CLOSED')?ADMIN_SITE_CLOSED:false;
        //$this->sParamSiteClosedPage=$this->Admin_GetValue('param_site_closed_page', $this->sParamSiteClosedPage);
        //$this->sParamSiteClosedText=$this->Admin_GetValue('param_site_closed_text', $this->sParamSiteClosedText);
        //$this->sParamSiteClosedFile=$this->Admin_GetValue('param_site_closed_file', $this->sParamSiteClosedFile);

        //$this->sParamPathThemes=Config::Get('path.root.server').'/templates/skin';
        //$this->sParamPathLanguages=Config::Get('path.root.server').'/templates/language';

        $this->aConfig['check_password'] = $this->PluginAceadminpanel_Admin_GetValue('param_check_password', $this->aConfig['check_password']);

        $oLang = $this->Lang_Dictionary();
        $this->Viewer_Assign('oLang', $oLang);
        $this->aAddons = array(
            'sitesettings' => array(
                'path' => 'admin/site/settings',
                'class' => 'PluginAceadminpanel_AdminSiteSettings',
                'file' => Config::Get('root.path.server') . 'plugins/aceadminpanel/classes/actions/ActionAdminSiteSettings.class.php',
                //'template'=>HelperPlugin::GetTemplatePath('admin_site_settings.tpl'),
                'language' => HelperPlugin::GetPluginPath() . '/templates/language/%%language%%.site_settings.php',
            ),
            'sitereset' => array(
                'path' => 'admin/site/reset',
                'class' => 'PluginAceadminpanel_AdminSiteReset',
                'file' => Config::Get('root.path.server') . 'plugins/aceadminpanel/classes/actions/ActionAdminSiteReset.class.php',
                'template' => HelperPlugin::GetTemplatePath('actions/ActionAdmin/site_reset.tpl'),
            ),
            'toolscomments' => array(
                'path' => 'admin/tools/comments',
                'class' => 'PluginAceadminpanel_AdminToolsComments',
                'file' => Config::Get('root.path.server') . 'plugins/aceadminpanel/classes/actions/ActionAdminToolsComments.class.php',
                'template' => HelperPlugin::GetTemplatePath('admin_tools_comments.tpl'),
            ),
        );
    }

    protected function MakeMenu()
    {
        $this->Viewer_AddMenu('aceadmin', $this->GetTemplateFile('/menu.admin.tpl'));
        $this->Viewer_Assign('menu', 'aceadmin');
    }

    public function SetMenuItemSelect($sItem)
    {
        $this->sMenuItemSelect = $sItem;
    }

    public function SetMenuSubItemSelect($sItem)
    {
        $this->sMenuSubItemSelect = $sItem;
    }

    public function SetMenuNavItemSelect($sItem)
    {
        $this->sMenuNavItemSelect = $sItem;
    }

    /*************************************************************************/
    protected function GetPluginName()
    {
        return $this->sPlugin;
    }

    protected function PluginConfigGet($sParam)
    {
        return Config::Get('plugin.' . $this->sPlugin . '.' . $sParam);
    }

    protected function GetTemplateFile($sFile)
    {
        return HelperPlugin::GetTemplatePath($sFile);
    }

    protected function _AddBlock($sGroup, $sBlock, $aParams = array(), $bSingle = true)
    {
        if (!$aParams OR !isset($aParams['plugin'])) $aParams['plugin'] = $this->sPlugin;
        if (!$aParams OR !isset($aParams['priority'])) $aParams['priority'] = 'top';
        return $this->PluginAddBlock($sGroup, $sBlock, $aParams, $bSingle);
    }

    public function PluginAddBlockTemplate($sGroup, $sBlockTemplate, $aParams = array(), $bSingle = true)
    {
        if (!isset($aParams['plugin'])) $aParams['plugin'] = null;
        if ($bSingle AND isset($this->aBlocks[$sGroup]))
            foreach ($this->aBlocks[$sGroup] as $aBlock) {
                if ($aBlock['block'] == $sBlockTemplate AND $aBlock['params']['plugin'] == $aParams['plugin']) {
                    // уже есть
                    return;
                }
            }
        $this->aBlocks[$sGroup][] = array('block' => $sBlockTemplate, 'params' => $aParams);
    }

    public function PluginAddBlock($sGroup, $sBlock, $aParams = array(), $bSingle = true)
    {
        //$sBlockTemplate = $this->GetTemplateFile('/block.'.$sBlockName.'.tpl');
        //$this->PluginAddBlockTemplate($sGroup, $sBlockTemplate, $bSingle);
        $this->PluginAddBlockTemplate($sGroup, $sBlock, $aParams, $bSingle);
    }

    protected function PluginDelBlock($sGroup, $sBlockName)
    {
        //$sTemplate = $this->GetTemplateFile('/block.'.$sBlockName.'.tpl');
        $sTemplate = $sBlockName;
        if (isset($this->aBlocks[$sGroup]))
            foreach ($this->aBlocks[$sGroup] as $nBlock => $sBlock) {
                if ($sBlock['block'] == $sTemplate) {
                    unset($this->aBlocks[$sGroup][$nBlock]);
                    return;
                }
            }
    }

    protected function PluginSetTemplate($sTemplate)
    {
        $this->SetTemplate($this->GetTemplateFile('/actions/ActionAdmin/' . $sTemplate . '.tpl'));
    }

    protected function PluginAppendScript($sScript, $aParams = array())
    {
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath($this->sPlugin) . 'js/' . $sScript);
    }

    protected function PluginAppendStyle($sStyle, $aParams = array())
    {
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath($this->sPlugin) . 'css/' . $sStyle);
    }

    /*************************************************************************/
    protected function ParseText($sText, $aData = Array())
    {
        if (!isset($aData['date'])) $aData['date'] = time();
        if (!isset($aData['user'])) {
            if ($this->oUserCurrent) {
                $aData['user'] = $this->oUserCurrent->getLogin();
            } else {
                $aData['user'] = '';
            }
        }
        return ($this->PluginAceadminpanel_Lang_ParseText($sText, $aData));
    }

    public function Message($type, $msg, $cmd = null, $bUseSession = false)
    {
        if (Config::Get('plugin.avalogs.admin_enable') AND $this->oLogs) {
            $this->aLogsMsg[] = ' * type=>' . $type . ', cmd=>' . $cmd . ', msg=>' . $msg;
        }
        if ($type == 'error') {
            $this->Message_AddError($msg, null, $bUseSession);
        } else {
            $this->Message_AddNotice($msg, null, $bUseSession);
        }
        return $msg;
    }

    protected function MessageError($msg, $cmd = null, $bUseSession = false)
    {
        return $this->Message('error', $msg, $cmd, $bUseSession);
    }

    protected function MessageNotice($msg, $cmd = null, $bUseSession = false)
    {
        return $this->Message('notice', $msg, $cmd, $bUseSession);
    }

    protected function GetEditFooter()
    {
        if ($this->aConfig['edit_footer_text']) {
            return "\n" . $this->ParseText($this->aConfig['edit_footer_text']);
        } else {
            return '';
        }
    }

    protected function CheckRefererUrl()
    {
        $bChecked = true;
        if ($this->PluginConfigGet('check_url')) {
            if (!isset($_SERVER["HTTP_REFERER"])) {
                $bChecked = false;
            } else {
                $sUrl = Config::Get('path.root.web') . '/admin/';
                if (strpos($_SERVER["HTTP_REFERER"], $sUrl) === false) {
                    $bChecked = false;
                }
            }
        }
        return $bChecked;
    }

    /**
     * Получение параметров с проверкой URL источника перехода
     *
     * @param   int     $nOffset
     * @param   mixed   $xDefault
     * @return  string
     */
    public function GetParam($nOffset, $xDefault = null)
    {
        if (!$this->CheckRefererUrl()) {
            return null;
        }
        else {
            return parent::GetParam($nOffset, $xDefault);
        }
    }

    protected function GetLastParam($default = null)
    {
        $nNumParams = sizeof(Router::GetParams());
        if ($nNumParams > 0) {
            $iOffset = $nNumParams - 1;
            return $this->GetParam($iOffset, $default);
        }
        return null;
    }

    /**
     * Получение REQUEST-переменной с проверкой "ключа секретности"
     *
     * @param   string  $sName
     * @param   string  $default
     * @param   string  $sType
     * @return  string
     */
    protected function GetRequestCheck($sName, $default = null, $sType = null)
    {
        $result = getRequest($sName, $default, $sType);

        if (!is_null($result)) $this->Security_ValidateSendForm();

        return $result;
    }

    /**
     * Вернуться на предыдущую страницу
     */
    protected function GoToBackPage()
    {
        if ($this->sPageRef)
            admHeaderLocation($this->sPageRef);
        else
            admHeaderLocation(Router::GetPath('admin'));
    }

    /* ==================================================================================== *
     * Events
     */

    /**
     * Запрет доступа
     *
     * @return string
     */
    protected function EventDenied()
    {
        $this->Message_AddErrorSingle($this->Lang_Get('adm_denied_text'), $this->Lang_Get('adm_denied_title'));
        return Router::Action('error');
    }

    protected function EventInfo()
    {
        $this->sMenuItemSelect = Router::GetActionEvent();
        if ($sReportMode = getRequest('report', null, 'post')) {
            $this->EventInfoReport($this->_getInfoData(), $sReportMode);
        }

        if ($this->GetParam(0) == 'phpinfo') {
            $this->EventInfoPhpInfo(1);
        } elseif ($this->GetParam(0) == 'params') {
            $this->EventInfoParams();
        } else {
            $this->sMenuSubItemSelect = 'about';
            $this->PluginSetTemplate('info_about');
            //$this->SetTemplate(HelperPlugin::GetTemplateActionPath('info_about.tpl'));
        }

        $this->_AddBlock('right', 'admin_info');

        $this->Viewer_Assign('aCommonInfo', $this->_getInfoData());
    }

    protected function _getInfoData()
    {
        $aPlugins = $this->Plugin_GetList();
        $aActivePlugins = $this->Plugin_GetActivePlugins();
        $aPluginList = array();
        foreach ($aActivePlugins as $sPlugin) {
            $aPliginProps = $aPlugins[$sPlugin];
            $sPluginName = htmlspecialchars((string)$aPliginProps['property']->name->data);
            $aPluginInfo = array(
                'item' => $sPlugin,
                'label' => $sPluginName,
            );
            if ($aPliginProps['property']->version) {
                $aPluginInfo['value'] = 'v.' . htmlspecialchars((string)$aPliginProps['property']->version);
            }
            $sPluginClass = 'Plugin' . ucfirst($sPlugin);
            if (class_exists($sPluginClass) AND method_exists($sPluginClass, 'GetUpdateInfo')) {
                $oPlugin = new $sPluginClass;
                $aPluginInfo['.html'] = ' - ' . $oPlugin->GetUpdateInfo();
            }
            $aPluginList[$sPlugin] = $aPluginInfo;
        }

        $aSiteStat = $this->PluginAceadminpanel_Admin_GetSiteStat();
        $sSmartyVersion = $this->Viewer_GetSmartyVersion();

        $aInfo = array(
            'versions' => array(
                'label' => $this->Lang_Get('adm_info_versions'),
                'data' => array(
                    'php' => array('label' => $this->Lang_Get('adm_info_version_php'), 'value' => PHP_VERSION,),
                    'smarty' => array('label' => $this->Lang_Get('adm_info_version_smarty'), 'value' => $sSmartyVersion?$sSmartyVersion:'n/a',),
                    'ls' => array('label' => $this->Lang_Get('adm_info_version_ls'), 'value' => LS_VERSION,),
                    'adminpanel' => array('label' => $this->Lang_Get('adm_info_version_adminpanel'), 'value' => $this->aPluginInfo['version'],),
                )

            ),
            'site' => array(
                'label' => $this->Lang_Get('adm_site_info'),
                'data' => array(
                    'url' => array('label' => $this->Lang_Get('adm_info_site_url'), 'value' => Config::Get('path.root.web'),),
                    'skin' => array('label' => $this->Lang_Get('adm_info_site_skin'), 'value' => Config::Get('aceadminpanel.saved.view.skin'),),
                    'jslib' => array('label' => $this->Lang_Get('adm_info_site_jslib'), 'value' => Config::Get('js.lib'),),
                ),
            ),
            'plugins' => array(
                'label' => $this->Lang_Get('adm_active_plugins'),
                'data' => $aPluginList,
            ),
            'stats' => array(
                'label' => $this->Lang_Get('adm_site_statistics'),
                'data' => array(
                    'users' => array('label' => $this->Lang_Get('adm_site_stat_users'), 'value' => $aSiteStat['users'],),
                    'blogs' => array('label' => $this->Lang_Get('adm_site_stat_blogs'), 'value' => $aSiteStat['blogs'],),
                    'topics' => array('label' => $this->Lang_Get('adm_site_stat_topics'), 'value' => $aSiteStat['topics'],),
                    'comments' => array('label' => $this->Lang_Get('adm_site_stat_comments'), 'value' => $aSiteStat['comments'],),
                ),
            ),
        );

        return $aInfo;
    }

    protected function EventInfoPhpInfo($nMode = 0)
    {
        if ($nMode) {
            ob_start();
            phpinfo(-1);

            $phpinfo = preg_replace(
                array('#^.*<body>(.*)</body>.*$#ms', '#<h2>PHP License</h2>.*$#ms',
                     '#<h1>Configuration</h1>#', "#\r?\n#", "#</(h1|h2|h3|tr)>#", '# +<#',
                     "#[ \t]+#", '#&nbsp;#', '#  +#', '# class=".*?"#', '%&#039;%',
                     '#<tr>(?:.*?)" src="(?:.*?)=(.*?)" alt="PHP Logo" /></a>'
                     . '<h1>PHP Version (.*?)</h1>(?:\n+?)</td></tr>#',
                     '#<h1><a href="(?:.*?)\?=(.*?)">PHP Credits</a></h1>#',
                     '#<tr>(?:.*?)" src="(?:.*?)=(.*?)"(?:.*?)Zend Engine (.*?),(?:.*?)</tr>#',
                     "# +#", '#<tr>#', '#</tr>#'),
                array('$1', '', '', '', '</$1>' . "\n", '<', ' ', ' ', ' ', '', ' ',
                     '<h2>PHP Configuration</h2>' . "\n" . '<tr><td>PHP Version</td><td>$2</td></tr>' .
                     "\n" . '<tr><td>PHP Egg</td><td>$1</td></tr>',
                     '<tr><td>PHP Credits Egg</td><td>$1</td></tr>',
                     '<tr><td>Zend Engine</td><td>$2</td></tr>' . "\n" .
                     '<tr><td>Zend Egg</td><td>$1</td></tr>', ' ', '%S%', '%E%'),
                ob_get_clean());
            $sections = explode('<h2>', strip_tags($phpinfo, '<h2><th><td>'));
            unset($sections[0]);

            $aPhpInfo = array();
            foreach ($sections as $ns => $section) {
                $n = substr($section, 0, strpos($section, '</h2>'));
                preg_match_all(
                    '#%S%(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?(?:<td>(.*?)</td>)?%E%#',
                    $section, $askapache, PREG_SET_ORDER);
                foreach ($askapache as $k => $m) {
                    if (!isset($m[2])) $m[2] = '';
                    $aPhpInfo[$n][$m[1]] = (!isset($m[3])OR$m[2] == $m[3]) ? $m[2] : array_slice($m, 2);
                }
            }
            $this->Viewer_Assign('aPhpInfo', array('collection' => $aPhpInfo, 'count' => sizeof($aPhpInfo)));
        } else {
            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();
            $phpinfo = str_replace("\n", ' ', $phpinfo);
            $info = '';
            if (preg_match('|<style\s*[\w="/]*>(.*)<\/style>|imu', $phpinfo, $match)) $info .= $match[0];
            if (preg_match('|<body\s*[\w="/]*>(.*)<\/body>|imu', $phpinfo, $match)) $info .= $match[1];
            if (!$info) $info = $phpinfo;
            $this->Viewer_Assign('sPhpInfo', $info);
        }
        $this->PluginSetTemplate('info_phpinfo');
        $this->PluginAppendScript('phpinfo.js');
    }

    protected function EventInfoParams()
    {
        $this->sMenuSubItemSelect = 'params';

        if ($this->getRequestCheck('submit_options_save')) {
            if ($this->EventInfoParamsSubmit()) {
                $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'params');
            } else {
                $this->MessageError($this->Lang_Get('adm_saved_err'), 'params');
            }
        }

        $this->Viewer_Assign('sParamPageUrlReserved', implode(',', $this->aConfig['reserverd_urls']));
        $this->Viewer_Assign('sParamItemsPerPage', $this->aConfig['items_per_page']);
        $this->Viewer_Assign('sParamVotesPerPage', $this->aConfig['votes_per_page']);
        $this->Viewer_Assign('sParamEditFooter', htmlspecialchars($this->aConfig['edit_footer_text']));

        $this->Viewer_Assign('nParamVoteValue', $this->aConfig['vote_value']);

        $this->Viewer_Assign('bParamCheckPassword', $this->aConfig['check_password']);
        $this->PluginSetTemplate('info_params');
    }

    protected function EventInfoParamsSubmit()
    {
        $bOk = true;
        if (isset($_POST['param_reserved_urls'])) {
            $aReservedUrls = explode(',', preg_replace("/\s+/", '', getRequest('param_reserved_urls')));
            $aNewReservedUrls = Array();
            foreach ($aReservedUrls as $sUrl) {
                if (func_check($sUrl, 'login', 1, 50)) $aNewReservedUrls[] = $sUrl;
            }
            $this->aConfig['reserverd_urls'] = $aNewReservedUrls;
            $sReservedUrls = implode(',', $aNewReservedUrls);
            $result = $this->PluginAceadminpanel_Admin_SetValue('param_reserved_urls', $sReservedUrls);
            $bOk = $bOk AND $result['result'];
        }
        if (isset($_POST['param_items_per_page'])) {
            $result = $this->PluginAceadminpanel_Admin_SetValue('param_items_per_page', intval(getRequest('param_items_per_page')));
            $bOk = $bOk AND $result['result'];
        }
        if (isset($_POST['param_votes_per_page'])) {
            $result = $this->PluginAceadminpanel_Admin_SetValue('param_votes_per_page', intval(getRequest('param_votes_per_page')));
            $bOk = $bOk AND $result['result'];
        }
        if (isset($_POST['param_edit_footer'])) {
            $result = $this->PluginAceadminpanel_Admin_SetValue('param_edit_footer', getRequest('param_edit_footer'));
            $bOk = $bOk AND $result['result'];
        }
        if (isset($_POST['param_vote_value'])) {
            $result = $this->PluginAceadminpanel_Admin_SetValue('param_vote_value', intval(getRequest('param_vote_value')));
            $bOk = $bOk AND $result['result'];
        }
        if (isset($_POST['param_check_password'])) {
            $param = intval(getRequest('param_check_password'));
        } else {
            $param = 0;
        }
        $result = $this->PluginAceadminpanel_Admin_SetValue('param_check_password', $param);
        $bOk = $bOk AND $result['result'];
        if ($bOk) $this->InitParams();

        return $bOk;
    }

    /* ==================================================================================== *
     * URL: admin/site
     */
    protected function EventSite()
    {
        $this->sMenuHeadItemSelect = 'site';

        if ($this->GetParam(0) == 'params') {
            $this->EventSitePlugins();
        } elseif ($this->GetParam(0) == 'reset') {
            $this->sMenuSubItemSelect = 'reset';
            $this->EventSiteReset();
        } elseif ($this->GetParam(0) == 'settings') {
            $this->sMenuSubItemSelect = 'settings';
            $this->EventSiteSettings();
        } elseif ($this->GetParam(0) == 'config') {
            $this->sMenuSubItemSelect = 'config';
            $this->EventSiteConfig();
        } else {
            $this->sMenuSubItemSelect = 'settings';
            $this->EventSiteSettings();
        }

        $this->_AddBlock('right', 'admin_info');

        $this->PluginSetTemplate('site');
    }

    /*
    * URL: admin/site
    * ==================================================================================== */

    /* ==================================================================================== *
     * URL: admin/plugins
     */
    protected function EventPlugins()
    {
        $this->PluginSetTemplate('plugins');
        if ($this->GetParam(0) == 'config') {
            $this->sMenuSubItemSelect = 'config';
            $this->PluginDelBlock('right', 'admin_info');
            return $this->EventPluginsConfig();
        } else {
            $this->sMenuSubItemSelect = 'list';
            return $this->EventPluginsList();
        }
    }

    protected function EventPluginsConfig()
    {
        $this->PluginSetTemplate('plugins');
        $this->PluginDelBlock('right', 'admin_info');
        $sPluginCode = $this->getParam(1);
        $oPlugin = $this->PluginAceadminpanel_Plugin_GetPlugin($sPluginCode);
        if ($oPlugin) {
            $sClass = $oPlugin->GetAdminClass();
            return $this->EventPluginsExec($sClass);
        } else {
            return false;
        }
    }

    protected function EventPluginsMenu()
    {
        $this->PluginSetTemplate('plugins');
        $this->PluginDelBlock('right', 'admin_info');
        $sEvent = Router::GetActionEvent();
        if (isset($this->aExternalEvents[$sEvent])) {
            return $this->EventPluginsExec($this->aExternalEvents[$sEvent]);
        }
    }

    /*
     * URL: admin/plugins
     * ==================================================================================== */

    /* ==================================================================================== *
     * URL: admin/tools
     */
    protected function EventTools()
    {
        $this->sMenuHeadItemSelect = 'tools';

        if ($this->GetParam(0) == 'params') {
            $this->EventSitePlugins();
        } elseif ($this->GetParam(0) == 'reset') {
            $this->sMenuSubItemSelect = 'reset';
            $this->EventSiteReset();
        } elseif ($this->GetParam(0) == 'settings') {
            $this->sMenuSubItemSelect = 'settings';
            $this->EventSiteSettings();
        } else {
            $this->sMenuSubItemSelect = 'comments';
            $this->EventToolsComments();
        }

        $this->_AddBlock('right', 'admin_info');

        $this->PluginSetTemplate('admincontent');
        $aPlugins = $this->Plugin_GetList();
    }

    /*
     * URL: admin/tools
     * ==================================================================================== */

    /* ==================================================================================== *
     * URL: admin/pages
     */
    protected function EventPages()
    {
        if (!$this->PluginAceadminpanel_Plugin_PluginActivated('Page')) {
            return parent::EventNotFound();
        }

        $this->sMenuSubItemSelect = 'list';

        if (($sAdminAction = $this->getRequestCheck('action'))) {
            $this->EventPagesAction($sAdminAction);
        }
        // * Обработка создания новой страницы
        if ($this->getRequestCheck('submit_page_save')) {
            if (!getRequest('page_id')) {
                $this->EventPagesAddSubmit();
            }
        }

        if ($this->GetParam(0) == 'new') { // создание новой страницы
            $this->sMenuSubItemSelect = 'new';
            $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/pages_new.tpl');
        }
        elseif ($this->GetParam(0) == 'edit') { // вывод формы для редактирования
            $this->EventPagesEdit();
            $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/pages_new.tpl');
        }
        elseif ($this->GetParam(0) == 'delete') { // отработка команды удаления
            $this->EventPagesDelSubmit();
            admHeaderLocation(Router::GetPath('admin') . 'pages/');
            return;
        }
        elseif ($this->GetParam(0) == 'sort') { // отработка команды сортировки
            $this->EventPagesSort();
            admHeaderLocation(Router::GetPath('admin') . 'pages/');
            return;
        }
        elseif ($this->GetParam(0) == 'options') { // вывод опций
            $this->sMenuSubItemSelect = 'options';
            $this->EventPagesOptions();
            $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/pages_options.tpl');
        }

        // * Получаем и загружаем список всех страниц
        $aPages = $this->PluginPage_Page_GetPages();
        if (sizeof($aPages) == 0 AND $this->PluginPage_Page_GetCountPage()) {
            $this->PluginPage_Page_SetPagesPidToNull();
            $aPages = $this->PluginPage_Page_GetPages();
        }
        $this->Viewer_Assign('aPages', $aPages);
    }

    protected function EventPagesAction($sAdminAction = null)
    {
        if ($sAdminAction) {
            $oPage = $this->PluginPage_Page_GetPageById($this->getRequestCheck('page_id'));

            if ($oPage) {
                if (($sAdminAction == 'activate') OR ($sAdminAction == 'deactivate')) {
                    $oPage->setActive(($sAdminAction == 'activate') ? 1 : 0);
                    if ($this->PluginPage_Page_UpdatePage($oPage)) {
                        $this->Message_AddNotice($this->Lang_Get('adm_action_ok'), $this->Lang_Get('attention'), true);
                    } else {
                        $this->Message_AddError($this->Lang_Get('adm_action_err'), $this->Lang_Get('error'), true);
                    }
                }
            }
        }
        Router::Location(Router::GetPath('admin') . 'pages/');
    }

    protected function EventPagesSort()
    {
        $this->Security_ValidateSendForm();
        $oPage = $this->PluginPage_Page_GetPageById($this->GetParam(1));
        if ($oPage) {
            $sDirection = $this->GetParam(2) == 'down' ? 'down' : 'up';
            $iSortOld = $oPage->getSort();
            if (($oPagePrev = $this->PluginPage_Page_GetNextPageBySort($iSortOld, $oPage->getPid(), $sDirection))) {
                $iSortNew = $oPagePrev->getSort();
                $oPagePrev->setSort($iSortOld);
                $this->PluginPage_Page_UpdatePage($oPagePrev);
            } else {
                if ($sDirection == 'down') {
                    $iSortNew = $iSortOld - 1;
                } else {
                    $iSortNew = $iSortOld + 1;
                }
            }
            /**
             * Меняем значения сортировки местами
             */
            $oPage->setSort($iSortNew);
            $this->PluginPage_Page_UpdatePage($oPage);
        }

    }

    /**
     * Обработка отправки формы добавления новой страницы
     *
     */
    protected function EventPagesAddSubmit()
    {
        // * Проверяем корректность полей
        if (!$this->EventPagesCheckFields()) {
            return;
        }
        // * Заполняем свойства
        $oPage = Engine::GetEntity('PluginPage_Page');
        $oPage->setAutoBr(getRequest('page_auto_br') ? 1 : 0);
        $oPage->setActive(getRequest('page_active') ? 1 : 0);
        $oPage->setMain(getRequest('page_main') ? 1 : 0);
        $oPage->setDateAdd(date("Y-m-d H:i:s"));
        $oPage->setOtherUrl(getRequest('page_other_url'));
        if (getRequest('page_pid') == 0) {
            $oPage->setUrlFull(getRequest('page_url'));
            $oPage->setPid(null);
        } else {
            $oPage->setPid(getRequest('page_pid'));
            $oPageParent = $this->PluginPage_Page_GetPageById(getRequest('page_pid'));
            $oPage->setUrlFull($oPageParent->getUrlFull() . '/' . getRequest('page_url'));
        }
        $oPage->setSeoDescription(getRequest('page_seo_description'));
        $oPage->setSeoKeywords(getRequest('page_seo_keywords'));
        $oPage->setText(getRequest('page_text'));
        $oPage->setTitle(getRequest('page_title'));
        $oPage->setUrl(getRequest('page_url'));

        if (getRequest('page_sort')) {
            $oPage->setSort(intval(getRequest('page_sort')));
        } else {
            $oPage->setSort($this->PluginPage_Page_GetMaxSortByPid($oPage->getPid()) + 1);
        }

        // * Добавляем страницу
        if ($this->PluginPage_Page_AddPage($oPage)) {
            $this->MessageNotice($this->Lang_Get('page_create_submit_save_ok'), 'page:add');
            $this->SetParam(0, null);
        } else {
            $this->MessageError($this->Lang_Get('system_error'), 'page:add');
        }
    }

    /**
     * Обработка вывода формы для редактирования страницы
     *
     */
    protected function EventPagesEdit()
    {
        if (($oPageEdit = $this->PluginPage_Page_GetPageById($this->GetParam(1)))) {
            if ($this->getRequestCheck('submit_page_save')) {
                // * Если отправили форму с редактированием, то обрабатываем её
                $this->EventPagesEditSubmit($oPageEdit);
            } else {
                $_REQUEST['page_id'] = $oPageEdit->getId();

                $_REQUEST['page_title'] = $oPageEdit->getTitle();
                $_REQUEST['page_pid'] = $oPageEdit->getPid();
                $_REQUEST['page_url'] = $oPageEdit->getUrl();
                $_REQUEST['page_text'] = $oPageEdit->getText();
                $_REQUEST['page_seo_keywords'] = $oPageEdit->getSeoKeywords();
                $_REQUEST['page_seo_description'] = $oPageEdit->getSeoDescription();

                $_REQUEST['page_active'] = $oPageEdit->getActive();
                $_REQUEST['page_auto_br'] = $oPageEdit->getAutoBr();
                $_REQUEST['page_main'] = $oPageEdit->getMain();
                $_REQUEST['page_sort'] = $oPageEdit->getSort();
                $_REQUEST['page_other_url'] = $oPageEdit->getOtherUrl();
            }
            $this->Viewer_Assign('oPageEdit', $oPageEdit);
        } else {
            $this->MessageError($this->Lang_Get('page_edit_notfound'), 'page:edit');
            $this->SetParam(0, null);
        }
    }

    /**
     * Обработка отправки формы при редактировании страницы
     *
     * @param unknown_type $oPageEdit
     */
    protected function EventPagesEditSubmit($oPageEdit)
    {
        $this->Security_ValidateSendForm();
        // * Проверяем корректность полей
        if (!$this->EventPagesCheckFields()) {
            return;
        }

        if ($oPageEdit->getId() == getRequest('page_pid')) {
            $this->MessageError($this->Lang_Get('system_error'), 'page:edit');
            return;
        }

        // * Обновляем свойства страницы
        $oPageEdit->setAutoBr(getRequest('page_auto_br') ? 1 : 0);
        $oPageEdit->setActive(getRequest('page_active') ? 1 : 0);
        $oPageEdit->setMain(getRequest('page_main') ? 1 : 0);
        $oPageEdit->setDateEdit(date("Y-m-d H:i:s"));
        if (getRequest('page_pid') == 0) {
            $oPageEdit->setUrlFull(getRequest('page_url'));
            $oPageEdit->setPid(null);
        } else {
            $oPageEdit->setPid(getRequest('page_pid'));
            $oPageParent = $this->PluginPage_Page_GetPageById(getRequest('page_pid'));
            $oPageEdit->setUrlFull($oPageParent->getUrlFull() . '/' . getRequest('page_url'));
        }
        $oPageEdit->setSeoDescription(getRequest('page_seo_description'));
        $oPageEdit->setSeoKeywords(getRequest('page_seo_keywords'));
        $oPageEdit->setText(getRequest('page_text'));
        $oPageEdit->setTitle(getRequest('page_title'));
        $oPageEdit->setUrl(getRequest('page_url'));
        $oPageEdit->setSort(intval(getRequest('page_sort')));
        $oPageEdit->setOtherUrl(getRequest('page_other_url'));

        // * Обновляем страницу
        if ($this->PluginPage_Page_UpdatePage($oPageEdit)) {
            $this->PluginPage_Page_RebuildUrlFull($oPageEdit);
            $this->MessageNotice($this->Lang_Get('page_edit_submit_save_ok'), 'page:edit');
            $this->SetParam(0, null);
            $this->SetParam(1, null);
        } else {
            $this->MessageError($this->Lang_Get('system_error'), 'page:edit');
        }
    }

    /**
     * Обработка удаления страницы
     *
     * @return  bool
     */
    protected function EventPagesDelSubmit()
    {
        $nPageId = $this->GetRequestCheck('page_id');
        if ($this->PluginPage_Page_DeletePageById($nPageId)) {
            $this->MessageNotice($this->Lang_Get('page_admin_action_delete_ok'), 'page:delete', true);
            return true;
        } else {
            $this->MessageError($this->Lang_Get('page_admin_action_delete_error'), 'page:delete', true);
            return false;
        }
    }

    /**
     * Обработка вывода/сохранения опций статических страниц
     */
    protected function EventPagesOptions()
    {
        if ($this->GetRequestCheck('submit_options_save')) {
            if ($this->EventInfoParamsSubmit()) {
                $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'page:options');
            } else {
                $this->MessageError($this->Lang_Get('adm_saved_err'), 'page:options');
            }
        }
        $this->Viewer_Assign('sParamPageUrlReserved', implode(',', $this->aConfig['reserverd_urls']));
    }

    /**
     * Проверка полей на корректность
     *
     * @return  bool
     */
    protected function EventPagesCheckFields()
    {
        $this->Security_ValidateSendForm();

        $bOk = true;

        // * Проверяем есть ли заголовок страницы
        if (!func_check(getRequest('page_title'), 'text', 2, 200)) {
            $this->MessageError($this->Lang_Get('page_create_title_error'), $this->Lang_Get('error'));
            $bOk = false;
        }
        //  * Проверяем есть ли заголовок страницы, с заменой всех пробельных символов на "_"
        $pageUrl = preg_replace("/\s+/", '_', getRequest('page_url'));
        $_REQUEST['page_url'] = $pageUrl;
        if (!func_check(getRequest('page_url'), 'login', 1, 50)) {
            $this->MessageError($this->Lang_Get('page_create_url_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        // * Проверяем на плохие/зарезервированные УРЛы
        if (in_array(getRequest('page_url'), $this->aConfig['reserverd_urls'])) {
            $this->MessageError($this->Lang_Get('page_create_url_error_bad') . ' ' . join(',', $this->aConfig['reserverd_urls']), $this->Lang_Get('error'));
            $bOk = false;
        }

        // * Проверяем есть ли содержимое страницы
        if (!func_check(getRequest('page_text'), 'text', 1, 50000)) {
            $this->MessageError($this->Lang_Get('page_create_text_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        // * Проверяем страницу в которую хотим вложить
        if (getRequest('page_pid') != 0 and !($oPageParent = $this->PluginPage_Page_GetPageById(getRequest('page_pid')))) {
            $this->MessageError($this->Lang_Get('page_create_parent_page_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        // * Проверяем сортировку
        if (getRequest('page_sort') and !is_numeric(getRequest('page_sort'))) {
            $this->Message_AddError($this->Lang_Get('page_create_sort_error'), $this->Lang_Get('error'));
            $bOk = false;
        }

        // * Выполнение хуков
        $this->Hook_Run('check_page_fields', array('bOk' => &$bOk));

        return $bOk;
    }

    /*
     * URL: admin/pages
     * ==================================================================================== */

    /* ==================================================================================== *
     * URL: admin/blogs/
     */
    protected function EventBlogs()
    {
        $this->sMenuSubItemSelect = 'list';
        $sMode = 'all';

        $sCmd = $this->GetParam(0);
        if ($sCmd == 'delete') {
            $this->EventBlogsDelete();
        } else {
            // * Передан ли номер страницы
            if (preg_match("/^page(\d+)$/i", $this->GetLastParam(), $aMatch)) {
                $iPage = $aMatch[1];
            } else {
                $iPage = 1;
            }
        }

        if ($this->GetParam(1) AND !strstr($this->GetParam(1), 'page')) $sMode = $this->GetParam(1);

        $aParams = array();
        if ($sMode AND $sMode != 'all') $aParams['type'] = $sMode;

        $iCount = 0;
        $aResult = $this->PluginAceadminpanel_Admin_GetBlogList($iCount, $iPage, $this->aConfig['items_per_page'], $aParams);
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4,
                                            Config::Get('path.root.web') . '/admin/blogs/list' . ($sMode ? '/' . $sMode
                                                    : ''));

        $aBlogTypes = $this->PluginAceadminpanel_Admin_GetBlogTypes();
        foreach ($aBlogTypes as $aRow) {
            $iCount += $aRow['blog_cnt'];
        }

        $this->Viewer_Assign('iBlogsTotal', $iCount);
        $this->Viewer_Assign('aBlogTypes', $aBlogTypes);
        $this->Viewer_Assign('aBlogs', $aResult['collection']);
        $this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        $this->Viewer_Assign('sMode', $sMode);
        $this->Viewer_Assign('aPaging', $aPaging);

    }

    protected function EventBlogsDelete()
    {
        $bOk = false;
        $iBlogId = $this->GetRequestCheck('blog_id');
        if ($iBlogId AND ($oBlog = $this->Blog_GetBlogById($iBlogId))) {
            $bOk = $this->PluginAceadminpanel_Admin_DelBlog($iBlogId);
        }
        if ($bOk) {
            $this->MessageNotice($this->Lang_Get('adm_action_ok'), 'blog_del');
        } else {
            $this->MessageError($this->Lang_Get('adm_action_err'), 'blog_del');
        }
        $this->GoToBackPage();
    }

    /*
     * URL: admin/blogs/
     * ==================================================================================== */

    /* ==================================================================================== *
     * URL: admin/topics/
     */
    protected function EventTopics()
    {
        $this->sMenuSubItemSelect = 'list';
        $sMode = 'all';

        $sCmd = $this->GetParam(0);
        if ($sCmd == 'delete') {
            return $this->EventTopicsDelete();
        } else {
            return parent::EventNotFound();
        }
    }

    protected function EventTopicsDelete()
    {
        $bOk = false;
        $iTopicId = intval($this->GetRequestCheck('topic_id'));
        if ($iTopicId AND ($oTopic = $this->Topic_GetTopicById($iTopicId))) {
            $bOk = $this->PluginAceadminpanel_Admin_DelTopic($oTopic);
        }
        if ($bOk) {
            $this->MessageNotice($this->Lang_Get('adm_action_ok'), 'blog_del');
        } else {
            $this->MessageError($this->Lang_Get('adm_action_err'), 'blog_del');
        }
        $this->GoToBackPage();
    }

    /*
     * URL: admin/topics/
     * ====================================================================================*/

    /* ==================================================================================== *
     * URL: admin/users
     */
    protected function EventUsers()
    {
        $this->sMenuSubItemSelect = 'list';

        if (($sAdminAction = $this->getRequestCheck('adm_user_action'))) {
            if ($sAdminAction == 'adm_ban_user') $this->EventUsersBan();
            elseif ($sAdminAction == 'adm_unban_user') $this->EventUsersUnBan();
            elseif ($sAdminAction == 'adm_ban_ip') $this->EventUsersBanIp();
            elseif ($sAdminAction == 'adm_unban_ip') $this->EventUsersUnBanIp(null);
            elseif ($sAdminAction == 'adm_user_setadmin') $this->EventUsersAddAdministrator();
            elseif ($sAdminAction == 'adm_del_user') $this->EventUsersDelete();
            elseif ($sAdminAction == 'adm_user_message') $this->EventUsersMessage();
        }
        if ($this->GetParam(0) == 'activate') { // активация юзера
            $this->EventUsersActivate();
        } elseif ($this->GetParam(0) == 'profile') { // профиль юзера
            if (isset($_SERVER['HTTP_REFERER'])) {
                $this->Viewer_Assign('sPageRef', $_SERVER['HTTP_REFERER']);
            }
            else {
                $this->Viewer_Assign('sPageRef', '');
            }
            $this->_AddBlock('right', 'admin_user');
            return $this->EventUsersProfile();
        } elseif ($this->GetParam(0) == 'fields') { // кастомные поля пользователей
            $this->sMenuSubItemSelect = 'fields';
            return $this->EventUsersFields();
        } elseif ($this->GetParam(0) == 'banlist') { // бан лист
            $this->sMenuSubItemSelect = 'banlist';
            $this->_AddBlock('right', 'admin_ban');
            return $this->EventUsersBanlist();
        } elseif ($this->GetParam(0) == 'invites') { // инвайты
            $this->sMenuSubItemSelect = 'invites';
            return $this->EventUsersInvites();
        } elseif ($this->GetParam(0) == 'admins' AND $this->GetParam(1) == 'del') {
            $this->EventUsersDelAdministrator();
        } else {
            $this->EventUsersList();
        }
    }

    protected function EventUserfields()
    {
        return $this->EventUsersFields();
    }

    protected function EventUsersFields()
    {
        $this->sMenuSubItemSelect = 'fields';
        $this->PluginSetTemplate('users');

        // * Загружаем в шаблон JS текстовки
        $this->Lang_AddLangJs(array('user_field_delete_confirm'));
        // * Получаем список всех полей
        $aUserFields = $this->User_getUserFields();
        $this->Viewer_Assign('aUserFields', $aUserFields);
        $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/users_fields.tpl');
    }

    protected function EventUsersBan($sUserLogin = null)
    {
        $bOk = false;
        if (!$sUserLogin) $sUserLogin = getRequest('ban_login');
        if ($sUserLogin == $this->oUserCurrent->GetLogin()) {
            $this->MessageError($this->Lang_Get('adm_cannot_ban_self'), 'users:ban');
            return false;
        }
        if (getRequest('ban_period') == 'days') {
            $nDays = intVal(getRequest('ban_days'));
        } else {
            $nDays = null;
        }
        $sComment = getRequest('ban_comment');
        if ($sUserLogin AND ($oUser = $this->PluginAceadminpanel_Admin_GetUserByLogin($sUserLogin))) {
            if (mb_strtolower($sUserLogin) == 'admin') {
                $this->MessageError($this->Lang_Get('adm_cannot_with_admin'), 'users:ban');
            } elseif ($oUser->IsAdministrator()) {
                $this->MessageError($this->Lang_Get('adm_cannot_ban_admin'), 'users:ban');
            } else {
                $this->PluginAceadminpanel_Admin_SetUserBan($oUser->GetId(), $nDays, $sComment);
                $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'users:ban');
                $bOk = true;
            }
        } else {
            $this->MessageError($this->Lang_Get('adm_user_not_found', Array('user' => $sUserLogin)), 'users:ban');
        }

        //if (getRequest('adm_user_ref')) func_header_location(getRequest('adm_user_ref'));
        return $bOk;
    }

    protected function EventUsersUnBan()
    {
        $this->Security_ValidateSendForm();

        $sUserLogin = getRequest('ban_login');
        if ($sUserLogin AND ($nUserId = $this->PluginAceadminpanel_Admin_GetUserId($sUserLogin))) {
            if ($this->PluginAceadminpanel_Admin_ClearUserBan($nUserId)) {
                $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'users:unban');
            } else {
                $this->MessageError($this->Lang_Get('adm_saved_err'), 'users:unban');
            }
        }
        if (getRequest('adm_user_ref')) func_header_location(getRequest('adm_user_ref'));
    }

    protected function EventUsersAddAdministrator()
    {
        $sUserLogin = $this->getRequestCheck('user_login_admin');
        if (!$sUserLogin OR !($oUser = $this->PluginAceadminpanel_Admin_GetUserByLogin($sUserLogin))) {
            $this->MessageError($this->Lang_Get('adm_user_not_found', $sUserLogin), 'admins:add');
        } elseif ($oUser->IsBanned()) {
            $this->MessageError($this->Lang_Get('adm_cannot_be_banned'), 'admins:add');
        } elseif ($oUser->IsAdministrator()) {
            $this->MessageError($this->Lang_Get('adm_already_added'), 'admins:add');
        } else {
            if ($this->PluginAceadminpanel_Admin_AddAdministrator($oUser->GetId())) {
                $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'admins:add');
            } else {
                $this->MessageError($this->Lang_Get('adm_saved_err'), 'admins:add');
            }
        }
        if (getRequest('adm_user_ref')) func_header_location(getRequest('adm_user_ref'));
    }

    protected function EventUsersDelAdministrator()
    {
        $sUserLogin = $this->getRequestCheck('user_login');
        if (!$sUserLogin OR !($oUser = $this->PluginAceadminpanel_Admin_GetUserByLogin($sUserLogin))) {
            $this->MessageError($this->Lang_Get('adm_user_not_found', $sUserLogin), 'admins:delete');
        } else {
            if (mb_strtolower($sUserLogin) == 'admin') {
                $this->MessageError($this->Lang_Get('adm_cannot_with_admin'), 'admins:delete');
            } elseif ($this->PluginAceadminpanel_Admin_DelAdministrator($oUser->GetId())) {
                $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'admins:delete');
            } else {
                $this->MessageError($this->Lang_Get('adm_saved_err'), 'admins:delete');
            }
        }
        if (getRequest('adm_user_ref')) func_header_location(getRequest('adm_user_ref'));
        else func_header_location(Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/users/admins/');
    }

    protected function EventUsersBanIp()
    {
        $ip1_1 = getRequest('adm_ip1_1');
        $ip1_2 = getRequest('adm_ip1_2');
        $ip1_3 = getRequest('adm_ip1_3');
        $ip1_4 = getRequest('adm_ip1_4');

        $ip2_1 = getRequest('adm_ip2_1');
        $ip2_2 = getRequest('adm_ip2_2');
        $ip2_3 = getRequest('adm_ip2_3');
        $ip2_4 = getRequest('adm_ip2_4');

        $sComment = getRequest('ban_comment');

        $ip1 = $ip1_1 . '.' . $ip1_2 . '.' . $ip1_3 . '.' . $ip1_4;
        $ip2 = $ip2_1 . '.' . $ip2_2 . '.' . $ip2_3 . '.' . $ip2_4;
        if (getRequest('ban_period') == 'days') {
            $nDays = intVal(getRequest('ban_days'));
        } else {
            $nDays = null;
        }
        if ($this->PluginAceadminpanel_Admin_SetBanIp($ip1, $ip2, $nDays, $sComment)) {
            $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'banip:add');
        } else {
            $this->MessageError($this->Lang_Get('adm_saved_err'), 'banip:add');
        }
        if (getRequest('adm_user_ref')) func_header_location(getRequest('adm_user_ref'));
    }

    protected function EventUsersUnBanIp($nId)
    {
        if ($this->PluginAceadminpanel_Admin_ClearBanIp($nId)) {
            $this->MessageNotice($this->Lang_Get('adm_saved_ok'), 'banip:delete');
        } else {
            $this->MessageError($this->Lang_Get('adm_saved_err'), 'banip:delete');
        }
        if (getRequest('adm_user_ref')) func_header_location(getRequest('adm_user_ref'));
    }

    protected function EventUsersMessageSeparate()
    {
        $bOk = true;

        $sTitle = getRequest('talk_title');
        // if (substr($sTitle, 0, 1)!='*') $sTitle='*'.$sTitle;
        $sText = $this->Text_Parser(getRequest('talk_text'));
        $sDate = date("Y-m-d H:i:s");
        $sIp = func_getIp();

        if (($sUsers = getRequest('users_list'))) {
            $aUsers = explode(',', str_replace(' ', '', $sUsers));
        } else {
            $aUsers = array();
        }

        if ($aUsers) {
            // Если указано, то шлем самому себе со списком получателей
            if (getRequest('send_copy_self')) {
                $oSelfTalk = Engine::GetEntity('Talk_Talk');
                $oSelfTalk->setUserId($this->oUserCurrent->getId());
                $oSelfTalk->setTitle($sTitle);
                $oSelfTalk->setText($this->Text_Parser('To: <i>' . $sUsers . '</i>' . "\n\n" . 'Msg: ' . getRequest('talk_text')));
                $oSelfTalk->setDate($sDate);
                $oSelfTalk->setDateLast($sDate);
                $oSelfTalk->setUserIp($sIp);
                if (($oSelfTalk = $this->Talk_AddTalk($oSelfTalk))) {
                    $oTalkUser = Engine::GetEntity('Talk_TalkUser');
                    $oTalkUser->setTalkId($oSelfTalk->getId());
                    $oTalkUser->setUserId($this->oUserCurrent->getId());
                    $oTalkUser->setDateLast($sDate);
                    $this->Talk_AddTalkUser($oTalkUser);

                    // уведомление по e-mail
                    $oUserToMail = $this->oUserCurrent;
                    $this->Notify_SendTalkNew($oUserToMail, $this->oUserCurrent, $oSelfTalk);
                } else {
                    $bOk = false;
                }
            }

            if ($bOk) {
                // теперь рассылаем остальным - каждому отдельное сообщение
                foreach ($aUsers as $sUserLogin) {
                    if ($sUserLogin AND $sUserLogin != $this->oUserCurrent->getLogin() AND ($iUserId = $this->PluginAceadminpanel_Admin_GetUserId($sUserLogin))) {
                        $oTalk = Engine::GetEntity('Talk_Talk');
                        $oTalk->setUserId($this->oUserCurrent->getId());
                        $oTalk->setTitle($sTitle);
                        $oTalk->setText($sText);
                        $oTalk->setDate($sDate);
                        $oTalk->setDateLast($sDate);
                        $oTalk->setUserIp($sIp);
                        if (($oTalk = $this->Talk_AddTalk($oTalk))) {
                            $oTalkUser = Engine::GetEntity('Talk_TalkUser');
                            $oTalkUser->setTalkId($oTalk->getId());
                            $oTalkUser->setUserId($iUserId);
                            $oTalkUser->setDateLast(null);
                            $this->Talk_AddTalkUser($oTalkUser);

                            // Отправка самому себе, чтобы можно было читать ответ
                            $oTalkUser = Engine::GetEntity('Talk_TalkUser');
                            $oTalkUser->setTalkId($oTalk->getId());
                            $oTalkUser->setUserId($this->oUserCurrent->getId());
                            $oTalkUser->setDateLast($sDate);
                            $this->Talk_AddTalkUser($oTalkUser);

                            // Отправляем уведомления
                            $oUserToMail = $this->User_GetUserById($iUserId);
                            $this->Notify_SendTalkNew($oUserToMail, $this->oUserCurrent, $oTalk);
                        } else {
                            $bOk = false;
                            break;
                        }
                    }
                }
            }
        }

        if ($bOk) {
            $this->MessageNotice($this->Lang_Get('adm_msg_sent_ok'));
        } else {
            $this->MessageError($this->Lang_Get('system_error'));
        }
    }

    protected function EventUsersMessageCommon()
    {
        $bOk = true;

        $sTitle = getRequest('talk_title');
        $sText = $this->Text_Parser(getRequest('talk_text'));
        $sDate = date("Y-m-d H:i:s");
        $sIp = func_getIp();

        if (($sUsers = getRequest('users_list'))) {
            $aUsers = explode(',', str_replace(' ', '', $sUsers));
        } else {
            $aUsers = array();
        }

        if ($aUsers) {
            if ($bOk AND $aUsers) {
                $oTalk = Engine::GetEntity('Talk_Talk');
                $oTalk->setUserId($this->oUserCurrent->getId());
                $oTalk->setTitle($sTitle);
                $oTalk->setText($sText);
                $oTalk->setDate($sDate);
                $oTalk->setDateLast($sDate);
                $oTalk->setUserIp($sIp);
                $oTalk = $this->Talk_AddTalk($oTalk);

                // добавляем себя в общий список
                $aUsers[] = $this->oUserCurrent->getLogin();
                // теперь рассылаем остальным
                foreach ($aUsers as $sUserLogin) {
                    if ($sUserLogin AND ($iUserId = $this->PluginAceadminpanel_Admin_GetUserId($sUserLogin))) {
                        $oTalkUser = Engine::GetEntity('Talk_TalkUser');
                        $oTalkUser->setTalkId($oTalk->getId());
                        $oTalkUser->setUserId($iUserId);
                        if ($sUserLogin != $this->oUserCurrent->getLogin()) {
                            $oTalkUser->setDateLast(null);
                        } else {
                            $oTalkUser->setDateLast($sDate);
                        }
                        $this->Talk_AddTalkUser($oTalkUser);

                        // Отправляем уведомления
                        if ($sUserLogin != $this->oUserCurrent->getLogin() OR getRequest('send_copy_self')) {
                            $oUserToMail = $this->User_GetUserById($iUserId);
                            $this->Notify_SendTalkNew($oUserToMail, $this->oUserCurrent, $oTalk);
                        }
                    }
                }
            }
        }

        if ($bOk) {
            $this->MessageNotice($this->Lang_Get('adm_msg_sent_ok'));
        } else {
            $this->MessageError($this->Lang_Get('system_error'));
        }
    }

    protected function EventUsersMessage()
    {
        if ($this->getRequestCheck('send_common_message') == 'yes') {
            $this->EventUsersMessageCommon();
        } else {
            $this->EventUsersMessageSeparate();
        }
    }

    protected function EventUsersProfile()
    {
        $sUserLogin = $this->GetParam(1);
        $oUserProfile = $this->PluginAceadminpanel_Admin_GetUserByLogin($sUserLogin);
        if (!$oUserProfile) {
            return parent::EventNotFound();
        }

        $this->sMenuSubItemSelect = 'profile';
        $sMode = $this->GetParam(2);
        $aUserVoteStat = $this->PluginAceadminpanel_Admin_GetUserVoteStat($oUserProfile->getId());

        if ($sMode == 'topics') {
            $this->EventUsersProfileTopics($oUserProfile);
        } elseif ($sMode == 'blogs') {
            $this->EventUsersProfileBlogs($oUserProfile);
        } elseif ($sMode == 'comments') {
            $this->EventUsersProfileComments($oUserProfile);
        } elseif ($sMode == 'votes') {
            $this->EventUsersProfileVotes($oUserProfile);
        } else {
            $sMode = 'info';
            $this->EventUsersProfileInfo($oUserProfile);
        }

        $this->Viewer_Assign('sMode', $sMode);
        $this->Viewer_Assign('oUserProfile', $oUserProfile);
        $this->Viewer_Assign('aUserVoteStat', $aUserVoteStat);
        $this->Viewer_Assign('nParamVoteValue', $this->aConfig['vote_value']);

        $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/users_profile.tpl');
    }

    protected function EventUsersProfileInfo($oUserProfile)
    {
        // * Получаем список друзей
        $aUsersFriend = $this->User_GetUsersFriend($oUserProfile->getId());

        if (Config::Get('general.reg.invite')) {
            // * Получаем список тех кого пригласил юзер
            $aUsersInvite = $this->User_GetUsersInvite($oUserProfile->getId());
            $this->Viewer_Assign('aUsersInvite', $aUsersInvite);
            // * Получаем того юзера, кто пригласил текущего
            $oUserInviteFrom = $this->User_GetUserInviteFrom($oUserProfile->getId());
            $this->Viewer_Assign('oUserInviteFrom', $oUserInviteFrom);
        }
        // * Получаем список блогов в которых состоит юзер
        $aBlogUsers = $this->Blog_GetBlogUsersByUserId($oUserProfile->getId(), ModuleBlog::BLOG_USER_ROLE_USER);
        $aBlogModerators = $this->Blog_GetBlogUsersByUserId($oUserProfile->getId(), ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        $aBlogAdministrators = $this->Blog_GetBlogUsersByUserId($oUserProfile->getId(), ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);

        // * Получаем список блогов которые создал юзер
        $aBlogsOwner = $this->Blog_GetBlogsByOwnerId($oUserProfile->getId());

        $nUserTopicCount = 0;
        $aLastTopics = $this->Topic_GetTopicsPersonalByUser($oUserProfile->getId(), 1, $nUserTopicCount, 1, 5);

        // * Загружаем переменные в шаблон
        $this->Viewer_Assign('aBlogsUser', $aBlogUsers);
        $this->Viewer_Assign('aBlogsModeration', $aBlogModerators);
        $this->Viewer_Assign('aBlogsAdministration', $aBlogAdministrators);
        $this->Viewer_Assign('aBlogsOwner', $aBlogsOwner);
        $this->Viewer_Assign('aUsersFriend', $aUsersFriend);
        $this->Viewer_Assign('aLastTopicList', $aLastTopics['collection']);
    }

    protected function EventUsersProfileTopics($oUserProfile)
    {
        $sMode = 'topics';

        if (preg_match("/^page(\d+)$/i", $this->getParam(0), $aMatch)) {
            $iPage = $aMatch[1];
        } else {
            $iPage = 1;
        }

        $iCount = 0;
        $aResult = $this->Topic_GetTopicsPersonalByUser($oUserProfile->getId(), 1, $iCount, $iPage, $this->aConfig['items_per_page']);
        $aTopics = $aResult['collection'];
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4,
                                            Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/profile/' . $oUserProfile->getLogin() . '/topics/');

        $this->Viewer_Assign('aTopics', $aTopics);
        $this->Viewer_Assign('aPaging', $aPaging);

    }

    protected function EventUsersProfileBlogs($oUserProfile)
    {
        $sMode = 'blogs';

        $aBlogs = $this->PluginAceadminpanel_Admin_GetBlogsByUserId($oUserProfile->GetId());
        $this->Viewer_Assign('aBlogs', $aBlogs);
    }

    protected function EventUsersProfileComments($oUserProfile)
    {
        $sMode = 'comments';

        if (preg_match("/^page(\d+)$/i", $this->getParam(0), $aMatch)) {
            $iPage = $aMatch[1];
        } else {
            $iPage = 1;
        }

        $aResult = $this->Comment_GetCommentsByUserId($oUserProfile->getId(), 'topic', $iPage, $this->aConfig['items_per_page']);
        $aComments = $aResult['collection'];
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4, Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/profile/' . $oUserProfile->getLogin() . '/comments/');

        $this->Viewer_Assign('aComments', $aComments);
        $this->Viewer_Assign('aPaging', $aPaging);
    }

    protected function EventUsersProfileVotes($oUserProfile)
    {
        $aVotes = $this->PluginAceadminpanel_Admin_GetVotesByUserId($oUserProfile->getId(), $this->aConfig['votes_per_page']);

        $this->Viewer_Assign('aVotes', $aVotes);
    }

    protected function EventUsersActivate()
    {
        $this->Security_ValidateSendForm();
        $sUserLogin = $this->GetParam(1);
        $oUser = $this->User_GetUserByLogin($sUserLogin);
        $oUser->setActivate(1);
        $oUser->setDateActivate(date('Y-m-d H:i:s'));
        $this->User_Update($oUser);
        if (isset($_SERVER['HTTP_REFERER'])) {
            func_header_location($_SERVER['HTTP_REFERER']);
        }
    }

    protected function EventUsersList()
    {
        $nParam = 0;
        if (($sData = $this->Session_Get('adm_userlist_filter'))) {
            $aFilter = unserialize($sData);
        }
        else {
            $aFilter = array();
        }

        if (($sData = $this->Session_Get('adm_userlist_sort'))) {
            $aSort = unserialize($sData);
        }
        else {
            $aSort = array();
        }

        if ($this->getParam($nParam) == 'admins') {
            $sMode = 'admins';
            $nParam += 1;
            $aFilter['admin'] = 1;
        } elseif ($this->getParam($nParam) == 'all') {
            $sMode = 'all';
            $nParam += 1;
            $aFilter['admin'] = null;
        } else {
            $sMode = 'all';
            $aFilter['admin'] = null;
        }

        $sUserIp = '*.*.*.*';
        $sUserRegDate = '';
        if ($this->getRequestCheck('adm_user_action') == 'adm_user_seek') {
            if (($sUserLogin = getRequest('user_login_seek'))) {
                if ($this->PluginAceadminpanel_Admin_GetUserId($sUserLogin)) {
                    $aFilter['login'] = $sUserLogin;
                }
                else {
                    $aFilter['like'] = $sUserLogin;
                }
            } else {
                $aFilter['login'] = $aFilter['like'] = null;
            }

            if (($s = getRequest('user_ip1_seek')) > '' AND is_numeric($s)) {
                $aUserIp[0] = intVal($s);
            } else {
                $aUserIp[0] = '*';
            }
            if (($s = getRequest('user_ip2_seek')) > '' AND is_numeric($s)) {
                $aUserIp[1] = intVal($s);
            } else {
                $aUserIp[1] = '*';
            }
            if (($s = getRequest('user_ip3_seek')) > '' AND is_numeric($s)) {
                $aUserIp[2] = intVal($s);
            } else {
                $aUserIp[2] = '*';
            }
            if (($s = getRequest('user_ip4_seek')) > '' AND is_numeric($s)) {
                $aUserIp[3] = intVal($s);
            } else {
                $aUserIp[3] = '*';
            }
            $sUserIp = $aUserIp[0] . '.' . $aUserIp[1] . '.' . $aUserIp[2] . '.' . $aUserIp[3];
            if ($sUserIp != '*.*.*.*') {
                if (preg_match('/\*\.\d/', $sUserIp)) {
                    $this->MessageError($this->Lang_Get('adm_err_wrong_ip'), 'users:list');
                } else {
                    $aFilter['ip'] = $sUserIp;
                }
            } else {
                $aFilter['ip'] = null;
            }

            if (($s = getRequest('user_regdate_seek'))) {
                if (preg_match('/(\d{4})(\-(\d{1,2})){0,1}(\-(\d{1,2})){0,1}/', $s, $aMatch)) {
                    if (isset($aMatch[1])) {
                        $sUserRegDate = $aMatch[1];
                        if (isset($aMatch[3])) {
                            $sUserRegDate .= '-' . sprintf('%02d', $aMatch[3]);
                            if (isset($aMatch[5])) {
                                $sUserRegDate .= '-' . sprintf('%02d', $aMatch[5]);
                            }
                        }
                    }
                }
                if ($sUserRegDate) {
                    $aFilter['regdate'] = $sUserRegDate;
                } else {
                    $aFilter['regdate'] = null;
                }
            }
            if (($s = getRequest('user_list_sort'))) {
                if (in_array($s, array('id', 'login', 'regdate', 'reg_ip', 'activated', 'last_date', 'last_ip'))) {
                    $aSort = array(); // так надо на будущее
                    $sUserListSort = $s;
                    $sUserListOrder = getRequest('user_list_order');
                    $aSort[$sUserListSort] = $sUserListOrder;
                }
            } else {
                $aSort = array();
            }
        }

        // Передан ли номер страницы
        if (preg_match("/^page(\d+)$/i", $this->getParam($nParam), $aMatch)) {
            $iPage = $aMatch[1];
        } else {
            $iPage = 1;
        }

        foreach ($aFilter as $key => $val) {
            if ($val === null) unset($aFilter[$key]);
        }
        $sUserListSort = $sUserListOrder = '';
        foreach ($aSort as $key => $val) {
            if ($val !== null) {
                $sUserListSort = $key;
                $sUserListOrder = $val;
            }
        }
        $this->Session_Set('adm_userlist_filter', serialize($aFilter));
        $this->Session_Set('adm_userlist_sort', serialize($aSort));
        // Получаем список юзеров
        $iCount = 0;
        $aResult = $this->PluginAceadminpanel_Admin_GetUserList($iCount, $iPage, $this->aConfig['items_per_page'], $aFilter, $aSort);
        if (($iPage > 1) AND ($iPage > $aResult['count'] / $this->aConfig['items_per_page'])) {
            $iPage = ceil($aResult['count'] / $this->aConfig['items_per_page']);
            $aResult = $this->PluginAceadminpanel_Admin_GetUserList($iCount, $iPage, $this->aConfig['items_per_page'], $aFilter, $aSort);
        }
        $aUserList = $aResult['collection'];
        /**
         * Формируем постраничность
         */
        if ($sMode == 'admins') {
            $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4, Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/users/admins');
        } else {
            $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4, Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/users');
        }
        $aStat = $this->User_GetStatUsers();

        // * Загружаем переменные в шаблон
        if ($aUserList) {
            $this->Viewer_Assign('aPaging', $aPaging);
        }
        if (isset($aFilter['admin'])) unset($aFilter['admin']); // чтобы блок в админке не раскрывался

        if (isset($aFilter['login']) AND $aFilter['login']) $sUserLoginSeek = $aFilter['login'];
        elseif (isset($aFilter['like']) AND $aFilter['like']) $sUserLoginSeek = $aFilter['like'];
        else $sUserLoginSeek = '';

        if (isset($aFilter['ip']) AND $aFilter['ip']) $sUserIp = $aFilter['ip'];
        $aUserIp = explode('.', $sUserIp);

        $this->Viewer_Assign('aUserList', $aUserList);
        $this->Viewer_Assign('aStat', $aStat);
        $this->Viewer_Assign('sMode', $sMode);
        $this->Viewer_Assign('sUserLoginSeek', $sUserLoginSeek);
        $this->Viewer_Assign('sUserListSort', $sUserListSort);
        $this->Viewer_Assign('sUserListOrder', $sUserListOrder);
        $this->Viewer_Assign('aUserIp', $aUserIp);
        $this->Viewer_Assign('aFilter', $aFilter);
        $this->Viewer_Assign('aSort', $aSort);
        $this->Viewer_Assign('USER_USE_ACTIVATION', Config::Get('general.reg.activation'));

        $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/users_list.tpl');
        $this->_addBlock('right', 'admin_admin');
    }

    // Список забаненных ip-адресов
    protected function EventUsersBanlistIps()
    {
        $sMode = 'ips';
        if ($this->GetParam(2) == 'del') {
            $nId = $this->GetParam(3);
            $this->EventUsersUnBanIp($nId);
        }

        // Передан ли номер страницы
        if (preg_match("/^page(\d+)$/i", $this->getParam(2), $aMatch)) {
            $iPage = $aMatch[1];
        } else {
            $iPage = 1;
        }

        // Получаем список забаненных ip-адресов
        $iCount = 0;
        $aResult = $this->PluginAceadminpanel_Admin_GetBanListIp($iCount, $iPage, $this->aConfig['items_per_page']);
        $aIpList = $aResult['collection'];

        // Формируем постраничность
        $aPaging = $this->Viewer_MakePaging(
            $aResult['count'], $iPage, $this->aConfig['items_per_page'], 4,
            Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/users/banlist/' . $sMode
        );
        if ($aPaging) {
            $this->Viewer_Assign('aPaging', $aPaging);
        }
        $this->Viewer_Assign('aIpList', $aIpList);
        $this->Viewer_Assign('sMode', $sMode);
    }

    protected function EventUsersBanlistIds()
    {
        $sMode = 'ids';

        if (($sData = $this->Session_Get('adm_userlist_filter'))) {
            $aFilter = unserialize($sData);
        }
        else {
            $aFilter = array();
        }
        if (($sData = $this->Session_Get('adm_userlist_sort'))) {
            $aSort = unserialize($sData);
        } else {
            $aSort = array();
        }
        if (isset($aFilter['admin'])) unset($aFilter['admin']);

        $sUserIp = '*.*.*.*';
        $sUserRegDate = '';
        if ($this->getRequestCheck('adm_user_action') == 'adm_user_seek') {
            if (($sUserLogin = getRequest('user_login_seek'))) {
                if ($this->PluginAceadminpanel_Admin_GetUserId($sUserLogin)) {
                    $aFilter['login'] = $sUserLogin;
                }
                else {
                    $aFilter['like'] = $sUserLogin;
                }
            } else {
                $aFilter['login'] = $aFilter['like'] = null;
            }

            if (($s = getRequest('user_ip1_seek')) > '' AND is_numeric($s)) {
                $aUserIp[0] = intVal($s);
            } else {
                $aUserIp[0] = '*';
            }
            if (($s = getRequest('user_ip2_seek')) > '' AND is_numeric($s)) {
                $aUserIp[1] = intVal($s);
            } else {
                $aUserIp[1] = '*';
            }
            if (($s = getRequest('user_ip3_seek')) > '' AND is_numeric($s)) {
                $aUserIp[2] = intVal($s);
            } else {
                $aUserIp[2] = '*';
            }
            if (($s = getRequest('user_ip4_seek')) > '' AND is_numeric($s)) {
                $aUserIp[3] = intVal($s);
            } else {
                $aUserIp[3] = '*';
            }
            $sUserIp = $aUserIp[0] . '.' . $aUserIp[1] . '.' . $aUserIp[2] . '.' . $aUserIp[3];
            if ($sUserIp != '*.*.*.*') {
                if (preg_match('/\*\.\d/', $sUserIp)) {
                    $this->MessageError($this->Lang_Get('adm_err_wrong_ip'), 'users:list');
                } else {
                    $aFilter['ip'] = $sUserIp;
                }
            } else {
                $aFilter['ip'] = null;
            }

            if (($s = getRequest('user_regdate_seek'))) {
                if (preg_match('/(\d{4})(\-(\d{1,2})){0,1}(\-(\d{1,2})){0,1}/', $s, $aMatch)) {
                    if (isset($aMatch[1])) {
                        $sUserRegDate = $aMatch[1];
                        if (isset($aMatch[3])) {
                            $sUserRegDate .= '-' . sprintf('%02d', $aMatch[3]);
                            if (isset($aMatch[5])) {
                                $sUserRegDate .= '-' . sprintf('%02d', $aMatch[5]);
                            }
                        }
                    }
                }
                if ($sUserRegDate) {
                    $aFilter['regdate'] = $sUserRegDate;
                } else {
                    $aFilter['regdate'] = null;
                }
            }
            if (($s = getRequest('user_list_sort'))) {
                if (in_array($s, array('id', 'login', 'regdate', 'reg_ip', 'activated', 'last_date', 'last_ip'))) {
                    $aSort = array(); // так надо на будущее
                    $sUserListSort = $s;
                    $sUserListOrder = getRequest('user_list_order');
                    $aSort[$sUserListSort] = $sUserListOrder;
                }
            } else {
                $aSort = array();
            }
        }

        foreach ($aFilter as $key => $val) {
            if ($val === null) unset($aFilter[$key]);
        }
        $sUserListSort = $sUserListOrder = '';
        foreach ($aSort as $key => $val) {
            if ($val !== null) {
                $sUserListSort = $key;
                $sUserListOrder = $val;
            }
        }

        // Передан ли номер страницы
        if (preg_match("/^page(\d+)$/i", $this->getParam(2), $aMatch)) {
            $iPage = $aMatch[1];
        } else {
            $iPage = 1;
        }

        // Получаем список забаненных юзеров
        $iCount = 0;
        $aResult = $this->PluginAceadminpanel_Admin_GetBanList($iCount, $iPage, $this->aConfig['items_per_page'], $aFilter, $aSort);

        /*
        if (($iPage > 1) AND ($iPage > $aResult['count'] / $this->aConfig['items_per_page'])) {
            $iPage = ceil($aResult['count'] / $this->aConfig['items_per_page']);
            //$aResult=$this->PluginAceadminpanel_Admin_GetBanList($iCount, $iPage, $this->aConfig['items_per_page'], $aFilter, $aSort);
            $aResult=$this->PluginAceadminpanel_Admin_GetBanList($iCount, $iPage, 3, $aFilter, $aSort);
        }
         * 
         */
        $aUserList = $aResult['collection'];

        // Формируем постраничность
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4, Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/users/banlist/' . $sMode);
        if ($aPaging) {
            $this->Viewer_Assign('aPaging', $aPaging);
        }

        if (isset($aFilter['login']) AND $aFilter['login']) $sUserLoginSeek = $aFilter['login'];
        elseif (isset($aFilter['like']) AND $aFilter['like']) $sUserLoginSeek = $aFilter['like'];
        else $sUserLoginSeek = '';

        if (isset($aFilter['ip']) AND $aFilter['ip']) $sUserIp = $aFilter['ip'];
        $aUserIp = explode('.', $sUserIp);

        $this->Viewer_Assign('aUserList', $aUserList);
        $this->Viewer_Assign('sMode', $sMode);
        $this->Viewer_Assign('sUserLoginSeek', $sUserLoginSeek);
        $this->Viewer_Assign('sUserListSort', $sUserListSort);
        $this->Viewer_Assign('sUserListOrder', $sUserListOrder);
        $this->Viewer_Assign('aUserIp', $aUserIp);
        $this->Viewer_Assign('aFilter', $aFilter);
        $this->Viewer_Assign('aSort', $aSort);
        $this->Viewer_Assign('USER_USE_ACTIVATION', Config::Get('general.reg.activation'));

        $this->Viewer_Assign('sMode', $sMode);
    }

    protected function EventUsersBanlist()
    {

        // Передан ли номер страницы
        if (preg_match("/^page(\d+)$/i", $this->getParam(0), $aMatch)) {
            $iPage = $aMatch[1];
        } else {
            $iPage = 1;
        }

        $sMode = $this->GetParam(1);
        if ($sMode == 'ips') {
            // Получаем список забаненных ip-адресов
            $result = $this->EventUsersBanlistIps();
        } else {
            $sMode = 'ids';

            // Получаем список забаненных юзеров
            $result = $this->EventUsersBanlistIds();
        }
        $this->Viewer_Assign('sMode', $sMode);
        $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/users_banlist.tpl');
        return $result;
    }


    // Список инвайтов
    protected function EventUsersInvites()
    {
        if ($this->GetParam(1) == 'new') {
            $sMode = 'new';
        }
        else {
            $sMode = 'list';
        }

        $sInviteMode = $this->getRequestCheck('adm_invite_mode');
        if (!$sInviteMode) $sInviteMode = 'mail';
        $iInviteCount = 0 + intVal(getRequest('invite_count'));
        $aNewInviteList = array();
        $sInviteOrder = getRequest('invite_order');
        $sInviteSort = getRequest('invite_sort');

        if ($this->getRequestCheck('adm_invite_submit')) {
            if ($sInviteMode == 'text') {
                if ($iInviteCount <= 0) {
                    $this->MessageError($this->Lang_Get('adm_invaite_text_empty'));
                } else {
                    for ($i = 0; $i < $iInviteCount; $i++) {
                        $oInvite = $this->User_GenerateInvite($this->oUserCurrent);
                        $aNewInviteList[$i + 1] = $oInvite->GetCode();
                    }
                    $this->MessageNotice($this->Lang_Get('adm_invaite_text_done', array('num' => $iInviteCount)));
                }
            } else {
                $sEmails = str_replace("\n", ' ', getRequest('invite_mail'));
                $sEmails = str_replace(';', ' ', $sEmails);
                $sEmails = str_replace(',', ' ', $sEmails);
                $sEmails = preg_replace('/\s{2,}/', ' ', $sEmails);
                $aEmails = explode(' ', $sEmails);
                $iInviteCount = 0;
                foreach ($aEmails as $sEmail) {
                    if ($sEmail) {
                        if (func_check($sEmail, 'mail')) {
                            $oInvite = $this->User_GenerateInvite($this->oUserCurrent);
                            $this->Notify_SendInvite($this->oUserCurrent, $sEmail, $oInvite);
                            $aNewInviteList[$sEmail] = $oInvite->GetCode();
                            $iInviteCount += 1;
                        } else {
                            $aNewInviteList[$sEmail] = '### ' . $this->Lang_Get('settings_invite_mail_error') . ' ###';
                        }
                    }
                }
                if ($iInviteCount) {
                    $this->MessageNotice($this->Lang_Get('adm_invaite_mail_done', array('num' => $iInviteCount)));
                }
            }
        }
        if ($sMode == 'list') {
            // Передан ли номер страницы
            if (preg_match("/^page(\d+)$/i", $this->getParam(1), $aMatch)) {
                $iPage = $aMatch[1];
            } else {
                $iPage = 1;
            }

            $aParam = array();
            if ($sInviteSort AND
                in_array($sInviteSort, array('id', 'code', 'user_from', 'date_add', 'user_to', 'date_used'))
            ) {
                $aParam['sort'] = $sInviteSort;
            }
            if ($sInviteOrder) $aParam['order'] = intVal($sInviteOrder);
            // Получаем список инвайтов
            $iCount = 0;
            $aResult = $this->PluginAceadminpanel_Admin_GetInvites($iCount, $iPage, $this->aConfig['items_per_page'], $aParam);
            $aInvites = $aResult['collection'];

            // Формируем постраничность
            $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, $this->aConfig['items_per_page'], 4, Config::Get('path.root.web') . '/' . ROUTE_PAGE_ADMIN . '/users/invites');
            if ($aPaging) {
                $this->Viewer_Assign('aPaging', $aPaging);
            }
            $this->Viewer_Assign('aInvites', $aInvites);
            $this->Viewer_Assign('iCount', $aResult['count']);
        }
        $this->Viewer_Assign('sMode', $sMode);

        if ($this->oUserCurrent->isAdministrator()) {
            $iCountInviteAvailable = -1;
        } else {
            $iCountInviteAvailable = $this->User_GetCountInviteAvailable($this->oUserCurrent);
        }
        $this->Viewer_Assign('iCountInviteAvailable', $iCountInviteAvailable);
        $this->Viewer_Assign('iCountInviteUsed', $this->User_GetCountInviteUsed($this->oUserCurrent->getId()));
        $this->Viewer_Assign('sInviteMode', $sInviteMode);
        $this->Viewer_Assign('iInviteCount', $iInviteCount);
        $this->Viewer_Assign('USER_USE_INVITE', Config::Get('general.reg.invite'));
        $this->Viewer_Assign('aNewInviteList', $aNewInviteList);
        $this->Viewer_Assign('sInviteOrder', getRequest('invite_order'));
        $this->Viewer_Assign('sInviteSort', getRequest('invite_sort'));

        $this->Viewer_Assign('include_tpl', Plugin::GetTemplatePath($this->sPlugin) . '/actions/ActionAdmin/users_invites.tpl');
    }

    protected function EventUsersDelete($sUserLogin = null)
    {
        $this->Security_ValidateSendForm();

        if (!$sUserLogin) $sUserLogin = getRequest('adm_del_login');
        if ($sUserLogin == $this->oUserCurrent->GetLogin()) {
            $this->MessageError($this->Lang_Get('adm_cannot_del_self'), 'users:delete');
            return false;
        }
        if ($sUserLogin AND ($oUser = $this->PluginAceadminpanel_Admin_GetUserByLogin($sUserLogin))) {
            if (mb_strtolower($sUserLogin) == 'admin') {
                $this->MessageError($this->Lang_Get('adm_cannot_with_admin'), 'users:delete');
            } elseif ($oUser->IsAdministrator()) {
                $this->MessageError($this->Lang_Get('adm_cannot_del_admin'), 'users:delete');
            } elseif (!getRequest('adm_user_del_confirm') AND !getRequest('adm_bulk_confirm')) {
                $this->MessageError($this->Lang_Get('adm_cannot_del_confirm'), 'users:delete');
            } else {
                $this->PluginAceadminpanel_Admin_DelUser($oUser->GetId());
                $this->MessageNotice($this->Lang_Get('adm_user_deleted', Array('user' => $sUserLogin ? $sUserLogin
                                                                               : '')), 'users:delete');
            }
        } else {
            $this->MessageError($this->Lang_Get('adm_user_not_found', Array('user' => $sUserLogin ? $sUserLogin
                                                                            : '')), 'users:delete');
        }
        return true;
    }

    /*
  * URL: admin/users
  * ==================================================================================== */

    public function EventShutdown()
    {
        $this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        $this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
        $this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        $this->Viewer_Assign('sMenuNavItemSelect', $this->sMenuNavItemSelect);

        $this->Viewer_Assign('aModConfig', $this->aConfig);
        $this->Viewer_Assign('DIR_PLUGIN_SKIN', Plugin::GetTemplatePath($this->sPlugin));
        //$sWebPluginSkin=admPath2Url(Plugin::GetTemplatePath($this->sPlugin));
        $sWebPluginSkin = Config::Get('path.admin.skin') . '/';
        $this->Viewer_Assign('sWebPluginPath', Config::Get('path.root.web') . '/plugins/' . $this->sPlugin);
        $this->Viewer_Assign('sWebPluginSkin', $sWebPluginSkin);

        if (Config::Get('plugin.avalogs.admin_enable') AND $this->oLogs AND $this->aLogsMsg) {
            $str = '';
            foreach ($this->aLogsMsg as $key => $val) {
                if ($key) $str .= str_repeat(' ', 20);
                $str .= $val;
                if ($key < sizeof($this->aLogsMsg) - 1) $str .= "\n";
            }
            $this->oLogs->Out('admin', $str);
        }

        //$aParams = array('dir' => HelperPlugin::GetTemplatePath());
        //$aParams = array('plugin' => $this->sPlugin);
        foreach ($this->aBlocks as $sGroup => $aGroupBlocks) {
            //$this->Viewer_AddBlocks($sGroup, $aGroupBlocks);
            /* */
            $this->Viewer_ClearBlocks($sGroup);
            foreach ($aGroupBlocks as $aBlock) {
                if ($aBlock['params']) {
                    $aParams = $aBlock['params'];
                } else {
                    $aParams = array('plugin' => $this->sPlugin);
                }
                $this->Viewer_AddBlock($sGroup, $aBlock['block'], $aParams, isset($aParams['priority'])
                                                      ? $aParams['priority'] : null);
            }
            /* */
        }

        if ($this->aConfig['check_password'] AND
            !$this->PluginAceadminpanel_Admin_IsPasswordQuality($this->oUserCurrent)
        ) {
            $this->Message_AddError($this->Lang_Get('adm_password_quality'));
        }
        $this->MakeMenu();
        $this->Viewer_Assign('sTemplatePath', HelperPlugin::GetTemplatePath());
        $this->Viewer_Assign('sTemplatePathAction', HelperPlugin::GetTemplateActionPath());
        $this->Viewer_Assign('aPluginInfo', $this->aPluginInfo);
        $this->Viewer_Assign('sPageRef', $this->sPageRef);
        $this->Viewer_Assign('LS_VERSION', LS_VERSION);

        $this->Hook_AddExecFunction('template_body_begin', array($this, '_CssUrls'));
    }

    public function _cssUrls()
    {
        $sContent = '';
        $sWebPluginSkin = admPath2Url(Plugin::GetTemplatePath($this->sPlugin));
        $sFile = Plugin::GetTemplatePath($this->sPlugin) . 'css/admin-url.css';
        if (file_exists($sFile)) {
            $sContent = file_get_contents($sFile);
            if ($sContent) {
                $sContent = preg_replace('|/\*.+\*/|iusU', '', $sContent);
                $sContent = str_replace('background-image: url(', 'background-image: url(' . $sWebPluginSkin . 'images/', $sContent);
                $sContent = '<style type="text/css">' . $sContent . '</style>';
            }
        }
        return $sContent;
    }

    protected function _callAdminAddon($aAddon, $aArgs)
    {
        if (!is_array($aAddon)) {
            if (!isset($this->aAddons[$aAddon]))
                return;
            $aAddon = $this->aAddons[$aAddon];
        }
        $sFileName = $aAddon['file'];
        $sClassName = $aAddon['class'];

        if (isset($aAddon['template']) AND $aAddon['template']) $sTemplate = $aAddon['template'];
        else $sTemplate = '';

        if (isset($aAddon['language']) AND $aAddon['language']) $sLangFile = $aAddon['language'];
        else $sLangFile = '';

        include_once $sFileName;

        $oEventClass = new $sClassName($this->oEngine, $this->sCurrentAction);

        // * load template
        if ($sTemplate) $this->Viewer_Assign('tpl_include', $sTemplate);

        // * load css
        //$sCssFile = HelperPlugin::GetTemplatePath('css/admin_site_settings.css');
        //$this->Viewer_AppendStyle($sCssFile);

        // * load language texts
        if ($sLangFile) {
            $this->Lang_LoadFile($sLangFile);
        }

        $oEventClass->SetAdminAction($this);
        $oEventClass->Init();
        $result = call_user_func_array(array($oEventClass, 'Event'), $aArgs);
        $oEventClass->EventShutdown();
        $oEventClass->Done();
        return $result;
    }

    protected function _checkAdminAddon($sAddon)
    {
        $sAddonId = strtolower($sAddon);
        if (isset($this->aAddons[$sAddonId]))
            return $sAddonId;
        if ($this->bAddonsAutoCheck) {
            $sFile = HelperPlugin::GetPluginPath() . '/classes/actions/ActionAdmin' . $sAddon . '.class.php';
            if (file_exists($sFile)) {
                $sTemplate = HelperPlugin::GetTemplatePath('admin_' . admStrUnderScore($sAddon) . '.tpl');
                $this->aAddons[$sAddonId] = array(
                    'file' => $sFile,
                    'class' => 'PluginAceadminpanel_Admin' . $sAddon,
                    'template' => (file_exists($sTemplate) ? $sTemplate : ''),
                );
                return $sAddonId;
            }
        }
        return false;
    }

    public function  __call($sName, $aArgs)
    {
        if (preg_match('/^Event([A-Z]\w+)/', $sName, $matches)) {
            $sAddonId = $this->_CheckAdminAddon($matches[1]);
            if (isset($this->aAddons[$sAddonId])) {
                return $this->_CallAdminAddon($sAddonId, $aArgs);
            } elseif ($this->bAddonsAutoCheck) {

            }
        }
        elseif (preg_match('/^Plugin_/', $sName)) {
            $sName = 'PluginAceadminpanel_' . $sName;
        }
        return Engine::getInstance()->_CallModule($sName, $aArgs);
    }
}

// EOF