{if !$submit_cache_save}

<div class="topic">
    <form method="post" action="">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <h3>{$oLang->adm_tools_comments}</h3>

        <div style="padding:10px;">
            <input type="checkbox" id="adm_tools_comments_clear" name="adm_tools_comments_clear" checked />
            <label for="adm_tools_comments_clear">{$oLang->adm_tools_comments_clear}</label><br />
            <span class="form_note">{$oLang->adm_tools_comments_clear_notice}</span><br />
        </div>

        <p class="buttons">
            <input type="submit" name="adm_submit" value="{$oLang->adm_execute}" />
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