{if $aIpList}
{include file='paging.tpl'}
<table width="100%" cellspacing="0" class="admin-table">
    <tr style="font-weight:bold;">
        <th>&nbsp;</th>
        <th>IP</th>
        <th>{$oLang->adm_users_banned}</th>
        <th>{$oLang->adm_ban_upto}</th>
        <th>{$oLang->adm_ban_comment}</th>
        <th>&nbsp;</th>
    </tr>

    {foreach from=$aIpList item=aIp name=el2}
    {if $smarty.foreach.el2.iteration % 2  == 0}
       	{assign var=className value=''}
    {else}
       	{assign var=className value='colored'}
    {/if}
    <tr class="{$className}">
        <td class="number"> {$smarty.foreach.el2.iteration}&nbsp; </td>
        <td class="center">{$aIp.ip1} - {$aIp.ip2}</td>
        <td class="center">{$aIp.bandate}</td>
        <td class="center">{if $aIp.banunlim}unlim{else}{$aIp.banline}{/if}</td>
        <td class="center">{$aIp.bancomment}</td>
        <td class="center"><a href="{router page='admin'}users/banlist/ips/del/{$aIp.id}/">{$oLang->adm_exclude}</a></td>
    </tr>
    {/foreach}
</table>
{include file='paging.tpl'}
{else}
    {$oLang->user_empty}
{/if}
