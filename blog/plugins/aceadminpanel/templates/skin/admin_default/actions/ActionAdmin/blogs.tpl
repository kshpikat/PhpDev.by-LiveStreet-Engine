{include file='header.tpl' showWhiteBack=true}

<script type="text/javascript">
    var sWebPluginSkin = "{$sWebPluginSkin}";
    var sWebPluginPath = "{$sWebPluginPath}";
</script>

<h3>{$oLang->adm_menu_blogs}</h3>
<div class="topic">

    <ul class="block-nav -box">
        <li {if $sMode=='all' || $sMode==''}class="active"{/if}><strong></strong><a href="{router page='admin'}blogs/list/">all ({$iBlogsTotal})</a></li>
        {foreach from=$aBlogTypes item=aBlogType name=loop}
        <li {if $sMode==$aBlogType.blog_type}class="active"{/if}>
            <a href="{router page='admin'}blogs/list/{$aBlogType.blog_type}/">{$aBlogType.blog_type} ({$aBlogType.blog_cnt})</a>
            {if $smarty.foreach.loop.last}<em></em>{/if}
        </li>
        {/foreach}
    </ul>

    <table class="admin-table">
        <thead>
            <tr>
                <th width="40px">&nbsp;</th>
                <th width="50px">Blog ID</th>
                <th>User</th>
                <th>Title</th>
                <th>Date</th>
                <th>Type</th>
                <th>Users</th>
                <th>Votes</th>
                <th>Rating</th>
            </tr>
        </thead>

        <tbody>
        {foreach from=$aBlogs item=aBlog name=el2}
            {if $smarty.foreach.el2.iteration % 2  == 0}
            {assign var=className value=''}
            {else}
            {assign var=className value='colored'}
            {/if}
            {assign var=sBlogTitle value=$aBlog.blog_title|escape:'html'}
            <tr class="{$className}">
                <td class="name">
                    {if $aBlog.blog_type=='personal'}
                    <img src="{$sWebPluginSkin}/images/edit_no.gif" alt="" />
                    {else}
                    <a href="{router page='blog'}edit/{$aBlog.blog_id}/" title="{$oLang->adm_blog_edit}"><img src="{cfg name='path.static.skin'}/images/edit.gif" alt="" /></a>
                    {/if}
                    <a href="#" title="{$oLang->adm_blog_delete}" onclick="AdminBlogDelete('{$oLang->adm_blog_del_confirm}','{$sBlogTitle}','{$aBlog.blog_id}'); return false;"><img src="{cfg name='path.static.skin'}/images/delete.gif" alt="" /></a>
                </td>
                <td class="number">{$aBlog.blog_id}</td>
                <td>
                    <a href="{router page='admin'}users/profile/{$aBlog.user_login}">{$aBlog.user_login}</a>
                </td>
                <td class="name">
                    <a href="{$aBlog.blog_url_full}">{$sBlogTitle}</a>
                </td>
                <td class="center">{$aBlog.blog_date_add}</td>
                <td class="center">{if $aBlog.blog_type!='personal'}<b>{/if}{$aBlog.blog_type}{if $aBlog.blog_type!='personal'}</b>{/if}</td>
                <td class="number">{$aBlog.blog_count_user}</td>
                <td class="number">{$aBlog.blog_count_vote}</td>
                <td class="number">{$aBlog.blog_rating}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {include file='paging.tpl'}
</div>

{include file='footer.tpl'}