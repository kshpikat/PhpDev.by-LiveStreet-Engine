{literal}
<script type="text/javascript">
function AdminSort(sort, order) {
  var i, el;
  if (document.getElementById) {
    if ((el=document.getElementById('user_list_sort')) )el.value=sort;
    if ((el=document.getElementById('user_list_order'))) el.value=order;
    if ((el=document.getElementById('admin_form_seek'))) el.submit();
  }
}
</script>
{/literal}

{if $aUserList}
{include file='paging.tpl'}
<table width="100%" cellspacing="0" class="admin-table">
    <tr style="font-weight:bold;">
        <th>
            {if $sUserListSort=='id'}
            <a href="#" onclick="AdminSort('id', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> id </b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('id', 1); return false;"> id </a>
            {/if}
        </th>

        <th>
            {if $sUserListSort=='login'}
            <a href="#" onclick="AdminSort('login', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> {$oLang->user}</b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('login', 1); return false;"> {$oLang->user} </a>
            {/if}
        </th>

        <th>
            {if $sUserListSort=='regdate'}
            <a href="#" onclick="AdminSort('regdate', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_users_date_reg} </b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('regdate', 1); return false;"> {$oLang->adm_users_date_reg} </a>
            {/if}
        </th>

        <th>
            {if $sUserListSort=='reg_ip'}
            <a href="#" onclick="AdminSort('reg_ip', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_users_ip_reg} </b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('reg_ip', 1); return false;"> {$oLang->adm_users_ip_reg} </a>
            {/if}
        </th>

	{if $oConfig->GetValue('general.reg.activation')}
        <th>
            {if $sUserListSort=='activated'}
            <a href="#" onclick="AdminSort('activated', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_users_activated} </b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('activated', 1); return false;"> {$oLang->adm_users_activated} </a>
            {/if}
        </th>
	{/if}

        <th>
            {if $sUserListSort=='last_date'}
            <a href="#" onclick="AdminSort('last_date', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_users_last_activity} </b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('last_date', 1); return false;"> {$oLang->adm_users_last_activity} </a>
            {/if}
        </th>

        <th>
            {if $sUserListSort=='last_ip'}
            <a href="#" onclick="AdminSort('last_ip', {if $sUserListOrder==1}2{else}1{/if}); return false;"><b> Last IP </b></a>
            <b>{if $sUserListOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('last_ip', 1); return false;"> Last IP </a>
            {/if}
        </th>

        <th>{$oLang->adm_ban_upto}</th>
        <th>{$oLang->adm_ban_comment}</th>
    </tr>

    {foreach from=$aUserList item=oUser name=el2}
    {if $smarty.foreach.el2.iteration % 2  == 0}
     	{assign var=className value=''}
    {else}
     	{assign var=className value='colored'}
    {/if}

    {assign var="oSession" value=$oUser->getSession()}

    <tr class="{$className}">
        <td style="text-align:right;"> {$oUser->getId()} &nbsp;</td>
        <td>
            <div class="{if !$oUser->getDateActivate()}adm_noactive{elseif $oUser->IsBannedByLogin()}adm_banned{elseif $oUser->isAdministrator()}adm_admin{else}adm_user{/if}" style="background-image: url({$sWebPluginSkin}/images/adm_icons.png)"></div>
            <a href="{router page='admin'}users/profile/{$oUser->getLogin()}/" class="link">{$oUser->getLogin()}</a>
        </td>
        <td style="text-align:center;">{$oUser->getDateRegister()}</td>
        <td style="text-align:center;">{$oUser->getIpRegister()}</td>
        {if $oConfig->GetValue('general.reg.activation')}
        <td style="text-align:center;">{$oUser->getDateActivate()}</td>
        {/if}
        <td style="text-align:center;">
            {if $oSession}{$oSession->getDateLast()}{else}&nbsp;{/if}
        </td>
        <td style="text-align:center;">
            {if $oSession}{$oSession->getIpLast()}{else}&nbsp;{/if}
        </td>
        <td style="text-align:center;">
            {if $oUser->isBanned()}{if $oUser->GetBanLine()}{$oUser->GetBanLine()}{else}unlim{/if}{/if}
        </td>
        <td style="text-align:center;">{$oUser->getBanComment()}</td>
    </tr>
    {/foreach}
</table>
{include file='paging.tpl'}
{else}
    {$oLang->user_empty}
{/if}
