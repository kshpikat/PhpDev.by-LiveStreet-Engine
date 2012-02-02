{include file='header.light.tpl'}
<link rel="stylesheet" type="text/css" href="{$sTemplateWebPathPlugin}css/style.css" media="all" />

<div class="openid-block step-two">
	<h1>{$aLang.loginza_login_head}</h1>

	<ul>
		<li {if !$emailUserExists}class="active"{/if} id="li_reg"><a href="javascript:showFormData()" ><span>{$aLang.loginza_new_user}</span></a></li>
		<li id="li_bind" {if $emailUserExists}class="active"{/if}><a href="javascript:showFormMail()" ><span>{$aLang.loginza_exists_user}</span></a></li>
	</ul>
	
	
	<form method="post" action="{router page='login'}loginza/reg/" id="reg_form" {if $emailUserExists}style="display: none;"{/if}>			
		<p>
		{$aLang.loginza_reg_help_message}
		</p>
		<p>
			<label>{$aLang.loginza_login_label}</label>
			<input type="text" class="openid-text" maxlength="50" name="login" value="{if $_aRequest.login}{$_aRequest.login|escape}{else}{$loginzaLogin|escape}{/if}" />
		</p>
		<p style="margin-bottom: 18px;">
			{if $oConfig->GetValue('plugin.loginza.mail_required')}
				<label>{$aLang.loginza_email_label}</label>
			{else}
				<a href="javascript:toggleMail()" class="openid-mail">{$aLang.loginza_email_need_link}</a>
			{/if}
			<input type="text" class="openid-text" style="margin-top: 5px;{if $oConfig->GetValue('plugin.loginza.mail_required') or $_aRequest.mail}display: block;{else}display: none;{/if}" maxlength="50" name="email" value="{if $_aRequest.email}{$_aRequest.email|escape}{else}{$loginza->email|escape}{/if}" id="mail"/>
		</p>
		
		<a href="#" class="openid-ok" name="submit_data"  onclick="getEl('reg_form').submit(); return false;"><span>{$aLang.loginza_new_user_button}</span></a>		
	</form>

	
	<form method="post" action="{router page='login'}loginza/bind/"  id="bind_form" {if $emailUserExists}style="display: block;"{else}style="display: none;"{/if}>							
		<p>
		{$aLang.loginza_bind_help_message|replace:"%1":$loginza->identity}
		</p>
		<p>
			<label>{$aLang.user_login}</label>
			<input type="text" class="openid-text" maxlength="50" name="email" value="{if $_aRequest.email}{$_aRequest.email|escape}{else}{$loginza->email|escape}{/if}" />	
		</p>
		<p>
			<label>{$aLang.user_password}</label>
			<input type="password" class="openid-text" maxlength="50" name="password" value="" />	
		</p>
		
		<a href="#" class="openid-ok"  onclick="getEl('bind_form').submit(); return false;"><span>{$aLang.loginza_exists_user_button}</span></a>			
	</form>
</div>	
					
{literal}
<script language="JavaScript" type="text/javascript">
	function getEl(id) {
		return document.getElementById(id);
	}

	function showFormData() {
		getEl('bind_form').style.display='none';
		getEl('reg_form').style.display='block';
		getEl('li_reg').className='active';
		getEl('li_bind').className='';		
	}
	
	function showFormMail() {
		getEl('reg_form').style.display='none';
		getEl('bind_form').style.display='block';
		getEl('li_reg').className='';
		getEl('li_bind').className='active';		
	}
	
	function toggleMail(id) {
		if (getEl('mail').style.display=='none') {
			getEl('mail').style.display='block';
		} else {
			getEl('mail').style.display='none';
		}
	}		
</script>
{/literal}

{include file='footer.light.tpl'}