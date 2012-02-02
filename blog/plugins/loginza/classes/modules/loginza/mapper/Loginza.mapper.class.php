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

/**
 * Маппер, обрабатывает запросы к БД
 *
 */
class PluginLoginza_ModuleLoginza_MapperLoginza extends Mapper {	
/**
	 * Получает identity связку
	 *
	 * @param unknown_type $sOpenId
	 * @return unknown
	 */
	public function getUserByIdentity ($identity) {
		$sql = "SELECT u.* FROM ".Config::Get('plugin.loginza.table.identities')." as l, ".Config::Get('db.table.user')." as u WHERE identity = ? and u.user_id=l.user_id ";
		if ($row = $this->oDb->selectRow($sql, $identity)) {
			return Engine::GetEntity('User',$row);
		}
		return null;
	}
	
	/**
	 * Создает связку identity Loginza
	 *
	 * @param PluginLoginza_ModuleLoginza_EntityLoginza $LoginzaIdentity
	 * @return unknown
	 */
	public function addIdentity (PluginLoginza_ModuleLoginza_EntityLoginza $LoginzaIdentity) {
		$sql = "INSERT INTO ".Config::Get('plugin.loginza.table.identities')." SET ?a ";			
		if ($this->oDb->query($sql, $LoginzaIdentity->_getData())===0) {
			return true;
		}		
		return false;
	}
	/**
	 * Получение списка идентификаторов пользователя
	 * @param unknown_type $user_id
	 */
	public function getUserIdentities ($user_id) {
		$sql = "SELECT * FROM ".Config::Get('plugin.loginza.table.identities')." WHERE user_id = ? ";
		
		$identities = array();
		if ($rows = $this->oDb->select($sql, $user_id)) {
			foreach ($rows as $row) {
				$identities[] = Engine::GetEntity('PluginLoginza_Loginza', $row);
			}
		}
		return $identities;
	}
	/**
	 * Удаление идентификатора
	 * @param unknown_type $identity
	 */
	public function deleteIdentity ($identity) {
		$sql = "DELETE FROM ".Config::Get('plugin.loginza.table.identities')." WHERE `identity` = ? ";			
		return $this->oDb->query($sql, $identity);
	}
}
?>