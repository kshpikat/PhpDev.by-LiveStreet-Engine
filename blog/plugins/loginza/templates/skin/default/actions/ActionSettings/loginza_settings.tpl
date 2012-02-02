{include file='header.tpl' menu='settings' showWhiteBack=true}
<link rel="stylesheet" type="text/css" href="{$sTemplateWebPathPlugin}css/providers.css" media="all" />
<h1>{$aLang.loginza_settings_head}</h1>

{literal}
<script language="JavaScript" type="text/javascript">
function deleteIdentity(identity, obj) {
	new Request.JSON({
		url: aRouter['settings']+'loginza/delete/',
		noCache: false,
		data: {identity: identity, security_ls_key: LIVESTREET_SECURITY_KEY},
		onSuccess: function(resp){
			if (resp) {
				if (resp.bStateError) {
					msgErrorBox.alert(resp.sMsgTitle,resp.sMsg);
				} else {
					msgNoticeBox.alert(resp.sMsgTitle,resp.sMsg);
					$(obj).getParent().fade(0);
				}
			} else {
				msgErrorBox.alert('Error','Please try again later');
			}
		}.bind(this),
		onFailure: function(){
			msgErrorBox.alert('Error','Please try again later');
		}
	}).send();
	return false;
}
</script>
{/literal}

<table>
<tr>
<td valign="top" width="400">
<h3>{$aLang.loginza_settings_exists_identity}</h3>
{if $loginzaIdentities}
	<ul>
	{foreach from=$loginzaIdentities item=Identity}
		<li>{if $Identity->getProvider()}<span class="providers_ico_sprite {$Identity->getProvider()}_ico"></span>{/if}
		{$Identity->getIdentity()|escape:'html'} <a href="#" onclick="deleteIdentity('{$Identity->getIdentity()|escape:'html'}', this);return false;">удалить</a></li>
	{/foreach}
	</ul>
{else}
	{$aLang.loginza_settings_identities_empty}
{/if}
</td>

<td valign="top">
	<h3>{$aLang.loginza_settings_bind_identity}</h3>
	<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
	<iframe src="https://loginza.ru/api/widget?hide_welcome=true&overlay=loginza&providers_set={$oConfig->GetValue('plugin.loginza.widget.providers')}&lang={$oConfig->GetValue('plugin.loginza.widget.lang')}&token_url={$aRouter.settings|urlencode}{'loginza/'|urlencode}" 
	style="width:359px;height:300px;" scrolling="no" frameborder="no"></iframe>
</td>
</tr>
</table>

{include file='footer.tpl'}