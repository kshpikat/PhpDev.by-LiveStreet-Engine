<h3>{$oLang->settings_menu_invite} {if $sMode=='list'}<span>({$iCount})</span>{/if}</h3>

<ul class="block-nav -box">
    <li {if $sMode=='list' || $sMode==''}class="active"{/if}><strong></strong><a href="{router page='admin'}users/invites/">list</a></li>
    <li {if $sMode=='new'}class="active"{/if}><a href="{router page='admin'}users/invites/new/">new</a><em></em></li>
</ul>

{if $sMode == 'new'}
  {include file="$sTemplatePathAction/users_invites_new.tpl"}
{else}
  {include file="$sTemplatePathAction/users_invites_list.tpl"}
{/if}
