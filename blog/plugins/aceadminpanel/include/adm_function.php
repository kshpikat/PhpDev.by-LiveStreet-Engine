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
 * @File Name: adm_function.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

function admHeaderLocation($sLocation)
{
    Engine::getInstance()->Shutdown();

    if (!headers_sent()) {
        //        func_header_location($sLocation);
        //        header("HTTP/1.1 301 Moved Permanently");
        header('HTTP/1.1 303 See Other');
        header('Location: ' . $sLocation, true);
        header('Content-type: text/html; charset=UTF-8');
    }
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<script language="JavaScript1.1" type="text/javascript">
<!--
location.replace("' . $sLocation . '");
//-->
</script>
<noscript>
<meta http-equiv="Refresh" content="0; URL=' . $sLocation . '">
</noscript>
</head>
<body>
Redirect to <a href="' . $sLocation . '">' . $sLocation . '</a>
</body>
</html>';
    exit;
}

/**
 * Получает требуемый элемент из стека вызова
 * Если $nLevel == -1, то ищется первое совпадение
 *
 * @param int $nLevel
 * @param string $sElement
 * @param string $sMatch
 * @return null
 */
function admBacktrace($nLevel = 0, $sElement = '', $sMatch = '')
{
    $aBacktrace = debug_backtrace();
    if ($nLevel < 0 AND $sElement) {
        foreach ($aBacktrace as $aCaller) {
            if (isset($aCaller[$sElement])) {
                if (($sMatch == '') OR ($sMatch > '' AND 0 === strpos($aCaller[$sElement], $sMatch)))
                    return $aCaller[$sElement];
            }
        }
        return null;
    } else {
        if (!isset($aBacktrace[++$nLevel])) return null;
        if ($sElement) {
            if (isset($aBacktrace[$nLevel][$sElement])) {
                return $aBacktrace[$nLevel][$sElement];
            } else {
                return null;
            }
        } else {
            return $aBacktrace[$nLevel];
        }
    }
}

/**
 * Преобразование пути на сервере в урл
 *
 * @param  string $sPath
 *
 * @return string
 */
function admPath2Url($sPath)
{
    return str_replace(
        str_replace(DIRECTORY_SEPARATOR, '/', Config::Get('path.root.server')),
        Config::Get('path.root.web'),
        str_replace(DIRECTORY_SEPARATOR, '/', $sPath)
    );
}

/**
 * Преобразование урл в путь на сервере
 *
 * @param   string          $sUrl
 * @param   string|null     $sSeparator
 *
 * @return  string
 */
function admUrl2Path($sUrl, $sSeparator = null)
{
    // * Delete www from path
    $sUrl = str_replace('//www.', '//', $sUrl);
    $sPathWeb = str_replace('//www.', '//', Config::Get('path.root.web'));
    // * do replace
    $sUrl = str_replace($sPathWeb, Config::Get('path.root.server'), $sUrl);
    return admFilePath($sUrl, $sSeparator);

}

/**
 * Нормализует путь к файлу на сервере
 *
 * @param   string      $sPath
 * @param   string|null    $sSeparator
 *
 * @return  string
 */
function admFilePath($sPath, $sSeparator = null)
{
    if (!$sSeparator) $sSeparator = DIRECTORY_SEPARATOR;

    if ($sSeparator == '/') {
        $sPath = str_replace('\\', $sSeparator, $sPath);
    } elseif ($sSeparator == '\\') {
        $sPath = str_replace('/', $sSeparator, $sPath);
    } else {
        $sPath = str_replace(array('/', '\\'), $sSeparator, $sPath);
    }

    $sPath = str_replace($sSeparator . $sSeparator, $sSeparator, $sPath);
    return $sPath;
}

function admLocalPath($sPath, $sRoot)
{
    $sPath = admFilePath($sPath);
    $sRoot = admFilePath($sRoot);
    if (strpos($sPath, $sRoot) === 0) {
        $sPath = substr($sPath, strlen($sRoot));
    }
    return $sPath;
}

function admLocalDir($sPath)
{
    return admLocalPath($sPath, Config::Get('path.root.server'));
}

function admLocalUrl($sPath)
{
    return admLocalPath($sPath, Config::Get('path.root.web'));
}

/**
 * Преобразует строку в массив
 *
 * @param   string|array    $sStr
 * @param   string          $sChr
 *
 * @return  array
 */
function admStr2Array($sStr, $sChr = ',')
{
    if (is_array($sStr)) $result = $sStr;
    else $result = explode($sChr, str_replace(' ', '', $sStr));
    return $result;
}

function admMakeDir($sNewDir)
{
    $sBasePath = admFilePath(Config::Get('path.root.server'));
    if (substr($sNewDir, 0, 2) == '//') {
        $sNewDir = substr($sNewDir, 2);
    } else {
        $sNewDir = admLocalPath($sNewDir, $sBasePath);
    }
    $sTempPath = $sBasePath;
    $aNewDir = explode('/', $sNewDir);
    foreach ($aNewDir as $sDir) {
        if ($sDir != '.' AND $sDir != '') {
            if (!is_dir($sTempPath . $sDir . '/')) {
                if (@mkdir($sTempPath . $sDir . '/')) {
                    //;
                } else {
                    //Exception('Cannot make dir "'.$sTempPath.$sDir.'/'.'"');
                    return false;
                }
                @chmod($sTempPath . $sDir . '/', 0755);
            }
            $sTempPath = $sTempPath . $sDir . '/';
        }
    }
    return $sTempPath;
}

function admRemoveDir($sDir)
{
    if (!is_dir($sDir)) return true;
    $sPath = rtrim($sDir, '/') . '/';

    if (($aFiles = glob($sPath . '*', GLOB_MARK))) {
        foreach ($aFiles as $sFile) {
            if (is_dir($sFile)) {
                admRemoveDir($sFile);
            } else {
                @unlink($sFile);
            }
        }
    }
    if (is_dir($sPath)) @rmdir($sPath);
}

function admClearDir($sDir, $bRecursive = true)
{
    $result = true;
    $sDir = str_replace('\\', '/', $sDir);
    if (substr($sDir, -1) != '/') $sDir .= '/';
    if (is_dir($sDir) AND ($files = glob($sDir . '*'))) {
        foreach ($files as $file) {
            // delete all files except started with 'dot'
            if (substr(basename($file), 0, 1) != '.') {
                if (is_dir($file) AND $bRecursive) $result = $result AND admClearDir($file, $bRecursive);
                else $result = $result AND @unlink($file);
            }
        }
    }
    return $result;
}

/**
 * Чистит временные файлы Smarty - кеш и скомпилированные файлы
 *
 * @return  bool
 */
function admClearSmartyCache()
{
    $result = admClearDir(Config::Get('path.smarty.compiled'));
    $result = $result AND admClearDir(Config::Get('path.smarty.cache'));
    return $result;
}

function admClearHeadfilesCache()
{
    $sCacheDir = Config::Get('path.smarty.cache') . "/" . Config::Get('view.skin');
    $result = admClearDir($sCacheDir);
    return $result;
}

function admClearAllCache()
{
    Engine::getInstance()->Cache_Clean();
    $result = admClearSmartyCache() AND admClearHeadfilesCache();
    return $result;
}

function admGetAllUserIp()
{
    $aIp[] = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $aIp[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    if (isset($_SERVER['HTTP_X_REAL_IP'])) $aIp[] = $_SERVER['HTTP_X_REAL_IP'];
    if (isset($_SERVER['HTTP_VIA'])) {
        if (preg_match('/\d+\.\d+\.\d+\.\d+/', $_SERVER['HTTP_VIA'], $m)) $aIp[] = $m[0];
    }
    return $aIp;
}

function admGetMajorVersion($sVersion)
{
    return str_replace(',', '.', '' . floatVal($sVersion));
}

function admMSIE6()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) AND strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0'))
        return true;
    else
        return false;
}

/**
 * Определение (и сохранение в куках на год) уникального ID посетителя сайта
 *
 * @return string
 */
function admGetVisitorId()
{
    if (!defined('ADM_VISITOR_ID')) {
        if (!isset($_COOKIE['visitor_id'])) {
            if (headers_sent()) {
                if (!isset($_SERVER['HTTP_USER_AGENT'])) {
                    // это точно не браузер
                    $sVisitorId = '';
                } else {
                    $sUserAgent = @$_SERVER['HTTP_USER_AGENT'];
                    $sVisitorId = md5($sUserAgent . '::' . serialize(admGetAllUserIp()));
                }
            } else {
                $sVisitorId = md5(uniqid(time()));
            }
        } else {
            $sVisitorId = $_COOKIE['visitor_id'];
        }
        if (!headers_sent()) {
            setcookie('visitor_id', $sVisitorId, time() + 60 * 60 * 24 * 365, Config::Get('sys.cookie.path'), Config::Get('sys.cookie.host'));
        }
        define('ADM_VISITOR_ID', $sVisitorId);
    }
    return ADM_VISITOR_ID;
}

function admSize($n)
{
    $unim = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $c = 0;
    while ($n >= 1024) {
        $c++;
        $n = $n / 1024;
    }
    return number_format($n, ($c ? 2 : 0), ',', '.') . '&nbsp;' . $unim[$c];
}

/**
 * Нормализует ассоциативные массивы, т.е. приводит массивы вида {'aaa', 'bbb'=>'ccc'}
 * к виду {'aaa'=> NULL, 'bbb'=>'ccc'}
 *
 * @param  $aArray
 *
 * @return array
 */
function admArrayAssoc($aArray)
{
    if (!is_array($aArray)) {
        $aArray = array($aArray);
    }
    $result = array();
    foreach ($aArray as $key => $val) {
        if (is_numeric($key) AND !is_array($val) AND $val) {
            $result[(string)$val] = NULL;
        } else {
            $result[$key] = $val;
        }
    }
    return $result;
}

/**
 * Transform from CamelCase to under_score
 *
 * @param  string $sStr
 *
 * @return string
 */
function admStrUnderScore($sStr)
{
    $sStr[0] = strtolower($sStr[0]);
    $func = create_function('$c', 'return "_" . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $sStr);
}

/**
 * Transform from under_score to CamelCase
 *
 * @param  $sStr
 * @param bool $bCapitaliseFirstChar
 * @return string
 */
function admStrCamelCase($sStr, $bCapitaliseFirstChar = false)
{
    if ($bCapitaliseFirstChar) {
        $sStr[0] = strtoupper($sStr[0]);
    }
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/', $func, $sStr);
}

function admSkinExists($sSkin)
{
    return true;
}

if (!defined('ADM_VISITOR_ID')) {
    admGetVisitorId();
}


// EOF