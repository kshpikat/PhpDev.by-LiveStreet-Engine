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
 * Модуль Loginza авторизации
 *
 */
class PluginLoginza_ModuleLoginza extends Module {

	protected $Loginza = null;
	protected $Mapper;

	/**
	 * Инициализация модуля
	 */
	public function Init() {
		/**
		 * Подключаем маппер
		 */		
		$this->Mapper = Engine::GetMapper(__CLASS__);
		/**
		 * Подключаем необходимые библиотеки
		 */
		require_once(Plugin::GetPath(__CLASS__).'classes/lib/LoginzaAPI.class.php');
		require_once(Plugin::GetPath(__CLASS__).'classes/lib/LoginzaUserProfile.class.php');
		/**
		 * Создаем объект OpenID с файловым хранилищем
		 */
		$this->Loginza = new LoginzaAPI(Config::Get('plugin.loginza.loginza.id'), Config::Get('plugin.loginza.loginza.skey'));
	}
	
	public function getAuthProfile ($token) {
		// получаем данные об авторизации
		return  $this->Loginza->getAuthInfo($token);
	}
	/**
	 * Получаем данные связки по loginza identity
	 * @param string $identity
	 */
	public function getUserByIdentity ($identity) {
		return $this->Mapper->getUserByIdentity($identity);
	}
	/**
	 * Создание связки
	 * @param PluginLoginza_ModuleLoginza_EntityLoginza $LoginzaIdentity
	 */
	public function addIdentity (PluginLoginza_ModuleLoginza_EntityLoginza $LoginzaIdentity) {
		return $this->Mapper->addIdentity($LoginzaIdentity);
	}
	public function getUserIdentities ($user_id) {
		return $this->Mapper->getUserIdentities($user_id);
	}
	public function deleteIdentity ($identity) {
		return $this->Mapper->deleteIdentity($identity);
	}
	/**
	 * Генерируем из профиля login
	 * @param unknown_type $profile
	 */
	public function genLogin ($profile) {
		// генерация логина
		$LoginzaUserProfile = new LoginzaUserProfile($profile);
		
		return $LoginzaUserProfile->genNickname();
	}
	public function genFullName ($profile) {
		$LoginzaUserProfile = new LoginzaUserProfile($profile);
		
		return $LoginzaUserProfile->genFullName($profile);
	}
	public function genUserSite ($profile) {
		$LoginzaUserProfile = new LoginzaUserProfile($profile);
		
		return $LoginzaUserProfile->genUserSite($profile);
	}
}
?>