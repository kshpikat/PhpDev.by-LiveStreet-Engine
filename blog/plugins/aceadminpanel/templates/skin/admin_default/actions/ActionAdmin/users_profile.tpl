<div class="profile-user">
    <ul class="block-nav -box">
        <li {if $sMode=='info'}class="active"{/if}><strong></strong><a href="{router page='admin'}users/profile/{$oUserProfile->getLogin()}/info/">info</a></li>
        <li {if $sMode=='blogs'}class="active"{/if}><a href="{router page='admin'}users/profile/{$oUserProfile->getLogin()}/blogs/">blogs</a></li>
        <li {if $sMode=='topics'}class="active"{/if}><a href="{router page='admin'}users/profile/{$oUserProfile->getLogin()}/topics/">topics</a></li>
        <li {if $sMode=='comments'}class="active"{/if}><a href="{router page='admin'}users/profile/{$oUserProfile->getLogin()}/comments/">comments</a></li>
        <li {if $sMode=='votes'}class="active"{/if}><a href="{router page='admin'}users/profile/{$oUserProfile->getLogin()}/votes/">votes</a><em></em></li>
    </ul>

    {if $sMode=='topics'}
        {include file="$sTemplatePathAction/users_profile_topics.tpl"}
    {elseif $sMode=='blogs'}
        {include file="$sTemplatePathAction/users_profile_blogs.tpl"}
    {elseif $sMode=='comments'}
        {include file="$sTemplatePathAction/users_profile_comments.tpl"}
    {elseif $sMode=='votes'}
        {include file="$sTemplatePathAction/users_profile_votes.tpl"}
    {else}
        {include file="$sTemplatePathAction/users_profile_info.tpl"}
    {/if}

</div>
