<script type="text/javascript">
    var sWebPluginSkin = "{$sWebPluginSkin}";
    var sWebPluginPath = "{$sWebPluginPath}";
</script>
{literal}
<script type="text/javascript">
    function AdminUserAction(n) {
        var i, el;
        if (document.getElementById) {
            for (i = 1; i <= 3; i++) {
                if (i == n) {
                    if ((el = document.getElementById('a' + i))) el.style.display = 'none';
                    if ((el = document.getElementById('t' + i))) el.style.display = '';
                    if ((el = document.getElementById('d' + i))) el.style.display = '';
                    if ((n == 2) && (el = document.getElementById('ban_days'))) el.focus();
                } else {
                    if ((el = document.getElementById('a' + i))) el.style.display = '';
                    if ((el = document.getElementById('t' + i))) el.style.display = 'none';
                    if ((el = document.getElementById('d' + i))) el.style.display = 'none';
                }
            }
        }
    }

    function AdminUserDelConfirm(msg_ask, msg_confirm) {
        var i, el;
        if (document.getElementById) {
            if ((el = document.getElementById('admin_user_del_confirm')) && !el.checked) {
                alert(msg_confirm);
                return false;
            }
            if (!confirm(msg_ask)) {
                return false;
            }
        }
        return true;
    }

</script>

<style type="text/css">
    .profile-user .voting a.adm_plus {
        background-image: url(/plugins/aceadminpanel/templates/skin/admin_default/images/adm_icons.png);
    }

    .profile-user .voting a.adm_plus:hover {
        background-image: url(/plugins/aceadminpanel/templates/skin/admin_default/images/adm_icons.png);
    }

    .profile-user .voting a.adm_minus {
        background-image: url(/plugins/aceadminpanel/templates/skin/admin_default/images/adm_icons.png);
    }

    .profile-user .voting a.adm_minus:hover {
        background-image: url(/plugins/aceadminpanel/templates/skin/admin_default/images/adm_icons.png);
    }
</style>
{/literal}

{assign var="oSession" value=$oUserProfile->getSession()}
{assign var="oVote" value=$oUserProfile->getVote()}

<div class="block white">
    <div class="tl">
        <div class="tr"></div>
    </div>
    <div class="cl">
        <div class="cr">

            <div class="profile-user">
                <div class="name">
                    <img src="{$oUserProfile->getProfileAvatarPath(100)}" alt="avatar" class="avatar"/>

                    <div>
                        <div class="status">
                            <div class="{if !$oUserProfile->getDateActivate()}adm_noactive{elseif $oUserProfile->IsBannedByLogin()}adm_banned{elseif $oUserProfile->isAdministrator()}adm_admin{else}adm_user{/if}"
                                 style="background-image: url({$sWebPluginSkin}/images/adm_icons.png)"></div>
                        </div>
                        <div class="nickname">ID: {$oUserProfile->getId()}xx</div>

                        <div class="nickname">{$oUserProfile->getLogin()}</div>
                    {if $oUserProfile->getProfileName() or 1==1}
                        <div class="realname">zzz{$oUserProfile->getProfileName()|escape:'html'}</div>
                    {/if}
                    </div>

                    <div class="strength">
                        <div class="clear" style="text-align:center;">{$oLang->user_skill}</div>
                        <div class="total" id="user_skill_{$oUserProfile->getId()}">{$oUserProfile->getSkill()}</div>
                    </div>


                    <div class="voting {if $oUserProfile->getRating()>=0}positive{else}negative{/if} ">
                        <div class="clear" style="text-align:center;">{$oLang->user_rating}</div>

                        <a href="#" class="adm_plus"
                           onclick="AdminVote({$oUserProfile->getId()}, this, {$nParamVoteValue}, 'user'); return false;"
                           style="background-image: url({$sWebPluginSkin}/images/adm_icons.png)"
                           title="+{$nParamVoteValue}"></a>
                        <a href="#" class="plus"
                           onclick="AdminVote({$oUserProfile->getId()}, this, 1,'user'); return false;" title="+1"></a>

                        <div class="total">{if $oUserProfile->getRating()>0}+{/if}{$oUserProfile->getRating()}</div>
                        <a href="#" class="minus"
                           onclick="AdminVote({$oUserProfile->getId()}, this, -1,'user'); return false;" title="-1"></a>
                        <a href="#" class="adm_minus"
                           onclick="AdminVote({$oUserProfile->getId()}, this, -{$nParamVoteValue}, 'user'); return false;"
                           style="background-image: url({$sWebPluginSkin}/images/adm_icons.png)"
                           title="-{$nParamVoteValue}"></a>

                        <div class="text">{$oLang->user_vote_count}:</div>
                        <div class="count">{$oUserProfile->getCountVote()}</div>
                    </div>
                </div>

                <div class="vote-stat">
                    <table width="100%">
                        <tr>
                            <th colspan="3">{$oLang->adm_user_voted} (cnt/sum)</th>
                        </tr>
                        <tr>
                            <td align="center">{$oLang->adm_user_voted_topics}</td>
                            <td align="center" style="color:#390;">{$aUserVoteStat.cnt_topics_p}
                                /{$aUserVoteStat.sum_topics_p}</td>
                            <td align="center" style="color:#f00;">{$aUserVoteStat.cnt_topics_m}
                                /{$aUserVoteStat.sum_topics_m}</td>
                        </tr>
                        <tr>
                            <td align="center">{$oLang->adm_user_voted_users}</td>
                            <td align="center" style="color:#390;">{$aUserVoteStat.cnt_users_p}
                                /{$aUserVoteStat.sum_users_p}</td>
                            <td align="center" style="color:#f00;">{$aUserVoteStat.cnt_users_m}
                                /{$aUserVoteStat.sum_users_m}</td>
                        </tr>
                        <tr>
                            <td align="center">{$oLang->adm_user_voted_comments}</td>
                            <td align="center" style="color:#390;">{$aUserVoteStat.cnt_comments_p}
                                /{$aUserVoteStat.sum_comments_p}</td>
                            <td align="center" style="color:#f00;">{$aUserVoteStat.cnt_comments_m}
                                /{$aUserVoteStat.sum_comments_m}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="clear">&nbsp;</div>
        {if $oUserProfile->IsBannedByLogin()}
            <div style="border-top:1px solid #CCC;text-align:center;">
                {$oLang->adm_ban_upto}
                : {if $oUserProfile->getBanLine()}{$oUserProfile->getBanLine()}{else}{$oLang->adm_ban_unlim}{/if}
            </div>
        {/if}
            <hr/>

            <h1>{$oLang->adm_users_action} &darr;</h1>

            <div style="margin-left:20px;">
            {if $oUserProfile->IsBannedByLogin()}
                <h4><span id="a1"><a href="#" onclick="AdminUserAction(1); return false;">{$oLang->adm_users_unban}</a></span><span
                        id="t1" style="display:none;">{$oLang->adm_users_unban}</span></h4>

                <form method="post" action="{$sPageRef}">
                    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}"/>

                    <div id="d1" style="margin-left:20px;display:none;border-bottom:1px solid #CCC;">
                        <br/>
                        {if $oUserProfile->getBanLine()}
                            {$oLang->adm_ban_upto} {$oUserProfile->getBanLine()} <br/>
                            {else}
                            {$oLang->adm_ban_unlim} <br/>
                        {/if}
                        {$oLang->adm_ban_comment}: {$oUserProfile->getBanComment()}<br/>
                        <br/>
                        <input type="hidden" name="ban_login" id="ban_login" value="{$oUserProfile->getLogin()}"/>
                        <input type="hidden" name="adm_user_ref" value="{$sPageRef}"/>
                        <input type="hidden" name="adm_user_action" value="adm_unban_user"/>
                        <input type="submit" name="adm_action_submit" value="{$oLang->adm_users_unban}"/>
                        <br/><br/>
                    </div>
                </form>
                {else}
                <h4><span id="a2"><a href="#"
                                     onclick="AdminUserAction(2); return false;">{$oLang->adm_users_ban}</a></span><span
                        id="t2" style="display:none;">{$oLang->adm_users_ban}</span></h4>

                <form method="post" action="{$sPageRef}">
                    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}"/>

                    <div id="d2" style="margin-left:20px;display:none;border-bottom:1px solid #CCC;">
                        <br/>
                        <input type="hidden" name="ban_login" id="ban_login" value="{$oUserProfile->getLogin()}"/>
                        <input type="radio" name="ban_period" value="days" checked/>{$oLang->adm_ban_for} <input
                            type="text" name="ban_days" id="ban_days"
                            style="width:25px;padding:0;text-align:right;"/> {$oLang->adm_ban_days}<br/>
                        <input type="radio" name="ban_period" value="unlim"/>{$oLang->adm_ban_unlim} <br/><br/>
                        <label for="ban_comment">{$oLang->adm_ban_comment}</label> <input type="text" name="ban_comment"
                                                                                          maxlength="255"
                                                                                          style="width:200px;"/><br/>
                        <br/>
                        <input type="hidden" name="adm_user_ref" value="{$sPageRef}"/>
                        <input type="hidden" name="adm_user_action" value="adm_ban_user"/>
                        <input type="submit" name="adm_action_submit" value="{$oLang->adm_users_ban}"/>
                        <br/><br/>
                    </div>
                </form>
            {/if}

                <h4>
                    <span id="a3">
                        <a href="#" onclick="AdminUserAction(3); return false;">{$oLang->adm_users_del}</a>
                    </span>
                    <span id="t3" style="display:none;">{$oLang->adm_users_del}</span>
                </h4>
                <br/>

                <form method="post" action="{router page='admin'}users/">
                    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}"/>

                    <div id="d3" style="margin-left:20px;display:none;">
                        <br/>

                        <div class="msg-warning">
                        {$oLang->adm_users_del_warning}
                        </div>
                        <input type="hidden" name="adm_del_login" id="ban_login" value="{$oUserProfile->getLogin()}"/>
                        <br/>
                        <input type="checkbox" name="adm_user_del_confirm"
                               id="admin_user_del_confirm"/>{$oLang->adm_users_del_confirm} <br/>
                        <br/>
                        <input type="hidden" name="adm_user_ref" value="{$sPageRef}"/>
                        <input type="hidden" name="adm_user_action" value="adm_del_user"/>
                        <input type="submit" name="adm_action_submit" value="{$oLang->adm_users_del}"
                               onclick="return AdminUserDelConfirm('{$oLang->adm_users_del_warning}', '{$oLang->adm_users_del_confirm}');"/>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div class="bl">
        <div class="br"></div>
    </div>
</div>
