<table width="100%" cellspacing="0" class="admin-table">
    <tr>
        <th align="center" width="40px">&nbsp;</th>
        <th align="center" width="50px">Topic ID</th>
        <th align="center">Title</th>
        <th align="center">Date Add</th>
        <th align="center">Comments</th>
        <th align="center">Votes</th>
        <th align="center">Rating</th>
    </tr>

    {foreach from=$aTopics item=oTopic name=el2}
    {if $smarty.foreach.el2.iteration % 2  == 0}
  	{assign var=className value=''}
    {else}
  	{assign var=className value='colored'}
    {/if}
    <tr class="{$className}">
        <td align="right">
            <a href="{router page='topic'}edit/{$oTopic->getId()}/" title="{$oLang->adm_topic_edit}"><img src="{$sWebPluginSkin}/images/edit.gif" alt="" /></a>
            &nbsp;
            <a href="#" title="{$oLang->adm_topic_delete}" onclick="AdminTopicDelete('{$oLang->adm_topic_del_confirm}','{$oTopic->getTitle()}',{$oTopic->getId()}); return false;"><img src="{cfg name='path.static.skin'}/images/delete.gif" alt="" /></a>
        </td>
        <td class="number">
    	{$oTopic->getId()}&nbsp;
        </td>
        <td class="title">
            <a href="{$oTopic->getUrl()}">{$oTopic->getTitle()}</a>
        </td>
        <td class="center">
    	{$oTopic->getDateAdd()}
        </td>
        <td class="number">
    	{$oTopic->getCountComment()}
        </td>
        <td class="number">
    	{$oTopic->getCountVote()}
        </td>
        <td class="number">
    	{$oTopic->getRating()}
        </td>
    </tr>
    {/foreach}

</table>
