<table width="100%" cellspacing="0" class="admin-table">
    <tr>
        <th align="center" width="40px">&nbsp;</th>
        <th align="center" width="50px">ID</th>
        <th align="center" width="50px">Date</th>
        <th align="center" width="50px">ip</th>
        <th align="center">Text</th>
        <th align="center" width="200px">Topic</th>
        <th align="center" width="40px">Votes</th>
        <th align="center" width="40px">Rating</th>
    </tr>

    {foreach from=$aComments item=oComment name=el2}
    {if $smarty.foreach.el2.iteration % 2  == 0}
  	{assign var=className value=''}
    {else}
  	{assign var=className value='colored'}
    {/if}
    {assign var=oTopic value=$oComment->getTarget()}
    <tr class="{$className}">
        <td>
            <!-- a href="{router page='admin'}edit/comment/{$oComment->getId()}/">
                <img src="{$sWebPluginSkin}/images/edit.gif" alt="{$oLang->page_admin_action_edit}" title="{$oLang->page_admin_action_edit}" />
            </a -->
            <img src="{$sWebPluginSkin}/images/edit_no.gif" alt="" />
        </td>
        <td class="number">
            {$oComment->getId()}&nbsp;
        </td>
        <td class="center">
            {$oComment->getDate()}
        </td>
        <td class="center">
            {$oComment->getUserIp()}
        </td>
        <td class="title">
            <a href="{$oTopic->getUrl()}#comment{$oComment->getId()}">{$oComment->getText()}</a>
        </td>
        <td class="title">
            <a href="{$oTopic->getUrl()}">{$oTopic->getTitle()}</a>
        </td>
        <td class="number">
            {$oComment->getCountVote()}
        </td>
        <td class="number">
            {$oComment->getRating()}
        </td>
    </tr>
    {/foreach}

</table>
