<h3>{$oLang->adm_menu_plugins}: {$oLang->adm_menu_settings}</h3>
<table>
{foreach from=$aPlugins item=aPlugin key=key}
    <tr>
        <td class="title">
            <b><a href="{router page='admin'}plugins/settings/{$key}/">
                {$aPlugin.property->name->data|escape:'html'}
            </a></b>
        </td>
        <td><!-- img src="{$sWebPluginSkin}images/cog.png" alt="" / --></td>
        <td class="description">{$aPlugin.property->description->data|escape:'html'}</td>
    </tr>
{/foreach}
</table>