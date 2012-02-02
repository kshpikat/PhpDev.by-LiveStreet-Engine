{literal}
<script type="text/javascript">
function AdminEdit(name) {
    var view, edit;
    if ((view=$('v_'+name)) && (edit=$('e_'+name))) {
        var size=view.getSize();
        view.style.display='none';
        edit.style.display='block';
        if (name=='user_profile_about') {
            edit.setStyle('width', size.x);
            if (name=='user_profile_about')
                edit.setStyle('height', size.y*5);
            else
                edit.setStyle('height', size.y);
            edit.focus();
            edit.select();
        } else if (name=='user_profile_site') {
            edit.focus();
        };
        $('edit_submit').disabled=false;
    }
}

function AdminSave(name) {
    var view, edit;
    if ((view=$('v_'+name)) && (edit=$('e_'+name))) {
        view.style.display='block';
        edit.style.display='none';
        if (name=='user_profile_site') {
            var s=$('profile_site').value;
            if (s.substr(0, 7)!='http://') s='http://'+s;
            view.set('html', '<a href="'+s+'">'+
                ($('profile_site_name').value?$('profile_site_name').value:$('profile_site').value)+
                '</a>');
        }
        else if (name=='user_profile_about') {
            view.set('text', edit.value);
        }
        else if (name=='user_profile_email') {
            view.set('text', $('profile_email').value);
        }
        if ($('edit_submit')) {
            $('edit_submit').disabled=true;
        }
    }
}

function AdminEditSubmit() {
    var params=new Hash();

    $('edit_submit').style.display='none';
    $('adm_process').style.display='block';

    params['user_id']=$('user_id').value;
    params['profile_about']=$('e_user_profile_about').value;
    params['profile_site']=$('profile_site').value;
    params['profile_site_name']=$('profile_site_name').value;
    params['profile_email']=$('profile_email').value;
    params['security_ls_key'] = LIVESTREET_SECURITY_KEY;

    new Request.JSON({
        url: aRouter['ajax'] + 'admin/setprofile/',
        noCache: true,
        data: params,
        onSuccess: function(result) {
            $('adm_process').style.display='none';
            $('edit_submit').style.display='';
            if (!result) {
                msgErrorBox.alert('Error', 'Please try again later');
            }
            else if (result.bStateError) {
                msgErrorBox.alert(result.sTitle, result.sText);
            } else {
                msgNoticeBox.alert(result.sTitle, result.sText);
                if ($('e_user_profile_about').style.display=='block') AdminSave('user_profile_about');
                if ($('e_user_profile_site').style.display=='block') AdminSave('user_profile_site');
                if ($('e_user_profile_email').style.display=='block') AdminSave('user_profile_email');
                $('edit_submit').disabled=true;
            }
        },
        onFailure: function() {
            $('adm_process').style.display='none';
            $('edit_submit').style.display='';
            msgErrorBox.alert('Error', 'Please try again later');
        }
    }).send();
}
</script>
{/literal}

{assign var="oSession" value=$oUserProfile->getSession()}
{assign var="oVote" value=$oUserProfile->getVote()}

<div class="profile-user -box">

    <h1 class="title">{$oLang->profile_privat}</h1>
    <table style="width:100%;">
        {if $oUserProfile->getProfileSex()!='other'}
        <tr>
            <td class="var">{$oLang->profile_sex}:</td>
            <td style="width:16px;">&nbsp;</td>
            <td>
                {if $oUserProfile->getProfileSex()=='man'}
                {$oLang->profile_sex_man}
                {else}
                {$oLang->profile_sex_woman}
                {/if}
            </td>
        </tr>
        {/if}

        <tr>
            <td class="var">{$oLang->profile_birthday}:</td>
            <td style="width:16px;">&nbsp;</td>
            <td>{$oUserProfile->getProfileBirthday()}</td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_place}:</td>
            <td>&nbsp;</td>
            <td>
            {if $oUserProfile->getProfileCountry()}
            <a href="{router page='people'}country/{$oUserProfile->getProfileCountry()|escape:'html'}/">{$oUserProfile->getProfileCountry()|escape:'html'}</a>{if $oUserProfile->getProfileCity()},{/if}
            {/if}
            {if $oUserProfile->getProfileCity()}
            <a href="{router page='people'}city/{$oUserProfile->getProfileCity()|escape:'html'}/">{$oUserProfile->getProfileCity()|escape:'html'}</a>
            {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_about}:</td>
            <td>
                <a href="#" onclick="AdminEdit('user_profile_about'); return false;"><img src="{$sWebPluginSkin}/images/edit.gif" alt="edit" /></a>
            </td>
            <td class="adm_field">
                <div>
                    <div id="v_user_profile_about" style="padding:4px;">{$oUserProfile->getProfileAbout()|escape:'html'}</div>
                    <textarea id="e_user_profile_about" rows="" cols="" style="overflow: auto;display:none;padding:4px;border:1px solid #CCC;background:#EEE;">{$oUserProfile->getProfileAbout()|escape:'html'}</textarea>
                </div>
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_site}:</td>
            <td>
                <a href="#" onclick="AdminEdit('user_profile_site'); return false;"><img src="{$sWebPluginSkin}/images/edit.gif" alt="edit" /></a>
            </td>
            <td class="adm_field">
                <div id="v_user_profile_site">
                    <noindex>
                        <a href="{$oUserProfile->getProfileSite(true)|escape:'html'}" rel="nofollow">
                            {if $oUserProfile->getProfileSiteName()}
                            {$oUserProfile->getProfileSiteName()|escape:'html'}
                            {else}
                            {$oUserProfile->getProfileSite()|escape:'html'}
                            {/if}
                        </a>
                    </noindex>
                </div>
                <div  id="e_user_profile_site" style="display:none;">
                    <input type="text" class="adm_edit" style="width:200px;" id="profile_site" name="profile_site" value="{$oUserProfile->getProfileSite()|escape:'html'}"/> <label for="profile_site">&mdash; {$oLang->settings_profile_site_url}</label><br />
                    <input type="text" class="adm_edit" style="width:200px;" id="profile_site_name" name="profile_site_name" value="{$oUserProfile->getProfileSiteName()|escape:'html'}"/> <label for="profile_site_name">&mdash; {$oLang->settings_profile_site_name}</label>
                </div>
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->settings_profile_mail}:</td>
            <td>
                <a href="#" onclick="AdminEdit('user_profile_email'); return false;"><img src="{$sWebPluginSkin}/images/edit.gif" alt="edit" /></a>
            </td>
            <td class="adm_field">
                <div id="v_user_profile_email">
                    <noindex>
                        <a href="mailto:{$oUserProfile->getMail()|escape:'hex'}" rel="nofollow">
                            {$oUserProfile->getMail()|escape:'html'}
                        </a>
                    </noindex>
                </div>
                <div  id="e_user_profile_email" style="display:none;">
                    <input type="text" class="adm_edit" style="width:200px;" id="profile_email" name="profile_email" value="{$oUserProfile->getMail()|escape:'html'}"/>
                </div>
            </td>
        </tr>

        <tr>
            <td>
            </td>
            <td>&nbsp;</td>
            <td>
                <input type="hidden" id="user_id" value="{$oUserProfile->getId()}" />
                <input type="button" id="edit_submit" value="{$oLang->adm_save}" onclick="AdminEditSubmit();" disabled />
                <div id="adm_process" style="text-align:left;display:none;">
                    <img src="{$sWebPluginSkin}/images/adm_process.gif" alt="" />
                </div>
            </td>
        </tr>
    </table>
    <br />

    <br />
    <h1 class="title">{$oLang->profile_activity}</h1>
    <table>
        <tr>
            <td class="var">{$oLang->profile_date_registration}:</td>
            <td>{date_format date=$oUserProfile->getDateRegister()} (ip:{$oUserProfile->getIpRegister()})</td>
        </tr>
        <tr>
            <td class="var">{$oLang->profile_date_last}:</td>
            <td>
                {if $oSession}
                {date_format date=$oSession->getDateLast()} (ip:{$oSession->getIpLast()})
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_friends}:</td>
            <td class="friends">
                {if $aUsersFrend}
                {foreach from=$aUsersFrend item=oUserFrend}
                <a href="{$oUserFrend->getUserWebPath()}">{$oUserFrend->getLogin()}</a>&nbsp;
                {/foreach}
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_friends_self}:</td>
            <td class="friends">
                {if $aUsersSelfFrend}
                {foreach from=$aUsersSelfFrend item=oUserFrend}
                <a href="{$oUserFrend->getUserWebPath()}">{$oUserFrend->getLogin()}</a>&nbsp;
                {/foreach}
                {/if}
            </td>
        </tr>

        {if $USER_USE_INVITE and $oUserInviteFrom}
        <tr>
            <td class="var">{$oLang->profile_invite_from}:</td>
            <td class="friends">
                <a href="{$oUserInviteFrom->getUserWebPath()}">{$oUserInviteFrom->getLogin()}</a>&nbsp;
            </td>
        </tr>
        {/if}

        {if $USER_USE_INVITE and $aUsersInvite}
        <tr>
            <td class="var">{$oLang->profile_invite_to}:</td>
            <td class="friends">
                {foreach from=$aUsersInvite item=oUserInvite}
                <a href="{$oUserInvite->getUserWebPath()}">{$oUserInvite->getLogin()}</a>&nbsp;
                {/foreach}
            </td>
        </tr>
        {/if}

        <tr>
            <td class="var">{$oLang->profile_blogs_self}:</td>
            <td>
                {if $aBlogsOwner}
                {foreach from=$aBlogsOwner item=oBlog name=blog_owner}
                <a href="{router page='blog'}{$oBlog->getUrl()}/">{$oBlog->getTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_owner.last}, {/if}
                {/foreach}
                {else}
                {$oLang->adm_no}
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_blogs_administration}:</td>
            <td>
                {if $aBlogsAdministration}
                {foreach from=$aBlogsAdministration item=oBlogUser name=blog_user}
                {assign var="oBlog" value=$oBlogUser->getBlog()}
                <a href="{$oBlog->getUrlFull()}">{$oBlog->getBlogTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
                {/foreach}
                {else}
                {$oLang->adm_no}
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_blogs_moderation}:</td>
            <td>
                {if $aBlogsModeration}
                {foreach from=$aBlogsModeration item=oBlogUser name=blog_user}
                {assign var="oBlog" value=$oBlogUser->getBlog()}
                <a href="{$oBlog->getUrlFull()}">{$oBlog->getBlogTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
                {/foreach}
                {else}
                {$oLang->adm_no}
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->profile_blogs_join}:</td>
            <td>
                {if $aBlogsUser}
                {foreach from=$aBlogsUser item=oBlogUser name=blog_user}
                {assign var="oBlog" value=$oBlogUser->getBlog()}
                <a href="{$oBlog->getUrlFull()}">{$oBlog->getBlogTitle()|escape:'html'}</a>{if !$smarty.foreach.blog_user.last}, {/if}
                {/foreach}
                {else}
                {$oLang->adm_no}
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->adm_user_wrote_topics}:</td>
            <td>
                {if $oUserProfile->GetCountTopics()}{$oUserProfile->GetCountTopics()}{else}0{/if}
                {if $aLastTopicList}
                (
                {foreach from=$aLastTopicList item=oTopic name=topic_user}
                <a href="{router page='blog'}{$oTopic->getId()}.html">{$oTopic->getTitle()|escape:'html'}</a>{if !$smarty.foreach.topic_user.last}, {/if}
                {/foreach}
                )
                {/if}
            </td>
        </tr>

        <tr>
            <td class="var">{$oLang->adm_user_wrote_comments}:</td>
            <td>
                {if $oUserProfile->GetCountComments()}{$oUserProfile->GetCountComments()}{else}0{/if}
            </td>
        </tr>

    </table>
</div>
