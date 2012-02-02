{literal}
<script type="text/javascript">
function adminPluginUp(plugin)     {
    var row = $(plugin+'_row');
    var priority = $(plugin+'_priority').value;
    var prev = row.getPrevious();
    if (prev) {
        var prev_priority = $(prev.get('id').replace('_row', '_priority'));
        row.inject(prev, 'before');
        $(plugin+'_priority').value=prev_priority.value;
        prev_priority.value=priority;
    }
}

function adminPluginDown(plugin)     {
    var priority = $(plugin+'_priority').value;
    var row = $(plugin+'_row');
    var next = row.getNext();
    if (next) {
        var next_priority = $(next.get('id').replace('_row', '_priority'));
        row.inject(next, 'after');
        $(plugin+'_priority').value=next_priority.value;
        next_priority.value=priority;
    }
}

function adminPluginSave() {
   return true;
}
</script>
{/literal}

<h3>{$oLang->adm_plugins_title}</h3>
    <ul class="block-nav -box">
        <li {if $sMode=='all' || $sMode==''}class="active"{/if}><strong></strong><a href="{router page='admin'}/plugins/list/all/">all</a></li>
        <li {if $sMode=='active'}class="active"{/if}><a href="{router page='admin'}plugins/list/active/">active</a></li>
        <li {if $sMode=='inactive'}class="active"{/if}><a href="{router page='admin'}plugins/list/inactive/">inactive</a><em></em></li>
    </ul>

    <form action="{router page='admin'}plugins/" method="post" id="form_plugins_list">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <table class="admin-table">
            <thead>
                <tr>
                    <th width="20px"><input type="checkbox" name="" onclick="checkAllPlugins(this);" /></th>
                    <th class="name">{$oLang->plugins_plugin_name}</th>
                    <th class="version">{$oLang->plugins_plugin_version}</th>
                    <th class="author">{$oLang->plugins_plugin_author}</th>
                    <th class="action">{$oLang->plugins_plugin_action}</th>
                    <th class="">{$oLang->adm_menu_settings}</th>
                </tr>
            </thead>

            <tbody id="plugin_list">
            {foreach from=$aPluginList item=oPlugin}
                <tr id="{$oPlugin->GetId()}_row" class="{if $oPlugin->IsActive()}active{else}inactive{/if}">
                    <td><input type="checkbox" name="plugin_del[{$oPlugin->GetId()}]" class="form_plugins_checkbox" /></td>
                    <td class="name">
                        <div class="{if $oPlugin->IsActive()}active{else}inactive{/if}"></div>
                        <div class="title">{$oPlugin->GetName()|escape:'html'}</div>
                        <div class="description">
                        <b>{$oPlugin->GetCode()}</b> - {$oPlugin->GetDescription()|escape:'html'}
                        </div>
                        {if ($oPlugin->GetHomepage()>'')}
                        <div class="url">
                        Homepage: {$oPlugin->GetHomepage()}
                        </div>
                        {/if}
                    </td>
                    <td class="version">{$oPlugin->GetVersion()|escape:'html'}</td>
                    <td class="author">{$oPlugin->GetAuthor()|escape:'html'}</td>
                    <td class="{if $oPlugin->IsActive()}deactivate{else}activate{/if}">
                            {if $oPlugin->IsActive()}
                            <a href="{router page='admin'}plugins/?plugin={$oPlugin->GetId()}&action=deactivate&security_ls_key={$LIVESTREET_SECURITY_KEY}">{$oLang->adm_act_deactivate}</a>
                            {else}
                            <a href="{router page='admin'}plugins/?plugin={$oPlugin->GetId()}&action=activate&security_ls_key={$LIVESTREET_SECURITY_KEY}">{$oLang->adm_act_activate}</a>
                            {/if}
                    </td>
                    <td class="center">
                        {if $oPlugin->IsActive() AND $oPlugin->HasAdminpanel()}
                        <!-- a href="{router page='admin'}plugins/config/{$oPlugin->GetId()}/">
                            <img src="{$sWebPluginSkin}images/cog.png" alt="{$oLang->adm_menu_settings}" title="{$oLang->adm_menu_settings}" />
                        </a -->
                        {/if}
                    </td>
                </tr>
        {/foreach}
            </tbody>
        </table>
        <!-- <br/> {$oLang->adm_plugin_priority_notice} -->
        <br/><br/>
        <input type="submit" name="submit_plugins_del" value="{$aLang.plugins_submit_delete}" onclick="return ($$('.form_plugins_checkbox:checked').length==0)?false:confirm('{$aLang.plugins_delete_confirm}');" />
        <!-- <input type="submit" name="submit_plugins_save" value="{$aLang.adm_save}" onclick="adminPluginSave();" /> -->
    </form>
