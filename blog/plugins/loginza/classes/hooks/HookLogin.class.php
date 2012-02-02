<?php
/**
 * Регистрация хуков
 *
 */
class PluginLoginza_HookLogin extends Hook {    

    public function RegisterHook() {
    	/**
    	 * Хук на инициализацию экшенов
    	 */
        $this->AddHook('init_action', 'InitAction', __CLASS__);
        /**
         * Хук на всплывающее окно авторизации
         */
        $this->AddHook('template_form_login_popup_begin', 'LoginTpl', __CLASS__);
         /**
         * Хук на страницу авторизации
         */
        $this->AddHook('template_form_login_begin', 'LoginTpl', __CLASS__);
        /**
         * Хук на страницу регистрации
         */
        $this->AddHook('template_form_registration_begin', 'LoginTpl', __CLASS__);
        /**
         * Хук на меню настроек пользователя
         */
        $this->AddHook('template_menu_settings', 'MenuSettingsTpl', __CLASS__);
    }

    /**
     * Отлавливаем нужные экшены и перенаправляем на экшены плагина
     *
     */
    public function InitAction() {    	
    	/**
		 * Обработка URL вида /login/loginza/
		 */
    	if (Router::GetAction()=='login' and Router::GetActionEvent()=='loginza') {
    		Router::Action('loginza_login', 'login');
    	}
    	/**
		 * Обработка URL вида /settings/loginza/
		 */
    	if (Router::GetAction()=='settings' and Router::GetActionEvent()=='loginza') {
    		Router::Action('loginza_settings', 'settings');
    	}
    }
    /**
     * Вставляем форму Loginza
     *
     * @return unknown
     */
    public function LoginTpl() {    	
		$this->Viewer_Assign('sTemplateWebPathPlugin',Plugin::GetTemplateWebPath(__CLASS__));
    	return $this->Viewer_Fetch(Plugin::GetTemplatePath('loginza').'loginza_login.tpl');
    }
	/**
     * Добавляем в меню настроек новый пункт
     *
     * @return unknown
     */
    public function MenuSettingsTpl() {    	
    	return $this->Viewer_Fetch(Plugin::GetTemplatePath('loginza').'settings_menu.tpl');
    }
}
?>