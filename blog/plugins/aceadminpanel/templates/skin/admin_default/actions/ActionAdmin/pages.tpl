{include file='header.tpl'}

<h3>
{$oLang->adm_pages}
{if $aParams.0=='new'}
    &rarr; {$oLang->adm_pages_new}
    {elseif $aParams.0=='edit'}
    &rarr; {$oLang->page_edit} «{$oPageEdit->getTitle()}»
    {elseif $aParams.0=='options'}
    &rarr; {$oLang->adm_pages_options}
{/if}
</h3>
<div class="topic">

{if $include_tpl}
{include file="$include_tpl"}
{else}

    <table class="admin-table">
        <tr>
            <th>{$aLang.page_admin_action}</th>
            <th>ID</th>
            <th>{$oLang->page_admin_title}</th>
            <th style="width:250px;">{$oLang->page_admin_url}</th>
            <th>{$oLang->page_admin_main}</th>
            <th></th>
        </tr>


        {foreach from=$aPages item=oPage name=el2}
            {if $smarty.foreach.el2.iteration % 2  == 0}
                {assign var=className value=''}
                {else}
                {assign var=className value='colored'}
            {/if}
            <tr class="{$className}" onmouseover="this.className='colored_sel';"
                onmouseout="this.className='{$className}';">
                <td align="center">
                    <a href="{router page='admin'}pages/edit/{$oPage->getId()}/"><img
                            src="{$sWebPluginSkin}images/edit.gif" alt="{$oLang->page_admin_action_edit}"
                            title="{$oLang->page_admin_action_edit}"/></a>
                    &nbsp;
                    <a href="{router page='admin'}pages/delete/?page_id={$oPage->getId()}&security_ls_key={$LIVESTREET_SECURITY_KEY}"
                       onclick="return confirm('«{$oPage->getTitle()}»: {$oLang->page_admin_action_delete_confirm}');"><img
                            src="{$sWebPluginSkin}images/delete.gif" alt="{$oLang->page_admin_action_delete}"
                            title="{$oLang->page_admin_action_delete}"/></a>
                    {if $smarty.foreach.el2.first}
                        <img src="{$sWebPluginSkin}images/up_no.png" alt="{$aLang.page_admin_sort_up}"
                             title="{$aLang.page_admin_sort_up} ({$oPage->getSort()})"/>
                    {else}
                        <a href="{router page='admin'}pages/sort/{$oPage->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><img
                                src="{$sWebPluginSkin}images/up.png" alt="{$aLang.page_admin_sort_up}"
                                title="{$aLang.page_admin_sort_up} ({$oPage->getSort()})"/></a>
                    {/if}
                    {if $smarty.foreach.el2.last}
                        <img src="{$sWebPluginSkin}images/down_no.png" alt="{$aLang.page_admin_sort_down}"
                             title="{$aLang.page_admin_sort_down} ({$oPage->getSort()})"/>
                        {else}
                        <a href="{router page='admin'}pages/sort/{$oPage->getId()}/down/?security_ls_key={$LIVESTREET_SECURITY_KEY}"><img
                                src="{$sWebPluginSkin}images/down.png" alt="{$aLang.page_admin_sort_down}"
                                title="{$aLang.page_admin_sort_down} ({$oPage->getSort()})"/></a>
                    {/if}
                </td>
                <td class="number">
                    {$oPage->getId()}
                </td>
                <td class="name">
                    <div class="{if $oPage->getActive()}active{else}unactive{/if}"></div>
                    <img src="{cfg name='path.static.skin'}/images/{if $oPage->getLevel()==0}folder{else}new{/if}_16x16.gif"
                         alt="" title="" style="margin-left: {$oPage->getLevel()*20}px;"/>
                    {if $oPage->getActive()}<a
                            href="{router page='page'}{$oPage->getUrlFull()}/">{/if}{$oPage->getTitle()}{if $oPage->getActive()}</a>{/if}
                </td>
                <td>
                    /{$oPage->getUrlFull()}/
                </td>
                <td class="center">
                    {if $oPage->getMain()}
                    {$aLang.page_admin_active_yes}
                {else}
                    {$aLang.page_admin_active_no}
                {/if}
                </td>
                <td class="{if $oPage->getActive()}deactivate{else}activate{/if}">
                    <strong>
                        {if $oPage->getActive()}
                            <a href="{router page='admin'}pages/?page_id={$oPage->getId()}&action=deactivate&security_ls_key={$LIVESTREET_SECURITY_KEY}">{$aLang.adm_act_deactivate}</a>
                            {else}
                            <a href="{router page='admin'}pages/?page_id={$oPage->getId()}&action=activate&security_ls_key={$LIVESTREET_SECURITY_KEY}">{$aLang.adm_act_activate}</a>
                        {/if}
                    </strong>
                </td>
            </tr>
        {/foreach}

    </table>
{/if}


</div>

{include file='footer.tpl'}