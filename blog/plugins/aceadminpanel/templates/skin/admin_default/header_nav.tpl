<!-- Navigation -->
<div id="nav">

{if $menu}
    {if in_array($menu,$aMenuContainers)}{$aMenuFetch.$menu}{else}{include file="menu.$menu.tpl"}{/if}
{/if}


    <div class="right"></div>
    <!--<a href="#" class="rss" onclick="return false;"></a>-->
    <div class="search">
        <form action="{router page='search'}topics/" method="GET">
            <input class="text" type="text" onblur="if (!value) value=defaultValue"
                   onclick="if (value==defaultValue) value=''" value="{$aLang.search}" name="q"/>
            <input class="button" type="submit" value=""/>
        </form>
    </div>
</div>
<!-- /Navigation -->