{literal}
<script type="text/javascript">
function AdminSort(sort, order) {
  $('invite_sort').value=sort;
  $('invite_order').value=order;
  $('admin_form_invite').submit();
}
</script>
{/literal}

{if $aInvites}
{include file='paging.tpl'}
<table width="100%" cellspacing="0" class="admin-table">
    <tr style="font-weight:bold;">
        <th>
            {if $sInviteSort=='id'}
            <a href="#" onclick="AdminSort('id', {if $sInviteOrder==1}2{else}1{/if}); return false;"><b> id </b></a>
            <b>{if $sInviteOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('id', 1); return false;"> id </a>
            {/if}
        </th>
        <th>
            {if $sInviteSort=='code'}
            <a href="#" onclick="AdminSort('code', {if $sInviteOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_invite_code}</b></a>
            <b>{if $sInviteOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('code', 1); return false;"> {$oLang->adm_invite_code} </a>
            {/if}
        </th>
        <th>
            {if $sInviteSort=='user_from'}
            <a href="#" onclick="AdminSort('user_from', {if $sInviteOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_invite_user_from} </b></a>
            <b>{if $sInviteOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('user_from', 1); return false;"> {$oLang->adm_invite_user_from} </a>
            {/if}
        </th>
        <th>
            {if $sInviteSort=='date_add'}
            <a href="#" onclick="AdminSort('date_add', {if $sInviteOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_invite_date_add} </b></a>
            <b>{if $sInviteOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('date_add', 1); return false;"> {$oLang->adm_invite_date_add} </a>
            {/if}
        </th>
        <th>
            {if $sInviteSort=='user_to'}
            <a href="#" onclick="AdminSort('user_to', {if $sInviteOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_invite_user_to} </b></a>
            <b>{if $sInviteOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('user_to', 1); return false;"> {$oLang->adm_invite_user_to} </a>
            {/if}
        </th>
        <th>
            {if $sInviteSort=='date_used'}
            <a href="#" onclick="AdminSort('date_used', {if $sInviteOrder==1}2{else}1{/if}); return false;"><b> {$oLang->adm_invite_date_used} </b></a>
            <b>{if $sInviteOrder==1}&darr;{else}&uarr;{/if}</b>
            {else}
            <a href="#" onclick="AdminSort('date_used', 1); return false;"> {$oLang->adm_invite_date_used} </a>
            {/if}
        </th>
    </tr>

    {foreach from=$aInvites item=aInvite name=el2}
    {if $smarty.foreach.el2.iteration % 2  == 0}
        {assign var=className value=''}
    {else}
        {assign var=className value='colored'}
    {/if}
    <tr class="{$className}" onmouseover="this.className='colored_sel';" onmouseout="this.className='{$className}';">
        <td class="right"> {$aInvite.invite_id} &nbsp;</td>
        <td> {$aInvite.invite_code} &nbsp;</td>
        <td>
            <a href="{router page='admin'}users/profile/{$aInvite.from_login}/" class="link">{$aInvite.from_login}</a>
        </td>
        <td class="center;">{$aInvite.invite_date_add}</td>
        <td>
            {if $aInvite.to_login}
            <a href="{router page='admin'}users/profile/{$aInvite.to_login}/" class="link">{$aInvite.to_login}</a>
            {else}
            &nbsp;
            {/if}
        </td>
        <td style="text-align:center;">
            {if $aInvite.invite_date_used}
            {$aInvite.invite_date_used}
            {else}
            &nbsp;
            {/if}
        </td>
    </tr>
    {/foreach}
</table>
<div style="display:none;">
    <form action="" id="admin_form_invite">
        <input type="hidden" name="invite_sort" id="invite_sort" />
        <input type="hidden" name="invite_order" id="invite_order" />
    </form>
</div>
{include file='paging.tpl'}
{else}
    {$oLang->user_empty}
{/if}
