{literal}
<script type="text/javascript">
function AdminInviteMode(mode) {
  if (mode=='mail') {
    $('div_invite_mail').style.display='';
    $('div_invite_text').style.display='none';
  } else {
    $('div_invite_mail').style.display='none';
    $('div_invite_text').style.display='';
  }
}

function AdminInviteSubmit(msg1, msg2) {
  if ($('adm_invite_mode_mail').checked) {
    if (!$('invite_mail').value) {
      alert(msg1);
      return false;
    }
  }
  if ($('adm_invite_mode_text').checked) {
    if (parseInt($('invite_count').value)<=0) {
      alert(msg2);
      return false;
    }
  }
  return true;
}
</script>
{/literal}

{if !$aNewInviteList}
<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <p>
    {$oLang->settings_invite_available}: <strong>{if $iCountInviteAvailable==-1}{$aLang.settings_invite_many}{else}{$iCountInviteAvailable}{/if}</strong><br />
    {$oLang->settings_invite_used}: <strong>{$iCountInviteUsed}</strong>
    </p>
    <p>
        <input type="radio" name="adm_invite_mode" id="adm_invite_mode_mail" value="mail" {if $sInviteMode=='mail'}checked{/if} onclick="AdminInviteMode('mail');" />
        {$oLang->adm_invite_mode_mail}<br/>
        <input type="radio" name="adm_invite_mode" id="adm_invite_mode_text" value="text" {if $sInviteMode=='text'}checked{/if} onclick="AdminInviteMode('text');" />
        {$oLang->adm_invite_mode_text}<br/>
    </p>
    <div id="div_invite_mail" {if $sInviteMode=='text'}style="display:none;"{/if}>
         <label for="invite_mail">{$oLang->adm_send_invite_mail}:</label><br />
        <textarea name="invite_mail" id="invite_mail" class="w300"></textarea><br />
    </div>
    <div id="div_invite_text" {if $sInviteMode=='mail'}style="display:none;"{/if}>
         <label for="invite_count">{$oLang->adm_make_invite_text}:</label><br />
        <input type="text" name="invite_count" id="invite_count" class="w100" style="text-align:right;" value="{$iInviteCount}" /><br />
    </div>
    <br/>
    <input type="submit" value="{$oLang->adm_invite_submit}" name="adm_invite_submit" onclick="return AdminInviteSubmit('{$oLang->adm_invaite_mail_empty}', '{$oLang->adm_invaite_text_empty}');" />
</form>
{else}
{foreach from=$aNewInviteList key=key item=item}
{$key} : {$item}<br/>
{/foreach}
<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <input type="hidden" name="adm_invite_mode" id="adm_invite_mode_mail" value="{$sInviteMode}" />
    <input type="hidden" name="invite_count" id="invite_count" value="{$iInviteCount}" /><br />
    <input type="submit" value="{$oLang->adm_continue}" />
</form>
{/if}