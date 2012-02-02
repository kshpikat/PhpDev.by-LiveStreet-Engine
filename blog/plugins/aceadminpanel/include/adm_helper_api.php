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
 * @File Name: adm_helper_api.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

if (!class_exists('Engine')) {
    if (!defined('LS_VERSION') && !defined('SYS_HACKER_CONSOLE')) {
        define('ADM_HELPER_API', 1);
    }

    if (!defined('LS_VERSION')) define('LS_VERSION', '0.4');
    if (!defined('SYS_HACKER_CONSOLE')) define('SYS_HACKER_CONSOLE', false);

    $sDirRoot = dirname(dirname(dirname(dirname(__FILE__))));
    set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

    require_once($sDirRoot.'/config/loader.php');
    require_once(Config::Get('path.root.engine').'/classes/Engine.class.php');
}

class HelperApi extends Object {
    static $oDb;
    static $oEngine;
    static $aData;
    static $aFunc;

    static function Enabled($sFunc=null) {
        $bOk = false;
        if (!is_array(self::$aFunc)) self::Init();
        if ($sFunc) {
            $n = strpos($sFunc, '::');
            if ($n) {
                $sFunc = strtolower(substr($sFunc, $n+2));
                $bOk = in_array(strtolower($sFunc), self::$aFunc);
            }
        }
        else $bOk = sizeof(self::$aFunc) > 0;
        return $bOk;
    }

    static function Init() {
        if (($aFunc = Config::Get('plugin.aceadminpanel.api'))) {
            self::$aFunc = get_class_methods(get_class());
            if (is_array($aFunc)) {
                self::$aFunc = array_intersect(self::$aFunc, $aFunc);
            }
            foreach (self::$aFunc as $key=>$val) self::$aFunc[$key] = strtolower($val);
        } else {
            self::$aFunc = array();
        }

        self::$oEngine = Engine::getInstance();
        self::$oDb = self::$oEngine->Database_GetConnect();
        self::LoadData();
    }

    static function LoadData() {
        if (!self::$aFunc) return null;

        $sUserId = self::$oEngine->Session_Get('user_id');
        $sql =
                "SELECT
                s.*, u.user_login, ua.user_id AS is_admin
                FROM
                    ".Config::Get('db.table.session')." AS s
                    INNER JOIN ".Config::Get('db.table.user')." AS u ON s.user_id = u.user_id
                    LEFT JOIN ".Config::Get('db.table.user_administrator')." AS ua ON s.user_id = ua.user_id
                WHERE
                    s.user_id = ?";
        self::$aData = self::$oDb->selectRow($sql, $sUserId);
    }
    /**
     * Залогинен ли пользователь
     *
     * @return <type>
     */
    static function IsUser() {
        if (!self::Enabled(__METHOD__)) return null;

        return (self::$aData && isset(self::$aData['user_id']));
    }

    /**
     * Залогинен ли пользователь и является ли он администратором
     *
     * @return <type>
     */
    static function IsAdministrator() {
        if (!self::Enabled(__METHOD__)) return null;

        if (self::IsUser() && isset(self::$aData['is_admin'])) {
            return self::$aData['is_admin']?true:false;
        }
        return false;
    }

    /**
     * Возвращает параметр конфигурации
     *
     * @param <type> $sKey
     * @return <type>
     */
    static function GetConfig($sKey=null) {
        if (!self::Enabled(__METHOD__)) return null;

        if (self::IsAdministrator()) return Config::Get($sKey);
    }

    /**
     * Если пользователь залогинен, то возвращает его логин
     *
     * @return <type>
     */
    static function GetUserLogin() {
        if (!self::Enabled(__METHOD__)) return null;

        if (self::IsUser() && isset(self::$aData['user_login'])) {
            return self::$aData['user_login'];
        }
        return false;
    }

    /**
     * Если пользователь залогинен, то возвращает его ID
     *
     * @return <type>
     */
    static function GetUserId() {
        if (!self::Enabled(__METHOD__)) return null;

        if (self::IsUser() && isset(self::$aData['user_id'])) {
            return self::$aData['user_id'];
        }
        return false;
    }

    /**
     * Авторизация пользователя
     *
     * @param <type> $sUserLogin
     * @param <type> $sUserPassword
     */
    static function Login($sUserLogin, $sUserPassword) {
        if (!self::Enabled(__METHOD__)) return null;

        if ($sUserLogin) {
            $oUser=self::$oEngine->User_GetUserByLogin($sUserLogin);
            if ($oUser && $oUser->getPassword()==func_encrypt($sUserPassword) && $oUser->getActivate()) {
                self::$oEngine->User_Authorization($oUser, true);
                self::LoadData();
            }
        }
        return (self::GetUserLogin() == $sUserLogin);
    }

    /**
     * Разлогирование пользователя
     */
    static function Logout() {
        if (!self::Enabled(__METHOD__)) return null;

        self::$oEngine->User_Logout();
        self::LoadData();
    }
}

if (defined('ADM_HELPER_API')) {
    HelperApi::Init();
}

// EOF