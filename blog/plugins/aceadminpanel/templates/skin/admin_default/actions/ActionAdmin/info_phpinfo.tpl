{include file='header.tpl' bNoSidebarLeft=true}

{literal}
<script type="text/javascript">
</script>
{/literal}

{if $sMenuSubItemSelect=='phpinfo'}
<h3>PHP Info</h3>

<div class="topic">
{if $sPhpInfo}
<div class="phpinfo">
{$sPhpInfo}
</div>
{/if}
{if $aPhpInfo.count}
{foreach from=$aPhpInfo.collection key=sSectionKey item=aSectionVal name=sec}
<div class="phpinfo">
<div class="h sechead">{$sSectionKey}
    <div class="close" id="close_{$smarty.foreach.sec.iteration}" onclick="js_func.action({$smarty.foreach.sec.iteration})">[&nbsp;-&nbsp;]</div>
</div>
<div class="section" id="section_{$smarty.foreach.sec.iteration}">
    <table>
    {foreach from=$aSectionVal key=sKey item=sVal}
        <tr>
            <td class="e">{$sKey}</td>
            <td class="v">{$sVal}</td>
        </tr>
    {/foreach}
    </table>
    </div>
</div>
{/foreach}
{/if}
</div>

{else}
<h3>{$oLang->adm_title}</h3>

<div class="topic">
<p>Description: Module for LiveStreet Engine Social Networking</p>
<p>Version: {$sModuleVersion}</p>
<p>Compatible with: LS ver.0.4</p>
<p>Current version of LiveStreet: {$LS_VERSION}</p>

<br /><br />
<b>Additional Modules</b>
<br />
<p>Description: Language module for LiveStreet. Extends system language module</p>
<p>Version: {$LANGUAGE_VERSION}</p>
<br />
<p>Description: Multilog module for LiveStreet</p>
<p>Version: {$LOGS_VERSION}</p>
<br /><br />

	{if $bNeedUpgrade}
		{$oLang->adm_need_upgrade}<br />
		<a href="{router page='admin'}upgrade/">Upgrade</a>
	{/if}
</div>
{/if}

{include file='footer.tpl'}