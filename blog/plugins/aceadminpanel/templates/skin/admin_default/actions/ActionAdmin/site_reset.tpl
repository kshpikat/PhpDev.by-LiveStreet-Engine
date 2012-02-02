{if !$submit_cache_save}

<div class="topic">
    <form method="post" action="">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <h3>{$oLang->adm_menu_reset_cache}</h3>

        <div style="padding:10px;">
            <input type="checkbox" id="adm_cache_clear_data" name="adm_cache_clear_data" checked />
            <label for="adm_cache_clear_data">{$oLang->adm_cache_clear_data}</label><br />
            <span class="form_note">{$oLang->adm_cache_clear_data_notice}</span><br />
        </div>

        <div style="padding:10px;">
            <input type="checkbox" id="adm_cache_clear_headfiles" name="adm_cache_clear_headfiles" checked />
            <label for="adm_cache_clear_headfiles">{$oLang->adm_cache_clear_headfiles}</label><br />
            <span class="form_note">{$oLang->adm_cache_clear_headfiles_notice}</span><br />
        </div>

        <div style="padding:10px;">
            <input type="checkbox" id="adm_cache_clear_smarty" name="adm_cache_clear_smarty" checked />
            <label for="adm_cache_clear_smarty">{$oLang->adm_cache_clear_smarty}</label><br />
            <span class="form_note">{$oLang->adm_cache_clear_smarty_notice}</span><br />
        </div>

        <h3>{$oLang->adm_menu_reset_config}</h3>
        
        <div style="padding:10px;">
            <input type="checkbox" id="adm_reset_config_data" name="adm_reset_config_data" />
            <label for="adm_reset_config_data">{$oLang->adm_reset_config_data}</label><br />
            <span class="form_note">{$oLang->adm_reset_config_data_notice}</span><br />
        </div>

        <p class="buttons">
            <input type="submit" name="adm_reset_submit" value="{$oLang->adm_save}" />
        </p>
    </form>
</div>

{else}

<div class="topic">
    <form method="post" action="">
        <input type="submit" name="admin_continue" value="{$oLang->adm_continue}" />
    </form>
</div>

{/if}