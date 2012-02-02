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
 * @File Name: ActionAdminSiteConfig.class.php
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *----------------------------------------------------------------------------
 */

class PluginAceadminpanel_AdminSiteConfig extends AceAdminPlugin
{
    protected $aStyles = array(
        'boolean' => '<span style="color:#75507b">$@</span>',
        'integer' => '<span style="color:#4e9a06">$@</span>',
        'double' => '<span style="">$@</span>',
        'string' => '<span style="color:#cc0000">\'$@\'</span>',
        'array' => '<span style="">$@</span>',
        'object' => '<span style="">$@</span>',
        'resource' => '<span style="">$@</span>',
        'NULL' => '<span style="color:#3465a4">$@</span>',
        'unknown type' => '<span style="">$@</span>',
    );

    public function Event()
    {
        $aConfig = Config::Get();
        $sOldKey = '';
        foreach ($aConfig as $sKey=>$xValue) {
            if ($sOldKey AND $sOldKey != $sKey) $this->_outItem();
            $this->_out($sKey, $xValue);
            $sOldKey = $sKey;
        }
    }

    protected function _out($sKey, $xValue, $nLevel=0)
    {
        if ($nLevel==0) $sKey = '<b>' . $sKey . '</b>';
        if (!is_array($xValue)) {
            $this->_outItem($sKey, $xValue);
        } else {
            foreach ($xValue as $sSubKey=>$sValue) {
                $this->_out($sKey . '.' . $sSubKey, $sValue, $nLevel+1);
            }
        }
    }

    protected function _outItem($sKey=null, $sValue=null)
    {
        if ($sKey) {
            echo $sKey;
            echo ' =&gt; ';
            echo $this->_outValue($sValue);
        }
        echo '<br/>' . "\n";
    }

    protected function _outValue($xValue)
    {
        $sType = gettype($xValue);
        if ($sType == 'boolean') {
            $xValue = $xValue?'true':'false';
        } elseif ($sType == 'NULL') {
            $xValue = 'null';
        }
        if (isset($this->aStyles[$sType])) {
            $sResult = str_replace('$@', $xValue, $this->aStyles[$sType]);
        } else {
            $sResult = $xValue;
        }
        return $sResult;
    }
}

// EOF