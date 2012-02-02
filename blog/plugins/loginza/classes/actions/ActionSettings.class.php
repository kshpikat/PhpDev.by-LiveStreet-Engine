<?php
/**
 * Обрабатывает настройки
 *
 */
class PluginLoginza_ActionSettings extends ActionPlugin {
	protected $oUserCurrent=null;
	
	/**
	 * Инициализация 
	 *
	 * @return null
	 */
	public function Init() {
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error'); 
		}
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->Viewer_Assign('menuSelected', true);
		$this->Viewer_Assign('sTemplateWebPathPlugin',Plugin::GetTemplateWebPath(__CLASS__));
	}
	
	protected function RegisterEvent() {
		$this->AddEventPreg('/^settings$/i','/^$/i','EventSettings');
		$this->AddEventPreg('/^settings$/i','/^delete$/i','/^$/i','EventLoginzaAjaxDelete');	
	}
	protected function EventSettings() {
		// если не передан токен (связывание)
		if ($token = getRequest('token',null,'post')) {
			// профиль пользователя
			$profile = $this->PluginLoginza_Loginza_getAuthProfile($token);
			
			// если не передан идентификатор
			if (empty($profile->identity)) {
				// ошибка
				$this->Message_AddError($this->Lang_Get('loginza_identity_is_empty'),$this->Lang_Get('error'));
			} else {
						
				// проверяем identity на наличие в БД
				$User = $this->PluginLoginza_Loginza_getUserByIdentity($profile->identity);
				
				if ($User) {
					// ошибка о существовании связки
					$this->Message_AddError($this->Lang_Get('loginza_identity_exists'),$this->Lang_Get('error'));
				} else {
					
					// связка с Loginza identity
					$Loginza = Engine::GetEntity('PluginLoginza_Loginza');
					$Loginza->setUserId($this->oUserCurrent->getId());
					$Loginza->setIdentity($profile->identity);
					
					// создание связки
					$this->PluginLoginza_Loginza_addIdentity($Loginza);
					
					// сообщение о успешной операции
					$this->Message_AddNoticeSingle($this->Lang_Get('loginza_identity_bind_ok'),$this->Lang_Get('attention'));	
				}
			}
		}
		
		// получаем список идентификаторов
		$identities = $this->PluginLoginza_Loginza_getUserIdentities($this->oUserCurrent->getUserId());
		
		$this->Viewer_Assign('loginzaIdentities', $identities);
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('loginza_settings');
	}
	
	protected function EventLoginzaAjaxDelete () {
		/**
		 * Устанавливаем тип ответа для Ajax
		 */
		$this->Viewer_SetResponseAjax('json');
		
		$identity = getRequest('identity',null,'post');
		
		if( !($oIdentity = $this->PluginLoginza_Loginza_getUserByIdentity($identity)) ) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		
		// если можно удалять
		if($oIdentity->getUserId() != $this->oUserCurrent->getId()) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;			
		}
		
		// если у пользователя нет почты
		if (!$this->oUserCurrent->getMail()) {
			// все идентификаторы пользователя
			$userIdentities = $this->PluginLoginza_Loginza_getUserIdentities($this->oUserCurrent->getUserId());
			
			// нельзя удалять последний идентификатор
			if (count($userIdentities) == 1) {
				$this->Message_AddErrorSingle($this->Lang_Get('loginza_identity_last_error'),$this->Lang_Get('error'));
				return;
			}
		}
		
		// удаление	
		$this->PluginLoginza_Loginza_deleteIdentity($identity);
		$this->Message_AddNoticeSingle($this->Lang_Get('loginza_identity_delete_ok'),$this->Lang_Get('attention'));		
	}
}
?>