<div class="page people">

    <h3>{$oLang->adm_menu_users_banlist}</h3>

    <ul class="block-nav -box">
        <li {if $sMode=='ids'}class="active"{/if}><strong></strong><a href="{router page='admin'}users/banlist/ids/">{$oLang->adm_banlist_ids}</a></li>
        <li {if $sMode=='ips'}class="active"{/if}><a href="{router page='admin'}users/banlist/ips/">{$oLang->adm_banlist_ips}</a><em></em></li>
    </ul>

    {if $sMode=='ips'}
        {include file="$sTemplatePathAction/users_banlist_ips.tpl"}
    {else}
        {include file="$sTemplatePathAction/users_banlist_ids.tpl"}
    {/if}
</div>
