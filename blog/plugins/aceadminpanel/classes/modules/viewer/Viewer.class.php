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
 * @File Name: Viewer.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

if (!class_exists('ModuleViewer')) {
    require_once(Config::Get('path.root.engine') . '/modules/viewer/Viewer.class.php');
}

/**
 * Расширение (перекрытие) стандартного модуля Viwer
 */
class PluginAceadminpanel_ModuleViewer extends PluginAceadminpanel_Inherit_ModuleViewer
{
    private $sPlugin = 'aceadminpanel';

    protected $bAddPluginDirs = false;

    /**
     * Вспомогательная функция для сортировки блоков
     *
     * @param   array   $a
     * @param   array   $b
     *
     * @return  int
     */
    protected function _CompareBlocks($a, $b)
    {
        if ($a["priority"] == $b["priority"]) {
            return ($a["index"] < $b["index"]) ? -1 : 1;
        } elseif ($a['priority'] === 'top') {
            return -1;
        } elseif ($b['priority'] === 'top') {
            return 1;
        }
        return ($a["priority"] > $b["priority"]) ? -1 : 1;
    }

    /**
     * Сортировка блоков по приоритету
     *
     * @return void
     */
    protected function _SortBlocks()
    {
        foreach ($this->aBlocks as $sGroup => $aBlocks) {
            // вводим дополнительный параметр сортировке,
            // иначе блоки с одинаковыми приоритетами сортируются неверно
            foreach ($aBlocks as $nIndex => $aBlock) {
                $aBlocks[$nIndex]['index'] = $nIndex;
            }
            uasort($aBlocks, array($this, '_CompareBlocks'));
            $this->aBlocks[$sGroup] = $aBlocks;
        }
    }

    /**
     * Определение вызвавшего плагина по стеку вызовов
     *
     * @return string
     */
    protected function _getCallerPlugin()
    {
        $aStack = debug_backtrace();
        foreach ($aStack as $aCaller) {
            if (isset($aCaller['class'])) {
                if ($aCaller['class'] != get_class() AND preg_match('/^Plugin([A-Z][a-z0-9]+)_[a-zA-Z0-9_]+$/', $aCaller['class'], $aMatches)) {
                    return strtolower($aMatches[1]);
                }
            }
        }
        return '';
    }

    /**
     * Определение (и корректировка) реального пути к шаблону
     * с учетом подмены путей в админке и возможного учета js-lib
     *
     * @param $sTemplate
     *
     * @return string
     */
    protected function _getRealTeplate($sTemplate)
    {
        $sRealTemplate = '';
        $sTemplate = admFilePath($sTemplate, '/');
        $sPathRoot = admFilePath(Config::Get('path.root.server'), '/');

        // На формирование шаблонов через "Plugin::GetTemplatePath(__CLASS__)" мы повлиять не можем
        // Поэтому ищем пуговицу
        if (Config::Get($this->sPlugin . '.saved.view.skin')) {
            if (strpos($sTemplate, $sPathRoot) !== 0 AND !is_file($sTemplate) AND ($sPlugin = $this->_getCallerPlugin())) {
                $sRealTemplate = $sPathRoot . '/plugins/' . $sPlugin . '/templates/skin/'
                    . Config::Get($this->sPlugin . '.saved.view.skin') . '/' . $sTemplate;
            }
            elseif (preg_match('|^' . $sPathRoot . '/plugins/(\w+)/templates/skin/default/(.*)$|', $sTemplate, $aMatches)) {
                // если дефолтный шаблон плагина, то проверим, нет ли шаблона в подмененном скине
                $sRealTemplate = admFilePath(
                    Config::Get('path.root.server') . '/plugins/' . $aMatches[1] . '/templates/skin/'
                        . Config::Get($this->sPlugin . '.saved.view.skin') . '/' . $aMatches[2]);
                // если нет, то смотрим с учетом js-lib
                if (!is_file($sRealTemplate)) {
                    $sRealTemplate = admFilePath(
                        Config::Get('path.root.server') . '/plugins/' . $aMatches[1] . '/templates/skin/'
                            . 'default-' . Config::Get('js.lib') . '/' . $aMatches[2]);
                }
            }
        }
        elseif (Config::Get('js.lib')) {
            if (strpos($sTemplate, $sPathRoot) !== 0 AND !is_file($sTemplate) AND ($sPlugin = $this->_getCallerPlugin())) {
                $sRealTemplate = $sPathRoot . '/plugins/' . $sPlugin . '/templates/skin/'
                    . 'default-' . Config::Get('js.lib') . '/' . $sTemplate;
            }
            elseif (preg_match('|^' . $sPathRoot . '/plugins/(\w+)/templates/skin/default/(.*)$|', $sTemplate, $aMatches)) {
                // если дефолтный шаблон плагина, то проверим, нет ли дефолтного шаблона с учетом js-lib
                $sRealTemplate = admFilePath(
                    Config::Get('path.root.server') . '/plugins/' . $aMatches[1] . '/templates/skin/'
                        . 'default-' . Config::Get('js.lib') . '/' . $aMatches[2]);
            }
        }
        if ($sRealTemplate AND is_file($sRealTemplate)) {
            $sTemplate = $sRealTemplate;
        }
        return $sTemplate;
    }

    protected function _JsUniq($sJs, $aParams)
    {
        if ((in_array($sJs, $this->aJsInclude['append']) OR in_array($sJs, $this->aJsInclude['prepend']))
            AND $this->aFilesParams['js'][$sJs] === $aParams
        ) return true;
        else return false;
    }

    protected function _CssUniq($sCss, $aParams)
    {
        if ((in_array($sCss, $this->aCssInclude['append']) OR in_array($sCss, $this->aCssInclude['prepend']))
            AND $this->aFilesParams['css'][$sCss] === $aParams
        ) return true;
        else return false;
    }

    /**
     * Инициализация вьюера
     *
     * @param bool $bLocal
     *
     * @return void
     */
    public function Init($bLocal = false)
    {
        parent::Init($bLocal);
        if (Config::Get($this->sPlugin . '.saved.path.smarty.template')) {
            $this->AddTemplateDir(Config::Get($this->sPlugin . '.saved.path.smarty.template'));
        }
        $this->oSmarty->addPluginsDir(dirname(__FILE__) . '/plugs');
        $this->oSmarty->default_template_handler_func = array($this, 'TemplateHandler');
    }

    public function TemplateHandler($sTemplateType, $sTemplateName, &$sContent, &$sModified, $oSmartyTemplate)
    {
        $sTemplateFile = $this->_getRealTeplate($sTemplateName);
        if ((!$sTemplateFile OR $sTemplateFile == $sTemplateName)
            AND $oSmartyTemplate->parent AND $oSmartyTemplate->parent->template_filepath
                AND !in_array(dirname($oSmartyTemplate->parent->template_filepath), array('.', '..'))
        ) {
            $sTemplateFile = dirname($oSmartyTemplate->parent->template_filepath) . '/' . $sTemplateName;
            if (!is_file($sTemplateFile) AND preg_match('|(.+)/actions/[\w+]|', $oSmartyTemplate->parent->template_filepath, $aMatches)) {
                $sTemplateFile = $aMatches[1] . '/' . $sTemplateName;
            }
        }
        if (is_file($sTemplateFile))
            return $sTemplateFile;
        else
            return false;
    }

    public function AppendScript($sJs, $aParams = array())
    {
        if (!in_array($sJs, $this->aJsInclude['append']) OR $this->aFilesParams['js'][$sJs] !== $aParams)
            return parent::AppendScript($sJs, $aParams);
    }

    public function PrependScript($sJs, $aParams = array())
    {
        if (!in_array($sJs, $this->aJsInclude['prepend']) OR $this->aFilesParams['js'][$sJs] !== $aParams)
            return parent::PrependScript($sJs, $aParams);
    }

    public function AppendStyle($sCss, $aParams = array())
    {
        if (!in_array($sCss, $this->aCssInclude['append']) OR $this->aFilesParams['css'][$sCss] !== $aParams)
            return parent::AppendStyle($sCss, $aParams);
    }

    public function PrependStyle($sCss, $aParams = array())
    {
        if (!in_array($sCss, $this->aCssInclude['prepend']) OR $this->aFilesParams['css'][$sCss] !== $aParams)
            return parent::PrependStyle($sCss, $aParams);
    }

    public function GetLocalViewer()
    {
        $sViewerClass = get_class();
        $oViewerLocal = new $sViewerClass(Engine::getInstance());
        $oViewerLocal->Init(true);
        $oViewerLocal->VarAssign();
        $oViewerLocal->Assign('aLang', $this->Lang_GetLangMsg());

        return $oViewerLocal;
    }

    public function AssignArray($sVarName, $aValue)
    {
        $this->oSmarty->append($sVarName, (array)$aValue, true);
    }

    public function VarAssign()
    {
        $this->_SortBlocks();
        parent::VarAssign();

        $aPlugins = $this->Plugin_GetActivePlugins();
        $plugins = array();
        foreach ($aPlugins as $sPlugin) {
            $plugins[$sPlugin] = array(
                'skin' => array(
                    'name' => HelperPlugin::GetPluginSkin($sPlugin),
                    'path' => HelperPlugin::GetPluginSkinPath($sPlugin),
                    'url' => HelperPlugin::GetPluginSkinUrl($sPlugin),
                ),
                'config' => Config::Get('plugin.' . $sPlugin)
            );
        }
        $ls = array(
            'site' => array(
                'skin' => array(
                    'name' => Config::Get($this->sPlugin . '.saved.view.skin')
                        ? Config::Get($this->sPlugin . '.saved.view.skin')
                        : Config::Get('view.skin'),
                    'path' => Config::Get($this->sPlugin . '.saved.path.smarty.template')
                        ? Config::Get($this->sPlugin . '.saved.path.smarty.template')
                        : Config::Get('path.smarty.template'),
                    'url' => Config::Get($this->sPlugin . '.saved.path.static.skin')
                        ? Config::Get($this->sPlugin . '.saved.path.static.skin')
                        : Config::Get('path.static.skin'),
                ),
            ),
            'js' => array(
                'lib' => Config::Get('js.lib'),
                'jquery' => Config::Get('js.jquery'),
                'mootools' => Config::Get('js.mootools'),
            ),
            'router' => array(
                'action' => Router::GetAction(),
                'event' => Router::GetActionEvent(),
                'param' => Router::GetParams(),
            ),
            'url' => $this->oSmarty->getTemplateVars('aRouter'),
            'plugin' => $plugins,
        );
        $this->AssignArray('ls', $ls);
    }

    /**
     * Добавить путь к шаблонам Smarty
     *
     * @param   array|string    $aTemplateDirs
     * @param   bool            $bFirst
     *
     * @return  void
     */
    public function AddTemplateDir($aTemplateDirs, $bFirst = false)
    {
        if ($bFirst) {
            if (!is_array($this->oSmarty->template_dir)) $this->oSmarty->template_dir = array();
            if (!is_array($aTemplateDirs)) {
                $aTemplateDirs = array((string)$aTemplateDirs);
            } else {
                $aTemplateDirs = array_reverse($aTemplateDirs);
            }
            foreach ($aTemplateDirs as $sDir)
                array_unshift($this->oSmarty->template_dir, $sDir);
        } else {
            $this->oSmarty->addTemplateDir($aTemplateDirs);
        }
    }

    public function GetTemplateDir()
    {
        return $this->oSmarty->getTemplateDir();
    }

    public function Display($sTemplate)
    {
        // ajax-запросы нас не интересуют ?
        if (!$this->sResponseAjax) {
            if ($sTemplate) {
                $sTemplate = admFilePath($this->Plugin_GetDelegate('template', $sTemplate), '/');
                if (!$this->TemplateExists($sTemplate)) {
                    if (dirname($sTemplate) == '.') {
                        if (strpos($sClass = Router::GetActionClass(), 'Plugin') === 0) {
                            $sTemplate = HelperPlugin::GetPluginSkinPath($sClass) . 'actions/Action' . ucfirst(Router::GetAction()) . '/' . $sTemplate;
                        }
                    }
                    $sTemplate = $this->_getRealTeplate($sTemplate);
                }
                $sPathRoot = admFilePath(Config::Get('path.root.server'), '/');
                if ($this->bAddPluginDirs AND (strpos($sTemplate, $sPathRoot) === 0) AND is_file($sTemplate)) {
                    // добавляем пути к шаблонам
                    $sPath = dirname($sTemplate);
                    if ($sPath AND $sPath != '.') {
                        $this->AddTemplateDir($sPath, true);
                        if (basename(dirname($sPath)) == 'actions') {
                            $this->AddTemplateDir(dirname(dirname($sPath)), true);
                        }
                    }
                }
            }
        }
        return parent::Display($sTemplate);
    }

    public function Fetch($sTemplate)
    {
        if (Config::Get($this->sPlugin . '.saved.view.skin')) {
            $sTemplate = $this->_getRealTeplate($sTemplate);
        }
        return parent::Fetch($sTemplate);
    }

    public function GetSmartyVersion()
    {
        $sSmartyVersion = null;
        if (property_exists($this->oSmarty, '_version')) {
            $sSmartyVersion = $this->oSmarty->_version;
        }
        return $sSmartyVersion;
    }

    public function AddBlock($sGroup, $sName, $aParams = array(), $iPriority = 5)
    {
        /**
         * Если не указана директория шаблона, но указана приналежность к плагину,
         * то "вычисляем" правильную директорию
         */
        if (!isset($aParams['dir']) and isset($aParams['plugin'])) {
            $aParams['dir'] = HelperPlugin::GetTemplatePath('', $aParams['plugin']);
        }
        return parent::AddBlock($sGroup, $sName, $aParams, $iPriority);
    }

}
// EOF