<h3>{$oLang->adm_settings_title}</h3>
<ul class="block-nav -box">
    <li {if $sMenuNavItemSelect=='base'}class="active"{/if}><strong></strong><a href="{router page='admin'}site/settings/base/">{$oLang->adm_settings_base}</a></li>
    <li {if $sMenuNavItemSelect=='sys'}class="active"{/if}><a href="{router page='admin'}site/settings/sys/">{$oLang->adm_settings_sys}</a></li>
    <li {if $sMenuNavItemSelect=='acl'}class="active"{/if}><a href="{router page='admin'}site/settings/acl/">{$oLang->adm_settings_acl}</a><em></em></li>
</ul>

<div class=topic>

    <form action="" method="POST">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        {foreach from=$aFields key=sConfigKey item=aItem}
        <p>
            {if $aItem.type=='section'}
               {assign var="sTitle" value="$sConfigKey"}
                <div class="adm_section_head">
                    <input type="submit" name="submit_data_save" value="{$oLang->adm_save}" />
                    <h4 class="adm_section">{$oLang->Get("`$sTitle`")}</h4>
                </div>
            {else}
            {if $aItem.type=='checkbox'}
                <label for="{$sConfigKey}" {if ($aItem.value)}class="checked"{/if}>{$oLang->Get("$sConfigKey")}:</label>
                <input type="{$aItem.type}" id="{$sConfigKey}" name="{$sConfigKey}" value="1" class="{$aItem.class}" {if ($aItem.value)}checked{/if} /><br />
            {elseif $aItem.type=='select'}
                <label for="{$sConfigKey}">{$oLang->Get("$sConfigKey")}:</label>
                <select id="{$sConfigKey}" name="{$sConfigKey}" class="{$aItem.class}">
                {foreach from=$aItem.options item=sOption}
                <option value="{$sOption}" {if $sOption==$aItem.value}selected{/if}>{$sOption}</option>
                {/foreach}
                </select>
            {else}
                <label for="{$sConfigKey}">{$oLang->Get("$sConfigKey")}:</label>
                <input type="{$aItem.type}" id="{$sConfigKey}" name="{$sConfigKey}" value="{$aItem.value}" class="{$aItem.class}" /><br />
            {/if}
            <!-- span class="form_note">{$oLang->Get("adm_set_`$sConfigKey`_notice")}</span -->
            {/if}
        </p>
        {/foreach}

        <div class="adm_section_head">
            <input type="submit" name="submit_data_save" value="{$oLang->adm_save}" />&nbsp;
        </div>

    </form>

</div>
