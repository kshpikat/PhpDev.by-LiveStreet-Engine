{include file='header.tpl'}

<form action="" method="post" target="_blank" class="adm-report">
<div class="-box1">
{foreach $aCommonInfo as $sSectionKey=>$aSection}
    {if $aSection@iteration is odd}
    <div style="clear: left;">
    {/if}
    <div class="adm_info_section">
        <h3>{$aSection.label}</h3>
        <div class="topic">
        {foreach $aSection.data as $sKey=>$aItem}
            <p>
            {if ($aItem.label)}
                {$aItem.label}:
            {/if}
            <span class="adm_info_value">{$aItem.value}</span> {if ($aItem['.html'])}{$aItem['.html']}{/if}
            </p>
        {/foreach}
        <div class="adm_info_input">
            <input type="checkbox" id="adm_report_{$sSectionKey}" name="adm_report_{$sSectionKey}" checked="checked" />
            <label for="adm_report_{$sSectionKey}" >{$oLang->adm_button_checkin}</label>
        </div>
        </div>
    </div>
{if $aSection@iteration is even}
</div>
{/if}
{/foreach}
</div>

<div style="clear: left;"></div>
    
<div class="adm_info_button">
    <label>{$oLang->adm_button_report}</label>
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <input type="submit" id="butAdmReportTxt" name="report" value="TXT" />
    <input type="submit" id="butAdmReportXml" name="report" value="XML" />
</div>
</form>
{include file='footer.tpl'}