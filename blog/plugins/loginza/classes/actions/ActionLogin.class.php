<?php
/**
 * Обрабатывает авторизацию
 *
 */
class PluginLoginza_ActionLogin extends ActionPlugin {		
	/**
	 * Инициализация 
	 *
	 * @return null
	 */
	public function Init() {
		/**
		 * Не пускаем авторизованных
		 */
		if ($this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('registration_is_authorization'), $this->Lang_Get('attention'));
			return Router::Action('error'); 
		}
		$this->Viewer_Assign('sTemplateWebPathPlugin',Plugin::GetTemplateWebPath(__CLASS__));
	}
	
	protected function RegisterEvent() {
		$this->AddEventPreg('/^login$/i','/^$/i','EventLogin');
		$this->AddEventPreg('/^login$/i','/^result$/i','/^$/i','EventLoginzaToken');
		$this->AddEventPreg('/^login$/i','/^reg$/i','/^$/i','EventLoginzaReg');
		$this->AddEventPreg('/^login$/i','/^bind$/i','/^$/i','EventLoginzaBind');
	}
	protected function EventLogin() {
		return Router::Action('login');
	}
	protected function EventLoginzaToken () {
		$token = getRequest('token',null,'post');
		
		// если не передан токен
		if (!$token) {
			// ошибка авторизации
			$this->Message_AddErrorSingle($this->Lang_Get('loginza_token_is_empty'));
			return Router::Action('error');
		}
		
		// профиль пользователя
		$profile = $this->PluginLoginza_Loginza_getAuthProfile($token);
		
		// если не передан идентификатор
		if (empty($profile->identity)) {
			// ошибка
			$this->Message_AddErrorSingle($this->Lang_Get('loginza_identity_is_empty'));
			return Router::Action('error');
		}
				
		// проверяем identity на наличие в БД
		$User = $this->PluginLoginza_Loginza_getUserByIdentity($profile->identity);
		
		if ($User) {
			// авторизуем
			$this->User_Authorization($User);
			Router::Location(Config::Get('path.root.web').'/');
		} else {
			$_SESSION['loginza']['profile'] = $profile;
			
			// генерация логина из профиля
			$this->Viewer_Assign('loginzaLogin', $this->PluginLoginza_Loginza_genLogin($profile));
			
			// если передан email
			if (@$profile->email) {
				// смотрим есть ли такой пользователь в БД
				if ($this->User_GetUserByMail($profile->email)) {
					// пользователь есть, сразу выбираем вкладку Сущ. пользователь
					$this->Viewer_Assign('emailUserExists', true);
				}
			}
		}
		
		// в шаблон
		$this->Viewer_Assign('loginza', $profile);

		
		// шаблон станицы подтверждения входа
		$this->SetTemplateAction('login_variantes');
	}
	/**
	 * Регистрация нового пользователя
	 */
	protected function EventLoginzaReg () {
		$this->SetTemplateAction('login_variantes');
		
		if (empty($_SESSION['loginza']['profile'])) {
			// ошибка
			$this->Message_AddErrorSingle($this->Lang_Get('loginza_identity_is_empty'));
			return Router::Action('error');
		}
		// флаг наличия ошибок
		$has_error = false;
		// Проверка логина
		if (!func_check(getRequest('login'),'login',3,30)) {
			$this->Message_AddError($this->Lang_Get('registration_login_error'),$this->Lang_Get('error'));
			$has_error = true;
		}
		// Проверка занятости логина
		if (!$has_error) {
			if ($this->User_GetUserByLogin(getRequest('login'))) {
				$this->Message_AddError($this->Lang_Get('registration_login_error_used'),$this->Lang_Get('error'));
				$has_error = true;
			}
		}
		// Проверка почты
		if ((Config::Get('plugin.loginza.mail_required') || (!Config::Get('plugin.loginza.mail_required') && 
			getRequest('email')) ) && !func_check(getRequest('email'), 'mail')
		) {
			$this->Message_AddError($this->Lang_Get('registration_mail_error'),$this->Lang_Get('error'));
			$has_error = true;
		}
		// Проверка занятости почты
		if (!$has_error) {
			if (getRequest('email') && $this->User_GetUserByMail(getRequest('email'))) {
				$this->Message_AddError($this->Lang_Get('registration_mail_error_used'),$this->Lang_Get('error'));
				$has_error = true;
			}
		}
		
		// если все впорядке
		if (!$has_error) {
			$User = Engine::GetEntity('User');
			$User->setLogin(getRequest('login'));
			
			$pass = '';
			if (getRequest('email')) {
				$pass = func_generator(7);
				$User->setMail(getRequest('email'));
				$User->setPassword(func_encrypt($pass));
			} else {
				$User->setMail(null);
				$User->setPassword($pass);
			}
			$User->setDateRegister(date("Y-m-d H:i:s"));
			$User->setIpRegister(func_getIp());
			$User->setActivate(1);
			$User->setActivateKey(null);
			
			// создаем пользователя в БД
			if ($this->User_Add($User)) {
				
				// письмо с паролем
				if ($User->getPassword()) {
					$this->Notify_SendRegistration($User, $pass);
				}
				
				// связка с Loginza identity
				$Loginza = Engine::GetEntity('PluginLoginza_Loginza');
				$Loginza->setUserId($User->getId());
				$Loginza->setIdentity($_SESSION['loginza']['profile']->identity);
				
				// создание связки
				$this->PluginLoginza_Loginza_addIdentity($Loginza);
				
				// Создание персонального блога
				$this->Blog_CreatePersonalBlog($User);
				
				// авторизация
				$this->User_Authorization($User, true);
				
				// заполнение профиля
				$User = $this->User_GetUserById($User->getId());
				$User->setProfileName( $this->PluginLoginza_Loginza_genFullName($_SESSION['loginza']['profile']) );	
				$User->setProfileSex ( (@$_SESSION['loginza']['profile']->gender == 'M' ? 'man' : (@$_SESSION['loginza']['profile']->gender == 'F') ? 'woman' : 'other'));
				$User->setProfileCountry (@$_SESSION['loginza']['profile']->address->home->country);
				$User->setProfileCity (@$_SESSION['loginza']['profile']->address->home->city);
				$User->setProfileSite( $this->PluginLoginza_Loginza_genUserSite($_SESSION['loginza']['profile']) );
				$User->setProfileAvatar(@$_SESSION['loginza']['profile']->photo);
				$User->setProfileBirthday((@$_SESSION['loginza']['profile']->dob ? $_SESSION['loginza']['profile']->dob : '0000-00-00').' 00:00:00');
				$User->setProfileDate(date("Y-m-d H:i:s"));
				// обновление профиля
				$this->User_Update($User);
				
				// удаляем временный массив
				unset($_SESSION['loginza']['profile']);
				
				Router::Location(Config::Get('path.root.web').'/');
			}
		}
	}
	/**
	 * Связывание соц. пользователя с учетной записью LiveStreet
	 */
	protected function EventLoginzaBind () {
		$this->SetTemplateAction('login_variantes');
		if (empty($_SESSION['loginza']['profile'])) {
			// ошибка
			$this->Message_AddErrorSingle($this->Lang_Get('loginza_identity_is_empty'));
			return Router::Action('error');
		}
		// флаг наличия ошибок
		$has_error = false;
		
		if (!getRequest('email') || !getRequest('password')) {
			// пароль или емайл пусты
			$this->Message_AddError($this->Lang_Get('loginza_empty_email_or_password'),$this->Lang_Get('error'));
			$has_error = true;
		}
		
		
		if (!$has_error) {
			// если передан email
			if (func_check(getRequest('email'),'mail')) {
				$User = $this->User_GetUserByMail(getRequest('email'));
			} else {
				$User = $this->User_GetUserByLogin(getRequest('email'));
			}
			
			if ($User) {
				// проверка правильности ввода пароля
				if ($User->getPassword() == func_encrypt(getRequest('password'))) {
					// связка с Loginza identity
					$Loginza = Engine::GetEntity('PluginLoginza_Loginza');
					$Loginza->setUserId($User->getId());
					$Loginza->setIdentity($_SESSION['loginza']['profile']->identity);
					
					// создание связки
					$this->PluginLoginza_Loginza_addIdentity($Loginza);
					
					unset($_SESSION['loginza']['profile']);
					
					// авторизация
					$this->User_Authorization($User, true);
					Router::Location(Config::Get('path.root.web').'/');
				}
			}
			
			// ошибка
			$this->Message_AddErrorSingle($this->Lang_Get('user_login_bad'));
			return Router::Action('error');
		}
	}
}
?>