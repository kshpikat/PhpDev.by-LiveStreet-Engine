<table width="100%" cellspacing="0" class="admin-table">
    <thead>
        <tr>
            <th width="40px">&nbsp;</th>
            <th width="50px">Blog ID</th>
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
                    <a href="#" title="{$oLang->adm_blog_delete}" onclick="AdminBlogDelete('{$oLang->adm_blog_del_confirm}','{$sBlogTitle}',{$aBlog.blog_id}); return false;"><img src="{cfg name='path.static.skin'}/images/delete.gif" alt="" /></a>
            </td>
            <td class="number">{$aBlog.blog_id}</td>
            <td class="name">
                <a href="{$aBlog.blog_url_full}">{$sBlogTitle}</a>
            </td>
            <td class="center">{$aBlog.blog_date_add}</td>
            <td class="center">{$aBlog.blog_type}</td>
            <td class="number">{$aBlog.blog_count_user}</td>
            <td class="number">{$aBlog.blog_count_vote}</td>
            <td class="number">{$aBlog.blog_rating}</td>
        </tr>
	{/foreach}
    </tbody>
</table>