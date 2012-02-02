<!-- Header -->
<div id="header">
    <div class="logo"><a href="{router page='admin'}"><img src="{$sWebPluginSkin}images/logo.png" alt="{$sAdminTitle}"/> {$sAdminTitle}</a></div>

    <ul class="nav-main">
        <li><a href="{cfg name='path.root.web'}" target="_blank">{$aLang.adm_goto_site}</a></li>
        {hook run='main_menu'}
    </ul>

    <div class="profile">
        <a href="{$oUserCurrent->getUserWebPath()}" class="avatar"><img src="{$oUserCurrent->getProfileAvatarPath(48)}"
                                                                        alt="{$oUserCurrent->getLogin()}"/></a>
        <ul>
            <li>
                <a href="{$oUserCurrent->getUserWebPath()}" class="author">{$oUserCurrent->getLogin()}</a>
                (<a href="{router page='login'}exit/?security_ls_key={$LIVESTREET_SECURITY_KEY}">{$aLang.exit}</a>)
            </li>
            <li>
                {if $iUserCurrentCountTalkNew}
                    <a href="{router page='talk'}" class="message" id="new_messages"
                       title="{$aLang.user_privat_messages_new}">{$iUserCurrentCountTalkNew}</a>
                    {else}
                    <a href="{router page='talk'}" class="message-empty" id="new_messages">&nbsp;</a>
                {/if}
                {$aLang.user_settings}
                <a href="{router page='settings'}profile/" class="author">{$aLang.user_settings_profile}</a> |
                <a href="{router page='settings'}tuning/" class="author">{$aLang.user_settings_tuning}</a>
            </li>
            <li>{$aLang.user_rating} <strong>{$oUserCurrent->getRating()}</strong></li>
            {hook run='userbar_item'}
        </ul>
    </div>

</div>
<!-- /Header -->