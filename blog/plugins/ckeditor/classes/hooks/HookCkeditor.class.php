<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

// Запрещаем напрямую через браузер обращение к этому файлу.
if(!class_exists('Hook')) {
	die('Hacking attemp!');
}


/**
 * Регистрация хуков
 *
 */
class PluginCkeditor_HookCkeditor extends Hook {    

    public function RegisterHook() {    	
        /**
         * Хук для вставки HTML кода
         */
       	$sAttachTo = 'form_add_topic_topic_end';
				$this->AddHook('init_action', 'CkeditorHead', __CLASS__);
    }

    /**
     * Подключаем CSS и JS
     *
     */
    public function CkeditorHead() {

			$sConfigLib = '';
			$bForbid = false;

    	if (!Config::Get('plugin.ckeditor.javascript_lib')) {
				$sConfigLib = 'mootools';
  
    	} else {
      	$sConfigLib = strtolower(Config::Get('plugin.ckeditor.javascript_lib'));
    	}

    	if (Config::Get('plugin.ckeditor.replace_forbidden')) {
    		$oConf = Config::Get('plugin.ckeditor.replace_forbidden');
    		if (isset($oConf[Router::GetAction()]) && isset($oConf[Router::GetAction()][Router::GetActionEvent()])) {
					$bForbid = true;
    		}
    	}

    	if (!$bForbid) {
		  	if ($sConfigLib == 'jquery') {
		
			    $sScript = Plugin::GetTemplateWebPath(__CLASS__).'js/'.'jquery.removePanel.js';
  	  	  $this->Viewer_AppendScript($sScript, array("merge"=>false));
	  		} elseif ($sConfigLib == 'mootools') {

			    $sScript = Plugin::GetTemplateWebPath(__CLASS__).'js/'.'removePanel.js';
  		    $this->Viewer_AppendScript($sScript, array("merge"=>false));
	  		}

  	    $sScript = Plugin::GetTemplateWebPath(__CLASS__).'js/'.'ckeditor/ckeditor.js';
    	  $this->Viewer_AppendScript($sScript, array("merge"=>false));

	      $sScript = Plugin::GetTemplateWebPath(__CLASS__).'js/'.'ckeditor/config.js';
  	    $this->Viewer_AppendScript($sScript, array("merge"=>false));

		  	if ($sConfigLib == 'jquery') {
		      $sScript = Plugin::GetTemplateWebPath(__CLASS__).'js/'.'ckeditor/adapters/jquery.js';
  		    $this->Viewer_AppendScript($sScript, array("merge"=>false));
	  		} elseif ($sConfigLib == 'mootools') {
		      $sScript = Plugin::GetTemplateWebPath(__CLASS__).'js/'.'ckeditor/adapters/mootools.js';
  		    $this->Viewer_AppendScript($sScript, array("merge"=>false));
	  		}

	  	}
  		
    }

}
?>