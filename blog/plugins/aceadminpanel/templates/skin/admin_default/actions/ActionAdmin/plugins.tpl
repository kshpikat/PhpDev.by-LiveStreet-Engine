{include file='header.tpl'}

<script type="text/javascript">
    var sWebPluginSkin = "{$sWebPluginSkin}";
    var sWebPluginPath = "{$sWebPluginPath}";
</script>

{if $tpl_content}
    {$tpl_content}
{/if}
{if $tpl_include}
    {include file="$tpl_include"}
{/if}

{include file='footer.tpl'}