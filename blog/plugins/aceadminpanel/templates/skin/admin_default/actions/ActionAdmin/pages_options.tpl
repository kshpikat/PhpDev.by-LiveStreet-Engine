<form action="" method="post">
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <p>
        <label for="param_reserved_urls">{$oLang->adm_page_options_urls}:</label>
        <input type="text" id="param_reserved_urls" name="param_reserved_urls" value="{$sParamPageUrlReserved}"  class="w100p" /><br />
        <span class="form_note">{$oLang->adm_page_options_urls_notice}</span>
    </p>

    <p class="buttons">
        <input type="submit" name="submit_options_save" value="{$oLang->page_create_submit_save}" />
    </p>
</form>