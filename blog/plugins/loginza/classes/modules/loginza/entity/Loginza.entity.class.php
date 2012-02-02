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
 * Сущность связи c identity loginza
 *
 */
class PluginLoginza_ModuleLoginza_EntityLoginza extends Entity 
{    
    public function getProvider () {
    	$providers = array('google','yandex','mail','vkontakte','facebook','twitter',
    	'loginza','myopenid','webmoney','rambler','flickr','lastfm','openid','verisign','aol','steam');
    	$identity = $this->getIdentity();
    	
    	foreach ($providers as $provider) {
    		if (preg_match('/^https?:\/\/([^\.]+\.)?'.$provider.'(\.[^\.]+)*\//i', $identity)) {
    			return $provider;
    		}
    	}
    	
    	return false;
    }
}
?>