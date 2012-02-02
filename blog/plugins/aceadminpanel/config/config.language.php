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
 * @File Name: config.language.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

$config['lang_define'] = 'russian,english';

// Время (количество дней), в течение которого будет сохраняться
// выбранный язык. Если 0, то язык сохраняется только на время
// текущей сессии
$config['lang_save_period'] = 365;

Config::Set('router.page.language', 'PluginAceadminpanel_ActionLanguage');

return $config;

// EOF