<?php
/*-------------------------------------------------------
*
*   Loginza
*   Author: Sergey Arsenichev
*   Contact e-mail: sergey@arsenichev.ru
*   ICQ: 465745
*
*--------------------------------------------------------
*
*   Official site: http://loginza.ru
*   Contact e-mail: sergey@loginza.ru
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

if (!class_exists('Plugin')) {
	die('Hacking attemp!');
}
/**
 * Plugin Loginza
 *
 */
class PluginLoginza extends Plugin {
	/**
	 * Активация плагина
	 */
	public function Activate() {
		if (!$this->isTableExists('prefix_loginza_identities')) {
			/**
			 * При активации выполняем SQL дамп
			 */
			$this->ExportSQL(dirname(__FILE__).'/dump.sql');
		}
		return true;
	}
	
	/**
	 * Инициализация плагина
	 */
	public function Init() {
		
	}
	/**
	 * Проверяет наличие таблицы в БД
	 *
	 * @param unknown_type $sTableName
	 * @return unknown
	 */
	protected function isTableExists($sTableName) {
		$sTableName = str_replace('prefix_', Config::Get('db.table.prefix'), $sTableName);
		$sQuery="SHOW TABLES LIKE '{$sTableName}'";
		if ($aRows=$this->Database_GetConnect()->select($sQuery)) {
			return true;
		}
		return false;
	}
}
?>